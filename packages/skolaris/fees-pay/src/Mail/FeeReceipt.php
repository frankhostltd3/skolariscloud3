<?php

namespace Skolaris\FeesPay\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeeReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $paymentData;
    public $pdfPath;

    public function __construct($paymentData, $pdfPath)
    {
        $this->paymentData = $paymentData;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('fees-pay::emails.receipt')
                    ->subject('School Fees Receipt')
                    ->attach($this->pdfPath, [
                        'as' => 'receipt.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
