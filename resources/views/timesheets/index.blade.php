@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-clock"></i> My Timesheets</h1>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex">
                <input type="month" name="month" value="{{ $month }}" class="form-control me-2">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </form>
            <a href="{{ route('timesheets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Timesheet
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h4>{{ number_format($monthlyHours, 1) }} hours</h4>
                <p class="mb-0">Total for {{ date('F Y', strtotime($month)) }}</p>
            </div>
        </div>
    </div>
</div>

@if($timesheets->count() > 0)
    @foreach($timesheets as $date => $dayTimesheets)
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                {{ date('l, F j, Y', strtotime($date)) }}
                <span class="badge bg-primary">
                    {{ $dayTimesheets->sum(function($ts) { return $ts->getWorkedHours(); }) }} hours
                </span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Time</th>
                            <th>Hours</th>
                            <th>Description</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dayTimesheets as $timesheet)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $timesheet->project->name }}</span>
                            </td>
                            <td>
                                {{ $timesheet->start_time->format('H:i') }} - 
                                {{ $timesheet->end_time->format('H:i') }}
                                @if($timesheet->break_minutes > 0)
                                    <br><small class="text-muted">Break: {{ $timesheet->break_minutes }}min</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $timesheet->getWorkedTime() }}</strong>
                            </td>
                            <td>
                                {{ Str::limit($timesheet->description, 80) }}
                                @if($timesheet->attachment)
                                    <i class="fas fa-paperclip text-primary" title="Has attachment"></i>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('timesheets.show', $timesheet) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('timesheets.edit', $timesheet) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete({{ $timesheet->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
            <h4>No timesheets found</h4>
            <p class="text-muted">You haven't logged any time for {{ date('F Y', strtotime($month)) }} yet.</p>
            <a href="{{ route('timesheets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Your First Timesheet
            </a>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
function confirmDelete(timesheetId) {
    if (confirm('Are you sure you want to delete this timesheet entry?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/timesheets/' + timesheetId;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush