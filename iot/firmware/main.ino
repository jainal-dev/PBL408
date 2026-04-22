// IoT Device Main Firmware
// For Arduino/ESP32 boards

#define SENSOR_PIN A0
#define BAUD_RATE 9600

void setup() {
  Serial.begin(BAUD_RATE);
  pinMode(SENSOR_PIN, INPUT);
  
  Serial.println("IoT Device initialized");
  Serial.println("Waiting for sensor data...");
}

void loop() {
  int sensorValue = analogRead(SENSOR_PIN);
  
  // Process sensor data (convert to voltage)
  float processedValue = sensorValue * 0.0048828125; // 5V / 1024
  
  // Send data to backend
  Serial.print("Sensor Data: ");
  Serial.print(sensorValue);
  Serial.print(" (");
  Serial.print(processedValue);
  Serial.println("V)");
  
  delay(1000); // 1 second interval
}
