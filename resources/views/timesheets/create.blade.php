@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle"></i> Add New Timesheet</h1>
        <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Timesheets
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clock"></i> New Timesheet Entry</h5>
            </div>
            <div class="card-body">
                @if($errors->has('time_overlap'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle"></i> Time Overlap Error!</strong>
                        <br>{{ $errors->first('time_overlap') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('timesheets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select a project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                   value="{{ old('start_time') }}" step="60" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                   value="{{ old('end_time') }}" step="60" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="break_minutes" class="form-label">Break (minutes)</label>
                            <input type="number" name="break_minutes" id="break_minutes" class="form-control @error('break_minutes') is-invalid @enderror" 
                                   value="{{ old('break_minutes', 0) }}" min="0" placeholder="0">
                            @error('break_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Describe what you worked on..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" 
                               accept="image/*">
                        <div class="form-text">Optional: Upload an image file (max 2MB)</div>
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-2"><i class="fas fa-calculator"></i> Time Calculator</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="text-muted">Total Hours:</span>
                                    <span id="calculated-hours" class="fw-bold">--:--</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">Working Hours:</span>
                                    <span id="working-hours" class="fw-bold">--:--</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('timesheets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Timesheet
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
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const breakMinutesInput = document.getElementById('break_minutes');
    const calculatedHours = document.getElementById('calculated-hours');
    const workingHours = document.getElementById('working-hours');

    function calculateHours() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const breakMinutes = parseInt(breakMinutesInput.value) || 0;

        if (startTime && endTime) {
            const start = new Date(`2000-01-01T${startTime}:00`);
            const end = new Date(`2000-01-01T${endTime}:00`);
            
            if (end > start) {
                const totalMinutes = (end - start) / (1000 * 60);
                const workingMinutes = totalMinutes - breakMinutes;
                
                const totalHours = Math.floor(totalMinutes / 60);
                const totalMins = totalMinutes % 60;
                calculatedHours.textContent = `${totalHours.toString().padStart(2, '0')}:${totalMins.toString().padStart(2, '0')}`;
                
                if (workingMinutes >= 0) {
                    const workHours = Math.floor(workingMinutes / 60);
                    const workMins = workingMinutes % 60;
                    workingHours.textContent = `${workHours.toString().padStart(2, '0')}:${workMins.toString().padStart(2, '0')}`;
                } else {
                    workingHours.textContent = '00:00';
                }
            } else {
                calculatedHours.textContent = '--:--';
                workingHours.textContent = '--:--';
            }
        } else {
            calculatedHours.textContent = '--:--';
            workingHours.textContent = '--:--';
        }
    }

    startTimeInput.addEventListener('change', calculateHours);
    endTimeInput.addEventListener('change', calculateHours);
    breakMinutesInput.addEventListener('input', calculateHours);
});
</script>
@endpush
