<?php

namespace Skolaris\FeesPay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Skolaris\FeesPay\Services\PaymentGateways\FlutterwaveGateway;
use Skolaris\FeesPay\Services\PaymentGateways\StripeGateway;
use Skolaris\FeesPay\Services\PaymentGateways\PayPalGateway;
use Skolaris\FeesPay\Services\Notifications\SmsService;
use Skolaris\FeesPay\Mail\FeeReceipt;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $gatewayName = $request->input('gateway');
        $gateway = $this->getGateway($gatewayName);

        $data = $request->all();
        // Add logic to generate tx_ref, etc.
        $data['tx_ref'] = uniqid('tx_');

        $result = $gateway->initiatePayment($data);

        return response()->json($result);
    }

    public function callback(Request $request)
    {
        $gatewayName = $request->input('gateway'); // Passed as query param or determined by route
        $gateway = $this->getGateway($gatewayName);
        
        $reference = $request->input('tx_ref') ?? $request->input('session_id'); // Adjust based on gateway
        
        $paymentDetails = $gateway->verifyPayment($reference);

        // If payment successful
        if ($this->isPaymentSuccessful($paymentDetails, $gatewayName)) {
            // Generate PDF Receipt
            $pdf = Pdf::loadView('fees-pay::pdf.receipt', ['paymentData' => $paymentDetails]);
            $pdfPath = storage_path('app/receipts/' . $reference . '.pdf');
            $pdf->save($pdfPath);

            // Send Email
            Mail::to($paymentDetails['customer']['email'])->send(new FeeReceipt($paymentDetails, $pdfPath));

            // Send SMS
            $smsService = new SmsService();
            $smsService->send($paymentDetails['customer']['phone'], "Payment of {$paymentDetails['amount']} received. Receipt sent to email.");

            return response()->json(['status' => 'success', 'message' => 'Payment verified and receipt sent.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment verification failed.']);
    }

    protected function getGateway($name)
    {
        switch ($name) {
            case 'stripe':
                return new StripeGateway();
            case 'paypal':
                return new PayPalGateway();
            case 'flutterwave':
            default:
                return new FlutterwaveGateway();
        }
    }

    protected function isPaymentSuccessful($details, $gateway)
    {
        // Implement logic to check status from gateway response
        return true; // Placeholder
    }
}
