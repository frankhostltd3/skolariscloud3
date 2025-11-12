const mongoose = require('mongoose');

const studentSchema = new mongoose.Schema({
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
  studentId: {
    type: String,
    required: true,
    unique: true,
  },
  grade: {
    type: String,
    required: [true, 'Please add a grade'],
  },
  section: {
    type: String,
  },
  rollNumber: {
    type: String,
  },
  admissionDate: {
    type: Date,
    default: Date.now,
  },
  parents: [{
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
  }],
  bloodGroup: {
    type: String,
  },
  emergencyContact: {
    name: String,
    phone: String,
    relation: String,
  },
  previousSchool: {
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

module.exports = mongoose.model('Student', studentSchema);
