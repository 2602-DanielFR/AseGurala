
import serial
import time
import sys

PUERTO = "COM8"
BAUDIOS = 9600

comando = sys.argv[1] if len(sys.argv) > 1 else "PRUEBA"

comandos_validos = {
    "PRUEBA": "OK",
    "INICIAR_CLASE": "CLASE_INICIADA",
    "FINALIZAR_CLASE": "CLASE_FINALIZADA"
}

if comando not in comandos_validos:
    print("ERROR: comando no valido")
    sys.exit(1)

try:
    with serial.Serial(PUERTO, BAUDIOS, timeout=3) as arduino:
        time.sleep(2)
        arduino.reset_input_buffer()
        arduino.write((comando + "\n").encode("utf-8"))
        arduino.flush()

        respuesta = arduino.readline().decode(
            "utf-8", errors="replace"
        ).strip()

        if respuesta == comandos_validos[comando]:
            print(respuesta)
            sys.exit(0)

        print(f"ERROR: respuesta inesperada: {respuesta}")
        sys.exit(1)

except Exception as error:
    print(f"ERROR: {error}")
    sys.exit(1)
