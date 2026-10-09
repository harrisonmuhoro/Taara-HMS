# Taara HMS — Security Remediation Guide

Companion to the second security audit (F-01 to F-10) plus gaps the audits did not cover.
**Rule: one finding per PR, each with its own regression test. Do not batch.**

> Adapt names (`status`, `payments.collect`, column names, relationships) to your actual schema. The code below is a reference implementation, not a drop-in patch.

---

## 0. Order of work

| # | Item | Why first |
| :-- | :--- | :--- |
| 1 | MySQL test database (F-06) | Nothing below is verifiable on SQLite |
| 2 | Inactive login + session revocation (F-02, F-05) | Fired staff keep access today |
| 3 | STK abuse controls (F-01, F-10) | Direct money and guest-harassment risk |
| 4 | Production boot guard (F-07) | Cheap, prevents the worst deploy mistake |
| 5 | Branch-scoped `exists` rules (F-08) | Same bug likely repeated across the codebase |
| 6 | Reset enumeration (F-04) | Quick win |
| 7 | CSV formula injection (F-03) | Quick win |
| 8 | PII in logs and payloads (F-09) | Compliance exposure |
| 9 | Structural items (Section 11) | Database-level invariants |

Status checklist:

- [ ] F-06 MySQL tests
- [ ] F-02 inactive login
- [ ] F-05 session revocation
- [ ] F-01 payment permission
- [ ] F-10 STK rate limits and duplicate-pending guard
- [ ] F-07 production guard
- [ ] F-08 branch-scoped validation
- [ ] F-04 reset enumeration
- [ ] F-03 CSV injection
- [ ] F-09 payload redaction
- [ ] Structural: `reservation_nights`
- [ ] Structural: server-side STK confirmation and amount check

---

## 1. F-06 — Test against MySQL, not SQLite

`lockForUpdate()` does nothing on in-memory SQLite, so every "concurrency safe" claim is currently unproven.

**`phpunit.xml`** (remove the SQLite lines, use a dedicated schema):

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="taara_hms_test"/>
```

**`.env.testing`**:

```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taara_hms_test
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=sync
CACHE_STORE=array
SESSION_DRIVER=array
```

Never point this at your real database. `RefreshDatabase` wipes it.

**Concurrency tests need committed data.** `RefreshDatabase` wraps each test in a transaction, which other processes cannot see. For parallel tests use `DatabaseMigrations` (or seed and clean up manually) and spawn real processes:

```php
use Illuminate\Support\Facades\Process;

it('does not double-book a room under parallel requests', function () {
    [$room, $guest] = seedRoomAndGuest();   // your factory helper, committed to DB

    $pool = Process::pool(function ($pool) use ($room, $guest) {
        foreach (range(1, 8) as $i) {
            $pool->command(sprintf(
                'php artisan app:test-book-room %d %d', $room->id, $guest->id
            ));
        }
    })->start()->wait();

    expect(Reservation::where('room_id', $room->id)->count())->toBe(1);
});
```

`app:test-book-room` is a small throwaway command (registered only when `APP_ENV=testing`) that calls the real `ReservationService`. Write the same shape of test for:

- double payment settle (same `CheckoutRequestID`, parallel callbacks)
- double refund on one payment
- double check-in on one reservation
- purchase marked `RECEIVED` twice
- parallel stock decrement below zero

A test that passes on SQLite and fails on MySQL is a **finding**, not a flaky test.

---

## 2. F-02 — Inactive accounts can log in

Three layers are required. Blocking login alone leaves existing sessions alive.

**Layer 1 — authenticate only active users** (`LoginRequest::authenticate()`):

```php
$credentials = [
    ...$this->only('email', 'password'),
    'status' => 'active',
];

if (! Auth::attempt($credentials, $this->boolean('remember'))) {
    RateLimiter::hit($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => trans('auth.failed'),   // generic: never reveal "account disabled"
    ]);
}

