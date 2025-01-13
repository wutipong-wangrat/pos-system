<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen p-4">
    <div class="content-header flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="text-xl sm:text-2xl font-bold">หมวดหมู่สินค้า</div>
        <button class="btn-primary w-full sm:w-auto" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มหมวดหมู่
        </button>
    </div>

    <div class="content-body">
        @if (session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('update'))
            <div class="alert-success mb-4">
                {{ session('update') }}
            </div>
        @endif
        @if (session('delete'))
            <div class="alert-success mb-4">
                {{ session('delete') }}
            </div>
        @endif
        @if ($categories->isEmpty())
            <div class="bg-gray-50 rounded-lg p-4 sm:p-8">
                <div class="text-center">
                    <div class="text-gray-400 text-lg sm:text-xl mb-2">
                        ไม่พบรายการหมวดหมู่
                    </div>
                    <p class="text-gray-500">
                        ยังไม่มีรายการหมวดหมู่ในระบบ
                    </p>
                </div>
            </div>
        @else
            <!-- Desktop View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="table min-w-full">
                    <thead>
                        <tr>
                            <th class="w-16">
                                <button wire:click="sortBy('id')" class="flex items-center justify-center space-x-1">
                                    <span>ลำดับ</span>
                                    @if ($sortField === 'id')
                                        <i
                                            class="fa-solid {{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="w-48">
                                <button wire:click="sortBy('name')" class="flex items-center justify-center space-x-1">
                                    <span>ชื่อหมวดหมู่</span>
                                    @if ($sortField === 'name')
                                        <i
                                            class="fa-solid {{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="w-64">รายละเอียด</th>
                            <th class="w-24">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="text-center">{{ $category->id }}</td>
                                <td>
                                    <div class="truncate max-w-[12rem]" title="{{ $category->name }}">
                                        {{ $category->name }}
                                    </div>
                                </td>
                                <td>
                                    <div class="truncate max-w-[16rem]" title="{{ $category->description }}">
                                        {{ $category->description }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="openModalEdit({{ $category->id }})" class="btn-edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button wire:click="openModalDelete({{ $category->id }})" class="btn-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="grid grid-cols-1 gap-4 sm:hidden">
                @foreach ($categories as $category)
                    <div class="bg-slate-500 rounded-lg shadow p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold truncate max-w-[200px]" title="{{ $category->name }}">
                                    {{ $category->name }}
                                </div>
                                <div class="text-sm text-white">
                                    #{{ $category->id }}
                                </div>
                            </div>
                            <div class="text-sm text-white truncate" title="{{ $category->description }}">
                                {{ $category->description }}
                            </div>
                            <div class="flex justify-end space-x-2 mt-2">
                                <button wire:click="openModalEdit({{ $category->id }})" class="btn-edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button wire:click="openModalDelete({{ $category->id }})" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($categories->isNotEmpty())
        <div class="mt-4 px-4">
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
