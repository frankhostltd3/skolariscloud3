const mongoose = require('mongoose');

const schoolSchema = new mongoose.Schema({
  name: {
    type: String,
    required: [true, 'Please add a school name'],
    unique: true,
  },
  address: {
    type: String,
    required: [true, 'Please add an address'],
  },
  phone: {
    type: String,
    required: [true, 'Please add a phone number'],
  },
  email: {
    type: String,
    required: [true, 'Please add an email'],
    unique: true,
  },
  website: {
    type: String,
  },
  principal: {
    type: String,
  },
  establishedYear: {
    type: Number,
  },
  logo: {
    type: String,
  },
  isActive: {
    type: Boolean,
    default: true,
  },
  subscription: {
    plan: {
      type: String,
      enum: ['basic', 'premium', 'enterprise'],
      default: 'basic',
    },
    startDate: {
      type: Date,
      default: Date.now,
    },
    endDate: {
      type: Date,
    },
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
});

module.exports = mongoose.model('School', schoolSchema);
