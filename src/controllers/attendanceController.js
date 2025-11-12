const Attendance = require('../models/Attendance');

// @desc    Get all attendance records
// @route   GET /api/attendance
// @access  Private
exports.getAttendance = async (req, res) => {
  try {
    const { studentId, courseId, startDate, endDate } = req.query;
    let query = {};

    if (studentId) query.student = studentId;
    if (courseId) query.course = courseId;
    if (startDate || endDate) {
      query.date = {};
      if (startDate) query.date.$gte = new Date(startDate);
      if (endDate) query.date.$lte = new Date(endDate);
    }

    const attendance = await Attendance.find(query)
      .populate('student')
      .populate({
        path: 'student',
        populate: {
          path: 'user',
          select: 'name email',
        },
      })
      .populate('course', 'name code')
      .populate('markedBy', 'name email')
      .sort('-date');

    res.status(200).json({
      success: true,
      count: attendance.length,
      data: attendance,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Mark attendance
// @route   POST /api/attendance
// @access  Private (Admin, Teacher)
exports.markAttendance = async (req, res) => {
  try {
    req.body.markedBy = req.user.id;
    const attendance = await Attendance.create(req.body);

    res.status(201).json({
      success: true,
      data: attendance,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Update attendance
// @route   PUT /api/attendance/:id
// @access  Private (Admin, Teacher)
exports.updateAttendance = async (req, res) => {
  try {
    const attendance = await Attendance.findByIdAndUpdate(
      req.params.id,
      req.body,
      {
        new: true,
        runValidators: true,
      }
    );

    if (!attendance) {
      return res.status(404).json({
        success: false,
        message: 'Attendance record not found',
      });
    }

    res.status(200).json({
      success: true,
      data: attendance,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

// @desc    Get attendance statistics
// @route   GET /api/attendance/stats/:studentId
// @access  Private
exports.getAttendanceStats = async (req, res) => {
  try {
    const { studentId } = req.params;
    const { startDate, endDate } = req.query;

    let query = { student: studentId };
    if (startDate || endDate) {
      query.date = {};
      if (startDate) query.date.$gte = new Date(startDate);
      if (endDate) query.date.$lte = new Date(endDate);
    }

    const attendance = await Attendance.find(query);

    const stats = {
      total: attendance.length,
      present: attendance.filter((a) => a.status === 'present').length,
      absent: attendance.filter((a) => a.status === 'absent').length,
      late: attendance.filter((a) => a.status === 'late').length,
      excused: attendance.filter((a) => a.status === 'excused').length,
    };

    stats.attendancePercentage =
      stats.total > 0 ? ((stats.present + stats.late) / stats.total) * 100 : 0;

    res.status(200).json({
      success: true,
      data: stats,
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};
