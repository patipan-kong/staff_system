@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-eye"></i> Timesheet Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('timesheets.edit', $timesheet) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $timesheet->id }})">
                <i class="fas fa-trash"></i> Delete
            </button>
            <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day"></i> 
                    {{ $timesheet->date->format('l, F j, Y') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Project</h6>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary fs-6 me-2">{{ $timesheet->project->name }}</span>
                            @if($timesheet->project->description)
                                <small class="text-muted">{{ Str::limit($timesheet->project->description, 50) }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Employee</h6>
                        <div class="d-flex align-items-center">
                            @if($timesheet->user->photo)
                                <img src="{{ asset(Storage::url($timesheet->user->photo)) }}" alt="Profile" class="rounded-circle me-2" width="24" height="24">
                            @else
                                <i class="fas fa-user-circle me-2"></i>
                            @endif
                            <span>{{ $timesheet->user->name }}</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Start Time</h6>
                        <div class="fs-5 text-success">
                            <i class="fas fa-play-circle me-1"></i>
                            {{ $timesheet->start_time->format('H:i') }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">End Time</h6>
                        <div class="fs-5 text-danger">
                            <i class="fas fa-stop-circle me-1"></i>
                            {{ $timesheet->end_time->format('H:i') }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Break Time</h6>
                        <div class="fs-5 text-warning">
                            <i class="fas fa-coffee me-1"></i>
                            {{ $timesheet->break_minutes ?? 0 }} min
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Total Hours</h6>
                        <div class="fs-4 text-primary fw-bold">
                            <i class="fas fa-clock me-1"></i>
                            {{ $timesheet->getWorkedTime() }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Description</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $timesheet->description }}
                    </div>
                </div>

                @if($timesheet->attachment)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Attachment</h6>
                    <div class="border rounded p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-paperclip fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0">Attached File</h6>
                                    <small class="text-muted">Click to view attachment</small>
                                </div>
                            </div>
                            <a href="{{ asset(Storage::url($timesheet->attachment)) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> View
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Time Summary Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-calculator"></i> Time Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Duration:</span>
                    <strong>{{ sprintf('%02d:%02d', floor($timesheet->start_time->diffInMinutes($timesheet->end_time) / 60), $timesheet->start_time->diffInMinutes($timesheet->end_time) % 60) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Break Time:</span>
                    <span class="text-warning">{{ $timesheet->break_minutes ?? 0 }} min</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Working Hours:</span>
                    <span class="fw-bold text-success">{{ $timesheet->getWorkedTime() }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Decimal Hours:</span>
                    <span>{{ number_format($timesheet->getWorkedHours(), 2) }}h</span>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-cogs"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('timesheets.edit', $timesheet) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Entry
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $timesheet->id }})">
                        <i class="fas fa-trash"></i> Delete Entry
                    </button>
                    <hr>
                    <a href="{{ route('timesheets.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Entry
                    </a>
                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> View All Entries
                    </a>
                </div>
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-muted"><i class="fas fa-info-circle"></i> Entry Information</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div class="mb-1">
                        <strong>Created:</strong> {{ $timesheet->created_at->format('M j, Y g:i A') }}
                    </div>
                    @if($timesheet->updated_at != $timesheet->created_at)
                    <div>
                        <strong>Last Updated:</strong> {{ $timesheet->updated_at->format('M j, Y g:i A') }}
                    </div>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(timesheetId) {
    if (confirm('Are you sure you want to delete this timesheet entry? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/timesheets/' + timesheetId;
        
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