RateLimiter::clear($this->throttleKey());
```

**Layer 2 — kill live sessions of deactivated users** (`app/Http/Middleware/EnsureUserIsActive.php`):

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->status !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => trans('auth.failed')]);
        }

        return $next($request);
    }
}
```

Register it in the authenticated route group, before everything else.

**Layer 3 — revoke on deactivation** (wherever staff are deactivated):

```php
DB::transaction(function () use ($user) {
    $user->forceFill([
        'status'         => 'inactive',
        'remember_token' => Str::random(60),
    ])->save();

    DB::table('sessions')->where('user_id', $user->id)->delete();
});
```

**Test:**

```php
it('rejects an inactive user with a live session', function () {
    $user = User::factory()->create(['status' => 'active']);
    $this->actingAs($user)->get('/dashboard')->assertOk();

    $user->update(['status' => 'inactive']);

    $this->actingAs($user->fresh())->get('/dashboard')->assertRedirect('/login');
});

it('rejects login for an inactive user with the correct password', function () {
    $user = User::factory()->create(['status' => 'inactive', 'password' => bcrypt('secret-pass')]);

    $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});
```

---

## 3. F-05 — Password change keeps other sessions

Add Laravel's session guard to the authenticated group:

```php
Route::middleware(['auth', \Illuminate\Session\Middleware\AuthenticateSession::class, 'verified'])
```

Requires `SESSION_DRIVER=database`. Also revoke explicitly after any password change, reset, or role change:

```php
DB::table('sessions')
    ->where('user_id', $user->id)
    ->where('id', '!=', $request->session()->getId())   // keep the current session
    ->delete();

$user->forceFill(['remember_token' => Str::random(60)])->save();
```

For a password **reset** (no current session), delete all of the user's sessions.

**Test:** log in from two session contexts, change the password in one, assert the other is redirected to login.

---

## 4. F-01 and F-10 — STK push abuse

Branch membership is not authorization. Any housekeeper could trigger a payment prompt on a guest's phone.

**Permission:** seed `payments.collect` and grant it to Front Desk, Cashier, Finance, Waiter (POS) and managers only.

**Rate limiters** (`AppServiceProvider::boot()`):

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

RateLimiter::for('mpesa-stk', function (Request $r) {
    $phone = Str::of((string) $r->input('phone'))->replaceMatches('/\D/', '')->toString();

    return [
        Limit::perMinute(5)->by('u:' . $r->user()->id),
        Limit::perMinutes(10, 3)->by('p:' . $phone),     // protects the guest's phone
    ];
});

RateLimiter::for('mpesa-status', fn (Request $r) =>
    Limit::perMinute(30)->by('u:' . $r->user()->id));
```

**Routes:**

```php
Route::post('/mpesa/stkpush/initiate', [MpesaController::class, 'initiateStkPush'])
    ->middleware(['can:payments.collect', 'throttle:mpesa-stk']);

Route::get('/mpesa/status/{checkoutRequestId}', [MpesaController::class, 'queryStatus'])
    ->middleware('throttle:mpesa-status');
```

**Validation and duplicate-pending guard** (inside the controller or a FormRequest):

```php
$data = $request->validate([
    'phone'          => ['required', 'regex:/^(?:\+?254|0)?[17]\d{8}$/'],
    'invoice_id'     => ['required_without_all:order_id,reservation_id', 'exists:invoices,id'],
    'order_id'       => ['required_without_all:invoice_id,reservation_id', 'exists:restaurant_orders,id'],
    'reservation_id' => ['required_without_all:invoice_id,order_id', 'exists:reservations,id'],
]);

// Normalise to 2547XXXXXXXX
$phone = '254' . substr(preg_replace('/\D/', '', $data['phone']), -9);

