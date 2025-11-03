<!doctype html>
<html lang="id">

<head>
    {{-- Meta --}}
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Icon --}}
    <link rel="icon" href="/logo.png" type="image/x-icon" />

    {{-- Judul --}}
    <title>Laravel Todolist</title>

    {{-- Styles --}}
    @livewireStyles
    <!-- Trix editor styles -->
    <link rel="stylesheet" href="https://unpkg.com/trix/dist/trix.css">
    <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid">
        @yield('content')
    </div>

    {{-- Scripts --}}
    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Trix editor -->
    <script src="https://unpkg.com/trix/dist/trix.umd.min.js"></script>
    <script>
        document.addEventListener("livewire:init", () => {
            Livewire.on("closeModal", (data) => {
                const modalId = data[0]?.id || data.id;
                const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                if (modal) {
                    modal.hide();
                }
            });

            Livewire.on("showModal", (data) => {
                const modalId = data[0]?.id || data.id;
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
                if (modal) {
                    modal.show();
                }
            });

            Livewire.on('trix-reset', (data) => {
                const trixId = data[0]?.id || data.id;
                const editor = document.getElementById(trixId)?.editor;
                if (editor) editor.loadHTML('');
            });

            Livewire.on('swal', (data) => {
                const detail = data[0] || data;
                const opts = {
                    title: detail.title || '',
                    text: detail.text || '',
                    icon: detail.icon || undefined,
                    showConfirmButton: detail.showConfirmButton ?? true,
                };

                if (detail.toast) {
                    Swal.fire({
                        toast: true,
                        position: detail.position || 'top-end',
                        timer: detail.timer || 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        ...opts
                    });
                } else {
                    Swal.fire(opts);
                }
            });
        });
    </script>
    @livewireScripts
</body>

</html>