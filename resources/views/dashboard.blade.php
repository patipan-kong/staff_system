@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="fas fa-tachometer-alt"></i> Dashboard
            <small class="text-muted">Welcome back, {{ $user->name }}!</small>
        </h1>
    </div>
</div>

<div class="row">
    <!-- Personal Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($weeklyHours, 1) }}</h4>
                        <p class="mb-0">Weekly Hours</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($monthlyHours, 1) }}</h4>
                        <p class="mb-0">Monthly Hours</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $activeProjects }}</h4>
                        <p class="mb-0">Active Projects</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $pendingLeaves }}</h4>
                        <p class="mb-0">Pending Leaves</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($user->isManagerOrAdmin())
<div class="row mt-4">
    <!-- Management Stats -->
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalUsers }}</h4>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalProjects }}</h4>
                        <p class="mb-0">Total Projects</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $pendingLeaveRequests }}</h4>
                        <p class="mb-0">Leave Requests</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Today's Timesheets</h5>
            </div>
            <div class="card-body">
                @if($recentTimesheets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Project</th>
                                    <th>Time</th>
                                    <th>Hours</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTimesheets as $timesheet)
                                <tr>
                                    <td>{{ $timesheet->user->name }}</td>
                                    <td>{{ $timesheet->project->name }}</td>
                                    <td>
                                        {{ $timesheet->start_time->format('H:i') }} - 
                                        {{ $timesheet->end_time->format('H:i') }}
                                    </td>
                                    <td>{{ $timesheet->getWorkedTime() }}</td>
                                    <td>{{ Str::limit($timesheet->description, 50) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No timesheets submitted today.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('timesheets.create') }}" class="btn btn-primary">
                        <i class="fas fa-clock"></i> Add Timesheet
                    </a>
                    <a href="{{ route('leaves.create') }}" class="btn btn-success">
                        <i class="fas fa-calendar-plus"></i> Request Leave
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-info">
                        <i class="fas fa-project-diagram"></i> View Projects
                    </a>
                    @if($user->isManagerOrAdmin())
                        <a href="{{ route('projects.create') }}" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Create Project
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection