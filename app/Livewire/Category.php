<?php

namespace App\Livewire;

use App\Models\CategoryModel;
use Livewire\Component;
use Livewire\WithPagination;

class Category extends Component
{
    use WithPagination;

    // public $categories = [];
    public $id;
    public $name;
    public $description = '';
    public $showModal = false;
    public $showModalEdit = false;
    public $showModalDelete = false;
    public $nameForDelete;

    // for pagination
    public $sortField = 'id';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required|min:3|max:255',
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

    // public function mount()
    // {
    //     $this->fetchData();
    // }

    public function openModal()
    {
        $this->showModal = true;
        $this->name = '';
        $this->description = '';
        $this->clearSession();
    }

    public function closeModal(){
        $this->showModal = false;
        $this->clearSession();
    }

    public function clearSession()
    {
        session()->forget('success');
        session()->forget('error');
    }

    public function openModalEdit($id)
    {
        $this->showModalEdit = true;
        $this->id = $id;

        $category = CategoryModel::find($id);
        $this->name = $category->name;
        $this->description = $category->description;
        $this->clearSession();
    }

    public function closeModalEdit()
    {
        $this->showModalEdit = false;
        $this->clearSession();
    }

    public function openModalDelete($id)
    {
        $this->showModalDelete = true;
        $this->id = $id;

        $category = CategoryModel::find($id);
        $this->nameForDelete = $category->name;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
        ]);
        try {
            //code...
            $category = CategoryModel::find($this->id);
            $category->name = $this->name;
            $category->description = $this->description;
            $category->save();

            $this->showModalEdit = false;
            session()->flash('update', 'อัพเดทรายการ "' . $this->name . '" เรียบร้อย');
        } catch (\Exception $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    public function deleteCategory()
    {
        try {
            //code...
            $category = CategoryModel::find($this->id);
            $category->delete();

            $this->showModalDelete = false;
            session()->flash('delete', 'ลบรายการ "' . $this->nameForDelete . '" เรียบร้อย');
        } catch (\Exception $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    // public function fetchData(){
    //     $this->categories = CategoryModel::orderBy('id', 'asc')->get();
    // }

    public function createCategory()
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
        ]);
        try {
            $category = new CategoryModel();
            if (empty($this->name)) {
                $this->addError('name', 'กรุณากรอกชื่อ');
                return;
            }
            $category->name = $this->name;
            $category->description = $this->description;
            $category->save();

            $this->showModal = false;
            session()->flash('success', 'เพิ่มรายการ"' . $this->name .'"เรียบร้อย');
        } catch (\Exception $th) {
            session()->flash('error', $th->getMessage());
        }

    }

    public function render()
    {
        $categories = CategoryModel::orderBy($this->sortField, $this->sortDirection)->paginate(5);
        return view('livewire.category', compact('categories'));
    }
}
