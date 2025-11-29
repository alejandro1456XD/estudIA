<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-md border border-blue-100">

        <h2 class="text-3xl font-extrabold text-center mb-6 text-blue-600">
            Crear Cuenta
        </h2>

        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/register" class="space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label class="block mb-1 font-semibold text-gray-700">Nombre</label>
                <input 
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none"
                >
            </div>

            {{-- Email --}}
            <div>
                <label class="block mb-1 font-semibold text-gray-700">Email</label>
                <input 
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none"
                >
            </div>

            {{-- Contraseña --}}
            <div>
                <label class="block mb-1 font-semibold text-gray-700">Contraseña</label>
                <input 
                    type="password"
                    name="password"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none"
                >
            </div>

            {{-- Confirmación --}}
            <div>
                <label class="block mb-1 font-semibold text-gray-700">Confirmar Contraseña</label>
                <input 
                    type="password"
                    name="password_confirmation"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none"
                >
            </div>

            {{-- Botón --}}
            <button 
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-lg hover:bg-blue-700 transition active:scale-[0.97]">
                Registrarme
            </button>

        </form>

        <p class="text-center mt-6 text-gray-600">
            ¿Ya tienes cuenta?
            <a href="/login" class="text-blue-600 font-semibold hover:underline">
                Inicia Sesión
            </a>
        </p>

    </div>

</body>
</html>
