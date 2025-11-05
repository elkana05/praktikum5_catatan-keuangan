<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Halo, {{ $auth->name }}</h3>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
        <div class="card-body">
            {{-- Statistik Grafik --}}
            <div class="mb-4">
                @livewire('financial-chart-livewire')
            </div>

            <div class="d-flex mb-2">
                <div class="flex-fill">
                    <h3>Catatan Keuangan</h3>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                        Tambah Catatan
                    </button>
                </div>
            </div>

            {{-- Filter dan Pencarian --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari deskripsi...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterType" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="filterDate" class="form-control">
                </div>
                <div class="col-md-3 text-end">
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset Filter</button>
                </div>
            </div>

            <table class="table table-striped">
                <tr class="table-light">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                    <th>Tipe</th>
                    <th>Tindakan</th>
                </tr>
                @foreach ($todos as $key => $todo)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ date('d F Y', strtotime($todo->transaction_date)) }}</td>
                        <td>{{ $todo->description }}</td>
                        <td>Rp {{ number_format($todo->amount, 0, ',', '.') }}</td>
                        <td>
                            @if ($todo->type == 'income')
                                <span class="badge bg-success">Pemasukan</span>
                            @else
                                <span class="badge bg-danger">Pengeluaran</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('app.todos.detail', ['todo_id' => $todo->id]) }}"
                                class="btn btn-sm btn-info">
                                Lihat
                            </a>
                            <button wire:click="prepareEditTodo({{ $todo->id }})" class="btn btn-sm btn-warning">
                                Ubah
                            </button>
                            <button wire:click="prepareDeleteTodo({{ $todo->id }})" class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if ($todos->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">Belum ada catatan keuangan yang tersedia.</td>
                    </tr>
                @endif
            </table>
            <div class="mt-3">
                {{ $todos->links() }}
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.todos.add')
    @include('components.modals.todos.edit')
    @include('components.modals.todos.delete')
</div>