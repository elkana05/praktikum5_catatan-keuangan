<form wire:submit.prevent="editTodo">
    <div class="modal fade" tabindex="-1" id="editTodoModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
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
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="editTodoAmount" placeholder="Contoh: 50000">
                        @error('editTodoAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3" wire:ignore>
                        <label class="form-label">Deskripsi</label>
                        <input id="edit_description" type="hidden" name="content" value="{{ $editTodoDescription }}">
                        <trix-editor input="edit_description"></trix-editor>
                        @error('editTodoDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            const trixEditor = document.getElementById('edit_description');
            trixEditor.addEventListener('trix-change', (event) => {
                @this.set('editTodoDescription', event.target.value);
            });
        });
    </script>
@endpush
