<!-- resources/views/livewire/order-history.blade.php -->
<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <!-- Header and Filters -->
    <div class="content-header space-y-4">
        <h2 class="text-2xl font-bold">ประวัติการสั่งซื้อ</h2>

        <div class="flex flex-wrap gap-4 text-gray-600">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live="searchTerm" placeholder="ค้นหาจากรหัสออเดอร์หรือชื่อสินค้า"
                    class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Date Range Filter -->
            <select wire:model.live="dateRange" class="rounded-lg border-gray-300 shadow-sm">
                <option value="">ทั้งหมด</option>
                <option value="today">วันนี้</option>
                <option value="last7days">7 วันที่ผ่านมา</option>
                <option value="last30days">30 วันที่ผ่านมา</option>
                <option value="custom">กำหนดเอง</option>
            </select>

            <!-- Status Filter -->
            <select wire:model.live="status" class="rounded-lg border-gray-300 shadow-sm">
                <option value="">สถานะทั้งหมด</option>
                <option value="completed">สำเร็จ</option>
                <option value="pending">รอดำเนินการ</option>
                <option value="cancelled">ยกเลิก</option>
            </select>

            <!-- Items Per Page -->
            <select wire:model.live="perPage" class="rounded-lg border-gray-300 shadow-sm">
                <option value="10">10 รายการ</option>
                <option value="25">25 รายการ</option>
                <option value="50">50 รายการ</option>
            </select>
        </div>
    </div>

    <!-- Orders List -->
    <div class="content-body space-y-4">
        @if ($orders->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8">
                <div class="text-center">
                    <div class="text-gray-400 text-xl mb-2">
                        ไม่พบรายการประวัติการสั่งซื้อ
                    </div>
                    <p class="text-gray-500">
                        ยังไม่มีรายการสั่งซื้อในขณะนี้
                    </p>
                </div>
            </div>
        @else
            @forelse ($orders as $order)
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="font-bold text-lg">รหัสออเดอร์: {{ $order->id }}</div>
                            <div class="text-gray-500">วันที่: {{ $order->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="text-right space-y-2">
                            <div
                                class="font-bold text-lg  {{ $order->payment_status === 'paid'
                                    ? 'text-green-500'
                                    : ($order->payment_status === 'pending'
                                        ? 'text-yellow-500'
                                        : 'text-red-500') }}">
                                ฿{{ number_format($order->total_amount, 2) }}
                            </div>

                            <!-- สถานะการชำระเงิน -->
                            <div class="text-sm">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $order->payment_status === 'paid' ? 'ชำระแล้ว' : ($order->payment_status === 'pending' ? 'รอชำระเงิน' : 'ยกเลิก') }}
                                </span>
                            </div>

                            <!-- สถานะการจัดส่ง (ถ้ามี) -->
                            @if ($order->delivery_address)
                                <div class="text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5
                                    {{ $order->delivery_status === 'delivered'
                                        ? 'bg-green-100 text-green-800'
                                        : ($order->delivery_status === 'shipping'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $order->delivery_status === 'delivered'
                                            ? 'จัดส่งแล้ว'
                                            : ($order->delivery_status === 'shipping'
                                                ? 'กำลังจัดส่ง'
                                                : 'รอจัดส่ง') }}
                                    </span>
                                </div>
                            @endif

                            <!-- ปุ่มจัดการ -->
                            @if ($order->status !== 'cancelled')
                                <div class="flex gap-2 justify-end">
                                    <button wire:click="openStatusModal('{{ $order->id }}')"
                                        class="px-3 py-1 text-sm rounded-md bg-blue-500 text-white hover:bg-blue-600">
                                        จัดการสถานะ
                                    </button>
                                    <a href="{{ route('receipt.print', ['order' => $order->id]) }}" 
                                       target="_blank"
                                       class="px-3 py-1 text-sm rounded-md bg-green-500 text-white hover:bg-green-600">
                                        พิมพ์ใบเสร็จ
                                    </a>
                                    <button wire:click="updateOrderStatus('{{ $order->id }}', 'cancelled')"
                                        class="px-3 py-1 text-sm rounded-md bg-red-500 text-white hover:bg-red-600"
                                        onclick="return confirm('ยืนยันการยกเลิกออเดอร์?')">
                                        ยกเลิก
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">สินค้า</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">ราคา/ชิ้น</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">จำนวน</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">ส่วนลด</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">รวม</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-gray-500">
                                @foreach ($order->orderDetails as $detail)
                                    <tr>
                                        <td class="px-4 py-3">{{ $detail->product->name }}</td>
                                        <td class="px-4 py-3 text-right">฿{{ number_format($detail->price, 2) }}</td>
                                        <td class="px-4 py-3 text-right">{{ $detail->quantity }}</td>
                                        <td class="px-4 py-3 text-right">{{ $detail->discount }}%</td>
                                        <td class="px-4 py-3 text-right">฿{{ number_format($detail->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4"
                                        class="px-4 py-3 text-right font-medium
                                    {{ $order->payment_status === 'paid'
                                        ? 'text-green-500'
                                        : ($order->payment_status === 'pending'
                                            ? 'text-yellow-500'
                                            : 'text-red-500') }}">
                                        รวมทั้งสิ้น:
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-bold  {{ $order->payment_status === 'paid'
                                            ? 'text-green-500'
                                            : ($order->payment_status === 'pending'
                                                ? 'text-yellow-500'
                                                : 'text-red-500') }}">
                                        ฿{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-500">
                                        ภาษี (7%):
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-500">
                                        ฿{{ number_format($order->tax_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Info -->
                    <div class="mt-4 text-sm text-gray-500">
                        <div class="flex justify-between">
                            <div>
                                <span class="font-medium">วิธีชำระเงิน:</span>
                                {{ $order->payment_method === 'cash' ? 'เงินสด' : 'บัตรเครดิต' }}
                            </div>

                            <div>
                                <span class="font-medium">สถานะการชำระเงิน:</span>
                                {{ $order->payment_status === 'paid' ? 'ชำระแล้ว' : 'รอชำระ' }}
                            </div>
                        </div>
                    </div>
                    @if ($order->delivery_address)
                        <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                            <div class="font-medium mb-1 text-gray-700">ที่อยู่จัดส่ง:</div>
                            <div class="text-gray-600">{{ $order->delivery_address }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    ไม่พบรายการสั่งซื้อ
                </div>
            @endforelse
        @endif
    </div>

    <!-- Pagination -->
    @if ($orders->isNotEmpty())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

    {{-- Status Modal --}}
    <div x-show="$wire.showStatusModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none !important;">
        <div class="bg-white p-6 rounded-lg w-96 text-gray-700">
            <h3 class="text-lg font-bold mb-4">อัพเดทสถานะ</h3>

            <div class="space-y-3">
                <!-- สถานะการชำระเงิน -->
                <div class="space-y-2">
                    <div class="font-medium text-gray-700">สถานะการชำระเงิน</div>
                    <button wire:click="updateOrderStatus('{{ $orderIdToUpdate }}', 'pending')"
                        class="w-full px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        รอชำระเงิน
                    </button>
                    <button wire:click="updateOrderStatus('{{ $orderIdToUpdate }}', 'completed')"
                        class="w-full px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        ชำระเงินแล้ว
                    </button>
                </div>

                <!-- สถานะการจัดส่ง -->
                @if ($selectedOrder && $selectedOrder->delivery_address)
                    <div class="pt-3 border-t">
                        <div class="font-medium text-gray-700 mb-2">สถานะการจัดส่ง</div>
                        <button wire:click="updateDeliveryStatus('{{ $orderIdToUpdate }}', 'pending')"
                            class="w-full px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 mb-2">
                            รอจัดส่ง
                        </button>
                        <button wire:click="updateDeliveryStatus('{{ $orderIdToUpdate }}', 'shipping')"
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            กำลังจัดส่ง
                        </button>
                        <button wire:click="updateDeliveryStatus('{{ $orderIdToUpdate }}', 'delivered')"
                            class="w-full px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 mt-2">
                            จัดส่งแล้ว
                        </button>
                    </div>
                @endif
            </div>

            <button wire:click="$set('showStatusModal', false)"
                class="w-full mt-4 px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                ปิด
            </button>
        </div>
    </div>
</div>
