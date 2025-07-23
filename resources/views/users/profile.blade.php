@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-cog"></i> {{ $user->name }}'s Profile</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()->isAdmin() || auth()->id() === $user->id)
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            @endif
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Team
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($user->photo ?? false)
                    <img src="{{ Storage::url($user->photo) }}" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                @endif
                
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->position ?? 'Staff' }}</p>
                
                @if($user->department ?? false)
                    <span class="badge bg-info mb-3">{{ $user->department->name }}</span>
                @endif

                @php
                    $roleLabels = [
                        0 => ['label' => 'Staff', 'class' => 'bg-secondary'],
                        1 => ['label' => 'Senior Staff', 'class' => 'bg-info'],
                        2 => ['label' => 'Manager', 'class' => 'bg-warning text-dark'],
                        3 => ['label' => 'Senior Manager', 'class' => 'bg-success'],
                        99 => ['label' => 'Administrator', 'class' => 'bg-danger']
                    ];
                    $roleInfo = $roleLabels[$user->role] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                @endphp
                <div class="mb-3">
                    <span class="badge {{ $roleInfo['class'] }} fs-6">{{ $roleInfo['label'] }}</span>
                </div>

                @if(auth()->user()->isAdmin() || auth()->id() === $user->id)
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Contact Information</h6>
            </div>
            <div class="card-body">
                @if($user->email)
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        @if(auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id)
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        @else
                            <span class="text-muted">{{ substr($user->email, 0, 3) }}***@{{ explode('@', $user->email)[1] ?? 'hidden' }}</span>
                        @endif
                    </div>
                @endif
                @if($user->phone ?? false)
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        @if(auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id)
                            <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                        @else
                            <span class="text-muted">***-***-{{ substr($user->phone, -4) }}</span>
                        @endif
                    </div>
                @endif
                @if($user->address ?? false)
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        @if(auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id)
                            {{ $user->address }}
                        @else
                            <span class="text-muted">Address on file</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-briefcase"></i> Employment Details</h6>
            </div>
            <div class="card-body">
                @if($user->hire_date ?? false)
                    <div class="mb-2">
                        <strong>Hire Date:</strong><br>
                        <i class="fas fa-calendar text-muted me-2"></i>
                        {{ $user->hire_date->format('F j, Y') }}
                        <small class="text-muted">({{ $user->hire_date->diffForHumans() }})</small>
                    </div>
                @endif
                @if(auth()->user()->isManagerOrAdmin() && $user->salary ?? false)
                    <div class="mb-2">
                        <strong>Salary:</strong><br>
                        <i class="fas fa-dollar-sign text-muted me-2"></i>
                        ${{ number_format($user->salary, 2) }}
                    </div>
                @endif
                <div class="mb-2">
                    <strong>Status:</strong><br>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Performance Stats (Only for managers/admins or own profile) -->
        @if(auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id)
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $user->projects->count() }}</h3>
                        <p class="mb-0">Active Projects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        @php
                            $thisMonthHours = $user->timesheets()
                                ->whereYear('date', now()->year)
                                ->whereMonth('date', now()->month)
                                ->get()
                                ->sum(function($ts) { return $ts->getWorkedHours(); });
                        @endphp
                        <h3>{{ number_format($thisMonthHours, 1) }}</h3>
                        <p class="mb-0">Hours This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $user->leaves()->where('status', 'approved')->count() }}</h3>
                        <p class="mb-0">Approved Leaves</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Projects Assigned -->
        @if($user->projects && $user->projects->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Assigned Projects ({{ $user->projects->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($user->projects->take(6) as $project)
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $project->name }}</h6>
                                @php
                                    $statusClasses = [
                                        'planning' => 'bg-secondary',
                                        'active' => 'bg-success',
                                        'on_hold' => 'bg-warning text-dark',
                                        'completed' => 'bg-primary'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ Str::limit($project->description, 80) }}</p>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> View Project
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($user->projects->count() > 6)
                    <div class="text-center">
                        <small class="text-muted">... and {{ $user->projects->count() - 6 }} more projects</small>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Time Logs (Only for managers/admins or own profile) -->
        @if((auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id) && $user->timesheets && $user->timesheets->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Time Logs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Hours</th>
                                <th>Description</th>
                                @if(auth()->id() === $user->id)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->timesheets->take(10) as $timesheet)
                            <tr>
                                <td>{{ $timesheet->date->format('M j, Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $timesheet->project->name }}</span>
                                </td>
                                <td><strong>{{ $timesheet->getWorkedTime() }}</strong></td>
                                <td>{{ Str::limit($timesheet->description, 60) }}</td>
                                @if(auth()->id() === $user->id)
                                    <td>
                                        <a href="{{ route('timesheets.show', $timesheet) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(auth()->id() === $user->id)
                <div class="card-footer text-center">
                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-primary">
                        View All My Time Logs
                    </a>
                </div>
            @endif
        </div>
        @endif

        <!-- Leave Requests (Only for managers/admins or own profile) -->
        @if((auth()->user()->isManagerOrAdmin() || auth()->id() === $user->id) && $user->leaves && $user->leaves->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Recent Leave Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Days</th>
                                <th>Status</th>
                                @if(auth()->id() === $user->id)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->leaves->take(5) as $leave)
                            <tr>
                                <td>
                                    @php
                                        $typeClasses = [
                                            'vacation' => 'bg-info',
                                            'sick' => 'bg-warning text-dark',
                                            'sick_with_certificate' => 'bg-danger',
                                            'personal' => 'bg-secondary'
                                        ];
                                    @endphp
                                    <span class="badge {{ $typeClasses[$leave->type] ?? 'bg-secondary' }}">
                                        {{ $leave->getTypeLabel() }}
                                    </span>
                                </td>
                                <td>
                                    {{ $leave->start_date->format('M j') }} - {{ $leave->end_date->format('M j, Y') }}
                                </td>
                                <td>{{ $leave->days }} {{ $leave->days == 1 ? 'day' : 'days' }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }}">
                                        {{ $leave->getStatusLabel() }}
                                    </span>
                                </td>
                                @if(auth()->id() === $user->id)
                                    <td>
                                        <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(auth()->id() === $user->id)
                <div class="card-footer text-center">
                    <a href="{{ route('leaves.index') }}" class="btn btn-outline-primary">
                        View All My Leave Requests
                    </a>
                </div>
            @endif
        </div>
        @endif

        <!-- Empty state if no data visible -->
        @if(!$user->projects->count() && (!auth()->user()->isManagerOrAdmin() && auth()->id() !== $user->id))
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <h4>Limited Access</h4>
                <p class="text-muted">You can only view basic information for this team member.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
