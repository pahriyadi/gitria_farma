# 📊 STANDAR ATURAN JURNAL & OTOMASI AKUNTANSI (JOURNAL ENGINE)
## SAWAMAWA MEDICAL CENTER ERP

Dokumen aturan ini merupakan memori permanen dan standar arsitektur wajib perancangan, formula pembagian hasil, serta penjurnalan otomatis transaksi finansial pada seluruh modul SIM-Klinik Sawamawa Medical Center.

---

### 1. Prinsip Mutlak Penjurnalan (Invariants)
1. **Double-Entry Balance ($Debit = Kredit$)**: Setiap entri jurnal umum yang dibukukan wajib seimbang secara mutlak ($Total Debit = Total Kredit$).
2. **Auto Penny-Rounding Balancing**: Jika pembagian persentase menghasilkan selisih pecahan rupiah/desimal, selisih tersebut harus dialokasikan ke akun penyeimbang utama (`DYNAMIC_OBAT_REMAINDER` atau pos obat/fasilitas) agar selisihnya tepat 0.00.
3. **Multi-Payment Method Resolver**:
   - `tunai` / `cash` -> Akun `1111` (Kas Kasir / Kas Tunai).
   - `qris` -> Akun `1121` (Kas Digital QRIS).
   - `transfer` / `debit` / `edc` / `bank` -> Akun `112` (Bank Rekening Operasional).
4. **Penomoran Jurnal Unik**: Format wajib `JV-YYYYMMDD-XXXX` berurutan per hari.

---

### 2. Struktur Tabel Basis Data Terkait
- `accounts`: Bagan akun (Chart of Accounts - COA) dengan kode akun, nama, tipe, dan normal balance (`debit`/`credit`).
- `journal_categories`: Master template transaksi (`RAWAT_JALAN_POLI`, `PENJUALAN_OBAT_RESEP`, `PENJUALAN_OBAT_BEBAS`, `KONSULTASI_ONLINE`, `RESTO_SALE`).
- `journal_category_rules`: Baris aturan bagi hasil per akun, posisi (`debit`/`credit`), kalkulasi (`percentage`/`fixed_amount`/`dynamic_fee`), nilai, dan `formula_code`.
- `journal_entries` & `journal_entry_details`: Header dan detail jurnal umum yang dibukukan.

---

### 3. Logika Formula & Template Bawaan

#### A. Pelayanan Rawat Jalan & Tindakan Medis (`RAWAT_JALAN_POLI`)
- **Debit (100%)**: Kas Tunai / Bank / QRIS (`1111` / `112`).
- **Kredit Tahap 1 (Pengurang Utama)**:
  - Dokter (`424` / `241`): Formula `DOCTOR_FEE_PCT` (default 66.67% atau Rp 100.000).
  - Utang Fee Karyawan (`242`): Formula `EMPLOYEE_FEE` (Rp 10.000 / proporsional).
- **Kredit Tahap 2 (Sisa Bersih Fasilitas / `REMAINDER_TIER`)**:
  - Fasilitas Klinik (`425`): 67.24% dari sisa.
  - BMHP (`415`): 19.025% dari sisa.
  - Administrasi (`421`): 10.9875% dari sisa.
  - Konseling Farmasi (`422`): 2.7475% dari sisa (`DYNAMIC_OBAT_REMAINDER`).

#### B. Penjualan Obat Resep (`PENJUALAN_OBAT_RESEP`)
- **Debit (100%)**: Kas Apotek.
- **Kredit**:
  - Obat (`511`): Formula `DYNAMIC_OBAT_REMAINDER` (44.00% dasar atau $49\% - \text{Fee Dokter}$).
  - Utang Pajak PPN (`231`): 11.00%.
  - Penunjang (`512`): 9.00%.
  - Jasa Obat Resep (`513`): 7.00%.
  - Administrasi Apotek (`514`): 24.00%.
  - Utang Fee Dokter Resep (`241`): Formula `DOCTOR_FEE_PCT` (default 5.00%).

#### C. Penjualan Obat Bebas / OTC (`PENJUALAN_OBAT_BEBAS`)
- **Debit (100%)**: Kas Apotek.
- **Kredit**: Obat (`511`: 49%), Pajak (`231`: 11%), Penunjang (`512`: 9%), Jasa Bebas (`513`: 7%), Administrasi (`514`: 24%). *(Total Kredit = 100%)*

#### D. Konsultasi Online & Telemedisin (`KONSULTASI_ONLINE`)
- **Debit (100%)**: Kas Apotek / Bank / QRIS (`1111` / `1121` / `112`).
- **Kredit Tahap 1 (Pengurang Kas)**:
  - **Pendapatan Jasa Dokter (`424` / `4-101`)**: Nilai nominal tetap **Rp 20.000**.
  - **Utang Fee Dokter (`241` / `2-102`)**: Diisi sendiri / manual oleh kasir saat transaksi (`doctor_fee_nominal`).
- **Kredit Tahap 2 (Basis Sisa Persentase)**:
  - $\text{Basis Sisa} = \text{Kas} - (\text{Utang Fee Dokter} + \text{Rp } 20.000)$.
  - 5 Pos persentase dihitung dari $\text{Basis Sisa}$:
    - Obat (`511`): 49.00% (berfungsi sebagai akun penyeimbang selisih pembulatan).
    - Utang Pajak PPN (`231`): 11.00%.
    - Penunjang (`512`): 9.00%.
    - Jasa Obat Resep (`513`): 7.00%.
    - Administrasi Apotek (`514`): 24.00%.
    *(Total Kredit = 100% dari Nilai Kas, terjamin Double-Entry Balance mutlak).*

#### E. Resto POS & Dapur Gizi (`RESTO_SALE`)
- Debit Kas Resto vs Kredit Pendapatan Resto & Dapur Gizi (`411` / `4-103`).

---

### 4. Integrasi Pemicu Lintas Modul (Cross-Module Triggers)
1. **Kasir Utama (`Keuangan::bayarBilling()`)**:
   - Pelunasan billing tagihan gabungan memecah biaya tindakan medis ke `postClinicSplitJournal()`, biaya obat ke `postPharmacySplitJournal()`, dan biaya makan ke `postJournal('RESTO_SALE')`.
2. **Farmasi Apotek (`PharmacyService.php`)**:
   - Transaksi kasir apotek memanggil `postPharmacySplitJournal()` secara real-time.
3. **Resto POS (`RestoService.php`)**:
   - Pesanan resto terbayar langsung memanggil `postJournal('RESTO_SALE')`.
4. **Pengeluaran Kasir (`Keuangan::simpanPengeluaran()`)**:
   - Mencatat pengeluaran petty cash dengan memanggil `postJournal('EXPENSE')`.
5. **Penyusutan Aset (`MonthlyDepreciation.php`)**:
   - Cron akhir bulan menjurnal Debit Beban Penyusutan vs Kredit Akumulasi Penyusutan Aset.
6. **Rekonsiliasi Pasif (`syncUnpostedTransactions()`)**:
   - Pemindaian otomatis saat membuka menu akuntansi untuk memastikan 0% transaksi terlewat.
