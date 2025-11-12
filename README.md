# SkolarisCloud 🎓

## SaaS for School Management & Academics Cloud System

A comprehensive, modern cloud-based School Management System (SMS) built as a SaaS platform. SkolarisCloud provides educational institutions with a complete suite of tools to manage students, teachers, courses, attendance, grades, and more.

## 🌟 Features

### Core Management
- **School Management**: Multi-tenant architecture supporting multiple schools
- **User Management**: Role-based access control (Admin, Teacher, Student, Parent)
- **Student Management**: Complete student information system with profiles and academic tracking
- **Teacher Management**: Staff management with qualifications, subjects, and schedules
- **Course Management**: Create and manage courses with enrollments and schedules

### Academic Features
- **Attendance Tracking**: Real-time attendance marking with statistics
- **Grade Management**: Comprehensive grading system with multiple assessment types
- **Academic Reports**: Student performance reports and analytics
- **Parent Portal**: Parent access to student progress and information

### Technical Features
- **RESTful API**: Complete REST API for all operations
- **JWT Authentication**: Secure token-based authentication
- **Role-Based Authorization**: Fine-grained access control
- **MongoDB Database**: Scalable NoSQL database
- **Multi-tenant Architecture**: Support for multiple schools in single deployment

## 🚀 Quick Start

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (v4.4 or higher)
- npm or yarn

### Installation

1. Clone the repository:
```bash
git clone https://github.com/frankhostltd3/skolariscloud3.git
cd skolariscloud3
```

2. Install dependencies:
```bash
npm install
```

3. Configure environment variables:
```bash
cp .env.example .env
```

Edit `.env` and set your configuration:
```env
PORT=5000
MONGODB_URI=mongodb://localhost:27017/skolariscloud
JWT_SECRET=your_secret_key_here
JWT_EXPIRE=30d
```

4. Start MongoDB (if running locally):
```bash
mongod
```

5. Start the development server:
```bash
npm run dev
```

6. Access the application:
- Frontend: http://localhost:5000
- API: http://localhost:5000/api

## 📚 API Documentation

See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete API reference.

### Quick API Examples

#### Register a User
```bash
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "student"
  }'
```

#### Login
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Get Students (with authentication)
```bash
curl -X GET http://localhost:5000/api/students \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## 🏗️ Project Structure

```
skolariscloud3/
├── src/
│   ├── config/          # Configuration files
│   │   └── database.js  # Database connection
│   ├── controllers/     # Request handlers
│   │   ├── authController.js
│   │   ├── schoolController.js
│   │   ├── studentController.js
│   │   ├── teacherController.js
│   │   ├── courseController.js
│   │   ├── attendanceController.js
│   │   └── gradeController.js
│   ├── middleware/      # Custom middleware
│   │   └── auth.js      # Authentication & authorization
│   ├── models/          # Database models
│   │   ├── User.js
│   │   ├── School.js
│   │   ├── Student.js
│   │   ├── Teacher.js
│   │   ├── Course.js
│   │   ├── Attendance.js
│   │   └── Grade.js
│   ├── routes/          # API routes
│   │   ├── authRoutes.js
│   │   ├── schoolRoutes.js
│   │   ├── studentRoutes.js
│   │   ├── teacherRoutes.js
│   │   ├── courseRoutes.js
│   │   ├── attendanceRoutes.js
│   │   └── gradeRoutes.js
│   └── utils/           # Utility functions
│       └── generateToken.js
├── public/              # Static files
│   └── index.html       # Frontend landing page
├── server.js            # Application entry point
├── package.json         # Dependencies and scripts
├── .env.example         # Environment variables template
└── README.md            # This file
```

## 🔒 Security Features

- Password hashing with bcrypt
- JWT-based authentication
- Role-based access control (RBAC)
- Protected API routes
- Input validation
- Secure password requirements

## 👥 User Roles

1. **Admin**: Full system access, can manage schools, users, and all resources
2. **Teacher**: Can manage students, courses, attendance, and grades
3. **Student**: Can view their own information, courses, and grades
4. **Parent**: Can view their children's information and progress

## 🛠️ Technology Stack

- **Backend**: Node.js, Express.js
- **Database**: MongoDB with Mongoose ODM
- **Authentication**: JWT (JSON Web Tokens)
- **Security**: bcryptjs for password hashing
- **API**: RESTful architecture
- **Frontend**: HTML5, CSS3, JavaScript

## 📊 Database Models

- **User**: Base user model for all user types
- **School**: School/institution information
- **Student**: Student-specific data and relationships
- **Teacher**: Teacher profiles and qualifications
- **Course**: Course details and schedules
- **Attendance**: Daily attendance records
- **Grade**: Assessment and grading records

## 🚦 API Endpoints

### Authentication
- POST `/api/auth/register` - Register new user
- POST `/api/auth/login` - Login user
- GET `/api/auth/me` - Get current user

### Schools
- GET `/api/schools` - Get all schools (Admin)
- POST `/api/schools` - Create school (Admin)
- GET `/api/schools/:id` - Get school by ID
- PUT `/api/schools/:id` - Update school (Admin)
- DELETE `/api/schools/:id` - Delete school (Admin)

### Students
- GET `/api/students` - Get all students
- POST `/api/students` - Create student (Admin/Teacher)
- GET `/api/students/:id` - Get student by ID
- PUT `/api/students/:id` - Update student (Admin/Teacher)
- DELETE `/api/students/:id` - Delete student (Admin)

### Teachers
- GET `/api/teachers` - Get all teachers
- POST `/api/teachers` - Create teacher (Admin)
- GET `/api/teachers/:id` - Get teacher by ID
- PUT `/api/teachers/:id` - Update teacher (Admin)
- DELETE `/api/teachers/:id` - Delete teacher (Admin)

### Courses
- GET `/api/courses` - Get all courses
- POST `/api/courses` - Create course (Admin/Teacher)
- GET `/api/courses/:id` - Get course by ID
- PUT `/api/courses/:id` - Update course (Admin/Teacher)
- DELETE `/api/courses/:id` - Delete course (Admin)
- POST `/api/courses/:id/enroll` - Enroll student (Admin/Teacher)

### Attendance
- GET `/api/attendance` - Get attendance records
- POST `/api/attendance` - Mark attendance (Admin/Teacher)
- PUT `/api/attendance/:id` - Update attendance (Admin/Teacher)
- GET `/api/attendance/stats/:studentId` - Get attendance statistics

### Grades
- GET `/api/grades` - Get all grades
- POST `/api/grades` - Create grade (Admin/Teacher)
- PUT `/api/grades/:id` - Update grade (Admin/Teacher)
- DELETE `/api/grades/:id` - Delete grade (Admin)
- GET `/api/grades/report/:studentId` - Get student grade report

## 📝 Scripts

- `npm start` - Start production server
- `npm run dev` - Start development server with nodemon
- `npm test` - Run tests (to be implemented)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the ISC License.

## 👨‍💻 Author

FrankHost Ltd

## 🙏 Acknowledgments

- Built with modern Node.js and Express.js
- MongoDB for flexible data storage
- JWT for secure authentication
- RESTful API design principles

## 📞 Support

For support, please open an issue in the GitHub repository.
