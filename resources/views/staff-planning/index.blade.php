@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-alt"></i> Staff Planning</h1>
        <div class="d-flex gap-2">
            <!-- Week Navigation -->
            <a href="{{ route('staff-planning.index', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
               class="btn btn-outline-secondary">
                <i class="fas fa-chevron-left"></i> Previous Week
            </a>
            <a href="{{ route('staff-planning.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-home"></i> Current Week
            </a>
            <a href="{{ route('staff-planning.index', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
               class="btn btn-outline-secondary">
                Next Week <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Week of {{ $weekStart->format('F j, Y') }}</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-light">Assign projects by clicking on cells</span>
                <!-- Project Filter -->
                <select id="projectFilter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" data-color="{{ $project->color ?? '#007bff' }}">
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 staff-planning-table">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 200px; position: sticky; left: 0; background: #f8f9fa; z-index: 10;">
                            <i class="fas fa-users"></i> Staff Member
                        </th>
                        @foreach($weekDates as $date)
                            @php
                                $dateKey = $date->format('Y-m-d');
                                $isHoliday = $holidays->has($dateKey);
                                $holiday = $isHoliday ? $holidays->get($dateKey) : null;
                            @endphp
                            <th class="text-center position-relative {{ $isHoliday ? 'bg-danger bg-opacity-10' : '' }}" style="min-width: 150px;">
                                <div class="fw-bold {{ $isHoliday ? 'text-danger' : '' }}">{{ $date->format('D') }}</div>
                                <div class="text-muted small">{{ $date->format('M j') }}</div>
                                @if($isHoliday)
                                    <div class="position-absolute top-0 end-0 p-1">
                                        <i class="fas fa-calendar-times text-danger" 
                                           title="{{ $holiday->name }}" 
                                           data-bs-toggle="tooltip"></i>
                                    </div>
                                    <div class="text-danger small fw-bold mt-1" style="font-size: 10px;">
                                        {{ $holiday->name }}
                                    </div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr data-user-id="{{ $user->id }}">
                            <td class="align-middle" style="position: sticky; left: 0; background: white; z-index: 5;">
                                <div class="d-flex align-items-center">
                                    @if($user->photo)
                                        <img src="{{ asset(Storage::url($user->photo)) }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle me-2" 
                                             width="32" height="32">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px; font-size: 14px; color: white;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->department->name ?? 'No Dept' }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($weekDates as $date)
                                @php
                                    $dayKey = $user->id . '_' . $date->format('Y-m-d');
                                    $dateKey = $date->format('Y-m-d');
                                    $dayPlannings = $staffPlannings->get($dayKey, collect());
                                    $isHoliday = $holidays->has($dateKey);
                                    $holiday = $isHoliday ? $holidays->get($dateKey) : null;
                                    
                                    // Check if user has leave on this date
                                    $userLeaves = $leaves->get($user->id, collect());
                                    $hasLeave = false;
                                    $leaveType = '';
                                    foreach ($userLeaves as $leave) {
                                        $startDate = \Carbon\Carbon::parse($leave->start_date);
                                        $endDate = \Carbon\Carbon::parse($leave->end_date);
                                        if ($date->between($startDate, $endDate)) {
                                            $hasLeave = true;
                                            $leaveType = $leave->type;
                                            break;
                                        }
                                    }
                                @endphp
                                <td class="planning-cell p-1 position-relative {{ $isHoliday ? 'bg-danger bg-opacity-10' : '' }} {{ $hasLeave ? 'bg-warning bg-opacity-20' : '' }}" 
                                    data-user-id="{{ $user->id }}" 
                                    data-date="{{ $date->format('Y-m-d') }}"
                                    style="height: 80px; cursor: pointer; vertical-align: top;">
                                    
                                    <!-- Holiday or Leave Overlay -->
                                    @if($isHoliday || $hasLeave)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                             style="z-index: 1; pointer-events: none;">
                                            @if($isHoliday)
                                                <div class="text-danger text-center">
                                                    <i class="fas fa-calendar-times fa-lg"></i>
                                                    <div class="small fw-bold mt-1">Holiday</div>
                                                </div>
                                            @elseif($hasLeave)
                                                <div class="text-warning text-center">
                                                    <i class="fas fa-user-times fa-lg"></i>
                                                    <div class="small fw-bold mt-1">{{ ucfirst($leaveType) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="planning-content h-100" style="position: relative; z-index: 2;">
                                        @foreach($dayPlannings as $planning)
                                            <div class="planning-item mb-1" 
                                                 data-planning-id="{{ $planning->id }}"
                                                 style="background-color: {{ $planning->project->color ?? '#007bff' }}; 
                                                        color: white; 
                                                        padding: 2px 6px; 
                                                        border-radius: 4px; 
                                                        font-size: 11px;
                                                        cursor: pointer;
                                                        position: relative;
                                                        {{ ($isHoliday || $hasLeave) ? 'opacity: 0.7;' : '' }}">
                                                @if($planning->project->icon)
                                                    <i class="{{ $planning->project->icon }} me-1"></i>
                                                @endif
                                                {{ $planning->project->name }}
                                                <button type="button" 
                                                        class="btn-close btn-close-white delete-planning" 
                                                        style="font-size: 8px; position: absolute; top: 1px; right: 2px;"
                                                        data-planning-id="{{ $planning->id }}"
                                                        onclick="event.stopPropagation();">
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-2"><i class="fas fa-info-circle"></i> Legend</h6>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center legend-item">
                        <div class="me-2 rounded border" style="width: 20px; height: 20px; background-color: #dc3545; opacity: 0.1; border-left: 3px solid #dc3545 !important;"></div>
                        <span class="small">Company Holiday</span>
                    </div>
                    <div class="d-flex align-items-center legend-item">
                        <div class="me-2 rounded border" style="width: 20px; height: 20px; background-color: #ffc107; opacity: 0.2; border-left: 3px solid #ffc107 !important;"></div>
                        <span class="small">Staff Leave</span>
                    </div>
                    <div class="d-flex align-items-center legend-item">
                        <i class="fas fa-calendar-times text-danger me-2"></i>
                        <span class="small">Holiday Indicator</span>
                    </div>
                    <div class="d-flex align-items-center legend-item">
                        <i class="fas fa-user-times text-warning me-2"></i>
                        <span class="small">Leave Indicator</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="small text-muted">
                    <i class="fas fa-lightbulb"></i> 
                    Click on any cell to assign projects. Projects assigned on holidays/leaves will be dimmed.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Selection Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Select a project to assign to <strong id="selectedStaff"></strong> on <strong id="selectedDate"></strong>:</p>
                <div class="list-group" id="projectList">
                    @foreach($projects as $project)
                        <a href="#" class="list-group-item list-group-item-action project-option" 
                           data-project-id="{{ $project->id }}"
                           data-project-name="{{ $project->name }}"
                           data-project-color="{{ $project->color ?? '#007bff' }}"
                           data-project-icon="{{ $project->icon }}">
                            <div class="d-flex align-items-center">
                                <div class="rounded me-3" 
                                     style="width: 20px; height: 20px; background-color: {{ $project->color ?? '#007bff' }};">
                                </div>
                                @if($project->icon)
                                    <i class="{{ $project->icon }} me-2"></i>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $project->name }}</div>
                                    <div class="text-muted small">{{ $project->groupProject->name ?? 'No Group' }}</div>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-planning-table th,
