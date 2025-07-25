@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-alt"></i> Leave Requests</h1>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Request Leave
        </a>
    </div>
</div>

@if($leaves->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($leave->user->photo)
                                        <img src="{{ asset(Storage::url($leave->user->photo)) }}" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                    @else
                                        <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $leave->user->name }}</div>
                                        <small class="text-muted">{{ $leave->user->position ?? 'Staff' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeClasses = [
                                        'vacation' => 'bg-info',
                                        'sick' => 'bg-warning text-dark',
                                        'sick_with_certificate' => 'bg-danger',
                                        'personal' => 'bg-secondary'
                                    ];
                                @endphp
                                <span class="badge {{ $typeClasses[$leave->type] ?? 'bg-secondary' }}">
                                    {{ $leave->getTypeLabel() }}
                                </span>
                                @if($leave->medical_certificate)
                                    <br><small class="text-success"><i class="fas fa-file-medical"></i> Certificate</small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $leave->start_date->format('M j, Y') }}</strong>
                                    @if($leave->start_date->format('Y-m-d') !== $leave->end_date->format('Y-m-d'))
                                        <br><span class="text-muted">to</span><br>
                                        <strong>{{ $leave->end_date->format('M j, Y') }}</strong>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ $leave->days }}</span>
                                <small class="text-muted d-block">{{ $leave->days == 1 ? 'day' : 'days' }}</small>
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$leave->status] ?? 'bg-secondary' }}">
                                    <i class="fas fa-{{ $leave->status === 'approved' ? 'check' : ($leave->status === 'rejected' ? 'times' : 'clock') }}"></i>
                                    {{ $leave->getStatusLabel() }}
                                </span>
                                @if($leave->approved_by && $leave->approved_at)
                                    <br><small class="text-muted">
                                        by {{ $leave->approvedBy->name }}<br>
                                        {{ $leave->approved_at->format('M j, Y') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $leave->reason }}">
                                    {{ Str::limit($leave->reason, 50) }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @can('view', $leave)
                                        <a href="{{ route('leaves.show', $leave) }}" class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update', $leave)
                                        <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('approve', $leave)
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="approveLeave({{ $leave->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="rejectLeave({{ $leave->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $leaves->links() }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-4">
        @php
            $pendingCount = $leaves->where('status', 'pending')->count();
            $approvedCount = $leaves->where('status', 'approved')->count();
            $rejectedCount = $leaves->where('status', 'rejected')->count();
        @endphp
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3>{{ $pendingCount }}</h3>
                    <p class="mb-0">Pending Requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $approvedCount }}</h3>
                    <p class="mb-0">Approved Requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $rejectedCount }}</h3>
                    <p class="mb-0">Rejected Requests</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <h4>No leave requests found</h4>
            <p class="text-muted">
                @if(auth()->user()->isManagerOrAdmin())
                    No leave requests have been submitted yet.
                @else
                    You haven't submitted any leave requests yet.
                @endif
            </p>
            <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Request Your First Leave
            </a>
        </div>
    </div>
@endif
@endsection

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
