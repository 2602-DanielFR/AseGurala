<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control de Asistencia - AseGurala</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0b1220;
            color: #f8fafc;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .panel {
            width: 100%;
            max-width: 880px;
        }

        .cabecera {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .cabecera-izq {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icono {
            background: #2563eb;
            padding: 15px;
            border-radius: 12px;
            font-size: 25px;
        }

        h1 {
            font-size: 23px;
            margin: 0 0 6px;
        }

        .subtitulo,
        .ayuda {
            color: #94a3b8;
            font-size: 14px;
        }

        .tarjeta {
            background: #172033;
            border: 1px solid #263449;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 12px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 15px;
            border: 1px solid #475569;
            background: #0b1220;
            color: white;
            border-radius: 10px;
            font-size: 16px;
        }

        input {
            font-size: 18px;
            text-align: center;
        }

        select {
            cursor: pointer;
            margin-top: 8px;
        }

        select:focus, input:focus {
            outline: 2px solid #2563eb;
            border-color: transparent;
        }

        .docente {
            display: none;
        }

        .nombre {
            font-size: 22px;
            margin: 10px 0;
            font-weight: bold;
        }

        .curso {
            background: #0b1220;
            padding: 18px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #1e293b;
        }

        .detalle-horario {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #1e293b;
            display: none;
        }

        .acciones-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 14px;
        }

        button, .btn-link {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 15px 18px;
            background: #2563eb;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.2s, background 0.2s;
        }

        button:hover:not(:disabled), .btn-link:hover {
            background: #1d4ed8;
        }

        button.btn-finalizar {
            background: #dc2626;
        }

        button.btn-finalizar:hover:not(:disabled) {
            background: #b91c1c;
        }

        button.btn-secundario {
            background: #0f766e;
        }

        button.btn-secundario:hover:not(:disabled) {
            background: #115e59;
        }

        button.btn-excel {
            background: #15803d;
        }

        button.btn-excel:hover:not(:disabled) {
            background: #166534;
        }

        button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        #mensaje {
            margin-top: 15px;
            color: #93c5fd;
            text-align: center;
        }

        .estado-clase {
            margin-top: 12px;
            margin-bottom: 12px;
            padding: 12px;
            border-radius: 8px;
            background: #0b1220;
            color: #93c5fd;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #1e3a8a;
        }

        /* El panel de alumnos permanece oculto hasta que se seleccione el curso asignado */
        .panel-alumnos {
            display: none;
        }

        .cabecera-alumnos {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .estado-biometrico {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #0b1220;
            border: 1px solid #334155;
            color: #cbd5e1;
        }

        .punto {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #f59e0b;
            display: inline-block;
        }

        .punto.online {
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
        }

        .resumen-contadores {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .kpi {
            background: #0b1220;
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }

        .kpi .valor {
            font-size: 24px;
            font-weight: bold;
            margin-top: 4px;
        }

        .kpi.verde .valor { color: #4ade80; }
        .kpi.rojo .valor { color: #f87171; }
        .kpi.azul .valor { color: #60a5fa; }

        .tabla-contenedor {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #263449;
            background: #0b1220;
            max-height: 520px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 11px 14px;
            text-align: left;
            border-bottom: 1px solid #1e293b;
        }

        th {
            background: #111c30;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        tr.fila-asistio {
            background: rgba(22, 163, 74, 0.12);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-asistio {
            background: #14532d;
            color: #4ade80;
            border: 1px solid #166534;
        }

        .badge-falta {
            background: #3b181e;
            color: #fda4af;
            border: 1px solid #7f1d1d;
        }
    </style>
</head>

<body>
    <main class="panel">

        <header class="cabecera">
            <div class="cabecera-izq">
                <div class="icono">▦</div>
                <div>
                    <h1>CONTROL DE ASISTENCIA BIOMÉTRICO</h1>
                    <div class="subtitulo">Escaneo Docente + Arduino Relé + ZKTeco MB10-VL</div>
                </div>
            </div>
            <div>
                <a href="/login" class="btn-link" style="width: auto; padding: 10px 16px; font-size: 13px; background: #1e293b; border: 1px solid #334155;">
                    🔐 Acceso Usuario Web (Admin)
                </a>
            </div>
        </header>

        <section class="tarjeta">
            <label for="dni">ESCANEA EL CÓDIGO DE BARRAS DEL DNI DEL DOCENTE</label>

            <input
                id="dni"
                type="text"
                placeholder="Esperando lectura del DNI (Ej. 12345678)..."
                maxlength="8"
                autocomplete="off"
                aria-label="DNI leído por el escáner"
            >

            <p class="ayuda">
                Utiliza el lector USB de código de barras o digita el DNI (Ej: <strong>12345678</strong>, <strong>87654321</strong> o <strong>44556677</strong>) y presiona Enter.
            </p>

            <div id="mensaje" role="status" aria-live="polite">
                Esperando lectura del DNI...
            </div>
        </section>

        <section class="tarjeta docente" id="datosDocente">
            <div class="subtitulo">DOCENTE IDENTIFICADO</div>

            <div class="nombre" id="nombre"></div>
            <div id="dniDocente" class="ayuda"></div>

            <div class="curso">
                <label for="selectorCurso" class="subtitulo" style="margin-bottom: 6px;">
                    SELECCIONA EL CURSO ASIGNADO PARA CARGAR LA LISTA DE ALUMNOS
                </label>

                <select id="selectorCurso">
                    <option value="">-- Seleccione un curso asignado --</option>
                </select>

                <div class="detalle-horario" id="detalleHorario">
                    <h3 id="curso" style="margin: 4px 0 6px; font-size: 17px;"></h3>
                    <div id="seccion" class="ayuda"></div>
                </div>
            </div>

            <div id="estadoClase" class="estado-clase" hidden></div>

            <button id="iniciar" disabled>
                SELECCIONE UN CURSO PARA CONTINUAR
            </button>

            <div class="acciones-grid">
                <button id="btnSincronizarManual" type="button" class="btn-secundario" disabled>
                    🔄 Sincronizar ZKTeco Ahora
                </button>
                <button id="btnExportarExcel" type="button" class="btn-excel" disabled>
                    📊 Exportar Asistencia en Excel
                </button>
            </div>
        </section>

        <!-- Panel de Alumnos: SOLO se muestra cuando el docente selecciona un curso asignado -->
        <section class="tarjeta panel-alumnos" id="panelAlumnos">
            <div class="cabecera-alumnos">
                <div>
                    <h2 style="margin: 0; font-size: 18px;" id="tituloListaAlumnos">LISTA DE ASISTENCIA DE ALUMNOS</h2>
                    <div class="subtitulo">Recepción automática vía Ethernet desde ZKTeco MB10-VL</div>
                </div>
                <div class="estado-biometrico">
                    <span id="puntoBiometrico" class="punto"></span>
                    <span id="textoBiometrico">Biométrico en espera de inicio de clase</span>
                </div>
            </div>

            <div class="resumen-contadores">
                <div class="kpi azul">
                    <div class="subtitulo">TOTAL ALUMNOS</div>
                    <div class="valor" id="kpiTotal">0</div>
                </div>
                <div class="kpi verde">
                    <div class="subtitulo">ASISTIERON</div>
                    <div class="valor" id="kpiAsistieron">0</div>
                </div>
                <div class="kpi rojo">
                    <div class="subtitulo">PENDIENTES / FALTAS</div>
                    <div class="valor" id="kpiFaltaron">0</div>
                </div>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>ID Biométrico / DNI</th>
                            <th>Apellidos y Nombres del Alumno</th>
                            <th>Hora Marcación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAlumnosBody">
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script>
        const campoDni = document.getElementById('dni');
        const mensaje = document.getElementById('mensaje');
        const tarjeta = document.getElementById('datosDocente');
        const selectorCurso = document.getElementById('selectorCurso');
        const detalleHorario = document.getElementById('detalleHorario');
        const botonIniciar = document.getElementById('iniciar');
        const botonSincronizar = document.getElementById('btnSincronizarManual');
        const botonExcel = document.getElementById('btnExportarExcel');
        const estadoClase = document.getElementById('estadoClase');
        const panelAlumnos = document.getElementById('panelAlumnos');
        const puntoBiometrico = document.getElementById('puntoBiometrico');
        const textoBiometrico = document.getElementById('textoBiometrico');
        const tablaAlumnosBody = document.getElementById('tablaAlumnosBody');
        const tituloListaAlumnos = document.getElementById('tituloListaAlumnos');

        let lectura = '';
        let ultimoDigito = 0;
        let modoBoton = 'iniciar';
        let horariosDocente = [];
        let horarioSeleccionado = null;
        let cursoActualId = null;
        let sesionActualId = null;
        let buscando = false;
        let intervaloPolling = null;
        let sincronizando = false;

        const dias = {
            1: 'Lunes',
            2: 'Martes',
            3: 'Miércoles',
            4: 'Jueves',
            5: 'Viernes',
            6: 'Sábado',
            7: 'Domingo'
        };

        document.addEventListener('keydown', async (evento) => {
            if (evento.ctrlKey || evento.altKey || evento.metaKey) {
                return;
            }

            if (document.activeElement === campoDni) {
                if (evento.key === 'Enter' && campoDni.value.trim().length === 8) {
                    evento.preventDefault();
                    await buscarDocente(campoDni.value.trim());
                }
                return;
            }

            if (/^[0-9]$/.test(evento.key)) {
                const ahora = Date.now();
                if (ahora - ultimoDigito > 200) {
                    lectura = '';
                }
                ultimoDigito = ahora;
                lectura += evento.key;

                if (lectura.length > 8) {
                    lectura = lectura.slice(-8);
                }

                campoDni.value = lectura;
                return;
            }

            if (evento.key === 'Enter' && (lectura.length === 8 || campoDni.value.trim().length === 8)) {
                evento.preventDefault();
                if (buscando) return;

                const dni = lectura.length === 8 ? lectura : campoDni.value.trim();
                lectura = '';
                await buscarDocente(dni);
            }
        });

        function detenerPollingBiometrico() {
            if (intervaloPolling) {
                clearInterval(intervaloPolling);
                intervaloPolling = null;
            }
        }

        function ocultarListaAlumnos() {
            detenerPollingBiometrico();
            panelAlumnos.style.display = 'none';
            tablaAlumnosBody.innerHTML = '';
            document.getElementById('kpiTotal').textContent = '0';
            document.getElementById('kpiAsistieron').textContent = '0';
            document.getElementById('kpiFaltaron').textContent = '0';
        }

        async function cargarPadronPorCursoSeleccionado(horario) {
            if (!horario) {
                ocultarListaAlumnos();
                return;
            }

            panelAlumnos.style.display = 'block';
            tituloListaAlumnos.textContent = `LISTA DE ASISTENCIA - ${horario.codigo} (${horario.nombre})`;
            tablaAlumnosBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8;">
                        Cargando lista de alumnos del curso seleccionado...
                    </td>
                </tr>
            `;

            try {
                const params = new URLSearchParams({
                    horario_id: horario.id,
                    curso_id: horario.curso_id || ''
                });
                if (horario.sesion_id) {
                    params.set('sesion_id', horario.sesion_id);
                }

                const resp = await fetch(`/asistencia/padron-alumnos?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();
                if (data.ok) {
                    renderizarTablaAlumnos(data.alumnos || [], data.resumen);
                }
            } catch (e) {
                console.error('Error cargando padrón del curso:', e);
            }
        }

        function iniciarPollingBiometrico(sesionId, esperarEncendido = false) {
            detenerPollingBiometrico();

            panelAlumnos.style.display = 'block';
            botonSincronizar.disabled = false;
            botonExcel.disabled = false;

            cargarListaActual(sesionId);

            if (esperarEncendido) {
                puntoBiometrico.classList.remove('online');
                textoBiometrico.textContent = 'Relé activado. Esperando arranque del ZKTeco MB10-VL (~12s)...';
                setTimeout(() => {
                    sincronizarConZKTeco(sesionId);
                }, 10000);
            } else {
                sincronizarConZKTeco(sesionId);
            }

            intervaloPolling = setInterval(() => {
                sincronizarConZKTeco(sesionId);
            }, 2500);
        }

        async function cargarListaActual(sesionId) {
            if (!sesionId) return;
            try {
                const resp = await fetch(`/sesion-clase/${sesionId}/asistencias`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();
                if (data.ok) {
                    renderizarTablaAlumnos(data.alumnos, data.resumen);
                }
            } catch (e) {
                console.error('Error al cargar lista de sesión:', e);
            }
        }

        async function sincronizarConZKTeco(sesionId) {
            if (!sesionId || sincronizando) return;
            sincronizando = true;

            try {
                const resp = await fetch(`/sesion-clase/${sesionId}/sincronizar-biometrico`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!resp.ok) return;
                const data = await resp.json();

                if (data.ok) {
                    if (data.biometrico_conectado) {
                        puntoBiometrico.classList.add('online');
                        textoBiometrico.textContent = data.mensaje_biometrico || 'ZKTeco MB10-VL conectado';
                    } else {
                        puntoBiometrico.classList.remove('online');
                        textoBiometrico.textContent = data.mensaje_biometrico || 'Esperando conexión con ZKTeco...';
                    }

                    renderizarTablaAlumnos(data.alumnos || [], data.resumen || { total: 0, asistieron: 0, faltaron: 0 });
                }
            } catch (error) {
                console.error('Error sincronizando biométrico:', error);
            } finally {
                sincronizando = false;
            }
        }

        function renderizarTablaAlumnos(alumnos, resumen) {
            document.getElementById('kpiTotal').textContent = resumen?.total ?? alumnos.length;
            document.getElementById('kpiAsistieron').textContent = resumen?.asistieron ?? 0;
            document.getElementById('kpiFaltaron').textContent = resumen?.faltaron ?? alumnos.length;

            if (!alumnos || alumnos.length === 0) {
                tablaAlumnosBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8;">
                            No hay alumnos registrados en este curso.
                        </td>
                    </tr>
                `;
                return;
            }

            tablaAlumnosBody.innerHTML = alumnos.map((alumno, idx) => {
                const asistio = alumno.estado === 'asistio';
                const nombreCompleto = `${alumno.apellidos || ''}, ${alumno.nombres || ''}`.replace(/^,\s*|\s*,$/g, '');
                const codigoMostrar = alumno.codigo ? `ID: ${alumno.codigo} (${alumno.dni})` : (alumno.dni || '-');
                return `
                    <tr class="${asistio ? 'fila-asistio' : ''}">
                        <td>${idx + 1}</td>
                        <td><strong>${codigoMostrar}</strong></td>
                        <td>${nombreCompleto}</td>
                        <td>${alumno.hora_formateada || '-'}</td>
                        <td>
                            <span class="badge ${asistio ? 'badge-asistio' : 'badge-falta'}">
                                ${asistio ? '✓ ASISTIÓ' : 'PENDIENTE'}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Evento al seleccionar un curso del desplegable
        selectorCurso.addEventListener('change', async () => {
            const horarioId = Number(selectorCurso.value);
            detenerPollingBiometrico();

            if (!horarioId) {
                horarioSeleccionado = null;
                cursoActualId = null;
                sesionActualId = null;
                detalleHorario.style.display = 'none';
                estadoClase.hidden = true;
                botonIniciar.textContent = 'SELECCIONE UN CURSO PARA CONTINUAR';
                botonIniciar.className = '';
                botonIniciar.disabled = true;
                botonSincronizar.disabled = true;
                botonExcel.disabled = true;
                ocultarListaAlumnos();
                return;
            }

            const horario = horariosDocente.find(h => Number(h.id) === horarioId);
            if (!horario) return;

            horarioSeleccionado = horario;
            cursoActualId = horario.curso_id || null;
            sesionActualId = horario.sesion_id || null;
            botonExcel.disabled = false;

            document.getElementById('curso').textContent = `${horario.codigo} - ${horario.nombre}`;
            document.getElementById('seccion').textContent =
                `${dias[Number(horario.dia_semana)] || 'Día no definido'} | ` +
                `Sección ${horario.seccion} | ` +
                `${horario.hora_inicio.substring(0, 5)} - ` +
                `${horario.hora_fin.substring(0, 5)}`;
            detalleHorario.style.display = 'block';

            if (horario.sesion_estado === 'iniciada') {
                modoBoton = 'finalizar';
                botonIniciar.textContent = 'FINALIZAR CLASE Y APAGAR BIOMÉTRICO';
                botonIniciar.className = 'btn-finalizar';
                botonIniciar.disabled = false;

                estadoClase.textContent = 'CLASE EN CURSO - BIOMÉTRICO ENCENDIDO';
                estadoClase.hidden = false;
                mensaje.textContent = 'Clase en curso. Capturando asistencia del ZKTeco MB10-VL...';

                iniciarPollingBiometrico(sesionActualId, false);

            } else if (horario.sesion_estado === 'finalizada') {
                modoBoton = 'finalizada';
                botonIniciar.textContent = 'CLASE FINALIZADA';
                botonIniciar.className = '';
                botonIniciar.disabled = true;
                botonSincronizar.disabled = true;

                estadoClase.textContent = 'CLASE FINALIZADA';
                estadoClase.hidden = false;
                mensaje.textContent = 'La clase de hoy ya fue finalizada. Puedes exportar el reporte Excel.';

                puntoBiometrico.classList.remove('online');
                textoBiometrico.textContent = 'Biométrico apagado (Clase finalizada)';
                await cargarPadronPorCursoSeleccionado(horario);

            } else if (horario.puede_iniciar) {
                modoBoton = 'iniciar';
                botonIniciar.textContent = 'INICIAR CLASE Y ENCENDER BIOMÉTRICO';
                botonIniciar.className = '';
                botonIniciar.disabled = false;
                botonSincronizar.disabled = true;

                estadoClase.hidden = true;
                puntoBiometrico.classList.remove('online');
                textoBiometrico.textContent = 'Curso seleccionado. Presiona INICIAR CLASE para encender el biométrico.';
                mensaje.textContent = 'Curso seleccionado y lista de alumnos cargada. Ya puedes iniciar la clase.';

                await cargarPadronPorCursoSeleccionado(horario);

            } else {
                modoBoton = 'iniciar';
                botonIniciar.textContent = 'FUERA DE HORARIO PARA INICIAR';
                botonIniciar.className = '';
                botonIniciar.disabled = true;
                botonSincronizar.disabled = true;

                estadoClase.hidden = true;
                puntoBiometrico.classList.remove('online');
                textoBiometrico.textContent = 'Biométrico en espera';
                mensaje.textContent = 'Curso fuera de horario (solo se activa desde 10 minutos antes en el día asignado).';

                await cargarPadronPorCursoSeleccionado(horario);
            }
        });

        async function buscarDocente(dni) {
            if (buscando) return;

            buscando = true;
            ocultarListaAlumnos();
            mensaje.textContent = 'Consultando docente...';
            tarjeta.style.display = 'none';
            detalleHorario.style.display = 'none';
            botonIniciar.disabled = true;
            botonSincronizar.disabled = true;
            botonExcel.disabled = true;
            estadoClase.hidden = true;

            try {
                const respuesta = await fetch(
                    `/docente/${encodeURIComponent(dni)}`,
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!respuesta.ok) {
                    throw new Error(`Error HTTP ${respuesta.status}`);
                }

                const resultado = await respuesta.json();

                if (!resultado.encontrado) {
                    mensaje.textContent = 'Docente no encontrado.';
                    campoDni.value = '';
                    return;
                }

                document.getElementById('nombre').textContent =
                    `${resultado.docente.nombres} ${resultado.docente.apellidos}`;

                document.getElementById('dniDocente').textContent =
                    `DNI: ${resultado.docente.dni}`;

                horariosDocente = resultado.horarios || [];

                // Llenar el selector de cursos asignados sin seleccionar ninguno por defecto
                // para que la lista de alumnos se cargue únicamente cuando el docente elija el curso
                selectorCurso.innerHTML = `<option value="">-- Seleccione un curso asignado (${horariosDocente.length} disponible${horariosDocente.length === 1 ? '' : 's'}) --</option>`;

                horariosDocente.forEach(h => {
                    const etiquetaEstado = h.sesion_estado === 'iniciada'
                        ? ' [EN CURSO]'
                        : (h.sesion_estado === 'finalizada' ? ' [FINALIZADA]' : (h.puede_iniciar ? ' [HABILITADO]' : ''));

                    const opcion = document.createElement('option');
                    opcion.value = h.id;
                    opcion.textContent = `${h.codigo} - ${h.nombre} (Sección ${h.seccion} | ${dias[Number(h.dia_semana)] || ''} ${h.hora_inicio.substring(0, 5)}-${h.hora_fin.substring(0, 5)})${etiquetaEstado}`;
                    selectorCurso.appendChild(opcion);
                });

                botonIniciar.textContent = 'SELECCIONE UN CURSO PARA CONTINUAR';
                botonIniciar.className = '';
                botonIniciar.disabled = true;

                mensaje.textContent = 'Docente identificado. Selecciona el curso asignado para cargar la lista de alumnos.';
                tarjeta.style.display = 'block';

            } catch (error) {
                console.error('Error al buscar docente:', error);
                tarjeta.style.display = 'none';
                mensaje.textContent =
                    error.message || 'No se pudo consultar el servidor.';

            } finally {
                buscando = false;
            }
        }

        botonIniciar.addEventListener('click', async () => {
            if (modoBoton === 'finalizada' || !horarioSeleccionado) {
                return;
            }

            botonIniciar.disabled = true;
            mensaje.textContent = 'Comunicando con Arduino y biométrico...';

            try {
                const finalizar = modoBoton === 'finalizar';

                const respuesta = await fetch(
                    finalizar
                        ? '/sesion-clase/finalizar'
                        : '/sesion-clase/iniciar',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        },
                        body: JSON.stringify(
                            finalizar
                                ? { sesion_id: sesionActualId }
                                : { horario_id: horarioSeleccionado.id }
                        )
                    }
                );

                const resultado = await respuesta.json();

                mensaje.textContent =
                    resultado.mensaje || 'Operación completada.';

                if (resultado.ok) {
                    if (finalizar) {
                        detenerPollingBiometrico();
                        modoBoton = 'finalizada';
                        horarioSeleccionado.sesion_estado = 'finalizada';

                        botonIniciar.textContent = 'CLASE FINALIZADA';
                        botonIniciar.className = '';
                        botonIniciar.disabled = true;
                        botonSincronizar.disabled = true;

                        estadoClase.textContent = 'CLASE FINALIZADA';
                        estadoClase.hidden = false;

                        puntoBiometrico.classList.remove('online');
                        textoBiometrico.textContent = 'Biométrico apagado (Relé desactivado)';
                        cargarListaActual(sesionActualId);

                    } else {
                        modoBoton = 'finalizar';
                        sesionActualId = resultado.sesion_id;
                        horarioSeleccionado.sesion_id = resultado.sesion_id;
                        horarioSeleccionado.sesion_estado = 'iniciada';

                        botonIniciar.textContent = 'FINALIZAR CLASE Y APAGAR BIOMÉTRICO';
                        botonIniciar.className = 'btn-finalizar';
                        botonIniciar.disabled = false;

                        estadoClase.textContent = 'CLASE EN CURSO - BIOMÉTRICO ENCENDIDO';
                        estadoClase.hidden = false;

                        iniciarPollingBiometrico(sesionActualId, true);
                    }
                } else {
                    botonIniciar.disabled = false;
                }

            } catch (error) {
                console.error('Error al procesar la clase:', error);
                mensaje.textContent = 'No se pudo procesar la operación.';
                botonIniciar.disabled = false;
            }
        });

        botonSincronizar.addEventListener('click', async () => {
            if (!sesionActualId) return;
            textoBiometrico.textContent = 'Sincronizando con ZKTeco MB10-VL...';
            await sincronizarConZKTeco(sesionActualId);
        });

        botonExcel.addEventListener('click', () => {
            if (!horarioSeleccionado && !cursoActualId && !sesionActualId) return;
            const params = new URLSearchParams();
            if (sesionActualId) {
                params.set('sesion_id', sesionActualId);
            } else if (horarioSeleccionado?.id) {
                params.set('horario_id', horarioSeleccionado.id);
            } else if (cursoActualId) {
                params.set('curso_id', cursoActualId);
            }
            window.location.href = `/asistencia/exportar-excel?${params.toString()}`;
        });
    </script>
</body>
</html>
