# SMS Integration Documentation - PDPS

## 📱 Overview

This document describes the SMS notification system integrated with Twilio for the PDPS (Pradeshiya Sabha Digital Platform System). The system provides automated SMS notifications for various services including water bill payments, hall reservations, and tax payments.

## 🚀 Features

### **SMS Notification Types**
- ✅ **Payment Confirmations**: Automatic SMS when payments are completed
- ✅ **Service Reminders**: Reminder SMS for upcoming due dates
- ✅ **Overdue Notices**: Urgent SMS for overdue payments
- ✅ **Hall Reservation Confirmations**: SMS for hall booking confirmations
- ✅ **Tax Assessment Notifications**: SMS for new tax assessments
- ✅ **Water Bill Notifications**: SMS for water bill generation
- ✅ **Custom Messages**: Ability to send custom SMS messages

### **Service Integration**
- 💧 **Water Bill Payments**: SMS notifications for payment status
- 🏛️ **Hall Reservations**: SMS for booking confirmations and updates
- 💰 **Tax Payments**: SMS for tax payment confirmations and assessments
- 🔄 **Unified Payment System**: Integrated with PayHere payment gateway

## 🛠️ Technical Implementation

### **Core Components**

#### **1. SmsNotificationService**
- **Location**: `app/Services/SmsNotificationService.php`
- **Purpose**: Main service for handling all SMS operations
- **Features**:
  - Rate limiting (Free plan: 10/hour, 100/day)
  - Phone number formatting
  - Message template system
  - Delivery status tracking
  - Error handling and logging

#### **2. SMS Notification Classes**
- **SmsPaymentConfirmation**: Payment confirmation notifications
- **SmsServiceReminder**: Service reminder notifications
- **SmsOverdueNotice**: Overdue payment notifications
- **SmsReservationConfirmation**: Hall reservation notifications

#### **3. SMS Channel**
- **Location**: `app/Channels/SmsChannel.php`
- **Purpose**: Laravel notification channel for SMS
- **Integration**: Works with Laravel's notification system

#### **4. SMS Controller**
- **Location**: `app/Http/Controllers/SmsNotificationController.php`
- **Purpose**: API endpoints for SMS operations
- **Features**: Test SMS, delivery status, account info

## 📋 Configuration

### **Environment Variables**
```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=+1234567890

# SMS Settings
TWILIO_SMS_ENABLED=true
TWILIO_PAYMENT_SMS=true
TWILIO_REMINDER_SMS=true
TWILIO_OVERDUE_SMS=true
TWILIO_RESERVATION_SMS=true
```

### **Configuration File**
- **Location**: `config/twilio.php`
- **Features**:
  - Rate limiting settings
  - Message templates
  - Country code configuration
  - Notification preferences

## 🔧 API Endpoints

### **SMS Notification Endpoints**

#### **Test SMS**
```http
POST /api/sms/test
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "message": "Test message"
}
```

#### **Payment Confirmation**
```http
POST /api/sms/payment-confirmation
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "amount": "1500.00",
    "receipt_no": "WB001",
    "service": "Water Bill"
}
```

#### **Service Reminder**
```http
POST /api/sms/service-reminder
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "service": "Water Bill",
    "due_date": "2024-02-15",
    "amount": "750.00"
}
```

#### **Overdue Notice**
```http
POST /api/sms/overdue-notice
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "service": "Tax Payment",
    "amount": "2500.00"
}
```

#### **Hall Reservation Confirmation**
```http
POST /api/sms/reservation-confirmation
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "date": "2024-02-20",
    "time": "18:00 - 22:00",
    "hall_name": "Main Hall"
}
```

#### **Tax Assessment**
```http
POST /api/sms/tax-assessment
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "amount": "3000.00",
    "due_date": "2024-03-01",
    "property_name": "123 Main Street"
}
```

#### **Water Bill**
```http
POST /api/sms/water-bill
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "amount": "850.00",
    "due_date": "2024-02-28",
    "account_no": "WB001234"
}
```

#### **Custom SMS**
```http
POST /api/sms/custom
Content-Type: application/json
Authorization: Bearer {token}

{
    "phone": "+94771234567",
    "message": "Custom message content"
}
```

#### **Delivery Status**
```http
GET /api/sms/status/{messageSid}
Authorization: Bearer {token}
```

#### **Account Information**
```http
GET /api/sms/account-info
Authorization: Bearer {token}
```

## 🧪 Testing

### **Artisan Commands**

#### **Test SMS Integration**
```bash
# Test all SMS types
php artisan sms:test --phone=+94771234567 --type=all

# Test specific type
php artisan sms:test --phone=+94771234567 --type=payment

# Test with custom message
php artisan sms:test --phone=+94771234567 --type=custom --message="Custom test message"
```

#### **Available Test Types**
- `all` - Test all SMS types
- `payment` - Payment confirmation SMS
- `reminder` - Service reminder SMS
- `overdue` - Overdue notice SMS
- `reservation` - Hall reservation SMS
- `tax` - Tax assessment SMS
- `water` - Water bill SMS
- `custom` - Custom message SMS

### **Manual Testing**

#### **1. Test SMS Service**
```bash
php artisan sms:test --phone=+94771234567 --type=all
```

#### **2. Test API Endpoints**
```bash
# Test payment confirmation
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

#### **3. Test Account Information**
```bash
curl -X GET http://localhost:8000/api/sms/account-info \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📱 Message Templates

### **Payment Confirmation**
```
Payment confirmed! Amount: LKR {amount}. Receipt: {receipt_no}. Thank you!
```

