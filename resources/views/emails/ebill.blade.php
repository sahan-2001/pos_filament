<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $order_number }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #b38b6d; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .company-name { 
            font-size: 24px; 
            font-weight: bold; 
            color: #2c3e50; 
            margin-bottom: 10px;
        }
        .invoice-details { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        .items-table th { 
            background: #2c3e50; 
            color: white; 
            padding: 10px; 
            text-align: left;
        }
        .items-table td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd;
        }
        .totals { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px;
        }
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px;
        }
        .grand-total { 
            font-size: 18px; 
            font-weight: bold; 
            color: #2c3e50; 
            border-top: 2px solid #b38b6d; 
            padding-top: 10px;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            color: #666; 
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">LUXURY STORE</div>
        <div>POS System Receipt</div>
    </div>

    <div class="invoice-details">
        <p><strong>Invoice #:</strong> {{ $order_number }}</p>
        <p><strong>Date:</strong> {{ $order_date }}</p>
        <p><strong>Customer:</strong> {{ $customer_name }}</p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>Rs.{{ number_format($item['unit_price'], 2) }}</td>
                <td>Rs.{{ number_format($item['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rs.{{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Discount:</span>
            <span>- Rs.{{ number_format($discount, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Grand Total:</span>
            <span>Rs.{{ number_format($total, 2) }}</span>
        </div>
    </div>

    <div>
        <p><strong>Payment Method:</strong> {{ ucfirst($payment_method) }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>LUXURY STORE<br>
        Contact: [Your Store Contact Information]</p>
    </div>
</body>
</html>