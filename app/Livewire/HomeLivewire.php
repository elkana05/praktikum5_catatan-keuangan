<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HomeLivewire extends Component
{
    use WithPagination;
    public $auth;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Tambah Catatan
    public $addTodoTitle;
    public $addTodoAmount;
    public $addTodoType = 0; // Default: Pengeluaran
    public $addTodoDescription;

    // Properti untuk Edit Catatan
    public $editTodoId;
    public $editTodoTitle;
    public $editTodoAmount;
    public $editTodoType;
    public $editTodoDescription;

    // Properti untuk Hapus Catatan
    public $deleteTodoId;
    public $deleteTodoTitle;
    public $deleteTodoConfirmTitle;

    // Properti untuk Pencarian dan Filter
    public $search = '';
    public $filterType = '';

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

    public function addTodo()
    {
        $this->validate([
            'addTodoTitle' => 'required|string|max:255',
            'addTodoAmount' => 'required|numeric',
            'addTodoType' => 'required|boolean',
            'addTodoDescription' => 'nullable|string',
        ]);

        Todo::create([
            'user_id' => $this->auth->id,
            'title' => $this->addTodoTitle,
            'add_todo_amount' => $this->addTodoAmount,
            'type' => $this->addTodoType,
            'description' => $this->addTodoDescription,
        ]);

        $this->reset(['addTodoTitle', 'addTodoAmount', 'addTodoType', 'addTodoDescription']);
        $this->dispatch('close-modal', 'addTodoModal'); // Tetap tutup modal
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
            $this->editTodoId = $todo->id;
            $this->editTodoTitle = $todo->title;
            $this->editTodoAmount = $todo->add_todo_amount;
            $this->editTodoType = $todo->type;
            $this->editTodoDescription = $todo->description;

            $this->dispatch('open-modal', 'editTodoModal');
        }
    }

    public function editTodo()
    {
        $this->validate([
            'editTodoTitle' => 'required|string|max:255',
            'editTodoAmount' => 'required|numeric',
            'editTodoType' => 'required|boolean',
            'editTodoDescription' => 'nullable|string',
        ]);

        $todo = Todo::find($this->editTodoId);
        if ($todo && $todo->user_id === $this->auth->id) {
            $todo->update([
                'title' => $this->editTodoTitle,
                'add_todo_amount' => $this->editTodoAmount,
                'type' => $this->editTodoType,
                'description' => $this->editTodoDescription,
            ]);
        }

        $this->reset(['editTodoId', 'editTodoTitle', 'editTodoAmount', 'editTodoType', 'editTodoDescription']);
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
                $this->addError('deleteTodoConfirmTitle', 'Konfirmasi judul tidak sesuai.');
            }
        }
    }

    public function render()
    {
        $query = Todo::where('user_id', $this->auth->id);

        // Terapkan pencarian jika ada input
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        // Terapkan filter jika ada pilihan
        if ($this->filterType !== '') {
            $query->where('type', $this->filterType);
        }

        $totalIncome = (clone $query)->where('type', 1)->sum('add_todo_amount');
        $totalExpense = (clone $query)->where('type', 0)->sum('add_todo_amount');

        $records = $query->latest()->paginate(20);

        // Kirim event untuk update chart di frontend
        $this->dispatch('update-chart', ['series' => [$totalIncome, $totalExpense]]);

        return view('livewire.home-livewire', [
            'records' => $records,
        ]);
    }
}