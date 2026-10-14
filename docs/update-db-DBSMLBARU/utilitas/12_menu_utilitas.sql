/* =====================================================================
   12 - Menu Utilitas web (Hitung Ulang Stock, Posting, Proses Tutup Buku)
   Untuk menu : Utilitas (081, 08102, 08103, 08104), disamakan dengan
                DBSMLNEW (newProgramAJC).
   Masalah    : di DBSMLBARU baris menunya sudah ada di DBMENUWEB, tapi
                href dan HeaderMenu masih NULL. Akibatnya:
                - menu Utilitas tidak tampil di navbar (navbar hanya
                  menampilkan baris dengan HeaderMenu tidak NULL), dan
                - halaman /hitungulangstock, /posting, /tutupbuku selalu
                  dilempar ke /home (getAkses1 mencari menu lewat href).
   Sifat      : HANYA MENGISI kolom yang masih NULL. Baris lain, hak akses
                (DBFLMENUWEB), dan menu lain tidak disentuh.
                Aman dijalankan ulang.
   Tidak diisi: 08101 Hitung Ulang HPP - di newProgramAJC href-nya
                'hitungulanghpp' tapi route/halamannya tidak ada (404),
                jadi dibiarkan tersembunyi. 08105 Transfer Data & 08106
                Aktivitas Pemakai juga tersembunyi di newProgramAJC.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;
BEGIN TRAN;

UPDATE dbo.DBMENUWEB SET HeaderMenu = 8 WHERE KODEMENU = '081' AND HeaderMenu IS NULL;

UPDATE m SET m.href = v.href, m.HeaderMenu = 8
FROM dbo.DBMENUWEB m
JOIN (VALUES ('08102', 'hitungulangstock'),
             ('08103', 'posting'),
             ('08104', 'tutupbuku')) v (KODEMENU, href) ON v.KODEMENU = m.KODEMENU
WHERE m.href IS NULL AND m.HeaderMenu IS NULL;

COMMIT;
GO

-- Cek hasil: 081, 08102, 08103, 08104 harus punya HeaderMenu = 8 dan href terisi.
SELECT KODEMENU, Keterangan, href, HeaderMenu FROM dbo.DBMENUWEB WHERE KODEMENU LIKE '081%' ORDER BY KODEMENU;
