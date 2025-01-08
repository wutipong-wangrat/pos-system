<?php

namespace App\Livewire;

use App\Models\ProductModel;
use App\Models\CashTransactionModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $dailyIncome = 0;
    public $weeklyIncome = 0;
    public $monthlyIncome = 0;
    public $profit = 0;
    public $drawerBalance = 0;
    public $totalProducts = 0;

    public $dailyChartData;
    public $weeklyChartData;
    public $pieChartData;

    public $showBalanceModal = false;
    public $newDrawerBalance = 0;

    public function mount(){
        $this->loadMetrics();
        $this->loadChartData();
        $this->loadDrawerBalance();
    }

    public function loadDrawerBalance(){
        $latestTransaction = CashTransactionModel::orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')->first();

        $this->drawerBalance = $latestTransaction ? $latestTransaction->balance : 0;
    }

    public function loadMetrics(){
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $this->dailyIncome = CashTransactionModel::where('type', 'cash_in')
        ->whereDate('created_at', $today)
        ->sum('amount');

        $this->weeklyIncome = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', $weekStart)
        ->sum('amount');

        $this->monthlyIncome = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', $monthStart)
        ->sum('amount');

        $latestTransaction = CashTransactionModel::latest()->first();
        $this->drawerBalance = $latestTransaction ? $latestTransaction->balance : 0;

        $this->profit = $this->monthlyIncome * 0.3;
        $this->totalProducts = ProductModel::where('status', 'available')->count();
    }

    public function loadChartData(){
        $this->dailyChartData = CashTransactionModel::where('type', 'cash_in')
        ->whereDate('created_at', Carbon::today())
        ->select(
            DB::raw('HOUR(created_at) as hour'), 
            DB::raw('SUM(amount) as total')
        )
        ->groupBy('hour')
        ->orderBy('hour')
        ->get()
        ->map(function ($item) {
            return [
                'hour' => sprintf('%02d:00', $item->hour),
                'total' => $item->total
            ];
        });

        // weekly sales chart data
        $this->weeklyChartData = CashTransactionModel::where('type', 'cash_in')
        ->where('created_at', '>=', Carbon::now()->startOfWeek())
        ->select(
            DB::raw('DAY(created_at) as date'), 
            DB::raw('SUM(amount) as total')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->map(function ($item){
            return [
                'day' => Carbon::parse($item->date)->format('D'),
                'total' => $item->total
            ];
        });

        $this->pieChartData = [
            ['name' => 'รายวัน', 'value' => (float)$this->dailyIncome],
            ['name' => 'รายสัปดาห์', 'value' => (float)$this->weeklyIncome],
            ['name' => 'รายเดือน', 'value' => (float)$this->monthlyIncome]
        ];
    }

    public function redirectToProducts(){
        return redirect()->route('products');
    }

    public function openDrawerBalanceModal(){
        $this->showBalanceModal = true;
    }

    public function closeDrawerBalanceModal(){
        $this->showBalanceModal = false;
    }

    public function changeDrawerBalance($balance){
        $transaction = new CashTransactionModel();

        if ($this->newDrawerBalance > $this->drawerBalance) {
            $transaction->type = 'adjust_balance';
        } else {
            $transaction->type = 'cash_out';
        }

        $transaction->previous_balance = $this->drawerBalance;
        $transaction->balance = $this->newDrawerBalance;
        $transaction->amount = $this->newDrawerBalance - $this->drawerBalance;
        $transaction->description = 'Cash change balance from ' . number_format($this->drawerBalance, 0) . ' to ' . number_format($this->newDrawerBalance, 0);
        $transaction->save();

        $this->drawerBalance = $this->newDrawerBalance;
        $this->loadMetrics();
        $this->loadChartData();
        $this->showBalanceModal = false;
        $this->newDrawerBalance = 0;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}