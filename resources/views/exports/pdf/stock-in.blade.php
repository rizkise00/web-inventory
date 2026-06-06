<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
    .header { background: #16a34a; color: white; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
    .header p { margin: 4px 0 0; font-size: 10px; opacity: 0.85; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f0fdf4; }
    th { padding: 8px 10px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #86efac; color: #166534; letter-spacing: 0.5px; }
    td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { margin-top: 14px; font-size: 9px; color: #9ca3af; text-align: right; }
    .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 600; }
</style>
</head>
<body>
    <div class="header">
        <h1>Stock In Report</h1>
        <p>Generated on {{ now()->format('d M Y, H:i:s') }} &nbsp;|&nbsp; Total: {{ $data->count() }} record(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Price</th>
                <th>Notes</th>
                <th>User</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $row->item->name ?? '-' }}</strong></td>
                <td><span class="badge">{{ $row->quantity }}</span></td>
                <td>Rp {{ number_format($row->unit_price, 0, ',', '.') }}</td>
                <td><strong>Rp {{ number_format($row->total_price, 0, ',', '.') }}</strong></td>
                <td>{{ $row->notes ?? '-' }}</td>
                <td>{{ $row->user->name ?? '-' }}</td>
                <td>{{ $row->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; color:#9ca3af; padding:20px;">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Web Inventory &mdash; Stock In Export &mdash; {{ now()->format('d M Y') }}</div>
</body>
</html>
