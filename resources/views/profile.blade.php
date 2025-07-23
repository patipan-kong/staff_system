@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="fas fa-user-cog"></i> My Profile</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if(auth()->user()->photo)
                    <img src="{{ Storage::url(auth()->user()->photo) }}" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                @endif
                
                <h4>{{ auth()->user()->name }}</h4>
                <p class="text-muted">{{ auth()->user()->position ?? 'Staff' }}</p>
                
                @if(auth()->user()->department)
                    <span class="badge bg-info mb-3">{{ auth()->user()->department->name }}</span>
                @endif

                @php
                    $roleLabels = [
                        0 => ['label' => 'Staff', 'class' => 'bg-secondary'],
                        1 => ['label' => 'Senior Staff', 'class' => 'bg-info'],
                        2 => ['label' => 'Manager', 'class' => 'bg-warning text-dark'],
                        3 => ['label' => 'Senior Manager', 'class' => 'bg-success'],
                        99 => ['label' => 'Administrator', 'class' => 'bg-danger']
                    ];
                    $roleInfo = $roleLabels[auth()->user()->role] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                @endphp
                <div class="mb-3">
                    <span class="badge {{ $roleInfo['class'] }} fs-6">{{ $roleInfo['label'] }}</span>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-envelope text-muted me-2"></i>
                    {{ auth()->user()->email }}
                </div>
                @if(auth()->user()->phone)
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        {{ auth()->user()->phone }}
                    </div>
                @endif
                @if(auth()->user()->address)
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        {{ auth()->user()->address }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-briefcase"></i> Employment Details</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->hire_date)
                    <div class="mb-2">
                        <strong>Hire Date:</strong><br>
                        <i class="fas fa-calendar text-muted me-2"></i>
                        {{ auth()->user()->hire_date->format('F j, Y') }}
                        <small class="text-muted">({{ auth()->user()->hire_date->diffForHumans() }})</small>
                    </div>
                @endif
                <div class="mb-2">
                    <strong>Status:</strong><br>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ auth()->user()->projects->count() }}</h3>
                        <p class="mb-0">Active Projects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        @php
                            $thisMonthHours = auth()->user()->timesheets()
                                ->whereYear('date', now()->year)
                                ->whereMonth('date', now()->month)
                                ->get()
                                ->sum(function($ts) { return $ts->getWorkedHours(); });
                        @endphp
                        <h3>{{ number_format($thisMonthHours, 1) }}</h3>
                        <p class="mb-0">Hours This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ auth()->user()->leaves()->where('status', 'approved')->count() }}</h3>
                        <p class="mb-0">Approved Leaves</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Time Logs</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $recentTimesheets = auth()->user()->timesheets()->with('project')->latest()->take(5)->get();
                @endphp
                @if($recentTimesheets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th>Hours</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTimesheets as $timesheet)
                                <tr>
                                    <td>{{ $timesheet->date->format('M j, Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $timesheet->project->name }}</span>
                                    </td>
                                    <td><strong>{{ $timesheet->getWorkedTime() }}</strong></td>
                                    <td>{{ Str::limit($timesheet->description, 50) }}</td>
                                    <td>
                                        <a href="{{ route('timesheets.show', $timesheet) }}" class="btn btn-sm btn-outline-info">
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
                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No time logs yet</p>
                        <a href="{{ route('timesheets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Log Your First Entry
                        </a>
                    </div>
                @endif
            </div>
            @if($recentTimesheets->count() > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-primary">
                        View All Time Logs
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ auth()->user()->phone }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control">{{ auth()->user()->address }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                        <div class="form-text">Upload a new photo to replace your current one</div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password (required to change password)</label>
                        <input type="password" name="current_password" id="current_password" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
