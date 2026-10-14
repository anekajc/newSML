# Catatan Update DB - Accounting (DBSMLBARU)

Menu Accounting newSML disamakan dengan newProgramAJC (2026-10-14). Controller dan view `Accounting` sudah identik dengan newProgramAJC. Yang berbeda adalah isi beberapa SP/view/trigger di database.

Keputusan user: SP accounting ikut DBSMLNEW, **asal tidak memengaruhi purchasing dan marketing**.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 14 | `14_sp_accounting_ikut_ajc.sql` | ALTER `sp_TransaksiKasBankDPH`, `CetakTTDPH` (isi DBSMLNEW) dan `SP_IsiNobukti` (isi DBSMLBARU + cabang `TDP`). | Sudah dijalankan 2026-10-14 |

Script umum terkait: `umum/15_sp_flmenuweb1_dbflmenuweb.sql` (hak akses Berkas > Pemakai).

## Yang disalin

- **sp_TransaksiKasBankDPH**: Kas/Bank masuk dengan pelunasan piutang (Plok=1) sekarang mencatat pelunasan ke DBHUTPIUT. Sebelumnya hanya Kas/Bank keluar yang mencatat, jadi piutang tidak berkurang. SP ini hanya dipanggil menu Kas, Bank, Giro Diterima, dan Giro Dibuka.
- **CetakTTDPH**: membaca `dbTempTTDPH`, tabel yang diisi menu Cetak Pengajuan DPH. Versi lama membaca `dbTempTTInvc` milik Tanda Terima Invoice marketing.
- **SP_IsiNobukti**: hanya ditambah cabang `TDP` (Penerimaan DPP). Halaman Penerimaan DPP meminta nomor TDP, tapi cabang itu tidak ada, jadi nomor urutnya tidak bertambah. Semua cabang lain, termasuk `TTI` dan kode purchasing/marketing, tetap versi DBSMLBARU.

## Yang sengaja TIDAK disalin dari DBSMLNEW

| Objek | Alasan |
|---|---|
| `TRI_DEL_DBTRANSAKSI` | Versi DBSMLNEW salah. Trigger tambah `TRI_ADD_DBTRANSAKSI` (isinya sama di kedua DB) memasukkan AKM+ ke SK dan AKM- ke SD. Versi DBSMLBARU mengurangi pasangan yang sama, sedangkan versi DBSMLNEW mengurangi pasangan yang terbalik, jadi saldo akumulasi penyusutan aktiva bisa rusak. Trigger ini jalan di setiap posting jurnal, termasuk dari purchasing dan marketing. |
| `vwHutPiut` | DBSMLNEW mengambil No PO dari `dbInvoicePL.PONo`, sedangkan invoice dari marketing newSML tidak mengisi kolom itu (contoh SML/INVC/01011/0926). No PO di laporan piutang akan kosong. DBSMLBARU mengambilnya dari `DBSO.NoPesanan`. View ini juga dipakai Kredit Note dan Debet Note. |
| `sp_CariHutangJT` | Versi DBSMLBARU lebih baru: retur pembelian (RPB) dihitung minus di DPH/DPP. Versi DBSMLNEW belum punya ini. |
| `SP_ProsesKursBank` | Versi DBSMLNEW berisi baris uji `select @bulan=6,@tahun=2026`, yang menimpa periode pilihan user di Tutup Buku > Selisih Kurs. |
