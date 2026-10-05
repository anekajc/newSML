/* =====================================================================
   06 - Menu Berkas > Pemakai ACC & Pemakai Non ACC
   Untuk menu : Pemakai ACC (route newsetpemakaiacc) dan Pemakai Non ACC
                (route newsetpemakainonacc). Halamannya sama dengan menu
                Pemakai, hanya daftar user-nya dibatasi menurut DBFLPASS.kodeBag
                (ACC saja / selain ACC).
   Sifat      : Hanya mengisi href kedua baris DBMENUWEB yang sudah ada
                (sebelumnya '#', jadi menunya tidak bisa dibuka). Tidak ada
                tabel/kolom/baris yang ditambah atau dihapus.
                Aman dijalankan ulang.
   Dijalankan : 2026-10-05 di DBSMLBARU - sukses.

   Catatan: kode menu di DBSMLBARU (000402 / 000403) berbeda dengan DBSMLNEW
   (000401 / 000402), jadi baris dicari lewat Keterangan di bawah menu 0004.
   Baris hak akses DBFLMENUWEB untuk kedua menu dibuat otomatis (semua 0)
   saat daftar akses user dibuka di menu Pemakai; centang aksesnya di sana.
   ===================================================================== */
USE DBSMLBARU;
GO
UPDATE dbo.DBMENUWEB SET href = 'newsetpemakaiacc'
WHERE KODEMENU LIKE '0004%' AND Keterangan = 'Pemakai ACC';

UPDATE dbo.DBMENUWEB SET href = 'newsetpemakainonacc'
WHERE KODEMENU LIKE '0004%' AND Keterangan = 'Pemakai Non ACC';
GO