// Refuse a second prompt while one is still live for the same target
$alreadyPending = MpesaTransaction::query()
    ->where('status', 'pending')
    ->where('created_at', '>', now()->subMinutes(3))
    ->where(fn ($q) => $q
        ->when($data['invoice_id']     ?? null, fn ($q, $v) => $q->orWhere('invoice_id', $v))
        ->when($data['order_id']       ?? null, fn ($q, $v) => $q->orWhere('order_id', $v))
        ->when($data['reservation_id'] ?? null, fn ($q, $v) => $q->orWhere('reservation_id', $v)))
    ->exists();

abort_if($alreadyPending, 429, 'A payment prompt is already pending for this item.');
```

**Amount rules:** STK accepts whole shillings only. Decide explicitly how a balance like `1500.50` is handled (round up and record the overpayment, or reject and require cash for the cents). Never silently truncate.

**Tests:** user without `payments.collect` gets 403; sixth request in a minute gets 429; second prompt on the same invoice within 3 minutes is refused.

---

## 5. F-07 — Production boot guard

Do not rely on remembering to flip `.env` values. Make the app refuse to start misconfigured (`AppServiceProvider::boot()`):

```php
if ($this->app->isProduction()) {
    $problems = [];

    if (config('app.debug'))                        $problems[] = 'APP_DEBUG must be false';
    if (! str_starts_with(config('app.url'), 'https://')) $problems[] = 'APP_URL must be https';
    if (! config('session.secure'))                 $problems[] = 'SESSION_SECURE_COOKIE must be true';
    if (config('session.same_site') === null)       $problems[] = 'SESSION_SAME_SITE must be set';
    if (empty(config('services.mpesa.webhook_ips'))) $problems[] = 'MPESA_WEBHOOK_IPS must not be empty';
    if (empty(env('TRUSTED_PROXIES')))              $problems[] = 'TRUSTED_PROXIES must be set';
    if (empty(env('BACKUP_ARCHIVE_PASSWORD')))      $problems[] = 'BACKUP_ARCHIVE_PASSWORD must be set';
    if (config('services.mpesa.env') !== 'live')    $problems[] = 'MPESA_ENV should be live in production';

    if ($problems) {
        throw new \RuntimeException("Unsafe production config:\n - " . implode("\n - ", $problems));
    }
}
```

Required `.env` values for production:

```env
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_DRIVER=database
```

Note: `env()` returns null once config is cached. Move these into `config/` files and read via `config()`.

---

## 6. F-08 — Cross-branch references

The bug is not only in maintenance. **Every `exists:` rule that is not branch-scoped is the same bug.** Find them all:

```bash
grep -rn "exists:" app/Http/ | grep -v "branch"
```

Fix pattern:

```php
use Illuminate\Validation\Rule;

$branchId = $request->user()->branch_id;
$isSuper  = $request->user()->isSuperAdmin();

'room_id' => ['required', Rule::exists('rooms', 'id')
    ->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId))],

'assigned_to' => ['nullable', Rule::exists('users', 'id')
    ->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId))
    ->where('status', 'active')],
```

If `rooms` has no `branch_id` column (branch via floor), validate with a query or a custom rule through the relationship instead.

For route-model binding, do not trust the global scope alone. Add `$this->authorize('view', $model)` and have the policy compare `branch_id`.

Audit every resource: guest, reservation, stay, folio, invoice, payment, refund, expense, document, order, ticket, employee, export, notification.

**Test:** user in Branch A submits a Branch B room ID, expect a validation error. Repeat per resource.

---

## 7. F-04 — Password-reset enumeration

```php
public function store(Request $request)
{
    $request->validate(['email' => ['required', 'email']]);

    Password::sendResetLink($request->only('email'));   // deliberately ignore the result

    return back()->with('status',
        'If that address is registered, a reset link has been sent.');
}
```

Remaining steps:

- Make the reset notification implement `ShouldQueue` so response time does not differ between known and unknown addresses.
- Rate limit by normalised email and by IP:

```php
RateLimiter::for('password-reset', fn (Request $r) => [
    Limit::perMinute(3)->by('e:' . Str::lower((string) $r->input('email'))),
    Limit::perMinute(10)->by('ip:' . $r->ip()),
]);
```

- Set `config('auth.passwords.users.expire')` to 30 or lower.
- Confirm reset tokens cannot be reused after a successful reset.
- Confirm reset links use `APP_URL`, not the request host header (set `TrustHosts`).

---

## 8. F-03 — CSV formula injection

Apply to **text columns only**. Prefixing numbers turns `-500.00` into `'-500.00` and breaks accounting imports.

