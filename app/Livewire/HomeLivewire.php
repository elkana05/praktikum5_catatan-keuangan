<?php

namespace App\Livewire;
 
use App\Models\FinancialRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class HomeLivewire extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Search and filter
    public $search = '';
    public $filterType = 'all'; // all | income | expense
    public $auth;

    protected $queryString = ['search', 'filterType'];

    public function mount()
    {
        $this->auth = Auth::user();
    }

    #[On('modalClosed')]
    public function handleModalClosed(array $data)
    {
        switch ($data['modalId']) {
            case 'editRecordModal':
            case 'detailRecordModal':
            case 'deleteRecordModal':
                $this->reset(['detailRecordId', 'detailDescription', 'detailType', 'detailAmount', 'detailRecordDate', 'detailAttachment']);
                $this->reset(['editRecordId', 'editDescription', 'editType', 'editAmount', 'editRecordDate', 'editAttachment', 'existingAttachment']);
                $this->reset(['deleteRecordId', 'deleteRecordDescription', 'deleteRecordConfirmDescription']);
                $this->resetErrorBag();
                break;
            case 'addRecordModal':
                $this->reset(['addDescription', 'addAmount', 'addRecordDate', 'addAttachment']);
                $this->addType = 'expense';
                break;
        }
    }

    private function getMonthlyStats()
    {
        $months = [];
        $incomeSeries = [];
        $expenseSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $months[] = $m->format('M Y');
            $start = $m->copy()->startOfMonth()->toDateString();
            $end = $m->copy()->endOfMonth()->toDateString();

            $incomeSeries[] = (float) FinancialRecord::where('user_id', $this->auth->id)
                ->where('type', 'income')
                ->whereBetween('record_date', [$start, $end])
                ->sum('amount');

            $expenseSeries[] = (float) FinancialRecord::where('user_id', $this->auth->id)
                ->where('type', 'expense')
                ->whereBetween('record_date', [$start, $end])
                ->sum('amount');
        }
        return compact('months', 'incomeSeries', 'expenseSeries');
    }

    public function getChartData()
    {
        $monthlyStats = $this->getMonthlyStats();
        $totalIncome = FinancialRecord::where('user_id', $this->auth->id)->where('type', 'income')->sum('amount');
        $totalExpense = FinancialRecord::where('user_id', $this->auth->id)->where('type', 'expense')->sum('amount');

        // Data untuk Radial Chart (Persentase)
        $totalTransactions = $totalIncome + $totalExpense;
        $distributionSeries = [
            $totalTransactions > 0 ? round(($totalIncome / $totalTransactions) * 100) : 0,
            $totalTransactions > 0 ? round(($totalExpense / $totalTransactions) * 100) : 0,
        ];

        return [
            'monthsLabels' => $monthlyStats['months'],
            'incomeSeries' => $monthlyStats['incomeSeries'],
            'expenseSeries' => $monthlyStats['expenseSeries'],
            'distributionSeries' => $distributionSeries,
        ];
    }

    public function render()
    {
        $query = FinancialRecord::where('user_id', $this->auth->id);

        // Apply search (description)
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Apply type filter 
        if ($this->filterType && $this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

    $records = $query->orderBy('record_date', 'desc')->paginate(20);

        // Statistik: Total Pemasukan, Pengeluaran, dan Saldo
        $totalIncome = FinancialRecord::where('user_id', $this->auth->id)->where('type', 'income')->sum('amount');
        $totalExpense = FinancialRecord::where('user_id', $this->auth->id)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $chartData = $this->getChartData();

        return view('livewire.home-livewire', [
            'records' => $records,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'chartMonthsLabels' => $chartData['monthsLabels'],
            'chartIncomeSeries' => $chartData['incomeSeries'],
            'chartExpenseSeries' => $chartData['expenseSeries'],
            'chartDistributionSeries' => $chartData['distributionSeries'],
        ]);
    }

    // Reset halaman pagination saat search atau filter berubah
    public function updatedSearch()
    {
        $this->resetPage();
        $this->dispatch('refresh-chart', $this->getChartData());
    }

    public function updatedFilterType()
    {
        $this->resetPage();
        $this->dispatch('refresh-chart', $this->getChartData());
    }

    // Method to trigger search
    public function doSearch()
    {
        $this->resetPage();
        $this->dispatch('refresh-chart', $this->getChartData());
    }

    // Add Record
    public $addDescription;
    public $addType = 'expense';
    public $addAmount;
    public $addRecordDate;
    public $addAttachment;

    public function mountAdd()
    {
        $this->addRecordDate = now()->format('Y-m-d');
    }

    public function addRecord()
    {
        $this->validate([
            'addDescription' => 'required|string|max:255',
            'addType' => 'required|in:income,expense',
            'addAmount' => 'required|numeric|min:0|max:9999999999999', // Max 13 digit untuk PostgreSQL
            'addRecordDate' => 'required|date',
            'addAttachment' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $attachmentPath = null;
        if ($this->addAttachment) {
            $attachmentPath = $this->addAttachment->store('attachments', 'public');
        }

        // Simpan catatan ke database
        FinancialRecord::create([
            'description' => $this->addDescription,
            'type' => $this->addType,
            'amount' => $this->addAmount,
            'record_date' => $this->addRecordDate,
            'attachment' => $attachmentPath,
            'user_id' => $this->auth->id,
        ]);

        // Reset the form
        $this->reset(['addDescription', 'addAmount', 'addRecordDate', 'addAttachment']);
        $this->addType = 'expense'; // Kembalikan ke nilai default
        $this->mountAdd(); // Set default date again

        // Tutup modal
    $this->dispatch('closeModal', ['id' => 'addRecordModal']);
    $this->dispatch('trix-reset', ['id' => 'addDescription']);
    $this->dispatch('refresh-chart', $this->getChartData());
        // Notifikasi sukses (emit via Livewire dispatch so layout listens with Livewire.on)
        $this->dispatch('swal', [
            'title' => 'Berhasil',
            'text' => 'Catatan keuangan berhasil ditambahkan.',
            'icon' => 'success',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }

    // Edit Record
    public $editRecordId;
    public $editDescription;
    public $editType;
    public $editAmount;
    public $editRecordDate;
    public $editAttachment;
    public $existingAttachment;

    public function mountEdit($id)
    {
        $record = FinancialRecord::where('id', $id)->where('user_id', $this->auth->id)->first();
        if ($record) {
            $this->editRecordId = $record->id;
            $this->editDescription = $record->description;
            $this->editType = $record->type;
            $this->editAmount = $record->amount;
            $this->editRecordDate = $record->record_date->format('Y-m-d');
            $this->existingAttachment = $record->attachment;
            $this->editAttachment = null; // Reset file input

            $this->dispatch('showModal', ['id' => 'editRecordModal']);
            $this->dispatch('trix-input', ['field' => 'editDescription', 'value' => $record->description]);
        }
    }

    public function updateRecord()
    {
        $this->validate([
            'editDescription' => 'required|string|max:255',
            'editType' => 'required|in:income,expense',
            'editAmount' => 'required|numeric|min:0|max:9999999999999',
            'editRecordDate' => 'required|date',
            'editAttachment' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $record = FinancialRecord::find($this->editRecordId);
        if ($record && $record->user_id == $this->auth->id) {
            $attachmentPath = $record->attachment;
            if ($this->editAttachment) {
                // Hapus lampiran lama jika ada
                if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                // Simpan lampiran baru
                $attachmentPath = $this->editAttachment->store('attachments', 'public');
            }

            $record->update([
                'description' => $this->editDescription,
                'type' => $this->editType,
                'amount' => $this->editAmount,
                'record_date' => $this->editRecordDate,
                'attachment' => $attachmentPath,
            ]);

            $this->dispatch('closeModal', ['id' => 'editRecordModal']);
            $this->dispatch('refresh-chart', $this->getChartData());
            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Catatan keuangan berhasil diperbarui.',
                'icon' => 'success',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2200,
            ]);
        }
    }

    // Delete Record
    public $deleteRecordId;
    public $deleteRecordDescription;
    public $deleteRecordConfirmDescription;

    public function mountDelete($id)
    {
        $record = FinancialRecord::where('id', $id)->where('user_id', $this->auth->id)->first();
        if ($record) {
            $this->deleteRecordId = $record->id;
            $this->deleteRecordDescription = strip_tags($record->description); // Ambil teks saja untuk konfirmasi
            $this->dispatch('showModal', ['id' => 'deleteRecordModal']);
        }
    }

    public function deleteRecord()
    {
        $record = FinancialRecord::find($this->deleteRecordId);
        if ($record && $record->user_id == $this->auth->id) {
            // Validasi konfirmasi
            if ($this->deleteRecordConfirmDescription !== strip_tags($record->description)) {
                $this->addError('deleteRecordConfirmDescription', 'Konfirmasi deskripsi tidak cocok.');
                return;
            }

            // Hapus lampiran jika ada
            if ($record->attachment && Storage::disk('public')->exists($record->attachment)) {
                Storage::disk('public')->delete($record->attachment);
            }

            $record->delete();

            $this->dispatch('closeModal', ['id' => 'deleteRecordModal']);
            $this->dispatch('refresh-chart', $this->getChartData());
            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Catatan keuangan berhasil dihapus.',
                'icon' => 'success',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2200,
            ]);
        }
    }

    // Detail Record
    public $detailRecordId;
    public $detailDescription;
    public $detailType;
    public $detailAmount;
    public $detailRecordDate;
    public $detailAttachment;

    public function mountDetail($id)
    {
        $record = FinancialRecord::where('id', $id)->where('user_id', $this->auth->id)->first();
        if ($record) {
            $this->detailRecordId = $record->id;
            $this->detailDescription = $record->description;
            $this->detailType = $record->type;
            $this->detailAmount = $record->amount;
            $this->detailRecordDate = $record->record_date->format('d F Y');
            $this->detailAttachment = $record->attachment;

            $this->dispatch('showModal', ['id' => 'detailRecordModal']);
        }
    }
}
