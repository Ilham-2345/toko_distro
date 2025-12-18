<?php include 'views/layouts/admin/header.php'; ?>
<div class="container my-4" x-data="offlineOrder()">
    <h3 class="fw-bold mb-4">Pesanan Offline (Kasir)</h3>
    <!-- FORM ORDER OFFLINE -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">Tambah Pesanan Offline</div>
        <div class="card-body">
            <form method="POST" action="index.php?page=admin_orders_offline&action=store">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label>Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option value="CASH">Cash</option>
                            <option value="DEBIT">Debit</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <hr>
                <!-- ITEM -->
                <h6 class="fw-bold mb-2">Item Pesanan</h6>
                <template x-for="(item, index) in items" :key="index">
                    <div class="row g-2 mb-2">

                        <div class="col-md-4">
                            <select class="form-select" name="product_id[]" x-model="item.product_id" @change="fetchStock(item)">
                                <option value="">Pilih Produk</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" name="size_id[]" x-model="item.size_id" @change="fetchStock(item)">
                                <option value="">Size</option>
                                <?php foreach ($sizes as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="number" 
                                name="qty[]" 
                                class="form-control" 
                                min="1"
                                :max="item.stock"
                                x-model.number="item.qty"
                                @input="validateQty(item)"
                                placeholder="Qty">

                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger" @click="removeItem(index)">×</button>
                        </div>

                    </div>
                </template>
                <button type="button" class="btn btn-outline-dark btn-sm mt-2" @click="addItem()">
                    + Tambah Item
                </button>
                <hr>
                <button class="btn btn-dark mt-3">Simpan Pesanan Offline</button>
            </form>
        </div>
    </div>

    <!-- TABLE ORDER OFFLINE -->
    <div class="card">
        <div class="card-header fw-semibold">Daftar Pesanan Offline</div>
        <div class="card-body">

            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center p-4">Belum ada pesanan offline</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                        <td>Rp <?= number_format($o['total_price']) ?></td>
                        <td>
                            <span class="badge bg-success"><?= ucfirst($o['status']) ?></span>
                        </td>
                        <td>
                            <button 
                                class="btn btn-info btn-sm text-light"
                                @click="openDetail(<?= $o['id'] ?>)">
                                Detail
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
            <!-- MODAL DETAIL OFFLINE -->
            <div class="modal fade" id="offlineDetailModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Detail Pesanan Offline
                            </h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <template x-if="detailOrder">
                                <div>
                                    <p><strong>Invoice:</strong> <span x-text="detailOrder.invoice_number"></span></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-success" x-text="detailOrder.status"></span>
                                    </p>
                                    <hr>
                                    <table class="table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Size</th>
                                                <th>Qty</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="item in detailItems" :key="item.id">
                                                <tr>
                                                    <td x-text="item.product_name"></td>
                                                    <td x-text="item.size_name"></td>
                                                    <td x-text="item.quantity"></td>
                                                    <td>Rp <span x-text="formatPrice(item.price)"></span></td>
                                                    <td>
                                                        Rp <span x-text="formatPrice(item.price * item.quantity)"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <div class="text-end fw-bold">
                                        Total: Rp <span x-text="formatPrice(detailOrder.total_price)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function offlineOrder() {
    return {
        items: [
            { product_id: '', size_id: '', qty: 1, stock: 0 }
        ],

        detailOrder: null,
        detailItems: [],

        addItem() {
            this.items.push({ product_id: '', size_id: '', qty: 1, stock: 0 })
        },

        removeItem(index) {
            this.items.splice(index, 1)
        },

        validateQty(item) {
            if (item.qty > item.stock) {
                alert('Qty melebihi stok tersedia!')
                item.qty = item.stock
            }
        },

        openDetail(id) {
            fetch(`index.php?page=admin_orders_offline&action=detail&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    this.detailOrder = data.order
                    this.detailItems = data.items

                    new bootstrap.Modal(
                        document.getElementById('offlineDetailModal')
                    ).show()
                })
        },

        formatPrice(val) {
            return Number(val).toLocaleString('id-ID')
        },

        fetchStock(item) {
            if (!item.product_id || !item.size_id) return

            fetch(`index.php?page=admin_orders_offline&action=get_stock&product_id=${item.product_id}&size_id=${item.size_id}`)
                .then(res => res.json())
                .then(data => {
                    item.stock = data.stock
                    if (item.qty > data.stock) {
                        item.qty = data.stock
                    }
                })
        }
    }
}
</script>

<?php include 'views/layouts/admin/footer.php'; ?>
