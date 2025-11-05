<div class="card">
    <div class="card-header">
        <h5>Statistik Keuangan (6 Bulan Terakhir)</h5>
    </div>
    <div class="card-body">
        <div wire:ignore id="financialChart"></div>
    </div>

    @script
    <script>
        document.addEventListener('livewire:init', () => {
            const chartData = @json($chartData);

            const options = {
                series: chartData.series,
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false,
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        borderRadius: 5,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: chartData.categories,
                },
                yaxis: {
                    title: {
                        text: 'Jumlah (Rp)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
                colors: ['#28a745', '#dc3545'] // Hijau untuk pemasukan, Merah untuk pengeluaran
            };

            const chart = new ApexCharts(document.querySelector("#financialChart"), options);
            chart.render();
        });
    </script>
    @endscript
</div>