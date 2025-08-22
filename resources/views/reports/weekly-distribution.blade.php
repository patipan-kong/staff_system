@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-chart-bar"></i> Weekly Distribution Report</h1>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="week" name="week" value="{{ $weekStart->format('Y-\WW') }}" class="form-control" onchange="this.form.submit()">
                <button type="button" class="btn btn-outline-secondary" onclick="location.href='{{ route('reports.weekly') }}'">
                    <i class="fas fa-calendar-day"></i> This Week
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Week Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('reports.weekly', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chevron-left"></i> Previous Week
                    </a>
                    <h4 class="mb-0">
                        {{ $weekStart->format('M j') }} - {{ $weekEnd->format('M j, Y') }}
                    </h4>
                    <a href="{{ route('reports.weekly', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                        Next Week <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white stats-card">
            <div class="card-body">
                <h5><i class="fas fa-clock"></i> Total Hours</h5>
                <h2>{{ number_format($weeklyStats['total_hours'], 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white stats-card">
            <div class="card-body">
                <h5><i class="fas fa-tasks"></i> Time Entries</h5>
                <h2>{{ $weeklyStats['total_entries'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white stats-card">
            <div class="card-body">
                <h5><i class="fas fa-users"></i> Active Users</h5>
                <h2>{{ $weeklyStats['users_with_time'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark stats-card">
            <div class="card-body">
                <h5><i class="fas fa-calendar-times"></i> On Leave</h5>
                <h2>{{ $weeklyStats['users_on_leave'] }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Calendar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-week"></i> Weekly Distribution Calendar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 calendar-table">
                        <thead class="table-dark">
                            <tr>
                                <th class="user-info-cell" style="width: 200px;">Team Member</th>
                                @foreach($weekDays as $day)
                                    <th class="text-center" style="width: 120px;">
                                        <div>{{ $day->format('D') }}</div>
                                        <small class="text-muted">{{ $day->format('M j') }}</small>
                                        @if(isset($weeklyStats['daily_totals'][$day->format('Y-m-d')]))
                                            <div class="badge bg-light text-dark mt-1">
                                                {{ number_format($weeklyStats['daily_totals'][$day->format('Y-m-d')], 1) }}h
                                            </div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="align-middle user-info-cell">
                                        <div class="d-flex align-items-center">
                                            @if($user->photo)
                                                <img src="{{ asset(Storage::url($user->photo)) }}" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <i class="fas fa-user-circle me-2 text-muted" style="font-size: 32px;"></i>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <small class="text-muted">
                                                    {{ $user->department->name ?? 'No Department' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($weekDays as $day)
                                        @php
                                            $dateStr = $day->format('Y-m-d');
                                            $userTimesheets = collect();
                                            
                                            // Safely get user timesheets
                                            if ($timesheets->has($user->id) && $timesheets->get($user->id)->has($dateStr)) {
                                                $userTimesheets = $timesheets->get($user->id)->get($dateStr);
                                            }
                                            
                                            $userLeaves = $leaves[$user->id] ?? collect();
                                            $isOnLeave = $userLeaves->filter(function($leave) use ($dateStr) {
                                                if (!$leave->start_date || !$leave->end_date) {
                                                    return false; // Skip if dates are not set
                                                }
                                                return strtotime($dateStr) >= strtotime($leave->start_date) && strtotime($dateStr) <= strtotime($leave->end_date);
                                            })->count() > 0;
                                            
                                            $totalHours = $userTimesheets->sum(function($ts) { return $ts ? $ts->getWorkedHours() : 0; });
                                        @endphp
                                        <td class="text-center position-relative timesheet-cell" 
                                            data-user-id="{{ $user->id }}" 
                                            data-date="{{ $dateStr }}"
                                            style="cursor: pointer; height: 80px; vertical-align: middle;">
                                            
                                            @if($isOnLeave)
                                                <div class="badge bg-warning text-dark w-100">
                                                    <i class="fas fa-calendar-times"></i><br>
                                                    <small>On Leave</small>
                                                </div>
                                            @elseif($userTimesheets->count() > 0)
                                                <div class="d-flex flex-column h-100 justify-content-center">
                                                    <div class="fw-bold text-success">{{ number_format($totalHours, 2) }}h</div>
                                                    <small class="text-muted">{{ $userTimesheets->count() }} entries</small>
                                                    
                                                    <!-- Project indicators -->
                                                    <div class="mt-1">
                                                        @foreach($userTimesheets->take(3) as $ts)
                                                            @if($ts && $ts->project)
                                                                <span class="badge" 
                                                                      style="background-color: {{ $ts->project->groupProject->color ?? '#6c757d' }}; font-size: 8px;"
                                                                      title="{{ $ts->project->name ?? 'Unknown' }}">
                                                                    {{ substr($ts->project->name ?? 'Unknown', 0, 3) }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                        @if($userTimesheets->count() > 3)
                                                            <span class="badge bg-secondary" style="font-size: 8px;">+{{ $userTimesheets->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    <i class="fas fa-minus"></i><br>
                                                    <small>No time logged</small>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Distribution -->
@if(count($weeklyStats['project_distribution']) > 0)
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Project Time Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($weeklyStats['project_distribution'] as $projectName => $hours)
                        @php
                            $percentage = $weeklyStats['total_hours'] > 0 ? ($hours / $weeklyStats['total_hours']) * 100 : 0;
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-truncate me-2">{{ $projectName }}</span>
                                <span class="fw-bold">{{ number_format($hours, 2) }}h</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Active Projects</h5>
            </div>
            <div class="card-body">
                @if($activeProjects->count() > 0)
                    @foreach($activeProjects->take(10) as $project)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge me-2" style="background-color: {{ $project->groupProject->color ?? '#6c757d' }}">
                                <i class="{{ $project->groupProject->icon ?? 'fas fa-project-diagram' }}"></i>
                            </span>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $project->name }}</div>
                                <small class="text-muted">{{ $project->groupProject->name ?? 'No Group' }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">No active projects</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Timesheet Detail Modal -->
<div class="modal fade" id="timesheetModal" tabindex="-1" aria-labelledby="timesheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timesheetModalLabel">
                    <i class="fas fa-clock"></i> Timesheet Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="timesheetModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
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
    // Handle timesheet cell clicks
    document.querySelectorAll('.timesheet-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const date = this.dataset.date;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('timesheetModal'));
            modal.show();
            
            // Load timesheet data
            fetch(`{{ route('reports.timesheet-data') }}?user_id=${userId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    const modalBody = document.getElementById('timesheetModalBody');
                    
                    if (data.timesheets.length === 0) {
                        modalBody.innerHTML = '<p class="text-center text-muted">No timesheet entries for this date.</p>';
                        return;
                    }
                    
                    let html = `
                        <div class="mb-3">
                            <h6>Total Hours: <span class="badge bg-primary">${data.total_hours}h</span></h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Time</th>
                                        <th>Hours</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.timesheets.forEach(ts => {
                        html += `
                            <tr>
                                <td>
                                    <span class="badge me-2" style="background-color: ${ts.group_color}">
                                        ${ts.group_name || 'No Group'}
                                    </span>
                                    ${ts.project_name}
                                </td>
                                <td>${ts.start_time} - ${ts.end_time}</td>
                                <td><span class="badge bg-success">${ts.worked_hours}h</span></td>
                                <td>${ts.description || '<em>No description</em>'}</td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading timesheet data:', error);
                    document.getElementById('timesheetModalBody').innerHTML = 
                        '<p class="text-center text-danger">Error loading timesheet data.</p>';
                });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.timesheet-cell:hover {
    background-color: #f8f9fa !important;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.table-responsive {
    max-height: 70vh;
    overflow-x: auto;
}

.progress {
    background-color: #e9ecef;
}

.badge {
    font-size: 0.75em;
}

.calendar-table {
    min-width: 900px;
}

.calendar-table th,
.calendar-table td {
    border: 2px solid #dee2e6 !important;
}

.calendar-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

.calendar-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.user-info-cell {
    background-color: #ffffff !important;
    position: sticky;
    left: 0;
    z-index: 5;
    border-right: 3px solid #dee2e6 !important;
}

.stats-card {
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

@media (max-width: 992px) {
    .timesheet-cell {
        height: 60px !important;
        font-size: 0.8em;
    }
    
    .timesheet-cell .badge {
        font-size: 0.6em;
    }
    
    .user-info-cell {
        min-width: 150px;
    }
}

@media (max-width: 768px) {
    .calendar-table {
        min-width: 700px;
    }
    
    .timesheet-cell {
        height: 50px !important;
        font-size: 0.7em;
    }
    
    .user-info-cell {
        min-width: 120px;
    }
}
</style>
@endpush
