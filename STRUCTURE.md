# PBL409 Production System - Complete Structure

## Project Overview
A complete production-ready example with Backend API, Web Interface, and IoT Integration.

## Folder Structure

```
PBL409/
├── backend/                          # Node.js Backend API Server
│   ├── api/
│   │   ├── controllers/              # Business logic handlers
│   │   │   └── README.md
│   │   ├── routes/                   # API endpoint definitions
│   │   │   └── README.md
│   │   ├── models/                   # Database schemas
│   │   │   └── README.md
│   │   └── middlewares/              # Request processing
│   │       └── README.md
│   ├── config/                       # Configuration files
│   │   ├── database.js               # Database config
│   │   └── env.js                    # Environment config
│   ├── utils/                        # Utility functions
│   ├── server.js                     # Express server entry point
│   └── package.json                  # Backend dependencies
│
├── web/                              # Web Application (Vanilla JS + Vite)
│   ├── public/
│   │   ├── index.html                # HTML entry point
│   │   ├── css/
│   │   │   └── style.css             # Main stylesheet
│   │   └── assets/
│   │       └── images/               # Image assets
│   ├── src/
│   │   ├── main.js                   # App initialization
│   │   ├── App.js                    # Main component
│   │   ├── components/               # Reusable components
│   │   └── pages/                    # Page components
│   └── package.json                  # Web dependencies
│
├── iot/                              # IoT Devices & Sensors
│   ├── firmware/
│   │   └── main.ino                  # Arduino/ESP32 main sketch
│   ├── drivers/                      # Device drivers
│   ├── sensors/                      # Sensor configurations
│   └── README.md
│
├── spark/                            # Original UI Components
│   ├── dashboard.html
│   ├── validasi.html
│   ├── detail_validasi.html
│   ├── laporan.html
│   ├── profil.html
│   ├── login.html
│   └── daftar.html
│
├── README.md                         # Project documentation
├── STRUCTURE.md                      # This file
├── .env.example                      # Environment template
└── .gitignore                        # Git ignore rules
```

## Key Files Created

### Backend
- `backend/server.js` - Express server with sample routes
- `backend/package.json` - NPM dependencies
- `backend/api/controllers/README.md` - Controller documentation
- `backend/api/routes/README.md` - Route documentation
- `backend/api/models/README.md` - Database model schemas
- `backend/api/middlewares/README.md` - Middleware documentation

### Web
- `web/public/index.html` - HTML entry point
- `web/src/main.js` - App initialization
- `web/src/App.js` - Main React-like component
- `web/public/css/style.css` - Styled components
- `web/package.json` - Frontend dependencies

### IoT
- `iot/firmware/main.ino` - Arduino sketch for sensor data
- `iot/README.md` - IoT setup documentation

### Configuration
- `.env.example` - Environment variables template
- `README.md` - Getting started guide
- `.gitignore` - Git ignore configuration

## Getting Started

### Backend Setup
```bash
cd backend
npm install
cp ../.env.example ../.env
npm run dev      # Development mode
npm start        # Production mode
```

### Web Setup
```bash
cd web
npm install
npm run dev      # Development server
npm run build    # Build for production
```

### IoT Setup
1. Connect Arduino/ESP32 board
2. Open Arduino IDE
3. Load `iot/firmware/main.ino`
4. Select correct board and port
5. Upload firmware
6. Monitor with Serial (9600 baud)

## API Endpoints

Base URL: `http://localhost:5000/api`

### Health Check
- `GET /api/health` - Server status

### IoT
- `GET /api/iot/devices` - List devices
- `POST /api/iot/devices` - Register device
- `GET /api/iot/devices/:id` - Get device
- `POST /api/iot/data` - Submit sensor data

### Future Routes
- Authentication endpoints
- User management
- Data analytics
- Device commands

## Architecture

```
┌─────────────┐
│   Browser   │
│  (Web App)  │
└──────┬──────┘
       │ HTTP/REST
       ▼
┌──────────────────┐
│   Express API    │
│   (Backend)      │
└──────┬───────────┘
       │ MQTT/Serial
       ▼
┌──────────────────┐
│  IoT Devices     │
│  (Sensors/Micro) │
└──────────────────┘
       │
       ▼
┌──────────────────┐
│   Database       │
│  (MongoDB)       │
└──────────────────┘
```

## Technology Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript, Vite
- **Backend**: Node.js, Express.js, MongoDB/Mongoose
- **IoT**: Arduino, ESP32, C/C++
- **Development**: npm, Git

## Environment Variables

```
PORT=5000
NODE_ENV=development
MONGODB_URI=mongodb://localhost:27017/pbl409
JWT_SECRET=your-secret-key
VITE_API_URL=http://localhost:5000/api
IOT_PORT=/dev/ttyUSB0
IOT_BAUD_RATE=9600
```

## Next Steps

1. Implement database models and schemas
2. Create authentication system
3. Build API controllers and routes
4. Develop web components and pages
5. Configure IoT device communication
6. Add data analytics and reporting
7. Deploy to production

---
**Last Updated**: April 23, 2026
