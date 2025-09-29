# Tax Payment Handling System - PDPS

## Overview
This document describes the comprehensive tax payment handling system implemented for the Pradeshiya Sabha (Local Government) management system. The system handles tax payees, assessments, payments, penalties, and property prohibition orders.

## System Architecture

### User Roles
- **OfficerTax** - Manages tax payees, assessments, payments, penalties, and prohibition orders
- **CustomerTax** - Views assessments, makes payments, searches by NIC
- **Admin** - Full access to all tax system features

### Database Schema

#### Core Tables
1. **tax_payees** - Tax payee information
2. **tax_properties** - Property details linked to payees
3. **tax_assessments** - Tax assessments for properties
4. **tax_payments** - Payment records
5. **tax_penalty_notices** - Penalty notices for overdue payments
6. **property_prohibition_orders** - Prohibition orders for properties

#### Relationships
- TaxPayee → hasMany → TaxProperty
- TaxProperty → hasMany → TaxAssessment
- TaxAssessment → hasMany → TaxPayment
- TaxAssessment → hasMany → TaxPenaltyNotice
- TaxProperty → hasMany → PropertyProhibitionOrder

## API Endpoints

### Tax Payee Management
```
GET    /api/tax-payees              - List payees
POST   /api/tax-payees              - Create payee
GET    /api/tax-payees/{id}         - View payee
PUT    /api/tax-payees/{id}         - Update payee
DELETE /api/tax-payees/{id}         - Delete payee
GET    /api/tax-payees/search/nic   - Search by NIC
```

### Tax Assessment Management
```
GET    /api/tax-assessments                    - List assessments
POST   /api/tax-assessments                     - Create assessment
GET    /api/tax-assessments/{id}                - View assessment
PUT    /api/tax-assessments/{id}                - Update assessment
DELETE /api/tax-assessments/{id}                - Delete assessment
GET    /api/tax-assessments/payee/{payeeId}     - Get by payee
PUT    /api/tax-assessments/{id}/mark-overdue   - Mark as overdue
```

### Tax Payment Management
```
GET    /api/tax-payments                    - List payments
POST   /api/tax-payments                     - Create payment (cash)
GET    /api/tax-payments/{id}                - View payment
PUT    /api/tax-payments/{id}                - Update payment
DELETE /api/tax-payments/{id}                - Delete payment
POST   /api/tax-payments/cash/{assessmentId} - Record cash payment
POST   /api/tax-payments/online/{assessmentId} - Process online payment
```

### Penalty Notices
```
GET    /api/penalty-notices                           - List notices
POST   /api/penalty-notices                           - Create notice
GET    /api/penalty-notices/{id}                      - View notice
PUT    /api/penalty-notices/{id}                      - Update notice
DELETE /api/penalty-notices/{id}                      - Delete notice
POST   /api/penalty-notices/assessment/{assessmentId} - Issue for assessment
PUT    /api/penalty-notices/{id}/resolve             - Resolve notice
```

### Property Prohibition Orders
```
GET    /api/prohibition-orders                    - List orders
POST   /api/prohibition-orders                     - Create order
GET    /api/prohibition-orders/{id}                - View order
PUT    /api/prohibition-orders/{id}                - Update order
DELETE /api/prohibition-orders/{id}                - Delete order
POST   /api/prohibition-orders/property/{propertyId} - Issue for property
PUT    /api/prohibition-orders/{id}/revoke         - Revoke order
GET    /api/prohibition-orders/active             - Get active orders
```

### PayHere Integration
```
POST   /api/payhere/callback - PayHere webhook callback
```

## PayHere Integration

### Configuration
Add to `.env`:
```
PAYHERE_MERCHANT_ID=your_merchant_id
PAYHERE_MERCHANT_SECRET=your_merchant_secret
PAYHERE_CHECKOUT_URL=https://sandbox.payhere.lk/pay/checkout
PAYHERE_RETURN_URL=https://yourdomain.com/api/payhere/return
PAYHERE_CANCEL_URL=https://yourdomain.com/api/payhere/cancel
PAYHERE_NOTIFY_URL=https://yourdomain.com/api/payhere/callback
```

