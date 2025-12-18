<?php include 'views/layouts/header.php'; ?>

<div class="container my-5" style="max-width: 900px;">
    <h2 class="fw-bold mb-4">My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-secondary text-center">
            Kamu belum memiliki pesanan.
        </div>
    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <!-- HEADER ORDER -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>Invoice:</strong> <?= $order['invoice_number'] ?><br>
                            <small class="text-muted">
                                <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                            </small>
                        </div>

                        <?php
                        $statusColor = match ($order['status']) {
                            'pending'   => 'warning',
                            'paid'      => 'primary',
                            'shipped'   => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default     => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?= $statusColor ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>

                    <!-- ITEM LIST -->
                    <?php if (!empty($orderItems[$order['id']])): ?>
                        <?php foreach ($orderItems[$order['id']] as $item): ?>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="uploads/<?= $item['image'] ?>" style="width: 100px" class="rounded">

                                <div class="flex-grow-1">
                                    <strong><?= $item['name'] ?></strong><br>
                                    <small>
                                        <?= $item['quantity'] ?> × Rp <?= number_format($item['price']) ?>
                                    </small>
                                </div>

                                <div class="fw-semibold">
                                    Rp <?= number_format($item['quantity'] * $item['price']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr>

                    <!-- TOTAL -->
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>Rp <?= number_format($order['total_price']) ?></span>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php include 'views/layouts/footer.php'; ?>
