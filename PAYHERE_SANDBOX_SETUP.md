# PayHere Sandbox Setup Guide - PDPS

## Sandbox Environment Configuration

### 1. Environment Variables
Add these to your `.env` file for PayHere sandbox:

```env
# PayHere Sandbox Configuration
PAYHERE_MERCHANT_ID=your_sandbox_merchant_id
PAYHERE_MERCHANT_SECRET=your_sandbox_merchant_secret
PAYHERE_CHECKOUT_URL=https://sandbox.payhere.lk/pay/checkout
PAYHERE_RETURN_URL=https://yourdomain.com/api/payhere/return
PAYHERE_CANCEL_URL=https://yourdomain.com/api/payhere/cancel
PAYHERE_NOTIFY_URL=https://yourdomain.com/api/payhere/callback
PAYHERE_SANDBOX=true
```

### 2. PayHere Sandbox Credentials
To get your sandbox credentials:

1. **Register at PayHere**: https://www.payhere.lk/register
2. **Login to Merchant Dashboard**: https://www.payhere.lk/merchant
3. **Navigate to Sandbox Settings**
4. **Copy your sandbox credentials**:
   - Merchant ID
   - Merchant Secret

### 3. Test Card Numbers
Use these test card numbers for sandbox testing:

#### Visa Cards
- **4111111111111111** - Visa (Success)
- **4000000000000002** - Visa (Declined)

#### Mastercard
- **5555555555554444** - Mastercard (Success)
- **5555555555554445** - Mastercard (Declined)

#### American Express
- **378282246310005** - Amex (Success)
- **378282246310006** - Amex (Declined)

### 4. Test Amounts
- **Minimum**: LKR 1.00
- **Maximum**: LKR 10,000.00
- **Recommended**: LKR 100.00 - LKR 1,000.00

## Testing the Integration

### 1. Test Water Bill Payment
```bash
curl -X POST https://yourdomain.com/api/water-bills/online-payment \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "water_bill_id": 1,
    "amount_paid": 500.00,
    "customer_data": {
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "phone": "0771234567",
      "address": "123 Main Street",
      "city": "Colombo"
    }
  }'
```

### 2. Test Hall Reservation Payment
```bash
curl -X POST https://yourdomain.com/api/hall-reservations/1/payments/online \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "amount": 2000.00,
    "customer_data": {
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane@example.com",
      "phone": "0779876543",
      "address": "456 Oak Avenue",
      "city": "Kandy"
    }
  }'
```

### 3. Test Tax Payment
```bash
curl -X POST https://yourdomain.com/api/tax-payments/online/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "payment": 1500.00,
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
  }'
```

## Webhook Testing

### 1. Using ngrok for Local Testing
```bash
# Install ngrok
npm install -g ngrok

# Start your Laravel server
php artisan serve

# In another terminal, expose your local server
ngrok http 8000

# Update your webhook URL in .env
PAYHERE_NOTIFY_URL=https://your-ngrok-url.ngrok.io/api/payhere/callback
```

### 2. Webhook Testing with PayHere
1. **Login to PayHere Sandbox Dashboard**
2. **Go to Webhook Testing**
3. **Enter your webhook URL**: `https://yourdomain.com/api/payhere/callback`
4. **Test webhook delivery**

### 3. Manual Webhook Testing
```bash
curl -X POST https://yourdomain.com/api/payhere/callback \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "merchant_id=your_merchant_id&order_id=WB_123&payhere_amount=500.00&payhere_currency=LKR&status_code=2&md5sig=test_signature"
```

## Sandbox Features

### 1. Test Scenarios
- **Successful Payments**: Use test cards that return success
- **Failed Payments**: Use test cards that return declined
- **Partial Payments**: Test partial payment scenarios
- **Refunds**: Test refund functionality

### 2. Sandbox Limitations
- **No Real Money**: All transactions are simulated
- **Limited Features**: Some production features may not be available
- **Test Data Only**: Use only test card numbers

### 3. Debugging
Enable debug logging in your `.env`:
```env
LOG_LEVEL=debug
PAYHERE_DEBUG=true
```

## Production Migration

### 1. When Ready for Production
1. **Get Production Credentials** from PayHere
2. **Update Environment Variables**:
   ```env
   PAYHERE_MERCHANT_ID=your_production_merchant_id
   PAYHERE_MERCHANT_SECRET=your_production_merchant_secret
   PAYHERE_CHECKOUT_URL=https://www.payhere.lk/pay/checkout
   PAYHERE_SANDBOX=false
   ```

3. **Update Webhook URLs** to production domain
4. **Test with Small Amounts** first
5. **Monitor Payment Processing**

### 2. Security Checklist
- [ ] HTTPS enabled for all webhook URLs
- [ ] Signature verification working
- [ ] Database transactions secure
- [ ] Error handling implemented
- [ ] Logging configured

## Troubleshooting

### Common Sandbox Issues

#### 1. "Invalid Merchant ID"
- Check your sandbox merchant ID
- Ensure you're using sandbox credentials
- Verify merchant account is active

#### 2. "Webhook Not Received"
- Check webhook URL accessibility
- Verify HTTPS is enabled
- Test webhook URL manually

#### 3. "Payment Not Processing"
- Check PayHere sandbox status
- Verify test card numbers
- Check amount limits

#### 4. "Signature Verification Failed"
- Verify merchant secret
- Check hash generation
- Ensure proper encoding

### Debug Commands
```bash
# Check PayHere configuration
php artisan tinker
>>> config('payhere')

# Test webhook URL
curl -X GET https://yourdomain.com/api/payhere/callback

# Check payment status
curl -X GET https://yourdomain.com/api/payments/status/WB_123
```

## Support Resources

### PayHere Documentation
- **Sandbox Guide**: https://www.payhere.lk/developers/sandbox
- **API Reference**: https://www.payhere.lk/developers/api
- **Webhook Testing**: https://www.payhere.lk/developers/webhooks

### Laravel Integration
- **HTTP Client**: https://laravel.com/docs/http-client
- **Queue Jobs**: https://laravel.com/docs/queues
- **Notifications**: https://laravel.com/docs/notifications

## Conclusion

The PayHere sandbox provides a safe environment to test all payment functionality before going live. Follow this guide to set up and test your integration thoroughly.
