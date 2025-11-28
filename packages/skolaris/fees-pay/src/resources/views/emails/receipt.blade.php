<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
</head>

<body>
    <h1>Payment Receipt</h1>
    <p>Dear {{ $paymentData['name'] }},</p>
    <p>Thank you for your payment of {{ $paymentData['currency'] }} {{ $paymentData['amount'] }}.</p>
    <p>Please find your receipt attached.</p>
    <p>Regards,<br>School Management</p>
</body>

</html>
