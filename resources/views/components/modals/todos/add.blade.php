<form wire:submit.prevent="addTodo">
    <div class="modal fade" tabindex="-1" id="addTodoModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Todo</h5>
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
                        <label class="form-label">Deskripsi</label>
                        <input id="addTodoDescription" type="hidden" wire:model="addTodoDescription">
                        <trix-editor input="addTodoDescription" class="trix-content" wire:ignore></trix-editor>
                        @error('addTodoDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <script>
                        document.addEventListener('trix-change', function (e) {
                            try {
                                const input = e.target.inputElement;
                                if (input && input.id === 'addTodoDescription') {
                                    // Trigger input event so Livewire picks up the change
                                    input.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            } catch (err) {
                                // ignore
                            }
                        });
                    </script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>