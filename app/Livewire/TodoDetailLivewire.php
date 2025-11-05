<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TodoDetailLivewire extends Component
{
    use WithFileUploads;

    public $todo;
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();

        $todo_id = request()->route('todo_id');
        // Pastikan todo ada dan dimiliki oleh user yang sedang login
        $targetTodo = Todo::where('id', 'like', $todo_id)
            ->where('user_id', $this->auth->id)
            ->first();
        if (!$targetTodo) {
            return redirect()->route('app.home');
        }

        $this->todo = $targetTodo;
    }

    public function render()
    {
        return view('livewire.todo-detail-livewire');
    }

    // Ubah Cover Todo
    public $editCoverTodoFile;

    public function editCoverTodo()
    {
        $this->validate([
            'editCoverTodoFile' => 'required|image|max:2048',  // 2MB Max
        ]);

        if ($this->editCoverTodoFile) {
            // Hapus cover lama jika ada
            if ($this->todo->receipt_image && Storage::disk('public')->exists($this->todo->receipt_image)) {
                Storage::disk('public')->delete($this->todo->receipt_image);
            }

            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editCoverTodoFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $path = $this->editCoverTodoFile->storeAs('receipts', $filename, 'public');
            $this->todo->receipt_image = $path;
            $this->todo->save();
        }

        $this->reset(['editCoverTodoFile']);

        $this->dispatch('closeModal', id: 'editCoverTodoModal');
        // Tampilkan notifikasi sukses
        $this->dispatch('swal:success', [
            'message' => 'Bukti transaksi berhasil diubah.'
        ]);
    }
}