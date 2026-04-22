# IoT Module - PBL409

IoT device management, firmware, and sensor data handling.

## Structure

### /firmware
- Main Arduino/ESP32 firmware code
- Configuration and initialization
- Sensor data collection logic

### /drivers
- Device drivers for various sensors
- Communication protocols (UART, I2C, SPI)
- Hardware abstraction layer

### /sensors
- Sensor-specific configurations
- Calibration data and parameters
- Sensor data processing utilities

## Getting Started

1. **Upload Firmware**
   ```
   Arduino IDE -> File -> Open -> main.ino
   Select Board: Arduino Uno / ESP32
   Select Port: COM3 (or your device port)
   Click Upload
   ```

2. **Configure Sensors**
   - Edit sensor configurations in /sensors folder
   - Set calibration values
   - Configure data intervals

3. **Monitor Data**
   - Use Serial Monitor (9600 baud)
   - View raw sensor readings
   - Debug connection issues

## Supported Devices
- Arduino Uno
- Arduino Mega
- ESP32
- ESP8266

## Dependencies
- Arduino IDE 1.8+
- Serial Communication Library
