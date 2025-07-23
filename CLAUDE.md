# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 8-based staff management system for small-medium organizations requiring comprehensive HR and project tracking capabilities. The system follows MVC architecture with role-based access control.

## Technology Stack

- **Backend**: Laravel 8.0, PHP 7.3+, Laravel Sanctum (authentication)
- **Frontend**: Bootstrap 5.1.3, Laravel Mix, Axios, SASS/SCSS
- **Database**: MySQL/MariaDB with Doctrine DBAL
- **Testing**: PHPUnit, Mockery

## Development Commands

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Development server
php artisan serve

# Asset compilation
npm run dev          # Development build
npm run watch        # Watch for changes
npm run production   # Production build

# Testing
php artisan test     # Run all tests
vendor/bin/phpunit   # Alternative test runner

# Code quality
php artisan route:list  # View all routes
php artisan tinker      # Interactive shell
```

## Architecture Overview

### Core Components

1. **Role-Based Access Control**: 5-tier system (Staff=0, Leader=1, Manager=2, Test=3, Admin=99)
2. **Key Entities**: Users, Departments, Projects, Timesheets, Leaves, Company Holidays
3. **Middleware**: IsAdministrator, IsManagerOrAdmin, IsStaff for access control

### Database Relationships

- Projects belong to GroupProjects and have many Timesheets
- Many-to-many relationship between Users and Projects via ProjectAssigns
- Comprehensive leave tracking with approval workflows

### Key Features

- **Project Management**: Hierarchical structure with group/individual projects, time tracking, cost calculations
- **Time Tracking**: Daily timesheets with task descriptions and image attachments
- **Leave Management**: Vacation/sick leave with approval workflows and quota tracking
- **Reporting**: Weekly distribution reports, project summary reports, calendar-based planning

## File Structure Expectations

```
app/
├── Http/
│   ├── Controllers/     # Main application controllers
│   ├── Middleware/      # Role-based access middleware
│   └── Requests/        # Form request validation
├── Models/              # Eloquent models (User, Project, Timesheet, etc.)
└── Providers/           # Service providers

database/
├── migrations/          # Database schema migrations
└── seeders/            # Database seeders

resources/
├── views/              # Blade templates
├── js/                 # Frontend JavaScript
└── sass/               # SCSS stylesheets

routes/
└── web.php             # Web routes with role-based protection

storage/
└── app/
    └── public/         # File uploads (profile photos, timesheet attachments)
```

## Development Guidelines

- Follow Laravel 8 conventions and MVC architecture
- Use role-based middleware for access control
- Implement proper validation for form requests
- Handle file uploads securely (profile photos, timesheet attachments)
- Use Eloquent relationships for data modeling
- Implement proper error handling and user feedback
- Support multi-language functionality (EN/JP)