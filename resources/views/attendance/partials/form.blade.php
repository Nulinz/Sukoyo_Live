@php
    $attendance = $attendance ?? null; // only set if exists (for edit), otherwise null
@endphp
@if(session('alert'))
    <script>
        alert("{{ session('alert') }}");
    </script>
@endif


<div class="mb-3">
    <label class="form-label">Employee</label>
<select class="form-select" name="employee_id" required>
    <option value="">Select Employee</option>
    @foreach($employees as $employee)
        <option value="{{ $employee->id }}"
            {{ old('employee_id', $attendance->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
            {{ $employee->empname }} ({{ $employee->empcode }})
            @if($employee->designation == 'manager') - MANAGER @endif
        </option>
    @endforeach
</select>
</div>


<div class="mb-3">
    <label class="form-label">Date</label>
    <input type="date" class="form-control" name="date" 
           value="{{ old('date', $attendance->date ?? date('Y-m-d')) }}" required>
</div>

<div class="row">
    <div class="col-md-6">
        <label class="form-label">In Time</label>
        <input type="time" class="form-control" name="in_time" 
               value="{{ old('in_time', $attendance->in_time ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Out Time</label>
        <input type="time" class="form-control" name="out_time" 
               value="{{ old('out_time', $attendance->out_time ?? '') }}">
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <label class="form-label">Break Out</label>
        <input type="time" class="form-control" name="break_out" 
               value="{{ old('break_out', $attendance->break_out ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Break In</label>
        <input type="time" class="form-control" name="break_in" 
               value="{{ old('break_in', $attendance->break_in ?? '') }}">
    </div>
</div>

<div class="mt-3">
    <label class="form-label">Status</label>
    <select class="form-select" name="status" required>
        <option value="">Select Status</option>
        <option value="present" {{ old('status', $attendance->status ?? '') == 'present' ? 'selected' : '' }}>Present</option>
        <option value="absent" {{ old('status', $attendance->status ?? '') == 'absent' ? 'selected' : '' }}>Absent</option>
    </select>
</div>
