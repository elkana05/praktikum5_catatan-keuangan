<div>
    <div class="header-section" style="background: var(--primary-gradient); color: white; padding: 2rem 0; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-wallet2 me-2"></i>Dashboard Keuangan
                    </h2>
                    <p class="mb-0 opacity-75">Selamat datang, {{ $auth->name }}! Kelola keuangan Anda dengan mudah.</p>
                </div>
                <a href="{{ route('auth.logout') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4 animate-fade-in" style="animation-delay: 0.1s">
                <div class="card stat-card income shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box success me-3">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Total Pemasukan</h6>
                                <h3 class="fw-bold mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in" style="animation-delay: 0.2s">
                <div class="card stat-card expense shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box danger me-3">
                                <i class="bi bi-arrow-down-circle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Total Pengeluaran</h6>
                                <h3 class="fw-bold mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in" style="animation-delay: 0.3s">
                <div class="card stat-card balance shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box info me-3">
                                <i class="bi bi-piggy-bank-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Saldo Saat Ini</h6>
                                <h3 class="fw-bold mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4" wire:ignore>
            <div class="col-lg-8 animate-fade-in" style="animation-delay: 0.4s">
                <div class="card chart-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                            Pemasukan vs Pengeluaran (6 Bulan Terakhir)
                        </h5>
                        <div id="chartMonthly"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 animate-fade-in" style="animation-delay: 0.5s">
                <div class="card chart-card h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-pie-chart-fill me-2 text-primary"></i>
                            Distribusi Keuangan
                        </h5>
                        <div id="chartRadial"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card table-card animate-fade-in" style="animation-delay: 0.6s">
            <div class="card-body p-4">
                <!-- Toolbar -->
                <div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
                    <h5 class="fw-bold flex-grow-1 mb-0">
                        <i class="bi bi-journal-text me-2 text-primary"></i>
                        Daftar Catatan Keuangan
                    </h5>
                    
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Cari deskripsi..." wire:model.live.debounce.300ms="search">
                    </div>
                    
                    <div class="btn-group" role="group">
                        <button type="button" class="btn {{ $filterType === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filterType', 'all')">
                            <i class="bi bi-list-ul me-1"></i>Semua
                        </button>
                        <button type="button" class="btn {{ $filterType === 'income' ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="$set('filterType', 'income')">
                            <i class="bi bi-arrow-up me-1"></i>Pemasukan
                        </button>
                        <button type="button" class="btn {{ $filterType === 'expense' ? 'btn-danger' : 'btn-outline-secondary' }}" wire:click="$set('filterType', 'expense')">
                            <i class="bi bi-arrow-down me-1"></i>Pengeluaran
                        </button>
                    </div>
                    
                    <button class="btn btn-gradient-primary btn-lg" wire:click="mountAdd">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Catatan
                    </button>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="40%">Deskripsi</th>
                                <th width="12%">Tipe</th>
                                <th width="15%" class="text-end">Jumlah</th>
                                <th width="16%" class="text-center">Aksi</th>
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
                                            <span class="badge rounded-pill text-bg-success">Pemasukan</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($record->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info action-btn" wire:click="mountDetail({{ $record->id }})" data-bs-toggle="tooltip" title="Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary action-btn" wire:click="mountEdit({{ $record->id }})" data-bs-toggle="tooltip" title="Ubah">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger action-btn" wire:click="mountDelete({{ $record->id }})" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center py-5">
                                            <i class="bi bi-journal-x" style="font-size: 4rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">Belum Ada Data</h5>
                                            <p class="text-muted">Silakan tambahkan catatan keuangan pertama Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($records->hasPages())
                    <div class="d-flex justify-content-end mt-4">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- Menggunakan satu form modal untuk add dan edit --}}
    @include('components.modals.records.form')
    @include('components.modals.records.detail')
    @include('components.modals.records.delete')
</div>

@script
<script>
    document.addEventListener('livewire:initialized', function () {
        // Inisialisasi Tooltip Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // --- Chart Configurations ---
        const monthlyOptions = {
            series: [{
                name: 'Pemasukan',
                data: @json($chartIncomeSeries)
            }, {
                name: 'Pengeluaran',
                data: @json($chartExpenseSeries)
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 10,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: @json($chartMonthsLabels) },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                    }
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    opacityFrom: 0.85,
                    opacityTo: 0.55,
                }
            },
            colors: ['#00b09b', '#f85032'],
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        const monthlyChart = new ApexCharts(document.querySelector("#chartMonthly"), monthlyOptions);
        monthlyChart.render();

        const radialOptions = {
            series: @json($chartDistributionSeries),
            chart: {
                type: 'radialBar',
                height: 350,
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            plotOptions: {
                radialBar: {
                    hollow: { size: '65%' },
                    track: { background: '#f1f1f1' },
                    dataLabels: {
                        name: { fontSize: '18px', fontWeight: 600 },
                        value: {
                            fontSize: '16px',
                            formatter: function(val) { return val.toFixed(1) + '%'; }
                        },
                        total: {
                            show: true,
                            label: 'Pengeluaran',
                            fontSize: '14px',
                            formatter: function(w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                const expensePercent = (w.globals.series[1] / total) * 100;
                                return (isFinite(expensePercent) ? expensePercent.toFixed(1) : '0.0') + '%';
                            }
                        }
                    }
                }
            },
            labels: ['Pemasukan', 'Pengeluaran'],
            colors: ['#00b09b', '#f85032'],
            legend: { show: true, position: 'bottom', fontSize: '14px', fontWeight: 500 }
        };

        const radialChart = new ApexCharts(document.querySelector("#chartRadial"), radialOptions);
        radialChart.render();

        // Listener untuk memperbarui chart
        @this.on('refresh-chart', (event) => {
            const { monthsLabels, incomeSeries, expenseSeries, distributionSeries } = event;

            monthlyChart.updateOptions({
                xaxis: { categories: monthsLabels }
            });
            monthlyChart.updateSeries([
                { name: 'Pemasukan', data: incomeSeries },
                { name: 'Pengeluaran', data: expenseSeries }
            ]);
            radialChart.updateSeries(distributionSeries);

            // Re-inisialisasi tooltip
            setTimeout(() => {
                const newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                newTooltipTriggerList.forEach(el => {
                    const tooltip = bootstrap.Tooltip.getInstance(el);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                    new bootstrap.Tooltip(el);
                });
            }, 200);
        });
    });
</script>
@endscript