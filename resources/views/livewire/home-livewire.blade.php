<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Selamat Datang, <span class="text-primary">{{ $auth->name }}</span>!</h2>
        <a href="{{ route('auth.logout') }}" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right me-2"></i>Keluar
        </a>
    </div>

    <!-- Info Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="bi bi-arrow-up-circle-fill fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Pemasukan</h6>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-3 me-3">
                        <i class="bi bi-arrow-down-circle-fill fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Pengeluaran</h6>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-3 me-3">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Saldo Saat Ini</h6>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" wire:ignore>
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Pemasukan vs Pengeluaran (6 Bulan Terakhir)</h5>
                    <div id="chart-monthly"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0" wire:ignore>
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Distribusi Keuangan</h5>
                    <div id="chart-radial-distribution"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Records Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Table Header -->
            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <h5 class="card-title fw-semibold flex-grow-1 mb-0">Daftar Catatan Keuangan</h5>
                <div class="input-group" style="max-width: 250px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 bg-light" placeholder="Cari deskripsi..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn {{ $filterType === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}"
                        wire:click="$set('filterType', 'all')">Semua</button>
                    <button type="button" class="btn {{ $filterType === 'income' ? 'btn-success' : 'btn-outline-secondary' }}"
                        wire:click="$set('filterType', 'income')">Pemasukan</button>
                    <button type="button" class="btn {{ $filterType === 'expense' ? 'btn-danger' : 'btn-outline-secondary' }}"
                        wire:click="$set('filterType', 'expense')">Pengeluaran</button>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal" wire:click="mountAdd">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Catatan
                </button>
            </div>

            <!-- Records Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Tipe</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td>{{ $records->firstItem() + $loop->index }}</td>
                                <td>{{ $record->record_date->format('d M Y') }}</td>
                                <td>{!! $record->description !!}</td>
                                <td>
                                    @if ($record->type == 'income')
                                        <span class="badge text-bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span>
                                    @else
                                        <span class="badge text-bg-danger-subtle text-danger-emphasis rounded-pill">Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="text-end fw-medium">Rp {{ number_format($record->amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-secondary" wire:click="mountDetail({{ $record->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-outline-secondary" wire:click="mountEdit({{ $record->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-secondary" wire:click="mountDelete({{ $record->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                    Belum ada catatan keuangan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($records->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $records->links() }}
                </div>
            @endif
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

        // Inisialisasi Tooltip Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const chartIncomeSeries = @json($chartIncomeSeries);
        const chartExpenseSeries = @json($chartExpenseSeries);
        const chartDistributionSeries = @json($chartDistributionSeries);

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

        // --- Modern Chart Configurations ---

        // Monthly Chart
        var monthlyOptions = {
            series: [{
                name: 'Pemasukan',
                data: chartIncomeSeries
            }, {
                name: 'Pengeluaran',
                data: chartExpenseSeries
            }],
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            plotOptions: { 
                bar: { 
                    horizontal: false, 
                    columnWidth: '50%', 
                    borderRadius: 8,
                    borderRadiusApplication: 'end',
                } 
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: chartMonthsLabels },
            yaxis: { 
                labels: { 
                    formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val / 1000) + 'k'; } 
                } 
            },
            fill: { opacity: 1 },
            colors: ['#198754', '#dc3545'],
            grid: {
                borderColor: '#f1f1f1',
            },
            tooltip: { 
                y: { formatter: function (val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } } 
            }
        };

        var monthlyChart = new ApexCharts(document.querySelector("#chart-monthly"), monthlyOptions);
        monthlyChart.render();

        // Radial Distribution Chart
        var radialOptions = {
            series: chartDistributionSeries,
            chart: { type: 'radialBar', height: 350 },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: '70%',
                    },
                    dataLabels: {
                        name: { fontSize: '22px' },
                        value: { fontSize: '16px' },
                        total: {
                            show: true,
                            label: 'Pengeluaran',
                            formatter: function (w) {
                                return w.globals.series[1] + '%'
                            }
                        }
                    }
                }
            },
            labels: ['Pemasukan', 'Pengeluaran'],
            colors: ['#198754', '#dc3545']
        };

        var radialChart = new ApexCharts(document.querySelector("#chart-radial-distribution"), radialOptions);
        radialChart.render();

        @this.on('refresh-chart', ({ monthsLabels, incomeSeries, expenseSeries, distributionSeries }) => {
            monthlyChart.updateOptions({
                xaxis: {
                    categories: monthsLabels
                }
            });
            monthlyChart.updateSeries([
                { name: 'Pemasukan', data: incomeSeries },
                { name: 'Pengeluaran', data: expenseSeries }
            ]);
            radialChart.updateSeries(distributionSeries);

            // Re-inisialisasi tooltip setelah Livewire me-render ulang DOM
            // Ini penting agar tooltip pada data baru (misal: halaman paginasi berikutnya) bisa berfungsi
            setTimeout(() => {
                const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                newTooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
            }, 100);
        });
    });
</script>