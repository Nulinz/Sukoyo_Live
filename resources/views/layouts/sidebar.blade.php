<!-- Web View Sidebar -->
<aside>
    <div class="flex-shrink-0 sidebar">
        <div class="nav col-md-12">
            <a href="./index.php" class="mx-auto">
                <img src="{{ asset('assets/images/logo.png') }}" alt="" height="40px" class="mx-auto lightLogo">
            </a>
        </div>
        <ul class="main-ul list-unstyled ps-0" style="margin-top: 12px">
            @include('layouts.menu')
        </ul>
    </div>
</aside>

<!-- Responsive Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        @include('layouts.user')
        <button type="button" class="btn-close bg-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="flex-shrink-0 sidebar">
            <ul class="list-unstyled mt-2 ps-0">
                @include('layouts.menu')
            </ul>
        </div>
    </div>
</div>