@extends('admin.layout')
@section('titulo', 'Gestión de Docentes')
@section('contenido')
<div class="tarjeta">
    <h2>Registrar Nuevo Docente</h2>
    <form method="POST" action="{{ url('/admin/docentes') }}">
        @csrf
        <div class="grid-2">
            <div>
                <label>DNI</label>
                <input type="text" name="dni" maxlength="15" required placeholder="Ej: 12345678">
            </div>
            <div>
                <label>Correo Electrónico</label>
                <input type="email" name="email" placeholder="docente@udh.edu.pe">
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>Nombres</label>
                <input type="text" name="nombres" required placeholder="Nombres del docente">
            </div>
            <div>
                <label>Apellidos</label>
                <input type="text" name="apellidos" required placeholder="Apellidos del docente">
            </div>
        </div>
        <button type="submit" class="btn btn-verde">Guardar Docente</button>
    </form>
</div>

<div class="tarjeta">
    <h2>Lista de Docentes</h2>
    <table>
        <thead>
            <tr><th>DNI</th><th>Apellidos y Nombres</th><th>Correo</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($docentes as $d)
            <tr>
                <td><strong>{{ $d->dni }}</strong></td>
                <td>{{ $d->apellidos }}, {{ $d->nombres }}</td>
                <td>{{ $d->email ?? '-' }}</td>
                <td>
                    <form method="POST" action="{{ url('/admin/docentes/' . $d->id) }}" onsubmit="return confirm('¿Eliminar docente?');">
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
