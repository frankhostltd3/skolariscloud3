const Grade = require('../models/Grade');

// @desc    Get all grades
// @route   GET /api/grades
// @access  Private
exports.getGrades = async (req, res) => {
  try {
    const { studentId, courseId } = req.query;
    let query = {};

    if (studentId) query.student = studentId;
    if (courseId) query.course = courseId;

    const grades = await Grade.find(query)
      .populate({
        path: 'student',
        populate: {
          path: 'user',
          select: 'name email',
        },
      })
      .populate('course', 'name code subject')
      .populate('gradedBy', 'name email')
      .sort('-date');

    res.status(200).json({
      success: true,
      count: grades.length,
      data: grades,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Create grade
// @route   POST /api/grades
// @access  Private (Admin, Teacher)
exports.createGrade = async (req, res) => {
  try {
    req.body.gradedBy = req.user.id;
    const grade = await Grade.create(req.body);

    res.status(201).json({
      success: true,
      data: grade,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Update grade
// @route   PUT /api/grades/:id
// @access  Private (Admin, Teacher)
exports.updateGrade = async (req, res) => {
  try {
    const grade = await Grade.findByIdAndUpdate(req.params.id, req.body, {
      new: true,
      runValidators: true,
    });

    if (!grade) {
      return res.status(404).json({
        success: false,
        message: 'Grade not found',
      });
    }

    res.status(200).json({
      success: true,
      data: grade,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Delete grade
// @route   DELETE /api/grades/:id
// @access  Private (Admin)
exports.deleteGrade = async (req, res) => {
  try {
    const grade = await Grade.findByIdAndDelete(req.params.id);

    if (!grade) {
      return res.status(404).json({
        success: false,
        message: 'Grade not found',
      });
    }

    res.status(200).json({
      success: true,
      data: {},
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Get student grade report
// @route   GET /api/grades/report/:studentId
// @access  Private
exports.getGradeReport = async (req, res) => {
  try {
    const { studentId } = req.params;
    const { courseId } = req.query;

    let query = { student: studentId };
    if (courseId) query.course = courseId;

    const grades = await Grade.find(query)
      .populate('course', 'name code subject')
      .sort('course date');

    // Calculate statistics
    const report = {};
    
    grades.forEach((grade) => {
      const courseName = grade.course.name;
      if (!report[courseName]) {
        report[courseName] = {
          course: grade.course,
          grades: [],
          total: 0,
          count: 0,
        };
      }
      report[courseName].grades.push(grade);
      report[courseName].total += grade.percentage;
      report[courseName].count += 1;
    });

    // Calculate averages
    Object.keys(report).forEach((courseName) => {
      report[courseName].average =
        report[courseName].total / report[courseName].count;
    });

    res.status(200).json({
      success: true,
      data: report,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};
