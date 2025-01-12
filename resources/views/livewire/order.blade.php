<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <!-- Header Section -->
    <div class="content-header flex flex-col lg:flex-row gap-4 mb-4">
        <!-- Categories -->
        <div class="flex flex-wrap gap-2">
            <button class="{{ is_null($selectedCategory) ? 'btn-warning' : 'btn-primary' }}"
                wire:click="changeCategory(null)">
                ทั้งหมด
            </button>
            @foreach ($categories as $category)
                <button class="{{ $selectedCategory == $category->id ? 'btn-warning' : 'btn-primary' }}"
                    wire:click="changeCategory({{ $category->id }})">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
        <!-- Search -->
        <div class="flex-grow">
            <form wire:submit.prevent="updateProducts" class="flex items-center justify-end">
                <input type="text" 
                    wire:model.debounce.500ms="searchTerm" 
                    placeholder="Search products..."
                    class="border-2 border-gray-300 rounded-md px-3 py-2 w-full md:w-80 focus:outline-none 
                    focus:ring focus:ring-blue-400 focus:border-blue-400 
                    transition-all duration-300 text-gray-800">
                <button type="submit" class="btn-primary ml-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Product List -->
        <div class="backdrop-blur-lg p-4 shadow-lg bg-white/30 rounded-xl lg:col-span-2 order-2 lg:order-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($products as $product)
                    <div class="flex flex-col cursor-pointer transition duration-300 ease-in-out transform hover:scale-105"
                        wire:click="addToCart({{ $product->id }})">
                        <div class="rounded-lg space-y-2 bg-neutral-100 shadow-md text-gray-800">
                            @if ($product->img)
                                <img src="/storage/{{ $product->img }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-32 object-cover rounded-t-lg">
                            @else
                                <img src="https://via.placeholder.com/160x120" 
                                    alt="No image"
                                    class="w-full h-32 object-cover rounded-t-lg">
                            @endif
                            <div class="p-3">
                                <span class="block text-md">{{ $product->name }}</span>
                                <span class="block font-bold">฿{{ number_format($product->price) }}</span>
                                <span class="block text-sm text-gray-500">ประเภท: {{ $product->category->name }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full flex justify-center items-center text-2xl text-white py-8">ไม่พบสินค้า</p>
                @endforelse
            </div>
        </div>

        <!-- Cart -->
        <div class="p-4 bg-white shadow-lg rounded-xl order-1 lg:order-2 text-gray-800">
            <div class="sticky top-4">
                <!-- Cart Header -->
                <div class="flex justify-between items-center border-b-2 border-stone-300 pb-2">
                    <span class="font-semibold text-lg">รายการ</span>
                    <button class="rounded-full px-4 py-2 bg-gray-300 hover:bg-gray-400" wire:click="clearCart">
                        <i class="fa-solid fa-recycle"></i>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="space-y-4 mt-4 max-h-[calc(100vh-20rem)] overflow-y-auto">
                    @if (count($cart) > 0)
                        @foreach ($cart as $index => $item)
                            <div class="bg-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-grow">
                                        <span class="block font-semibold text-lg">{{ $item['name'] }}</span>
                                    </div>
                                    <button wire:click="removeFromCart({{ $index }})" 
                                        class="text-red-500 ml-2">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Price Display -->
                                <div class="flex flex-col items-end mb-2">
                                    <span class="text-lg font-bold">
                                        ฿{{ number_format($item['price'] * $item['quantity'] * (1 - $item['discount'] / 100), 2) }}
                                    </span>
                                    @if ($item['discount'] > 0)
                                        <span class="text-sm text-gray-400 line-through">
                                            ฿{{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Quantity & Discount -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="quantity-{{ $index }}" class="text-sm block">จำนวน</label>
                                        <input type="number" 
                                            id="quantity-{{ $index }}"
                                            wire:model.lazy="cart.{{ $index }}.quantity"
                                            wire:change="updateCart" 
                                            min="1"
                                            class="border rounded-md px-2 py-1 w-full">
                                    </div>
                                    <div>
                                        <label for="discount-{{ $index }}" class="text-sm block">ส่วนลด (%)</label>
                                        <input type="number" 
                                            id="discount-{{ $index }}"
                                            wire:model.lazy="cart.{{ $index }}.discount"
                                            wire:change="updateCart" 
                                            min="0"
                                            class="border rounded-md px-2 py-1 w-full">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-600 py-8">ไม่พบสินค้าในตะกร้า</p>
                    @endif
                </div>

                <!-- Summary -->
                <div class="space-y-2 border-t-2 border-stone-300 mt-4 pt-4">
                    <div class="flex justify-between">
                        <span class="font-semibold">ยอดรวม:</span>
                        <span>฿{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">ภาษี:</span>
                        <span>฿{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>ยอดชำระ:</span>
                        <span>฿{{ number_format($payableAmount, 2) }}</span>
                    </div>
                </div>

                <!-- Checkout Button -->
                <button class="w-full btn-primary mt-4" wire:click="checkout">
                    <i class="fa-solid fa-cart-shopping me-2"></i>
                    ชำระเงิน
                </button>
                @if (session('error'))
                    <div class="alert-danger mt-2">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>