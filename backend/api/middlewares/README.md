# Middlewares

Request processing and validation middlewares.

## Middleware Files

### authMiddleware.js
JWT authentication verification
- verifyToken() - Validate JWT tokens
- requireAuth() - Route protection middleware

### errorHandler.js
Global error handling
- errorHandler() - Centralized error handler
- asyncHandler() - Async error wrapper

### validator.js
Request validation
- validateEmail() - Email format validation
- validatePassword() - Password strength validation
- validateDeviceData() - Sensor data validation

### cors.js
CORS configuration
- corsOptions - CORS policy settings
- corsMiddleware() - Apply CORS rules

## Usage Example

```javascript
// In server.js
const authMiddleware = require('./api/middlewares/authMiddleware');
const errorHandler = require('./api/middlewares/errorHandler');

// Apply auth to protected routes
app.get('/api/protected', authMiddleware.requireAuth, controller);

// Global error handler (last middleware)
app.use(errorHandler.errorHandler);
```
