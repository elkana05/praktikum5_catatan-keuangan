<form wire:submit.prevent="{{ $editRecordId ? 'updateRecord' : 'addRecord' }}">
    <div class="modal fade" id="formModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header" style="background: var(--primary-gradient); color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        @if ($editRecordId)
                            <i class="bi bi-pencil-square me-2"></i>Ubah Catatan
                        @else
                            <i class="bi bi-plus-circle me-2"></i>Tambah Catatan
                        @endif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" wire:model="editRecordId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" class="form-control" wire:model="{{ $editRecordId ? 'editRecordDate' : 'addRecordDate' }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Transaksi</label>
                        <select class="form-select" wire:model="{{ $editRecordId ? 'editType' : 'addType' }}" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="{{ $editRecordId ? 'editAmount' : 'addAmount' }}" 
                               placeholder="Masukkan jumlah..." required min="0">
                    </div>
                    
                    <div class="mb-3" wire:ignore>
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <input id="recordDescription" type="hidden" wire:model.defer="{{ $editRecordId ? 'editDescription' : 'addDescription' }}">
                        <trix-editor input="recordDescription"></trix-editor>
                    </div>

                    <script>
                        document.addEventListener('trix-change', function (e) {
                            @this.set('{{ $editRecordId ? 'editDescription' : 'addDescription' }}', e.target.value)
                        });

                        Livewire.on('trix-input', (event) => {
                            const editor = document.querySelector("trix-editor[input='recordDescription']").editor;
                            if (editor.element.value !== event.value) {
                                editor.loadHTML(event.value || '');
                            }
                        });
                    </script>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="bi bi-save me-2"></i>
                        {{ $editRecordId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>