# API Controllers

Business logic handlers for API requests and responses.

## Controller Files

### authController.js
Authentication logic
- register(req, res) - Handle user registration
- login(req, res) - Handle user login
- logout(req, res) - Handle user logout
- refreshToken(req, res) - Refresh JWT token
- verifyEmail(req, res) - Email verification

### userController.js
User management logic
- getAllUsers(req, res) - Fetch all users
- getUserById(req, res) - Fetch user by ID
- updateUser(req, res) - Update user profile
- deleteUser(req, res) - Delete user account

### iotController.js
IoT device management
- registerDevice(req, res) - Register new device
- getDevices(req, res) - List all devices
- getDeviceData(req, res) - Get sensor data
- updateDevice(req, res) - Update device config
- sendCommand(req, res) - Send command to device

### dataController.js
Sensor data handling
- getSensorData(req, res) - Get raw sensor data
- getAnalytics(req, res) - Get aggregated analytics
- generateReport(req, res) - Generate data report

## Example Controller

```javascript
// controllers/authController.js
exports.login = async (req, res) => {
  try {
    const { email, password } = req.body;
    
    // Validate input
    if (!email || !password) {
      return res.status(400).json({ error: 'Missing credentials' });
    }
    
    // Find user
    const user = await User.findOne({ email });
    if (!user) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }
    
    // Create token
    const token = generateToken(user);
    
    res.json({ token, user });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
```
