@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-building"></i> Department Management</h1>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Department
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> All Departments</h5>
            </div>
            <div class="card-body">
                @if($departments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Department Name</th>
                                    <th>Manager</th>
                                    <th>Staff Count</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                    <tr>
                                        <td>
                                            <strong>{{ $department->name }}</strong>
                                        </td>
                                        <td>
                                            @if($department->manager)
                                                <div class="d-flex align-items-center">
                                                    @if($department->manager->photo)
                                                        <img src="{{ asset(Storage::url($department->manager->photo)) }}" 
                                                             alt="Manager" class="rounded-circle me-2" width="30" height="30">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 30px; height: 30px;">
                                                            <i class="fas fa-user text-white fa-xs"></i>
                                                        </div>
                                                    @endif
                                                    <span>{{ $department->manager->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">No manager assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $department->users->count() }} staff</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($department->description, 50) ?: 'No description' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.departments.show', $department) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.departments.edit', $department) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.departments.destroy', $department) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this department?')">>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete" {{ $department->users->count() > 0 ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Departments Found</h5>
                        <p class="text-muted">Create your first department to get started.</p>
                        <a href="{{ route('admin.departments.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Department
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
