<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan - <?= esc($order->order_no) ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 8px 12px;
            width: 76mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header h3 {
            margin: 0 0 2px 0;
            font-size: 15px;
            font-weight: bold;
        }
        .header p {
            margin: 1px 0;
            font-size: 10px;
        }

        .meta-info {
            font-size: 11px;
            margin-bottom: 6px;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 6px;
        }
        table.items-table th {
            border-bottom: 1px solid #000;
            padding: 3px 0;
            text-align: left;
        }
        table.items-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .totals-section {
            border-top: 1px dashed #000;
            padding-top: 4px;
            font-size: 11px;
            margin-bottom: 6px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 4px 0;
        }

        .alert-diet {
            border: 1px solid #000;
            padding: 4px;
            margin: 6px 0;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-top: 6px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="text-align: center; margin-bottom: 10px; padding: 6px; background: #eee;">
        <button onclick="window.print()" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">🖨️ Cetak Ulang</button>
        <button onclick="window.close()" style="padding: 4px 10px; margin-left: 5px; cursor: pointer;">Tutup</button>
    </div>

    <!-- Header -->
    <div class="header">
        <h3>SAWAMAWA HEALTHY CORNER</h3>
        <p>Resto Sehat, Terapi Nutrisi & Dermal Care</p>
        <p>Jl. Mawar Merah No. 12, Sumbawa / Jakarta</p>
        <p>Telp: (021) 8920-1928</p>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <div class="meta-row">
            <span>No. Pesanan:</span>
            <span class="font-weight-bold"><?= esc($order->order_no) ?></span>
        </div>
        <div class="meta-row">
            <span>Waktu:</span>
            <span><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></span>
        </div>
        <div class="meta-row">
            <span>Tipe:</span>
            <span class="font-weight-bold text-uppercase"><?= $order->order_type === 'diet_pasien' ? 'DIET PASIEN POLI' : 'WALK-IN' ?></span>
        </div>
        <div class="meta-row">
            <span>Pelanggan:</span>
            <span class="font-weight-bold"><?= esc($order->customer_name ?: 'Walk-In') ?></span>
        </div>
        <?php if ($order->visit_id && $order->polyclinic_name): ?>
            <div class="meta-row">
                <span>Rujukan:</span>
                <span>[<?= esc($order->no_rm) ?>] <?= esc($order->polyclinic_name) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($order->cashier_name): ?>
            <div class="meta-row">
                <span>Kasir:</span>
                <span><?= esc($order->cashier_name) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Diet / Allergy Alert -->
    <?php if (!empty($order->diet_instructions)): ?>
        <div class="alert-diet">
            <strong>* CATATAN DIET KHUSUS GIZI:</strong><br>
            <?= esc($order->diet_instructions) ?>
        </div>
    <?php endif; ?>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Menu / Produk</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 35%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $d): ?>
                <tr>
                    <td>
                        <?= esc($d->menu_name) ?>
                        <?php if ($d->menu_code): ?>
                            <br><small style="color: #444;">(<?= esc($d->menu_code) ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $d->qty ?></td>
                    <td class="text-right">Rp <?= number_format($d->price * $d->qty, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <div class="totals-row">
            <span>Total Tagihan:</span>
            <span>Rp <?= number_format($order->total_amount, 0, ',', '.') ?></span>
        </div>
        <?php if ($order->discount_amount > 0): ?>
            <div class="totals-row">
                <span>Diskon / Potongan:</span>
                <span>- Rp <?= number_format($order->discount_amount, 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
        <div class="totals-row grand-total">
            <span>TOTAL AKHIR:</span>
            <span>Rp <?= number_format($order->grand_total, 0, ',', '.') ?></span>
        </div>
        <div class="totals-row">
            <span>Status Pembayaran:</span>
            <span class="font-weight-bold text-uppercase">
                <?php 
                if ($order->payment_status === 'paid') echo 'LUNAS (' . strtoupper($order->payment_method ?: 'CASH') . ')';
                elseif ($order->payment_status === 'billed_to_clinic') echo 'BILLING KLINIK TERPADU';
                else echo 'BELUM LUNAS (OPEN)';
                ?>
            </span>
        </div>
        <?php if ($order->payment_status === 'paid' && $order->paid_amount > 0): ?>
            <div class="totals-row">
                <span>Uang Diterima:</span>
                <span>Rp <?= number_format($order->paid_amount, 0, ',', '.') ?></span>
            </div>
            <div class="totals-row">
                <span>Kembalian:</span>
                <span>Rp <?= number_format($order->change_amount, 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Terima Kasih Atas Kunjungan Anda</strong></p>
        <p><em>"Nutrisi Sehat & Seimbang, Kunci Tubuh Bugar Sepanjang Hari"</em></p>
        <p>Struk ini merupakan bukti pembayaran sah.</p>
    </div>

</body>
</html>
