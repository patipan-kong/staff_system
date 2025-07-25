@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-building"></i> Department Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Department Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Name:</th>
                        <td>{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <th>Manager:</th>
                        <td>
                            @if($department->manager)
                                <div class="d-flex align-items-center">
                                    @if($department->manager->photo)
                                        <img src="{{ asset(Storage::url($department->manager->photo)) }}" 
                                             alt="Manager" class="rounded-circle me-2" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $department->manager->name }}</strong><br>
                                        <small class="text-muted">{{ $department->manager->position }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">No manager assigned</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Staff Count:</th>
                        <td>
                            <span class="badge bg-info fs-6">{{ $department->users->count() }} staff members</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $department->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
                
                @if($department->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $department->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Department Staff</h5>
            </div>
            <div class="card-body">
                @if($department->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Role</th>
                                    <th>Hire Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($user->photo)
                                                    <img src="{{ asset(Storage::url($user->photo)) }}" 
                                                         alt="Employee" class="rounded-circle me-2" width="30" height="30">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white fa-xs"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $user->name }}</strong><br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->position ?? 'Staff' }}</td>
                                        <td>
                                            @php
                                                $roleLabels = [
                                                    0 => ['label' => 'Staff', 'class' => 'bg-secondary'],
                                                    1 => ['label' => 'Senior Staff', 'class' => 'bg-info'],
                                                    2 => ['label' => 'Manager', 'class' => 'bg-warning text-dark'],
                                                    3 => ['label' => 'Senior Manager', 'class' => 'bg-success'],
                                                    99 => ['label' => 'Administrator', 'class' => 'bg-danger']
                                                ];
                                                $roleInfo = $roleLabels[$user->role] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                            @endphp
                                            <span class="badge {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                                        </td>
                                        <td>{{ $user->hire_date ? $user->hire_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Staff Assigned</h5>
                        <p class="text-muted">This department has no staff members assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
