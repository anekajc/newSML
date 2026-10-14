/* =====================================================================
   17 - Tabel permintaan otorisasi + langganan Web Push (lonceng otorisasi)
   Untuk menu : lonceng "Permintaan otorisasi" di header, /otorisasiinbox,
                dan tombol Otorisasi / Batal Otorisasi menu Kas
                (OtorisasiService, disalin dari newProgramAJC 2026-10-14).
   Isi        : DBREQOTORISASI (antrian permintaan otorisasi) dan
                DBPUSHSUBSCRIPTION (perangkat yang berlangganan notifikasi).
                Struktur & index sama dengan DBSMLNEW.
   Sifat      : HANYA MENAMBAH tabel baru. Aman dijalankan ulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
IF OBJECT_ID('dbo.DBREQOTORISASI') IS NULL
BEGIN
  CREATE TABLE dbo.DBREQOTORISASI (
    ID       int IDENTITY(1,1) NOT NULL PRIMARY KEY,
    KODEMENU varchar(20)  NOT NULL,
    MENUPATH varchar(100) NOT NULL,
    NOBUKTI  varchar(30)  NOT NULL,
    LEVELOTO int          NOT NULL DEFAULT (1),
    REQUSER  varchar(50)  NOT NULL,
    TGLREQ   datetime     NOT NULL DEFAULT (getdate()),
    STATUS   char(1)      NOT NULL DEFAULT ('P'),
    ACTUSER  varchar(50)  NULL,
    TGLACT   datetime     NULL,
    CATATAN  varchar(255) NULL,
    REQREAD  bit          NOT NULL DEFAULT (0)
  );
  CREATE INDEX IX_DBREQOTORISASI_STATUS  ON dbo.DBREQOTORISASI (STATUS, MENUPATH);
  CREATE INDEX IX_DBREQOTORISASI_NOBUKTI ON dbo.DBREQOTORISASI (NOBUKTI);
  CREATE INDEX IX_DBREQOTORISASI_REQUSER ON dbo.DBREQOTORISASI (REQUSER);
END
GO
IF OBJECT_ID('dbo.DBPUSHSUBSCRIPTION') IS NULL
BEGIN
  CREATE TABLE dbo.DBPUSHSUBSCRIPTION (
    ID        int IDENTITY(1,1) NOT NULL PRIMARY KEY,
    USERID    varchar(50)  NOT NULL,
    ENDPOINT  varchar(500) NOT NULL,
    P256DH    varchar(255) NOT NULL,
    AUTHKEY   varchar(255) NOT NULL,
    USERAGENT varchar(255) NULL,
    TGLBUAT   datetime     NOT NULL DEFAULT (getdate()),
    CONSTRAINT UQ_DBPUSHSUBSCRIPTION_ENDPOINT UNIQUE (ENDPOINT)
  );
  CREATE INDEX IX_DBPUSHSUBSCRIPTION_USERID ON dbo.DBPUSHSUBSCRIPTION (USERID);
END
GO
SELECT name FROM sys.tables WHERE name IN ('DBREQOTORISASI', 'DBPUSHSUBSCRIPTION');
