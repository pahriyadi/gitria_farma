<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Pengiriman - <?= esc($sale->invoice_no) ?></title>
    <style>
        @page { size: A4; margin: 12mm 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.4; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 800; color: #0f172a; text-transform: uppercase; margin: 0; }
        .info-table { width: 100%; margin-bottom: 14px; }
        .info-table td { vertical-align: top; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 11.5px; }
        .table-items th { background: #f1f5f9; color: #334155; padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; font-weight: 700; }
        .table-items td { padding: 6px 8px; border: 1px solid #cbd5e1; }
        .sig-table { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .sig-table td { text-align: center; vertical-align: bottom; width: 33.33%; height: 80px; }
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
            <strong>Surat Jalan Pengiriman: DO-<?= esc($sale->invoice_no) ?></strong>
        </div>
        <div>
            <button onclick="window.print()" style="background: #0d9488; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 8px;">
                &#128438; Cetak Surat Jalan
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
                <h2 style="margin: 0; color: #0d9488; font-size: 18px; font-weight: 800;">
                    <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER & DISTRIBUSI')) ?>
                </h2>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    <strong>Logistik &amp; Gudang Distribusi Farmasi</strong><br>
                    <?= esc(clinic_setting('clinic_address', 'Jl. Trans Sulawesi, Indonesia')) ?><br>
                    Telp: <?= esc(clinic_setting('clinic_phone', '0811-xxxx-xxxx')) ?>
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div class="title">SURAT JALAN</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    DELIVERY ORDER (DO)
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                    DO-<?= esc($sale->invoice_no) ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- DESTINATION & DELIVERY INFO -->
    <table class="info-table">
        <tr>
            <td style="width: 55%; padding-right: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px;">
                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">TUJUAN PENGIRIMAN:</span>
                    <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 2px;"><?= esc($sale->customer_name) ?></div>
                    <?php if (!empty($sale->customer_company)): ?>
                        <div style="font-weight: 600; color: #334155;"><?= esc($sale->customer_company) ?></div>
                    <?php endif; ?>
                    <div style="font-size: 11px; color: #475569; margin-top: 3px;">
                        Alamat Kirim: <?= esc($sale->customer_address ?: '-') ?><br>
                        Penerima / Telp: <?= esc($sale->customer_phone ?: '-') ?>
                    </div>
                </div>
            </td>

            <td style="width: 45%;">
                <table style="width: 100%; font-size: 11.5px;">
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Tanggal Kirim:</td>
                        <td style="text-align: right; font-weight: 700;"><?= date('d F Y', strtotime($sale->sale_date)) ?></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Ref. No. Faktur:</td>
                        <td style="text-align: right; font-weight: 700; color: #4f46e5;"><?= esc($sale->invoice_no) ?></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2px 0;">Status Pengiriman:</td>
                        <td style="text-align: right; font-weight: 700; color: #16a34a;">LENGKAP &amp; BAIK</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; margin-bottom: 8px; color: #334155;">
        Mohon diterima barang-barang berikut ini dalam keadaan baik dan tersegel:
    </div>

    <!-- ITEMS TABLE -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 50%;">Nama Obat &amp; Deskripsi Barang</th>
                <th style="width: 20%;">No. Batch &amp; Exp</th>
                <th style="width: 10%; text-align: center;">Satuan</th>
                <th style="width: 15%; text-align: center;">Qty Dikirim</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $idx => $it): ?>
                <tr>
                    <td style="text-align: center;"><?= $idx + 1 ?></td>
                    <td>
                        <strong><?= esc($it->med_name) ?></strong>
                        <div style="font-size: 10px; color: #64748b;">Kode: <?= esc($it->med_code) ?></div>
                    </td>
                    <td>
                        <span><?= esc($it->batch_no ?: '-') ?></span>
                        <div style="font-size: 10px; color: #64748b;"><?= $it->expired_date ? ('Exp: ' . date('d/m/Y', strtotime($it->expired_date))) : '' ?></div>
                    </td>
                    <td style="text-align: center;"><?= esc($it->unit) ?></td>
                    <td style="text-align: center; font-weight: 800; font-size: 13px; color: #0f172a;"><?= $it->qty ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="font-size: 10.5px; color: #64748b; font-style: italic;">
        * Barang yang sudah diterima dan ditandatangani dianggap telah sesuai dengan pesanan dan dalam kondisi baik.
    </div>

    <!-- SIGNATURES -->
    <table class="sig-table">
        <tr>
            <td>
                Penerima Barang,
                <br><br><br>
                <strong>( ..................................... )</strong><br>
                <small style="color: #64748b;">Nama &amp; Cap Toko</small>
            </td>
            <td>
                Pengemudi / Ekspedisi,
                <br><br><br>
                <strong>( ..................................... )</strong><br>
                <small style="color: #64748b;">Supir / Kurir</small>
            </td>
            <td>
                Pengirim,<br>
                <strong>Gudang Distributor</strong>
                <br><br><br>
                <strong>( <?= esc($sale->cashier_name ?: 'Petugas Gudang') ?> )</strong><br>
                <small style="color: #64748b;">Staff Logistik</small>
            </td>
        </tr>
    </table>

</body>
</html>
