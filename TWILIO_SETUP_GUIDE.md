# Twilio SMS Setup Guide - PDPS

## 🚀 Getting Started with Twilio Free Plan

This guide will help you set up Twilio SMS notifications for your PDPS system using the free plan.

## 📋 Prerequisites

- Twilio account (free)
- Laravel application (already set up)
- Phone number for testing

## 🔧 Step 1: Create Twilio Account

### **1.1 Sign Up**
1. Go to [https://www.twilio.com/try-twilio](https://www.twilio.com/try-twilio)
2. Click "Start Free Trial"
3. Fill in your details:
   - **Email**: Your email address
   - **Password**: Strong password
   - **Phone Number**: Your phone number for verification
   - **Country**: Sri Lanka

### **1.2 Verify Your Account**
1. Check your email for verification link
2. Verify your phone number via SMS
3. Complete the account setup

## 🔑 Step 2: Get Your Credentials

### **2.1 Account SID and Auth Token**
1. Go to [Twilio Console Dashboard](https://console.twilio.com/)
2. Find your **Account SID** (starts with AC...)
3. Find your **Auth Token** (click to reveal)
4. Copy both values

### **2.2 Phone Number**
1. Go to **Phone Numbers** → **Manage** → **Buy a number**
2. Choose a number (free trial includes one number)
3. Select a number with SMS capabilities
4. Complete the purchase (free for trial)

## ⚙️ Step 3: Configure Environment

### **3.1 Update .env File**
```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_FROM_NUMBER=+1234567890

# SMS Settings
TWILIO_SMS_ENABLED=true
TWILIO_PAYMENT_SMS=true
TWILIO_REMINDER_SMS=true
TWILIO_OVERDUE_SMS=true
TWILIO_RESERVATION_SMS=true
```

### **3.2 Verify Configuration**
```bash
# Check if configuration is loaded
php artisan config:cache
php artisan config:clear
```

## 🧪 Step 4: Test SMS Integration

### **4.1 Test Configuration**
```bash
# Test SMS service
php artisan sms:test --phone=+94771234567 --type=custom --message="Test SMS from PDPS"
```

### **4.2 Test API Endpoints**
```bash
# Start Laravel server
php artisan serve

# Test in another terminal
curl -X POST http://localhost:8000/api/sms/test \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "phone": "+94771234567",
    "message": "Test SMS from PDPS API"
  }'
```

## 📱 Step 5: Test Different SMS Types

### **5.1 Payment Confirmation**
```bash
php artisan sms:test --phone=+94771234567 --type=payment
```

### **5.2 Service Reminder**
```bash
php artisan sms:test --phone=+94771234567 --type=reminder
```

### **5.3 Overdue Notice**
```bash
php artisan sms:test --phone=+94771234567 --type=overdue
```

### **5.4 Hall Reservation**
```bash
php artisan sms:test --phone=+94771234567 --type=reservation
```

### **5.5 Tax Assessment**
```bash
php artisan sms:test --phone=+94771234567 --type=tax
```

### **5.6 Water Bill**
```bash
php artisan sms:test --phone=+94771234567 --type=water
```

## 🔒 Step 6: Free Plan Limitations

### **6.1 SMS Limits**
- **Daily Limit**: 100 SMS per day
- **Hourly Limit**: 10 SMS per hour per number
- **Phone Numbers**: Only verified numbers
- **Features**: Basic SMS only

### **6.2 Rate Limiting**
The system automatically handles rate limiting:
- **Cache-based**: Uses Laravel cache
- **Per Number**: Limits per phone number
- **Automatic Reset**: Resets every hour/day

### **6.3 Monitoring Usage**
```bash
# Check account info
curl -X GET http://localhost:8000/api/sms/account-info \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🚨 Step 7: Troubleshooting

### **7.1 Common Issues**

#### **SMS Not Sending**
```bash
# Check configuration
php artisan config:show twilio

# Check logs
tail -f storage/logs/laravel.log
```

#### **Invalid Phone Number**
- Ensure format: `+94771234567`
- Remove spaces and dashes
- Include country code

#### **Rate Limit Exceeded**
- Wait for reset (1 hour for hourly, 24 hours for daily)
- Check current usage
- Consider upgrading plan

#### **Authentication Failed**
- Verify Account SID and Auth Token
- Check for typos in .env file
- Ensure credentials are correct

### **7.2 Debug Commands**
```bash
# Test SMS service directly
php artisan sms:test --phone=+94771234567 --type=all

# Check Twilio account status
php artisan sms:test --phone=+94771234567 --type=custom --message="Account test"
```

## 📊 Step 8: Monitor SMS Delivery

### **8.1 Check Delivery Status**
```bash
# Get message SID from logs or response
curl -X GET http://localhost:8000/api/sms/status/{messageSid} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### **8.2 View Logs**
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log | grep SMS

# Search for specific phone number
grep "+94771234567" storage/logs/laravel.log
```

## 🎯 Step 9: Production Setup

### **9.1 Upgrade Considerations**
- **Paid Plan**: For higher SMS limits
- **Dedicated Number**: For production use
- **Webhooks**: For delivery status updates
- **Analytics**: For usage tracking

### **9.2 Security Best Practices**
- **Environment Variables**: Never commit credentials
- **Rate Limiting**: Implement proper limits
- **Error Handling**: Graceful failure handling
- **Logging**: Comprehensive logging

### **9.3 Queue Processing**
```bash
# For background SMS processing
php artisan queue:work

# Monitor queue
php artisan queue:monitor
```

## 📈 Step 10: Usage Analytics

### **10.1 Track SMS Usage**
- **Twilio Console**: View usage in dashboard
- **Laravel Logs**: Check application logs
- **Database**: Store SMS logs (optional)

### **10.2 Monitor Performance**
- **Delivery Rate**: Track successful deliveries
- **Error Rate**: Monitor failed messages
- **Response Time**: Track SMS processing time

## 🔧 Advanced Configuration

### **10.1 Custom Message Templates**
Edit `config/twilio.php` to customize templates:

```php
'templates' => [
    'payment_confirmation' => 'Payment confirmed! Amount: LKR {amount}. Receipt: {receipt_no}. Thank you!',
    'service_reminder' => 'Reminder: Your {service} is due on {due_date}. Amount: LKR {amount}.',
    // Add more custom templates
],
```

### **10.2 Rate Limiting Configuration**
```php
'rate_limiting' => [
    'enabled' => true,
    'max_per_hour' => 10, // Adjust as needed
    'max_per_day' => 100, // Adjust as needed
],
```

## ✅ Step 11: Verification Checklist

### **11.1 Setup Verification**
- [ ] Twilio account created
- [ ] Account SID and Auth Token obtained
- [ ] Phone number purchased
- [ ] Environment variables configured
- [ ] SMS service tested
- [ ] API endpoints working
- [ ] Rate limiting configured
- [ ] Error handling implemented

### **11.2 Functionality Verification**
- [ ] Test SMS sending
- [ ] Payment confirmation SMS
- [ ] Service reminder SMS
- [ ] Overdue notice SMS
- [ ] Hall reservation SMS
- [ ] Tax assessment SMS
- [ ] Water bill SMS
- [ ] Delivery status tracking
- [ ] Error logging working

## 🎉 Step 12: Go Live!

### **12.1 Final Testing**
```bash
# Test all SMS types
php artisan sms:test --phone=+94771234567 --type=all

# Test API endpoints
curl -X POST http://localhost:8000/api/sms/payment-confirmation \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "phone": "+94771234567",
    "amount": "1500.00",
    "receipt_no": "WB001",
    "service": "Water Bill"
  }'
```

### **12.2 Production Deployment**
1. **Environment**: Set production environment variables
2. **Queue**: Configure queue workers
3. **Monitoring**: Set up monitoring and alerting
4. **Backup**: Ensure proper backup procedures

## 📞 Support Resources

### **Twilio Support**
- **Documentation**: https://www.twilio.com/docs/sms
- **Support**: https://support.twilio.com/
- **Community**: https://www.twilio.com/community
- **Status**: https://status.twilio.com/

### **Laravel Integration**
- **Notifications**: https://laravel.com/docs/notifications
- **Queues**: https://laravel.com/docs/queues
- **Logging**: https://laravel.com/docs/logging

## 🎯 Conclusion

Your Twilio SMS integration is now ready! The system provides:

- ✅ **Automated SMS notifications** for all PDPS services
- ✅ **Rate limiting** to respect free plan limits
- ✅ **Error handling** for robust operation
- ✅ **Testing tools** for easy verification
- ✅ **API endpoints** for integration
- ✅ **Comprehensive logging** for monitoring

**Ready for production use!** 🚀

## 📱 Next Steps

1. **Test thoroughly** with your phone number
2. **Monitor usage** to stay within free plan limits
3. **Consider upgrading** if you need higher limits
4. **Set up monitoring** for production use
5. **Train staff** on SMS notification features

**Happy SMS sending!** 📱✨
