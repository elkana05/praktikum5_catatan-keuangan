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
            <!-- Info Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card p-3 text-white bg-success">
                        <h6>Total Pemasukan</h6>
                        <h5>Rp {{ number_format($totalIncome, 2, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 text-white bg-danger">
                        <h6>Total Pengeluaran</h6>
                        <h5>Rp {{ number_format($totalExpense, 2, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 text-white bg-info">
                        <h6>Saldo Saat Ini</h6>
                        <h5>Rp {{ number_format($balance, 2, ',', '.') }}</h5>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="card p-2" wire:ignore>
                        <h6 class="mb-2">Pemasukan vs Pengeluaran (6 bulan terakhir)</h6>
                        <div id="chart-monthly"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-2" wire:ignore>
                        <h6 class="mb-2">Distribusi Total</h6>
                        <div id="chart-donut"></div>
                    </div>
                </div>
            </div>

            <!-- Table Header -->
            <div class="d-flex mb-2 align-items-center">
                <div class="flex-fill">
                    <h3>Daftar Catatan Keuangan</h3>
                </div>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 200px;">
                        <input type="text" class="form-control" placeholder="Cari deskripsi..." 
                               wire:model.live.debounce.300ms="search">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                    <div class="btn-group" role="group" aria-label="Filter Tipe">
                        <button type="button" class="btn {{ $filterType === 'all' ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="$set('filterType', 'all')">
                            Semua
                        </button>
                        <button type="button" class="btn {{ $filterType === 'income' ? 'btn-success' : 'btn-outline-success' }}"
                            wire:click="$set('filterType', 'income')">
                            Pemasukan
                        </button>
                        <button type="button" class="btn {{ $filterType === 'expense' ? 'btn-danger' : 'btn-outline-danger' }}"
                            wire:click="$set('filterType', 'expense')">
                            Pengeluaran
                        </button>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal" wire:click="mountAdd">
                        Tambah Catatan
                    </button>
                </div>
            </div>

            <!-- Records Table -->
            <table class="table table-striped">
                <tr class="table-light">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $records->firstItem() + $loop->index }}</td>
                        <td>{{ $record->record_date->format('d F Y') }}</td>
                        <td>{!! $record->description !!}</td>
                        <td>
                            @if ($record->type == 'income')
                                <span class="badge bg-success">Pemasukan</span>
                            @else
                                <span class="badge bg-danger">Pengeluaran</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($record->amount, 2, ',', '.') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" wire:click="mountDetail({{ $record->id }})">
                                    Detail
                                </button>
                                <button class="btn btn-sm btn-primary" wire:click="mountEdit({{ $record->id }})">
                                    Ubah
                                </button>
                                <button class="btn btn-sm btn-danger" wire:click="mountDelete({{ $record->id }})">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada catatan keuangan yang tersedia.</td>
                    </tr>
                @endforelse
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.records.add')
    @include('components.modals.records.edit')
    @include('components.modals.records.detail')
    @include('components.modals.records.delete')
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        const chartMonthsLabels = @json($chartMonthsLabels);
        const chartIncomeSeries = @json($chartIncomeSeries);
        const chartExpenseSeries = @json($chartExpenseSeries);
        const chartTotalsSeries = @json($chartTotalsSeries);

        // Event listener untuk menutup modal dan mereset state
        ['addRecordModal', 'editRecordModal', 'detailRecordModal', 'deleteRecordModal'].forEach(id => {
            const modalEl = document.getElementById(id);
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    // Hanya kirim event jika komponen masih ada di halaman.
                    // $wire is a JS proxy for the component, so we can check if it's still there.
                    if (Livewire.find($wire.id)) {
                        $wire.dispatch('modalClosed', { modalId: id });
                    }
                });
            }
        });

        // Monthly Chart
        var monthlyOptions = {
            series: [{
                name: 'Pemasukan',
                data: chartIncomeSeries
            }, {
                name: 'Pengeluaran',
                data: chartExpenseSeries
            }],
            chart: { type: 'bar', height: 320 },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: chartMonthsLabels },
            yaxis: { labels: { formatter: function (val) { return new Intl.NumberFormat('id-ID').format(val); } } },
            colors: ['#28a745', '#dc3545'],
            tooltip: { y: { formatter: function (val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } } }
        };

        var monthlyChart = new ApexCharts(document.querySelector("#chart-monthly"), monthlyOptions);
        monthlyChart.render();

        // Donut Chart
        var donutOptions = {
            series: chartTotalsSeries,
            chart: { type: 'donut', height: 320 },
            labels: ['Pemasukan', 'Pengeluaran'],
            colors: ['#28a745', '#dc3545'],
            tooltip: { y: { formatter: function (val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } } }
        };

        var donutChart = new ApexCharts(document.querySelector("#chart-donut"), donutOptions);
        donutChart.render();

        @this.on('refresh-chart', ({ monthsLabels, incomeSeries, expenseSeries, totalsSeries }) => {
            monthlyChart.updateOptions({
                xaxis: {
                    categories: monthsLabels
                }
            });
            monthlyChart.updateSeries([
                { name: 'Pemasukan', data: incomeSeries },
                { name: 'Pengeluaran', data: expenseSeries }
            ]);
            donutChart.updateSeries(totalsSeries);
        });
    });
</script>