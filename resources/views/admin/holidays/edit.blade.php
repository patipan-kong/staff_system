@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Holiday</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.holidays.show', $holiday) }}" class="btn btn-outline-info">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Holiday Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $holiday->name) }}" 
                               placeholder="e.g., Christmas Day, New Year's Day" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Holiday Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_recurring" name="is_recurring" value="1" 
                                   {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
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
                                  placeholder="Brief description of the holiday or any special notes...">{{ old('description', $holiday->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.holidays.show', $holiday) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Holiday
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
