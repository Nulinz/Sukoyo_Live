@foreach($expenses as $index => $expense)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
    <td>{{ $expense->expense_no }}</td>
    <td>{{ $expense->vendor->vendorname ?? '-' }}</td>
    <td>{{ $expense->payment_type ?? '-' }}</td>
    <td>₹ {{ number_format($expense->amount, 2) }}</td>
</tr>
@endforeach
