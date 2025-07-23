@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus"></i> Add Holiday</h1>
        <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Holidays
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Holiday Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.holidays.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="e.g., Christmas Day, New Year's Day" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Holiday Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_recurring" name="is_recurring" value="1" 
                                   {{ old('is_recurring') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">
                                <strong>Recurring Holiday</strong>
                                <br>
                                <small class="text-muted">Check this if the holiday occurs annually on the same date</small>
                            </label>
                        </div>
                        @error('is_recurring')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Brief description of the holiday or any special notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add Holiday
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
    // Set minimum date to today
    const dateInput = document.getElementById('date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;
    
    // Auto-suggest common holidays
    const nameInput = document.getElementById('name');
    const commonHolidays = [
        'New Year\'s Day',
        'Independence Day',
        'Christmas Day',
        'Thanksgiving',
        'Labor Day',
        'Memorial Day',
        'Veterans Day',
        'Good Friday',
        'Easter Monday'
    ];
    
    // You could implement autocomplete here if needed
});
</script>
@endpush
