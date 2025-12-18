<?php include 'views/layouts/admin/header.php'; ?>

<div class="container my-4" x-data="orderFilter()">
    <h3 class="fw-bold mb-3">Manajemen Pesanan</h3>

    <!-- FILTER -->
    <form class="card p-3 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Nama Pelanggan</label>
                <input type="text" name="customer" class="form-control" placeholder="Cari Nama" x-model="customer">
            </div>
            <div class="col-md-2">
                <label>Dari Tanggal</label>
                <input type="date" name="from" class="form-control" x-model="fromDate">
            </div>
            <div class="col-md-2">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" x-model="toDate">
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-select" x-model="status">
                    <option value="">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="shipped">Dikirim</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-dark w-100">Cari</button>
            </div>
        </div>
    </form>

    <!-- TABLE -->
    <div class="card">
        <div class="card-header fw-semibold">Daftar Pesanan</div>
        <div class="card-body">
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredOrders.length === 0">
                        <tr>
                            <td colspan="6" class="text-center p-4">Tidak ada data</td>
                        </tr>
                    </template>

                    <template x-for="order in filteredOrders" :key="order.id">
                        <tr>
                            <td>#<span x-text="order.id"></span></td>

                            <td x-text="formatDate(order.created_at)"></td>

                            <td x-text="order.customer_name"></td>

                            <td>
                                Rp <span x-text="formatPrice(order.total_price)"></span>
                            </td>

                            <td>
                                <span 
                                    class="badge"
                                    :class="statusBadge(order.status)"
                                    x-text="order.status.charAt(0).toUpperCase() + order.status.slice(1)"
                                ></span>
                            </td>

                            <td>
                                <!-- DETAIL -->
                                <a 
                                    :href="'index.php?page=admin_order_detail&id=' + order.id"
                                    class="btn btn-sm btn-info text-light"
                                >
                                    Detail
                                </a>

                                <!-- PROSES -->
                                <button 
                                    x-show="order.status !== 'completed'"
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateStatusModal"
                                    :data-id="order.id"
                                    :data-status="order.status"
                                >
                                    Proses Pesanan
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
<script>
function orderFilter() {
    return {
        customer: '',
        fromDate: '',
        toDate: '',
        status: '',

        orders: <?= json_encode($orders) ?>,

        get filteredOrders() {
            return this.orders.filter(o => {

                // Filter nama
                if (this.customer && 
                    !o.customer_name.toLowerCase().includes(this.customer.toLowerCase())
                ) return false

                // Filter status
                if (this.status && o.status !== this.status) return false

                // Filter tanggal
                const orderDate = new Date(o.created_at)

                if (this.fromDate && orderDate < new Date(this.fromDate)) return false
                if (this.toDate && orderDate > new Date(this.toDate + ' 23:59:59')) return false

                return true
            })
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID')
        },

        formatPrice(price) {
            return Number(price).toLocaleString('id-ID')
        },

        statusBadge(status) {
            return {
                'pending': 'bg-secondary',
                'paid': 'bg-primary',
                'shipped': 'bg-info',
                'completed': 'bg-success',
                'cancelled': 'bg-danger'
            }[status]
        }
    }
}
</script>

</div>

<!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="index.php?page=admin_orders&action=update_status">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- DIISI VIA JS -->
                <input type="hidden" name="order_id" id="modalOrderId">

                <div class="mb-3">
                    <label>Status Pesanan</label>
                    <select name="status" id="modalStatus" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="shipped">Dikirim</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Status</button>
            </div>

        </div>
    </form>
  </div>
</div>


<script>
const updateModal = document.getElementById('updateStatusModal')

updateModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget

    const orderId = button.getAttribute('data-id')
    const status  = button.getAttribute('data-status')

    document.getElementById('modalOrderId').value = orderId
    document.getElementById('modalStatus').value  = status
})
</script>

<?php include 'views/layouts/admin/footer.php'; ?>
