<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
        
        <h2 class="text-3xl font-bold text-center mb-6 text-blue-600">Iniciar Sesión</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-600 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <label class="block mb-2 font-semibold">Email</label>
            <input 
                type="email" 
                name="email" 
                class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300 mb-4"
            >

            <label class="block mb-2 font-semibold">Contraseña</label>
            <input 
                type="password" 
                name="password" 
                class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300 mb-6"
            >

            <button 
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Entrar
            </button>
        </form>

        <p class="text-center mt-4 text-gray-600">
            ¿No tienes cuenta? 
            <a href="/register" class="text-blue-600 font-semibold hover:underline">Regístrate</a>
        </p>

    </div>

</body>
</html>