```php
<?php

namespace App\Support;

class Csv
{
    public static function safe(mixed $value): string
    {
        $s = (string) $value;

        // Neutralise cells a spreadsheet would treat as a formula
        return $s !== '' && preg_match('/^[=+\-@\t\r]/', $s) === 1
            ? "'" . $s
            : $s;
    }
}
```

Use it for guest names, notes, special requests, product, supplier and category names, staff names, expense descriptions, audit log values, anything a user typed. Do **not** use it for amounts, quantities, dates or IDs.

**Test:** create a guest named `=HYPERLINK("http://evil.test","x")`, export, assert the cell starts with `'`.

---

## 9. F-09 — M-Pesa PII in payloads and logs

Store what reconciliation needs, not the full callback.

```php
// Persist a minimal, purpose-built record instead of the raw payload
$transaction->update([
    'result_code'    => data_get($payload, 'Body.stkCallback.ResultCode'),
    'result_desc'    => data_get($payload, 'Body.stkCallback.ResultDesc'),
    'receipt_number' => $receipt,
    'phone_last4'    => substr($phone, -4),         // for support lookups
    // callback_payload: dropped, or stored encrypted with a retention window
]);
```

If you must keep the raw payload for disputes, encrypt it (`'callback_payload' => 'encrypted:array'` cast) and schedule a purge after 90 days.

**Log redaction helper:**

```php
function maskPhone(string $phone): string
{
    return substr($phone, 0, 5) . '****' . substr($phone, -2);
}
```

Rules for all payment logging:

- Never log full phone numbers, passkeys, consumer secrets, OAuth tokens, or the `Authorization` header.
- Log `checkout_request_id` and `receipt_number` instead.
- Check exception handlers: a failed HTTP call to Daraja can dump the request body into the log.

---

## 10. Items from the first audit still to verify

**Seeder credentials.** Open `database/seeders`. If the first SuperAdmin has a hardcoded or guessable password, closing `/register` only replaced an open door with a known key. Fix:

```php
// Seed from env, force change on first login, refuse in production if unset
$password = env('INITIAL_ADMIN_PASSWORD') ?? throw new RuntimeException('Set INITIAL_ADMIN_PASSWORD');
```

**Git history secrets.** The repo is public. Scan the whole history, not just the current tree:

```bash
gitleaks detect --source . --log-opts="--all"
# or
trufflehog git file://. --only-verified
```

Rotate anything that ever appeared, even if you deleted the line later. Deleted is not removed from history.

**Public repo, proprietary license.** Decide: make it private, or add a real `LICENSE` and remove "proprietary" language.

**Prompt files at the repo root** (`CLAUDE.md`, `AGENTS.md`, the two "Master Development Prompt" files): move to `docs/` or remove.

---

## 11. Structural fixes (database-level guarantees)

### 11.1 Double-booking: make the database refuse it

Row locks only protect code paths that take the lock. A unique constraint protects every path.

```php
Schema::create('reservation_nights', function (Blueprint $table) {
    $table->id();
    $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
    $table->foreignId('room_id')->constrained();
    $table->date('night');
    $table->unique(['room_id', 'night']);     // the invariant
});
```

Insert one row per occupied night **in the same transaction** as the reservation. Delete the rows on cancel, no-show, or date change (rewrite them atomically). A race now fails with a unique violation, which you catch and turn into "room no longer available".

