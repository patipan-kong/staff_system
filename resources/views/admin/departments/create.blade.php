@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus"></i> Create Department</h1>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Departments
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-building"></i> Department Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Department Manager</label>
                        <select class="form-select @error('manager_id') is-invalid @enderror" 
                                id="manager_id" name="manager_id">
                            <option value="">Select Manager (Optional)</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} - {{ $manager->position ?? 'Manager' }}
                                </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Only managers and administrators are listed</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Brief description of the department's responsibilities...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
