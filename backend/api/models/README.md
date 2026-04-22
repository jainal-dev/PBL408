# Database Models

Database schema definitions for MongoDB collections.

## Models to Create

### User.js
User authentication and profile information
```javascript
{
  _id: ObjectId,
  username: String,
  email: String,
  password: String (hashed),
  role: String (admin, user, viewer),
  createdAt: Date,
  updatedAt: Date
}
```

### IoTDevice.js
Connected IoT device information
```javascript
{
  _id: ObjectId,
  deviceId: String,
  name: String,
  type: String (sensor, actuator, gateway),
  status: String (active, inactive),
  location: String,
  owner: ObjectId (Reference to User),
  createdAt: Date
}
```

### SensorData.js
Time-series sensor measurements
```javascript
{
  _id: ObjectId,
  deviceId: String,
  sensorType: String,
  value: Number,
  unit: String,
  timestamp: Date
}
```

### Log.js
Activity and audit logs
```javascript
{
  _id: ObjectId,
  action: String,
  userId: ObjectId,
  deviceId: String,
  details: Object,
  timestamp: Date
}
```
