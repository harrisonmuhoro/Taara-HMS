<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Hit both limiters with 15-minute soft lock (900 seconds)
            RateLimiter::hit($this->throttleKeyAccount(), 900);
            RateLimiter::hit($this->throttleKeyIp(), 900);

            // Log ALL failures with IP + User Agent + Timestamp
            Log::warning('Failed login attempt', [
                'email' => $this->string('email')->value(),
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Authentication successful, clear both limiters
        RateLimiter::clear($this->throttleKeyAccount());
        RateLimiter::clear($this->throttleKeyIp());

        // Alert on off-hours logins (Assuming off-hours are 8 PM to 6 AM)
        $hour = now()->hour;
        if ($hour < 6 || $hour >= 20) {
            Log::alert('Off-hours login detected', [
                'user_id' => Auth::id(),
                'email' => $this->string('email')->value(),
                'ip' => $this->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // 5 attempts per account
        if (RateLimiter::tooManyAttempts($this->throttleKeyAccount(), 5)) {
            $this->lockoutResponse($this->throttleKeyAccount());
        }

        // 20 attempts per IP
        if (RateLimiter::tooManyAttempts($this->throttleKeyIp(), 20)) {
            $this->lockoutResponse($this->throttleKeyIp());
        }
    }

    /**
     * Throw the lockout response and event.
     */
    protected function lockoutResponse($key)
    {
        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the account.
     */
    public function throttleKeyAccount(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|account');
    }

    /**
     * Get the rate limiting throttle key for the IP address.
     */
    public function throttleKeyIp(): string
    {
        return $this->ip().'|ip';
    }
}
