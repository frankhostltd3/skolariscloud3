const express = require('express');
const {
  getStudents,
  getStudent,
  createStudent,
  updateStudent,
  deleteStudent,
} = require('../controllers/studentController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, getStudents)
  .post(protect, authorize('admin', 'teacher'), createStudent);

router
  .route('/:id')
  .get(protect, getStudent)
  .put(protect, authorize('admin', 'teacher'), updateStudent)
  .delete(protect, authorize('admin'), deleteStudent);

module.exports = router;
