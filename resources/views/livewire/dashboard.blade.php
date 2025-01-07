<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <div class="p-6 space-y-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-200">แดชบอร์ดภาพรวม</h1>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <!-- Daily Income Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">รายได้วันนี้</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($dailyIncome, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Profit Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">กำไร</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($profit, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Drawer Balance Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200 hover:scale-105 cursor-pointer"
                wire:click="openDrawerBalanceModal()">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fas fa-cash-register text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">เงินในลิ้นชัก</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($drawerBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200 hover:scale-105 cursor-pointer"
                wire:click="redirectToProducts()">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">จำนวนสินค้า</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalProducts) }} รายการ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Daily Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">ยอดขายรายวัน</h2>
                <div id="dailyChart" style="height: 300px;" class="text-gray-800"></div>
            </div>

            <!-- Weekly Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">ยอดขายรายสัปดาห์</h2>
                <div id="weeklyChart" style="height: 300px;" class="text-gray-800"></div>
            </div>

            <!-- Income Distribution Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">สัดส่วนรายได้</h2>
                <div id="pieChart" style="height: 300px;" class="text-gray-800"></div>
            </div>
        </div>
    </div>

    <x-modal title="เงินในลิ้นชัก" maxWidth="md" wire:model="showBalanceModal" zIndex="9999">
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
            <label for="drawerBalance" class="block text-sm font-medium text-gray-700 mb-1">เพิ่มเงินในลิ้นชัก</label>
            <input type="number" wire:model="newDrawerBalance" class="form-control">
        </div>

        <div class="flex justify-center mt-6 pb-4">
            <button
                class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg mr-2 hover:bg-green-600 transition duration-200"
                wire:click="changeDrawerBalance('{{ $drawerBalance }}')">
                <i class="fa-solid fa-check mr-2"></i>
                บันทึก
            </button>
            <button
                class="flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200"
                wire:click="closeDrawerBalanceModal()">
                <i class="fa-solid fa-xmark mr-2"></i>
                ยกเลิก
            </button>
        </div>
    </x-modal>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const chartTheme = {
                fontFamily: 'Noto Sans Thai, sans-serif',
                toolbar: {
                    show: false
                }
            };

            // Daily Sales Chart
            const dailyOptions = {
                chart: {
                    type: 'line',
                    height: 300,
                    ...chartTheme
                },
                series: [{
                    name: 'ยอดขาย',
                    data: @json($dailyChartData->pluck('total'))
                }],
                xaxis: {
                    categories: @json($dailyChartData->pluck('hour'))
                },
                title: {
                    text: 'ยอดขายรายชั่วโมง',
                    align: 'center'
                },
                stroke: {
                    curve: 'smooth'
                }
            };
            new ApexCharts(document.querySelector("#dailyChart"), dailyOptions).render();

            // Weekly Sales Chart
            const weeklyOptions = {
                chart: {
                    type: 'bar',
                    height: 300,
                    ...chartTheme
                },
                series: [{
                    name: 'ยอดขาย',
                    data: @json($weeklyChartData->pluck('total'))
                }],
                xaxis: {
                    categories: @json($weeklyChartData->pluck('day'))
                },
                title: {
                    text: 'ยอดขายรายวัน',
                    align: 'center'
                },
                colors: ['#3B82F6']
            };
            new ApexCharts(document.querySelector("#weeklyChart"), weeklyOptions).render();

            // Pie Chart
            const pieOptions = {
                chart: {
                    type: 'pie',
                    height: 300,
                    ...chartTheme
                },
                series: @json(collect($pieChartData)->pluck('value')->filter()), // กรองค่าที่เป็น null หรือ 0 ออก
                labels: @json(collect($pieChartData)->pluck('name')),
                title: {
                    text: 'สัดส่วนรายได้',
                    align: 'center'
                },
                // เพิ่มการตั้งค่าเพิ่มเติม
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#pieChart"), pieOptions).render();
        });
    </script>
@endpush
