const express = require('express');
const {
  getGrades,
  createGrade,
  updateGrade,
  deleteGrade,
  getGradeReport,
} = require('../controllers/gradeController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, getGrades)
  .post(protect, authorize('admin', 'teacher'), createGrade);

router
  .route('/:id')
  .put(protect, authorize('admin', 'teacher'), updateGrade)
  .delete(protect, authorize('admin'), deleteGrade);

router.get('/report/:studentId', protect, getGradeReport);

module.exports = router;
