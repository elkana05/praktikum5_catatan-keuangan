<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Catatan Keuangan' }}</title>

    {{-- Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>[data-trix-button-group="file-tools"] { display: none !important; }</style>
</head>

<body>
    <main class="py-5">
        <div class="container">
            {{ $slot }}
        </div>
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('show-alert', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: data.type,
                    title: data.title,
                    text: data.message,
                });
            });

            // Inisialisasi ApexChart
            const chartOptions = {
                series: [0, 0],
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: ['Pemasukan', 'Pengeluaran'],
                colors: ['#198754', '#dc3545'],
                title: {
                    text: 'Statistik Keuangan',
                    align: 'center'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#financial-chart"), chartOptions);
            chart.render();

            // Listener untuk update chart
            @this.on('update-chart', (event) => {
                const data = event[0];
                chart.updateSeries(data.series);
            });
        });
    </script>
</body>
</html>