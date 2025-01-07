<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <div class="content-header">
        <div class="text-xl">จัดการผู้ใช้งาน</div>
        <button class="btn-primary" wire:click="openModal">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มผู้ใช้
        </button>
    </div>
    <div class="content-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ชื่อ</th>
                    <th>อีเมล</th>
                    <th class="w-1/6">ตำแหน่ง</th>
                    <th class="w-1/6">สถานะ</th>
                    <th width="200px">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listUser as $user)
                    <tr>
                        <td class="text-center">{{ $user->name }}</td>
                        <td class="text-left">{{ $user->email }}</td>
                        <td class="text-center">{{ $user->role }}</td>
                        <td class="text-center">
                            <span
                                class="{{ $user->status == 'active'
                                    ? 'px-2 rounded-md bg-green-500 text-slate-100 text-sm font-semibold'
                                    : 'px-2 rounded-md bg-red-500 text-slate-100 text-sm font-semibold' }}">{{ $user->status }}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn-edit mr-2" wire:click="openModalEdit({{ $user->id }})">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <button class="btn-delete"
                                wire:click="openModalDelete({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-modal title="เพิ่มผู้ใช้งาน" wire:model="showModal" maxWidth="2xl">
        <div>
            <label>ชื่อผู้ใช้</label>
            <input type="text" class="form-control" wire:model="name">
        </div>

        <div class="mt-3">
            <label>อีเมล</label>
            <input type="email" class="form-control" wire:model="email">
            @if ($mailError)
                <span class="alert-danger">{{ $mailError }}</span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
            <div>
                <label>รหัสผ่าน</label>
                <input type="password"
                    class="form-control {{ $errorLengthPassword ? 'ring-red-500/70 border-red-500/70' : '' }}"
                    wire:model="password" wire:blur="checkPasswordLength">
                @if ($errorLengthPassword)
                    <span class="text-danger text-sm">{{ $errorLengthPassword }}</span>
                @endif
            </div>
            <div>
                <label>ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" wire:model="password_confirm">
            </div>
            @if ($error)
                <span class="text-danger text-sm">{{ $error }}</span>
            @endif
        </div>


        <div class="mt-3">
            <label>สถานะ</label>
            <select class="form-control" wire:model="status">
                @foreach ($listStatus as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>


        <div class="mt-3 text-center pb-3">
            <button class="btn-success" wire:click="save">
                <i class="fa-solid fa-check me-2"></i>
                ตกลง
            </button>
            <button class="btn-secondary" wire:click="showModal = false">
                <i class="fa-solid fa-xmark me-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>

    <x-modal-confirm title="ลบผู้ใช้" text="คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้   
    {{ $nameForDelete }}"
        clickConfirm="delete" clickCancel="showModalDelete = false" showModalDelete="showModalDelete" />
</div>
