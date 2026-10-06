-- 07 - Hak akses penuh user SA ke menu Verifikasi Barang (href verifikasibarang, L1 04013).
-- Permintaan user 6 Oktober 2026: HANYA user SA. User lain tidak diubah.
-- Sebelumnya SA sudah punya semua hak kecuali IsBatal (tombol Batal Verifikasi).
-- Aman dijalankan ulang (hanya UPDATE satu baris).

UPDATE DBFLMENUWEB
SET HASACCESS = 1, ISTAMBAH = 1, ISKOREKSI = 1, ISHAPUS = 1, ISCETAK = 1, ISEXPORT = 1,
    IsOtorisasi1 = 1, IsOtorisasi2 = 1, IsOtorisasi3 = 1, IsOtorisasi4 = 1, IsOtorisasi5 = 1,
    IsBatal = 1
WHERE L1 = '04013' AND USERID = 'SA';

-- Cek hasil
SELECT USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT, IsBatal
FROM DBFLMENUWEB
WHERE L1 = '04013' AND USERID = 'SA';
