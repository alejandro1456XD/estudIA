<!DOCTYPE html>
<html lang="es" data-bs-theme="light"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - estudIA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body, .navbar, .card, .dropdown-menu {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    
    @include('partials.navbar')

    <main class="container mt-4">
        @yield('content')
    </main>

    
    @include('partials.footer')

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const htmlElement = document.documentElement;

            // Función para aplicar el tema y guardar preferencia
            const setTheme = (theme) => {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);

                // Cambiar iconos
                if (theme === 'dark') {
                    if(darkIcon) darkIcon.classList.add('d-none');
                    if(lightIcon) lightIcon.classList.remove('d-none');
                } else {
                    if(darkIcon) darkIcon.classList.remove('d-none');
                    if(lightIcon) lightIcon.classList.add('d-none');
                }
            };

            // 1. Verificar preferencia al cargar la página
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                setTheme('dark');
            } else {
                setTheme('light');
            }

            // 2. Evento Click en el botón
            if(toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>