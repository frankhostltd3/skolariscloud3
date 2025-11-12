const express = require('express');
const {
  getSchools,
  getSchool,
  createSchool,
  updateSchool,
  deleteSchool,
} = require('../controllers/schoolController');
const { protect, authorize } = require('../middleware/auth');

const router = express.Router();

router
  .route('/')
  .get(protect, authorize('admin'), getSchools)
  .post(protect, authorize('admin'), createSchool);

router
  .route('/:id')
  .get(protect, getSchool)
  .put(protect, authorize('admin'), updateSchool)
  .delete(protect, authorize('admin'), deleteSchool);

module.exports = router;
