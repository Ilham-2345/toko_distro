<?php 
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

if ($action == 'print' && isset($_GET['id'])) {
    $userId = $_SESSION['user']['id'] ?? null;
    $orderId = $_GET['id'];

    // REUSE QUERY YANG SAMA
    $stmt = $pdo->prepare("
        SELECT o.*, u.name, u.phone, u.address
        FROM orders o
        JOIN users u ON u.id = o.user_id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order tidak ditemukan!";
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            p.name AS product_name,
            p.image,
            s.name AS size_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN sizes s ON oi.size_id = s.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // GENERATE PDF
    ob_start();
    include 'views/user/invoice_print.php';
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream("invoice-" . $order['invoice_number'] . ".pdf", [
        "Attachment" => false
    ]);

    exit;
}