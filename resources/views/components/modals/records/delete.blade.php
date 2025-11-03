<form wire:submit.prevent="deleteRecord">
    <div class="modal fade" tabindex="-1" id="deleteRecordModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Catatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($deleteRecordDescription)
                        <div class="alert alert-danger">
                            <p>Apakah Anda yakin ingin menghapus catatan berikut?</p>
                            <div class="mt-2 mb-3"><strong>"{{ $deleteRecordDescription }}"</strong></div>
                            <p>Untuk konfirmasi, ketik deskripsi catatan yang akan dihapus di bawah ini:</p>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" wire:model.live="deleteRecordConfirmDescription" placeholder="Ketik deskripsi catatan di sini...">
                            @error('deleteRecordConfirmDescription')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Catatan tidak ditemukan atau tidak memiliki akses.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</form>
