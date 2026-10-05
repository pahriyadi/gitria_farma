<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan Grosir - <?= esc($sale->invoice_no) ?></title>
    <style>
        @page { size: A4; margin: 12mm 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.4; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 800; color: #1e1b4b; text-transform: uppercase; margin: 0; }
        .badge-type { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #e0e7ff; color: #3730a3; }
        .info-table { width: 100%; margin-bottom: 14px; }
        .info-table td { vertical-align: top; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 11.5px; }
        .table-items th { background: #f1f5f9; color: #334155; padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; font-weight: 700; }
        .table-items td { padding: 6px 8px; border: 1px solid #cbd5e1; }
        .table-totals { width: 100%; border-collapse: collapse; }
        .table-totals td { padding: 3px 8px; }
        .total-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; }
        .sig-table { width: 100%; margin-top: 25px; page-break-inside: avoid; }
        .sig-table td { text-align: center; vertical-align: bottom; width: 33.33%; height: 75px; }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
        }
    </style>
</head>
<body>

    <!-- NO-PRINT BUTTONS -->
    <div class="no-print" style="background: #1e293b; color: #fff; padding: 10px 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-radius: 6px;">
        <div>
            <strong>Faktur Penjualan Distributor: <?= esc($sale->invoice_no) ?></strong>
        </div>
        <div>
            <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 8px;">
                &#128438; Cetak Faktur (Print)
            </button>
            <button onclick="window.close()" style="background: #64748b; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>

    <!-- HEADER KOP SURAT -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <h2 style="margin: 0; color: #0d9488; font-size: 18px; font-weight: 800; letter-spacing: 0.5px;">
                    <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER & DISTRIBUSI')) ?>
                </h2>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    <strong>Unit Bisnis Distributor &amp; PBF Farmasi Grosir</strong><br>
                    <?= esc(clinic_setting('clinic_address', 'Jl. Trans Sulawesi, Indonesia')) ?><br>
                    Telp: <?= esc(clinic_setting('clinic_phone', '0811-xxxx-xxxx')) ?> | Email: distributor@sawamawamedicalcenter.id
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div class="title">FAKTUR PENJUALAN</div>
                <div style="font-size: 14px; font-weight: 700; color: #4f46e5; margin-top: 2px;">
                    <?= esc($sale->invoice_no) ?>
                </div>
                <div style="margin-top: 4px;">
                    <span class="badge-type"><?= $sale->payment_type === 'credit' ? ('KREDIT TEMPO (' . $sale->payment_terms_days . ' HARI)') : 'TUNAI / CASH' ?></span>
                </div>
            </td>
        </tr>
    </table>

    <!-- INVOICE INFO & CUSTOMER DETAILS -->
    <table class="info-table">
        <tr>
            <!-- CUSTOMER INFO -->
            <td style="width: 55%; padding-right: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px;">
                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">DITAGIHKAN KEPADA (PELANGGAN):</span>
                    <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 2px;"><?= esc($sale->customer_name) ?></div>
                    <?php if (!empty($sale->customer_company)): ?>
                        <div style="font-weight: 600; color: #334155;"><?= esc($sale->customer_company) ?></div>
                    <?php endif; ?>
                    <div style="font-size: 11px; color: #475569; margin-top: 3px;">
                        Alamat: <?= esc($sale->customer_address ?: '-') ?><br>
                        Telp / WA: <?= esc($sale->customer_phone ?: '-') ?>
                        <?php if (!empty($sale->customer_npwp)): ?>
                            | NPWP: <?= esc($sale->customer_npwp) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </td>

            <!-- DATES & SUMMARY -->
            <td style="width: 45%;">
                <table style="width: 100%; font-size: 11.5px;">
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Tanggal Faktur:</td>
                        <td style="text-align: right; font-weight: 700;"><?= date('d F Y', strtotime($sale->sale_date)) ?></td>
                    </tr>
                    <?php if ($sale->payment_type === 'credit' && !empty($sale->due_date)): ?>
                    <tr>
                        <td style="color: #dc2626; font-weight: 700; padding: 2px 0;">Jatuh Tempo:</td>
                        <td style="text-align: right; font-weight: 800; color: #dc2626;"><?= date('d F Y', strtotime($sale->due_date)) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Status Pembayaran:</td>
                        <td style="text-align: right; font-weight: 700; color: <?= $sale->payment_status === 'paid' ? '#16a34a' : '#ea580c' ?>;">
                            <?= strtoupper($sale->payment_status === 'paid' ? 'Lunas' : ($sale->payment_status === 'partial' ? 'Sebagian' : 'Belum Lunas')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Petugas / Kasir:</td>
                        <td style="text-align: right;"><?= esc($sale->cashier_name ?: 'Admin Distributor') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 40%;">Nama Obat &amp; Deskripsi</th>
                <th style="width: 15%;">No. Batch &amp; Exp</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Harga Satuan</th>
                <th style="width: 15%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $idx => $it): ?>
                <tr>
                    <td style="text-align: center;"><?= $idx + 1 ?></td>
                    <td>
                        <strong><?= esc($it->med_name) ?></strong>
                        <div style="font-size: 10px; color: #64748b;">Kode: <?= esc($it->med_code) ?> | Satuan: <?= esc($it->unit) ?></div>
                    </td>
                    <td>
                        <span><?= esc($it->batch_no ?: '-') ?></span>
                        <div style="font-size: 10px; color: #64748b;"><?= $it->expired_date ? ('Exp: ' . date('d/m/Y', strtotime($it->expired_date))) : '' ?></div>
                    </td>
                    <td style="text-align: center; font-weight: 700;"><?= $it->qty ?></td>
                    <td style="text-align: right;">Rp <?= number_format($it->selling_price, 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: 700;">Rp <?= number_format($it->subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TOTALS & BANK ACCOUNTS -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 11px;">
                    <strong>Informasi Pembayaran Bank:</strong><br>
                    Bank: <strong>Bank Central Asia (BCA)</strong><br>
                    No. Rekening: <strong>7925-888-999</strong><br>
                    Atas Nama: <strong>GITRIA FARMA / SAWAMAWA ERP</strong><br>
                    <small style="color: #64748b; display: block; margin-top: 4px;">* Harap mencantumkan No. Faktur pada berita transfer.</small>
                </div>

                <?php if (!empty($sale->notes)): ?>
                    <div style="margin-top: 8px; font-size: 11px; color: #475569;">
                        <strong>Catatan:</strong> <?= esc($sale->notes) ?>
                    </div>
                <?php endif; ?>
            </td>

            <td style="width: 45%; vertical-align: top;">
                <table class="table-totals" style="font-size: 12px;">
                    <tr>
                        <td style="color: #64748b;">Subtotal:</td>
                        <td style="text-align: right; font-weight: 700;">Rp <?= number_format($sale->subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php if ($sale->discount_amount > 0): ?>
                    <tr>
                        <td style="color: #dc2626;">Diskon Faktur:</td>
                        <td style="text-align: right; color: #dc2626; font-weight: 700;">(Rp <?= number_format($sale->discount_amount, 0, ',', '.') ?>)</td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($sale->tax_amount > 0): ?>
                    <tr>
                        <td style="color: #64748b;">PPN (<?= $sale->tax_percent ?>%):</td>
                        <td style="text-align: right; font-weight: 700;">Rp <?= number_format($sale->tax_amount, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr style="border-top: 2px solid #0f172a; font-size: 14px;">
                        <td style="font-weight: 800; color: #0f172a; padding-top: 6px;">TOTAL TAGIHAN:</td>
                        <td style="text-align: right; font-weight: 800; color: #4f46e5; padding-top: 6px;">Rp <?= number_format($sale->total_amount, 0, ',', '.') ?></td>
                    </tr>
                    <?php if ($sale->payment_type === 'credit'): ?>
                    <tr>
                        <td style="color: #16a34a; font-weight: 600;">Sudah Dibayar:</td>
                        <td style="text-align: right; color: #16a34a; font-weight: 700;">Rp <?= number_format($sale->paid_amount, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="color: #dc2626; font-weight: 800;">Sisa Tagihan (Piutang):</td>
                        <td style="text-align: right; color: #dc2626; font-weight: 800;">Rp <?= number_format($sale->remaining_amount, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

    <!-- SIGNATURES -->
    <table class="sig-table">
        <tr>
            <td>
                Penerima / Pelanggan,
                <br><br><br>
                <strong>( <?= esc($sale->customer_name) ?> )</strong>
            </td>
            <td>
                Bagian Gudang &amp; Pengiriman,
                <br><br><br>
                <strong>( ..................................... )</strong>
            </td>
            <td>
                Hormat Kami,<br>
                <strong>Distributor Farmasi</strong>
                <br><br><br>
                <strong>( <?= esc($sale->cashier_name ?: 'Admin Distributor') ?> )</strong>
            </td>
        </tr>
    </table>

</body>
</html>
