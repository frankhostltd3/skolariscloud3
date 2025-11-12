# SkolarisCloud API Documentation

## Overview
SkolarisCloud is a comprehensive School Management & Academics Cloud SaaS system providing RESTful APIs for managing educational institutions.

## Base URL
```
http://localhost:5000/api
```

## Authentication
The API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:
```
Authorization: Bearer <token>
```

## API Endpoints

### Authentication
#### Register User
```
POST /api/auth/register
```
**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "student",
  "phone": "1234567890",
  "address": "123 Main St",
  "dateOfBirth": "2000-01-01",
  "school": "schoolId"
}
```

#### Login
```
POST /api/auth/login
```
**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Get Current User
```
GET /api/auth/me
```
**Headers:** `Authorization: Bearer <token>`

---

### Schools
#### Get All Schools
```
GET /api/schools
```
**Access:** Admin only

#### Get Single School
```
GET /api/schools/:id
```

#### Create School
```
POST /api/schools
```
**Access:** Admin only
**Body:**
```json
{
  "name": "Example High School",
  "address": "456 School Ave",
  "phone": "9876543210",
  "email": "info@examplehs.edu",
  "principal": "Dr. Smith",
  "establishedYear": 1995,
  "subscription": {
    "plan": "premium",
    "endDate": "2025-12-31"
  }
}
```

#### Update School
```
PUT /api/schools/:id
```
**Access:** Admin only

#### Delete School
```
DELETE /api/schools/:id
```
**Access:** Admin only

---

### Students
#### Get All Students
```
GET /api/students
```

#### Get Single Student
```
GET /api/students/:id
```

#### Create Student
```
POST /api/students
```
**Access:** Admin, Teacher
**Body:**
```json
{
  "user": "userId",
  "school": "schoolId",
  "studentId": "STU001",
  "grade": "10",
  "section": "A",
  "rollNumber": "101",
  "bloodGroup": "O+",
  "emergencyContact": {
    "name": "Parent Name",
    "phone": "1234567890",
    "relation": "Father"
  }
}
```

#### Update Student
```
PUT /api/students/:id
```
**Access:** Admin, Teacher

#### Delete Student
```
DELETE /api/students/:id
```
**Access:** Admin only

---

### Teachers
#### Get All Teachers
```
GET /api/teachers
```

#### Get Single Teacher
```
GET /api/teachers/:id
```

#### Create Teacher
```
POST /api/teachers
```
**Access:** Admin only
**Body:**
```json
{
  "user": "userId",
  "school": "schoolId",
  "employeeId": "EMP001",
  "subjects": ["Mathematics", "Physics"],
  "qualification": "M.Sc. Mathematics",
  "experience": 5,
  "designation": "Senior Teacher",
  "department": "Science",
  "salary": 50000
}
```

#### Update Teacher
```
PUT /api/teachers/:id
```
**Access:** Admin only

#### Delete Teacher
```
DELETE /api/teachers/:id
```
**Access:** Admin only

---

### Courses
#### Get All Courses
```
GET /api/courses
```

#### Get Single Course
```
GET /api/courses/:id
```

#### Create Course
```
POST /api/courses
```
**Access:** Admin, Teacher
**Body:**
```json
{
  "name": "Advanced Mathematics",
  "code": "MATH101",
  "school": "schoolId",
  "grade": "10",
  "subject": "Mathematics",
  "teacher": "teacherId",
  "description": "Advanced mathematics course",
  "credits": 4,
  "schedule": [
    {
      "day": "Monday",
      "startTime": "09:00",
      "endTime": "10:00",
      "room": "101"
    }
  ]
}
```

#### Update Course
```
PUT /api/courses/:id
```
**Access:** Admin, Teacher

#### Delete Course
```
DELETE /api/courses/:id
```
**Access:** Admin only

#### Enroll Student
```
POST /api/courses/:id/enroll
```
**Access:** Admin, Teacher
**Body:**
```json
{
  "studentId": "studentId"
}
```

---

### Attendance
#### Get Attendance Records
```
GET /api/attendance?studentId=xxx&courseId=xxx&startDate=2024-01-01&endDate=2024-12-31
```

#### Mark Attendance
```
POST /api/attendance
```
**Access:** Admin, Teacher
**Body:**
```json
{
  "student": "studentId",
  "course": "courseId",
  "date": "2024-01-15",
  "status": "present",
  "remarks": "On time"
}
```

#### Update Attendance
```
PUT /api/attendance/:id
```
**Access:** Admin, Teacher

#### Get Attendance Statistics
```
GET /api/attendance/stats/:studentId?startDate=2024-01-01&endDate=2024-12-31
```

---

### Grades
#### Get All Grades
```
GET /api/grades?studentId=xxx&courseId=xxx
```

#### Create Grade
```
POST /api/grades
```
**Access:** Admin, Teacher
**Body:**
```json
{
  "student": "studentId",
  "course": "courseId",
  "assessmentType": "midterm",
  "assessmentName": "Midterm Exam",
  "score": 85,
  "maxScore": 100,
  "remarks": "Good performance"
}
```

#### Update Grade
```
PUT /api/grades/:id
```
**Access:** Admin, Teacher

#### Delete Grade
```
DELETE /api/grades/:id
```
**Access:** Admin only

#### Get Grade Report
```
GET /api/grades/report/:studentId?courseId=xxx
```

---

## User Roles
- **admin**: Full system access
- **teacher**: Can manage students, courses, attendance, and grades
- **student**: Can view their own information
- **parent**: Can view their children's information

## Status Codes
- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request data
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `500 Internal Server Error`: Server error

## Response Format
All API responses follow this format:
```json
{
  "success": true,
  "data": {},
  "message": "Optional message"
}
```

## Error Response Format
```json
{
  "success": false,
  "message": "Error message"
}
```
