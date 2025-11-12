const express = require('express');
const {
  getAttendance,
  markAttendance,
  updateAttendance,
  getAttendanceStats,
} = require('../controllers/attendanceController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, getAttendance)
  .post(protect, authorize('admin', 'teacher'), markAttendance);

router
  .route('/:id')
  .put(protect, authorize('admin', 'teacher'), updateAttendance);

router.get('/stats/:studentId', protect, getAttendanceStats);

module.exports = router;
