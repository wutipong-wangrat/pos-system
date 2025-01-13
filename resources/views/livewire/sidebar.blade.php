<div class="font-noto_thai">
    <!-- Mobile Menu Toggle Button -->
    <button
        class="fixed top-4 left-4 z-50 lg:hidden bg-slate-800 text-white p-2 rounded-lg shadow-lg hover:bg-slate-700 transition-colors"
        onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
        <i class="fa-solid fa-bars text-xl"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed top-0 left-0 h-full w-64 bg-slate-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col overflow-hidden">
        <!-- Top Logo Section -->
        <div class="flex-shrink-0 flex flex-col items-center py-6 border-b border-slate-700">
            <i class="fa-solid fa-shop text-3xl text-white mb-2"></i>
            <h1 class="text-lg font-semibold text-white">POS Laravel</h1>
            <h2 class="text-sm text-slate-400">For Shop Owner</h2>
        </div>

        <!-- Scrollable Container -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Menu Section -->
            <div class="flex-grow p-4">
                <div class="mb-4">
                    <h1 class="font-bold text-lg text-white">เมนูหลัก</h1>
                </div>

                <ul class="space-y-2">
                    @if ($user_role == 'admin')
                        <li wire:click="changeMenu('dashboard')"
                            class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'dashboard' ? 'bg-slate-700' : '' }}">
                            <i class="fa-solid fa-chart-line w-6"></i>
                            <span>Dashboard</span>
                        </li>
                    @endif

                    <li wire:click="changeMenu('order')"
                        class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'order' ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-shopping-cart w-6"></i>
                        <span>สั่งซื้อสินค้า</span>
                    </li>

                    @if ($user_role == 'admin')
                        <li wire:click="changeMenu('categories')"
                            class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'categories' ? 'bg-slate-700' : '' }}">
                            <i class="fa-solid fa-list w-6"></i>
                            <span>หมวดหมู่สินค้า</span>
                        </li>

                        <li wire:click="changeMenu('products')"
                            class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'products' ? 'bg-slate-700' : '' }}">
                            <i class="fa-solid fa-box w-6"></i>
                            <span>สินค้า</span>
                        </li>
                    @endif
                    <li wire:click="changeMenu('history')"
                        class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'history' ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-history w-6"></i>
                        <span>ประวัติการขาย</span>
                    </li>

                    @if ($user_role == 'admin')
                        <li wire:click="changeMenu('users')"
                            class="menu-item flex items-center p-2 rounded-lg text-white hover:bg-slate-700 cursor-pointer transition-colors {{ $currentMenu == 'users' ? 'bg-slate-700' : '' }}">
                            <i class="fa-solid fa-users w-6"></i>
                            <span>จัดการผู้ใช้</span>
                        </li>
                    @endif

                </ul>
            </div>

            <!-- Profile Section -->
            <div class="flex-shrink-0 border-t border-slate-700 p-4">
                <div class="flex flex-col space-y-4">
                    <!-- User Info -->
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ $user_name ?? 'Tester' }}</p>
                        <p class="text-xs text-slate-400">{{ $user_email ?? 'tester@example.com' }}</p>
                    </div>

                    <!-- Profile Actions -->
                    <div class="flex flex-col space-y-2">
                        <button wire:click="editProfile"
                            class="w-full px-3 py-2 text-sm bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-colors flex items-center justify-center">
                            <i class="fa-solid fa-pencil mr-2"></i>
                            แก้ไขข้อมูลส่วนตัว
                        </button>
                        <button wire:click="showModal = true"
                            class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            ออกจากระบบ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div onclick="document.getElementById('sidebar').classList.add('-translate-x-full')"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden transition-opacity duration-300 ease-in-out"
        style="display: none;">
    </div>

    <x-modal wire:model="showModal" maxWidth="sm" title="ออกจากระบบ" zIndex="9999">
        <div class="text-center">
            <i class="fa-solid fa-question text-red-500 text-5xl"></i>
            <div>
                <h1 class="text-3xl font-bold mt-4 text-gray-800">ออกจากระบบ</h1>
            </div>
            <div>
                <h2 class="text-2xl mt-3 text-gray-800">คุณต้องการออกจากระบบหรือไม่</h2>
            </div>
        </div>

        <div class="flex justify-center mt-6 pb-4">
            <button
                class="flex items-center px-4 py-2 bg-red-500 text-white rounded-lg mr-2 shadow-md hover:bg-red-600 transition duration-200"
                wire:click="logout">
                <i class="fa-solid fa-check mr-2"></i>
                ยืนยัน
            </button>
            <button
                class="flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition duration-200"
                wire:click="showModal = false">
                <i class="fa-solid fa-xmark mr-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>

    <x-modal wire:model='showModalEdit' title="แก้ไขข้อมูลผู้ใช้" maxWidth="lg" zIndex=9999>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" wire:model="username" class="form-control">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password ใหม่</label>
                <input type="password" wire:model="password" class="form-control">
            </div>

            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">ยืนยัน
                    Password</label>
                <input type="password" wire:model="password_confirm" class="form-control">
            </div>
        </div>

        <div class="flex justify-center mt-6 pb-4">
            <button
                class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg mr-2 hover:bg-green-600 transition duration-200"
                wire:click="updateProfile">
                <i class="fa-solid fa-check mr-2"></i>
                บันทึก
            </button>
            <button
                class="flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200"
                wire:click="showModalEdit = false">
                <i class="fa-solid fa-xmark mr-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            const sidebar = document.getElementById('sidebar');
            // const overlay = document.getElementById('sidebar-overlay');

            // function toggleOverlay(show) {
            //     overlay.style.display = show ? 'block' : 'none';
            // }

            // Toggle overlay when sidebar opens/closes on mobile
            // const observer = new MutationObserver((mutations) => {
            //     mutations.forEach((mutation) => {
            //         if (mutation.target.classList.contains('-translate-x-full')) {
            //             toggleOverlay(false);
            //         } else {
            //             toggleOverlay(true);
            //         }
            //     });
            // });

            // observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    toggleOverlay(false);
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    toggleOverlay(false);
                }
            });

            // Initialize sidebar state
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
            }
        });
    </script>
@endpush
