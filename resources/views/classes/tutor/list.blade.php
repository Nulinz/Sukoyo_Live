@extends('layouts.app')

@section('content')
<div class="body-div p-3">
  <div class="body-head d-flex justify-content-between">
    <h4>Tutor List</h4>
    <a href="{{ route('class.tutoradd') }}">
      <button class="listbtn"><i class="fas fa-plus"></i> Add Tutor</button>
    </a>
  </div>
  @if(session('status'))
      <div class="alert alert-info">{{ session('status') }}</div>
  @endif

  <div class="container-fluid mt-3 listtable">
     <div class="filter-container">
                <div class="filter-container-start">
                    <select class="headerDropdown form-select filter-option">
                        <option value="All" selected>All</option>
                    </select>
                    <input type="text" class="form-control filterInput" placeholder=" Search">
                </div>
            </div>
    <div class="table-wrapper">
      <table class="example table table-bordered">
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Expertise</th>
            <th>Internal/External</th><th>Contact</th>
            <th>Status</th><th>Action</th>
          </tr>
        </thead>
<tbody>
  @forelse($tutors as $i => $t)
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $t->name }}</td>
      <td>{{ $t->expertise }}</td>
      <td>{{ $t->internal_external }}</td>
      <td>{{ $t->contact }}</td>
      <td>
        @if($t->status === 'Active')
          <span class="text-success">Active</span>
        @else
          <span class="text-danger">Inactive</span>
        @endif
      </td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <form action="{{ route('class.tutortoggle', $t->id) }}" method="POST">
            @csrf
            <button type="submit" class="border-0 bg-transparent" data-bs-toggle="tooltip"
                    title="{{ $t->status === 'Active' ? 'Set Inactive' : 'Set Active' }}">
              @if($t->status === 'Active')
                <i class="fas fa-circle-check text-success"></i>
              @else
                <i class="fas fa-circle-xmark text-danger"></i>
              @endif
            </button>
          </form>
          <a href="{{ route('class.tutorprofile', $t->id) }}" data-bs-toggle="tooltip" title="Profile">
            <i class="fas fa-arrow-up-right-from-square"></i>
          </a>
          <a href="{{ route('class.tutoredit', $t->id) }}" data-bs-toggle="tooltip" title="Edit">
            <i class="fas fa-pen-to-square"></i>
          </a>
        </div>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="7" class="text-center">No data available in table</td>
    </tr>
  @endforelse
</tbody>

      </table>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
  var table = $('.example').DataTable({
    paging: true, searching: true, ordering: true,
    bDestroy: true, info: false, responsive: true,
    pageLength: 10,
    dom: '<"top"f>rt<"bottom"lp><"clear">'
  });

  $('.example thead th').each(function (i) {
    if (!['Action',''].includes($(this).text().trim())) {
      $('.headerDropdown').append(`<option value="${i}">${$(this).text()}</option>`);
    }
  });

  $('.filterInput').on('keyup', function() {
    var col = $('.headerDropdown').val();
    col !== 'All' ? table.column(col).search(this.value).draw() : table.search(this.value).draw();
  });

  $('.headerDropdown').on('change', function(){
    table.search('').columns().search('').draw();
    $('.filterInput').val('');
  });
});
</script>
@endpush
@endsection
