/* =====================================================================
   31 - Perkiraan per item PO (DBPODET.PERKIRAAN)
   Untuk menu : Purchase Order gabungan (/purchaseorder), form PO Non Stock.
                1 PO bisa berisi banyak barang dengan COA Biaya berbeda, jadi
                perkiraan disimpan per baris detail, bukan hanya di header.
   Isi        : - Tambah kolom DBPODET.PERKIRAAN varchar(25) NULL (kalau belum ada).
                - Isi PERKIRAAN baris PO Non Stock lama (DBPO.pJasa = 1) dari COA
                  Biaya barang (DBBARANG -> dbSubGroup.PerkPers, Master Headgroup).
   Kode       : PONonStockController@spAdd mengisi PERKIRAAN baris baru sesudah
                exec sp_PO. POController@getDetail membaca B.PERKIRAAN, jadi
                skrip ini WAJIB dijalankan sebelum kode baru dipasang (tanpa kolom
                ini, detail PO Stock & PO Non Stock error).
   Tidak diubah: sp_PO, dan nilai DBPO.PERKIRAAN pada PO lama. Kode baru mengirim
                PERKIRAAN header kosong ke sp_PO, jadi PO baru/yang diedit
                header-nya kosong (perkiraan sudah per item).
   Sifat      : hanya menambah kolom & mengisi yang masih kosong. Aman dijalankan
                ulang. Rollback di bawah.
   ===================================================================== */
USE DBSMLBARU;
GO

IF COL_LENGTH('dbo.DBPODET', 'PERKIRAAN') IS NULL
  ALTER TABLE dbo.DBPODET ADD PERKIRAAN varchar(25) NULL;
GO

UPDATE d SET d.PERKIRAAN = s.PerkPers
  FROM dbo.DBPODET d
  JOIN dbo.DBPO h       ON h.NOBUKTI = d.NOBUKTI
  JOIN dbo.DBBARANG b   ON b.KODEBRG = d.KODEBRG
  JOIN dbo.dbSubGroup s ON s.KodeHDGrp = b.KodeHdGrp AND s.KodeSubGrp = b.KODESUBGRP
 WHERE ISNULL(h.pJasa, 0) = 1
   AND ISNULL(d.PERKIRAAN, '') = ''
   AND ISNULL(s.PerkPers, '') <> '';
GO

/* Cek hasil: baris PO Non Stock yang masih belum punya perkiraan
   (biasanya karena COA Biaya barangnya belum diatur di Master Headgroup). */
SELECT d.NOBUKTI, d.URUT, d.KODEBRG, d.PERKIRAAN
  FROM dbo.DBPODET d
  JOIN dbo.DBPO h ON h.NOBUKTI = d.NOBUKTI
 WHERE ISNULL(h.pJasa, 0) = 1 AND ISNULL(d.PERKIRAAN, '') = ''
 ORDER BY d.NOBUKTI, d.URUT;
GO

/* ---------------------------------------------------------------------
   ROLLBACK (jalankan manual kalau perlu, sesudah kode lama dipasang lagi):
   ALTER TABLE dbo.DBPODET DROP COLUMN PERKIRAAN;
   --------------------------------------------------------------------- */
