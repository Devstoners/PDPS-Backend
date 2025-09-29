# Brevo Email Configuration

## Environment Variables

Add these variables to your `.env` file:

```env
# Brevo SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=980707001@smtp-brevo.com
MAIL_PASSWORD=5fJh8qSTMmzRUHwj
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=lak.bope@gmail.com
MAIL_FROM_NAME="Asanka Lakshitha"

# Brevo API Configuration (Optional - for API-based sending)
BREVO_API_KEY=your_brevo_api_key_here
BREVO_SMTP_USERNAME=980707001@smtp-brevo.com
BREVO_SMTP_PASSWORD=5fJh8qSTMmzRUHwj
```

## Configuration Files Updated

### 1. `config/mail.php`
- Updated SMTP host to `smtp-relay.brevo.com`
- Added Brevo-specific configuration
- Set default port to 587 with TLS encryption

### 2. `app/Services/BrevoEmailService.php`
- Created service for sending emails via Brevo API
- Includes methods for tax payment confirmations
- Includes methods for tax assessment notifications
- HTML and text email templates included

## Testing the Configuration

### 1. Test SMTP Configuration
```bash
php artisan brevo:test-email your-email@example.com
```

### 2. Test in Code
```php
use App\Services\BrevoEmailService;

$brevoService = new BrevoEmailService();
$result = $brevoService->sendEmail(
    ['email' => 'test@example.com', 'name' => 'Test User'],
    'Test Subject',
    '<h1>Test HTML Content</h1>',
    'Test Text Content'
);
```

### 3. Test Laravel Mail
```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Test email content', function ($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

## Email Templates

The service includes pre-built templates for:

1. **Tax Payment Confirmation**
   - Payment details
   - Property information
   - Payment method and date
   - Status information

2. **Tax Assessment Notification**
   - Assessment details
   - Property information
   - Due date and amount
   - Status information

## Usage in Controllers

```php
use App\Services\BrevoEmailService;

// In your controller
public function sendPaymentConfirmation($payment)
{
    $brevoService = new BrevoEmailService();
    $brevoService->sendTaxPaymentConfirmation($payment);
}

public function sendAssessmentNotification($assessment)
{
    $brevoService = new BrevoEmailService();
    $brevoService->sendTaxAssessmentNotification($assessment);
}
```

## Troubleshooting

### Common Issues:

1. **Authentication Failed**
   - Verify username and password are correct
   - Check if account is active in Brevo dashboard

2. **Connection Timeout**
   - Verify SMTP host and port settings
   - Check firewall settings

3. **Email Not Delivered**
   - Check spam folder
   - Verify recipient email address
   - Check Brevo sending limits

### Debug Commands:

```bash
# Test email configuration
php artisan brevo:test-email your-email@example.com

# Check mail configuration
php artisan config:show mail

# Clear configuration cache
php artisan config:clear
```

## Security Notes

- Never commit API keys or passwords to version control
- Use environment variables for sensitive data
- Regularly rotate API keys and passwords
- Monitor email sending limits and usage


