const mongoose = require('mongoose');

const teacherSchema = new mongoose.Schema({
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true,
  },
  school: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'School',
    required: true,
  },
  employeeId: {
    type: String,
    required: true,
    unique: true,
  },
  subjects: [{
    type: String,
  }],
  qualification: {
    type: String,
  },
  experience: {
    type: Number,
  },
  joiningDate: {
    type: Date,
    default: Date.now,
  },
  salary: {
    type: Number,
  },
  designation: {
    type: String,
  },
  department: {
    type: String,
  },
  isActive: {
    type: Boolean,
    default: true,
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
});

module.exports = mongoose.model('Teacher', teacherSchema);
