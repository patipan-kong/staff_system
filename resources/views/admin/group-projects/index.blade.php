@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-layer-group"></i> Group Project Management</h1>
        <a href="{{ route('admin.group-projects.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Group Project
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> All Group Projects</h5>
            </div>
            <div class="card-body">
                @if($groupProjects->count() > 0)
                    <div class="row">
                        @foreach($groupProjects as $groupProject)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center" 
                                         style="background-color: {{ $groupProject->color }}20; border-bottom: 2px solid {{ $groupProject->color }}">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $groupProject->icon ?? 'fas fa-folder' }} fa-lg me-2" 
                                               style="color: {{ $groupProject->color }}"></i>
                                            <h6 class="mb-0">{{ $groupProject->name }}</h6>
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
                                        <p class="card-text text-muted">
                                            {{ Str::limit($groupProject->description, 80) ?: 'No description available' }}
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-project-diagram"></i>
                                                {{ $groupProject->projects_count }} {{ Str::plural('project', $groupProject->projects_count) }}
                                            </small>
                                            <small class="text-muted">
                                                Created {{ $groupProject->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('admin.group-projects.show', $groupProject) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.group-projects.edit', $groupProject) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.group-projects.destroy', $groupProject) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this group project?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        {{ $groupProject->projects_count > 0 ? 'disabled' : '' }}
                                                        title="{{ $groupProject->projects_count > 0 ? 'Cannot delete - contains projects' : 'Delete group project' }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-layer-group fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Group Projects Found</h5>
                        <p class="text-muted">Create your first group project to organize related projects.</p>
                        <a href="{{ route('admin.group-projects.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Group Project
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($groupProjects->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Group Project Statistics</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalGroups = $groupProjects->count();
                        $activeGroups = $groupProjects->where('status', 'active')->count();
                        $completedGroups = $groupProjects->where('status', 'completed')->count();
                        $totalProjects = $groupProjects->sum('projects_count');
                    @endphp
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $totalGroups }}</h4>
                            <small class="text-muted">Total Groups</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $activeGroups }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $totalProjects }}</h4>
                            <small class="text-muted">Total Projects</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary">{{ $completedGroups }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Status Distribution</h6>
                </div>
                <div class="card-body">
                    @php
                        $statusCounts = $groupProjects->groupBy('status')->map->count();
                    @endphp
                    @foreach(['active', 'planned', 'completed', 'inactive'] as $status)
                        @if($statusCounts->get($status, 0) > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-capitalize">{{ $status }}:</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 10px;">
                                        <div class="progress-bar {{ $statusClasses[$status] ?? 'bg-secondary' }}" 
                                             style="width: {{ ($statusCounts->get($status, 0) / $totalGroups) * 100 }}%"></div>
                                    </div>
                                    <span class="badge {{ $statusClasses[$status] ?? 'bg-secondary' }}">
                                        {{ $statusCounts->get($status, 0) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
