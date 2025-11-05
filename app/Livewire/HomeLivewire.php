<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class HomeLivewire extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterDate = '';
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        $query = Todo::where('user_id', $this->auth->id);

        // Pencarian berdasarkan deskripsi
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Filter berdasarkan tipe (pemasukan/pengeluaran)
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        // Filter berdasarkan tanggal
        if ($this->filterDate) {
            $query->whereDate('transaction_date', $this->filterDate);
        }

        $todos = $query->orderBy('transaction_date', 'desc')->paginate(20);
        $data = [
            'todos' => $todos
        ];
        return view('livewire.home-livewire', $data);
    }

    // Add Todo
    public $addTodoType = 'expense';
    public $addTodoAmount;
    public $addTodoDescription;
    public $addTodoDate;

    public function addTodo()
    {
        $this->validate([
            'addTodoType' => 'required|in:income,expense',
            'addTodoAmount' => 'required|numeric|min:0',
            'addTodoDescription' => 'required|string',
            'addTodoDate' => 'required|date',
        ]);

        // Simpan todo ke database
        Todo::create([
            'user_id' => $this->auth->id,
            'description' => $this->addTodoDescription,
            'amount' => $this->addTodoAmount,
            'type' => $this->addTodoType,
            'transaction_date' => $this->addTodoDate,
            'is_finished' => true, // Anggap saja ini sebagai penanda transaksi selesai
        ]);

        // Reset the form
        $this->reset(['addTodoType', 'addTodoAmount', 'addTodoDescription', 'addTodoDate']);

        // Tutup modal
        $this->dispatch('closeModal', id: 'addTodoModal');
        // Tampilkan notifikasi sukses
        $this->dispatch('swal:success', [
            'message' => 'Catatan keuangan berhasil ditambahkan.'
        ]);
    }

    // Edit Todo
    public $editTodoId;
    public $editTodoType;
    public $editTodoAmount;
    public $editTodoDescription;
    public $editTodoDate;

    public function prepareEditTodo($id)
    {
        // Pastikan todo milik user yang sedang login
        $todo = Todo::where('id', $id)
            ->where('user_id', $this->auth->id)->first();
        if (!$todo) {
            return;
        }

        $this->editTodoId = $todo->id;
        $this->editTodoType = $todo->type;
        $this->editTodoAmount = $todo->amount;
        $this->editTodoDescription = $todo->description;
        $this->editTodoDate = $todo->transaction_date;

        $this->dispatch('showModal', id: 'editTodoModal');
    }

    public function editTodo()
    {
        $this->validate([
            'editTodoType' => 'required|in:income,expense',
            'editTodoAmount' => 'required|numeric|min:0',
            'editTodoDescription' => 'required|string',
            'editTodoDate' => 'required|date',
        ]);

        // Pastikan todo milik user yang sedang login
        $todo = Todo::where('id', $this->editTodoId)
            ->where('user_id', $this->auth->id)->first();
        if (!$todo) {
            $this->addError('editTodoTitle', 'Data todo tidak tersedia.');
            return;
        }
        $todo->type = $this->editTodoType;
        $todo->amount = $this->editTodoAmount;
        $todo->description = $this->editTodoDescription;
        $todo->transaction_date = $this->editTodoDate;
        $todo->save();

        $this->reset(['editTodoId', 'editTodoType', 'editTodoAmount', 'editTodoDescription', 'editTodoDate']);
        $this->dispatch('closeModal', id: 'editTodoModal');
        // Tampilkan notifikasi sukses
        $this->dispatch('swal:success', [
            'message' => 'Catatan keuangan berhasil diubah.'
        ]);
    }

    // Delete Todo
    public $deleteTodoId;
    public $deleteTodoTitle;
    public $deleteTodoConfirmTitle;

    public function prepareDeleteTodo($id)
    {
        // Pastikan todo milik user yang sedang login
        $todo = Todo::where('id', $id)
            ->where('user_id', $this->auth->id)->first();
        if (!$todo) {
            return;
        }

        $this->deleteTodoId = $todo->id;
        $this->deleteTodoTitle = "Rp " . number_format($todo->amount, 0, ',', '.') . " - " . $todo->description;
        $this->dispatch('showModal', id: 'deleteTodoModal');
    }

    public function deleteTodo()
    {
        if ($this->deleteTodoConfirmTitle !== 'HAPUS') {
            $this->addError('deleteTodoConfirmTitle', 'Ketik "HAPUS" untuk konfirmasi.');
            $this->dispatch('swal:error', [
                'message' => 'Konfirmasi penghapusan tidak sesuai.'
            ]);
            return;
        }

        // Pastikan todo milik user yang sedang login sebelum menghapus
        $todo = Todo::where('id', $this->deleteTodoId)
            ->where('user_id', $this->auth->id)->first();

        if ($todo) {
            // Hapus gambar bukti jika ada
            if ($todo->receipt_image && Storage::disk('public')->exists($todo->receipt_image)) {
                Storage::disk('public')->delete($todo->receipt_image);
            }
            $todo->delete();
        }

        $this->reset(['deleteTodoId', 'deleteTodoTitle', 'deleteTodoConfirmTitle']);
        $this->dispatch('closeModal', id: 'deleteTodoModal');
        // Tampilkan notifikasi sukses
        $this->dispatch('swal:success', [
            'message' => 'Catatan keuangan berhasil dihapus.'
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterType', 'filterDate']);
    }
}
