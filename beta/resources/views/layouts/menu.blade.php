{{-- Dashboard - Different routes for admin and manager --}}
<li class="mt-3 mb-2">
    @if(Session::get('role') === 'admin')
        <a href="{{ route('dashboard.admin') }}">
    @else
        <a href="{{ route('dashboard.manager') }}">
    @endif
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

{{-- Class - Available for both admin and manager --}}
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
                <a href="{{ route('class.classlist') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Class</a>
            </li>
            <li>
                <a href="{{ route('class.bookingslist') }}"
                    class="d-inline-flex text-decoration-none rounded">Bookings</a>
            </li>
            <li>
                <a href="{{ route('class.tutorlist') }}" class="d-inline-flex text-decoration-none rounded">Tutor</a>
            </li>
        </ul>
    </div>
</li>

{{-- Store - Only for admin --}}
@if(Session::get('role') === 'admin')
<li class="mb-2">
    <a href="{{ route('store.list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('store.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_store.png') }}" height="20px" alt=""> <span>Store</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
@endif

{{-- Warehouse - Only for admin --}}
@if(Session::get('role') === 'admin')
<li class="mb-2">
    <a href="{{ route('warehouse.list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('warehouse.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_warehouse.png') }}" height="20px" alt=""> <span>Warehouse</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
@endif

{{-- Party - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('party.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_parties.png') }}" height="20px" alt=""> <span>Party</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse4">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('party.vendorlist') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Vendor</a>
            </li>
            <li>
                <a href="{{ route('party.customerlist') }}"
                    class="d-inline-flex text-decoration-none rounded">Customer</a>
            </li>
        </ul>
    </div>
</li>

{{-- Enquiry - Different for admin and manager --}}
@if(Session::get('role') === 'admin')
<li class="mb-2">
    <a href="{{ route('enquiry.list') }}">
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
@else
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
@endif

{{-- Inventory - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('inventory.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_inventory.png') }}" height="20px" alt=""> <span>Inventory</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse5">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('inventory.brandlist') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Brand</a>
            </li>
            <li>
                <a href="{{ route('inventory.categorylist') }}"
                    class="d-inline-flex text-decoration-none rounded">Category</a>
            </li>
            <li>
                <a href="{{ route('inventory.subcategorylist') }}"
                    class="d-inline-flex text-decoration-none rounded">Sub Category</a>
            </li>
            <li>
                <a href="{{ route('inventory.itemlist') }}" class="d-inline-flex text-decoration-none rounded">Item</a>
            </li>
        <!--    <li>-->
        <!--        <a href="{{ route('inventory.repackinglist') }}"-->
        <!--            class="d-inline-flex text-decoration-none rounded">Repacking</a>-->
        <!--    </li>-->
        <!--</ul>-->
        @if(Session::get('role') === 'admin')
            <li>
                <a href="{{ route('inventory.repackinglist') }}"
                    class="d-inline-flex text-decoration-none rounded">Repacking</a>
            </li>
                        <li>
                <a href="{{ route('inventory.transferlist') }}"
                    class="d-inline-flex text-decoration-none rounded">Transfer Items</a>
            </li>
        @endif
    </div>
</li>

{{-- Stock - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('stock.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_stock.png') }}" height="20px" alt=""> <span>Stock</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse6">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('stock.lowstock') }}" class="d-inline-flex text-decoration-none rounded mt-3">Low
                    Stock</a>
            </li>
            <li>
                <a href="{{ route('stock.overstock') }}"
                    class="d-inline-flex text-decoration-none rounded">Overstocked</a>
            </li>
            <li>
                <a href="{{ route('stock.zeromovement') }}" class="d-inline-flex text-decoration-none rounded">Zero
                    Movement</a>
            </li>
        </ul>
    </div>
</li>

{{-- Sales - Available for both admin and manager --}}
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
                <a href="{{ route('sales.list') }}" class="d-inline-flex text-decoration-none rounded mt-3">Sales
                    Invoice</a>
            </li>
                        <li>
                <a href="{{ route('sales.return_vouchers') }}" class="d-inline-flex text-decoration-none rounded mt-3">
                    Sales Return</a>
            </li>
        </ul>
    </div>
