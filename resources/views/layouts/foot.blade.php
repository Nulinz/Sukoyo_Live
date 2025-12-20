<!-- Icons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/boxicons.js') }}'"></script>

<!-- jQuery (CDN + fallback) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    if (typeof jQuery === 'undefined') {
        document.write('<script src="{{ asset('assets/libs/jquery.min.js') }}"><\/script>');
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/jquery-3.6.4.min.js') }}'"></script>

<!-- Bootstrap JS (CDN + fallback) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/bootstrap.bundle.min.js') }}'"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/jquery.dataTables.min.js') }}'"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/dataTables.bootstrap4.min.js') }}'"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/select2.min.js') }}'"></script>

<!-- Lazy Loading -->
<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"
        onerror="this.onerror=null;this.src='{{ asset('assets/libs/lozad.min.js') }}'"></script>

<!-- Project Script -->
<script src="{{ asset('assets/js/script.js') }}"></script>

<!-- Tooltip -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>

</body>
</html>