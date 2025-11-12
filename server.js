const express = require('express');
const dotenv = require('dotenv');
const cors = require('cors');
const helmet = require('helmet');
const mongoSanitize = require('express-mongo-sanitize');
const connectDB = require('./src/config/database');
const { apiLimiter } = require('./src/middleware/rateLimiter');

// Load env vars
dotenv.config();

// Connect to database
connectDB();

const app = express();

// Set security HTTP headers
app.use(helmet());

// Body parser
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Sanitize data to prevent NoSQL injection
app.use(mongoSanitize());

// Enable CORS
app.use(cors());

// Apply rate limiting to all API routes
app.use('/api/', apiLimiter);

// Serve static files
app.use(express.static('public'));

// Route files
const authRoutes = require('./src/routes/authRoutes');
const schoolRoutes = require('./src/routes/schoolRoutes');
const studentRoutes = require('./src/routes/studentRoutes');
const teacherRoutes = require('./src/routes/teacherRoutes');
const courseRoutes = require('./src/routes/courseRoutes');
const attendanceRoutes = require('./src/routes/attendanceRoutes');
const gradeRoutes = require('./src/routes/gradeRoutes');

// Mount routers
app.use('/api/auth', authRoutes);
app.use('/api/schools', schoolRoutes);
app.use('/api/students', studentRoutes);
app.use('/api/teachers', teacherRoutes);
app.use('/api/courses', courseRoutes);
app.use('/api/attendance', attendanceRoutes);
app.use('/api/grades', gradeRoutes);

// Welcome route
app.get('/', (req, res) => {
  res.json({
    message: 'Welcome to SkolarisCloud - School Management & Academics SaaS',
    version: '1.0.0',
    endpoints: {
      auth: '/api/auth',
      schools: '/api/schools',
      students: '/api/students',
      teachers: '/api/teachers',
      courses: '/api/courses',
      attendance: '/api/attendance',
      grades: '/api/grades',
    },
  });
});

const PORT = process.env.PORT || 5000;

const server = app.listen(PORT, () => {
  console.log(`Server running in ${process.env.NODE_ENV || 'development'} mode on port ${PORT}`);
});

// Handle unhandled promise rejections
process.on('unhandledRejection', (err, promise) => {
  console.log(`Error: ${err.message}`);
  // Close server & exit process
  server.close(() => process.exit(1));
});

module.exports = app;