### Payment Flow
1. Customer initiates online payment
2. System creates pending payment record
3. Customer redirected to PayHere checkout
4. PayHere processes payment
5. PayHere sends callback to webhook
6. System verifies signature and updates payment status
7. Notification sent to customer

## Notification System

### Email Notifications
- **TaxAssessmentCreated** - When new assessment is created
- **TaxPaymentConfirmed** - When payment is confirmed
- **TaxOverdueNotice** - When assessment becomes overdue
- **ProhibitionOrderIssued** - When prohibition order is issued

### SMS Notifications (Optional)
- Critical payment reminders
- Overdue notices
- Prohibition order alerts

### Automated Tasks
```bash
# Send overdue notifications (run daily)
php artisan tax:send-overdue-notifications
```

## Business Logic

### Tax Assessment Workflow
1. Officer creates tax assessment
2. System sends notification to payee
3. Payee can view assessment and make payment
4. If overdue, system marks as overdue and sends notice
5. Penalty notices can be issued for overdue payments
6. Prohibition orders can be issued for non-payment

### Payment Processing
1. **Cash Payments**: Officer records payment manually
2. **Online Payments**: Processed through PayHere gateway
3. **Payment Confirmation**: Automatic status updates and notifications
4. **Receipt Generation**: Available for all confirmed payments

### Prohibition Orders
1. Can only be issued by authorized officers
2. Requires secretary approval for certain cases
3. Automatically updates property prohibition status
4. Can be revoked when tax obligations are fulfilled

## Security Features

### PayHere Security
- Signature verification for all callbacks
- Secure hash generation for checkout data
- Transaction ID tracking

### Access Control
- Role-based permissions for all endpoints
- Officer-specific access to tax functions
- Customer access limited to their own data

## Error Handling

### Common Error Scenarios
- Invalid PayHere signatures
- Duplicate prohibition orders
- Payment processing failures
- Assessment deletion with existing payments

### Error Responses
All API endpoints return consistent JSON error responses:
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Testing

### Manual Testing
1. Create tax payee
2. Create tax property
3. Create tax assessment
4. Process cash payment
5. Process online payment
6. Issue penalty notice
7. Issue prohibition order
8. Test notifications

### Automated Testing
```bash
# Run migrations
php artisan migrate

# Seed test data
php artisan db:seed --class=TaxSystemSeeder

# Run tests
php artisan test --filter=TaxSystem
```

## Deployment

### Environment Setup
1. Configure PayHere credentials
2. Set up email notifications (Mailtrap/SMTP)
3. Configure SMS service (optional)
4. Set up scheduled tasks for overdue notifications

### Database Migration
```bash
php artisan migrate
```

### Queue Configuration
```bash
# For notification processing
php artisan queue:work
```

## Monitoring and Maintenance

### Daily Tasks
- Send overdue notifications
- Process pending payments
- Update assessment statuses

### Weekly Tasks
- Generate payment reports
- Review prohibition orders
- Clean up old notifications

### Monthly Tasks
- Generate tax collection reports
- Review penalty notices
- Update system configurations

## Support and Troubleshooting

### Common Issues
1. **PayHere Integration**: Check merchant credentials and webhook URLs
2. **Email Notifications**: Verify SMTP configuration
3. **Payment Processing**: Check database transactions and rollbacks
4. **Permission Errors**: Verify user roles and middleware

### Log Files
- Laravel logs: `storage/logs/laravel.log`
- PayHere callbacks: Check application logs
- Notification failures: Check queue logs

## Future Enhancements

### Planned Features
1. Bulk payment processing
2. Advanced reporting dashboard
3. Mobile app integration
4. Automated penalty calculations
5. Integration with other government systems

### Performance Optimizations
1. Database indexing for large datasets
2. Caching for frequently accessed data
3. Queue optimization for notifications
4. API response optimization

## Conclusion

The tax payment handling system provides a comprehensive solution for managing tax collections in the Pradeshiya Sabha system. It includes all necessary features for tax officers, customers, and administrators while maintaining security and reliability through proper validation, notifications, and error handling.
