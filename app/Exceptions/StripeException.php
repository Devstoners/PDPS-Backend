<?php

namespace App\Exceptions;

use Exception;

class StripeException extends Exception
{
    protected $stripeError;
    protected $context;

    public function __construct(string $message = "", int $code = 0, Exception $previous = null, $stripeError = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->stripeError = $stripeError;
        $this->context = $context;
    }

    public function getStripeError()
    {
        return $this->stripeError;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getFormattedMessage(): string
    {
        $message = $this->getMessage();
        
        if ($this->stripeError) {
            $message .= " | Stripe Error: " . json_encode($this->stripeError);
        }
        
        if (!empty($this->context)) {
            $message .= " | Context: " . json_encode($this->context);
        }
        
        return $message;
    }
}
