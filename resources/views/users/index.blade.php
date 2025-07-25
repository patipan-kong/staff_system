@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users"></i> Team Members</h1>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        @endif
    </div>
</div>

@if($users->count() > 0)
    <div class="row">
        @foreach($users as $user)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    @if($user->photo ?? false)
                        <img src="{{ Storage::url($user->photo) }}" alt="Profile" class="rounded-circle mb-3" width="80" height="80">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                    @endif
                    
                    <h5 class="card-title">{{ $user->name }}</h5>
                    @if($user->nickname)
                        <p class="text-muted mb-1 small">"{{ $user->nickname }}"</p>
                    @endif
                    <p class="text-muted">{{ $user->position ?? 'Staff' }}</p>
                    
                    @if($user->department ?? false)
                        <span class="badge bg-info mb-2">{{ $user->department->name }}</span>
                    @endif

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
                    <div class="mb-3">
                        <span class="badge {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            @if($user->email)
                                <i class="fas fa-envelope"></i> {{ $user->email }}<br>
                            @endif
                            @if($user->phone ?? false)
                                <i class="fas fa-phone"></i> {{ $user->phone }}<br>
                            @endif
                            @if($user->hire_date ?? false)
                                <i class="fas fa-calendar"></i> Joined {{ $user->hire_date->format('M Y') }}
                            @endif
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Team Statistics -->
    <div class="row mt-4">
        @php
            $roleStats = $users->groupBy('role')->map->count();
            $departmentStats = $users->whereNotNull('department_id')->groupBy('department.name')->map->count();
        @endphp
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Team by Role</h6>
                </div>
                <div class="card-body">
                    @foreach($roleLabels as $roleId => $roleData)
                        @if($roleStats->has($roleId))
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $roleData['label'] }}</span>
                                <span class="badge {{ $roleData['class'] }}">{{ $roleStats[$roleId] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        @if($departmentStats->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-building"></i> Team by Department</h6>
                </div>
                <div class="card-body">
                    @foreach($departmentStats as $deptName => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $deptName }}</span>
                            <span class="badge bg-secondary">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h4>No team members found</h4>
            <p class="text-muted">The team directory is empty.</p>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add First Team Member
                </a>
            @endif
        </div>
    </div>
@endif
@endsection
