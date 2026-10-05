<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-file-invoice-dollar text-warning mr-2"></i> Monitoring Piutang &amp; Pelunasan
                </h4>
                <small class="text-muted">Manajemen umur piutang (Aging Schedule), jatuh tempo pelanggan, dan pencatatan pembayaran cicilan/lunas</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-cart-plus mr-1"></i> Buat Faktur Baru
                    </a>
                    <a href="<?= base_url('distributor/pelanggan') ?>" class="btn btn-outline-info btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-users mr-1"></i> Data Pelanggan
                    </a>
                    <a href="<?= base_url('distributor/laporan?tab=piutang') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-chart-pie mr-1"></i> Laporan Piutang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- FILTER & AGING BUTTONS -->
        <div class="card card-outline card-warning shadow-none border mb-3">
            <div class="card-body p-3">
                <form method="get" action="<?= base_url('distributor/piutang') ?>">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">PELANGGAN</label>
                            <select class="form-control form-control-sm" name="customer_id" onchange="this.form.submit()">
                                <option value="">-- Semua Pelanggan --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c->id ?>" <?= $customerId == $c->id ? 'selected' : '' ?>>
                                        <?= esc($c->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">STATUS PEMBAYARAN</label>
                            <select class="form-control form-control-sm" name="status" onchange="this.form.submit()">
                                <option value="">-- Belum Lunas &amp; Sebagian --</option>
                                <option value="unpaid" <?= $status === 'unpaid' ? 'selected' : '' ?>>Belum Bayar Sama Sekali</option>
                                <option value="partial" <?= $status === 'partial' ? 'selected' : '' ?>>Bayar Sebagian (Cicil)</option>
                                <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Sudah Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">KATEGORI UMUR PIUTANG (AGING)</label>
                            <div class="btn-group btn-group-sm btn-block">
                                <a href="<?= base_url('distributor/piutang') ?>" class="btn <?= empty($aging) ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">Semua</a>
                                <a href="<?= base_url('distributor/piutang?aging=0-30') ?>" class="btn <?= $aging === '0-30' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">0-30 Hari</a>
                                <a href="<?= base_url('distributor/piutang?aging=31-60') ?>" class="btn <?= $aging === '31-60' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">31-60 Hari</a>
                                <a href="<?= base_url('distributor/piutang?aging=>90') ?>" class="btn <?= $aging === '>90' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">&gt;90 Hari</a>
                                <a href="<?= base_url('distributor/piutang?aging=overdue') ?>" class="btn <?= $aging === 'overdue' ? 'btn-danger font-weight-bold' : 'btn-outline-danger' ?>"><i class="fas fa-triangle-exclamation mr-1"></i>Lewat Tempo</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- RECEIVABLES TABLE CARD -->
        <div class="card card-outline card-warning shadow-none border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>No. Faktur</th>
                                <th>Pelanggan</th>
                                <th>Tanggal Faktur</th>
                                <th>Jatuh Tempo</th>
                                <th>Umur Piutang</th>
                                <th class="text-right">Total Tagihan</th>
                                <th class="text-right">Sudah Dibayar</th>
                                <th class="text-right">Sisa Piutang</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($receivables)): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-check-double fa-3x mb-2 text-success opacity-50 d-block"></i>
                                        Tidak ada piutang yang tertunggak pada filter ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($receivables as $r): 
                                    $today = strtotime(date('Y-m-d'));
                                    $invDate = strtotime($r->invoice_date);
                                    $dueDate = strtotime($r->due_date);
                                    $ageDays = max(0, round(($today - $invDate) / (60 * 60 * 24)));
                                    $isOverdue = ($dueDate < $today) && $r->status !== 'paid';
                                ?>
                                    <tr class="<?= $isOverdue ? 'bg-light-danger' : '' ?>">
                                        <td class="font-weight-bold text-indigo">
                                            <?= esc($r->invoice_no) ?>
                                        </td>
                                        <td>
                                            <strong class="text-dark d-block"><?= esc($r->customer_name) ?></strong>
                                            <?php if (!empty($r->company_name)): ?>
                                                <small class="text-muted"><?= esc($r->company_name) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', $invDate) ?></td>
                                        <td>
                                            <span class="<?= $isOverdue ? 'text-danger font-weight-bold' : '' ?>">
                                                <?= date('d/m/Y', $dueDate) ?>
                                                <?php if ($isOverdue): ?>
                                                    <i class="fas fa-triangle-exclamation text-danger ml-1" title="Lewat Jatuh Tempo!"></i>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $ageDays > 60 ? 'badge-danger' : ($ageDays > 30 ? 'badge-warning' : 'badge-light border') ?>">
                                                <?= $ageDays ?> Hari
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            Rp <?= number_format($r->total_receivable, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right text-success font-weight-bold">
                                            Rp <?= number_format($r->total_paid, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-danger">
                                            Rp <?= number_format($r->remaining_balance, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($r->status === 'paid'): ?>
                                                <span class="badge badge-success">Lunas</span>
                                            <?php elseif ($r->status === 'partial'): ?>
                                                <span class="badge badge-info">Sebagian</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($r->remaining_balance > 0): ?>
                                                <button type="button" class="btn btn-xs btn-warning font-weight-bold" onclick='openPaymentModal(<?= json_encode($r) ?>)'>
                                                    <i class="fas fa-hand-holding-dollar mr-1"></i> Bayar
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-xs btn-outline-info" onclick="viewPaymentLogs(<?= $r->id ?>, '<?= esc($r->invoice_no) ?>')" title="Riwayat Pembayaran">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL PEMBAYARAN PIUTANG -->
<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="formPayReceivable">
                <input type="hidden" id="pay_receivable_id" value="">
                
                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-dollar mr-1"></i> Form Pembayaran / Pelunasan Piutang</h6>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="p-2 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted">No. Faktur:</span>
                            <strong id="modal_pay_inv" class="text-indigo">-</strong>
                        </div>
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted">Pelanggan:</span>
                            <strong id="modal_pay_cust" class="text-dark">-</strong>
                        </div>
                        <div class="d-flex justify-content-between text-xs">
                            <span class="text-muted">Sisa Tagihan Piutang:</span>
                            <strong id="modal_pay_rem" class="text-danger font-weight-bold" style="font-size: 14px;">Rp 0</strong>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold text-muted mb-1">TANGGAL PEMBAYARAN</label>
                        <input type="date" class="form-control form-control-sm" id="pay_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold text-muted mb-1">JUMLAH DIBAYAR (RP) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm font-weight-bold text-success text-right" id="pay_amount" min="1" step="100" required style="font-size: 16px;">
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold text-muted mb-1">METODE PEMBAYARAN</label>
                        <select class="form-control form-control-sm font-weight-bold" id="pay_method">
                            <option value="cash" selected>Kas Tunai Distributor (Akun 1-104)</option>
                            <option value="transfer">Transfer Bank Distributor (Akun 1-114)</option>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold text-muted mb-1">NO. BUKTI / REFERENSI TRANSFER</label>
                        <input type="text" class="form-control form-control-sm" id="pay_ref" placeholder="No. resi bank / no. cek...">
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-muted mb-1">CATATAN PEMBAYARAN</label>
                        <input type="text" class="form-control form-control-sm" id="pay_notes" placeholder="Pelunasan tahap 1 / transfer BCA...">
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning font-weight-bold" id="btnSubmitPayment">
                        <i class="fas fa-check mr-1"></i> Simpan Pembayaran &amp; Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RIWAYAT PEMBAYARAN -->
<div class="modal fade" id="modalPaymentLogs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-indigo text-white py-2" style="background-color: #4f46e5;">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-history mr-1"></i> Riwayat Pembayaran Faktur: <span id="log_inv_title"></span></h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="paymentLogsBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-indigo"></i> Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<script>
let maxRemBalance = 0;

function openPaymentModal(r) {
    document.getElementById('pay_receivable_id').value = r.id;
    document.getElementById('modal_pay_inv').innerText = r.invoice_no;
    document.getElementById('modal_pay_cust').innerText = r.customer_name;
    document.getElementById('modal_pay_rem').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(r.remaining_balance);
    
    maxRemBalance = parseFloat(r.remaining_balance);
    document.getElementById('pay_amount').value = maxRemBalance;
    document.getElementById('pay_amount').max = maxRemBalance;

    $('#modalPayment').modal('show');
}

document.getElementById('formPayReceivable').addEventListener('submit', function(e) {
    e.preventDefault();

    const amt = parseFloat(document.getElementById('pay_amount').value || 0);
    if (amt <= 0) {
        alert('Jumlah pembayaran harus lebih besar dari 0.');
        return;
    }
    if (amt > maxRemBalance) {
        alert('Jumlah pembayaran melebihi sisa tagihan piutang.');
        return;
    }

    const payload = {
        receivable_id: document.getElementById('pay_receivable_id').value,
        amount: amt,
        payment_date: document.getElementById('pay_date').value,
        payment_method: document.getElementById('pay_method').value,
        reference_number: document.getElementById('pay_ref').value,
        notes: document.getElementById('pay_notes').value
    };

    const btn = document.getElementById('btnSubmitPayment');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';

    fetch('<?= base_url('distributor/bayar-piutang') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            alert(res.message);
            window.location.reload();
        } else {
            alert('Error: ' + res.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Simpan Pembayaran &amp; Jurnal';
        }
    })
    .catch(err => {
        alert('Gagal memproses pembayaran: ' + err);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Simpan Pembayaran &amp; Jurnal';
    });
});

function viewPaymentLogs(recId, invNo) {
    document.getElementById('log_inv_title').innerText = invNo;
    $('#modalPaymentLogs').modal('show');
    const body = document.getElementById('paymentLogsBody');
    body.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-indigo"></i> Memuat data...</div>';

    fetch('<?= base_url('distributor/riwayat-pembayaran/') ?>/' + recId)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const pays = res.payments;
            if (pays.length === 0) {
                body.innerHTML = '<div class="text-center py-4 text-muted">Belum ada pembayaran yang dicatat untuk faktur ini.</div>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-bordered table-sm text-sm mb-0"><thead class="bg-light"><tr><th>No. Bukti</th><th>Tanggal</th><th>Metode</th><th class="text-right">Nominal</th><th>Kasir</th></tr></thead><tbody>';
            pays.forEach(p => {
                html += `
                    <tr>
                        <td class="font-weight-bold text-indigo">${p.payment_no}</td>
                        <td>${p.payment_date}</td>
                        <td><span class="badge badge-light border text-uppercase">${p.payment_method}</span></td>
                        <td class="text-right font-weight-bold text-success">Rp ${new Intl.NumberFormat('id-ID').format(p.amount)}</td>
                        <td><small class="text-muted">${p.cashier_name || 'Admin'}</small></td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            body.innerHTML = html;
        } else {
            body.innerHTML = '<div class="alert alert-danger">Gagal memuat riwayat pembayaran.</div>';
        }
    })
    .catch(err => {
        body.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan: ' + err + '</div>';
    });
}
</script>
<?= $this->endSection() ?>
