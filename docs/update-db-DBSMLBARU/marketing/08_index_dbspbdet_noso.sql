-- 08 - Index dbSPBDet (NoSo, UrutSo) untuk mempercepat menu Sales Order (marketing/so).
-- Permintaan user 7 Oktober 2026 ("boleh cepetin lagi").
-- Kolom Status SO (Belum proses / Proses Sebagian / Full supply / Terclose) dan filter
-- status di menu SO menjumlah Qty SPB per baris SO. dbSPBDet (~137 ribu baris) belum punya
-- index di NoSo, jadi tiap pencarian SPB untuk satu SO memindai seluruh tabel.
-- Hanya MENAMBAH index; tidak mengubah tabel/data. Aman dijalankan ulang.

IF NOT EXISTS (SELECT 1 FROM sys.indexes
               WHERE name = 'IX_SPB_NoSo_UrutSo' AND object_id = OBJECT_ID('dbSPBDet'))
BEGIN
  CREATE NONCLUSTERED INDEX IX_SPB_NoSo_UrutSo ON dbSPBDet (NoSo, UrutSo) INCLUDE (QNT);
END

-- Cek hasil
SELECT i.name, c.name kolom, ic.key_ordinal, ic.is_included_column
FROM sys.indexes i
JOIN sys.index_columns ic ON ic.object_id = i.object_id AND ic.index_id = i.index_id
JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id
WHERE i.object_id = OBJECT_ID('dbSPBDet') AND i.name = 'IX_SPB_NoSo_UrutSo'
ORDER BY ic.key_ordinal;
