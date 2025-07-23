@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-eye"></i> Leave Request Details</h1>
        <div class="d-flex gap-2">
            @can('update', $leave)
                <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan
            @can('approve', $leave)
                <button type="button" class="btn btn-success" onclick="approveLeave({{ $leave->id }})">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectLeave({{ $leave->id }})">
                    <i class="fas fa-times"></i> Reject
                </button>
            @endcan
            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> 
                    {{ $leave->getTypeLabel() }}
                </h5>
                @php
                    $statusClasses = [
                        'pending' => 'bg-warning text-dark',
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger'
                    ];
                @endphp
                <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }} fs-6">
                    <i class="fas fa-{{ $leave->status === 'approved' ? 'check' : ($leave->status === 'rejected' ? 'times' : 'clock') }}"></i>
                    {{ $leave->getStatusLabel() }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Employee</h6>
                        <div class="d-flex align-items-center">
                            @if($leave->user->photo ?? false)
                                <img src="{{ Storage::url($leave->user->photo) }}" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $leave->user->name }}</h6>
                                <small class="text-muted">{{ $leave->user->position ?? 'Staff' }}</small>
                                @if($leave->user->department ?? false)
                                    <br><small class="text-info">{{ $leave->user->department->name }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Leave Type</h6>
                        @php
                            $typeClasses = [
                                'vacation' => 'bg-info',
                                'sick' => 'bg-warning text-dark',
                                'sick_with_certificate' => 'bg-danger',
                                'personal' => 'bg-secondary'
                            ];
                        @endphp
                        <span class="badge {{ $typeClasses[$leave->type] ?? 'bg-secondary' }} fs-6">
                            {{ $leave->getTypeLabel() }}
                        </span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Start Date</h6>
                        <div class="fs-5 text-success">
                            <i class="fas fa-calendar-day me-1"></i>
                            {{ $leave->start_date->format('F j, Y') }}
                        </div>
                        <small class="text-muted">{{ $leave->start_date->format('l') }}</small>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">End Date</h6>
                        <div class="fs-5 text-danger">
                            <i class="fas fa-calendar-day me-1"></i>
                            {{ $leave->end_date->format('F j, Y') }}
                        </div>
                        <small class="text-muted">{{ $leave->end_date->format('l') }}</small>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Duration</h6>
                        <div class="fs-4 text-primary fw-bold">
                            <i class="fas fa-calendar-check me-1"></i>
                            {{ $leave->days }} {{ $leave->days == 1 ? 'day' : 'days' }}
                        </div>
                        <small class="text-muted">Working days only</small>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Reason</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $leave->reason }}
                    </div>
                </div>

                @if($leave->medical_certificate)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Medical Certificate</h6>
                    <div class="border rounded p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-medical fa-2x text-success me-3"></i>
                                <div>
                                    <h6 class="mb-0">Medical Certificate</h6>
                                    <small class="text-muted">
                                        @php
                                            $fileExtension = pathinfo($leave->medical_certificate, PATHINFO_EXTENSION);
                                            $fileSize = Storage::disk('public')->exists($leave->medical_certificate) 
                                                ? Storage::disk('public')->size($leave->medical_certificate) 
                                                : 0;
                                            $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024, 1) . ' KB' : 'Unknown size';
                                        @endphp
                                        {{ strtoupper($fileExtension) }} file • {{ $fileSizeFormatted }}
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @can('downloadMedicalCertificate', $leave)
                                    <a href="{{ route('leaves.medical-certificate', $leave) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endcan
                                @can('downloadMedicalCertificate', $leave)
                                    <a href="{{ Storage::url($leave->medical_certificate) }}" target="_blank" class="btn btn-outline-success">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($leave->approved_by && $leave->approved_at)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Approval Information</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                @if($leave->approvedBy->photo ?? false)
                                    <img src="{{ Storage::url($leave->approvedBy->photo) }}" alt="Profile" class="rounded-circle me-3" width="32" height="32">
                                @else
                                    <i class="fas fa-user-circle fa-lg me-3 text-muted"></i>
                                @endif
                                <div>
                                    <strong>{{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }} by:</strong> {{ $leave->approvedBy->name }}<br>
                                    <small class="text-muted">{{ $leave->approved_at->format('F j, Y g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($leave->notes)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Manager Notes</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-sticky-note"></i>
                        {{ $leave->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Leave Timeline Card -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-timeline"></i> Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Request Submitted</h6>
                            <small class="text-muted">{{ $leave->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                    </div>
                    
                    @if($leave->approved_at)
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $leave->status === 'approved' ? 'bg-success' : 'bg-danger' }}"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }}</h6>
                            <small class="text-muted">{{ $leave->approved_at->format('M j, Y g:i A') }}</small>
                            <br><small class="text-muted">by {{ $leave->approvedBy->name }}</small>
                        </div>
                    </div>
                    @endif

                    @if($leave->status === 'approved' && $leave->start_date->isFuture())
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Leave Starts</h6>
                            <small class="text-muted">{{ $leave->start_date->format('M j, Y') }}</small>
                            <br><small class="text-info">{{ $leave->start_date->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($leave->isPending() && (auth()->id() === $leave->user_id || auth()->user()->isManagerOrAdmin()))
                        <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Request
                        </a>
                    @endif
                    
                    @if(auth()->user()->isManagerOrAdmin() && $leave->isPending())
                        <button type="button" class="btn btn-success" onclick="approveLeave({{ $leave->id }})">
                            <i class="fas fa-check"></i> Approve Request
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectLeave({{ $leave->id }})">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    @endif
                    
                    <a href="{{ route('leaves.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                    <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> All Requests
                    </a>
                </div>
            </div>
        </div>

        <!-- Request Details Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-muted"><i class="fas fa-info-circle"></i> Request Details</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div class="mb-2">
                        <strong>Submitted:</strong><br>
                        {{ $leave->created_at->format('M j, Y g:i A') }}
                        <br>{{ $leave->created_at->diffForHumans() }}
                    </div>
                    
                    @if($leave->updated_at != $leave->created_at)
                    <div class="mb-2">
                        <strong>Last Updated:</strong><br>
                        {{ $leave->updated_at->format('M j, Y g:i A') }}
                    </div>
                    @endif

                    <div class="mb-2">
                        <strong>Request ID:</strong> #{{ $leave->id }}
                    </div>

                    @if($leave->start_date->isFuture())
                        <div class="text-info">
                            <strong>Starts in:</strong> {{ $leave->start_date->diffForHumans() }}
                        </div>
                    @elseif($leave->start_date->isPast() && $leave->end_date->isFuture())
                        <div class="text-warning">
                            <strong>Currently on leave</strong><br>
                            Ends {{ $leave->end_date->diffForHumans() }}
                        </div>
                    @elseif($leave->end_date->isPast())
                        <div class="text-muted">
                            <strong>Completed</strong><br>
                            Ended {{ $leave->end_date->diffForHumans() }}
                        </div>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content h6 {
    margin-bottom: 2px;
}
</style>
@endpush

@push('scripts')
<script>
function approveLeave(leaveId) {
    if (confirm('Are you sure you want to approve this leave request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/leaves/${leaveId}/approve`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
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

function rejectLeave(leaveId) {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) { // User didn't cancel
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/leaves/${leaveId}/reject`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        if (reason.trim()) {
            const reasonField = document.createElement('input');
            reasonField.type = 'hidden';       
            reasonField.name = 'notes';
            reasonField.value = reason;
            form.appendChild(reasonField);
        }
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
