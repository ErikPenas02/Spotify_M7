@extends('layouts.adminMain')

@section('title', 'Listado de Canciones')

@section('content')
<div class="container mt-4">
    <h1>Gestión de Canciones</h1>

    <div class="row my-3">
        <div class="col-md-4">
            <input type="text" id="buscarCancion" class="form-control" placeholder="Buscar por título...">
        </div>
        <div class="col-md-2">
            <button id="btnBuscar" class="btn btn-primary">Buscar</button>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dashboard.canciones.create') }}" class="btn btn-success">+ Nueva Canción</a>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="tablaCanciones">
        <thead>
            <tr>
                <th>Título</th>
                <th>Duración</th>
                <th>Álbum</th>
                <th>Artista Principal</th>
                <th>Colaboradores</th>
                <th>Géneros</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="bodyCanciones">
            <tr><td colspan="7" class="text-center">Cargando...</td></tr>
        </tbody>
    </table>
</div>

<script type="module" src="{{ asset('js/canciones/index.js') }}"></script>
@endsection
