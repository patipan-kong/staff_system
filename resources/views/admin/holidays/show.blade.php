@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-alt"></i> Holiday Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ $holiday->name }}</h5>
                @if($holiday->is_recurring)
                    <span class="badge bg-info">
                        <i class="fas fa-redo"></i> Recurring Holiday
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="fas fa-calendar"></i> One-time Holiday
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Holiday Name:</th>
                                <td><strong>{{ $holiday->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>
                                    <strong>{{ $holiday->date->format('F d, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $holiday->date->format('l') }}</small>
                                    @if($holiday->date->isToday())
                                        <br><span class="badge bg-success">Today</span>
                                    @elseif($holiday->date->isFuture())
                                        <br><span class="badge bg-warning">{{ $holiday->date->diffForHumans() }}</span>
                                    @else
                                        <br><span class="badge bg-secondary">{{ $holiday->date->diffForHumans() }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    @if($holiday->is_recurring)
                                        <i class="fas fa-redo text-info"></i> Recurring Annual Holiday
                                        <br><small class="text-muted">This holiday occurs every year on {{ $holiday->date->format('F d') }}</small>
                                    @else
                                        <i class="fas fa-calendar text-secondary"></i> One-time Holiday
                                        <br><small class="text-muted">This is a specific date holiday</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $holiday->created_at->format('M d, Y \a\t g:i A') }}</td>
                            </tr>
                            @if($holiday->updated_at != $holiday->created_at)
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $holiday->updated_at->format('M d, Y \a\t g:i A') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-calendar-day"></i> Holiday Information</h6>
                            </div>
                            <div class="card-body">
                                @if($holiday->description)
                                    <h6>Description:</h6>
                                    <p class="text-muted">{{ $holiday->description }}</p>
                                @else
                                    <p class="text-muted font-italic">No description provided for this holiday.</p>
                                @endif
                                
                                <hr>
                                
                                <div class="text-center">
                                    <div class="d-inline-block p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <h3 class="mb-0">{{ $holiday->date->format('d') }}</h3>
                                        <small>{{ $holiday->date->format('M Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($holiday->date->isFuture())
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Upcoming Holiday:</strong> This holiday is {{ $holiday->date->diffForHumans() }}.
                        Staff should be notified about this upcoming holiday.
                    </div>
                @elseif($holiday->date->isToday())
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-calendar-check"></i>
                        <strong>Today's Holiday:</strong> {{ $holiday->name }} is today!
                    </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this holiday?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Delete Holiday
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Holiday
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
