<?php

namespace App\Livewire\Forms; // Namespace ini sudah benar

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Form;

class EditTodoForm extends Form
{
    public $todoId;

    #[Rule('required|string|max:255', attribute: 'Judul')]
    public $title = '';

    #[Rule('required|numeric', attribute: 'Jumlah (Nominal)')]
    public $amount = 0;

    #[Rule('required|boolean', attribute: 'Jenis Catatan')]
    public $type = 0;

    #[Rule('nullable|string', attribute: 'Deskripsi')]
    public $description = '';

    // Properti untuk menyimpan path cover lama
    public $oldCover;

    // Properti untuk file cover baru yang diunggah
    #[Rule('nullable|image|max:2048', attribute: 'Bukti Baru')]
    public $newCover;

    #[Rule('required|date', attribute: 'Tanggal')]
    public $created_at = '';

    public function reset(...$properties)
    {
        parent::reset(...$properties);
        $this->type = 0; // Kembalikan ke default
        $this->newCover = null; // Pastikan file baru di-reset
        $this->oldCover = null; // Pastikan cover lama di-reset
    }
}