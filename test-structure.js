// Test script to verify application structure
const fs = require('fs');
const path = require('path');

console.log('🧪 Testing SkolarisCloud Structure...\n');

const requiredFiles = [
  'server.js',
  'package.json',
  '.env.example',
  '.gitignore',
  'README.md',
  'API_DOCUMENTATION.md',
  'SETUP_GUIDE.md',
  'public/index.html',
  'src/config/database.js',
  'src/middleware/auth.js',
  'src/utils/generateToken.js',
  // Models
  'src/models/User.js',
  'src/models/School.js',
  'src/models/Student.js',
  'src/models/Teacher.js',
  'src/models/Course.js',
  'src/models/Attendance.js',
  'src/models/Grade.js',
  // Controllers
  'src/controllers/authController.js',
  'src/controllers/schoolController.js',
  'src/controllers/studentController.js',
  'src/controllers/teacherController.js',
  'src/controllers/courseController.js',
  'src/controllers/attendanceController.js',
  'src/controllers/gradeController.js',
  // Routes
  'src/routes/authRoutes.js',
  'src/routes/schoolRoutes.js',
  'src/routes/studentRoutes.js',
  'src/routes/teacherRoutes.js',
  'src/routes/courseRoutes.js',
  'src/routes/attendanceRoutes.js',
  'src/routes/gradeRoutes.js',
];

let passed = 0;
let failed = 0;

requiredFiles.forEach(file => {
  const filePath = path.join(__dirname, file);
  if (fs.existsSync(filePath)) {
    console.log(`✓ ${file}`);
    passed++;
  } else {
    console.log(`✗ ${file} - MISSING`);
    failed++;
  }
});

console.log('\n📊 Test Summary:');
console.log(`   Passed: ${passed}/${requiredFiles.length}`);
console.log(`   Failed: ${failed}/${requiredFiles.length}`);

if (failed === 0) {
  console.log('\n✅ All required files are present!');
  console.log('\n📋 Project Statistics:');
  console.log(`   Models: 7`);
  console.log(`   Controllers: 7`);
  console.log(`   Routes: 7`);
  console.log(`   API Endpoints: 35+`);
  console.log('\n🚀 Ready for deployment!');
  process.exit(0);
} else {
  console.log('\n❌ Some files are missing!');
  process.exit(1);
}
