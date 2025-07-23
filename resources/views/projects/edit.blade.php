@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Project</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-project-diagram"></i> 
                    Editing: {{ $project->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $project->name) }}" required placeholder="Enter project name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="group_project_id" class="form-label">Project Group <span class="text-danger">*</span></label>
                            <select name="group_project_id" id="group_project_id" class="form-select @error('group_project_id') is-invalid @enderror" required>
                                <option value="">Select a group</option>
                                @foreach($groupProjects as $group)
                                    <option value="{{ $group->id }}" 
                                            {{ old('group_project_id', $project->group_project_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Describe the project objectives, scope, and requirements...">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="">Select priority</option>
                                <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Select status</option>
                                <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="estimated_hours" class="form-label">Estimated Hours</label>
                            <input type="number" name="estimated_hours" id="estimated_hours" 
                                   class="form-control @error('estimated_hours') is-invalid @enderror" 
                                   value="{{ old('estimated_hours', $project->estimated_hours) }}" min="0" step="0.5" placeholder="0">
                            @error('estimated_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="budget" class="form-label">Budget ($)</label>
                            <input type="number" name="budget" id="budget" 
                                   class="form-control @error('budget') is-invalid @enderror" 
                                   value="{{ old('budget', $project->budget) }}" min="0" step="0.01" placeholder="0.00">
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Team Members</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    @foreach($users as $user)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="assigned_users[]" 
                                                   value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                   {{ in_array($user->id, old('assigned_users', $assignedUsers)) ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex align-items-center" for="user_{{ $user->id }}">
                                                @if($user->photo)
                                                    <img src="{{ Storage::url($user->photo) }}" alt="Profile" class="rounded-circle me-2" width="24" height="24">
                                                @else
                                                    <i class="fas fa-user-circle me-2"></i>
                                                @endif
                                                <div>
                                                    <div>{{ $user->name }}</div>
                                                    <small class="text-muted">{{ $user->position ?? 'Staff' }}</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($users->count() == 0)
                                    <p class="text-muted text-center py-3">No users available to assign.</p>
                                @endif
                            </div>
                        </div>
                        @error('assigned_users')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-2"><i class="fas fa-info-circle"></i> Project Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Selected Team Members:</small>
                                    <div id="selected-members" class="fw-bold">None selected</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Timeline:</small>
                                    <div id="project-timeline" class="fw-bold">Not set</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($project->timesheets && $project->timesheets->count() > 0)
                        @php
                            $totalHours = $project->timesheets->sum(function($ts) { return $ts->getWorkedHours(); });
                        @endphp
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This project has {{ $project->timesheets->count() }} logged time entries 
                            totaling {{ number_format($totalHours, 1) }} hours. Deleting this project will also remove all associated time logs.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userCheckboxes = document.querySelectorAll('input[name="assigned_users[]"]');
    const selectedMembersDiv = document.getElementById('selected-members');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const timelineDiv = document.getElementById('project-timeline');

    function updateSelectedMembers() {
        const selectedUsers = [];
        userCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                const userName = label.querySelector('div div').textContent;
                selectedUsers.push(userName);
            }
        });

        if (selectedUsers.length === 0) {
            selectedMembersDiv.textContent = 'None selected';
        } else if (selectedUsers.length <= 3) {
            selectedMembersDiv.textContent = selectedUsers.join(', ');
        } else {
            selectedMembersDiv.textContent = `${selectedUsers.slice(0, 3).join(', ')} +${selectedUsers.length - 3} more`;
        }
    }

    function updateTimeline() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate && !endDate) {
            timelineDiv.textContent = 'Not set';
        } else if (startDate && !endDate) {
            timelineDiv.textContent = `Starts ${new Date(startDate).toLocaleDateString()}`;
        } else if (!startDate && endDate) {
            timelineDiv.textContent = `Due ${new Date(endDate).toLocaleDateString()}`;
        } else {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            timelineDiv.textContent = `${start.toLocaleDateString()} - ${end.toLocaleDateString()} (${diffDays} days)`;
        }
    }

    // Event listeners
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedMembers);
    });

    startDateInput.addEventListener('change', updateTimeline);
    endDateInput.addEventListener('change', updateTimeline);

    // Initial updates
    updateSelectedMembers();
    updateTimeline();

    // Validate end date is after start date
    endDateInput.addEventListener('change', function() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alert('End date cannot be before start date.');
            endDateInput.value = '';
            updateTimeline();
        }
    });
});
</script>
@endpush
