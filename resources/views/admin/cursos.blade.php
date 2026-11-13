@extends('admin.layout')
@section('titulo', 'Gestión de Cursos')
@section('contenido')
<div class="tarjeta">
    <h2>Registrar Nuevo Curso</h2>
    <form method="POST" action="{{ url('/admin/cursos') }}">
        @csrf
        <div class="grid-2">
            <div>
                <label>Código del Curso</label>
                <input type="text" name="codigo" required placeholder="Ej: ING-SIS-04">
            </div>
            <div>
                <label>Nombre del Curso</label>
                <input type="text" name="nombre" required placeholder="Ej: INTELIGENCIA ARTIFICIAL">
            </div>
        </div>
        <button type="submit" class="btn btn-verde">Guardar Curso</button>
    </form>
</div>

<div class="tarjeta">
    <h2>Lista de Cursos</h2>
    <table>
        <thead>
            <tr><th>Código</th><th>Nombre del Curso</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($cursos as $c)
            <tr>
                <td><strong>{{ $c->codigo }}</strong></td>
                <td>{{ $c->nombre }}</td>
                <td>
                    <form method="POST" action="{{ url('/admin/cursos/' . $c->id) }}" onsubmit="return confirm('¿Eliminar curso?');">
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
