# PayHere Payment Gateway Integration - PDPS

## Overview
This document describes the comprehensive PayHere payment gateway integration for all payment types in the Pradeshiya Sabha (Local Government) management system. The integration covers water bill payments, hall reservation payments, and tax payments.

## System Architecture

### Unified Payment System
The system uses a unified approach to handle all payment types through PayHere gateway:

- **UnifiedPayHereService** - Central service for all PayHere operations
- **UnifiedPaymentController** - Handles all payment processing
- **Payment Type Support** - Water bills, hall reservations, tax payments

### Payment Flow
1. **Payment Initiation** - Customer/User initiates payment
2. **Validation** - System validates payment data and customer info
3. **PayHere Integration** - Generate checkout data and redirect to PayHere
4. **Payment Processing** - PayHere processes the payment
5. **Callback Handling** - PayHere sends callback with payment status
6. **Status Update** - System updates payment and related records
7. **Notification** - Send confirmation to customer

## API Endpoints

### Unified Payment Routes
```
POST   /api/payments/online                    - Process any online payment
GET    /api/payments/{type}/{id}/receipt       - Get payment receipt
GET    /api/payments/{type}/{id}/receipt/download - Download receipt
GET    /api/payments/status/{orderId}          - Check payment status
POST   /api/payhere/callback                   - PayHere webhook callback
```

### Water Bill Payment Routes
```
POST   /api/water-bills/online-payment         - Process water bill payment
GET    /api/water-payments/{id}/receipt        - Get water payment receipt
```

### Hall Reservation Payment Routes
```
POST   /api/hall-reservations/{id}/payments/online - Process hall payment
POST   /api/hall-reservations/{id}/payments    - Add manual payment
```

### Tax Payment Routes
```
POST   /api/tax-payments/online/{assessmentId} - Process tax payment
GET    /api/tax-payments/{id}/receipt          - Get tax payment receipt
```

## Payment Types

### 1. Water Bill Payments
**Payment Type**: `water_bill`
**Order ID Format**: `WB_{payment_id}`

**Request Format**:
```json
{
    "water_bill_id": 123,
    "amount_paid": 1500.00,
    "customer_data": {
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "0771234567",
        "address": "123 Main Street",
        "city": "Colombo"
    }
}
```

**Response**:
```json
{
    "message": "Payment initiated",
    "payment_id": 456,
    "checkout_data": {
        "merchant_id": "your_merchant_id",
        "order_id": "WB_456",
        "amount": 1500.00,
        "currency": "LKR",
        "hash": "generated_hash",
        // ... other PayHere data
    },
    "checkout_url": "https://sandbox.payhere.lk/pay/checkout"
}
```

### 2. Hall Reservation Payments
**Payment Type**: `hall_reservation`
**Order ID Format**: `HR_{payment_id}`

**Request Format**:
```json
{
    "hall_reservation_id": 789,
    "amount": 5000.00,
    "customer_data": {
        "first_name": "Jane",
        "last_name": "Smith",
        "email": "jane@example.com",
        "phone": "0779876543",
        "address": "456 Oak Avenue",
        "city": "Kandy"
    }
}
```

### 3. Tax Payments
**Payment Type**: `tax_payment`
**Order ID Format**: `TX_{payment_id}`

**Request Format**:
```json
{
    "payment": 2500.00,
    "discount_amount": 0,
    "fine_amount": 0,
    "customer_data": {
        "first_name": "Robert",
        "last_name": "Johnson",
        "email": "robert@example.com",
        "phone": "0775555555",
        "address": "789 Pine Street",
        "city": "Galle"
    }
}
```

## PayHere Configuration

### Environment Variables
Add to your `.env` file:
```env
PAYHERE_MERCHANT_ID=your_merchant_id
PAYHERE_MERCHANT_SECRET=your_merchant_secret
PAYHERE_CHECKOUT_URL=https://sandbox.payhere.lk/pay/checkout
PAYHERE_RETURN_URL=https://yourdomain.com/api/payhere/return
PAYHERE_CANCEL_URL=https://yourdomain.com/api/payhere/cancel
PAYHERE_NOTIFY_URL=https://yourdomain.com/api/payhere/callback
```

### Configuration File
The system uses `config/payhere.php` for PayHere settings:

```php
return [
    'merchant_id' => env('PAYHERE_MERCHANT_ID'),
    'merchant_secret' => env('PAYHERE_MERCHANT_SECRET'),
    'checkout_url' => env('PAYHERE_CHECKOUT_URL', 'https://sandbox.payhere.lk/pay/checkout'),
    'return_url' => env('PAYHERE_RETURN_URL', env('APP_URL') . '/api/payhere/return'),
    'cancel_url' => env('PAYHERE_CANCEL_URL', env('APP_URL') . '/api/payhere/cancel'),
    'notify_url' => env('PAYHERE_NOTIFY_URL', env('APP_URL') . '/api/payhere/callback'),
    'currency' => 'LKR',
    'country' => 'Sri Lanka',
];
```

