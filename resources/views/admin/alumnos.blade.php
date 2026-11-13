@extends('admin.layout')
@section('titulo', 'Gestión de Alumnos')
@section('contenido')
<div class="tarjeta">
    <h2>Registrar Alumno</h2>
    <form method="POST" action="{{ url('/admin/alumnos') }}">
        @csrf
        <div class="grid-2">
            <div>
                <label>DNI</label>
                <input type="text" name="dni" maxlength="20" required placeholder="Ej: 70000042">
            </div>
            <div>
                <label>Código Universitario / ID ZKTeco</label>
                <input type="text" name="codigo" placeholder="Ej: 42 (Debe coincidir con ID del biométrico)">
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>Nombres</label>
                <input type="text" name="nombres" required placeholder="Nombres">
            </div>
            <div>
                <label>Apellidos</label>
                <input type="text" name="apellidos" required placeholder="Apellidos">
            </div>
        </div>
        <button type="submit" class="btn btn-verde">Guardar Alumno</button>
    </form>
</div>

<div class="tarjeta">
    <h2>Nómina de Alumnos ({{ count($alumnos) }})</h2>
    <table>
        <thead>
            <tr><th>ID / Código</th><th>DNI</th><th>Apellidos y Nombres</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($alumnos as $a)
            <tr>
                <td><strong>{{ $a->codigo ?? '-' }}</strong></td>
                <td>{{ $a->dni }}</td>
                <td>{{ $a->apellidos }}, {{ $a->nombres }}</td>
                <td>
                    <form method="POST" action="{{ url('/admin/alumnos/' . $a->id) }}" onsubmit="return confirm('¿Eliminar alumno?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-rojo" style="padding: 5px 10px; font-size: 11px;">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
