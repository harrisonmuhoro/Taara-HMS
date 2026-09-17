<x-app-layout title="New Reservation">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Reservations', 'url' => route('reservations.index')],
            ['label' => 'New Reservation', 'url' => '#'],
        ]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">New Reservation</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Fill in the details below to create a new reservation.</p>
    </x-slot>

    @if ($errors->any())
        <x-alert type="danger" title="Please fix the following errors:" class="mb-6">
            <ul class="list-disc list-inside space-y-1 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" title="Reservation could not be created" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('reservations.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── LEFT / MAIN COLUMN ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Guest Selection --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">1</span>
                        Guest Information
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label for="guest_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Select Guest <span class="text-red-500">*</span></label>
                            <select id="guest_id" name="guest_id" required
                                    class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('guest_id') border-red-500 @enderror">
                                <option value="">— Choose a guest —</option>
                                @foreach ($guests as $guest)
                                    <option value="{{ $guest->id }}" {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                                        {{ $guest->first_name }} {{ $guest->last_name }} — {{ $guest->email }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('guest_id')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Dates & Guests --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">2</span>
                        Stay Dates
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Check-in Date <span class="text-red-500">*</span></label>
                            <input type="date" id="check_in_date" name="check_in_date" min="{{ today()->toDateString() }}"
                                   value="{{ old('check_in_date', now()->addDay()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}"
                                   required
                                   class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('check_in_date') border-red-500 @enderror">
                            <x-input-error :messages="$errors->get('check_in_date')" class="mt-2" />
                        </div>
                        <div>
                            <label for="check_out_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Check-out Date <span class="text-red-500">*</span></label>
                            <input type="date" id="check_out_date" name="check_out_date"
                                   value="{{ old('check_out_date', now()->addDays(2)->toDateString()) }}"
                                   required
                                   class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('check_out_date') border-red-500 @enderror">
                            <x-input-error :messages="$errors->get('check_out_date')" class="mt-2" />
                        </div>
                        <div>
                            <label for="adults" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Adults <span class="text-red-500">*</span></label>
                            <input type="number" id="adults" name="adults" value="{{ old('adults', 1) }}" min="1" max="10" required
                                   class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                        <div>
                            <label for="children" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Children</label>
                            <input type="number" id="children" name="children" value="{{ old('children', 0) }}" min="0" max="10"
                                   class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Room & Branch --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">3</span>
                        Room &amp; Branch
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label for="branch_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Branch <span class="text-red-500">*</span></label>
                            <select id="branch_id" name="branch_id" required
                                    class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('branch_id') border-red-500 @enderror">
                                <option value="">— Select a branch —</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                        </div>
                        <div>
                            <label for="room_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Room <span class="text-red-500">*</span></label>
                            <select id="room_id" name="room_id" required
                                    class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('room_id') border-red-500 @enderror">
                                <option value="">— Select an available room —</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} — {{ $room->roomType->name ?? 'N/A' }}
                                        (KES {{ number_format($room->roomType->base_rate ?? 0, 2) }}/night)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">4</span>
                        Special Requests
                    </h2>
                    <textarea id="notes" name="notes" rows="4" placeholder="Any special requests, notes, or requirements…"
                              class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- ── RIGHT / SUMMARY COLUMN ── --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 sticky top-28">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Summary</h2>

                    <div id="booking-summary" class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Check-in</span>
                            <span id="summary-checkin" class="font-medium text-slate-900 dark:text-white">—</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Check-out</span>
                            <span id="summary-checkout" class="font-medium text-slate-900 dark:text-white">—</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Nights</span>
                            <span id="summary-nights" class="font-medium text-slate-900 dark:text-white">—</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Room</span>
                            <span id="summary-room" class="font-medium text-slate-900 dark:text-white">—</span>
                        </div>
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-3 mt-3">
                            <div class="flex justify-between text-base font-semibold text-slate-900 dark:text-white">
                                <span>Estimated Total</span>
                                <span id="summary-total" class="text-brand-600 dark:text-brand-400">—</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Includes 16% VAT. Final amount calculated by system.</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit"
                                class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 text-sm hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-brand-500">
                            Create Reservation
                        </button>
                        <a href="{{ route('reservations.index') }}"
                           class="block w-full py-3 px-4 text-center bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-colors text-sm">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Live summary script --}}
    <script>
    (function () {
        const ciEl  = document.getElementById('check_in_date');
        const coEl  = document.getElementById('check_out_date');
        const roomEl = document.getElementById('room_id');

        function fmt(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('en-KE', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function getRateFromRoom() {
            const opt = roomEl.options[roomEl.selectedIndex];
            if (!opt || !opt.value) return 0;
            const match = opt.text.match(/KES ([\d,]+\.?\d*)\//);
            return match ? parseFloat(match[1].replace(/,/g, '')) : 0;
        }

        function update() {
            const ci = ciEl.value, co = coEl.value;
            document.getElementById('summary-checkin').textContent  = fmt(ci);
            document.getElementById('summary-checkout').textContent = fmt(co);

            let nights = 0;
            if (ci && co && co > ci) {
                nights = Math.round((new Date(co) - new Date(ci)) / 86400000);
            }
            document.getElementById('summary-nights').textContent = nights || '—';

            const opt = roomEl.options[roomEl.selectedIndex];
            document.getElementById('summary-room').textContent = opt && opt.value
                ? 'Room ' + opt.text.split(' — ')[0].replace('Room ', '')
                : '—';

            const rate = getRateFromRoom();
            if (nights > 0 && rate > 0) {
                const subtotal = rate * nights;
                const total    = subtotal * 1.16;
                document.getElementById('summary-total').textContent = 'KES ' + total.toLocaleString('en-KE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                document.getElementById('summary-total').textContent = '—';
            }
        }

        [ciEl, coEl, roomEl].forEach(el => el && el.addEventListener('change', update));
        update();
    })();
    </script>
</x-app-layout>
