# Assignment System - Menu Locations Guide

## ✅ SIDEBAR MENU LINKS NOW VISIBLE

### 🎓 **TEACHER SIDEBAR** (Left Navigation)

After logging in as a teacher, you'll see this sidebar menu:

```
📊 Dashboard
📚 My Classes
🎥 Virtual Classes
📝 Lesson Plans
📁 Learning Materials
✏️ Assignments          ← **CLICK HERE FOR ASSIGNMENTS!**
❓ Quizzes
💻 Online Exams
💬 Discussions
🧩 Integrations
✓ Attendance
📊 Reports
```

**Direct Access:**
- Click **"Assignments"** in the left sidebar
- Icon: ✏️ (pencil square)
- URL: `https://yourschool.example.com/teacher/classroom/exercises`

---

### 👨‍🎓 **STUDENT SIDEBAR** (Left Navigation)

After logging in as a student, you'll see this sidebar menu:

```
🏠 Dashboard
🏪 Bookstore
🚪 Classroom
✏️ Assignments          ← **CLICK HERE FOR ASSIGNMENTS!**
🏆 My Grades           ← **CLICK HERE TO SEE YOUR GRADES!**
🎥 Virtual Classes
📁 Materials
📅 Timetable
✓ Attendance
💳 Pay Fees
```

**Direct Access:**
- Click **"Assignments"** to view all assignments
- Click **"My Grades"** to see graded work
- Icon: ✏️ (pencil square) for Assignments
- Icon: 🏆 (award) for Grades

**Student URLs:**
- Assignments: `https://yourschool.example.com/student/classroom/exercises`
- Grades: `https://yourschool.example.com/student/classroom/exercises/grades`

---

## 📍 DETAILED MENU STRUCTURE

### **Teacher Menu Items:**

| Menu Item | Icon | Functionality | URL Endpoint |
|-----------|------|---------------|--------------|
| Dashboard | 📊 | Overview & statistics | `/teacher/dashboard` |
| My Classes | 📚 | View assigned classes | `/teacher/classes` |
| Virtual Classes | 🎥 | Online class sessions | `/teacher/classroom/virtual` |
| Lesson Plans | 📝 | Create lesson plans | `/teacher/classroom/lessons` |
| Learning Materials | 📁 | Upload study materials | `/teacher/classroom/materials` |
| **Assignments** | **✏️** | **Create & manage assignments** | **`/teacher/classroom/exercises`** |
| Quizzes | ❓ | Create quizzes | `/teacher/classroom/quizzes` |
| Online Exams | 💻 | Manage online exams | `/teacher/classroom/exams` |
| Discussions | 💬 | Class discussions | `/teacher/classroom/discussions` |
| Integrations | 🧩 | Third-party integrations | `/teacher/classroom/integrations` |
| Attendance | ✓ | Mark attendance | `/teacher/attendance` |
| Reports | 📊 | View reports | `/reports` |

### **Student Menu Items:**

| Menu Item | Icon | Functionality | URL Endpoint |
|-----------|------|---------------|--------------|
| Dashboard | 🏠 | Overview & notifications | `/student/dashboard` |
| Bookstore | 🏪 | Buy books online | `/bookstore` |
| Classroom | 🚪 | Classroom hub | `/student/classroom` |
| **Assignments** | **✏️** | **View & submit assignments** | **`/student/classroom/exercises`** |
| **My Grades** | **🏆** | **View grades & feedback** | **`/student/classroom/exercises/grades`** |
| Virtual Classes | 🎥 | Join online classes | `/student/classroom/virtual` |
| Materials | 📁 | Download study materials | `/student/classroom/materials` |
| Timetable | 📅 | View class schedule | `/student/timetable` |
| Attendance | ✓ | View attendance record | `/student/attendance` |
| Pay Fees | 💳 | Make fee payments | `/finance/payments/pay` |

---

## 🔍 HOW TO TEST THE MENU LINKS

### **For Teachers:**

1. **Logout** if currently logged in
2. **Login** with teacher credentials
3. Look at the **left sidebar**
4. You should see **"Assignments"** with a ✏️ icon
5. **Click "Assignments"** → You'll be taken to the assignment management page

**Expected Result:**
- Page Title: "Assignments & Exercises"
- Green button: "Create New Assignment"
- Statistics cards showing: Total Assignments, Active, Submissions, Graded
- List of all your assignments

### **For Students:**

