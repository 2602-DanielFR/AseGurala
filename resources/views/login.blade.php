<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Panel Administrativo AseGurala</title>
    <style>
        * { box-sizing: border-box; }
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
        .caja-login {
            width: 100%;
            max-width: 440px;
            background: #172033;
            border: 1px solid #263449;
            border-radius: 16px;
            padding: 30px;
        }
        h1 { margin: 0 0 8px; font-size: 22px; }
        .subtitulo { color: #94a3b8; font-size: 14px; margin-bottom: 24px; }
        label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 6px; color: #cbd5e1; }
        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #475569;
            background: #0b1220;
            color: white;
            border-radius: 10px;
            font-size: 15px;
            margin-bottom: 16px;
        }
        button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 14px;
            background: #2563eb;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .error {
            background: #3b181e;
            color: #fda4af;
            border: 1px solid #7f1d1d;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .credenciales-demo {
            margin-top: 20px;
            padding: 14px;
            background: #0b1220;
            border: 1px solid #1e293b;
            border-radius: 10px;
            font-size: 13px;
            color: #94a3b8;
        }
        .enlace-volver {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="caja-login">
        <h1>PANEL ADMINISTRATIVO</h1>
        <div class="subtitulo">Acceso para Usuarios Web (Administrador / Docente)</div>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <label for="email">CORREO ELECTRÓNICO</label>
            <input type="email" id="email" name="email" value="{{ old('email', 'admin@asegurala.edu.pe') }}" required autofocus>

            <label for="password">CONTRASEÑA</label>
            <input type="password" id="password" name="password" value="admin12345" required>

            <button type="submit">INGRESAR AL PANEL</button>
        </form>

        <div class="credenciales-demo">
            <strong>Credenciales de prueba pre-cargadas:</strong><br><br>
            • <strong>Admin:</strong> admin@asegurala.edu.pe / <code>admin12345</code><br>
            • <strong>Docente:</strong> docente@asegurala.edu.pe / <code>docente12345</code>
        </div>

        <a href="{{ url('/asistencia') }}" class="enlace-volver">← Ir al Terminal de Escaneo de DNI y Asistencia</a>
    </div>
</body>
</html>
