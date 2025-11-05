<?php

namespace App\Livewire;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinancialChartLivewire extends Component
{
    public $chartData;

    public function mount()
    {
        $this->prepareChartData();
    }

    public function prepareChartData()
    {
        $user = Auth::user();
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        // Ambil data transaksi 6 bulan terakhir
        $transactions = Todo::where('user_id', $user->id)
            ->where('transaction_date', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month', 'type')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $data = [];
        // Inisialisasi data untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y'); // e.g., Nov 2023
            $data[$monthName] = ['income' => 0, 'expense' => 0];
        }

        // Isi data dari database
        foreach ($transactions as $transaction) {
            $monthName = Carbon::createFromDate($transaction->year, $transaction->month, 1)->format('M Y');
            if (isset($data[$monthName])) {
                if ($transaction->type == 'income') {
                    $data[$monthName]['income'] = $transaction->total;
                } else {
                    $data[$monthName]['expense'] = $transaction->total;
                }
            }
        }

        $this->chartData = [
            'categories' => array_keys($data),
            'series' => [
                ['name' => 'Pemasukan', 'data' => array_column($data, 'income')],
                ['name' => 'Pengeluaran', 'data' => array_column($data, 'expense')],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.financial-chart-livewire');
    }
}