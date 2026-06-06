<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
    .header { background: #0891b2; color: white; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
    .header p { margin: 4px 0 0; font-size: 10px; opacity: 0.85; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #ecfeff; }
    th { padding: 8px 10px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #a5f3fc; color: #164e63; letter-spacing: 0.5px; }
    td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { margin-top: 14px; font-size: 9px; color: #9ca3af; text-align: right; }
    .badge-manager  { display:inline-block; background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:600; }
    .badge-user     { display:inline-block; background:#f3e8ff; color:#6b21a8; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:600; }
    .badge-approved { display:inline-block; background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:600; }
    .badge-pending  { display:inline-block; background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:600; }
</style>
</head>
<body>
    <div class="header">
        <h1>Users Report</h1>
        <p>Generated on {{ now()->format('d M Y, H:i:s') }} &nbsp;|&nbsp; Total: {{ $data->count() }} record(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $row->name }}</strong></td>
                <td>{{ $row->email }}</td>
                <td>
                    <span class="{{ $row->role === 'manager' ? 'badge-manager' : 'badge-user' }}">
                        {{ ucfirst($row->role) }}
                    </span>
                </td>
                <td>
                    <span class="{{ $row->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $row->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </td>
                <td>{{ $row->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color:#9ca3af; padding:20px;">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Web Inventory &mdash; Users Export &mdash; {{ now()->format('d M Y') }}</div>
</body>
</html>
