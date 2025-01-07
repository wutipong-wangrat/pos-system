@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <div class="content-header">
        <div class="text-2xl font-bold">จัดการสินค้า</div>
        <button class="btn-primary" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มสินค้า
        </button>
    </div>
    <div class="content-body">
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if ($products->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8">
                <div class="text-center">
                    <div class="text-gray-400 text-xl mb-2">
                        ไม่พบรายการสินค้า
                    </div>
                    <p class="text-gray-500">
                        ยังไม่มีรายการสินค้าในระบบ
                    </p>
                </div>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center w-auto">รูปภาพ</th>
                        <th class="text-center w-auto">
                            <button wire:click="sortBy('name')">
                                ชื่อสินค้า
                                @if ($sortField === 'name')
                                    @if ($sortDirection === 'asc')
                                        <i class="fa-solid arrow-up"></i>
                                    @else
                                        <i class="fa-solid arrow-down"></i>
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th class="text-center w-auto">รายละเอียด</th>
                        <th class="text-center w-auto">ประเภท</th>
                        <th class="text-center w-auto">ราคา</th>
                        <th class="text-center w-auto">จำนวน</th>
                        <th class="text-center w-auto">สถานะ</th>
                        <th class="text-center w-auto">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><img src="{{ Storage::url($product->img) }}" alt="product img"
                                    class="w-14 h-12 rounded-md"></td>
                            <td class="text-center">{{ $product->name }}</td>
                            <td class="text-left">{{ $product->description }}</td>
                            <td class="text-center">{{ $product->category->name }}</td>
                            <td class="text-right">{{ number_format($product->price, 2) }}</td>
                            <td class="text-right">{{ number_format($product->quantity) }}</td>
                            <td class="text-center">
                                <span
                                    class="{{ $product->getStatus() == 'มีสินค้า'
                                        ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm'
                                        : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm' }}">{{ $product->getStatus() }}</span>
                            </td>
                            <td class="h-full align-middle text-center">
                                <button wire:click="openModalEdit({{ $product->id }})" class="btn-edit me-2"><i
                                        class="fa-solid fa-pencil"></i>
                                </button>
                                <button wire:click="openModalDelete({{ $product->id }}, '{{ $product->name }}')"
                                    class="btn-delete"><i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($products->isNotEmpty())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif

    <x-modal wire:model="showModal" title="สินค้า" maxWidth="2xl">
        <div class="mb-4">
            <div>ชื่อสินค้า</div>
            <input type="text" class="form-control" wire:model="name" />
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <div>รายละเอียด</div>
            <input type="text" class="form-control" wire:model="description" />
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4 flex gap-3">
            <div class="w-1/2">
                <div>ราคา</div>
                <input type="number" class="form-control" wire:model="price" />
                @error('price')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="w-1/2">
                <div>จํานวน</div>
                <input type="number" class="form-control" wire:model="quantity" />
                @error('quantity')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-4 flex gap-3">
            <div class="w-1/2">
                <div>ประเภท</div>
                <select class="form-control" wire:model="categoryId">
                    <option value="">เลือกประเภท</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <div>เลือกรูปภาพ</div>
            <input type="file" class="form-control" wire:model="img" accept="image/*" />
            <div wire:loading wire:target="img" class="mt-2">
                กำลังอัปโหลด...
            </div>
            @if ($img)
                <div class="mt-2 flex justify-center">
                    <img src="{{ $img->temporaryUrl() }}" alt="Preview" class="rounded-md w-40">
                </div>
            @endif
            @error('img')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-center pb-3">
            <button class="btn-success" wire:click="createProduct">
                <i class="fa-solid fa-check me-2"></i>
                ตกลง
            </button>
            <button class="btn-secondary" wire:click="closeModal">
                <i class="fa-solid fa-xmark me-2"></i>
                ยกเลิก
            </button>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif
    </x-modal>

    <x-modal wire:model="showModalEdit" title="สินค้า" maxWidth="2xl">
        <div class="mb-4">
            <div>ชื่อสินค้า</div>
            <input type="text" class="form-control w-full" wire:model="name" required />
        </div>

        <div class="mb-4">
            <div>รายละเอียด</div>
            <input type="text" class="form-control w-full" wire:model="description" />
        </div>

        <div class="mb-4 flex gap-3">
            <div class="w-1/2">
                <div>ราคา</div>
                <input type="number" class="form-control w-full" wire:model="price" required />
            </div>
            <div class="w-1/2">
                <div>จํานวน</div>
                <input type="number" class="form-control w-full" wire:model="quantity" required />
            </div>
        </div>

        <div class="mb-4 flex gap-3">
            <div class="w-1/2">
                <div>ประเภท</div>
                <select class="form-control" wire:model="categoryId" required>
                    <option value="">เลือกประเภท</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <div>เลือกรูปภาพ</div>
            <input type="file" wire:model="img" class="form-control" accept="image/*"
                onchange="this.dispatchEvent(new Event('input'))" />
        </div>

        <div class="text-center pb-3">
            <button class="btn-success" wire:click="updateProduct">
                <i class="fa-solid fa-check me-2"></i>
                ตกลง
            </button>
            <button class="btn-secondary" wire:click="closeModalEdit">
                <i class="fa-solid fa-xmark me-2"></i>
                ยกเลิก
            </button>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif
    </x-modal>

    <x-modal-confirm title="ลบสินค้า" text="คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า {{ $nameForDelete }}?"
        clickConfirm="delete" clickCancel="showModalDelete = false"
        showModalDelete="showModalDelete"></x-modal-confirm>
</div>
