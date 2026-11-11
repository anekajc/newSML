/* =====================================================================
   26 - Sembunyikan menu PR Non Agen & PR Non Stock dari sidebar
   Lanjutan 25_gabung_menu_pr.sql. Sesudah href-nya dikosongkan, kedua menu
   masih tampil di sidebar (tidak bisa diklik). Sidebar web (HomeController /
   NewMenuController@getMenuL0) hanya menampilkan baris DBMENUWEB yang
   HeaderMenu-nya tidak NULL, jadi HeaderMenu kedua menu dikosongkan - pola
   yang sama dengan menu tersembunyi lain (mis. 030303 List Reject).
     030101 PR Stock (Non Keagenan)
     030103 PR Non Stock
   Hasil di sidebar: Pembelian > PR > PR (Keagenan), Closing PR.
   Tidak diubah: baris menu & hak akses DBFLMENUWEB 030101/030103 tetap ada,
   dipakai halaman PR gabungan sebagai hak akses jenis Non Agen & Non Stock.
   Sifat      : hanya UPDATE kolom HeaderMenu, aman dijalankan ulang.
   ===================================================================== */
USE DBSMLBARU;
GO

UPDATE dbo.DBMENUWEB SET HeaderMenu = NULL
 WHERE KODEMENU IN ('030101', '030103');
GO

SELECT KODEMENU, Keterangan, HeaderMenu, href FROM dbo.DBMENUWEB WHERE KODEMENU LIKE '0301%' ORDER BY KODEMENU;
GO

/* ---- ROLLBACK ----
UPDATE dbo.DBMENUWEB SET HeaderMenu = 3 WHERE KODEMENU IN ('030101', '030103');
*/
