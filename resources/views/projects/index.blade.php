@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-project-diagram"></i> Projects</h1>
        @if(auth()->user()->isManagerOrAdmin())
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Project
            </a>
        @endif
    </div>
</div>

@if($projects->count() > 0)
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $project->name }}</h5>
                    <div class="d-flex gap-1">
                        @php
                            $statusClasses = [
                                'planning' => 'bg-secondary',
                                'active' => 'bg-success',
                                'on_hold' => 'bg-warning',
                                'completed' => 'bg-primary'
                            ];
                            $priorityClasses = [
                                'low' => 'bg-info',
                                'medium' => 'bg-warning',
                                'high' => 'bg-danger'
                            ];
                        @endphp
                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                        <span class="badge {{ $priorityClasses[$project->priority] ?? 'bg-info' }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">
                        {{ Str::limit($project->description, 100) }}
                    </p>
                    
                    <div class="mb-3">
                        <small class="text-muted">Group:</small>
                        <span class="badge bg-secondary">{{ $project->groupProject->name ?? 'N/A' }}</span>
                    </div>

                    @if($project->timesheets_count ?? $project->timesheets->count())
                        @php
                            $totalHours = $project->timesheets->sum(function($ts) { return $ts->getWorkedHours(); });
                        @endphp
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="mb-0 text-primary">{{ number_format($totalHours, 1) }}h</h6>
                                    <small class="text-muted">Logged</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="mb-0 text-info">{{ $project->estimated_hours ?? '--' }}h</h6>
                                <small class="text-muted">Estimated</small>
                            </div>
                        </div>
                    @endif

                    @if($project->users && $project->users->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Team ({{ $project->users->count() }}):</small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($project->users->take(3) as $user)
                                    @if($user->photo)
                                        <img src="{{ Storage::url($user->photo) }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="24" height="24"
                                             title="{{ $user->name }}">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                             style="width: 24px; height: 24px; font-size: 12px;" 
                                             title="{{ $user->name }}">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                @endforeach
                                @if($project->users->count() > 3)
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted" 
                                         style="width: 24px; height: 24px; font-size: 10px;">
                                        +{{ $project->users->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($project->start_date || $project->end_date)
                        <div class="mb-3">
                            <small class="text-muted">Timeline:</small><br>
                            <small>
                                @if($project->start_date)
                                    <i class="fas fa-play text-success"></i> {{ $project->start_date->format('M j, Y') }}
                                @endif
                                @if($project->start_date && $project->end_date)
                                    -
                                @endif
                                @if($project->end_date)
                                    <i class="fas fa-flag text-danger"></i> {{ $project->end_date->format('M j, Y') }}
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if(auth()->user()->isManagerOrAdmin())
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete({{ $project->id }}, '{{ $project->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
            <h4>No projects found</h4>
            <p class="text-muted">
                @if(auth()->user()->isManagerOrAdmin())
                    Start by creating your first project.
                @else
                    You haven't been assigned to any projects yet.
                @endif
            </p>
            @if(auth()->user()->isManagerOrAdmin())
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Project
                </a>
            @endif
        </div>
    </div>
@endif
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
