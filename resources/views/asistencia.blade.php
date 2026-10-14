
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control de Asistencia</title>

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
            max-width: 650px;
        }

        .cabecera {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
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

        input {
            width: 100%;
            padding: 16px;
            border: 1px solid #475569;
            background: #0b1220;
            color: white;
            border-radius: 10px;
            font-size: 18px;
            text-align: center;
        }

        .docente {
            display: none;
        }

        .nombre {
            font-size: 22px;
            margin: 10px 0;
        }

        .curso {
            background: #0b1220;
            padding: 18px;
            border-radius: 10px;
            margin: 20px 0;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 17px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
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
            padding: 12px;
            border-radius: 8px;
            background: #0b1220;
            color: #93c5fd;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <main class="panel">

        <header class="cabecera">
            <div class="icono">▦</div>
            <div>
                <h1>CONTROL DE ASISTENCIA</h1>
                <div class="subtitulo">Registro de ingreso docente</div>
            </div>
        </header>

        <section class="tarjeta">
            <label for="dni">ESCANEA EL CÓDIGO DE BARRAS DEL DNI</label>

            <input
                id="dni"
                type="text"
                placeholder="Esperando lectura..."
                maxlength="8"
                readonly
                autocomplete="off"
                aria-label="DNI leído por el escáner"
            >

            <p class="ayuda">
                Utiliza el lector USB para identificar al docente.
            </p>

            <div id="mensaje" role="status" aria-live="polite">
                Esperando lectura del DNI...
            </div>
        </section>

        <section class="tarjeta docente" id="datosDocente">
            <div class="subtitulo">DOCENTE IDENTIFICADO</div>

            <div class="nombre" id="nombre"></div>
            <div id="dniDocente"></div>

            <div class="curso">
                <div class="subtitulo">CURSO ASIGNADO</div>
                <h3 id="curso">Pendiente de asignación</h3>

                <div id="seccion" class="ayuda">
                    Sección y horario pendientes
                </div>
            </div>

            <div id="estadoClase" class="estado-clase" hidden></div>

            <button id="iniciar" disabled>
                INICIAR CLASE
            </button>
        </section>

    </main>

    <script>
        const campoDni = document.getElementById('dni');
        const mensaje = document.getElementById('mensaje');
        const tarjeta = document.getElementById('datosDocente');
        const botonIniciar = document.getElementById('iniciar');
        const estadoClase = document.getElementById('estadoClase');

        let lectura = '';
        let ultimoDigito = 0;
        let modoBoton = 'iniciar';
        let horarioSeleccionado = null;
        let sesionActualId = null;
        let buscando = false;

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
            if (
                evento.ctrlKey ||
                evento.altKey ||
                evento.metaKey
            ) {
                return;
            }

            if (/^[0-9]$/.test(evento.key)) {
                const ahora = Date.now();

                if (ahora - ultimoDigito > 150) {
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

            if (evento.key === 'Enter' && lectura.length === 8) {
                evento.preventDefault();

                if (buscando) {
                    return;
                }

                const dni = lectura;
                lectura = '';

                await buscarDocente(dni);
            }
        });

        async function buscarDocente(dni) {
            if (buscando) {
                return;
            }

            buscando = true;
            mensaje.textContent = 'Consultando docente...';
            tarjeta.style.display = 'none';
            botonIniciar.disabled = true;
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

                const horarios = resultado.horarios || [];

                const horarioActivo = horarios.find(
                    h => h.sesion_estado === 'iniciada'
                );

                const horarioFinalizado = horarios.find(
                    h => h.sesion_estado === 'finalizada'
                );

                const horarioDisponible = horarios.find(
                    h => h.puede_iniciar
                );

                if (horarioActivo) {
                    horarioSeleccionado = horarioActivo;
                    sesionActualId = horarioActivo.sesion_id;
                    modoBoton = 'finalizar';

                    botonIniciar.textContent = 'FINALIZAR CLASE';
                    botonIniciar.disabled = false;

                    estadoClase.textContent = 'CLASE EN CURSO';
                    estadoClase.hidden = false;

                    mensaje.textContent = 'Tienes una clase en curso.';

                } else if (horarioFinalizado) {
                    horarioSeleccionado = horarioFinalizado;
                    sesionActualId = horarioFinalizado.sesion_id;
                    modoBoton = 'finalizada';

                    botonIniciar.textContent = 'CLASE FINALIZADA';
                    botonIniciar.disabled = true;

                    estadoClase.textContent = 'CLASE FINALIZADA';
                    estadoClase.hidden = false;

                    mensaje.textContent = 'La clase de hoy ya fue finalizada.';

                } else if (horarioDisponible) {
                    horarioSeleccionado = horarioDisponible;
                    sesionActualId = null;
                    modoBoton = 'iniciar';

                    botonIniciar.textContent = 'INICIAR CLASE';
                    botonIniciar.disabled = false;

                    estadoClase.hidden = true;

                    mensaje.textContent =
                        'Docente identificado. Horario habilitado para iniciar.';

                } else {
                    horarioSeleccionado = null;
                    sesionActualId = null;
                    modoBoton = 'iniciar';

                    botonIniciar.textContent = 'INICIAR CLASE';
                    botonIniciar.disabled = true;

                    estadoClase.hidden = true;

                    mensaje.textContent =
                        'No hay una clase habilitada en este momento.';
                }

                const horarioMostrar =
                    horarioActivo ||
                    horarioFinalizado ||
                    horarioDisponible ||
                    horarios[0];

                if (horarioMostrar) {
                    document.getElementById('curso').textContent =
                        `${horarioMostrar.codigo} - ${horarioMostrar.nombre}`;

                    document.getElementById('seccion').textContent =
                        `${dias[Number(horarioMostrar.dia_semana)] || 'Día no definido'} | ` +
                        `Sección ${horarioMostrar.seccion} | ` +
                        `${horarioMostrar.hora_inicio.substring(0, 5)} - ` +
                        `${horarioMostrar.hora_fin.substring(0, 5)}`;

                } else {
                    const cursos = resultado.cursos || [];

                    document.getElementById('curso').textContent =
                        cursos.length
                            ? cursos.map(c => `${c.codigo} - ${c.nombre}`).join(', ')
                            : 'Sin curso asignado';

                    document.getElementById('seccion').textContent =
                        'No hay horarios registrados para este docente.';
                }

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
            if (
                modoBoton === 'finalizada' ||
                !horarioSeleccionado
            ) {
                return;
            }

            botonIniciar.disabled = true;
            mensaje.textContent = 'Procesando...';

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
                        modoBoton = 'finalizada';

                        botonIniciar.textContent = 'CLASE FINALIZADA';
                        botonIniciar.disabled = true;

                        estadoClase.textContent = 'CLASE FINALIZADA';
                        estadoClase.hidden = false;

                    } else {
                        modoBoton = 'finalizar';
                        sesionActualId = resultado.sesion_id;

                        botonIniciar.textContent = 'FINALIZAR CLASE';
                        botonIniciar.disabled = false;

                        estadoClase.textContent = 'CLASE EN CURSO';
                        estadoClase.hidden = false;
                    }
                } else {
                    // Volver a consultar el servidor para conocer
                    // el estado real de la clase.
                    await buscarDocente(campoDni.value);
                }

            } catch (error) {
                console.error('Error al procesar la clase:', error);
                mensaje.textContent = 'No se pudo procesar la operación.';
                botonIniciar.disabled = false;
            }
        });
    </script>
</body>
</html>