## Security Features

### Hash Generation
All PayHere requests include a secure hash for verification:

```php
$hashString = $merchantId . 
             $orderId . 
             $amount . 
             $currency . 
             strtoupper(hash('sha256', $merchantSecret));

$hash = strtoupper(hash('sha256', $hashString));
```

### Callback Verification
All PayHere callbacks are verified using signature validation:

```php
$expectedSignature = strtoupper(hash('sha256', 
    $merchantId . 
    $orderId . 
    $amount . 
    $currency . 
    $statusCode . 
    strtoupper(hash('sha256', $merchantSecret))
));
```

## Payment Status Handling

### Status Codes
- **2** - Payment successful
- **0** - Payment failed
- **-1** - Payment cancelled

### Database Updates
After successful payment:
1. Update payment record with transaction ID
2. Update related records (bill/reservation/assessment status)
3. Send confirmation notifications
4. Generate receipt

## Error Handling

### Common Error Scenarios
1. **Invalid Payment Data** - Validation errors
2. **Payment Already Processed** - Duplicate payment prevention
3. **PayHere Signature Mismatch** - Security validation failure
4. **Database Transaction Failures** - Rollback on errors

### Error Response Format
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Receipt Generation

### Receipt Data Structure
```json
{
    "receipt": {
        "payment_id": 123,
        "customer_name": "John Doe",
        "amount": 1500.00,
        "payment_date": "2025-01-27",
        "transaction_id": "PH123456789",
        "type": "Water Bill Payment"
    },
    "receipt_number": 123,
    "download_url": "https://yourdomain.com/api/payments/water_bill/123/receipt/download"
}
```

### Receipt HTML Template
The system generates HTML receipts with:
- Payment details
- Customer information
- Transaction information
- Official receipt formatting

## Testing

### Test Payment Flow
1. **Create Test Payment** - Use sandbox environment
2. **Process Payment** - Submit to PayHere sandbox
3. **Verify Callback** - Check webhook handling
4. **Validate Updates** - Confirm database changes
5. **Test Receipt** - Generate and download receipt

### Sandbox Configuration
For testing, use PayHere sandbox:
- **Checkout URL**: `https://sandbox.payhere.lk/pay/checkout`
- **Test Cards**: Use PayHere test card numbers
- **Webhook Testing**: Use ngrok or similar for local testing

## Production Deployment

### Environment Setup
1. **Configure Production PayHere** - Use live merchant credentials
2. **Set Up SSL** - Ensure HTTPS for webhook URLs
3. **Configure Notifications** - Set up email/SMS services
4. **Monitor Logs** - Track payment processing

### Security Considerations
1. **Webhook Security** - Verify all callbacks
2. **Data Encryption** - Protect sensitive payment data
3. **Access Control** - Limit payment processing access
4. **Audit Logging** - Track all payment activities

## Monitoring and Maintenance

### Daily Tasks
- Monitor payment processing logs
- Check failed payment notifications
- Verify webhook functionality

### Weekly Tasks
- Generate payment reports
- Review payment success rates
- Update payment configurations

### Monthly Tasks
- Analyze payment trends
- Review security logs
- Update PayHere integration

## Troubleshooting

### Common Issues

#### 1. Payment Not Processing
- Check PayHere credentials
- Verify webhook URLs
- Check database connectivity

#### 2. Callback Not Received
- Verify webhook URL accessibility
- Check PayHere merchant settings
- Review server logs

#### 3. Payment Status Not Updated
- Check callback processing
- Verify database transactions
- Review error logs

### Debug Steps
1. **Check Logs** - Review Laravel and PayHere logs
2. **Verify Configuration** - Confirm environment variables
3. **Test Webhook** - Use PayHere test tools
4. **Database Check** - Verify payment records

## Support and Documentation

### PayHere Resources
- **Official Documentation**: https://www.payhere.lk/developers
- **API Reference**: https://www.payhere.lk/developers/api
- **Support**: https://www.payhere.lk/support

### System Integration
- **Laravel Integration**: Uses Laravel HTTP client
- **Database Integration**: Eloquent ORM for data management
- **Notification System**: Email and SMS notifications

## Conclusion

The PayHere integration provides a comprehensive payment solution for the PDPS system, supporting all major payment types with secure processing, proper error handling, and detailed receipt generation. The unified approach ensures consistency across all payment types while maintaining security and reliability.
