<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                background-color: white !important;
                padding: 0 !important;
            }
            
            .btn-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 20px;
            color: #333;
        }
        
        .receipt {
            max-width: 350px;
            margin: 0 auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4a6fdc;
        }
        
        .receipt-logo {
            max-width: 100px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        
        .receipt-title {
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 8px;
            color: #4a6fdc;
        }
        
        .receipt-info {
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .receipt-details {
            margin-bottom: 25px;
        }
        
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .receipt-items {
            margin-bottom: 25px;
        }
        
        .receipt-item {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e9ecef;
        }
        
        .receipt-item:last-child {
            border-bottom: none;
        }
        
        .receipt-item-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
        }
        
        .receipt-item-details {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }
        
        .receipt-summary {
            margin-bottom: 25px;
        }
        
        .receipt-footer {
            text-align: center;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #4a6fdc;
            color: #6c757d;
        }
        
        .btn-print {
            display: block;
            width: 220px;
            margin: 25px auto;
            padding: 10px;
            background-color: #4a6fdc;
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background-color: #3a5fc9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .receipt-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px dashed #e9ecef;
            padding-top: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            @if($profile && $profile->logo)
                <img src="{{ asset($profile->logo) }}" alt="Logo" class="receipt-logo">
            @endif
            <div class="receipt-title">{{ $profile->profile_name ?? 'Company Name' }}</div>
            <div class="receipt-info">{{ $profile->address ?? 'Address' }}</div>
            <div class="receipt-info">Phone: {{ $profile->phone_number ?? 'Phone Number' }}</div>
            <div class="receipt-info">{{ $profile->email ?? 'Email' }}</div>
        </div>
        
        <div class="receipt-details">
            <div class="receipt-row">
                <span>Order #:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="receipt-row">
                <span>Date:</span>
                <span>{{ $order->order_date->format('d M Y H:i') }}</span>
            </div>
            <div class="receipt-row">
                <span>Cashier:</span>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <div class="receipt-row">
                <span>Customer:</span>
                <span>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</span>
            </div>
            <div class="receipt-row">
                <span>Payment:</span>
                <span>{{ ucfirst($order->type_payment) }}</span>
            </div>
        </div>
        
        <div class="receipt-items">
            @foreach($order->items as $item)
                <div class="receipt-item">
                    <div class="receipt-item-name">{{ $item->product->name }}</div>
                    <div class="receipt-item-details">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->order_price, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="receipt-summary">
            <div class="receipt-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($order->total_cost_before, 0, ',', '.') }}</span>
            </div>
            @if($order->percent_discount > 0)
                <div class="receipt-row">
                    <span>Discount ({{ $order->percent_discount }}%):</span>
                    <span>-Rp {{ number_format($order->amount_discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="receipt-row receipt-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total_cost, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-row">
                <span>Payment:</span>
                <span>Rp {{ number_format($order->input_payment, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-row">
                <span>Change:</span>
                <span>Rp {{ number_format($order->return_payment, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="receipt-footer">
            <div>Thank you for your purchase!</div>
        </div>
    </div>
    
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print me-2"></i> Print Receipt
    </button>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>