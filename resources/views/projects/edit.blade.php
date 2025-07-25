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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label for="icon" class="form-label">Icon</label>
                            <select name="icon" id="icon" class="form-select @error('icon') is-invalid @enderror">
                                <option value="">Select icon</option>
                                <option value="fas fa-laptop-code" {{ old('icon', $project->icon) == 'fas fa-laptop-code' ? 'selected' : '' }}>💻 Development</option>
                                <option value="fas fa-paint-brush" {{ old('icon', $project->icon) == 'fas fa-paint-brush' ? 'selected' : '' }}>🎨 Design</option>
                                <option value="fas fa-chart-line" {{ old('icon', $project->icon) == 'fas fa-chart-line' ? 'selected' : '' }}>📊 Analytics</option>
                                <option value="fas fa-mobile-alt" {{ old('icon', $project->icon) == 'fas fa-mobile-alt' ? 'selected' : '' }}>📱 Mobile</option>
                                <option value="fas fa-server" {{ old('icon', $project->icon) == 'fas fa-server' ? 'selected' : '' }}>🖥️ Server</option>
                                <option value="fas fa-database" {{ old('icon', $project->icon) == 'fas fa-database' ? 'selected' : '' }}>🗄️ Database</option>
                                <option value="fas fa-shield-alt" {{ old('icon', $project->icon) == 'fas fa-shield-alt' ? 'selected' : '' }}>🛡️ Security</option>
                                <option value="fas fa-cogs" {{ old('icon', $project->icon) == 'fas fa-cogs' ? 'selected' : '' }}>⚙️ System</option>
                                <option value="fas fa-rocket" {{ old('icon', $project->icon) == 'fas fa-rocket' ? 'selected' : '' }}>🚀 Launch</option>
                                <option value="fas fa-bug" {{ old('icon', $project->icon) == 'fas fa-bug' ? 'selected' : '' }}>🐛 Bug Fix</option>
                            </select>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="color" class="form-label">Color</label>
                            <div class="input-group">
                                <input type="color" name="color" id="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       value="{{ old('color', $project->color ?? '#007bff') }}" title="Choose project color">
                                <input type="text" class="form-control" id="color-text" 
                                       value="{{ old('color', $project->color ?? '#007bff') }}" placeholder="#007bff" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
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
                            <label for="po_date" class="form-label">PO Date</label>
                            <input type="date" name="po_date" id="po_date" 
                                   class="form-control @error('po_date') is-invalid @enderror" 
                                   value="{{ old('po_date', $project->po_date ? $project->po_date->format('Y-m-d') : '') }}">
                            @error('po_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" 
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   value="{{ old('due_date', $project->due_date ? $project->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="on_production_date" class="form-label">On Production Date</label>
                            <input type="date" name="on_production_date" id="on_production_date" 
                                   class="form-control @error('on_production_date') is-invalid @enderror" 
                                   value="{{ old('on_production_date', $project->on_production_date ? $project->on_production_date->format('Y-m-d') : '') }}">
                            @error('on_production_date')
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
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card">
                                            <div class="card-body p-2">
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
                                                            <div class="fw-bold">{{ $user->name }}</div>
                                                            <small class="text-muted">{{ $user->position ?? 'Staff' }}</small>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="mt-2 user-hours-input" id="hours_input_{{ $user->id }}" 
                                                     style="display: {{ in_array($user->id, $assignedUsers) ? 'block' : 'none' }};">
                                                    <label for="hours_{{ $user->id }}" class="form-label text-muted small">Estimated Hours</label>
                                                    <input type="number" class="form-control form-control-sm estimated-hours" 
                                                           id="hours_{{ $user->id }}" name="user_estimated_hours[{{ $user->id }}]" 
                                                           value="{{ old('user_estimated_hours.'.$user->id, $userEstimatedHours[$user->id] ?? '') }}" 
                                                           min="0" step="0.5" placeholder="0.0"
                                                           data-user-id="{{ $user->id }}"
                                                           data-salary="{{ $user->salary ?? 0 }}">
                                                    <div class="estimated-cost mt-1" id="cost_{{ $user->id }}" style="display: none;">
                                                        <small class="text-success">
                                                            <i class="fas fa-dollar-sign"></i>
                                                            Estimated Cost: <span class="fw-bold cost-amount">$0.00</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
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
                                <div class="col-md-4">
                                    <small class="text-muted">Selected Team Members:</small>
                                    <div id="selected-members" class="fw-bold">None selected</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Timeline:</small>
                                    <div id="project-timeline" class="fw-bold">Not set</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Total Estimated Cost:</small>
                                    <div id="total-estimated-cost" class="fw-bold text-success">$0.00</div>
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
    const poDateInput = document.getElementById('po_date');
    const dueDateInput = document.getElementById('due_date');
    const timelineDiv = document.getElementById('project-timeline');
    const totalCostDiv = document.getElementById('total-estimated-cost');
    const estimatedHoursInputs = document.querySelectorAll('.estimated-hours');

    // Cost calculation function
    function calculateHourlyCost(salary) {
        // Formula: salary / (22 working days * 8 hours per day)
        return salary / (22 * 8);
    }

    function updateIndividualCost(userId) {
        const hoursInput = document.getElementById(`hours_${userId}`);
        const costDiv = document.getElementById(`cost_${userId}`);
        const costAmount = costDiv.querySelector('.cost-amount');
        
        if (!hoursInput || !costDiv || !costAmount) return;
        
        const hours = parseFloat(hoursInput.value) || 0;
        const salary = parseFloat(hoursInput.dataset.salary) || 0;
        
        if (hours > 0 && salary > 0) {
            const hourlyCost = calculateHourlyCost(salary);
            const totalCost = hours * hourlyCost;
            costAmount.textContent = `$${totalCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            costDiv.style.display = 'block';
        } else {
            costDiv.style.display = 'none';
        }
        
        updateTotalCost();
    }

    function updateTotalCost() {
        let totalCost = 0;
        
        userCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const userId = checkbox.value;
                const hoursInput = document.getElementById(`hours_${userId}`);
                
                if (hoursInput) {
                    const hours = parseFloat(hoursInput.value) || 0;
                    const salary = parseFloat(hoursInput.dataset.salary) || 0;
                    
                    if (hours > 0 && salary > 0) {
                        const hourlyCost = calculateHourlyCost(salary);
                        totalCost += hours * hourlyCost;
                    }
                }
            }
        });
        
        totalCostDiv.textContent = `$${totalCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    function updateSelectedMembers() {
        const selectedUsers = [];
        userCheckboxes.forEach(checkbox => {
            const userId = checkbox.value;
            const hoursInput = document.getElementById(`hours_input_${userId}`);
            const costDiv = document.getElementById(`cost_${userId}`);
            
            if (checkbox.checked) {
                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                const userName = label.querySelector('div div').textContent;
                selectedUsers.push(userName);
                
                // Show hours input
                if (hoursInput) {
                    hoursInput.style.display = 'block';
                }
                
                // Update individual cost for this user
                updateIndividualCost(userId);
            } else {
                // Hide hours input and clear value
                if (hoursInput) {
                    hoursInput.style.display = 'none';
                    const hoursField = document.getElementById(`hours_${userId}`);
                    if (hoursField) {
                        hoursField.value = '';
                        updateIndividualCost(userId);
                    }
                }
                
                // Hide cost display
                if (costDiv) {
                    costDiv.style.display = 'none';
                }
            }
        });

        if (selectedUsers.length === 0) {
            selectedMembersDiv.textContent = 'None selected';
        } else if (selectedUsers.length <= 3) {
            selectedMembersDiv.textContent = selectedUsers.join(', ');
        } else {
            selectedMembersDiv.textContent = `${selectedUsers.slice(0, 3).join(', ')} +${selectedUsers.length - 3} more`;
        }
        
        updateTotalCost();
    }

    function updateTimeline() {
        const poDate = poDateInput.value;
        const dueDate = dueDateInput.value;

        if (!poDate && !dueDate) {
            timelineDiv.textContent = 'Not set';
        } else if (poDate && !dueDate) {
            timelineDiv.textContent = `PO Date: ${new Date(poDate).toLocaleDateString()}`;
        } else if (!poDate && dueDate) {
            timelineDiv.textContent = `Due ${new Date(dueDate).toLocaleDateString()}`;
        } else {
            const start = new Date(poDate);
            const end = new Date(dueDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            timelineDiv.textContent = `${start.toLocaleDateString()} - ${end.toLocaleDateString()} (${diffDays} days)`;
        }
    }

    // Event listeners
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedMembers);
    });

    // Add event listeners for estimated hours inputs
    estimatedHoursInputs.forEach(input => {
        input.addEventListener('input', function() {
            const userId = this.dataset.userId;
            updateIndividualCost(userId);
        });
    });

    poDateInput.addEventListener('change', updateTimeline);
    dueDateInput.addEventListener('change', updateTimeline);

    // Initial updates
    updateSelectedMembers();
    updateTimeline();
    
    // Calculate initial costs for already assigned users
    userCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            updateIndividualCost(checkbox.value);
        }
    });

    // Color picker synchronization
    const colorPicker = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    
    colorPicker.addEventListener('input', function() {
        colorText.value = this.value;
    });

    // Validate due date is after PO date
    dueDateInput.addEventListener('change', function() {
        const poDate = poDateInput.value;
        const dueDate = dueDateInput.value;
        
        if (poDate && dueDate && new Date(dueDate) < new Date(poDate)) {
            alert('Due date cannot be before PO date.');
            dueDateInput.value = '';
            updateTimeline();
        }
    });
});
</script>
@endpush
