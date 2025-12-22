@include('layouts.header')

<div class="main">
    @include('layouts.sidebar_pos')

    <div class="body-main">
        @include('layouts.navbar_pos')

        @yield('content')

    </div>
</div>

@include('layouts.footer')