<?php

namespace App\Livewire;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Livewire\Component;

class Order extends Component
{
    public $searchTerm = ''; // คำค้นหา
    public $selectedCategory = null; // หมวดหมู่ที่เลือก
    public $products = []; // สินค้า
    public $categories = []; // หมวดหมู่
    public $cart = []; // สินค้าในตะกร้า
    public $subtotal = 0; // ยอดรวม
    public $tax = 0; // ภาษี
    public $payableAmount = 0; // ยอดรวมสุทธิ
    public $taxRate = 0.07; // อัตราภาษี

    /**
     * อัปเดตสินค้าตามเงื่อนไขการค้นหาและหมวดหมู่
     */
    public function updateProducts()
    {
        $query = ProductModel::query();

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $query->where('status', 'available');
        $this->products = $query->get();
    }

    /**
     * อัปเดตข้อมูลในตะกร้า เช่น Subtotal, Tax และ Payable Amount
     */
    public function updateCart()
    {
        $this->subtotal = 0;

        foreach ($this->cart as $index => $item) {
            // Validate and set default quantity
            if (empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] < 0) {
                $this->cart[$index]['quantity'] = 1; // Set default quantity to 1
            }

            // Validate and set default discount
            if (empty($item['discount']) || !is_numeric($item['discount']) || $item['discount'] < 0) {
                $this->cart[$index]['discount'] = 0; // Set default discount to 0
            }

            // Calculate with validated values
            $discountedPrice = $item['price'] * (1 - $this->cart[$index]['discount'] / 100);
            $this->subtotal += $discountedPrice * $this->cart[$index]['quantity'];
        }

        // $this->tax = $this->subtotal * $this->taxRate;
        $this->payableAmount = $this->subtotal + $this->tax;
    }

    /**
     * เปลี่ยนหมวดหมู่
     */
    public function changeCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->updateProducts();
    }

    /**
     * เพิ่มสินค้าไปยังตะกร้า
     */
    public function addToCart($productId)
    {
        $product = ProductModel::find($productId);

        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        // ตรวจสอบว่ามีสินค้าในตะกร้าอยู่แล้วหรือไม่
        foreach ($this->cart as $index => $item) {
            if ($item['id'] === $product->id) {
                $this->cart[$index]['quantity']++;
                $this->updateCart();
                return;
            }
        }

        // เพิ่มสินค้าใหม่ในตะกร้า
        $this->cart[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'discount' => 0, // ส่วนลดเริ่มต้นเป็น 0%
        ];

        $this->updateCart();
    }

    /**
     * ลบสินค้าออกจากตะกร้า
     */
    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // รีเซ็ตคีย์ใน array
        $this->updateCart();
    }

    /**
     * ล้างตะกร้าสินค้า
     */
    public function clearCart()
    {
        $this->cart = [];
        $this->updateCart();
    }

    /**
     * คำนวณยอดรวมในตะกร้า
     */
    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    /**
     * โหลดข้อมูลเริ่มต้น
     */
    public function mount()
    {
        $this->cart = [];
        $this->categories = CategoryModel::all(); // โหลดหมวดหมู่ทั้งหมด
        $this->updateProducts(); // โหลดสินค้าตามเงื่อนไขเริ่มต้น
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty');
            return;
        }

        session(['cart' => $this->cart]);
        session(['subtotal' => $this->subtotal]);
        session(['tax' => $this->tax]);
        session(['payableAmount' => $this->payableAmount]);

        return redirect()->route('order.checkout');
    }

    /**
     * Render View
     */
    public function render()
    {
        return view('livewire.order', [
            'categories' => $this->categories,
            'products' => $this->products,
        ]);
    }
}
