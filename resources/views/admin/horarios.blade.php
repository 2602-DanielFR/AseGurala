@extends('admin.layout')
@section('titulo', 'Asignación y Horarios')
@section('contenido')
<div class="tarjeta">
    <h2>Asignar Curso a Docente y Horario</h2>
    <form method="POST" action="{{ url('/admin/horarios') }}">
        @csrf
        <div class="grid-2">
            <div>
                <label>Seleccionar Docente</label>
                <select name="docente_id" required>
                    <option value="">-- Seleccione Docente --</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}">{{ $d->apellidos }}, {{ $d->nombres }} (DNI: {{ $d->dni }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Seleccionar Curso</label>
                <select name="curso_id" required>
                    <option value="">-- Seleccione Curso --</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}">{{ $c->codigo }} - {{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>Sección</label>
                <input type="text" name="seccion" value="A" required placeholder="Ej: A">
            </div>
            <div>
                <label>Día de la Semana</label>
                <select name="dia_semana" required>
                    <option value="1">1 - Lunes</option>
                    <option value="2">2 - Martes</option>
                    <option value="3">3 - Miércoles</option>
                    <option value="4">4 - Jueves</option>
                    <option value="5">5 - Viernes</option>
                    <option value="6">6 - Sábado</option>
                    <option value="7">7 - Domingo</option>
                </select>
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>Hora Inicio</label>
                <input type="time" name="hora_inicio" value="08:00" required>
            </div>
            <div>
                <label>Hora Fin</label>
                <input type="time" name="hora_fin" value="12:00" required>
            </div>
        </div>
        <button type="submit" class="btn btn-verde">Guardar Asignación y Horario</button>
    </form>
</div>
@endsection
