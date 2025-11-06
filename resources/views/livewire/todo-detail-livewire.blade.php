<div class="mt-3" data-bs-theme="{{ $theme }}">
    <div class="card">
        <div class="card-header">
            <a href="{{ route('app.home') }}" class="text-decoration-none text-muted" wire:navigate>
                &lt; Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <h2 class="card-title">{{ $todo->title }}</h2>
            <div class="mb-3">
                @if ($todo->type == 1)
                    <span class="badge bg-success fs-6">Pemasukan</span>
                @else
                    <span class="badge bg-danger fs-6">Pengeluaran</span>
                @endif
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Jumlah:</strong></p>
                    <h4>Rp {{ number_format($todo->amount, 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Tanggal Dibuat:</strong></p>
                    <h4>{{ $todo->created_at->format('d F Y') }}</h4>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Deskripsi:</h5>
            <div class="trix-content">{!! $todo->description !!}</div>

            @if ($todo->cover)
                <div class="mt-4">
                    <p>
                        <strong>Lampiran:</strong>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#viewCoverModal">
                            Lihat Bukti
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>
    <x-modals.todos.view-cover :coverUrl="$todo->cover" />
</div>