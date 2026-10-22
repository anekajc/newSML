-- 23 - vwPostHutPiut: Kredit Note valas masuk KreditD (22 Oktober 2026)
--
-- Bagian "Kredit Note" di vwPostHutPiut mengisi nilai valas ke DebetD:
--     case when B.KodeVLS='IDR' then 0 else sum(B.Nilai) end DebetD, 0.00 KreditD
-- Untuk Tipe 'PT', SaldoD = DebetD - KreditD, jadi KN valas malah MENAMBAH saldo valas piutang
-- (uji: KN 5 USD -> SaldoD +5), padahal Kredit (Rp) sudah benar mengurangi Saldo. Diubah jadi:
--     0.00 DebetD, case when B.KodeVLS='IDR' then 0 else sum(B.Nilai) end KreditD
-- Bagian Debet Note (Tipe 'HT', DebetD) sudah benar dan tidak disentuh.
--
-- Skrip mengambil definisi view yang ada, mengganti fragmen itu HANYA sesudah penanda
-- 'FROM   DBKreditNote A' (bagian KN), lalu menjalankannya sebagai ALTER VIEW. Bagian view lain
-- tidak berubah. Kalau sudah pernah dijalankan (fragmen tidak ketemu), skrip tidak mengubah apa pun.
-- Aman dijalankan ulang. Dipakai juga untuk DBSMLNEW (ganti baris USE).

USE DBSMLBARU
GO

declare @def nvarchar(max) = object_definition(object_id('dbo.vwPostHutPiut'))
declare @lama nvarchar(200) = N'case when B.KodeVLS=''IDR'' then 0 else sum(B.Nilai) end DebetD, 0.00 KreditD'
declare @baru nvarchar(200) = N'0.00 DebetD, case when B.KodeVLS=''IDR'' then 0 else sum(B.Nilai) end KreditD'
declare @from int = charindex(N'FROM   DBKreditNote A', @def)
-- awal bagian KN = penanda '-- Kredit Note' terakhir sebelum FROM DBKreditNote
declare @awal int = @from - charindex(reverse(N'-- Kredit Note'), reverse(left(@def, @from)))
declare @pos int = charindex(@lama, @def, @awal)

if @from = 0 or @awal <= 0
  print 'Bagian Kredit Note di vwPostHutPiut tidak ditemukan, tidak ada yang diubah'
else if @pos = 0 or @pos > @from
  print 'vwPostHutPiut sudah benar (Kredit Note valas sudah KreditD), tidak ada yang diubah'
else
begin
  set @def = stuff(@def, @pos, len(@lama), @baru)
  set @def = stuff(@def, charindex(N'CREATE', @def), len(N'CREATE'), N'ALTER')
  exec (@def)
  print 'vwPostHutPiut: Kredit Note valas sekarang masuk KreditD'
end
GO

-- Cek: harus berisi "0.00 DebetD, case when B.KodeVLS='IDR' then 0 else sum(B.Nilai) end KreditD"
declare @d nvarchar(max) = object_definition(object_id('dbo.vwPostHutPiut'))
select substring(@d, charindex(N'FROM   DBKreditNote A', @d) - 420, 420) bagian_kredit_note
