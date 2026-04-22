# PBL409 - Production System

A comprehensive production example with Backend API, Web Interface, and IoT Integration.

## Project Structure

```
PBL409/
├── backend/              # Node.js Backend API
│   ├── api/
│   │   ├── controllers/  # Business logic
│   │   ├── routes/       # API endpoints
│   │   ├── models/       # Database schemas
│   │   └── middlewares/  # Request processing
│   ├── config/           # Configuration files
│   ├── utils/            # Utility functions
│   └── package.json
│
├── web/                  # Web Application
│   ├── public/
│   │   ├── index.html
│   │   ├── assets/       # Images, fonts
│   │   └── css/          # Stylesheets
│   ├── src/
│   │   ├── components/   # Reusable components
│   │   ├── pages/        # Page components
│   │   ├── App.js        # Main app component
│   │   └── main.js       # Entry point
│   └── package.json
│
├── iot/                  # IoT Devices & Firmware
│   ├── firmware/         # Device firmware code
│   ├── drivers/          # Device drivers
│   └── sensors/          # Sensor configurations
│
└── spark/                # Original UI Components
```

## Getting Started

### Backend Setup
```bash
cd backend
npm install
npm run dev
```

### Web Setup
```bash
cd web
npm install
npm run dev
```

### IoT Setup
- Upload firmware to your Arduino/ESP32 board
- Configure sensors in iot/sensors/

## Architecture

- **Backend**: Express.js API with MongoDB
- **Web**: Vanilla JS frontend with Vite
- **IoT**: Arduino/ESP32 firmware for sensor data collection

---
Generated: 2026-04-23
