# Assignment System - Access Guide

## 📍 How to Access the Assignment System

### 🎓 FOR TEACHERS

#### **Primary Access Route:**
1. **Login** → Teacher Dashboard → **Classroom** section → **Exercises & Assignments**

#### **Step-by-Step Navigation:**

1. **Login to the system** at: `https://yourschool.example.com/login`
   - Use your teacher credentials

2. **Teacher Dashboard** (After login):
   - URL: `https://yourschool.example.com/teacher/dashboard`
   - You'll see assignment statistics on your dashboard

3. **Classroom Hub** (Main classroom page):
   - Click **"Classroom"** in the sidebar menu
   - URL: `https://yourschool.example.com/teacher/classroom`
   - You'll see a card: **"Exercises & Assignments"**

4. **Assignments Management** (Main assignment interface):
   - Click **"All Assignments"** button
   - URL: `https://yourschool.example.com/teacher/classroom/exercises`
   - This is your **MAIN ASSIGNMENT CENTER**

#### **Teacher URLs (Quick Reference):**

| Feature | URL Path | Description |
|---------|----------|-------------|
| **Assignment List** | `/teacher/classroom/exercises` | View all assignments |
| **Create Assignment** | `/teacher/classroom/exercises/create` | Create new assignment |
| **View Assignment** | `/teacher/classroom/exercises/{id}` | View specific assignment details |
| **Edit Assignment** | `/teacher/classroom/exercises/{id}/edit` | Edit assignment |
| **View Submissions** | `/teacher/classroom/exercises/{id}/submissions` | View student submissions |
| **Analytics Dashboard** | `/teacher/classroom/exercises/{id}/analytics` | View assignment analytics |
| **Export Data** | `/teacher/classroom/exercises/{id}/export` | Export assignment data (CSV/PDF) |

#### **Teacher Menu Structure:**
```
Teacher Dashboard
└── Classroom
    ├── Virtual Classes
    ├── Learning Materials
    └── Exercises & Assignments ← YOU ARE HERE
        ├── All Assignments (Index)
        ├── Create New Assignment
        ├── Active Assignments
        ├── Draft Assignments
        └── Archived Assignments
```

#### **What Teachers Can Do:**

✅ **Assignment Management:**
- Create assignments with questions
- Upload attachments (PDFs, docs, images)
- Set due dates and maximum scores
- Add rubrics for detailed grading
- Enable plagiarism checking
- Enable peer review (coming soon)
- Set late submission penalties
- Draft, publish, archive assignments

✅ **Submission Management:**
- View all student submissions
- Grade submissions manually
- Auto-grade objective questions
- Provide written feedback
- Download student submissions
- Bulk grade multiple submissions

✅ **Analytics & Reporting:**
- View submission statistics
- See score distribution
- Check completion rates
- Identify struggling students
- Export data to Excel/PDF
- Check plagiarism reports

✅ **Advanced Features:**
- Send reminders to students who haven't submitted
- Duplicate assignments (reuse for next term)
- Archive old assignments
- Reopen closed assignments
- View detailed analytics per assignment

---

### 👨‍🎓 FOR STUDENTS

#### **Primary Access Route:**
1. **Login** → Student Dashboard → **Classroom** → **Assignments**

#### **Step-by-Step Navigation:**

1. **Login to the system** at: `https://yourschool.example.com/login`
   - Use your student credentials

2. **Student Dashboard** (After login):
   - URL: `https://yourschool.example.com/student/dashboard`
   - You'll see pending assignments on your dashboard

3. **Classroom Hub** (Main classroom page):
   - Click **"Classroom"** in the sidebar menu
   - URL: `https://yourschool.example.com/student/classroom`
   - You'll see: **"Assignments & Homework"** section

4. **View All Assignments**:
   - Click **"View All Assignments"** button
   - URL: `https://yourschool.example.com/student/classroom/exercises`
   - This is your **ASSIGNMENT CENTER**

#### **Student URLs (Quick Reference):**

| Feature | URL Path | Description |
|---------|----------|-------------|
| **Assignment List** | `/student/classroom/exercises` | View all your assignments |
| **View Assignment** | `/student/classroom/exercises/{id}` | View assignment details |
| **Submit Assignment** | `/student/classroom/exercises/{id}` (scroll down) | Submit your work |
| **My Grades** | `/student/classroom/exercises/grades` | View all your grades |

#### **Student Menu Structure:**
```
Student Dashboard
└── Classroom
    ├── Virtual Classes
    ├── Today's Classes
    ├── Materials
    └── Assignments ← YOU ARE HERE
        ├── All Assignments (Index)
        ├── Pending Assignments (filter)
        ├── Submitted Assignments (filter)
        ├── Graded Assignments (filter)
        ├── Overdue Assignments (filter)
        └── My Grades
```

#### **What Students Can Do:**

