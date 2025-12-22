@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Employee Attendance</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                Add Attendance
            </button>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Break Out</th>
                        <th>Break In</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->employee->empname }} ({{ $attendance->employee->empcode }})</td>
                        <td>{{ $attendance->date }}</td>
                        <td>{{ $attendance->in_time }}</td>
                        <td>{{ $attendance->out_time }}</td>
                        <td>{{ $attendance->break_out }}</td>
                        <td>{{ $attendance->break_in }}</td>
                        <td>{{ ucfirst($attendance->status) }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                                Edit
                            </button>
                        </td>
                    </tr>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editAttendanceModal{{ $attendance->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Attendance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('attendance.partials.form', ['attendance' => $attendance])
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr><td colspan="8" class="text-center">No records found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Attendance Modal --}}
<div class="modal fade" id="addAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('attendance.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Do NOT pass $attendance here --}}
                    @include('attendance.partials.form', ['attendance' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var addModal = document.getElementById('addAttendanceModal');
    addModal.addEventListener('show.bs.modal', function () {
        this.querySelector('form').reset();
    });
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
@endsection
