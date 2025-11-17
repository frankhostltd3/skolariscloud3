# Academic Foundation Systems - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

Complete setup guide for Education Levels, Examination Bodies, and Countries management systems.

## Prerequisites

✅ Laravel 10.x installed  
✅ Multi-tenant architecture active  
✅ User authenticated with school context  
✅ Migrations completed

## Step 1: Run Migrations (30 seconds)

```bash
php artisan tenants:migrate
```

**Expected Output:**
```
Migrating tenant database for SMATCAMPUS Demo School . 310ms DONE
Migrating tenant database for Starlight Academy ...... 195ms DONE
Migrating tenant database for Busoga College Mwiri ... 174ms DONE
Migrating tenant database for Jinja Senior Secondary School 196ms DONE
```

This creates three tables:
- `countries` (global)
- `examination_bodies` (tenant-scoped)
- `education_levels` (tenant-scoped)

## Step 2: Access the Systems (1 minute)

Navigate to the Academics section via:

**Option A: Sidebar Navigation**
1. Click **Academics** in main navigation
2. See three new menu items:
   - 🎓 **Education Levels**
   - 🏆 **Examination Bodies**
   - 🌍 **Countries**

**Option B: Direct URLs**
- Education Levels: `http://subdomain.localhost:8000/tenant/academics/education-levels`
- Examination Bodies: `http://subdomain.localhost:8000/tenant/academics/examination-bodies`
- Countries: `http://subdomain.localhost:8000/tenant/academics/countries`

## Step 3: Set Up Countries (2 minutes)

Countries provide the foundation for examination bodies.

### Quick Add - Common Countries

**Uganda:**
```
Name: Uganda
ISO 2: UG
ISO 3: UGA
Phone Code: +256
Currency Code: UGX
Currency Symbol: UGX
Timezone: Africa/Kampala
Flag Emoji: 🇺🇬
Status: Active
```

**Kenya:**
```
Name: Kenya
ISO 2: KE
ISO 3: KEN
Phone Code: +254
Currency Code: KES
Currency Symbol: KES
Timezone: Africa/Nairobi
Flag Emoji: 🇰🇪
Status: Active
```

**United Kingdom:**
```
Name: United Kingdom
ISO 2: GB
ISO 3: GBR
Phone Code: +44
Currency Code: GBP
Currency Symbol: £
Timezone: Europe/London
Flag Emoji: 🇬🇧
Status: Active
```

**United States:**
```
Name: United States
ISO 2: US
ISO 3: USA
Phone Code: +1
Currency Code: USD
Currency Symbol: $
Timezone: America/New_York
Flag Emoji: 🇺🇸
Status: Active
```

### Steps:
1. Click **"Add Country"** button
2. Fill in the form (Name and ISO codes are required)
3. Click **"Create"**
4. Repeat for all countries you need

