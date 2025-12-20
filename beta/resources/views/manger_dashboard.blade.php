@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/dashboard_main.css') }}">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4 class="m-0">Store Dashboard</h4>
    </div>

<div class="container-fluid px-0">
    {{-- Store Information Header --}}
    <!-- <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="mb-0">
                    <i class="fas fa-store me-2"></i>
                    {{ $storeName }} - Manager Dashboard
                    <small class="text-muted">({{ $manager->empname }})</small>
                </h5>
            </div>
        </div>
    </div> -->

    <div class="row d-flex flex-wrap" id="main_card">
        {{-- Today Sales Card --}}
        <div class="col-sm-6 col-md-4 mb-3 cards">
            <div class="cardsdiv">
                <div class="cardsmain">
                    <div>
                        <h6>Today Sales</h6>
                        <h5>₹ {{ number_format($todaySales, 2) }}</h5>
                    </div>
                    <img src="{{ asset('assets/images/icon_1.png') }}" height="50px" alt="">
                </div>
                <div class="cardssub d-block">
                    <h5>
                        @if($salesTrend == 'up')
                            <span style="color: var(--green);">
                                <i class="fas fa-arrow-trend-up pe-1"></i> {{ $salesPercentageChange }}%
                            </span> Up from yesterday
                        @else
                            <span style="color: var(--red);">
                                <i class="fas fa-arrow-trend-down pe-1"></i> {{ $salesPercentageChange }}%
                            </span> Down from yesterday
                        @endif
                    </h5>
                </div>
            </div>
        </div>

        {{-- Today Purchase Card --}}
        <div class="col-sm-6 col-md-4 mb-3 cards">
            <div class="cardsdiv">
                <div class="cardsmain">
                    <div>
                        <h6>Today Purchase</h6>
                        <h5>₹ {{ number_format($todayPurchase, 2) }}</h5>
                    </div>
                    <img src="{{ asset('assets/images/icon_2.png') }}" height="50px" alt="">
                </div>
                <div class="cardssub d-block">
                    <h5>
                        @if($purchaseTrend == 'up')
                            <span style="color: var(--green);">
                                <i class="fas fa-arrow-trend-up pe-1"></i> {{ $purchasePercentageChange }}%
                            </span> Up from past week average
                        @else
                            <span style="color: var(--red);">
                                <i class="fas fa-arrow-trend-down pe-1"></i> {{ $purchasePercentageChange }}%
                            </span> Down from past week average
                        @endif
                    </h5>
                </div>
            </div>
        </div>

        {{-- Today Bills Card --}}
        <div class="col-sm-6 col-md-4 mb-3 cards">
            <div class="cardsdiv">
                <div class="cardsmain">
                    <div>
                        <h6>Today Bills</h6>
                        <h5>{{ number_format($todayBills) }}</h5>
                    </div>
                    <img src="{{ asset('assets/images/icon_3.png') }}" height="50px" alt="">
                </div>
                <div class="cardssub d-block">
                    <h5>
                        @if($billsTrend == 'up')
                            <span style="color: var(--green);">
                                <i class="fas fa-arrow-trend-up pe-1"></i> {{ $billsPercentageChange }}%
                            </span> Up from yesterday
                        @else
                            <span style="color: var(--red);">
                                <i class="fas fa-arrow-trend-down pe-1"></i> {{ $billsPercentageChange }}%
                            </span> Down from yesterday
                        @endif
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Dynamic POS Sales Section --}}
    <div class="body-head mb-3">
        <h4 class="m-0">Today Sales by POS</h4>
    </div>
    <div class="container-fluid px-0">
        <div class="row d-flex flex-wrap" id="main_card">
            @forelse($posData as $pos)
                <div class="col-sm-6 col-md-4 mb-3 cards">
                    <div class="cardsdiv">
                        <div class="cardshead">
                            <div>
                                <h6>POS {{ $pos['pos_number'] }}</h6>
                                <h5>{{ $pos['employee_name'] }} - {{ $pos['employee_code'] }}</h5>
                            </div>
                            <div>
                                <h6 class="text-end">Total</h6>
                                <h5 class="text-end">₹ {{ number_format($pos['total_sales'], 0) }}</h5>
                            </div>
                        </div>
                        <div class="cardssub">
                            <div>
                                <h6 class="text-start">Cash</h6>
                                <h5 class="text-start">₹ {{ number_format($pos['cash_sales'], 0) }}</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-center">Online</h6>
                                <h5 class="text-center">₹ {{ number_format($pos['online_sales'], 0) }}</h5>
                            </div>
                            <div class="brdr"></div>
                            <div>
                                <h6 class="text-end">Loyalty Points</h6>
                                <h5 class="text-end">₹ {{ number_format($pos['loyalty_points'], 0) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No employees assigned to this store or no sales data available for today.
                    </div>
                </div>
            @endforelse
        </div>
    </div>



</div>

@endsection