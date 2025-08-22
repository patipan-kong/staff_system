@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chalkboard"></i> Team Whiteboard</h1>
        <div class="d-flex align-items-center gap-3">
            <div class="badge bg-info fs-6">
                <i class="fas fa-calendar"></i> {{ $yesterday->format('M j') }} & {{ $today->format('M j, Y') }}
            </div>
            <button onclick="location.reload()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-refresh"></i> Refresh
            </button>
        </div>
    </div>
</div>

<div class="row">
    @foreach($users as $user)
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100 shadow-sm">
            <!-- User Header -->
            <div class="card-header bg-light">
                <div class="d-flex align-items-center">
                    @if($user->photo)
                        <img src="{{ route('profile.photo', basename($user->photo)) }}" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                        @if($user->nickname)
                            <small class="text-muted">"{{ $user->nickname }}"</small>
                        @endif
                        <div class="small text-muted">
                            {{ $user->position ?? 'Staff' }}
                            @if($user->department)
                                • {{ $user->department->name }}
                            @endif
                        </div>
                        @php
                            $roleLabels = [
                                0 => ['label' => 'Staff', 'class' => 'bg-secondary'],
                                1 => ['label' => 'Senior', 'class' => 'bg-info'],
                                2 => ['label' => 'Manager', 'class' => 'bg-warning text-dark'],
                                3 => ['label' => 'Sr. Manager', 'class' => 'bg-success'],
                                99 => ['label' => 'Admin', 'class' => 'bg-danger']
                            ];
                            $roleInfo = $roleLabels[$user->role] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                        @endphp
                        <span class="badge {{ $roleInfo['class'] }} badge-sm">{{ $roleInfo['label'] }}</span>
                    </div>
                    <div class="text-end">
                        @php
                            $totalHours = $user->yesterdayHours + $user->todayHours;
                            $badgeClass = $totalHours > 8 ? 'bg-success' : ($totalHours > 4 ? 'bg-warning' : 'bg-secondary');
                        @endphp
                        <div class="badge {{ $badgeClass }}">
                            {{ number_format($totalHours, 2) }}h
                        </div>
                        <div class="small text-muted">total</div>
                    </div>
                </div>
            </div>

            <!-- Timesheet Content -->
            <div class="card-body p-0">
                @php
                    $yesterdayTimesheets = $user->timesheets->filter(function ($timesheet) use ($yesterday) {
                        return $timesheet->date->isSameDay($yesterday);
                    });
                    $todayTimesheets = $user->timesheets->filter(function ($timesheet) use ($today) {
                        return $timesheet->date->isSameDay($today);
                    });
                @endphp

                <!-- Yesterday Section -->
                <div class="border-bottom">
                    <div class="p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-muted">
                                <i class="fas fa-calendar-minus"></i> Yesterday ({{ $yesterday->format('M j') }})
                            </h6>
                            <span class="badge bg-secondary">
                                {{ number_format($user->yesterdayHours, 2) }}h
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        @if($yesterdayTimesheets->count() > 0)
                            @foreach($yesterdayTimesheets as $timesheet)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $timesheet->project->name ?? 'No Project' }}</div>
                                    <div class="text-muted small">
                                        {{ $timesheet->start_time->format('H:i') }} - 
                                        {{ $timesheet->end_time ? $timesheet->end_time->format('H:i') : 'ongoing' }}
                                    </div>
                                    @if($timesheet->description)
                                        <div class="text-muted small">{{ Str::limit($timesheet->description, 50) }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">
                                        {{ number_format($timesheet->getWorkedHours(), 2) }}h
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <div>No time logged</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Today Section -->
                <div>
                    <div class="p-3 bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-day"></i> Today ({{ $today->format('M j') }})
                            </h6>
                            <span class="badge bg-light text-dark">
                                {{ number_format($user->todayHours, 2) }}h
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        @if($todayTimesheets->count() > 0)
                            @foreach($todayTimesheets as $timesheet)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $timesheet->project->name ?? 'No Project' }}</div>
                                    <div class="text-muted small">
                                        {{ $timesheet->start_time->format('H:i') }} - 
                                        {{ $timesheet->end_time ? $timesheet->end_time->format('H:i') : 'ongoing' }}
                                        @if(!$timesheet->end_time)
                                            <span class="badge bg-warning text-dark ms-1">ACTIVE</span>
                                        @endif
                                    </div>
                                    @if($timesheet->description)
                                        <div class="text-muted small">{{ Str::limit($timesheet->description, 50) }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($timesheet->end_time)
                                        <span class="badge bg-success">
                                            {{ number_format($timesheet->getWorkedHours(), 2) }}h
                                        </span>
                                    @else
                                        @php
                                            $start = \Carbon\Carbon::parse($timesheet->start_time);
                                            $now = \Carbon\Carbon::now();
                                            $minutesWorked = $now->diffInMinutes($start);
                                            $breakMinutes = $timesheet->break_minutes ?? 0;
                                            $currentHours = max(0, ($minutesWorked - $breakMinutes) / 60);
                                        @endphp
                                        <span class="badge bg-warning text-dark">
                                            {{ number_format($currentHours, 2) }}h
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <div>No time logged today</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Status Footer -->
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        @if($user->todayHours > 0)
                            @if($todayTimesheets->where('end_time', null)->count() > 0)
                                <span class="text-success"><i class="fas fa-circle"></i> Working</span>
                            @else
                                <span class="text-info"><i class="fas fa-check-circle"></i> Logged time</span>
                            @endif
                        @else
                            <span class="text-muted"><i class="fas fa-minus-circle"></i> No activity</span>
                        @endif
                    </div>
                    <div class="small">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($users->count() === 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4>No Users Found</h4>
                <p class="text-muted">There are no users in the system to display.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Summary Statistics -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4>{{ $users->count() }}</h4>
                        <small>Total Users</small>
                    </div>
                    <div class="col-md-3">
                        <h4>{{ $users->where('todayHours', '>', 0)->count() }}</h4>
                        <small>Active Today</small>
                    </div>
                    <div class="col-md-3">
                        <h4>{{ number_format($users->sum('todayHours'), 2) }}h</h4>
                        <small>Total Hours Today</small>
                    </div>
                    <div class="col-md-3">
                        <h4>{{ number_format($users->sum('yesterdayHours'), 2) }}h</h4>
                        <small>Total Hours Yesterday</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000); // 5 minutes
    
    // Add visual indicators for active timesheets
    const activeTimesheets = document.querySelectorAll('.badge:contains("ACTIVE")');
    activeTimesheets.forEach(function(badge) {
        badge.style.animation = 'pulse 2s infinite';
    });
});

// Add CSS animation for active badges
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .badge-sm {
        font-size: 0.65rem;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .bg-outline-secondary {
        background: transparent !important;
        border: 1px solid #6c757d;
        color: #6c757d;
    }
`;
document.head.appendChild(style);
</script>
@endpush
