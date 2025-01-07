<!-- resources/views/receipts/print.blade.php -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน #{{ $order->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-info {
            margin-bottom: 20px;
        }
        
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
        }
        
        .totals {
            width: 300px;
            margin-left: auto;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 10px;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Company Header -->
        <div class="header">
            <h1>POS_SYSTEM</h1>
            <div class="company-info">
                123 ถนนตัวอย่าง, แขวงตัวอย่าง, เขตตัวอย่าง, กรุงเทพฯ 10XXX<br>
                โทร: 02-XXX-XXXX, อีเมล: example@company.com<br>
                เลขประจำตัวผู้เสียภาษี: XXX-XXX-XXXX
            </div>
            <h2 class="receipt-title">ใบเสร็จรับเงิน</h2>
        </div>

        <!-- Receipt Info -->
        <div class="info-grid">
            <div>
                <strong>ออกโดย:</strong>
                {{ $order->user->name ?? 'ลูกค้าทั่วไป' }}<br>
                <strong>ที่อยู่จัดส่ง:</strong>
                {{ $order->delivery_address ?? '-' }}
            </div>
            <div>
                <strong>เลขที่: </strong> {{ $order->id }}<br>
                <strong>วันที่: </strong> {{ $thaiDate }}<br>
                <strong>วิธีการชำระเงิน: </strong> 
                {{ $order->payment_method === 'cash' ? 'เงินสด' : 'บัตรเครดิต' }}
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">ลำดับ</th>
                    <th>รายการ</th>
                    <th style="width: 100px; text-align: right;">ราคา/หน่วย</th>
                    <th style="width: 80px; text-align: right;">จำนวน</th>
                    <th style="width: 80px; text-align: right;">ส่วนลด</th>
                    <th style="width: 120px; text-align: right;">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->product->name }}</td>
                    <td style="text-align: right;">{{ number_format($detail->price, 2) }}</td>
                    <td style="text-align: right;">{{ $detail->quantity }}</td>
                    <td style="text-align: right;">{{ $detail->discount }}%</td>
                    <td style="text-align: right;">{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>รวมเป็นเงิน:</span>
                <span>{{ number_format($subtotal, 2) }} บาท</span>
            </div>
            <div class="total-row">
                <span>ภาษีมูลค่าเพิ่ม 7%:</span>
                <span>{{ number_format($tax, 2) }} บาท</span>
            </div>
            <div class="total-row" style="font-weight: bold; font-size: 16px;">
                <span>จำนวนเงินทั้งสิ้น:</span>
                <span>{{ number_format($total, 2) }} บาท</span>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    ผู้รับเงิน
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    ผู้จ่ายเงิน
                </div>
            </div>
        </div>

        <!-- Print Button - Only shows on screen -->
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                พิมพ์ใบเสร็จ
            </button>
        </div>
    </div>
</body>
</html>