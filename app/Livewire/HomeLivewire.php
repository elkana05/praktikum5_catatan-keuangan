<?php

namespace App\Livewire;

use Livewire\Attributes\WithoutUrl;
use App\Livewire\Forms\AddTodoForm;
use App\Livewire\Forms\EditTodoForm;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HomeLivewire extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $auth;
    protected $paginationTheme = 'bootstrap';

    // Menggunakan Form Objects
    public AddTodoForm $addForm;
    public EditTodoForm $editForm;
    
    // Properti untuk Hapus Catatan
    public $deleteTodoId;
    public $deleteTodoTitle;
    public $deleteTodoConfirmTitle;

    // Properti untuk Pencarian dan Filter
    public $search = '';
    public $filterType = '';
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function resetDateFilter()
    {
        $this->reset('startDate', 'endDate');
    }

    public function addTodo()
    {
        // Livewire akan secara otomatis mengisi data ke $this->addForm sebelum validate() dipanggil

        $this->addForm->validate();

        $coverPath = null;
        if ($this->addForm->cover) {
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->addForm->cover->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $coverPath = $this->addForm->cover->storeAs('covers', $filename, 'public');
        }

        Todo::create([
            'user_id' => $this->auth->id,
            'title' => $this->addForm->title,
            'amount' => $this->addForm->amount,
            'type' => $this->addForm->type,
            'cover' => $coverPath,
            'description' => $this->addForm->description,
            'created_at' => $this->addForm->created_at,
        ]);
        $this->addForm->reset();
        $this->dispatch('close-modal', 'addTodoModal'); // Tetap tutup modal
        $this->dispatch('reset-trix', 'add_todo_description');

        $this->dispatch('show-alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Catatan keuangan berhasil ditambahkan.',
        ]);
    }

    public function prepareEditTodo($todoId)
    {
        $todo = Todo::find($todoId);
        if ($todo && $todo->user_id === $this->auth->id) {
            $this->editForm->todoId = $todo->id;
            $this->editForm->title = $todo->title;
            $this->editForm->amount = $todo->amount;
            $this->editForm->type = $todo->type;
            $this->editForm->description = $todo->description;
            $this->editForm->oldCover = $todo->cover;
            $this->editForm->newCover = null; // Reset pratinjau file baru
            
            $this->dispatch('open-modal', 'editTodoModal');
        }
    }

    public function editTodo()
    {
        $this->editForm->validate();

        $todo = Todo::find($this->editForm->todoId);
        if ($todo && $todo->user_id === $this->auth->id) {
            $coverPath = $todo->cover; // Simpan path lama

            if ($this->editForm->newCover) {
                // Hapus cover lama jika ada
                if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                    Storage::disk('public')->delete($coverPath);
                }
                // Simpan cover baru
                $userId = $this->auth->id;
                $dateNumber = now()->format('YmdHis');
                $extension = $this->editForm->newCover->getClientOriginalExtension();
                $filename = $userId . '_' . $dateNumber . '.' . $extension;
                $coverPath = $this->editForm->newCover->storeAs('covers', $filename, 'public');
            }

            $todo->update([
                'title' => $this->editForm->title,
                'amount' => $this->editForm->amount,
                'type' => $this->editForm->type,
                'description' => $this->editForm->description,
                'cover' => $coverPath, // Update dengan path baru atau path lama
            ]);
        }
        $this->editForm->reset(); // Reset form setelah berhasil
        $this->dispatch('close-modal', 'editTodoModal'); // Tetap tutup modal
        $this->dispatch('show-alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Catatan keuangan berhasil diperbarui.',
        ]);
    }

    public function prepareDeleteTodo($todoId)
    {
        $todo = Todo::find($todoId);
        if ($todo && $todo->user_id === $this->auth->id) {
            $this->deleteTodoId = $todo->id;
            $this->deleteTodoTitle = $todo->title;
            $this->deleteTodoConfirmTitle = '';

            $this->dispatch('open-modal', 'deleteTodoModal');
        }
    }

    public function deleteTodo()
    {
        $todo = Todo::find($this->deleteTodoId);
        if ($todo && $todo->user_id === $this->auth->id) {
            // Validasi konfirmasi judul
            if ($this->deleteTodoConfirmTitle === $todo->title) {
                $todo->delete();
                $this->reset(['deleteTodoId', 'deleteTodoTitle', 'deleteTodoConfirmTitle']);
                $this->dispatch('close-modal', 'deleteTodoModal'); // Tetap tutup modal
                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Catatan keuangan berhasil dihapus.',
                ]);
            } else {
                // $this->addError('deleteTodoConfirmTitle', 'Konfirmasi judul tidak sesuai.');
                $this->dispatch('show-alert', [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Konfirmasi judul tidak sesuai. Catatan tidak dihapus.',
                ]);
            }
        }
    }

    public function render()
    {
        // Query dasar untuk semua data pengguna
        $baseQuery = Todo::where('user_id', $this->auth->id);

        // Query untuk chart, dengan filter tanggal
        $chartQuery = (clone $baseQuery);
        if ($this->startDate) {
            $chartQuery->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $chartQuery->whereDate('created_at', '<=', $this->endDate);
        }

        // Hitung total untuk chart berdasarkan query yang sudah difilter tanggal
        $totalIncome = $chartQuery->where('type', 1)->sum('amount');
        $totalExpense = (clone $chartQuery)->where('type', 0)->sum('amount');

        // Hitung saldo akhir dari SEMUA data, bukan hanya dari rentang tanggal chart
        $overallIncome = (clone $baseQuery)->where('type', 1)->sum('amount');
        $overallExpense = (clone $baseQuery)->where('type', 0)->sum('amount');
        $balance = $overallIncome - $overallExpense;

        // Terapkan pencarian pada query yang difilter
        if ($this->search) {
            $baseQuery->where('title', 'like', '%' . $this->search . '%');
        }

        // Terapkan filter jenis pada query yang difilter
        if ($this->filterType !== '') {
            $baseQuery->where('type', $this->filterType);
        }
        $records = $baseQuery->latest()->paginate(20);

        // Kirim event ke browser untuk update chart
        $this->dispatch('update-charts', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);

        return view('livewire.home-livewire', [
            'records' => $records,
            'balance' => $overallIncome - $overallExpense, // Saldo dari semua data
            'totalIncome' => $overallIncome, // Total pemasukan dari semua data
            'totalExpense' => $overallExpense, // Total pengeluaran dari semua data
            'theme' => session('theme', 'dark'), // Kirim tema ke view
        ]);
    }
}