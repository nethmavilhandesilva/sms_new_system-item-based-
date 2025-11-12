<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'notosanssinhala'; font-size: 12pt; }
        h2 { text-align: center; color: #003366; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; vertical-align: middle; }
        th { background-color: #e6f0ff; color: #003366; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

<h2>පාරිභෝගික ලැයිස්තුව (Customer List)</h2>

<table>
    <thead>
        <tr>
            <th>කෙටි නම</th>
            <th>සම්පූර්ණ නම</th>
            <th>ID_NO</th>
            <th>ලිපිනය</th>
            <th>දුරකථන අංකය</th>
            <th>ණය සීමාව (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{ strtoupper($customer->short_name) }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->ID_NO }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->telephone_no }}</td>
                <td>{{ number_format($customer->credit_limit,2) }}</td>
            </tr>
        @endforeach
        @if($customers->isEmpty())
            <tr>
                <td colspan="6" style="text-align:center;">පාරිභෝගිකයන් නොමැත</td>
            </tr>
        @endif
    </tbody>
</table>

</body>
</html>