### **Payment Failed**
```
Payment failed for {service}. Please try again or contact support.
```

### **Service Reminder**
```
Reminder: Your {service} is due on {due_date}. Amount: LKR {amount}.
```

### **Overdue Notice**
```
URGENT: Your {service} is overdue. Amount: LKR {amount}. Please pay immediately.
```

### **Hall Reservation Confirmation**
```
Hall reservation confirmed! Date: {date}, Time: {time}, Hall: {hall_name}.
```

### **Hall Reservation Cancellation**
```
Hall reservation cancelled for {date}. Refund will be processed.
```

### **Tax Assessment**
```
New tax assessment: LKR {amount} due on {due_date}. Property: {property_name}.
```

### **Water Bill**
```
Water bill generated: LKR {amount} due on {due_date}. Account: {account_no}.
```

## 🔒 Security & Rate Limiting

### **Rate Limiting**
- **Hourly Limit**: 10 SMS per phone number (Free plan)
- **Daily Limit**: 100 SMS per phone number (Free plan)
- **Implementation**: Laravel Cache with TTL
- **Bypass**: Admin override available

### **Phone Number Validation**
- **Format**: International format (+94XXXXXXXXX)
- **Validation**: Automatic country code addition
- **Sri Lanka**: Default +94 country code

### **Message Security**
- **Length Limit**: 160 characters (standard SMS)
- **Unicode Support**: Enabled for Sinhala/Tamil
- **Content Filtering**: Basic profanity filter

## 📊 Monitoring & Logging

### **Logging**
- **Success Logs**: Message SID, recipient, timestamp
- **Error Logs**: Error codes, failure reasons
- **Rate Limit Logs**: Exceeded limits tracking
- **Location**: `storage/logs/laravel.log`

### **Monitoring**
- **Delivery Status**: Real-time status tracking
- **Success Rate**: Monitoring delivery success
- **Error Tracking**: Failed message analysis
- **Usage Statistics**: SMS volume tracking

## 🚀 Deployment

### **Production Setup**

#### **1. Twilio Account Setup**
1. Create Twilio account
2. Get Account SID and Auth Token
3. Purchase phone number
4. Configure webhooks (if needed)

#### **2. Environment Configuration**
```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_FROM_NUMBER=+1234567890
TWILIO_SMS_ENABLED=true
```

#### **3. Database Migration**
```bash
# No additional migrations required
# SMS data is stored in logs and cache
```

#### **4. Queue Configuration**
```bash
# For background SMS processing
php artisan queue:work
```

### **Free Plan Limitations**
- **SMS Limit**: 100 SMS per day
- **Phone Numbers**: Limited to verified numbers
- **Features**: Basic SMS only
- **Support**: Community support

## 🔧 Troubleshooting

### **Common Issues**

#### **1. SMS Not Sending**
- Check Twilio credentials
- Verify phone number format
- Check rate limiting
- Review error logs

#### **2. Rate Limit Exceeded**
- Wait for rate limit reset
- Check daily/hourly limits
- Consider upgrading plan

#### **3. Invalid Phone Number**
- Ensure international format
- Check country code
- Verify number validity

#### **4. Message Delivery Failed**
- Check Twilio account status
- Verify phone number
- Review error codes

### **Debug Commands**
```bash
# Check SMS configuration
php artisan sms:test --phone=+94771234567 --type=custom

# Check account status
curl -X GET http://localhost:8000/api/sms/account-info

# Review logs
tail -f storage/logs/laravel.log
```

## 📈 Performance Optimization

### **Best Practices**
- **Queue Processing**: Use Laravel queues for background SMS
- **Rate Limiting**: Implement proper rate limiting
- **Error Handling**: Graceful error handling
- **Logging**: Comprehensive logging for debugging

### **Scaling Considerations**
- **Queue Workers**: Multiple queue workers for high volume
- **Database**: Consider SMS logging database for analytics
- **Caching**: Cache rate limit data
- **Monitoring**: Real-time monitoring and alerting

## 🎯 Future Enhancements

### **Planned Features**
- **SMS Templates**: Admin-configurable templates
- **Bulk SMS**: Mass messaging capabilities
- **SMS Analytics**: Detailed reporting
- **Two-Factor Authentication**: SMS-based 2FA
- **SMS Scheduling**: Scheduled message delivery

### **Integration Opportunities**
- **WhatsApp**: WhatsApp Business API integration
- **Email**: Combined SMS/Email notifications
- **Push Notifications**: Mobile app notifications
- **Voice Calls**: Automated voice notifications

## 📞 Support

### **Twilio Resources**
- **Documentation**: https://www.twilio.com/docs/sms
- **Support**: https://support.twilio.com/
- **Status Page**: https://status.twilio.com/

### **Laravel Integration**
- **Notifications**: https://laravel.com/docs/notifications
- **Queues**: https://laravel.com/docs/queues
- **Logging**: https://laravel.com/docs/logging

## ✅ Conclusion

The SMS integration provides a comprehensive notification system for the PDPS platform, enabling automated communication with users for various services. The system is designed to be scalable, secure, and user-friendly while respecting rate limits and providing excellent error handling.

**Key Benefits:**
- ✅ **Automated Notifications**: Reduces manual communication
- ✅ **User Engagement**: Improves user experience
- ✅ **Payment Tracking**: Real-time payment confirmations
- ✅ **Service Reminders**: Reduces overdue payments
- ✅ **Cost Effective**: Free plan suitable for small to medium usage
- ✅ **Scalable**: Easy to upgrade for higher volumes

**Ready for production use!** 🚀
