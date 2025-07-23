# ARK Staff Management System

A comprehensive Laravel-based staff management system with advanced HR capabilities, project tracking, time management, and detailed reporting features.

## 🌟 **System Overview**

The ARK Staff Management System is a full-featured organizational management platform built on Laravel 8, designed for small to medium-sized organizations. It provides complete staff lifecycle management, project coordination, time tracking, leave management, and powerful reporting capabilities with a modern, responsive interface.

## ✨ **Key Features**

### 👥 **User & Staff Management**
- **Multi-tier Role System**: 5 distinct roles (Staff, Leader, Manager, Test, Admin) with granular permissions
- **Complete User Profiles**: Photos, contact information, department assignments, positions, and salary tracking
- **Department Management**: Organizational structure with department hierarchies
- **Profile Photo Management**: Secure image upload and serving with authorization controls

### 📊 **Project Management**
- **Hierarchical Project Structure**: Group projects containing individual projects with color coding and icons
- **Project Assignments**: Flexible team member assignments with role-based access
- **Progress Tracking**: Status monitoring, timeline management, and completion tracking  
- **Budget Management**: Project budgets with cost tracking and resource allocation
- **Visual Project Cards**: Modern UI with project status indicators and progress bars

### ⏱️ **Time Tracking & Timesheets**
- **Daily Time Entries**: Comprehensive timesheet system with start/end times and break tracking
- **Project-specific Logging**: Time allocation across multiple projects with detailed descriptions
- **File Attachments**: Support for timesheet documentation and proof of work
- **Automated Calculations**: Worked hours, overtime, and project cost calculations
- **Monthly Views**: Calendar-based timesheet visualization and management

### 🏖️ **Leave Management**
- **Multiple Leave Types**: Vacation, sick leave, personal leave, and medical certificate requirements
- **Approval Workflows**: Manager/admin approval system with status tracking
- **Medical Certificates**: Secure file upload and download with proper authorization
- **Leave Quotas**: Automatic day calculations and quota management
- **Leave History**: Complete leave request tracking with approval notes

### 📈 **Advanced Reporting & Analytics**
- **Weekly Distribution Reports**: Interactive calendar view showing team activity and project time allocation
- **Project Analytics**: Budget vs actual costs, resource utilization, and progress tracking
- **Team Performance**: User activity summaries and productivity metrics
- **Leave Statistics**: Comprehensive leave tracking and pattern analysis
- **Interactive Dashboards**: Real-time statistics with drill-down capabilities

### 🎨 **Modern User Interface**
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Interactive Components**: AJAX-powered modals, dynamic forms, and live updates
- **Visual Indicators**: Color-coded status badges, progress bars, and icons
- **Dark/Light Themes**: Professional styling with consistent branding
- **Accessibility**: WCAG-compliant interface with keyboard navigation support

## 🔐 **Security & Authorization**

### **Role-Based Access Control (RBAC)**
| Role | Level | Permissions |
|------|-------|-------------|
| **Staff** | 0 | Personal timesheets, profile, assigned projects, leave requests |
| **Leader** | 1 | Team coordination, enhanced project access |
| **Manager** | 2 | Project management, team oversight, leave approvals |
| **Test** | 3 | Testing and development access |
| **Admin** | 99 | Full system administration, user management, system settings |

### **Security Features**
- **Laravel Policies**: Comprehensive authorization policies for all models
- **Middleware Protection**: Route-level security with role-based access
- **File Security**: Secure image serving with authorization checks
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Input Validation**: Comprehensive form validation and sanitization
- **Session Management**: Secure authentication with session handling

## 🏗️ **Technical Architecture**

### **Backend Stack**
- **Framework**: Laravel 8.0 with PHP 7.4+
- **Authentication**: Laravel Sanctum with custom middleware
- **Database**: MySQL/MariaDB with Eloquent ORM
- **File Storage**: Laravel Storage with public disk configuration
- **Image Processing**: Intervention Image for profile photo handling
- **Validation**: Laravel Form Requests with custom validation rules

