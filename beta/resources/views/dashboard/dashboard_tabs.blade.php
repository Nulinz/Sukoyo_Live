<div class="headtabs d-flex justify-content-start align-items-center flex-wrap gap-2 mb-4">
    <a href="{{ route('dashboard.admin') }}">
        <button class="headbtn {{ Request::routeIs('dashboard.admin') ? 'active' : '' }}">Overview</button>
    </a>
    <a href="{{ route('dashboard.pos') }}">
        <button class="headbtn {{ Request::routeIs('dashboard.pos') ? 'active' : '' }}">POS Dashboard</button>
    </a>
    <a href="{{ route('dashboard.abc') }}">
        <button class="headbtn {{ Request::routeIs('dashboard.abc') ? 'active' : '' }}">ABC Dashboard</button>
    </a>
</div>