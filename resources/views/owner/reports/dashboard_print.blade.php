<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $monthsName = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $periodParts = [];
        if ($day) {
            $periodParts[] = 'Tanggal ' . $day;
        }
        if ($month) {
            $periodParts[] = $monthsName[$month] ?? $month;
        }
        if ($year) {
            $periodParts[] = $year;
        }

        $periodLabel = !empty($periodParts) ? implode(' ', $periodParts) : 'Semua Periode';
    @endphp
    <title>Laporan Penjualan ({{ $periodLabel }}) — {{ $business->business_name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f4f4f5; margin: 0; padding: 24px 12px; color: #18181b; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        
        .report-card { 
            background: white; 
            width: 820px; 
            max-width: 100%; 
            padding: 32px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08); 
            border: 1px solid #e4e4e7; 
        }
        .header { text-align: center; border-bottom: 2px solid #27272a; padding-bottom: 16px; margin-bottom: 24px; position: relative; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; color: #27272a; text-transform: uppercase; letter-spacing: 0.5px; }
        .header h2 { margin: 6px 0 0 0; font-size: 13px; font-weight: 600; color: #52525b; }
        .header p { margin: 4px 0 0 0; font-size: 11px; color: #71717a; }
        .badge-period { display: inline-block; margin-top: 8px; padding: 4px 12px; background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 20px; font-size: 12px; font-weight: 700; color: #27272a; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .stat-box { background: #fafafa; border: 1px solid #e4e4e7; padding: 12px; border-radius: 10px; text-align: center; }
        .stat-box .title { font-size: 10px; color: #71717a; font-weight: 700; text-transform: uppercase; }
        .stat-box .val { font-size: 14px; font-weight: 800; color: #18181b; margin-top: 4px; }
        .stat-box.highlight { background: #fef3c7; border-color: #fde68a; }
        .stat-box.highlight .val { color: #92400e; }

        table { width: 100%; font-size: 12px; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #27272a; color: white; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        td { padding: 9px 12px; border-bottom: 1px solid #e4e4e7; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .signature-section { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        .signature-box { width: 200px; }
        .signature-space { height: 50px; }

        .actions { margin-top: 20px; display: flex; gap: 12px; justify-content: center; width: 820px; max-width: 100%; }
        .btn { padding: 12px 20px; border-radius: 12px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; }
        .btn-pdf { background: #27272a; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .btn-pdf:hover { background: #09090b; }
        .btn-print { background: #d97706; color: white; box-shadow: 0 4px 10px rgba(217,119,6,0.25); }
        .btn-print:hover { background: #b45309; }

        @media print {
            body { background: white; padding: 0; margin: 0; }
            .report-card { box-shadow: none; border: none; width: 100%; padding: 0; border-radius: 0; }
            .actions { display: none !important; }
            th { background: #18181b !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge-success { background: #dcfce7 !important; color: #166534 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge-warning { background: #fef3c7 !important; color: #92400e !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge-danger { background: #fee2e2 !important; color: #991b1b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .stat-box.highlight { background: #fef3c7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div id="reportWrapper" style="padding: 4px; background: white; border-radius: 16px;">
        <div id="reportContent" class="report-card">
            <div class="header">
                <div style="font-size:26px; margin-bottom: 4px;">☕</div>
                <h1>{{ $business->business_name }}</h1>
                @if($business->address)
                    <p>{{ $business->address }}</p>
                @endif
                <h2>LAPORAN PENJUALAN & OPERASIONAL WARKOP</h2>
                <div class="badge-period">📌 Periode: {{ $periodLabel }}</div>
                <p style="margin-top: 8px;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            {{-- Ringkasan Statistik --}}
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="title">Total Transaksi</div>
                    <div class="val">{{ $stats['total_orders'] }} Order</div>
                </div>
                <div class="stat-box">
                    <div class="title">Pesanan Disetujui</div>
                    <div class="val" style="color: #166534;">{{ $stats['approved_orders'] }}</div>
                </div>
                <div class="stat-box">
                    <div class="title">Omzet Tunai (Cash)</div>
                    <div class="val">Rp {{ number_format($stats['cash_revenue'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-box highlight">
                    <div class="title">Total Omzet Disetujui</div>
                    <div class="val">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Tabel Pesanan --}}
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 18%;">No. Order</th>
                        <th style="width: 18%;">Waktu Order</th>
                        <th style="width: 18%;">Pelanggan</th>
                        <th style="width: 15%;">Cara Bayar</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 13%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td class="font-bold">{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                {{ $order->customer_name }}
                                <br><small style="color: #71717a;">Meja: {{ $order->table_number ?? '-' }}</small>
                            </td>
                            <td>{{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }}</td>
                            <td>
                                @if($order->payment_status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($order->payment_status === 'waiting_verification')
                                    <span class="badge badge-warning">Menunggu</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-right font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 24px; color: #71717a;">
                                Tidak ada data pesanan pada periode filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($orders->count() > 0)
                    <tfoot>
                        <tr style="background: #f4f4f5; font-weight: bold;">
                            <td colspan="6" class="text-right" style="padding: 12px; font-size: 13px;">TOTAL OMZET DISETUJUI:</td>
                            <td class="text-right" style="padding: 12px; font-size: 14px; color: #92400e;">
                                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            {{-- Signature Section --}}
            <div class="signature-section">
                <div class="signature-box">
                    <p style="font-size: 11px; color: #71717a; margin-bottom: 4px;">Dibuat oleh,</p>
                    <p style="font-size: 12px; font-weight: bold;">Staf Kasir / Operasional</p>
                    <div class="signature-space"></div>
                    <p style="font-size: 12px;">( .................................... )</p>
                </div>
                <div class="signature-box">
                    <p style="font-size: 11px; color: #71717a; margin-bottom: 4px;">Mengetahui & Menyetujui,</p>
                    <p style="font-size: 12px; font-weight: bold;">Pemilik Warkop (Owner)</p>
                    <div class="signature-space"></div>
                    <p style="font-size: 12px;">( .................................... )</p>
                </div>
            </div>
        </div>
    </div>

    <div class="actions">
        <button onclick="downloadPDF()" class="btn btn-pdf">
            📥 Download PDF Laporan
        </button>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Cetak / Print Sekarang
        </button>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('reportWrapper');
            const opt = {
                margin:       [8, 8, 8, 8],
                filename:     'Laporan-Warkop-{{ \Illuminate\Support\Str::slug($periodLabel) }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