✅ **View Assignments:**
- See all assignments from teachers
- Filter by status (pending/submitted/graded/overdue)
- View assignment details (questions, due date, max score)
- Download teacher attachments
- See rubrics (grading criteria)

✅ **Submit Work:**
- Submit text answers
- Upload files (PDFs, Word docs, images, etc.)
- Submit before due date
- Resubmit if teacher allows
- See late submission penalties

✅ **Track Progress:**
- View submission status (pending/submitted/graded)
- See grades and feedback from teacher
- View score percentages
- Download graded submissions
- Check overdue assignments

✅ **Grades & Performance:**
- View all grades in one place
- See average scores
- Track completion rates
- View teacher feedback
- Download graded work

---

### 👔 FOR ADMINISTRATORS

#### **Primary Access Route:**
Admins have **VIEW-ONLY** access to monitor the system.

#### **What Admins Access:**

1. **Teacher Assignment Reports** (View-only):
   - URL: `/admin/reports/assignments` (if route exists)
   - View system-wide assignment statistics
   - Monitor teacher activity
   - Track student engagement

2. **Admin Dashboard**:
   - URL: `/admin/dashboard`
   - See overview metrics:
     - Total assignments created
     - Submission rates
     - Average grades
     - Active assignments

3. **Settings Configuration**:
   - URL: `/admin/settings`
   - Configure system-wide assignment settings:
     - File upload limits
     - Plagiarism detection settings
     - Notification preferences
     - Automation settings

#### **What Admins CANNOT Do:**
❌ Create assignments (teacher-only)
❌ Grade student submissions (teacher-only)
❌ Submit assignments (student-only)
❌ View individual assignment content (unless granted special permission)

#### **Admin Monitoring Capabilities:**

✅ **System Monitoring:**
- View total assignments across all classes
- Monitor submission rates
- Track grading completion
- Identify inactive teachers/students
- Export system-wide reports

✅ **Settings Management:**
- Configure file upload size limits
- Enable/disable plagiarism checking
- Set notification schedules
- Configure late submission policies
- Manage automation settings

---

## 🚀 Quick Access URLs Summary

### **Teacher Panel:**
```
Base URL: https://yourschool.example.com

Main Assignment Page:
/teacher/classroom/exercises

Create New:
/teacher/classroom/exercises/create

View Submissions:
/teacher/classroom/exercises/{id}/submissions

Analytics:
/teacher/classroom/exercises/{id}/analytics
```

### **Student Panel:**
```
Base URL: https://yourschool.example.com

Main Assignment Page:
/student/classroom/exercises

View Specific Assignment:
/student/classroom/exercises/{id}

My Grades:
/student/classroom/exercises/grades
```

### **Admin Panel:**
```
Base URL: https://yourschool.example.com

Dashboard:
/admin/dashboard

Settings:
/admin/settings

Reports:
/admin/reports
```

---

## 📱 User Journey Examples

### **Teacher Journey: Creating and Grading an Assignment**

1. Login → Teacher Dashboard
2. Click **"Classroom"** in sidebar
3. Click **"Exercises & Assignments"** card
4. Click **"Create New Assignment"** button (green button, top-right)
5. Fill in assignment details:
   - Title: "Mathematics Quiz 1"
   - Subject: Mathematics
   - Class: Grade 10 A
   - Instructions: "Solve all questions"
   - Due Date: Next Friday
   - Max Score: 100
   - Questions: Add 10 questions
   - Upload attachments (optional)
6. Click **"Create Assignment"** → Assignment is now live!
7. Students receive email notifications
8. Wait for submissions...
9. Go to **"View Submissions"** (from assignment detail page)
10. Grade each submission manually or use **"Auto-Grade"** for objective questions
11. Provide feedback
12. Click **"Save Grade"** → Student receives email notification
13. View **"Analytics"** to see class performance

### **Student Journey: Completing an Assignment**

1. Login → Student Dashboard
2. See notification: **"New assignment: Mathematics Quiz 1"**
3. Click **"Classroom"** in sidebar
4. Click **"Assignments"**
5. See **"Mathematics Quiz 1"** in list (status: Pending, due in 5 days)
6. Click on assignment to view details
7. Read questions and teacher instructions
8. Download any attachments (if provided)
9. Scroll down to **"Submit Your Work"** section
10. Answer all questions in text boxes
11. Upload files (if needed)
12. Click **"Submit Assignment"** → Confirmation message appears
13. Status changes to **"Submitted"**
14. Wait for grading...
15. Receive email: **"Your assignment has been graded"**
16. Go to **"My Grades"** to view score and feedback

### **Admin Journey: Monitoring the System**

1. Login → Admin Dashboard
2. See statistics:
   - **120 Active Assignments**
   - **85% Submission Rate**
   - **90% Graded**
3. Click **"Reports"** → **"Assignments"**
4. View system-wide statistics:
   - Total assignments by subject
   - Average grades by class
   - Teacher activity
   - Student engagement
5. Export data to Excel for management review
6. Check **"Settings"** to adjust system configurations

