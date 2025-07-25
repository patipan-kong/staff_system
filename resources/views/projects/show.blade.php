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
        <div class="card mb-4" style="border-left: 4px solid {{ $project->color ?? '#007bff' }};">
            <div class="card-header text-white" style="background-color: {{ $project->color ?? '#007bff' }};">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 d-flex align-items-center">
                        @if($project->icon)
                            <i class="{{ $project->icon }} me-2"></i>
                        @endif
                        {{ $project->name }}
                    </h4>
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

                @if($project->po_date || $project->due_date || $project->on_production_date)
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-muted">PO Date</h6>
                        <p>
                            @if($project->po_date)
                                <i class="fas fa-calendar text-info"></i> {{ $project->po_date->format('F j, Y') }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Due Date</h6>
                        <p>
                            @if($project->due_date)
                                <i class="fas fa-calendar text-danger"></i> {{ $project->due_date->format('F j, Y') }}
                                @if($project->due_date->isPast() && $project->status !== 'completed')
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">On Production Date</h6>
                        <p>
                            @if($project->on_production_date)
                                <i class="fas fa-calendar text-success"></i> {{ $project->on_production_date->format('F j, Y') }}
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
                    @foreach($project->projectAssigns as $assignment)
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    @if($assignment->user->photo)
                                        <img src="{{ Storage::url($assignment->user->photo) }}" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $assignment->user->name }}</h6>
                                        <small class="text-muted">{{ $assignment->user->position ?? 'Staff' }}</small>
                                        @if($assignment->user->department)
                                            <br><small class="text-info">{{ $assignment->user->department->name }}</small>
                                        @endif
                                    </div>
                                    @if($assignment->estimated_hours)
                                        <div class="text-end">
                                            <div class="badge bg-primary">
                                                {{ $assignment->estimated_hours }}h
                                            </div>
                                            <br><small class="text-muted">estimated</small>
                                        </div>
                                    @endif
                                </div>
                                @if($assignment->assigned_date)
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> Assigned: {{ $assignment->assigned_date->format('M j, Y') }}
                                    </small>
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
                    <div class="col-4 border-end">
                        <h4 class="text-primary mb-0">{{ number_format($totalHours, 1) }}</h4>
                        <small class="text-muted">Hours Logged</small>
                    </div>
                    <div class="col-4 border-end">
                        <h4 class="text-info mb-0">{{ $project->estimated_hours ?? '--' }}</h4>
                        <small class="text-muted">Project Est.</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success mb-0">{{ number_format($project->getTotalEstimatedHours(), 1) }}</h4>
                        <small class="text-muted">Team Est.</small>
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
        @if($project->po_date || $project->due_date || $project->on_production_date)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Timeline</h6>
            </div>
            <div class="card-body">
                @if($project->po_date)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-file-contract text-info me-2"></i>
                        <div>
                            <strong>PO Date:</strong><br>
                            <small>{{ $project->po_date->format('F j, Y') }}</small>
                        </div>
                    </div>
                @endif
                @if($project->on_production_date)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-cog text-success me-2"></i>
                        <div>
                            <strong>On Production:</strong><br>
                            <small>{{ $project->on_production_date->format('F j, Y') }}</small>
                        </div>
                    </div>
                @endif
                @if($project->due_date)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-flag {{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-danger' : 'text-primary' }} me-2"></i>
                        <div>
                            <strong>{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'Overdue:' : 'Due:' }}</strong><br>
                            <small>{{ $project->due_date->format('F j, Y') }}</small>
                            @if($project->due_date->isFuture())
                                <br><small class="text-muted">{{ $project->due_date->diffForHumans() }}</small>
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
