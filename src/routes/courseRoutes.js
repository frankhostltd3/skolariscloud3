const express = require('express');
const {
  getCourses,
  getCourse,
  createCourse,
  updateCourse,
  deleteCourse,
  enrollStudent,
} = require('../controllers/courseController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, getCourses)
  .post(protect, authorize('admin', 'teacher'), createCourse);

router
  .route('/:id')
  .get(protect, getCourse)
  .put(protect, authorize('admin', 'teacher'), updateCourse)
  .delete(protect, authorize('admin'), deleteCourse);

router.post(
  '/:id/enroll',
  protect,
  authorize('admin', 'teacher'),
  enrollStudent
);

module.exports = router;
