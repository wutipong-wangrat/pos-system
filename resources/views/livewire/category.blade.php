<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <div class="content-header">
        <div class="text-2xl font-bold">หมวดหมู่สินค้า</div>
        <button class="btn-primary" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มหมวดหมู่
        </button>
    </div>

    <div class="content-body">
        <table class="table">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($categories->isEmpty())
                <div class="bg-gray-50 rounded-lg p-8">
                    <div class="text-center">
                        <div class="text-gray-400 text-xl mb-2">
                            ไม่พบรายการหมวดหมู่
                        </div>
                        <p class="text-gray-500">
                            ยังไม่มีรายการหมวดหมู่ในระบบ
                        </p>
                    </div>
                </div>
            @else
                <thead>
                    <tr>
                        <th class="text-center w-1/12">
                            <button wire:click="sortBy('id')" class="text-center">
                                ลำดับ
                                @if ($sortField === 'id')
                                    @if ($sortDirection === 'asc')
                                        <i class="fa-solid arrow-up"></i>
                                    @else
                                        <i class="fa-solid arrow-down"></i>
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th class="text-center w-3/12">
                            <button wire:click="sortBy('name')">
                                ชื่อหมวดหมู่
                                @if ($sortField === 'name')
                                    @if ($sortDirection === 'asc')
                                        <i class="fa-solid arrow-up"></i>
                                    @else
                                        <i class="fa-solid arrow-down"></i>
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th class="text-center w-4/12">รายละเอียด</th>
                        <th class="text-center w-2/12">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="text-center">{{ $category->id }}</td>
                            <td class="text-left" width="30%">{{ $category->name }}</td>
                            <td class="text-left">{{ $category->description }}</td>
                            <td class="flex justify-center">
                                <button wire:click="openModalEdit({{ $category->id }})" class="btn-edit me-2"><i
                                        class="fa-solid fa-pencil"></i></button>
                                <button wire:click="openModalDelete({{ $category->id }})" class="btn-delete"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
        </table>
        @endif
    </div>

    @if ($categories->isNotEmpty())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif

    <x-modal wire:model="showModal" maxWidth="2xl" title="เพิ่มหมวดหมู่สินค้า">
        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif

        <div class="mt-3">
            <div>ชื่อหมวดหมู่</div>
            <input type="text" class="form-control" wire:model="name">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-3">
            <div>รายละเอียด</div>
            <input class="form-control w-full" type="text" wire:model="description" />
        </div>

        <div class="mt-5 text-center pb-5">
            <button class="btn-success" wire:click="createCategory">
                <i class="fa-solid fa-check me-2"></i>
                เพิ่ม
            </button>
            <button class="btn-secondary" wire:click="closeModal">
                <i class="fa-solid fa-xmark me-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>

    <x-modal wire:model="showModalEdit" maxWidth="2xl" title="แก้ไขหมวดหมู่สินค้า">
        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif

        <div class="mt-3">
            <div>ชื่อหมวดหมู่</div>
            <input type="text" class="form-control" wire:model="name">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-3">
            <div>รายละเอียด</div>
            <input type="text" class="form-control" wire:model="description" />
        </div>

        <div class="mt-5 text-center pb-5">
            <button class="btn-success" wire:click="updateCategory">
                <i class="fa-solid fa-check me-2"></i>
                แก้ไข
            </button>
            <button class="btn-secondary" wire:click="showModalEdit = false">
                <i class="fa-solid fa-xmark me-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>

    <x-modal-confirm showModalDelete="showModalDelete" title="ลบหมวดหมู่"
        text="คุณแน่ใจหรือไม่ว่าต้องการลบหมวดหมู่ {{ $nameForDelete }}" clickConfirm="deleteCategory"
        clickCancel="showModalDelete = false" />
</div>
