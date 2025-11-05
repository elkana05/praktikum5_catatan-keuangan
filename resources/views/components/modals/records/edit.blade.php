<form wire:submit.prevent="updateRecord">
    <div class="modal fade" tabindex="-1" id="editRecordModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Catatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input id="editDescription" type="hidden" wire:model="editDescription">
                        <trix-editor input="editDescription" class="trix-content" wire:ignore></trix-editor>
                        @error('editDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('trix-change', function (e) {
                            try {
                                const input = e.target.inputElement;
                                if (input && input.id === 'editDescription') {
                                    input.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            } catch (err) {
                                // ignore
                            }
                        });
                    </script>
                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            const trixEditor = document.querySelector("#editRecordModal trix-editor");

                            // Event untuk mengisi Trix editor saat modal edit dibuka
                            @this.on('trix-value-updated', (event) => {
                                trixEditor.editor.loadHTML(event.description);
                            });
                        });
                    </script>

                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" wire:model="editType">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                        @error('editType')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" step="0.01" class="form-control" wire:model="editAmount">
                        <small class="form-text text-muted">Maksimal: Rp 9.999.999.999.999 (13 digit)</small>
                        @error('editAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" wire:model="editRecordDate">
                        @error('editRecordDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti (Opsional)</label>
                        <input type="file" class="form-control" wire:model="editAttachment">
                        <div wire:loading wire:target="editAttachment">Mengunggah...</div>
                        @if ($editAttachment)
                            <p class="mt-2 mb-0">Pratinjau file baru:</p>
                            <img src="{{ $editAttachment->temporaryUrl() }}" class="img-fluid mt-1" style="max-height: 200px;">
                        @elseif ($existingAttachment)
                            <p class="mt-2 mb-0">File saat ini:</p>
                            <img src="{{ asset('storage/' . $existingAttachment) }}" class="img-fluid mt-1" style="max-height: 200px;">
                        @endif
                        @error('editAttachment')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </div>
        </div>
    </div>
</form>