.staff-planning-table td {
    border: 1px solid #dee2e6;
}

.planning-cell:hover {
    background-color: #f8f9fa;
}

.planning-item:hover {
    opacity: 0.9;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.planning-item .delete-planning {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.planning-item:hover .delete-planning {
    opacity: 1;
}

.project-option:hover {
    transform: translateX(5px);
    transition: transform 0.2s ease;
}

/* Make table horizontally scrollable while keeping first column sticky */
.table-responsive {
    overflow-x: auto;
}

/* Holiday and Leave styling */
.planning-cell.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.08) !important;
    border-left: 3px solid #dc3545;
}

.planning-cell.bg-warning.bg-opacity-20 {
    background-color: rgba(255, 193, 7, 0.15) !important;
    border-left: 3px solid #ffc107;
}

/* Header holiday styling */
th.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.05) !important;
    border-top: 3px solid #dc3545;
}

/* Holiday/Leave overlay animation */
.planning-cell .position-absolute {
    transition: all 0.3s ease;
}

.planning-cell:hover .position-absolute {
    opacity: 0.8;
}

/* Tooltip styling */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Legend styling */
.legend-item {
    transition: transform 0.2s ease;
}

.legend-item:hover {
    transform: scale(1.05);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    const projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
    let selectedCell = null;
    
    // Handle cell clicks to open project selection
    document.querySelectorAll('.planning-cell').forEach(cell => {
        cell.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-planning') || e.target.closest('.delete-planning')) {
                return; // Don't open modal when clicking delete button
            }
            
            // Check if this is a holiday or leave day
            const isHoliday = this.classList.contains('bg-danger');
            const isLeave = this.classList.contains('bg-warning');
            
            if (isHoliday || isLeave) {
                const dayType = isHoliday ? 'company holiday' : 'staff leave day';
                if (!confirm(`This is a ${dayType}. Are you sure you want to assign a project on this day?`)) {
                    return;
                }
            }
            
            selectedCell = this;
            const userId = this.dataset.userId;
            const date = this.dataset.date;
            const userName = this.closest('tr').querySelector('.fw-bold').textContent;
            
            document.getElementById('selectedStaff').textContent = userName;
            document.getElementById('selectedDate').textContent = new Date(date).toLocaleDateString();
            
            projectModal.show();
        });
    });
    
    // Handle project selection
    document.querySelectorAll('.project-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            const projectId = this.dataset.projectId;
            const projectName = this.dataset.projectName;
            const projectColor = this.dataset.projectColor;
            const projectIcon = this.dataset.projectIcon;
            const userId = selectedCell.dataset.userId;
            const date = selectedCell.dataset.date;
            
            // Send AJAX request to create planning
            fetch('{{ route("staff-planning.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    project_id: projectId,
                    planned_date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                // Add the planning item to the cell
                const planningContent = selectedCell.querySelector('.planning-content');
                const planningItem = document.createElement('div');
                planningItem.className = 'planning-item mb-1';
                planningItem.dataset.planningId = data.id;
                planningItem.style.backgroundColor = data.project_color;
                planningItem.style.color = 'white';
                planningItem.style.padding = '2px 6px';
                planningItem.style.borderRadius = '4px';
                planningItem.style.fontSize = '11px';
                planningItem.style.cursor = 'pointer';
                planningItem.style.position = 'relative';
                
                // Check if this is a holiday or leave day and dim the project
                const isHoliday = selectedCell.classList.contains('bg-danger');
                const isLeave = selectedCell.classList.contains('bg-warning');
                if (isHoliday || isLeave) {
                    planningItem.style.opacity = '0.7';
                }
                
                let iconHtml = '';
                if (data.project_icon) {
                    iconHtml = `<i class="${data.project_icon} me-1"></i>`;
                }
                
                planningItem.innerHTML = `
                    ${iconHtml}${data.project_name}
                    <button type="button" 
                            class="btn-close btn-close-white delete-planning" 
                            style="font-size: 8px; position: absolute; top: 1px; right: 2px;"
                            data-planning-id="${data.id}"
                            onclick="event.stopPropagation();">
                    </button>
                `;
                
                planningContent.appendChild(planningItem);
                
                // Add event listener for the new delete button
                planningItem.querySelector('.delete-planning').addEventListener('click', deletePlanning);
                
                projectModal.hide();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the planning.');
            });
        });
    });
    
    // Handle planning deletion
    function deletePlanning(e) {
        e.stopPropagation();
        
        const planningId = this.dataset.planningId;
        
        if (!confirm('Are you sure you want to remove this planning?')) {
            return;
        }
        
        fetch(`{{ route("staff-planning.destroy", ":id") }}`.replace(':id', planningId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the planning item from the DOM
                this.closest('.planning-item').remove();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the planning.');
        });
    }
    
    // Add event listeners to existing delete buttons
    document.querySelectorAll('.delete-planning').forEach(button => {
        button.addEventListener('click', deletePlanning);
    });
    
    // Project filter functionality
    const projectFilter = document.getElementById('projectFilter');
    projectFilter.addEventListener('change', function() {
        const selectedProjectId = this.value;
        const planningItems = document.querySelectorAll('.planning-item');
        
        planningItems.forEach(item => {
            if (selectedProjectId === '' || item.querySelector('i')) {
                item.style.display = 'block';
            } else {
                // This is a simple filter - in a real app you might want to store project ID as data attribute
                item.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
