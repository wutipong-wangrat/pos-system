<div class="any-content font-noto_thai lg:ml-64 bg-gray-100 min-h-screen">
    <div class="p-6 space-y-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-200">แดชบอร์ดภาพรวม</h1>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <!-- Weekly Income Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <i class="fas fa-calendar-week text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">รายได้สัปดาห์นี้</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($weeklyIncome, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Income Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">รายได้เดือนนี้</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($monthlyIncome, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Yearly Income Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fas fa-calendar text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">รายได้ปีนี้</p>
                        <p class="text-xl font-bold text-gray-800">฿{{ number_format($yearlyIncome, 2) }}</p>
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
            <!-- Weekly Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">ยอดขายรายสัปดาห์</h2>
                <div id="weeklyChart" style="height: 300px;" class="text-gray-800"></div>
            </div>

            <!-- Monthly Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">ยอดขายรายเดือน</h2>
                <div id="monthlyChart" style="height: 300px;" class="text-gray-800"></div>
            </div>

            <!-- Yearly Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">ยอดขายรายปี</h2>
                <div id="yearlyChart" style="height: 300px;" class="text-gray-800"></div>
            </div>
        </div>
    </div>
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
                    text: 'ยอดขายรายวัน (7 วันล่าสุด)',
                    align: 'center'
                },
                colors: ['#3B82F6']
            };
            new ApexCharts(document.querySelector("#weeklyChart"), weeklyOptions).render();

            // Monthly Sales Chart
            const monthlyOptions = {
                chart: {
                    type: 'line',
                    height: 300,
                    ...chartTheme
                },
                series: [{
                    name: 'ยอดขาย',
                    data: @json($monthlyChartData->pluck('total'))
                }],
                xaxis: {
                    categories: @json($monthlyChartData->pluck('month'))
                },
                title: {
                    text: 'ยอดขายรายเดือน (12 เดือนล่าสุด)',
                    align: 'center'
                },
                stroke: {
                    curve: 'smooth'
                }
            };
            new ApexCharts(document.querySelector("#monthlyChart"), monthlyOptions).render();

            // Yearly Sales Chart
            const yearlyOptions = {
                chart: {
                    type: 'bar',
                    height: 300,
                    ...chartTheme
                },
                series: [{
                    name: 'ยอดขาย',
                    data: @json($yearlyChartData->pluck('total'))
                }],
                xaxis: {
                    categories: @json($yearlyChartData->pluck('year'))
                },
                title: {
                    text: 'ยอดขายรายปี (5 ปีล่าสุด)',
                    align: 'center'
                },
                colors: ['#10B981']
            };
            new ApexCharts(document.querySelector("#yearlyChart"), yearlyOptions).render();
        });
    </script>
@endpush
