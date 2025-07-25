@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-plus"></i> Create New User</h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-cog"></i> User Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nickname" class="form-label">Nickname</label>
                            <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                                   id="nickname" name="nickname" value="{{ old('nickname') }}">
                            <small class="form-text text-muted">Optional preferred name or nickname</small>
                            @error('nickname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="vacation_quota" class="form-label">Annual Vacation Days</label>
                            <input type="number" min="0" max="365" class="form-control @error('vacation_quota') is-invalid @enderror" 
                                   id="vacation_quota" name="vacation_quota" value="{{ old('vacation_quota', 20) }}">
                            <small class="form-text text-muted">Number of vacation days per year</small>
                            @error('vacation_quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="0" {{ old('role') == 0 ? 'selected' : '' }}>Staff</option>
                                <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Senior Staff</option>
                                <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Manager</option>
                                <option value="3" {{ old('role') == 3 ? 'selected' : '' }}>Senior Manager</option>
                                <option value="99" {{ old('role') == 99 ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                   id="position" name="position" value="{{ old('position') }}" required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('salary') is-invalid @enderror" 
                                   id="salary" name="salary" value="{{ old('salary') }}">
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                   id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required>
                            @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/*">
                        <small class="form-text text-muted">Maximum file size: 2MB. Accepted formats: JPG, PNG, GIF</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create User
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
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswordMatch() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Passwords do not match');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswordMatch);
    passwordConfirmation.addEventListener('input', validatePasswordMatch);
});
</script>
@endpush
