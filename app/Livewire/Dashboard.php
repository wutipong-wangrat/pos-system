<?php

namespace App\Livewire;

use App\Models\ProductModel;
use App\Models\CashTransactionModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $weeklyIncome = 0;
    public $monthlyIncome = 0;
    public $yearlyIncome = 0;
    public $profit = 0;
    public $totalProducts = 0;

    public $weeklyChartData;
    public $monthlyChartData;
    public $yearlyChartData;
    public $pieChartData;

    public function mount(){
        $this->loadMetrics();
        $this->loadChartData();
    }

    public function loadMetrics(){
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        $this->weeklyIncome = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', $weekStart)
        ->sum('amount');

        $this->monthlyIncome = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', $monthStart)
        ->sum('amount');

        $this->yearlyIncome = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', $yearStart)
        ->sum('amount');

        $this->profit = $this->monthlyIncome * 0.3;
        $this->totalProducts = ProductModel::where('status', 'available')->count();
    }

    public function loadChartData()
    {
        // Weekly chart data (last 7 days)
        $this->weeklyChartData = CashTransactionModel::where('type', 'cash_in')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'day' => Carbon::parse($item->date)->format('D'),
                    'total' => $item->total
                ];
            });

        // Monthly chart data (last 12 months)
        $this->monthlyChartData = CashTransactionModel::where('type', 'cash_in')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->month)->format('M Y'),
                    'total' => $item->total
                ];
            });

        // Yearly chart data (last 5 years)
        $this->yearlyChartData = CashTransactionModel::where('type', 'cash_in')
            ->where('created_at', '>=', Carbon::now()->subYears(5))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'total' => $item->total
                ];
            });

        $this->pieChartData = [
            ['name' => 'รายสัปดาห์', 'value' => (float)$this->weeklyIncome],
            ['name' => 'รายเดือน', 'value' => (float)$this->monthlyIncome],
            ['name' => 'รายปี', 'value' => (float)$this->yearlyIncome]
        ];
    }

    public function redirectToProducts(){
        return redirect()->route('products');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}