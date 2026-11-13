from zk import ZK

zk = ZK("192.168.1.201", port=4370, timeout=10)

try:
    dispositivo = zk.connect()
    print("BIOMETRICO CONECTADO (ZKTeco MB10-VL)")

    usuarios = dispositivo.get_users()
    print(f"Usuarios registrados en el biométrico: {len(usuarios)}")
    for u in usuarios:
        print(f"  - ID/DNI: {u.user_id} | Nombre: {u.name}")

    registros = dispositivo.get_attendance()
    print(f"Marcaciones encontradas: {len(registros)}")

    for registro in registros[-15:]:
        print(
            "Usuario (DNI/Código):", registro.user_id,
            "| Fecha y hora:", registro.timestamp,
            "| Tipo:", registro.status
        )

    dispositivo.disconnect()

except Exception as error:
    print("ERROR:", error)
