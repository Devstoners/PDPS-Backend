<?php

namespace App\Services;

use App\Models\Tax;
use App\Models\Payments;
use App\Repositories\TaxRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;

class TaxService
{
    protected TaxRepository $taxRepository;
    protected PaymentRepository $paymentRepository;

    public function __construct(TaxRepository $taxRepository, PaymentRepository $paymentRepository)
    {
        $this->taxRepository = $taxRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Create or update tax record with payment
     * @param array $data
     * @return array
     */
    public function processTaxPayment(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Check if tax record exists
            $existingTax = $this->taxRepository->findByNic($data['nic']);
            
            if (!$existingTax) {
                // Create new tax record
                $tax = $this->taxRepository->create([
                    'name' => $data['name'],
                    'nic' => $data['nic'],
                    'amount' => $data['amount'],
                    'account' => $data['account'],
                    'telephone' => $data['telephone'],
                    'description' => $data['description'],
                    'email' => $data['email'],
                ]);
            } else {
                $tax = $existingTax;
            }

            // Create payment record
            $payment = $this->paymentRepository->createPayment([
                'year' => $data['year'],
                'amount' => $data['amount'],
                'tax_id' => $tax->id,
                'payment_type' => $data['payment_type']
            ]);

            // Send notification email
            $this->sendPaymentNotification($tax, $payment);

            return [
                'tax' => $tax,
                'payment' => $payment,
            ];
        });
    }

    /**
     * Get tax record by NIC
     * @param string $nic
     * @return Tax|null
     */
    public function getTaxByNic(string $nic): ?Tax
    {
        return $this->taxRepository->findByNic($nic);
    }

    /**
     * Get all tax records with payments
     * @return Collection
     */
    public function getAllTaxRecordsWithPayments(): Collection
    {
        return $this->taxRepository->getWithPayments();
    }

    /**
     * Get tax statistics
     * @return array
     */
    public function getTaxStatistics(): array
    {
        $currentYear = now()->year;
        
        return [
            'total_tax_amount' => $this->taxRepository->getTotalTaxAmount(),
            'total_tax_records' => $this->taxRepository->count(),
            'current_year_payments' => $this->paymentRepository->getTotalPaymentsByYear($currentYear),
            'recent_payments' => $this->paymentRepository->getRecentPayments(5, ['*'], ['tax']),
        ];
    }

    /**
     * Get payments by tax ID
     * @param int $taxId
     * @return Collection
     */
    public function getPaymentsByTaxId(int $taxId): Collection
    {
        return $this->paymentRepository->getByTaxId($taxId);
    }

    /**
     * Get payments by year
     * @param int $year
     * @return Collection
     */
    public function getPaymentsByYear(int $year): Collection
    {
        return $this->paymentRepository->getByYear($year);
    }

    /**
     * Get payments by type
     * @param string $paymentType
     * @return Collection
     */
    public function getPaymentsByType(string $paymentType): Collection
    {
        return $this->paymentRepository->getByType($paymentType);
    }

    /**
     * Search tax records by name
     * @param string $name
     * @return Collection
     */
    public function searchTaxByName(string $name): Collection
    {
        return $this->taxRepository->searchByName($name);
    }

    /**
     * Get tax records by amount range
     * @param float $minAmount
     * @param float $maxAmount
     * @return Collection
     */
    public function getTaxByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->taxRepository->getByAmountRange($minAmount, $maxAmount);
    }

    /**
     * Update tax record
     * @param int $taxId
     * @param array $data
     * @return Tax
     */
    public function updateTaxRecord(int $taxId, array $data): Tax
    {
        return $this->taxRepository->update($taxId, $data);
    }

    /**
     * Delete tax record
     * @param int $taxId
     * @return bool
     */
    public function deleteTaxRecord(int $taxId): bool
    {
        return $this->taxRepository->delete($taxId);
    }

    /**
     * Get yearly payment report
     * @param int $year
     * @return array
     */
    public function getYearlyPaymentReport(int $year): array
    {
        $payments = $this->paymentRepository->getByYear($year, ['*'], ['tax']);
        
        return [
            'year' => $year,
            'total_amount' => $this->paymentRepository->getTotalPaymentsByYear($year),
            'payment_count' => $payments->count(),
            'payments' => $payments,
            'by_type' => $payments->groupBy('payment_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount')
                ];
            }),
        ];
    }

    /**
     * Send payment notification email
     * @param Tax $tax
     * @param Payments $payment
     * @return bool
     */
    private function sendPaymentNotification(Tax $tax, Payments $payment): bool
    {
        try {
            $message = "Payment of {$payment->amount} for year {$payment->year} has been processed successfully.";
            
            if ($tax->email) {
                Mail::to($tax->email)->send(new CustomEmail($message));
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send payment notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate tax summary report
     * @param array $filters
     * @return array
     */
    public function generateTaxSummaryReport(array $filters = []): array
    {
        $taxRecords = $this->taxRepository->all(['*'], ['payments']);
        
        $summary = [
            'total_records' => $taxRecords->count(),
            'total_tax_amount' => $taxRecords->sum('amount'),
            'total_payments' => 0,
            'total_payment_amount' => 0,
            'outstanding_amount' => 0,
        ];

        foreach ($taxRecords as $tax) {
            $paymentTotal = $tax->payments->sum('amount');
            $summary['total_payments'] += $tax->payments->count();
            $summary['total_payment_amount'] += $paymentTotal;
            $summary['outstanding_amount'] += max(0, $tax->amount - $paymentTotal);
        }

        return $summary;
    }
}