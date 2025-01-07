<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <script src="https://js.stripe.com/v3/"></script>
    <div class="p-4">
        <div class="inline-flex">
            <div>
                <button wire:click="goBack">
                    <i class="fa-solid fa-arrow-left me-4"></i>
                </button>
            </div>
            <h2 class="text-xl font-bold mb-4">Checkout Details</h2>
        </div>

        <!-- Order Summary -->
        <div class="mb-4">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="border-t-2 border-stone-300">
                    @foreach ($cart as $item)
                        <tr>
                            <td class="py-3">{{ $item['name'] }}</td>
                            <td class="text-right py-3">{{ $item['quantity'] }}</td>
                            <td class="text-right py-3">฿{{ number_format($item['price'], 2) }}</td>
                            <td class="text-right py-3">{{ $item['discount'] }}%</td>
                            <td class="text-right py-3">
                                ฿{{ number_format($item['price'] * $item['quantity'] * (1 - $item['discount'] / 100), 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-stone-300">
                    <tr>
                        <td colspan="4" class="text-right font-bold pt-4">Subtotal:</td>
                        <td class="text-right pt-4">฿{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right font-bold">Tax:</td>
                        <td class="text-right">฿{{ number_format($tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right font-bold text-green-500">Total:</td>
                        <td class="text-right font-bold">฿{{ number_format($payableAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Delivery Option -->
        <div class="mb-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.live="isDelivery" id="delivery" class="rounded border-gray-300">
                <label for="delivery" class="text-white">ต้องการให้จัดส่งสินค้า</label>
            </div>

            @if ($isDelivery)
                <!-- Delivery Address -->
                <div class="bg-white p-4 rounded-lg shadow-sm border mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ที่อยู่จัดส่ง
                    </label>
                    <textarea wire:model.live.debounce.500ms="deliveryAddress" rows="3"
                        class="ps-2 w-full rounded-md border-gray-300 shadow-sm text-gray-700 @error('deliveryAddress') border-red-500 @enderror"
                        placeholder="กรุณากรอกที่อยู่จัดส่ง"></textarea>
                    @error('deliveryAddress')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

        <!-- Payment Methods (Show only for pay_now option) -->
        @if (!$isDelivery || $isDelivery)
            <div class="mb-6">
                <div class="flex gap-4 mb-4">
                    <button wire:click="$set('paymentMethod', 'cash')"
                        class="px-6 py-3 rounded-lg flex items-center gap-2 {{ $paymentMethod === 'cash' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 3a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm8 0a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1zm-8 4a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm8 0a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        เงินสด
                    </button>
                    <button wire:click="$set('paymentMethod', 'card')"
                        class="px-6 py-3 rounded-lg flex items-center gap-2 {{ $paymentMethod === 'card' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                            <path fill-rule="evenodd"
                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                clip-rule="evenodd" />
                        </svg>
                        บัตรเครดิต
                    </button>
                </div>

                <!-- Cash Payment Panel -->
                @if ($paymentMethod === 'cash')
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    จำนวนเงินที่รับ (Amount Received)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400">฿</span>
                                    <input type="number" wire:model.live="cashReceived"
                                        class="pl-8 pr-4 py-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-700"
                                        step="0.01" min="{{ $payableAmount }}" placeholder="0.00">
                                </div>
                            </div>

                            @if ($cashReceived > 0)
                                <div class="bg-blue-50 p-4 rounded-md space-y-2 text-gray-600">
                                    <div class="flex justify-between text-sm">
                                        <span>ยอดที่ต้องชำระ:</span>
                                        <span class="font-medium">฿{{ number_format($payableAmount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span>รับเงิน:</span>
                                        <span class="font-medium">฿{{ number_format($cashReceived, 2) }}</span>
                                    </div>
                                    @if ($change > 0)
                                        <div class="flex justify-between text-lg font-bold text-green-600">
                                            <span>เงินทอน:</span>
                                            <span>฿{{ number_format($change, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Card Payment Panel -->
                @if ($paymentMethod === 'card')
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        @if ($stripeClientSecret)
                            <div id="card-element" class="mb-4 min-h-[40px] border p-3 rounded">
                                <!-- Stripe Elements จะถูกแสดงที่นี่ -->
                            </div>
                            <div id="card-errors" class="text-red-500 text-sm mb-4" role="alert"></div>
                        @else
                            <div class="text-center py-4">
                                <span class="text-gray-500">กำลังโหลด...</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Submit Button -->
        <div class="mt-6">
            <button wire:click="processCheckout"
                class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="processCheckout">
                    ยืนยันการชำระเงิน
                </span>
                <span wire:loading wire:target="processCheckout">
                    กำลังดำเนินการ...
                </span>
            </button>
        </div>

        {{-- <div class="mt-6">
            <button wire:click="$dispatch('processStripePayment', { clientSecret: '{{ $stripeClientSecret }}' })"
                class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="processCheckout">
                    ยืนยันการชำระเงิน
                </span>
                <span wire:loading wire:target="processCheckout">
                    กำลังดำเนินการ...
                </span>
            </button>
        </div> --}}

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        // ตรวจสอบว่ามี Stripe key หรือไม่
        const stripeKey = '{{ config('services.stripe.key') }}';
        if (!stripeKey) {
            console.error('Stripe publishable key is missing');
            return;
        }

        const stripe = Stripe(stripeKey);
        let elements;
        let card;

        // สร้าง card element เมื่อได้รับ client secret
        Livewire.on('stripeClientSecretUpdated', (clientSecret) => {
            console.log('Received client secret:', clientSecret);
            
            if (elements) {
                card.destroy();
            }

            elements = stripe.elements();
            card = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                }
            });

            card.mount('#card-element');

            card.addEventListener('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        });

        // จัดการการชำระเงิน
        Livewire.on('processStripePayment', async ({ clientSecret }) => {
            console.log('Processing payment with secret:', clientSecret);
            
            try {
                const result = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: card,
                    }
                });

                if (result.error) {
                    document.getElementById('card-errors').textContent = result.error.message;
                } else {
                    @this.processCheckout();
                }
            } catch (error) {
                console.error('Stripe error:', error);
                document.getElementById('card-errors').textContent = 'เกิดข้อผิดพลาดในการชำระเงิน';
            }
        });
    });
</script>
@endpush