</li>

{{-- Purchase - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('purchase.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_purchase.png') }}" height="20px" alt=""> <span>Purchase</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse8">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('purchase.inv_list') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Purchase Invoice</a>
            </li>
            <li>
                <a href="{{ route('purchase.order_list') }}" class="d-inline-flex text-decoration-none rounded">Purchase
                    Order</a>
            </li>
        </ul>
    </div>
</li>

{{-- Accounts - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('accounts.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_account.png') }}" height="20px" alt=""> <span>Accounts</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse9">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('accounts.payment') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Payment</a>
            </li>
            <li>
                <a href="{{ route('accounts.expense') }}" class="d-inline-flex text-decoration-none rounded">Expense</a>
            </li>
            <li>
                <a href="{{ route('accounts.bank_account') }}" class="d-inline-flex text-decoration-none rounded">Bank
                    Account</a>
            </li>
            <li>
                <a href="{{ route('accounts.cash') }}" class="d-inline-flex text-decoration-none rounded">Cash In
                    Hand</a>
            </li>
        </ul>
    </div>
</li>

{{-- POS Billing - Available for both admin and manager --}}
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

{{-- Attendance - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('attd.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_attd.png') }}" height="20px" alt=""> <span>Attendance</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse11">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            @if(session('role') === 'manager')
            <li>
                <a href="{{ route('attendance.index') }}" class="d-inline-flex text-decoration-none rounded mt-3">Add Attendance</a>
            </li>
            @endif
            <li>
                <a href="{{ route('attd.daily') }}" class="d-inline-flex text-decoration-none rounded mt-3">Daily</a>
            </li>
            <li>
                <a href="{{ route('attendance.monthly') }}" class="d-inline-flex text-decoration-none rounded">Monthly</a>
            </li>
            <li>
                <a href="{{ route('attendance.individual') }}"
                    class="d-inline-flex text-decoration-none rounded">Individual</a>
            </li>
        </ul>
    </div>
</li>
{{-- Offers - Available for both admin and manager --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('offers.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse12" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_lp.png') }}" height="20px" alt=""> <span>Offers</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse12">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ route('offers.lplist') }}" class="d-inline-flex text-decoration-none rounded mt-3">Loyalty
                    Points</a>
            </li>
            <li>
                <a href="{{ route('offers.giftlist') }}" class="d-inline-flex text-decoration-none rounded">Gift
                    Cards</a>
            </li>
            <li>
                <a href="{{ route('offers.voucherlist') }}"
                    class="d-inline-flex text-decoration-none rounded">Vouchers</a>
            </li>
        </ul>
    </div>
</li>

{{-- Reports - Available for both admin and manager --}}
<li class="mb-2">
    <a href="{{ route('reports.list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('reports.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" data-bs-target="#collapse15" aria-expanded="false">
            <div class="btnname">
                <img src="{{ asset('assets/images/icon_notes.png') }}" height="20px" alt=""> <span>Reports</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>

{{-- Settings - Modified for role-based access --}}
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('settings.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapse13" aria-expanded="false">
        <div class="btnname">
            <img src="{{ asset('assets/images/icon_settings.png') }}" height="20px" alt=""> <span>Settings</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse13">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            {{-- Company - Only for admin --}}
            @if(Session::get('role') === 'admin')
            <li>
                <a href="{{ route('settings.companyprofile') }}"
                    class="d-inline-flex text-decoration-none rounded mt-3">Company</a>
            </li>
            @endif
            <li>
                <a href="{{ route('settings.emp_list') }}"
                    class="d-inline-flex text-decoration-none rounded {{ Session::get('role') === 'admin' ? '' : 'mt-3' }}">Employee</a>
            </li>
            <li>
                <a href="{{ route('settings.pos') }}" class="d-inline-flex text-decoration-none rounded">POS System</a>
            </li>
        </ul>
    </div>
</li>