<?php

namespace App\Livewire;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Product extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $showModal = false;
    public $showModalEdit = false;
    public $showModalDelete = false;

    // public $products = [];
    // public $categories = [];
    public $id;
    public $nameForDelete = '';

    public $name;
    public $description;
    public $price;
    public $quantity;
    public $categoryId;
    public $img;

    public $sortField = 'id';
    public $sortDirection = 'asc';
    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'categoryId' => 'required|exists:categories,id',
        'img' => 'required|image|max:1024',
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
        $this->clearSession();
        // $this->fetchData();
    }

    public function closeModal()
    {
        $this->showModal = false;
        // $this->fetchData();
        $this->clearSession();
    }

    public function clearSession()
    {
        session()->forget('success');
        session()->forget('error');
    }

    public function createProduct()
    {
        // เพิ่ม validation
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'categoryId' => 'required|exists:categories,id',
            'img' => 'required|image|max:1024', // validate รูปภาพ
        ]);

        $imageUrl = null;

        try {
            if ($this->img) {
                $imageUrl = $this->img->store('products', 'public');
            } else {
                $this->addError('img', 'กรุณาเลือกรูปภาพ.');
            }

            ProductModel::create([
                "name" => $this->name,
                "description" => $this->description,
                "price" => $this->price,
                "quantity" => $this->quantity,
                "category_id" => $this->categoryId,
                "img" => $imageUrl,
                "status" => $this->quantity > 0 ? "available" : "out_of_stock",
            ]);

            $this->closeModal();
            session()->flash('success', 'เพิ่มรายการ "' . $this->name . '" เรียบร้อย');
        } catch (Exception $e) {
            throw $e;
            session()->flash('error', $e->getMessage());
        }
    }

    public function openModalEdit($id)
    {
        $product = ProductModel::find($id);
        $this->showModalEdit = true;

        $this->id = $id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->quantity = $product->quantity;
        $this->categoryId = $product->category_id;
        $this->imgUrl = $product->img ? Storage::disk('public')->url($product->img) : '';
        // $this->fetchData();
    }

    public function closeModalEdit()
    {
        $this->showModalEdit = false;
        $this->clearSession();
    }

    public function updateProduct()
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'categoryId' => 'required|exists:categories,id',
        ]);

        try {
            $product = ProductModel::find($this->id);
            $product->name = $this->name;
            $product->description = $this->description;
            $product->price = $this->price;
            $product->quantity = $this->quantity;
            $product->category_id = $this->categoryId;

            if ($this->img) {
                $storage = Storage::disk('public');
                if ($product->img && $storage->exists($product->img)) {
                    $storage->delete($product->img);
                }
                $path = $this->img->store('products', 'public');
                $product->img = $path;
            }

            // ProductModel::where('id', $this->id)->update([

            // ])

            $product->status = $this->quantity > 0 ? 'available' : 'out_of_stock';
            $product->save();
            session()->flash('success', 'อัพเดทรายการ "' . $this->name . '" สําเร็จ');

            // $this->fetchData();
            $this->closeModalEdit();
        } catch (\Exception $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    public function openModalDelete($id, $name)
{
        $this->showModalDelete = true;
        $this->id = $id;
        $this->nameForDelete = $name;
        // $this->fetchData();
    }

    public function delete()
{
        try {
            $product = ProductModel::find($this->id);

            if ($product->img) {
                $storage = Storage::disk('public');
                if ($storage->exists($product->img)) {
                    $storage->delete($product->img);
                }
            }

            $product->delete();

            // $this->fetchData();
            $this->showModalDelete = false;
            session()->flash('success', 'ลบรายการ "' . $this->nameForDelete . '" เรียบร้อย');
        } catch (\Exception $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
        }

    }

    public function resetForm()
{
        $this->name = '';
        $this->description = '';
        $this->price = 0;
        $this->quantity = 0;
        $this->categoryId = null;
        $this->img = null;
        $this->imgUrl = null;
    }

    // public function mount(){
    //     $this->fetchData();
    // }

    // public function fetchData(){
    //     $this->categories = CategoryModel::all();
    //     $this->products = ProductModel::all();

    //     foreach($this->products as $product){
    //         $product->imgUrl = $product->img ? Storage::disk('public')->url($product->img) : null;
    //     }
    // }

    public function render()
{
        $products = ProductModel::orderBy($this->sortField, $this->sortDirection)->paginate(5);
        $categories = CategoryModel::all();
        // dd($products);
        return view('livewire.product', compact('products', 'categories'));
    }
}
