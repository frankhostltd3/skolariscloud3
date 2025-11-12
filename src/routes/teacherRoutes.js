const express = require('express');
const {
  getTeachers,
  getTeacher,
  createTeacher,
  updateTeacher,
  deleteTeacher,
} = require('../controllers/teacherController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, getTeachers)
  .post(protect, authorize('admin'), createTeacher);

router
  .route('/:id')
  .get(protect, getTeacher)
  .put(protect, authorize('admin'), updateTeacher)
  .delete(protect, authorize('admin'), deleteTeacher);

module.exports = router;
