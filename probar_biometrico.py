from zk import ZK

zk = ZK("192.168.1.201", port=4370, timeout=10)

try:
    dispositivo = zk.connect()
    print("BIOMETRICO CONECTADO")

    registros = dispositivo.get_attendance()
    print(f"Marcaciones encontradas: {len(registros)}")

    for registro in registros[-10:]:
        print(
            "Usuario:", registro.user_id,
            "| Fecha y hora:", registro.timestamp,
            "| Tipo:", registro.status
        )

    dispositivo.disconnect()

except Exception as error:
    print("ERROR:", error)