---

## 🎯 Role Permissions Summary

| Action | Teacher | Student | Admin |
|--------|---------|---------|-------|
| **Create Assignment** | ✅ Yes | ❌ No | ❌ No |
| **Edit Assignment** | ✅ Yes (own) | ❌ No | ❌ No |
| **Delete Assignment** | ✅ Yes (own) | ❌ No | ❌ No |
| **View Assignment** | ✅ Yes (own) | ✅ Yes (own class) | ✅ Yes (all) |
| **Submit Assignment** | ❌ No | ✅ Yes | ❌ No |
| **Grade Submission** | ✅ Yes (own assignments) | ❌ No | ❌ No |
| **View Submissions** | ✅ Yes (own assignments) | ✅ Yes (own only) | ✅ Yes (reports) |
| **View Analytics** | ✅ Yes (own assignments) | ✅ Yes (own grades) | ✅ Yes (system-wide) |
| **Export Data** | ✅ Yes (own assignments) | ❌ No | ✅ Yes (system-wide) |
| **Configure Settings** | ❌ No | ❌ No | ✅ Yes |
| **Send Reminders** | ✅ Yes | ❌ No | ❌ No |
| **Duplicate Assignment** | ✅ Yes | ❌ No | ❌ No |
| **Archive Assignment** | ✅ Yes | ❌ No | ❌ No |

---

## 🔔 Notification Flow

### **Students Receive Notifications When:**
- ✉️ New assignment is created
- ✉️ Assignment is graded
- ✉️ Reminder sent for pending assignments
- ✉️ Due date is approaching (24 hours before)

### **Teachers Receive Notifications When:**
- ✉️ Student submits assignment
- ✉️ All students have submitted
- ✉️ Assignment deadline passes

### **Admins Receive Notifications When:**
- ✉️ Weekly system summary (optional)
- ✉️ Low submission rates detected (optional)

---

## 📊 Dashboard Widgets

### **Teacher Dashboard Shows:**
- Total assignments created
- Active assignments
- Pending grades count
- Submission rate (%)
- Recent submissions (last 5)

### **Student Dashboard Shows:**
- Pending assignments (due soon)
- Recently graded assignments
- Average score
- Completion rate (%)
- Overdue assignments (if any)

### **Admin Dashboard Shows:**
- Total assignments (system-wide)
- Average submission rate
- Average grades
- Teacher engagement
- Student engagement

---

## 💡 Pro Tips

### **For Teachers:**
- 📝 Use **rubrics** for consistent grading across students
- 🔄 **Duplicate** assignments to reuse for next term/class
- 📊 Check **analytics** to identify struggling students early
- ⏰ Enable **reminders** to increase submission rates
- 🔍 Use **plagiarism detection** for essay-type assignments
- 📤 **Export** data for record-keeping and reporting

### **For Students:**
- ⏰ Check assignments daily to avoid missing deadlines
- 📥 Download teacher attachments before starting work
- 💾 Save your work frequently before submitting
- ✅ Double-check all questions before submitting
- 📧 Enable email notifications to stay updated
- 📊 Review graded assignments to learn from feedback

### **For Admins:**
- 📈 Monitor submission rates to identify system issues
- 🔧 Adjust settings based on teacher feedback
- 📊 Generate weekly reports for management
- 🎓 Train teachers on advanced features
- 🔔 Configure notification schedules to avoid spam

---

## 🆘 Support & Help

### **Need Help?**
- **Teacher Guide**: See full documentation at `/docs/ASSIGNMENT_SYSTEM_PRODUCTION_READY.md`
- **Contact Support**: support@yourschool.example.com
- **Training Videos**: Available in the Help section
- **FAQ**: Check the FAQ page for common questions

### **Common Issues:**

**Q: I can't see the "Create Assignment" button**
- **A:** Ensure you're logged in as a Teacher, not Student or Admin

**Q: Student can't submit assignment**
- **A:** Check if the assignment is still active and not past due date (unless late submission is allowed)

**Q: Grades not showing for students**
- **A:** Ensure you've clicked "Save Grade" after entering the score and feedback

**Q: Plagiarism check not working**
- **A:** Ensure it's enabled in assignment settings and you have at least 2 submissions to compare

---

## ✅ System Status Indicators

### **Assignment Statuses:**
- 🟢 **Draft** - Created but not published
- 🔵 **Active** - Published and accepting submissions
- 🟡 **Closed** - Past due date, no new submissions
- ⚫ **Archived** - Old assignment, hidden from active list

### **Submission Statuses:**
- ⏳ **Pending** - Not yet submitted
- ✅ **Submitted** - Submitted, waiting for grading
- 📊 **Graded** - Graded with score and feedback
- 🔴 **Overdue** - Past due date without submission
- ⏰ **Late** - Submitted after due date

---

**Last Updated:** November 28, 2025  
**System Version:** 1.0 Production Ready  
**Documentation Version:** 1.0
