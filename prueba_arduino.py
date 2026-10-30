
import serial
import time

PUERTO = "COM8"
BAUDIOS = 9600

try:
    arduino = serial.Serial(PUERTO, BAUDIOS, timeout=3)

    # El Arduino se reinicia al abrir el puerto.
    time.sleep(2)

    # Descartar mensajes iniciales.
    arduino.reset_input_buffer()

    # Enviar el comando de prueba.
    arduino.write(b"PRUEBA\n")
    arduino.flush()

    respuesta = arduino.readline().decode(
        "utf-8", errors="replace"
    ).strip()

    print("Respuesta del Arduino:", respuesta)

    if respuesta == "OK":
        print("COMUNICACION CORRECTA")
    else:
        print("No se recibio la respuesta esperada.")

    arduino.close()

except Exception as error:
    print("Error:", error)