### **Frontend Stack**
- **CSS Framework**: Bootstrap 5.1.3 with custom SCSS
- **Icons**: FontAwesome 6.0 for comprehensive icon library
- **JavaScript**: Vanilla JS with AJAX for dynamic interactions
- **Build Tools**: Laravel Mix with Webpack for asset compilation
- **Responsive Design**: Mobile-first approach with breakpoint optimization

### **Database Schema**
```sql
Key Entities:
├── users (staff management)
├── departments (organizational structure)  
├── group_projects (project categorization)
├── projects (individual projects)
├── project_assigns (team assignments)
├── timesheets (time tracking)
├── leaves (leave management)
└── company_holidays (organizational calendar)
```

## 🚀 **Installation & Setup**

### **Requirements**
- PHP 7.4+ with required extensions
- MySQL 5.7+ or MariaDB 10.3+
- Composer for dependency management
- Node.js & NPM for asset compilation

### **Installation Steps**
```bash
# Clone the repository
git clone [repository-url] ark-staff-system
cd ark-staff-system

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage linking
php artisan storage:link

# Asset compilation
npm run dev

# Start development server
php artisan serve
```

### **Environment Configuration**
```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ark_staff_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# File Storage
FILESYSTEM_DRIVER=public

# Application Settings
APP_NAME="ARK Staff Management"
APP_ENV=production
APP_DEBUG=false
```

## 📱 **Usage Guide**

### **Dashboard Navigation**
- **Dashboard**: Overview of personal stats, recent activities, and notifications
- **Timesheets**: Daily time tracking with project allocation
- **Projects**: View assigned projects and progress tracking
- **Leaves**: Submit and manage leave requests
- **Weekly Report**: Interactive team activity calendar (accessible to all staff)

### **Admin Functions**
- **User Management**: Create, edit, and manage staff accounts
- **Department Management**: Organizational structure administration
- **Project Administration**: Group projects and individual project management
- **Holiday Management**: Company-wide holiday calendar
- **System Reports**: Advanced analytics and organizational insights

### **Manager Functions**
- **Team Oversight**: View all team member activities and timesheets
- **Project Management**: Create and manage projects, assign team members
- **Leave Approvals**: Review and approve/reject leave requests
- **Resource Planning**: Weekly distribution reports for resource allocation

## 🔧 **API Endpoints**

### **Authentication Routes**
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - User logout

### **Core Application Routes**
- `GET /dashboard` - Main dashboard
- `GET /profile` - User profile management
- `GET /reports/weekly` - Weekly distribution report
- `GET /reports/timesheet-data` - AJAX timesheet data

### **Resource Routes**
- `/timesheets/*` - Complete timesheet CRUD operations
- `/projects/*` - Project management (role-based access)
- `/leaves/*` - Leave request management with approval workflow
- `/users/*` - User management (admin/manager access)

### **Admin Routes**
- `/admin/departments/*` - Department management
- `/admin/holidays/*` - Company holiday management  
- `/admin/group-projects/*` - Group project administration

### **File Serving**
- `GET /storage/profile_photos/{filename}` - Secure profile photo serving
- `GET /storage/{path}` - General file serving with authorization

## 🧪 **Testing**

The system includes comprehensive testing coverage:

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate test coverage
php artisan test --coverage
```

## 📚 **Documentation**

Additional documentation files:
- `WEEKLY_REPORT.md` - Weekly distribution report feature guide
- `LEAVE_POLICY.md` - Leave management authorization documentation
- `LEAVE_ROUTES_FIX.md` - Technical details on route parameter fixes
- `CLAUDE.md` - Development notes and implementation details

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 **License**

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 **Acknowledgments**

- Laravel Framework for the solid foundation
- Bootstrap team for the responsive UI framework  
- FontAwesome for the comprehensive icon library
- The open-source community for inspiration and best practices

---

**ARK Staff Management System** - Empowering organizations with comprehensive staff and project management capabilities. 🚀
