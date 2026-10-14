/* =====================================================================
   16 - Menu Gudang > Closing Perintah Opname (06022)
   Untuk menu : Closing Perintah Opname (ClosingPerintahOpnameController,
                disalin dari newProgramAJC 2026-10-14).
   Masalah    : baris 06022 sudah ada di DBMENUWEB DBSMLBARU, tapi href-nya
                NULL. Halaman /closingperintahopname dilempar ke /home
                (getAkses1 mencari menu lewat href) dan menunya tidak bisa
                diklik. DBSMLNEW: href = 'closingperintahopname'.
   Sifat      : HANYA MENGISI href yang masih NULL. Aman dijalankan ulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
UPDATE dbo.DBMENUWEB SET href = 'closingperintahopname' WHERE KODEMENU = '06022' AND href IS NULL;
GO
SELECT KODEMENU, Keterangan, href, HeaderMenu FROM dbo.DBMENUWEB WHERE KODEMENU = '06022';
