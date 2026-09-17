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
                                    <span class="text-brand-600 dark:text-brand-400 font-bold">${{ number_format($item->price, 2) }}</span>
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
                            <div class="text-xs text-brand-600 dark:text-brand-400 font-medium" x-text="'$' + Number(item.price).toFixed(2)"></div>
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
                        <span x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400">
                        <span>Tax (16%)</span>
                        <span x-text="'$' + tax.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700/50">
                        <span>Total</span>
                        <span class="text-brand-600 dark:text-brand-400" x-text="'$' + total.toFixed(2)"></span>
                    </div>
                </div>

                <form action="{{ route('restaurant.pos.checkout') }}" method="POST" id="checkout-form">
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
                            <option value="room_charge">Charge to Room</option>
                        </select>
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
                            class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 dark:disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition-colors flex justify-center items-center gap-2">
                        <span>Place Order</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </form>
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
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