### 11.2 Never let a callback alone mark money as received

Treat the callback as a hint, then verify server-side before settling:

```php
// After receiving a success callback
$transaction = MpesaTransaction::where('checkout_request_id', $id)->lockForUpdate()->first();
abort_unless($transaction, 404);                       // unknown ID: ignore

$confirmed = $mpesa->stkQuery($id);                    // server-side Daraja STK Query

if ($confirmed->resultCode !== 0) { /* do not settle */ }

// Amount must match what we asked for
if (! Money::equals($callbackAmount, $transaction->amount)) {
    // flag for manual review, do not auto-settle
}
```

Additional rules:

- Only definitive result codes may mark a transaction `failed`. "Still processing" must stay `pending`.
- STK Query does not return the M-Pesa receipt number. A transaction settled by reconciliation needs a follow-up step (C2B confirmation, Transaction Status API, or statement import) so finance can match it to the M-Pesa statement.
- Add a second factor to the callback URL, such as a per-transaction secret in the path, because the IP allowlist depends on correct `TRUSTED_PROXIES`.

### 11.3 Content-Security-Policy and Alpine

Standard Alpine evaluates expressions with `new Function`, which requires `'unsafe-eval'`. Check the CSP header and the browser console on every page. Either:

- migrate to the CSP-safe build (`@alpinejs/csp`), or
- accept `unsafe-eval` knowingly and document that the CSP is weaker than it looks.

Add one browser test (Dusk or Playwright) that loads the main pages and fails on CSP violations. Self-host fonts to drop the Google Fonts exception.

### 11.4 Authorization matrix test

This is where multi-branch systems actually leak. Generate it from the route list:

```php
dataset('routes', fn () => collect(Route::getRoutes())
    ->filter(fn ($r) => in_array('auth', $r->gatherMiddleware()))
    ->map(fn ($r) => [$r->methods()[0], $r->uri()]));

it('denies a low-privilege user on every sensitive route', function ($method, $uri) {
    $user = User::factory()->withRole('Housekeeper')->create();
    $this->actingAs($user)->call($method, $uri)->assertStatus(403);
})->with('routes');
```

Maintain an explicit allow-list of routes each role may reach. Anything not on the list must return 403. Then repeat across branches (Branch A user against Branch B record IDs).

---

## 12. Design-level gaps (schedule, do not forget)

| Gap | Approach | Effort |
| :-- | :--- | :-- |
| MFA for Admin, Finance, SuperAdmin | `pragmarx/google2fa-laravel` or Fortify 2FA; enforce by role | 2-3 days |
| Refund maker-checker | `requested_by` / `approved_by` columns, policy rejects `requested_by === approved_by` | 1-2 days |
| Tamper-evident audit log | Hash chain (`hash = sha256(prev_hash + row)`) or ship to append-only storage; revoke UPDATE/DELETE on the table for the app DB user | 2 days |
| Guest ID scan encryption at rest | Store on a private disk, encrypt before write, stream decrypted via authorised controller | 2 days |
| Kenya DPA: consent, retention, export, erasure | Consent field on guest, retention job, data-export and anonymise actions | 1 week |
| Backup restore drill | Restore to an isolated DB, document the steps and the time taken | 1 day |
| Horizon vs Windows | Horizon needs `pcntl`/`posix`. Pick Linux for production or drop Horizon. Remove whichever deployment path you abandon | decision |

---

## 13. Definition of done

A finding is closed only when all of these are true:

- [ ] A failing test existed before the fix
- [ ] The test passes on **MySQL**
- [ ] The fix is a single, small PR
- [ ] The PR links the finding ID
- [ ] Any related code path found by grep has been checked (the same bug rarely lives in one place)
- [ ] CI runs the test on every push

**Before first real deployment:** a human tries to break the running app from the browser using two accounts in different branches with different roles. Static review does not replace this.
