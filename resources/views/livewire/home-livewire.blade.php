<div data-bs-theme="{{ $theme }}">
    @push('styles')
        <style>
            /* Tema Gelap */
            [data-bs-theme="dark"] .trix-content,
            [data-bs-theme="dark"] .trix-toolbar {
                background-color: #2b3035;
                color: #f8f9fa;
            }

            /* Tema Terang */
            [data-bs-theme="light"] .trix-content,
            [data-bs-theme="light"] .trix-toolbar {
                background-color: #ffffff;
                color: #212529;
            }

            [data-bs-theme="dark"] .trix-toolbar .trix-button-group {
                border-color: #495057;
            }

            [data-bs-theme="dark"] .trix-toolbar .trix-button {
                color: #f8f9fa;
            }

            [data-bs-theme="dark"] .trix-toolbar .trix-button.trix-active {
                background-color: #495057;
            }
        </style>
    @endpush
    {{-- Judul Halaman dan Sambutan --}}
    <div class="mb-4">
        <h1 class="display-5 fw-bold">Dashboard Keuangan</h1>
        <p class="lead">Selamat datang kembali, {{ auth()->user()->name }}. Kelola catatan keuangan Anda di sini.</p>
    </div>

    {{-- Filter Tanggal untuk Chart --}}
    <div class="card bg-dark-subtle shadow-sm mb-4 rounded-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-funnel-fill"></i> Filter Diagram</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="startDate" class="form-label">Dari Tanggal</label>
                    <input type="date" id="startDate" class="form-control form-control-dark" wire:model.live="startDate">
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label">Hingga Tanggal</label>
                    <input type="date" id="endDate" class="form-control form-control-dark" wire:model.live="endDate">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-secondary w-100" wire:click="resetDateFilter"><i class="bi bi-arrow-clockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card bg-dark-subtle mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Proporsi Keuangan</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore id="financial-pie-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-dark-subtle mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Perbandingan Keuangan</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore id="financial-bar-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Ringkasan Saldo --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-dark-subtle shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-arrow-up-circle text-success fs-1 me-3"></i>
                    <div>
                        <small class="text-muted">Total Pemasukan</small>
                        <h5 class="card-title mb-0 fw-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark-subtle shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-arrow-down-circle text-danger fs-1 me-3"></i>
                    <div>
                        <small class="text-muted">Total Pengeluaran</small>
                        <h5 class="card-title mb-0 fw-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark-subtle shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-wallet2 text-primary fs-1 me-3"></i>
                    <div>
                        <small class="text-muted">Saldo Akhir</small>
                        <h5 class="card-title mb-0 fw-bold text-primary">Rp {{ number_format($balance, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Catatan Keuangan --}}
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-2 mb-md-0">Catatan Keuangan</h5>
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                        <i class="bi bi-plus-circle"></i> Tambah Catatan
                    </button>
                    <x-theme-switcher />
                    <a href="{{ route('auth.logout') }}" class="btn btn-outline-danger ms-2">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterType">
                        <option value="">Semua Jenis</option>
                        <option value="1">Pemasukan</option>
                        <option value="0">Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <input type="search" class="form-control" placeholder="Cari berdasarkan judul..."
                        wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-dark">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td>{{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}</td>
                                <td>{{ $record->title }}</td>
                                <td>
                                    @if ($record->type == 1)
                                        <span class="badge bg-success">Pemasukan</span>
                                    @else
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($record->amount, 0, ',', '.') }}</td>
                                <td>{{ $record->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('app.todo.detail', ['todo_id' => $record->id]) }}" wire:navigate
                                    <a href="{{ route('app.app.todo.detail', ['todo_id' => $record->id]) }}" wire:navigate
                                        class="btn btn-sm btn-outline-info rounded-pill px-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                        wire:click="prepareEditTodo({{ $record->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        wire:click="prepareDeleteTodo({{ $record->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.todos.add')
    @include('components.modals.todos.edit')
    @include('components.modals.todos.delete')

    @script
    <script>
        // Script untuk Pengalih Tema
        document.addEventListener('livewire:initialized', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const htmlElement = document.documentElement;

            // Fungsi untuk menerapkan tema
            const applyTheme = (theme) => {
                htmlElement.setAttribute('data-bs-theme', theme);
                if (theme === 'dark') {
                    lightIcon.classList.add('d-none');
                    darkIcon.classList.remove('d-none');
                } else {
                    darkIcon.classList.add('d-none');
                    lightIcon.classList.remove('d-none');
                }
                localStorage.setItem('theme', theme);
            };

            // Cek tema saat halaman dimuat
            const storedTheme = localStorage.getItem('theme') || 'dark'; // Default ke dark
            applyTheme(storedTheme);

            // Event listener untuk tombol
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);

                // Beri tahu Livewire untuk me-render ulang dengan tema baru
                // Ini penting agar komponen yang dirender server (seperti pagination) ikut berubah
                @this.dispatch('theme-changed');
            });

            // Listener untuk memperbarui chart saat data berubah
            Livewire.on('update-charts', (event) => {
                const income = event.totalIncome || 0;
                const expense = event.totalExpense || 0;
                const newSeriesData = [Number(income), Number(expense)];

                pieChart.updateSeries(newSeriesData, true); // true untuk animasi
                barChart.updateSeries([{ data: newSeriesData }], true);
            });
        });

        document.addEventListener('livewire:initialized', () => {
            let pieChart;
            let barChart;

            // Fungsi untuk inisialisasi atau update pie chart
            function initPieChart(income, expense) {
                const seriesData = [Number(income) || 0, Number(expense) || 0];
                const chartOptions = {
                    series: seriesData,
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    labels: ['Pemasukan', 'Pengeluaran'],
                    colors: ['#198754', '#dc3545'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: '100%'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                // Hancurkan chart lama jika ada untuk menghindari memory leak
                if (pieChart) {
                    pieChart.destroy();
                }

                pieChart = new ApexCharts(document.querySelector("#financial-pie-chart"), chartOptions);
                pieChart.render();
            }

            // Fungsi untuk inisialisasi atau update bar chart
            function initBarChart(income, expense) {
                const barChartOptions = {
                    series: [{
                        name: 'Jumlah',
                        data: [Number(income) || 0, Number(expense) || 0]
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            distributed: true, // Warnai setiap bar secara berbeda
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#198754', '#dc3545'], // Green for income, Red for expense
                    xaxis: {
                        categories: ['Pemasukan', 'Pengeluaran'],
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    },
                    legend: {
                        show: false // Sembunyikan legenda karena sudah ada di label x-axis
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }
                };

                if (barChart) {
                    barChart.destroy();
                }

                barChart = new ApexCharts(document.querySelector("#financial-bar-chart"), barChartOptions);
                barChart.render();
            }

            initPieChart(@js($totalIncome), @js($totalExpense)); // Inisialisasi pertama kali
            initBarChart(@js($totalIncome), @js($totalExpense)); // Inisialisasi pertama kali

            Livewire.on('show-alert', (data) => { // This listener is global, no need to re-register on navigate
                const eventData = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: eventData.type,
                    title: eventData.title,
                    text: eventData.message,
                });
            });

            // Listener untuk memperbarui chart saat data berubah
            Livewire.on('update-charts', (event) => {
                const income = Array.isArray(event) ? event[0].totalIncome : event.totalIncome;
                const expense = Array.isArray(event) ? event[0].totalExpense : event.totalExpense;
                const newSeriesData = [Number(income) || 0, Number(expense) || 0];

                if (pieChart) {
                    pieChart.updateSeries(newSeriesData, true); // true untuk animasi
                    barChart.updateSeries([{ data: newSeriesData }], true);
                }
            });
        });

        let cleanupOpenModal, cleanupCloseModal;

        // Listener yang akan dijalankan ulang setiap kali navigasi SPA terjadi
        document.addEventListener("livewire:navigated", () => {
            // Hapus listener lama jika ada untuk menghindari duplikasi
            if (cleanupOpenModal) cleanupOpenModal();
            if (cleanupCloseModal) cleanupCloseModal();

            cleanupOpenModal = Livewire.on('open-modal', (id) => {
                const modalId = Array.isArray(id) ? id[0] : id;
                new bootstrap.Modal(document.getElementById(modalId)).show();
            });

            cleanupCloseModal = Livewire.on('close-modal', (id) => {
                const modalId = Array.isArray(id) ? id[0] : id;
                bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
            });
        });
    </script>
    @endscript
</div>