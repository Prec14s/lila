<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_number }} — {{ $business->business_name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Courier New', Courier, monospace; }
        body { background: #F8F1E9; margin: 0; padding: 20px 10px; color: #2B1B12; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        
        .receipt-card { 
            background: white; 
            width: 310px; 
            max-width: 100%; 
            padding: 16px; 
            border-radius: 14px; 
            box-shadow: 0 4px 20px rgba(75,46,30,0.12); 
            border: 1px solid #E7D4BC; 
        }
        .center { text-align: center; }
        .line { border-top: 1px dashed #A08C7D; margin: 8px 0; }
        table { width: 100%; font-size: 11px; border-collapse: collapse; table-layout: fixed; }
        td { padding: 3px 0; vertical-align: top; word-break: break-word; overflow-wrap: break-word; }
        td.label { width: 38%; text-align: left; }
        td.val { width: 62%; text-align: right; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        
        .actions { margin-top: 18px; display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; width: 310px; max-width: 100%; }
        .btn { padding: 12px 16px; border-radius: 12px; border: none; font-family: sans-serif; font-size: 13px; font-weight: bold; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; }
        .btn-pdf { background: #4B2E1E; color: white; flex: 1; min-width: 130px; box-shadow: 0 4px 10px rgba(75,46,30,0.25); }
        .btn-pdf:hover { background: #2B1B12; }
        .btn-print { background: #F5EDE3; color: #4B2E1E; border: 1px solid #E7D4BC; flex: 1; min-width: 110px; }
        .btn-print:hover { background: #E7D4BC; }
        
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .receipt-card { box-shadow: none; border: none; width: 100%; padding: 0; border-radius: 0; }
            .actions { display: none !important; }
        }
    </style>
</head>
<body>
    <div id="receiptWrapper" style="padding: 6px; background: white; border-radius: 14px;">
        <div id="receiptContent" class="receipt-card">
            <div class="center">
                <div style="font-size:22px; margin-bottom: 2px;">☕</div>
                <div class="bold" style="font-size:15px;">{{ $business->business_name }}</div>
                @if($business->address)
                    <div style="font-size:10px; color: #555; margin-top: 2px;">{{ $business->address }}</div>
                @endif
            </div>

            <div class="line"></div>

            <table>
                <tr><td class="label">No. Order</td><td class="val bold">{{ $order->order_number }}</td></tr>
                <tr><td class="label">Tanggal</td><td class="val">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td></tr>
                <tr><td class="label">Pelanggan</td><td class="val">{{ $order->customer_name }}</td></tr>
                <tr><td class="label">No. WA</td><td class="val">{{ $order->customer_phone }}</td></tr>
                <tr><td class="label">No. Meja</td><td class="val bold">{{ $order->table_number ?? '-' }}</td></tr>
            </table>

            <div class="line"></div>

            <table>
                @foreach($order->items as $item)
                    <tr>
                        <td style="width:65%;">{{ $item->menu_name }}<br><span style="font-size:10px; color:#666;">{{ $item->qty }} x Rp {{ number_format($item->price,0,',','.') }}</span></td>
                        <td style="width:35%; text-align:right;">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                @endforeach
            </table>

            <div class="line"></div>

            <table>
                <tr><td class="bold" style="width:40%;">TOTAL</td><td class="right bold" style="width:60%; font-size:13px;">Rp {{ number_format($order->total,0,',','.') }}</td></tr>
            </table>

            <div class="line"></div>

            <table>
                <tr>
                    <td class="label">Cara Bayar</td>
                    <td class="val">{{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }} — {{ $order->paymentMethodLabel() }}</td>
                </tr>
                <tr><td class="label">Status Bayar</td><td class="val bold">{{ $order->paymentStatusLabel() }}</td></tr>
                @if($order->verifier)
                    <tr><td class="label">Diverifikasi</td><td class="val">{{ $order->verifier->name }}</td></tr>
                @endif
            </table>

            <div class="center" style="margin-top:14px; font-size:10px; color: #555;">
                Terima kasih telah berkunjung ke<br><span class="bold" style="color: #2B1B12;">{{ $business->business_name }}</span> ☕
            </div>
        </div>
    </div>

    <div class="actions">
        <button onclick="downloadPDF()" class="btn btn-pdf">
            📥 Download PDF Struk
        </button>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('receiptWrapper');
            const opt = {
                margin:       [6, 6, 6, 6],
                filename:     'Struk-{{ $order->order_number }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