1. **Logout** if currently logged in
2. **Login** with student credentials
3. Look at the **left sidebar**
4. You should see **"Assignments"** and **"My Grades"** with icons
5. **Click "Assignments"** → View all assignments
6. **Click "My Grades"** → View your grades

**Expected Result (Assignments page):**
- Page Title: "My Assignments"
- Filter tabs: All, Pending, Submitted, Graded, Overdue
- List of assignments with due dates and status badges

**Expected Result (Grades page):**
- Page Title: "My Grades"
- Statistics: Average Score, Total Assignments, Completed, Pending
- List of graded assignments with scores and feedback

---

## 🎨 VISUAL INDICATORS

### **Active Menu Item:**
When you're on the assignments page, the menu item will be highlighted:
- Background: Primary color (usually blue)
- Text: White
- The entire row will be visibly different from other menu items

### **Menu Item States:**
- **Normal**: Black text, white background
- **Hover**: Slight gray background
- **Active**: Primary color background, white text

---

## 🚨 TROUBLESHOOTING

### **Problem: I don't see "Assignments" in the sidebar**

**Solution 1: Clear Browser Cache**
```
1. Press Ctrl + Shift + Delete (Windows) or Cmd + Shift + Delete (Mac)
2. Select "Cached images and files"
3. Click "Clear data"
4. Refresh the page (F5 or Ctrl + R)
```

**Solution 2: Hard Refresh**
```
1. Press Ctrl + Shift + R (Windows) or Cmd + Shift + R (Mac)
2. This forces a reload without cache
```

**Solution 3: Verify Role**
```
1. Check your user role in the top-right profile menu
2. Teacher role should show "Teaching Staff"
3. Student role should show "Student"
4. If role is wrong, contact admin
```

### **Problem: Menu link shows "#" instead of actual page**

**Cause:** Routes are not registered or route names don't match

**Solution:**
```bash
# Clear route cache
php artisan route:clear

# Rebuild route cache
php artisan route:cache

# Restart server
php artisan serve
```

### **Problem: I get a 404 error when clicking the link**

**Cause:** Routes not defined or middleware blocking access

**Solution:**
```bash
# Check if routes exist
php artisan route:list | grep "exercises"

# You should see routes like:
# tenant.teacher.classroom.exercises.index
# tenant.student.classroom.exercises.index
```

---

## 📋 MENU UPDATE SUMMARY

### **Changes Made:**

✅ **Teacher Sidebar:**
- Renamed "Exercises" → "Assignments" (more user-friendly)
- Route: `tenant.teacher.classroom.exercises.index`
- Active states: All `tenant.teacher.classroom.exercises.*` routes

✅ **Student Sidebar:**
- Added "Classroom" link (hub page)
- Updated "Assignments" link with actual route (was placeholder `#`)
- Added "My Grades" link (dedicated grades page)
- Added "Virtual Classes" link
- Added "Materials" link
- Added "Attendance" link
- Route: `tenant.student.classroom.exercises.index`
- Active states: All `tenant.student.classroom.exercises.*` routes

✅ **Student Grades Link:**
- New dedicated menu item
- Route: `tenant.student.classroom.exercises.grades`
- Shows all graded assignments with scores and feedback

---

## 🎯 QUICK REFERENCE CARD

### **Teachers - How to Access Assignments:**
1. Login
2. Left sidebar → Click **"Assignments"** (✏️ icon)
3. Click **"Create New Assignment"** (green button)

### **Students - How to Access Assignments:**
1. Login
2. Left sidebar → Click **"Assignments"** (✏️ icon)
3. Click on any assignment to view/submit

### **Students - How to Check Grades:**
1. Login
2. Left sidebar → Click **"My Grades"** (🏆 icon)
3. View all your scores and teacher feedback

---

## 📞 NEED HELP?

If you still can't see the menu items after following this guide:

1. **Screenshot the sidebar** and send to support
2. **Check console for errors**: Right-click → Inspect → Console tab
3. **Verify your role**: Profile menu (top-right) → Check user type
4. **Contact system administrator** with:
   - Your username
   - Your role (Teacher/Student)
   - Screenshot of sidebar
   - Any error messages

---

**Last Updated:** November 28, 2025  
**File Location:** `c:\wamp5\www\skolariscloud3\resources\views\tenant\layouts\partials\sidebar.blade.php`  
**Changes Applied:** ✅ Live and active
