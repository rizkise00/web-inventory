<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
    .header { background: #7c3aed; color: white; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
    .header p { margin: 4px 0 0; font-size: 10px; opacity: 0.85; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f5f3ff; }
    th { padding: 8px 10px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #c4b5fd; color: #5b21b6; letter-spacing: 0.5px; }
    td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { margin-top: 14px; font-size: 9px; color: #9ca3af; text-align: right; }
</style>
</head>
<body>
    <div class="header">
        <h1>Category Report</h1>
        <p>Generated on {{ now()->format('d M Y, H:i:s') }} &nbsp;|&nbsp; Total: {{ $data->count() }} record(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $row->name }}</strong></td>
                <td>{{ $row->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center; color:#9ca3af; padding:20px;">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Web Inventory &mdash; Categories Export &mdash; {{ now()->format('d M Y') }}</div>
</body>
</html>
