<?php

namespace App\Livewire;

use App\Models\OrderModel;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $dateRange = '';
    public $status = '';
    public $perPage = 10;

    public $orderIdToUpdate;
    public $showStatusModal = false;
    public $selectedOrder = null;

    protected $listeners = [
        'refreshHistory' => '$refresh',
    ];

    public function openStatusModal($orderId){
        $this->orderIdToUpdate = $orderId;
        $this->selectedOrder = OrderModel::find($orderId);
        $this->showStatusModal = true;
    }

    public function updateOrderStatus($orderId, $newStatus)
    {
        try {
            $order = OrderModel::findOrFail($orderId);

            switch($newStatus) {
                case 'cancelled':
                    $order->status = 'cancelled';
                    $order->payment_status = 'cancelled';
                    break;
                case 'completed':
                    $order->status = 'completed';
                    $order->payment_status = 'paid';
                    break;
                case 'pending':
                    $order->status = 'pending';
                    $order->payment_status = 'pending';
                    break;
            }

            $order->save();
            session()->flash('success', 'สถานะออเดอร์ถูกปรับปรุงเรียบร้อยแล้ว');
            $this->showStatusModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateDeliveryStatus($orderId, $newStatus){
        try {
            $order = OrderModel::findOrFail($orderId);

            if (!$order->delivery_address){
                session()->flash('error', 'กรุณาเพิ่มที่อยู่จัดส่งก่อน');
                return;
            }

            switch($newStatus) {
                case 'pending':
                    $order->delivery_status = 'pending';
                    break;
                case 'shipping':
                    $order->delivery_status = 'shipping';
                    break;
                case 'delivered':
                    $order->delivery_status = 'delivered';
                    break;
            }

            $order->save();
            session()->flash('success', 'สถานะการจัดส่งถูกปรับปรุงเรียบร้อยแล้ว');
            $this->showStatusModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = OrderModel::with(['orderDetails', 'orderDetails.product']) // แก้ตรงนี้ ให้ eager load ทั้ง orderDetails และ product
            ->when($this->searchTerm, function ($query) {
                $query->where('id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('orderDetails.product', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.history', compact('orders'));
    }
}
