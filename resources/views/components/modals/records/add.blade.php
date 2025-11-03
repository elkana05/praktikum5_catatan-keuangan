<form wire:submit.prevent="addRecord">
    <div class="modal fade" tabindex="-1" id="addRecordModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Keuangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input id="addDescription" type="hidden" wire:model="addDescription">
                        <trix-editor input="addDescription" class="trix-content" wire:ignore></trix-editor>
                        @error('addDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('trix-change', function (e) {
                            try {
                                const input = e.target.inputElement;
                                if (input && input.id === 'addDescription') {
                                    input.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            } catch (err) {
                                // ignore
                            }
                        });

                    </script>

                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" wire:model="addType">
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                        @error('addType')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" step="0.01" class="form-control" wire:model="addAmount" placeholder="Contoh: 5000000">
                        <small class="form-text text-muted">Maksimal: Rp 9.999.999.999.999 (13 digit)</small>
                        @error('addAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" wire:model="addRecordDate">
                        @error('addRecordDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti (Opsional)</label>
                        <input type="file" class="form-control" wire:model="addAttachment">
                        <div wire:loading wire:target="addAttachment">Mengunggah...</div>
                        @if ($addAttachment)
                            <img src="{{ $addAttachment->temporaryUrl() }}" class="img-fluid mt-2" style="max-height: 200px;">
                        @endif
                        @error('addAttachment')
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
