@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen p-4">
    <div class="content-header flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="text-xl sm:text-2xl font-bold">จัดการสินค้า</div>
        <button class="btn-primary w-full sm:w-auto" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มสินค้า
        </button>
    </div>

    <div class="content-body">
        @if (session('success'))
            <div class="alert-success mx-2">{{ session('success') }}</div>
        @endif
        @if (session('update'))
            <div class="alert-success mx-2">{{ session('update') }}</div>
        @endif
        @if (session('delete'))
            <div class="alert-success mx-2">{{ session('delete') }}</div>
        @endif

        @if ($products->isEmpty())
            <div class="bg-gray-50 rounded-lg p-4 sm:p-8">
                <div class="text-center">
                    <div class="text-gray-400 text-lg sm:text-xl mb-2">
                        ไม่พบรายการสินค้า
                    </div>
                    <p class="text-gray-500">
                        ยังไม่มีรายการสินค้าในระบบ
                    </p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <div class="hidden sm:block"> <!-- Desktop Table View -->
                    <table class="table min-w-full">
                        <thead>
                            <tr>
                                <th class="text-center w-20">รูปภาพ</th>
                                <th class="text-center w-48">
                                    <button wire:click="sortBy('name')"
                                        class="text-center">
                                        <span>ชื่อสินค้า</span>
                                        @if ($sortField === 'name')
                                            <i
                                                class="fa-solid {{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} text-center"></i>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-center w-64">รายละเอียด</th>
                                <th class="text-center w-32">ประเภท</th>
                                <th class="text-center w-24">ราคา</th>
                                <th class="text-center w-24">จำนวน</th>
                                <th class="text-center w-24">สถานะ</th>
                                <th class="text-center w-24">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ Storage::url($product->img) }}" alt="product img"
                                            class="w-14 h-12 rounded-md mx-auto object-cover">
                                    </td>
                                    <td class="text-left">
                                        <div class="truncate max-w-[12rem] px-2" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <div class="truncate max-w-[16rem] px-2" title="{{ $product->description }}">
                                            {{ $product->description }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $product->category->name }}</td>
                                    <td class="text-right">{{ number_format($product->price, 2) }}</td>
                                    <td class="text-right">{{ number_format($product->quantity) }}</td>
                                    <td class="text-center">
                                        <span
                                            class="{{ $product->getStatus() == 'มีสินค้า' ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm' : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm' }}">
                                            {{ $product->getStatus() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button wire:click="openModalEdit({{ $product->id }})" class="btn-edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                            <button
                                                wire:click="openModalDelete({{ $product->id }}, '{{ $product->name }}')"
                                                class="btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="grid grid-cols-1 gap-4 sm:hidden">
                    @foreach ($products as $product)
                        <div class="bg-slate-500 rounded-lg shadow p-4">
                            <div class="flex items-center space-x-4">
                                <img src="{{ Storage::url($product->img) }}" alt="product img"
                                    class="w-20 h-20 rounded-md object-cover">
                                <div class="flex-1 min-w-0"> <!-- Added min-w-0 for proper truncation -->
                                    <h3 class="font-bold truncate" title="{{ $product->name }}">{{ $product->name }}
                                    </h3>
                                    <p class="text-sm text-white truncate" title="{{ $product->description }}">
                                        {{ $product->description }}</p>
                                    <p class="text-sm text-white">{{ $product->category->name }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-semibold">฿{{ number_format($product->price, 2) }}</span>
                                        <span
                                            class="{{ $product->getStatus() == 'มีสินค้า' ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm' : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm' }}">
                                            {{ $product->getStatus() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button wire:click="openModalEdit({{ $product->id }})" class="btn-edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button wire:click="openModalDelete({{ $product->id }}, '{{ $product->name }}')"
                                    class="btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if ($products->isNotEmpty())
        <div class="mt-4 px-4">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Modals -->
    <x-modal wire:model="showModal" title="สินค้า" maxWidth="2xl">
        <div class="p-4">
            <div class="mb-4">
                <div>ชื่อสินค้า</div>
                <input type="text" class="form-control w-full" wire:model="name" />
                @error('name')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <div>รายละเอียด</div>
                <input type="text" class="form-control w-full" wire:model="description" />
                @error('description')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <div>ราคา</div>
                    <input type="number" class="form-control w-full" wire:model="price" />
                    @error('price')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <div>จํานวน</div>
                    <input type="number" class="form-control w-full" wire:model="quantity" />
                    @error('quantity')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div>ประเภท</div>
                <select class="form-control w-full" wire:model="categoryId">
                    <option value="">เลือกประเภท</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <div>เลือกรูปภาพ</div>
                <input type="file" class="form-control w-full" wire:model="img" accept="image/*" />
                <div wire:loading wire:target="img" class="mt-2 text-sm text-gray-500">
                    กำลังอัปโหลด...
                </div>
                @if ($img)
                    <div class="mt-2 flex justify-center">
                        <img src="{{ $img->temporaryUrl() }}" alt="Preview" class="rounded-md w-40">
                    </div>
                @endif
                @error('img')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-2">
                <button class="btn-success w-full sm:w-auto" wire:click="createProduct">
                    <i class="fa-solid fa-check me-2"></i>
                    ตกลง
                </button>
                <button class="btn-secondary w-full sm:w-auto" wire:click="closeModal">
                    <i class="fa-solid fa-xmark me-2"></i>
                    ยกเลิก
                </button>
            </div>

            @if ($errors->any())
                <div class="alert-danger mt-4">
                    @foreach ($errors->all() as $error)
                        <ul>
                            <li>{{ $error }}</li>
                        </ul>
                    @endforeach
                </div>
            @endif
        </div>
    </x-modal>

    <!-- Edit Modal and Delete Confirmation Modal remain largely the same but with added responsive classes -->
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
