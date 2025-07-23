@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Leave Request</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('leaves.show', $leave) }}" class="btn btn-outline-info">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-edit"></i> 
                    Edit Leave Request - {{ $leave->getTypeLabel() }}
                </h5>
            </div>
            <div class="card-body">
                @if(!$leave->isPending())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> This leave request has already been {{ $leave->status }}. 
                        Changes may require additional approval.
                    </div>
                @endif

                <form action="{{ route('leaves.update', $leave) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select leave type</option>
                                <option value="vacation" {{ old('type', $leave->type) == 'vacation' ? 'selected' : '' }}>Vacation</option>
                                <option value="sick" {{ old('type', $leave->type) == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="sick_with_certificate" {{ old('type', $leave->type) == 'sick_with_certificate' ? 'selected' : '' }}>Sick Leave (with Certificate)</option>
                                <option value="personal" {{ old('type', $leave->type) == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <div class="form-control-plaintext d-flex align-items-center">
                                @if($leave->user->photo ?? false)
                                    <img src="{{ Storage::url($leave->user->photo) }}" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                @else
                                    <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                @endif
                                <div>
                                    <strong>{{ $leave->user->name }}</strong>
                                    <br><small class="text-muted">{{ $leave->user->position ?? 'Staff' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" 
                                  placeholder="Please provide a detailed reason for your leave request..." required>{{ old('reason', $leave->reason) }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="medical-certificate-field" style="display: none;">
                        <label for="medical_certificate" class="form-label">Medical Certificate</label>
                        @if($leave->medical_certificate)
                            <div class="mb-2">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-medical"></i> Current certificate: 
                                    <a href="{{ Storage::url($leave->medical_certificate) }}" target="_blank" class="alert-link">
                                        View Current Document
                                    </a>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="medical_certificate" id="medical_certificate" 
                               class="form-control @error('medical_certificate') is-invalid @enderror" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">
                            @if($leave->medical_certificate)
                                Upload a new certificate to replace the current one (PDF, JPG, JPEG, PNG - max 2MB)
                            @else
                                Upload a medical certificate (PDF, JPG, JPEG, PNG - max 2MB)
                            @endif
                        </div>
                        @error('medical_certificate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-2"><i class="fas fa-calculator"></i> Leave Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="text-muted">Duration:</span>
                                    <div id="leave-duration" class="fw-bold">{{ $leave->start_date->format('M j') }} - {{ $leave->end_date->format('M j, Y') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">Working Days:</span>
                                    <div id="working-days" class="fw-bold text-primary">{{ $leave->days }} {{ $leave->days == 1 ? 'day' : 'days' }}</div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Only weekdays (Monday-Friday) are counted as working days.
                                </small>
                            </div>
                        </div>
                    </div>

                    @if(!$leave->isPending())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Status:</strong> This request is currently <strong>{{ $leave->getStatusLabel() }}</strong>.
                            @if($leave->approved_by)
                                It was {{ $leave->status }} by {{ $leave->approvedBy->name }} on {{ $leave->approved_at->format('M j, Y') }}.
                            @endif
                            Editing this request may change its status back to pending.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This request is currently pending approval. 
                            Your changes will be saved and the request will remain pending.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('leaves.show', $leave) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Request
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
    const typeSelect = document.getElementById('type');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const medicalCertificateField = document.getElementById('medical-certificate-field');
    const leaveDurationDiv = document.getElementById('leave-duration');
    const workingDaysDiv = document.getElementById('working-days');

    function toggleMedicalCertificate() {
        const type = typeSelect.value;
        if (type === 'sick_with_certificate') {
            medicalCertificateField.style.display = 'block';
        } else {
            medicalCertificateField.style.display = 'none';
        }
    }

    function calculateDays() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end < start) {
                leaveDurationDiv.textContent = 'Invalid date range';
                workingDaysDiv.textContent = '0 days';
                return;
            }

            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            // Calculate working days (excluding weekends)
            let workingDays = 0;
            let currentDate = new Date(start);
            
            while (currentDate <= end) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not Sunday (0) or Saturday (6)
                    workingDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            if (diffDays === 1) {
                leaveDurationDiv.textContent = start.toLocaleDateString();
            } else {
                leaveDurationDiv.textContent = `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`;
            }
            
            workingDaysDiv.textContent = `${workingDays} working ${workingDays === 1 ? 'day' : 'days'}`;
        }
    }

    // Event listeners
    typeSelect.addEventListener('change', toggleMedicalCertificate);
    startDateInput.addEventListener('change', function() {
        // Update minimum end date
        endDateInput.min = startDateInput.value;
        calculateDays();
    });
    endDateInput.addEventListener('change', function() {
        if (endDateInput.value < startDateInput.value) {
            alert('End date cannot be before start date.');
            endDateInput.value = startDateInput.value;
        }
        calculateDays();
    });

    // Initial setup
    toggleMedicalCertificate();
    calculateDays();
});
</script>
@endpush
