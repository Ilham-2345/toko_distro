<?php 
require_once __DIR__ . '/../vendor/autoload.php';

function updateOrderStatus($orderId, $status, $paymentMethod) {
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET 
            payment_method = :payment_method,
            status = :status, updated_at = NOW() 
        WHERE invoice_number = :invoice_number
    ");

    return $stmt->execute([
        'status' => $status,
        'payment_method' => $paymentMethod,
        'invoice_number' => $orderId
    ]);
}

if ($action == 'update') {
    \Midtrans\Config::$serverKey = 'SB-Mid-server-Abx9Ib5nEaqaM-JV5BOQyi8Z';
    \Midtrans\Config::$isProduction = false;


    $notif = new \Midtrans\Notification();
    $transaction = $notif->transaction_status;
    $fraud = $notif->fraud_status;

    # dapatkan order id
    $orderId = $notif->order_id;

    // ambil payment type
    $paymentType = $notif->payment_type;
    $paymentChannel = null;

    if ($paymentType == 'bank_transfer') {
        $paymentChannel = $notif->va_numbers[0]->bank ?? null;
    } elseif ($paymentType == 'cstore') {
        $paymentChannel = $notif->store ?? null;
    } else {
        $paymentChannel = $paymentType;
    }

    $order = $pdo->prepare("SELECT * FROM orders WHERE invoice_number = :invoice_number LIMIT 1");
    $order->execute([
        'invoice_number' => $orderId
    ]);

    if (!$order) {
        return response()->json(['error' => 'Order not found!'], 404);
    }

    // Wait
    sleep(16);

    if ($transaction == 'capture') {
        if ($fraud == 'accept') {
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET 
                    payment_method = :payment_method,
                    status = :status
                WHERE invoice_number = :invoice_number
            ");

            $stmt->execute([
                'status' => 'paid',
                'payment_method' => $paymentChannel,
                'invoice_number' => $orderId
            ]);
        }
    }
    else if ($transaction == 'cancel') {

    }
    else if ($transaction == 'deny') {

    }
    else if ($transaction == 'settlement') {
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET 
                payment_method = :payment_method,
                status = :status
            WHERE invoice_number = :invoice_number
        ");

        $stmt->execute([
            'status' => 'paid',
            'payment_method' => $paymentChannel,
            'invoice_number' => $orderId
        ]);
    }
}