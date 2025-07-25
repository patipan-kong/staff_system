<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CompanyHolidayController;
use App\Http\Controllers\GroupProjectController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Image serving routes (no authentication required for cached images)
Route::get('/storage/profile_photos/{filename}', [ImageController::class, 'profilePhoto'])->name('profile.photo');
Route::get('/storage/{path}', [ImageController::class, 'serveFile'])->where('path', '.*')->name('storage.file');

// Admin Only Routes (Role 99)
Route::middleware(['auth', 'admin'])->group(function () {
    // User Management (create, edit, update, delete)
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // System Administration Routes
    Route::get('/admin/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::get('/admin/departments/create', [DepartmentController::class, 'create'])->name('admin.departments.create');
    Route::post('/admin/departments/store', [DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::get('/admin/departments/{department}/show', [DepartmentController::class, 'show'])->name('admin.departments.show');
    Route::get('/admin/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('admin.departments.edit');
    Route::put('/admin/departments/{department}/update', [DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('/admin/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');
    // Route::resource('admin/departments', DepartmentController::class)->except(['index']);
    
    Route::get('/admin/holidays', [CompanyHolidayController::class, 'index'])->name('admin.holidays.index');
    Route::get('/admin/holidays/create', [CompanyHolidayController::class, 'create'])->name('admin.holidays.create');
    Route::post('/admin/holidays/store', [CompanyHolidayController::class, 'store'])->name('admin.holidays.store');
    Route::get('/admin/holidays/{holiday}/show', [CompanyHolidayController::class, 'show'])->name('admin.holidays.show');
    Route::get('/admin/holidays/{holiday}/edit', [CompanyHolidayController::class, 'edit'])->name('admin.holidays.edit');
    Route::put('/admin/holidays/{holiday}/update', [CompanyHolidayController::class, 'update'])->name('admin.holidays.update');
    Route::delete('/admin/holidays/{holiday}', [CompanyHolidayController::class, 'destroy'])->name('admin.holidays.destroy');
    // Route::resource('admin/holidays', CompanyHolidayController::class)->except(['index']);
    
    // Group Projects Management
    Route::get('/admin/group-projects', [GroupProjectController::class, 'index'])->name('admin.group-projects.index');
    Route::get('/admin/group-projects/create', [GroupProjectController::class, 'create'])->name('admin.group-projects.create');
    Route::post('/admin/group-projects/store', [GroupProjectController::class, 'store'])->name('admin.group-projects.store');
    Route::get('/admin/group-projects/{group_project}/show', [GroupProjectController::class, 'show'])->name('admin.group-projects.show');
    Route::get('/admin/group-projects/{group_project}/edit', [GroupProjectController::class, 'edit'])->name('admin.group-projects.edit');
    Route::put('/admin/group-projects/{group_project}/update', [GroupProjectController::class, 'update'])->name('admin.group-projects.update');
    Route::delete('/admin/group-projects/{group_project}', [GroupProjectController::class, 'destroy'])->name('admin.group-projects.destroy');
    
});

// Manager/Admin Routes (Role 2+)
Route::middleware(['auth', 'manager_or_admin'])->group(function () {
    // Project Management
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
    
    // Staff Planning
    Route::get('/staff-planning', [App\Http\Controllers\StaffPlanningController::class, 'index'])->name('staff-planning.index');
    Route::post('/staff-planning', [App\Http\Controllers\StaffPlanningController::class, 'store'])->name('staff-planning.store');
    Route::delete('/staff-planning/{planning}', [App\Http\Controllers\StaffPlanningController::class, 'destroy'])->name('staff-planning.destroy');
    
    // Leave Approval
    Route::put('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::put('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    
    // User Management (view all users)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
});

// Protected Routes - Staff Level (Role 0+)
Route::middleware(['auth', 'staff'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Timesheets
    Route::resource('timesheets', TimesheetController::class);
    
    // Projects (view assigned projects)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    
    // Leave Requests
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::get('/leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
    Route::get('/leaves/{leave}/medical-certificate', [LeaveController::class, 'downloadMedicalCertificate'])->name('leaves.medical-certificate');
    
    // Reports (accessible to all authenticated users)
    Route::get('/reports/weekly', [ReportController::class, 'weeklyDistribution'])->name('reports.weekly');
    Route::get('/reports/timesheet-data', [ReportController::class, 'getTimesheetData'])->name('reports.timesheet-data');
    
    // Whiteboard (accessible to all authenticated users)
    Route::get('/whiteboard', [App\Http\Controllers\WhiteboardController::class, 'index'])->name('whiteboard.index');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
