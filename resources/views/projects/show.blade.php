@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-eye"></i> Project Details</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()->isManagerOrAdmin())
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $project->id }}, '{{ $project->name }}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            @endif
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Project Info Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $project->name }}</h4>
                    <div class="d-flex gap-2">
                        @php
                            $statusClasses = [
                                'planning' => 'bg-secondary',
                                'active' => 'bg-success',
                                'on_hold' => 'bg-warning text-dark',
                                'completed' => 'bg-info'
                            ];
                            $priorityClasses = [
                                'low' => 'bg-info',
                                'medium' => 'bg-warning text-dark',
                                'high' => 'bg-danger'
                            ];
                        @endphp
                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                        <span class="badge {{ $priorityClasses[$project->priority] ?? 'bg-info' }} fs-6">
                            {{ ucfirst($project->priority) }} Priority
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($project->description)
                    <p class="lead">{{ $project->description }}</p>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Project Group</h6>
                        <p><span class="badge bg-secondary fs-6">{{ $project->groupProject->name ?? 'N/A' }}</span></p>
                    </div>
                    @if($project->budget)
                    <div class="col-md-6">
                        <h6 class="text-muted">Budget</h6>
                        <p class="text-success fw-bold">${{ number_format($project->budget, 2) }}</p>
                    </div>
                    @endif
                </div>

                @if($project->start_date || $project->end_date)
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Start Date</h6>
                        <p>
                            @if($project->start_date)
                                <i class="fas fa-calendar text-success"></i> {{ $project->start_date->format('F j, Y') }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">End Date</h6>
                        <p>
                            @if($project->end_date)
                                <i class="fas fa-calendar text-danger"></i> {{ $project->end_date->format('F j, Y') }}
                                @if($project->end_date->isPast() && $project->status !== 'completed')
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Team Members Card -->
        @if($project->users && $project->users->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Team Members ({{ $project->users->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($project->users as $user)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            @if($user->photo)
                                <img src="{{ Storage::url($user->photo) }}" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->position ?? 'Staff' }}</small>
                                @if($user->department)
                                    <br><small class="text-info">{{ $user->department->name }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Timesheets Card -->
        @if($project->timesheets && $project->timesheets->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Time Entries</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Hours</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->timesheets->take(10) as $timesheet)
                            <tr>
                                <td>{{ $timesheet->date->format('M j, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($timesheet->user->photo)
                                            <img src="{{ Storage::url($timesheet->user->photo) }}" alt="Profile" class="rounded-circle me-2" width="24" height="24">
                                        @else
                                            <i class="fas fa-user-circle me-2"></i>
                                        @endif
                                        {{ $timesheet->user->name }}
                                    </div>
                                </td>
                                <td><strong>{{ $timesheet->getWorkedTime() }}</strong></td>
                                <td>{{ Str::limit($timesheet->description, 50) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Project Stats Card -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Project Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h4 class="text-primary mb-0">{{ number_format($totalHours, 1) }}</h4>
                        <small class="text-muted">Hours Logged</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">{{ $project->estimated_hours ?? '--' }}</h4>
                        <small class="text-muted">Estimated</small>
                    </div>
                </div>
                
                @if($project->estimated_hours)
                    <hr>
                    <div class="progress mb-2">
                        @php
                            $progressPercent = ($totalHours / $project->estimated_hours) * 100;
                            $progressClass = $progressPercent > 100 ? 'bg-danger' : ($progressPercent > 80 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="progress-bar {{ $progressClass }}" style="width: {{ min($progressPercent, 100) }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ number_format($progressPercent, 1) }}% of estimated time
                        @if($progressPercent > 100)
                            <span class="text-danger">(Over budget)</span>
                        @endif
                    </small>
                @endif

                @if($project->budget && isset($totalCost))
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Cost so far:</span>
                        <strong class="text-success">${{ number_format($totalCost, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Budget:</span>
                        <strong>${{ number_format($project->budget, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(in_array(auth()->id(), $project->users->pluck('id')->toArray()))
                        <a href="{{ route('timesheets.create') }}?project_id={{ $project->id }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Log Time
                        </a>
                    @endif
                    @if(auth()->user()->isManagerOrAdmin())
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Project
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $project->id }}, '{{ $project->name }}')">
                            <i class="fas fa-trash"></i> Delete Project
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Timeline Card -->
        @if($project->start_date || $project->end_date)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Timeline</h6>
            </div>
            <div class="card-body">
                @if($project->start_date)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-play text-success me-2"></i>
                        <div>
                            <strong>Started:</strong><br>
                            <small>{{ $project->start_date->format('F j, Y') }}</small>
                        </div>
                    </div>
                @endif
                @if($project->end_date)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-flag {{ $project->end_date->isPast() && $project->status !== 'completed' ? 'text-danger' : 'text-primary' }} me-2"></i>
                        <div>
                            <strong>{{ $project->end_date->isPast() && $project->status !== 'completed' ? 'Overdue:' : 'Due:' }}</strong><br>
                            <small>{{ $project->end_date->format('F j, Y') }}</small>
                            @if($project->end_date->isFuture())
                                <br><small class="text-muted">{{ $project->end_date->diffForHumans() }}</small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(projectId, projectName) {
    if (confirm(`Are you sure you want to delete the project "${projectName}"? This action cannot be undone and will also delete all associated timesheets.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/projects/' + projectId;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
