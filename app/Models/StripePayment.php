<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StripePayment extends Model
{
    use HasFactory;

    protected $table = 'stripe_payments';

    protected $fillable = [
        'stripe_session_id',
        'stripe_payment_intent_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'tax_type',
        'taxpayer_name',
        'nic',
        'email',
        'phone',
        'address',
        'stripe_metadata',
        'stripe_response',
        'paid_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'stripe_metadata' => 'array',
        'stripe_response' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Get the tax payee associated with this payment
     */
    public function taxPayee(): BelongsTo
    {
        return $this->belongsTo(TaxPayee::class, 'nic', 'nic');
    }

    /**
     * Get the tax property associated with this payment
     */
    public function taxProperty(): BelongsTo
    {
        return $this->belongsTo(TaxProperty::class, 'payment_id', 'id');
    }

    /**
     * Get the tax assessment associated with this payment
     */
    public function taxAssessment(): BelongsTo
    {
        return $this->belongsTo(TaxAssessment::class, 'payment_id', 'id');
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'succeeded');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark payment as successful
     */
    public function markAsSuccessful(): void
    {
        $this->update([
            'status' => 'succeeded',
            'paid_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => Carbon::now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Get payment status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'succeeded' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            'processing' => 'info',
            'canceled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get payment status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'succeeded' => 'Payment Successful',
            'failed' => 'Payment Failed',
            'pending' => 'Payment Pending',
            'processing' => 'Processing Payment',
            'canceled' => 'Payment Canceled',
            default => 'Unknown Status',
        };
    }
}
