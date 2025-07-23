@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-alt"></i> Holiday Management</h1>
        <a href="{{ route('admin.holidays.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Holiday
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Company Holidays</h5>
            </div>
            <div class="card-body">
                @if($holidays->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Holiday Name</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $holiday)
                                    <tr class="{{ $holiday->date->isPast() ? 'text-muted' : '' }}">
                                        <td>
                                            <strong>{{ $holiday->name }}</strong>
                                            @if($holiday->date->isToday())
                                                <span class="badge bg-success ms-2">Today</span>
                                            @elseif($holiday->date->isFuture() && $holiday->date->diffInDays() <= 7)
                                                <span class="badge bg-warning ms-2">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $holiday->date->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $holiday->date->format('l') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($holiday->is_recurring)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-redo"></i> Recurring
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-calendar"></i> One-time
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($holiday->description, 60) ?: 'No description' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.holidays.show', $holiday) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.holidays.edit', $holiday) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.holidays.destroy', $holiday) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this holiday?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Holidays Found</h5>
                        <p class="text-muted">Add company holidays to help staff plan their time.</p>
                        <a href="{{ route('admin.holidays.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Holiday
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($holidays->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Holiday Statistics</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalHolidays = $holidays->count();
                        $recurringHolidays = $holidays->where('is_recurring', true)->count();
                        $upcomingHolidays = $holidays->where('date', '>=', now())->count();
                        $pastHolidays = $holidays->where('date', '<', now())->count();
                    @endphp
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $totalHolidays }}</h4>
                            <small class="text-muted">Total Holidays</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $upcomingHolidays }}</h4>
                            <small class="text-muted">Upcoming</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $recurringHolidays }}</h4>
                            <small class="text-muted">Recurring</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-muted">{{ $pastHolidays }}</h4>
                            <small class="text-muted">Past</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar-check"></i> Next Holidays</h6>
                </div>
                <div class="card-body">
                    @php
                        $nextHolidays = $holidays->where('date', '>=', now())->take(5);
                    @endphp
                    @if($nextHolidays->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($nextHolidays as $holiday)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <strong>{{ $holiday->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $holiday->date->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $holiday->date->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No upcoming holidays</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
