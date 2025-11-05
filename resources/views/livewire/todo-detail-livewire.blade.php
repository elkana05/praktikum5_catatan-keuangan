<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <a href="{{ route('app.home') }}" class="text-decoration-none">
                    <small class="text-muted">
                        &lt; Kembali
                    </small>
                </a>
                <h3>
                    Detail Catatan Keuangan
                    @if ($todo->type == 'income')
                        <small class="badge bg-success">Pemasukan</small>
                    @else
                        <small class="badge bg-danger">Pengeluaran</small>
                    @endif
                </h3>
                <p class="text-muted">Tanggal: {{ date('d F Y', strtotime($todo->transaction_date)) }}</p>
            </div>
            <div>
                <button class="btn btn-warning" data-bs-target="#editCoverTodoModal" data-bs-toggle="modal">
                    Ubah Bukti
                </button>
            </div>
        </div>
        <div class="card-body">
            <h4>Jumlah: Rp {{ number_format($todo->amount, 0, ',', '.') }}</h4>
            <p style="font-size: 18px;">Deskripsi: {{ $todo->description }}</p>
            <hr>
            <h5>Bukti Transaksi</h5>
            @if ($todo->receipt_image)
                <img src="{{ asset('storage/' . $todo->receipt_image) }}" alt="Bukti Transaksi" style="max-width: 100%;">
                <hr>
            @endif
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.todos.edit-cover')
</div>