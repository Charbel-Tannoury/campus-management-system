# Campus Management System - Database Schema

## Overview
Professional database schema for academic administration supporting students, faculty, courses, enrollment, grades, and communications.

## Import Instructions
```bash
mysql -u root -p campus-management-system < schema.sql
```

## Key Tables & ID Ranges
- **students**: Student records (IDs start at 12345)
- **doctors**: Faculty/professors (IDs start at 1234)
- **employee**: Administrative staff (IDs start at 123)
- **courses**: Academic course catalog
- **enrollment**: Student course registrations
- **grades**: Academic performance tracking
- **mails**: System-wide announcements
- **anonymous_complaint**: Anonymous feedback system

## Features
✓ Foreign key constraints for referential integrity  
✓ Strategic AUTO_INCREMENT ranges for different user types  
✓ Support for multi-faculty/college structure  
✓ Comprehensive student-staff messaging system
