@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-layer-group"></i> Group Project Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.group-projects.edit', $groupProject) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.group-projects.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" 
                 style="background-color: {{ $groupProject->color }}20; border-bottom: 2px solid {{ $groupProject->color }}">
                <div class="d-flex align-items-center">
                    <i class="{{ $groupProject->icon ?? 'fas fa-folder' }} fa-lg me-2" 
                       style="color: {{ $groupProject->color }}"></i>
                    <h5 class="mb-0">{{ $groupProject->name }}</h5>
                </div>
                @php
                    $statusClasses = [
                        'active' => 'bg-success',
                        'inactive' => 'bg-secondary',
                        'planned' => 'bg-info',
                        'completed' => 'bg-primary'
                    ];
                @endphp
                <span class="badge {{ $statusClasses[$groupProject->status] ?? 'bg-secondary' }}">
                    {{ ucfirst($groupProject->status) }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Name:</th>
                        <td><strong>{{ $groupProject->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge {{ $statusClasses[$groupProject->status] ?? 'bg-secondary' }} fs-6">
                                {{ ucfirst($groupProject->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Projects:</th>
                        <td>
                            <span class="badge bg-info fs-6">{{ $groupProject->projects->count() }} {{ Str::plural('project', $groupProject->projects->count()) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $groupProject->created_at->format('M d, Y') }}</td>
                    </tr>
                    @if($groupProject->updated_at != $groupProject->created_at)
                        <tr>
                            <th>Updated:</th>
                            <td>{{ $groupProject->updated_at->format('M d, Y') }}</td>
                        </tr>
                    @endif
                </table>
                
                @if($groupProject->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $groupProject->description }}</p>
                    </div>
                @endif

                @if($groupProject->projects->count() > 0)
                    <div class="mt-4">
                        <h6>Project Statistics:</h6>
                        @php
                            $totalEstimatedHours = $groupProject->projects->sum('estimated_hours');
                            $totalActualHours = $groupProject->projects->sum('actual_hours');
                            $totalBudget = $groupProject->projects->sum('budget');
                            $completedProjects = $groupProject->projects->where('status', 'completed')->count();
                        @endphp
                        <div class="row text-center">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Estimated Hours</small>
                                <strong>{{ number_format($totalEstimatedHours, 1) }}h</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Actual Hours</small>
                                <strong>{{ number_format($totalActualHours, 1) }}h</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Total Budget</small>
                                <strong>${{ number_format($totalBudget, 2) }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Completed</small>
                                <strong>{{ $completedProjects }}/{{ $groupProject->projects->count() }}</strong>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Projects in this Group</h5>
                @if(auth()->user()->isManagerOrAdmin())
                    <a href="{{ route('projects.create') }}?group_project_id={{ $groupProject->id }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Add Project
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($groupProject->projects->count() > 0)
                    <div class="row">
                        @foreach($groupProject->projects as $project)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center" 
                                         style="background-color: {{ $project->color ?? $groupProject->color }}20; border-bottom: 2px solid {{ $project->color ?? $groupProject->color }}">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $project->icon ?? 'fas fa-project-diagram' }} me-2" 
                                               style="color: {{ $project->color ?? $groupProject->color }}"></i>
                                            <h6 class="mb-0">{{ Str::limit($project->name, 20) }}</h6>
                                        </div>
                                        @php
                                            $projectStatusClasses = [
                                                'not_started' => 'bg-secondary',
                                                'in_progress' => 'bg-warning text-dark',
                                                'completed' => 'bg-success',
                                                'on_hold' => 'bg-info',
                                                'cancelled' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $projectStatusClasses[$project->status] ?? 'bg-secondary' }} small">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <p class="card-text small text-muted mb-2">
                                            {{ Str::limit($project->description, 60) ?: 'No description' }}
                                        </p>
                                        
                                        <div class="d-flex justify-content-between text-sm mb-2">
                                            <span class="text-muted">
                                                <i class="fas fa-users"></i> {{ $project->users->count() }}
                                            </span>
                                            <span class="text-muted">
                                                <i class="fas fa-clock"></i> {{ $project->estimated_hours }}h
                                            </span>
                                        </div>
                                        
                                        @if($project->start_date && $project->end_date)
                                            <small class="text-muted d-block">
                                                {{ $project->start_date->format('M d') }} - {{ $project->end_date->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent p-2">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->isManagerOrAdmin())
                                                <a href="{{ route('projects.edit', $project) }}" 
                                                   class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Projects Yet</h5>
                        <p class="text-muted">This group project doesn't have any projects assigned yet.</p>
                        @if(auth()->user()->isManagerOrAdmin())
                            <a href="{{ route('projects.create') }}?group_project_id={{ $groupProject->id }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add First Project
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($groupProject->projects->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Progress Overview</h6>
                </div>
                <div class="card-body">
                    @php
                        $projectsByStatus = $groupProject->projects->groupBy('status');
                        $totalProjects = $groupProject->projects->count();
                    @endphp
                    
                    @foreach(['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'] as $status)
                        @if($projectsByStatus->has($status))
                            @php
                                $count = $projectsByStatus[$status]->count();
                                $percentage = ($count / $totalProjects) * 100;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-capitalize">{{ str_replace('_', ' ', $status) }}:</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 120px; height: 10px;">
                                        <div class="progress-bar {{ $projectStatusClasses[$status] ?? 'bg-secondary' }}" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="badge {{ $projectStatusClasses[$status] ?? 'bg-secondary' }}">
                                        {{ $count }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
