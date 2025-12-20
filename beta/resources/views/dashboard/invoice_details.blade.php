@extends('layouts.app_pos')

@section('content')
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4 class="m-0">Invoice Details</h4>
    </div>

    <!-- <div class="mb-3">
        <strong>Invoice No:</strong> INV{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}<br>
        <strong>Customer:</strong> {{ $invoice->customer->name ?? 'Walk-in' }}<br>
        <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y H:i') }}<br>
        <strong>Total Amount:</strong> ₹ {{ number_format($invoice->grand_total, 2) }}
    </div> -->

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Discount (%)</th>
                    <th>Tax (%)</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>₹ {{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->discount }}</td>
                        <td>{{ $item->tax }}</td>
                        <td>₹ {{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('dashboard.bill') }}" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
@endsection
