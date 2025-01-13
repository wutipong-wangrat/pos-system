<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen p-4">
    <div class="content-header flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="text-xl sm:text-2xl font-bold">จัดการผู้ใช้งาน</div>
        <button class="btn-primary w-full sm:w-auto" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มผู้ใช้
        </button>
    </div>

    <div class="content-body">
        @if (session('success'))
            <div class="alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if (session('update'))
            <div class="alert-success mb-4">{{ session('update') }}</div>
        @endif
        @if (session('delete'))
            <div class="alert-success mb-4">{{ session('delete') }}</div>
        @endif
        <!-- Desktop View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="table min-w-full">
                <thead>
                    <tr>
                        <th class="w-48">ชื่อ</th>
                        <th class="w-64">อีเมล</th>
                        <th class="w-32">ตำแหน่ง</th>
                        <th class="w-32">สถานะ</th>
                        <th class="w-24">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listUser as $user)
                        <tr>
                            <td>
                                <div class="truncate max-w-[12rem]" title="{{ $user->name }}">
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>
                                <div class="truncate max-w-[16rem]" title="{{ $user->email }}">
                                    {{ $user->email }}
                                </div>
                            </td>
                            <td class="text-center">{{ $user->role }}</td>
                            <td class="text-center">
                                <span class="{{ $user->status == 'active' ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm font-semibold' : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm font-semibold' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center space-x-2">
                                    <button class="btn-edit" wire:click="openModalEdit({{ $user->id }})">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="btn-delete" wire:click="openModalDelete({{ $user->id }}, '{{ $user->name }}')">
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
            @foreach ($listUser as $user)
                <div class="bg-slate-500 rounded-lg shadow p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <div class="font-semibold truncate max-w-[200px]" title="{{ $user->name }}">
                                {{ $user->name }}
                            </div>
                            <span class="{{ $user->status == 'active' ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm font-semibold' : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm font-semibold' }}">
                                {{ $user->status }}
                            </span>
                        </div>
                        <div class="text-sm text-white truncate" title="{{ $user->email }}">
                            {{ $user->email }}
                        </div>
                        <div class="text-sm text-white">
                            ตำแหน่ง: {{ $user->role }}
                        </div>
                        <div class="flex justify-end space-x-2 mt-2">
                            <button class="btn-edit" wire:click="openModalEdit({{ $user->id }})">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <button class="btn-delete" wire:click="openModalDelete({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Forms -->
    <x-modal title="ผู้ใช้งาน" wire:model="showModal" maxWidth="2xl">
        <div class="p-4">
            <div class="space-y-4">
                <div>
                    <label class="block mb-1">ชื่อผู้ใช้</label>
                    <input type="text" class="form-control w-full" wire:model="name">
                </div>

                <div>
                    <label class="block mb-1">อีเมล</label>
                    <input type="email" class="form-control w-full" wire:model="email">
                    @if ($mailError)
                        <span class="alert-danger text-sm">{{ $mailError }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">รหัสผ่าน</label>
                        <input type="password" class="form-control w-full {{ $errorLengthPassword ? 'ring-red-500/70 border-red-500/70' : '' }}"
                            wire:model="password" wire:blur="checkPasswordLength">
                        @if ($errorLengthPassword)
                            <span class="text-danger text-sm">{{ $errorLengthPassword }}</span>
                        @endif
                    </div>
                    <div>
                        <label class="block mb-1">ยืนยันรหัสผ่าน</label>
                        <input type="password" class="form-control w-full" wire:model="password_confirm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">สถานะ</label>
                        <select class="form-control w-full" wire:model="status">
                            @foreach ($listStatus as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">ตำแหน่ง</label>
                        <select class="form-control w-full" wire:model="role">
                            @foreach ($listRole as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-2 mt-6">
                    <button class="btn-success w-full sm:w-auto" wire:click="save">
                        <i class="fa-solid fa-check me-2"></i>
                        ตกลง
                    </button>
                    <button class="btn-secondary w-full sm:w-auto" wire:click="showModal = false">
                        <i class="fa-solid fa-xmark me-2"></i>
                        ยกเลิก
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <x-modal title="ผู้ใช้งาน" wire:model="showModalEdit" maxWidth="2xl">
        <div class="p-4">
            <div class="space-y-4">
                <div>
                    <label class="block mb-1">ชื่อผู้ใช้</label>
                    <input type="text" class="form-control w-full" wire:model="name">
                </div>

                <div>
                    <label class="block mb-1">อีเมล</label>
                    <input type="email" class="form-control w-full" wire:model="email">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">รหัสผ่าน</label>
                        <input type="password" class="form-control w-full {{ $errorLengthPassword ? 'ring-red-500/70 border-red-500/70' : '' }}"
                            wire:model="password" wire:blur="checkPasswordLength">
                        @if ($errorLengthPassword)
                            <span class="text-danger text-sm">{{ $errorLengthPassword }}</span>
                        @endif
                    </div>
                    <div>
                        <label class="block mb-1">ยืนยันรหัสผ่าน</label>
                        <input type="password" class="form-control w-full" wire:model="password_confirm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">สถานะ</label>
                        <select class="form-control w-full" wire:model="status">
                            @foreach ($listStatus as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">ตำแหน่ง</label>
                        <select class="form-control w-full" wire:model="role">
                            @foreach ($listRole as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-2 mt-6">
                    <button class="btn-success w-full sm:w-auto" wire:click="updateUser">
                        <i class="fa-solid fa-check me-2"></i>
                        ตกลง
                    </button>
                    <button class="btn-secondary w-full sm:w-auto" wire:click="showModalEdit = false">
                        <i class="fa-solid fa-xmark me-2"></i>
                        ยกเลิก
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <x-modal-confirm title="ลบผู้ใช้" text="คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้   
    {{ $nameForDelete }}"
        clickConfirm="delete" clickCancel="showModalDelete = false" showModalDelete="showModalDelete" />
</div>
