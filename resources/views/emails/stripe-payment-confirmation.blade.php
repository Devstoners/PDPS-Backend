<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - PDPS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
        }
        .success-badge {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .payment-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #2c5aa0;
        }
        .detail-label {
            color: #666;
        }
        .detail-value {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .thank-you {
            text-align: center;
            font-size: 18px;
            color: #2c5aa0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Pathadumbara Pradeshiya Sabawa</div>
            <div class="subtitle">Official Tax Payment Confirmation</div>
        </div>

        <div class="success-badge">✅ Payment Successful</div>

        <div class="thank-you">
            Thank you for your payment!
        </div>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #2c5aa0;">Payment Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Payment ID:</span>
                <span class="detail-value">#{{ $stripePayment->payment_id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value">LKR {{ number_format($stripePayment->amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($stripePayment->created_at)->format('M d, Y \a\t g:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">Stripe Online Payment</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #28a745;">Completed</span>
            </div>
        </div>

        @if($taxPayee)
        <div class="payment-details">
            <h3 style="margin-top: 0; color: #2c5aa0;">Taxpayer Information</h3>
            
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $taxPayee->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">NIC:</span>
                <span class="detail-value">{{ $taxPayee->nic }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $taxPayee->email }}</span>
            </div>
            
            @if($taxPayee->tel)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $taxPayee->tel }}</span>
            </div>
            @endif
        </div>
        @endif

        <div class="contact-info">
            <h4 style="margin-top: 0; color: #2c5aa0;">Need Help?</h4>
            <p>If you have any questions about this payment, please contact us:</p>
            <p><strong>Email:</strong> pathadumbarapradeshiyasabawa@gmail.com</p>
            <p><strong>Phone:</strong> +94 XX XXX XXXX</p>
        </div>

        <div class="footer">
            <p>This is an automated payment confirmation from Pathadumbara Pradeshiya Sabawa.</p>
            <p>Please keep this email as your payment receipt.</p>
            <p style="font-size: 12px; color: #999;">
                © {{ date('Y') }} Pathadumbara Pradeshiya Sabawa. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
