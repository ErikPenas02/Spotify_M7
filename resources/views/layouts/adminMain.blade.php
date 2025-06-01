<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - @yield('title', 'MySpotify')</title>

    {{-- Bootstrap (si lo sigues usando) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tu hoja de estilos personalizada --}}
    <link rel="stylesheet" href="{{ asset('css/layouts/cruds.css') }}">
</head>
<body>

<div class="d-flex">
    {{-- Sidebar --}}
    <nav class="sidebar p-3">
        <h4 class="text-center mb-4">Admin</h4>
        <a href="{{-- route('admin.albumes.index') --}}">Álbumes</a>
        <a href="{{-- route('admin.canciones.index') --}}">Canciones</a>
        <a href="{{-- route('admin.usuarios.index') --}}">Usuarios</a>
        <hr>
        <a href="{{-- route('logout') --}}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Cerrar sesión
        </a>
        <form id="logout-form" action="{{-- route('logout') --}}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>

    {{-- Contenido --}}
    <div class="flex-grow-1 p-4">
        @yield('content')
    </div>
</div>

{{-- JS de Bootstrap opcional --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
