<li class="mt-3 mb-2">
    <a href="{{ route('dashboard.bill') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('dashboard.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse0" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_dashboard.png') }}" height="20px" alt=""> <span>Dashboard</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('class.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_class.png') }}" height="20px" alt=""> <span>Class</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse1">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('class.bookingslist.data') }}"
                    class="d-inline-flex text-decoration-none rounded">Bookings</a>
            </li>
          
        </ul>
    </div>
</li>

<li class="mb-2">
    <a href="{{ route('enquiry.lists') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('enquiry.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse14" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_enquiry.png') }}" height="20px" alt=""> <span>Enquiry</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>

<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('sales.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_sales.png') }}" height="20px" alt=""> <span>Sales</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse7">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('sales.invoice.list') }}" class="d-inline-flex text-decoration-none rounded mt-3">Sales
                    Invoice</a>
            </li>
        </ul>
    </div>
</li>


<li class="mb-2">
    <a href="{{ route('pos.pos-bill') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('pos.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_pos.png') }}" height="20px" alt=""> <span>POS Billing</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