💡 **Tip:** ISO codes are standardized. Find them at [iso.org](https://www.iso.org/obp/ui/)

## Step 4: Create Examination Bodies (1 minute)

Examination bodies conduct assessments and exams.

### Quick Add - Common Exam Bodies

**Uganda - UNEB:**
```
Name: Uganda National Examinations Board
Code: UNEB
Country: Uganda
Website: https://uneb.ac.ug
Type: National
Status: Active
Description: Conducts PLE, UCE, and UACE examinations
```

**Kenya - KNEC:**
```
Name: Kenya National Examinations Council
Code: KNEC
Country: Kenya
Website: https://www.knec.ac.ke
Type: National
Status: Active
Description: Conducts KCPE, KCSE, and KSCE examinations
```

**International - Cambridge:**
```
Name: Cambridge International Examinations
Code: Cambridge
Country: United Kingdom
Website: https://www.cambridgeinternational.org
Type: International ✓
Status: Active
Description: Offers IGCSE, O-Level, A-Level qualifications
```

**International - IB:**
```
Name: International Baccalaureate
Code: IB
Country: (leave blank)
Website: https://www.ibo.org
Type: International ✓
Status: Active
Description: Offers IB Diploma Programme
```

### Steps:
1. Click **"Add Examination Body"** button
2. Fill in the form (Name is required)
3. Select country from dropdown (if applicable)
4. Check **"International"** if it's a global body
5. Click **"Create"**

## Step 5: Set Up Education Levels (1 minute)

Education levels organize your school's academic structure.

### Quick Add - Common Systems

**Uganda System:**
```
Level 1:
Name: Primary
Code: P
Min Grade: 1
Max Grade: 7
Sort Order: 10
Status: Active

Level 2:
Name: O-Level
Code: O
Min Grade: 1
Max Grade: 4
Sort Order: 20
Status: Active

Level 3:
Name: A-Level
Code: A
Min Grade: 5
Max Grade: 6
Sort Order: 30
Status: Active
```

**Kenya CBC System:**
```
Level 1:
Name: Pre-Primary
Code: PP
Min Grade: 1
Max Grade: 2
Sort Order: 10

Level 2:
Name: Lower Primary
Code: LP
Min Grade: 1
Max Grade: 3
Sort Order: 20

Level 3:
Name: Upper Primary
Code: UP
Min Grade: 4
Max Grade: 6
Sort Order: 30

Level 4:
Name: Junior Secondary
Code: JS
Min Grade: 7
Max Grade: 9
Sort Order: 40

Level 5:
Name: Senior Secondary
Code: SS
Min Grade: 10
Max Grade: 12
Sort Order: 50
```

**USA System:**
```
Level 1:
Name: Elementary School
Code: ES
Min Grade: 0 (Kindergarten)
Max Grade: 5
Sort Order: 10

Level 2:
Name: Middle School
Code: MS
Min Grade: 6
Max Grade: 8
Sort Order: 20

Level 3:
Name: High School
Code: HS
Min Grade: 9
Max Grade: 12
Sort Order: 30
```

**UK System:**
```
Level 1:
Name: Key Stage 1
Code: KS1
Min Grade: 1
Max Grade: 2
Sort Order: 10

Level 2:
Name: Key Stage 2
Code: KS2
Min Grade: 3
Max Grade: 6
Sort Order: 20

Level 3:
Name: Key Stage 3
Code: KS3
Min Grade: 7
Max Grade: 9
Sort Order: 30

Level 4:
Name: Key Stage 4 (GCSE)
Code: KS4
Min Grade: 10
Max Grade: 11
Sort Order: 40

Level 5:
Name: Key Stage 5 (A-Level)
Code: KS5
Min Grade: 12
Max Grade: 13
Sort Order: 50
```

### Steps:
1. Click **"Add Education Level"** button
2. Fill in the form (Name is required)
3. Set grade range (e.g., Primary: 1-7)
4. Set sort order (10, 20, 30, etc.)
5. Click **"Create"**
6. Repeat for all levels

💡 **Tip:** Use sort order increments of 10 to allow future insertions

## Common Workflows

### Workflow 1: View Statistics
1. Go to Education Levels index
2. See **Class Count** for each level
3. Click level name to see details

### Workflow 2: Edit Information
1. Click **Edit** button (pencil icon)
2. Update fields
3. Click **Update**
4. See success message

### Workflow 3: Deactivate (Don't Delete)
1. Edit the record
2. Change **Status** to "Inactive"
3. Click **Update**
4. Record hidden from active lists but preserved

### Workflow 4: View Related Records
1. Go to Countries index
2. See **Exam Bodies** count per country
3. Click country name
4. See list of all exam bodies for that country

## Integration with Classes

Once education levels are created, you can:

1. **Create Classes**:
   - Go to Classes management
   - Select education level from dropdown
   - Create class (e.g., "P1" under Primary level)

2. **Filter Classes by Level**:
   - Use education level as filter criteria
   - View all classes in a specific level

3. **Track Progression**:
   - Monitor student movement between levels
   - Generate level-based reports

## Quick Reference

### Menu Icons
- 🎓 **Education Levels**: `bi-mortarboard-fill`
- 🏆 **Examination Bodies**: `bi-award`
- 🌍 **Countries**: `bi-globe`

### Status Badges
- 🟢 **Active**: Green badge
- 🟡 **Inactive**: Yellow badge

### Required Fields
**Countries:**
- Name
- ISO Code 2 (2 characters)
- ISO Code 3 (3 characters)

**Examination Bodies:**
- Name

**Education Levels:**
- Name

### Validation Rules
- Education level names must be unique per school
- ISO codes must be unique globally
- Max grade must be ≥ Min grade
- Website must be valid URL

## Troubleshooting

### "Duplicate name" error
**Problem:** Education level name already exists  
**Solution:** Use different name or edit existing level

### "Cannot delete" error
**Problem:** Record has related data  
**Solution:** Remove related records first or mark as inactive

### Country not appearing in dropdown
**Problem:** Country marked as inactive  
**Solution:** Edit country and set status to Active

### ISO code validation failed
**Problem:** Invalid ISO code format  
**Solution:** Use 2-char (UG) and 3-char (UGA) standard codes

## Next Steps

After setup:

1. ✅ Create Classes linked to education levels
2. ✅ Enroll students and assign exam bodies
3. ✅ Generate level-based reports
4. ✅ Configure exam registration settings
5. ✅ Set up timetables per level

## Support

### Documentation
- Full Reference: `docs/ACADEMIC_FOUNDATION_SYSTEMS.md`
- Class Management: `docs/CLASS_MANAGEMENT_SYSTEM.md`
- Stream Management: `docs/STREAM_MANAGEMENT_SYSTEM.md`

### Need Help?
- Check validation error messages on forms
- Review empty state messages for guidance
- Ensure migrations completed successfully

## Summary Checklist

- [ ] Migrations run successfully (4 databases)
- [ ] At least 1 country created
- [ ] At least 1 examination body created
- [ ] At least 1 education level created
- [ ] Navigation menus displaying correctly
- [ ] Can view, edit, and delete records
- [ ] Ready to create classes linked to levels

**Total Setup Time: ~5 minutes** ⏱️

🎉 **You're all set!** Start creating classes and enrolling students.
