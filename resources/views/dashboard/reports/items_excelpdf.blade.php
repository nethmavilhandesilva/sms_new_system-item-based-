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

    <h2>භාණ්ඩ ලැයිස්තුව (Items List)</h2>

    <table>
        <thead>
            <tr>
                <th>ක අංකය</th>
                <th>වර්ගය</th>
                <th>මල්ලක අගය</th>
                <th>මල්ලක කුලිය</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td style="text-transform: uppercase;">{{ $item->no }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ number_format($item->pack_cost, 2) }}</td>
                    <td>{{ number_format($item->pack_due, 2) }}</td>
                </tr>
            @endforeach
            @if($items->isEmpty())
                <tr>
                    <td colspan="4" style="text-align:center;">භාණ්ඩ නොමැත</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
