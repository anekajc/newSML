/* =====================================================================
   15 - SP_FlmenuWeb1 menyimpan hak akses ke DBFLMENUWEB (Berkas > Pemakai)
   Masalah    : di DBSMLBARU SP ini meng-UPDATE DBFLMENU (tabel hak akses
                program lama), padahal web membaca DBFLMENUWEB. Akibatnya
                centang hak akses per menu di Berkas > Pemakai tidak
                berpengaruh. Isi disamakan dengan DBSMLNEW (beda hanya nama
                tabel yang di-update).
   Sifat      : ALTER SP. Disetujui user 2026-10-14. Aman diulang.
                DBFLMENU (program lama) tidak lagi diubah dari web.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
ALTER PROCEDURE [dbo].[SP_FlmenuWeb1]

@USERID varchar(25),
@L1 varchar(25),
@HASACCESS bit,
@ISTAMBAH bit,
@ISKOREKSI bit,
@ISHAPUS bit,
@ISCETAK bit,
@ISEXPORT bit,
@IsOtorisasi1 bit,
@IsOtorisasi2 bit,
@IsOtorisasi3 bit,
@IsOtorisasi4 bit,
@IsOtorisasi5 bit,
@IsBatal bit,
@pembatalan bit
as
UPDATE [dbo].[DBFLMENUWEB]
           
     SET
         
            L1 = @L1
           , HASACCESS =@HASACCESS
           ,ISTAMBAH = @ISTAMBAH
           ,ISKOREKSI = @ISKOREKSI
           ,ISHAPUS = @ISHAPUS
           ,ISCETAK = @ISCETAK
           ,ISEXPORT = @ISEXPORT
           ,IsOtorisasi1 = @IsOtorisasi1
           ,IsOtorisasi2 = @IsOtorisasi2
           ,IsOtorisasi3 = @IsOtorisasi3
           ,IsOtorisasi4 = @IsOtorisasi4
           ,IsOtorisasi5 = @IsOtorisasi5
           ,IsBatal = @IsBatal
           ,pembatalan = @pembatalan
WHERE USERID = @USERID and L1 = @L1
GO
