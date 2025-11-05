<form wire:submit.prevent="addTodo">
    <div class="modal fade" tabindex="-1" id="addTodoModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Keuangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" wire:model="addTodoTitle">
                        @error('addTodoTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Nominal)</label>
                        <input type="number" class="form-control" placeholder="Contoh: 50000" wire:model="addTodoAmount">
                        @error('addTodoAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Catatan</label>
                        <select class="form-select" wire:model="addTodoType">
                            <option value="0">Pengeluaran</option>
                            <option value="1">Pemasukan</option>
                        </select>
                        @error('addTodoType')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <x-trix-editor id="add_todo_description" wire:model="addTodoDescription" />
                        @error('addTodoDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>