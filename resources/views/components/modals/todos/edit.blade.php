<form wire:submit.prevent="editTodo">
    <div class="modal fade" tabindex="-1" id="editTodoModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title">Ubah Todo</h5>
                    <h5 class="modal-title">Ubah Catatan Keuangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" wire:model="editTodoType">
                                <option value="expense">Pengeluaran</option>
                                <option value="income">Pemasukan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" wire:model="editTodoDate">
                            @error('editTodoDate') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" wire:model="editTodoTitle">
                        @error('editTodoTitle')
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="editTodoAmount" placeholder="Contoh: 50000">
                        @error('editTodoAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sudah Selesai?</label>
                        <select class="form-select" wire:model="editTodoIsFinished">
                            <option value="1" {{ $editTodoIsFinished ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ !$editTodoIsFinished ? 'selected' : '' }}>Tidak</option>
                            @error('editTodoIsFinished')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" rows="4" wire:model="editTodoDescription"></textarea>
                        <x-trix-input id="edit_description" wire:model.live="editTodoDescription" />
                        @error('editTodoDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</form>
