# API Routes

RESTful API endpoint definitions and routing configuration.

## Route Modules

### authRoutes.js
Authentication and authorization endpoints
- POST /auth/register - User registration
- POST /auth/login - User login
- POST /auth/logout - User logout
- POST /auth/refresh-token - Token refresh

### userRoutes.js
User management endpoints
- GET /users - List all users
- GET /users/:id - Get user by ID
- PUT /users/:id - Update user
- DELETE /users/:id - Delete user

### iotRoutes.js
IoT device management
- GET /iot/devices - List devices
- POST /iot/devices - Register device
- GET /iot/devices/:id - Get device details
- GET /iot/devices/:id/data - Get device sensor data
- POST /iot/devices/:id/command - Send command to device

### dataRoutes.js
Sensor data retrieval and analytics
- GET /data/sensors - List all sensors
- GET /data/sensors/:id - Get sensor readings
- POST /data/analytics - Analyze sensor data

## Usage

Import and register routes in server.js:
```javascript
const authRoutes = require('./api/routes/authRoutes');
const userRoutes = require('./api/routes/userRoutes');
const iotRoutes = require('./api/routes/iotRoutes');

app.use('/api', authRoutes);
app.use('/api', userRoutes);
app.use('/api', iotRoutes);
```
