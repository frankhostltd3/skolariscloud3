const mongoose = require('mongoose');

const gradeSchema = new mongoose.Schema({
  student: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Student',
    required: true,
  },
  course: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Course',
    required: true,
  },
  assessmentType: {
    type: String,
    enum: ['quiz', 'assignment', 'midterm', 'final', 'project', 'other'],
    required: true,
  },
  assessmentName: {
    type: String,
    required: true,
  },
  score: {
    type: Number,
    required: true,
  },
  maxScore: {
    type: Number,
    required: true,
  },
  percentage: {
    type: Number,
  },
  grade: {
    type: String,
  },
  remarks: {
    type: String,
  },
  date: {
    type: Date,
    default: Date.now,
  },
  gradedBy: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
});

// Calculate percentage before saving
gradeSchema.pre('save', function (next) {
  this.percentage = (this.score / this.maxScore) * 100;
  
  // Assign letter grade
  if (this.percentage >= 90) this.grade = 'A';
  else if (this.percentage >= 80) this.grade = 'B';
  else if (this.percentage >= 70) this.grade = 'C';
  else if (this.percentage >= 60) this.grade = 'D';
  else this.grade = 'F';
  
  next();
});

module.exports = mongoose.model('Grade', gradeSchema);
