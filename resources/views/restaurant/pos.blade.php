<x-app-layout title="POS Terminal">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Point of Sale</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="posApp()">
        
        {{-- Menu Grid (Left 2/3) --}}
        <div class="lg:col-span-2 flex flex-col lg:h-[calc(100vh-12rem)]">
            {{-- Categories --}}
            <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar">
                <button type="button" @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="px-5 py-2.5 rounded-xl font-medium text-sm whitespace-nowrap transition-all">
                    All Items
                </button>
                @foreach($categories as $category)
                    <button type="button" @click="activeCategory = {{ $category->id }}" 
                            :class="activeCategory === {{ $category->id }} ? 'bg-brand-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                            class="px-5 py-2.5 rounded-xl font-medium text-sm whitespace-nowrap transition-all">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            {{-- Items Grid --}}
            <div class="flex-1 overflow-y-auto pr-2 no-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        @foreach($category->items as $item)
                            <div x-show="activeCategory === 'all' || activeCategory === {{ $category->id }}"
                                 @click="addToCart({{ $item->toJson() }})"
                                 class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-4 cursor-pointer hover:border-brand-500 hover:shadow-md transition-all flex flex-col h-full group relative overflow-hidden">
                                <div class="absolute inset-0 bg-brand-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="font-medium text-slate-900 dark:text-white leading-tight mb-2">{{ $item->name }}</div>
                                <div class="mt-auto flex justify-between items-end">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold">KES {{ number_format($item->price, 2) }}</span>
                                    @if($item->prep_time_minutes > 0)
                                        <span class="text-[10px] text-slate-400">{{ $item->prep_time_minutes }}m</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Current Order (Right 1/3) --}}
        <div class="lg:col-span-1 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col lg:h-[calc(100vh-12rem)] shadow-sm relative overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    Current Order
                </h2>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex gap-3 items-center group bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-100 dark:border-slate-700/30">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-slate-900 dark:text-white" x-text="item.name"></div>
                            <div class="text-xs text-brand-600 dark:text-brand-400 font-medium" x-text="'KES ' + Number(item.price).toFixed(2)"></div>
                        </div>
                        <div class="flex items-center gap-2 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-1 shadow-sm">
                            <button type="button" @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded text-lg">&minus;</button>
                            <span class="w-6 text-center text-sm font-medium text-slate-700 dark:text-slate-300" x-text="item.quantity"></span>
                            <button type="button" @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded text-lg">&plus;</button>
                        </div>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-400 space-y-3 opacity-60">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    <p class="text-sm">Cart is empty</p>
                </div>
            </div>

            {{-- Checkout Footer --}}
            <div class="p-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/40">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400">
                        <span>Subtotal</span>
                        <span x-text="'KES ' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400">
                        <span>Tax (16%)</span>
                        <span x-text="'KES ' + tax.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700/50">
                        <span>Total</span>
                        <span class="text-brand-600 dark:text-brand-400" x-text="'KES ' + total.toFixed(2)"></span>
                    </div>
                </div>

                <form action="{{ route('restaurant.pos.checkout') }}" method="POST" id="checkout-form" @submit.prevent="submitCheckout()">
                    @csrf
                    <template x-for="(item, index) in cart" :key="index">
                        <div>
                            <input type="hidden" :name="'items['+index+'][menu_item_id]'" :value="item.id">
                            <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                        </div>
                    </template>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Method</label>
                        <select name="payment_method" x-model="paymentMethod" class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mpesa">M-Pesa (STK Push)</option>
                            <option value="room_charge">Charge to Room</option>
                        </select>
                    </div>

                    {{-- M-Pesa Phone Input --}}
                    <div x-show="paymentMethod === 'mpesa'" class="mb-4" style="display:none;">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Customer Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="tel" name="phone" x-model="mpesaPhone" placeholder="07XXXXXXXX or 254..." class="w-full pl-9 text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">An STK Push prompt will be sent immediately to the customer.</p>
                    </div>

                    <div x-show="paymentMethod === 'room_charge'" class="mb-4" style="display:none;">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Select Room/Guest</label>
                        <select name="reservation_id" :required="paymentMethod === 'room_charge'" class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                            <option value="">Select a checked-in guest...</option>
                            @foreach($activeReservations as $res)
                                <option value="{{ $res->id }}">Room {{ $res->room->room_number ?? '?' }} - {{ $res->guest->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" 
                            :disabled="cart.length === 0"
                            :class="paymentMethod === 'mpesa' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-brand-600 hover:bg-brand-700'"
                            class="w-full py-3 px-4 disabled:bg-slate-300 dark:disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition-colors flex justify-center items-center gap-2">
                        <span x-text="paymentMethod === 'mpesa' ? 'Send M-Pesa STK Push' : 'Place Order'"></span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- M-Pesa Processing Modal --}}
        <div x-show="mpesaModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display:none;" x-cloak>
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" @click.outside="if (mpesaState === 'completed' || mpesaState === 'failed') closeMpesaModal()">
                <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg leading-tight">M-Pesa POS Checkout</h3>
                            <p class="text-emerald-100 text-xs">Lipa Na M-Pesa Online</p>
                        </div>
                    </div>
                    <button type="button" @click="closeMpesaModal()" class="text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-emerald-50 dark:bg-emerald-950/30 rounded-xl p-4 flex items-center justify-between border border-emerald-100 dark:border-emerald-900/30">
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Order Total</div>
                            <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400" x-text="'KES ' + total.toFixed(2)"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Customer Phone</div>
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200" x-text="mpesaPhone"></div>
                        </div>
                    </div>

                    <div class="text-center py-6">
                        <template x-if="mpesaState === 'sending' || mpesaState === 'waiting'">
                            <div>
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4 text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                </div>
                                <h4 class="font-bold text-base text-slate-900 dark:text-white" x-text="mpesaState === 'sending' ? 'Sending STK Push...' : 'Waiting for Guest PIN...'"></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto" x-text="mpesaStatusMsg"></p>
                            </div>
                        </template>

                        <template x-if="mpesaState === 'completed'">
                            <div>
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4 text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h4 class="font-bold text-base text-emerald-700 dark:text-emerald-400">Payment Confirmed!</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="'Receipt: ' + mpesaReceipt"></p>
                                <div class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-300">Order successfully completed and recorded.</div>
                            </div>
                        </template>

                        <template x-if="mpesaState === 'failed'">
                            <div>
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4 text-red-600 dark:text-red-400">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <h4 class="font-bold text-base text-red-600 dark:text-red-400">Payment Failed</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto" x-text="mpesaStatusMsg"></p>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <template x-if="mpesaState === 'completed'">
                            <button type="button" @click="finishCompletedOrder()" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                                Start Next Order
                            </button>
                        </template>
                        <template x-if="mpesaState === 'failed'">
                            <div class="flex gap-2 w-full">
                                <button type="button" @click="closeMpesaModal()" class="flex-1 py-2.5 px-4 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                                    Close
                                </button>
                                <button type="button" @click="retryStkPush()" class="flex-1 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all">
                                    Retry STK Push
                                </button>
                            </div>
                        </template>
                        <template x-if="mpesaState === 'waiting'">
                            <button type="button" @click="closeMpesaModal()" class="w-full py-2 px-3 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                Dismiss (Order remains pending until customer pays)
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                activeCategory: 'all',
                cart: [],
                paymentMethod: 'cash',
                mpesaPhone: '',
                mpesaModalOpen: false,
                mpesaState: 'idle', // 'idle' | 'sending' | 'waiting' | 'completed' | 'failed'
                mpesaStatusMsg: '',
                mpesaReceipt: '',
                checkoutRequestId: '',
                pollTimer: null,
                createdOrderId: null,
                
                addToCart(item) {
                    const existing = this.cart.find(i => i.id === item.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cart.push({
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            quantity: 1
                        });
                    }
                },
                
                updateQuantity(index, change) {
                    const newQty = this.cart[index].quantity + change;
                    if (newQty > 0) {
                        this.cart[index].quantity = newQty;
                    } else {
                        this.cart.splice(index, 1);
                    }
                },
                
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                
                get tax() {
                    return this.subtotal * 0.16;
                },
                
                get total() {
                    return this.subtotal + this.tax;
                },

                async submitCheckout() {
                    if (this.cart.length === 0) return;

                    if (this.paymentMethod === 'mpesa') {
                        if (!this.mpesaPhone.trim()) {
                            alert('Please enter the customer M-Pesa phone number.');
                            return;
                        }
                        this.mpesaModalOpen = true;
                        this.mpesaState = 'sending';
                        this.mpesaStatusMsg = 'Sending STK Push prompt to ' + this.mpesaPhone.trim() + '...';
                        this.initiateMpesaOrder();
                    } else {
                        document.getElementById('checkout-form').submit();
                    }
                },

                async initiateMpesaOrder() {
                    try {
                        const payload = {
                            items: this.cart.map(item => ({ menu_item_id: item.id, quantity: item.quantity })),
                            payment_method: 'mpesa',
                            phone: this.mpesaPhone.trim(),
                        };

                        const response = await fetch('{{ route('restaurant.pos.checkout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (data.success && data.checkout_request_id) {
                            this.checkoutRequestId = data.checkout_request_id;
                            this.createdOrderId = data.order_id;
                            this.mpesaState = 'waiting';
                            this.mpesaStatusMsg = 'Prompt sent to ' + this.mpesaPhone + '. Awaiting customer M-Pesa PIN...';
                            this.startPollingStatus();
                        } else {
                            this.mpesaState = 'failed';
                            this.mpesaStatusMsg = data.message || 'Failed to initiate STK push.';
                        }
                    } catch (err) {
                        this.mpesaState = 'failed';
                        this.mpesaStatusMsg = 'Network error or server unreachable. Please try again.';
                    }
                },

                startPollingStatus() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                    let attempts = 0;
                    const maxAttempts = 20; // 60 seconds

                    this.pollTimer = setInterval(async () => {
                        attempts++;
                        if (attempts > maxAttempts) {
                            clearInterval(this.pollTimer);
                            if (this.mpesaState === 'waiting') {
                                this.mpesaState = 'failed';
                                this.mpesaStatusMsg = 'Transaction timed out. If customer entered PIN, the order will complete automatically.';
                            }
                            return;
                        }

                        try {
                            const res = await fetch('/api/mpesa/status/' + encodeURIComponent(this.checkoutRequestId), {
                                headers: { 'Accept': 'application/json' }
                            });
                            const resData = await res.json();

                            if (resData.success) {
                                if (resData.status === 'completed') {
                                    clearInterval(this.pollTimer);
                                    this.mpesaState = 'completed';
                                    this.mpesaReceipt = resData.transaction_id || 'Confirmed';
                                    this.cart = [];
                                } else if (resData.status === 'failed') {
                                    clearInterval(this.pollTimer);
                                    this.mpesaState = 'failed';
                                    this.mpesaStatusMsg = resData.result_desc || 'Customer cancelled or transaction failed.';
                                }
                            }
                        } catch (e) {
                            // Keep polling despite network hiccups
                        }
                    }, 3000);
                },

                retryStkPush() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                    this.mpesaState = 'sending';
                    this.mpesaStatusMsg = 'Retrying STK Push prompt to ' + this.mpesaPhone.trim() + '...';
                    this.initiateMpesaOrder();
                },

                closeMpesaModal() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                    this.mpesaModalOpen = false;
                    if (this.mpesaState === 'completed') {
                        this.cart = [];
                        window.location.reload();
                    }
                    this.mpesaState = 'idle';
                },

                finishCompletedOrder() {
                    this.closeMpesaModal();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
