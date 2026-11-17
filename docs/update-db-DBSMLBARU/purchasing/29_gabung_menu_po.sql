/* =====================================================================
   29 - Gabung menu PO Stock & PO Non Stock jadi satu
   Untuk menu : Purchase Order gabungan di /purchaseorder (POController +
                purchaseOrder.blade.php + partials/poNonStock*). Jenis dibedakan
                lewat kolom & filter Jenis, Tambah memilih PO Stock / PO Non Stock.
   Isi        : - DBMENUWEB 030202 PO Non Stock (pononstock): href & HeaderMenu
                  dikosongkan (NULL) supaya hilang dari sidebar. Route /pononstock
                  diarahkan ke /purchaseorder?jenis=nonstock.
                - DBMENU 030022 PO Jasa (pononstock): href dikosongkan.
                - DBMENUWEB 030201 label 'PO Stock' -> 'PO'.
   Tidak diubah: baris menu & hak akses DBFLMENUWEB 030201/030202 TETAP ada -
                halaman gabungan memakainya sebagai hak akses jenis PO Stock dan
                PO Non Stock (dibaca per KODEMENU, bukan per href).
   Sifat      : hanya UPDATE, aman dijalankan ulang. Rollback di bawah.
   ===================================================================== */
USE DBSMLBARU;
GO

UPDATE dbo.DBMENUWEB SET href = NULL, HeaderMenu = NULL
 WHERE KODEMENU = '030202';

UPDATE dbo.DBMENU SET href = NULL
 WHERE KODEMENU = '030022' AND href = 'pononstock';

UPDATE dbo.DBMENUWEB SET Keterangan = 'PO'
 WHERE KODEMENU = '030201' AND Keterangan = 'PO Stock';
GO

-- Cek hasil: 030202 href & HeaderMenu NULL, 030201 'PO' / purchaseorder, DBMENU 030022 href NULL.
SELECT 'DBMENUWEB' tabel, KODEMENU, Keterangan, href, HeaderMenu FROM dbo.DBMENUWEB WHERE KODEMENU IN ('030201', '030202')
UNION ALL
SELECT 'DBMENU', KODEMENU, Keterangan, href, NULL FROM dbo.DBMENU WHERE KODEMENU IN ('03002', '030022');
GO

/* ---- ROLLBACK (kalau menu lama perlu dihidupkan lagi) ----
UPDATE dbo.DBMENUWEB SET href = 'pononstock', HeaderMenu = 3 WHERE KODEMENU = '030202';
UPDATE dbo.DBMENU    SET href = 'pononstock' WHERE KODEMENU = '030022';
UPDATE dbo.DBMENUWEB SET Keterangan = 'PO Stock' WHERE KODEMENU = '030201';
-- lalu kembalikan route /pononstock ke PONonStockController@index di routes/purchasing.php
*/
