<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Hay, {{ $auth->name }}</h3>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div id="financial-chart" wire:ignore></div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian Judul</label>
                    <input type="text" wire:model.lazy="search" class="form-control"
                        placeholder="Cari berdasarkan judul...">
                </div>
                <div class="col-md-3">
                    <label for="filterType" class="form-label">Filter Jenis Catatan</label>
                    <select wire:model="filterType" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="1">Pemasukan</option>
                        <option value="0">Pengeluaran</option>
                    </select>
                </div>
            </div>
            <div class="d-flex mb-2">
                <div class="flex-fill">
                    <h3>Daftar Catatan Keuangan</h3>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                        Tambah Catatan
                    </button>
                </div>
            </div>
            <table class="table table-striped">
                <tr class="table-light">
                    <th>No</th>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Dibuat pada</th>
                    <th>Tindakan</th>
                </tr>
                @foreach ($records as $key => $record)
                    <tr>
                        <td>{{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}</td>
                        <td>{{ $record->title }}</td>
                        <td>
                            @if ($record->type)
                                <span class="badge bg-success">Pemasukan</span>
                            @else
                                <span class="badge bg-danger">Pengeluaran</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($record->add_todo_amount, 0, ',', '.') }}</td>
                        <td>{{ date('d F Y, H:i', strtotime($record->created_at)) }}</td>
                        <td>
                            <a href="{{ route('app.todos.detail', ['todo_id' => $record->id]) }}"
                                class="btn btn-sm btn-info">
                                Detail
                            </a>
                            <button wire:click="prepareEditTodo({{ $record->id }})" class="btn btn-sm btn-warning">
                                Edit
                            </button>
                            <button wire:click="prepareDeleteTodo({{ $record->id }})" class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if (sizeof($records) === 0)
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data catatan keuangan yang tersedia.</td>
                    </tr>
                @endif
            </table>
            <div class="mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.todos.add')
    @include('components.modals.todos.edit')
    @include('components.modals.todos.delete')
</div>