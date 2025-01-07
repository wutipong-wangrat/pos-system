<?php

namespace App\Livewire;

use App\Models\CashTransactionModel;
use App\Models\OrderDetailModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class Checkout extends Component
{
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $payableAmount = 0;
    public $paymentMethod = null;
    public $cashReceived = 0;
    public $change = 0;

    public $cashInDrawer = 0;
    public $showCashDrawerSummary = false;

    public $isDelivery = false;
    public $deliveryAddress = '';

    // stripe
    public $stripePaymentIntent = null;
    public $stripeClientSecret = null;

    protected $validationAttributes = [
        'deliveryAddress' => 'ที่อยู่จัดส่ง',
    ];

    protected $rules = [
        'paymentMethod' => 'required|in:cash,card',
        'cashReceived' => 'required_if:paymentMethod,cash|numeric|min:0',
        'deliveryAddress' => 'required_if:isDelivery,true|string|max:255',
    ];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->subtotal = session()->get('subtotal', 0);
        $this->tax = session()->get('tax', 0);
        $this->payableAmount = session()->get('payableAmount', 0);
        $this->cashInDrawer = CashTransactionModel::latest()->value('balance') ?? 0;

        if ($this->paymentMethod === 'card') {
            $this->createStripePaymentIntent();
        }
    }

    public function updatedIsDelivery()
    {
        if (!$this->isDelivery) {
            $this->deliveryAddress = '';
        }
    }

    public function updatedPaymentMethod()
    {
        if ($this->paymentMethod === 'card') {
            $this->createStripePaymentIntent();
        } else {
            $this->stripePaymentIntent = null;
            $this->stripeClientSecret = null;
        }
    }

    public function updatedCashReceived()
    {
        if (is_numeric($this->cashReceived)) {
            $this->change = max(0, $this->cashReceived - $this->payableAmount);
            $this->showCashDrawerSummary = $this->change > 0;
        }
    }

    protected function createStripePaymentIntent()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($this->payableAmount * 100), //แปลงเป็นเซ็นต์
                'currency' => 'thb',
                'metadata' => [
                    'subtotal' => $this->subtotal,
                    'tax' => $this->tax,
                ],
            ]);

            $this->stripePaymentIntent = $paymentIntent->id;
            $this->stripeClientSecret = $paymentIntent->client_secret;
        
            $this->dispatch('stripeClientSecretUpdated', $this->stripeClientSecret);
        } catch (\Exception $e) {
            session()->flash('error' . $e->getMessage());
        }
    }

    public function processCheckout()
    {
        $this->validate();

        if ($this->paymentMethod === 'cash') {
            if ($this->cashReceived < $this->payableAmount) {
                session()->flash('error', 'จำนวนเงินไม่เพียงพอ');
                return;
            }

            if ($this->change > $this->cashInDrawer) {
                session()->flash('error', 'เงินทอนไม่เพียงพอ');
                return;
            }
        }

        if ($this->paymentMethod === 'card') {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($this->stripePaymentIntent);

                if ($paymentIntent->status !== 'succeeded') {
                    session()->flash('error', 'การชําระเงินไม่สําเร็จ');
                    return;
                }
            } catch (\Exception $e) {
                session()->flash('error', 'เกิดข้อผิดพลาดในการชําระเงิน: ' . $e->getMessage());
                return;
            }
        }

        try {
            DB::beginTransaction();

            $order = OrderModel::create([
                'user_id' => session()->get('user_id'),
                'total_amount' => $this->payableAmount,
                'tax_amount' => $this->tax,
                'subtotal' => $this->subtotal,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $this->paymentMethod,
                'cash_received' => $this->paymentMethod === 'cash' ? $this->cashReceived : null,
                'change_amount' => $this->paymentMethod === 'cash' ? $this->change : null,
                'delivery_address' => $this->isDelivery ? $this->deliveryAddress : null,
                'delivery_status' => $this->isDelivery ? 'pending' : null,
            ]);

            foreach ($this->cart as $item) {
                OrderDetailModel::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100),
                ]);
            }

            // บันทึกธุรกรรมเงินสดเฉพาะกรณีชำระเงินทันทีด้วยเงินสด
            if ($this->paymentMethod === 'cash') {
                CashTransactionModel::create([
                    'type' => 'cash_in',
                    'amount' => $this->cashReceived,
                    'previous_balance' => $this->cashInDrawer,
                    'balance' => $this->cashInDrawer + $this->cashReceived,
                    'description' => 'Cash Received for Order ID: ' . $order->id,
                    'order_id' => $order->id,
                ]);

                if ($this->change > 0) {
                    CashTransactionModel::create([
                        'type' => 'cash_out',
                        'amount' => $this->change,
                        'previous_balance' => $this->cashInDrawer + $this->cashReceived,
                        'balance' => $this->cashInDrawer + $this->cashReceived - $this->change,
                        'description' => 'Change for Order ID: ' . $order->id,
                        'order_id' => $order->id,
                    ]);
                }
            }
            
            foreach ($this->cart as $item) {
                $product = ProductModel::find($item['id']);
                if (!$product) {
                    throw new \Exception('ไม่พบสินค้า ID: ' . $item['id']);
                }
    
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception('สินค้า ' . $product->name . ' มีไม่เพียงพอ');
                }
    
                $product->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            session()->flash('success', 'ชำระเงินเรียบร้อย');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }

    public function goBack()
    {
        return redirect()->route('order');
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
