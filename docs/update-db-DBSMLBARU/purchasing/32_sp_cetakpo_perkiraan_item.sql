/* =====================================================================
   32 - Sp_CetakPO: Perkiraan per item (DBPODET.PERKIRAAN)
   Untuk menu : Cetak PO (PO Non Stock, juga dipakai PO Stock & Closing PO).
   Isi        : Kolom PERKIRAAN & ketperk diambil dari detail (b.PERKIRAAN).
                Kalau detail kosong (PO lama / PO Stock), pakai header a.PERKIRAAN.
                Nama & urutan kolom hasil TIDAK berubah, jadi pemanggil lama aman.
   Syarat     : 31_dbpodet_perkiraan.sql sudah dijalankan (kolom DBPODET.PERKIRAAN).
   Sifat      : ALTER PROCEDURE, aman dijalankan ulang. Rollback: definisi lama
                disimpan di 32_sp_cetakpo_ROLLBACK.sql.
   ===================================================================== */
USE DBSMLBARU;
GO


ALTER Procedure [dbo].[Sp_CetakPO]
--declare
@NoBukti varchar(30)
---select * from dbpo where ppn=2 order by tanggal desc
--Select @NoBukti='SML/PO/00113/0526'
as
select a.nobukti,a.tanggal,a.KODEVLS,a.HARI,d.NAMACUSTSUPP Expedisi,D.ALAMAT1+' ('+j.NamaKota+')' almkirim,
       E.Usaha+' '+e.NAMACUSTSUPP NAMA,e.ALAMAT1 ALAMAT1,
       b.kodebrg,case when B.NAMABRG='' then C.NAMABRG else c.NamaBrg End NAMABRG,h.NAMAMERK,b.QNT,b.SATUAN,b.NoPPL,
       case when A.PPN<>2 then b.HARGA else b.HARGA / (1.0 +(b.nilaippn/100.00)) End harga,B.HARGA hrg,
       case when a.ppn<>2 then (b.qnt*b.harga) else b.qnt * (b.HARGA/(1.0 +(b.nilaippn/100.00))) end SUBTOTALRp,
       case when b.DISCTOT<> 0 then case when a.PPN=2 then (b.qnt*b.harga)-round(b.NNET,0) else
       (b.qnt*b.harga)-round(b.NDPP,0) end End disc,b.QNT*b.HARGA 'c',b.NDPP,b.DISCTOT,
       b.ndpp ndpprp,b.NNET nnetRp,b.NPPN nppnRp,d.kota,I.NamaKota,
       case when isnull(c.PartNumber,'')='' then B.KODEBRG else c.PartNumber end PartNumber,
        X.JmlRecord,A.PPN,A.Iduser,A.tglInput,Xn.Nama OtoUser1,A.PPN,E.USAHA,
        Y.TSUBTOTALRp,xm.fullname,
        
       Round( case when a.PPN=0 then 0 else
        case when a.TANGGAL >='01/01/2025' then
        (case when a.NOBUKTI like '%POB%' then y.TNDPPRPB else y.Tndpprp end) * 11.00/12.00
        else  (case when a.NOBUKTI like '%POB%' then y.TNDPPRPB else y.Tndpprp end) end 
        
        end,0)
        Tndpprp,
        
        
        
        round(Y.TnppnRp,0) TnppnRp,Y.TnnetRp,G.Alamat AlamatGudang,
         case when a.NOBUKTI like '%POB%' then y.TdiscB else Y.Tdisc  end  Tdisc , b.QNT we,
         case when a.ppn=2 then (case when A.PPN<>2 then b.SUBTOTAL else b.SUBTOTAL-(b.SUBTOTAL * (b.nilaippn/100.00)) End)/*b.SUBTOTAL*(10.00/11.00)*/ else b.SUBTOTAL end subtotalx
        ,case when isnull(b.SATX,'')='' then  b.SATUAN else b.SATX end SATX,
        Y.Tndpprp*A.NPPH23/100 JNPPH23,a.NPPH23,Y.TnnetRp-Y.Tndpprp*A.NPPH23/100 nnetpph,
        isnull(nullif(b.PERKIRAAN,''),a.PERKIRAAN) PERKIRAAN,y1.Keterangan ketperk,tsub,
        b.QNT-ISNULL(b.QntBatal,0) Qntbatal,
        case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else  case when A.PPN<>2 then b.HARGA else b.HARGA / (1.0 + (b.nilaippn/100.00)) End End HrgBatal
        
        , case when a.ppn=2 then (case when A.PPN<>2 then (case when B.[KURS]=(1) then round( (B.[Qnt]-ISNULL(b.QntBatal,0)) *(B.[HARGA]-B.[DiscTot]),(0),(0)) else (B.[Qnt]  -ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DiscTot]) end)
                                                     else (case when B.[KURS]=(1) then round( (B.[Qnt]-ISNULL(b.QntBatal,0)) *(B.[HARGA]-B.[DiscTot]),(0),(0)) else (B.[Qnt]  -ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DiscTot]) end) -
                                                     ((case when B.[KURS]=(1) then round( (B.[Qnt]-ISNULL(b.QntBatal,0)) *(B.[HARGA]-B.[DiscTot]),(0),(0)) else (B.[Qnt]  -ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DiscTot]) end) * (b.nilaippn/100.00)) End) 
          else (case when B.[KURS]=(1) then round( (B.[Qnt]-ISNULL(b.QntBatal,0)) *(B.[HARGA]-B.[DiscTot]),(0),(0)) else (B.[Qnt]  -ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DiscTot]) end) 
          end  subtotalxbatal
		
		, (b.QNT-ISNULL(b.QntBatal,0)) * case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else  case when A.PPN<>2 then b.HARGA else b.HARGA / (1.0 + (b.nilaippn/100.00)) End End TT 
		,(case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.xx End) TSUBTOTALRpbatal
		,
		
		case when a.PPN=0 then 0 else
		case when a.TANGGAL >='01/01/2025' then
		(case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.xx End) * 11.00/12.00
		 else (case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.xx End) end
		end
		TSUBTOTALRpbatalpajak,
		
		y.xx
		,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.Tdisc End Tdiscbatal
		,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else y.TNDPPRPBATAL End TndpprpBatal
		,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TNDPPRPBATAL*A.NPPH23/100 End JNPPH23Batal
		,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TnppnRpbATAL End TnppnRpBatal
		,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TnnetRp-Y.TNDPPRPBATAL*A.NPPH23/100 End nnetpphBatal

,case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TSUBTOTALRpbATAL End + 
case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.Tdisc End -
case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TnppnRpbATAL End TTT,A.tglkirim,b.nilaippn,y2.FullName otouser
,(case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.xx End) + 
case when b.QNT-ISNULL(b.QntBatal,0)=0 then 0 else Y.TnppnRpbATAL End nnetrpbatal
from DBPO A
left outer join DBPODET b on b.NOBUKTI=A.NOBUKTI
Left Outer join DBBARANG c on c.KodeBrg = B.KODEBRG
Left Outer join DBCUSTSUPP E on a.KODESUPP = E.KODECUSTSUPP
left outer join DBCUSTSUPP d on d.KODECUSTSUPP=a.KODEEXP
Left outer join DBGUDANG G on g.KODEGDG =A.KodeGDG
left outer join DBMERK H on H.KODEMERK=c.KodeMerk
left outer join DBKOTA i on E.KOTA=i.KodeKota
left outer join DBKOTA J on d.kota =j.KodeKota
left outer join 
	(select NoBukti, COUNT(*) JmlRecord
	from DBPODET where NoBukti=@nobukti group by NoBukti
	) X on X.NoBukti=A.NoBukti
Left Outer Join DBFLPASS XM on A.OtoUser1=xm.USERID
left outer join dbKaryawan xn on xm.keynik=cast(xn.NIK as int)	
left outer join 
   (select A.NOBUKTI,
   SUM(NDPP) TSUBTOTALRp, 
   SUM((case when A.[ppn]=(1) OR A.[ppn]=(0) then (A.[Qnt]-ISNULL(a.QntBatal,0) )  *(A.[HARGA]-A.[DISCTOT])-(((A.[Qnt]-ISNULL(a.QntBatal,0) )*(A.[HARGA]-A.[DISCTOT]))*A.[DISC])/(100) when A.[ppn]=(2) then ((A.[Qnt]-ISNULL(a.QntBatal,0))*(A.[HARGA]-A.[DISCTOT])-(((A.[Qnt]-ISNULL(a.QntBatal,0))*(A.[HARGA]-A.[DISCTOT]))*A.[DISC])/(100))/((1.0 +(a.nilaippn/100.00))) else (0) end)) TSUBTOTALRpbATAL,
     
   SUM((case when A.[ppn]=(1) OR A.[ppn]=(0) then (A.[Qnt]-ISNULL(a.QntBatal,0) )  *(A.[HARGA])-(((A.[Qnt]-ISNULL(a.QntBatal,0) )*(A.[HARGA]))*A.[DISC])/(100) when A.[ppn]=(2) then ((A.[Qnt]-ISNULL(a.QntBatal,0))*(A.[HARGA])-(((A.[Qnt]-ISNULL(a.QntBatal,0))*(A.[HARGA]-A.[DISCTOT]))*A.[DISC])/(100))/((1.0 +(a.nilaippn/100.00))) else (0) end)) xx,  
     
    /* CASE WHEN A.PPN<>2 THEN
       ISnull(
				case when sum(A.DISCTOT)<> 0 then case when A.PPN=2 then sum((A.qnt - A.QntBatal *(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-round((case when A.PPN<>2 then a.NNET else a.NNET-(a.NNET * (a.nilaippn/100.00)) End),0)) else
       sum((A.qnt - A.QntBatal*(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-case when b.KODEVLS='IDR' then round((case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End),0) else (case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End) end) end End,0) + (SUM((case when A.PPN<>2 then a.SUBTOTAL else a.SUBTOTAL-(a.SUBTOTAL * (a.nilaippn/100.00)) End)) * B.DISC/100)          
      ELSE
       sum(case when A.PPN<>2 then DISCTOT * case when DISCTOT<>0 then QNT - QntBatal else 0 end else (DISCTOT / (1.0 +(a.nilaippn/100.00))) * case when DISCTOT<>0 then QNT-QntBatal else 0 end End )
      END*/ Sum(NDISKON + ((A.QNT - A.QntBatal) * (case when A.PPN<>2 then DISCTOT else DISCTOT / (1.0 +(a.nilaippn/100.00)) End))) Tdisc ,
      SUM(a.NDISKON) TdiscB,sum(A.SubTotal)-sum(A.NDiskon)TNDPPRPB
      
       ,
      -- sum(case when b.ppn<>2 then (a.qnt*a.harga) else a.qnt * (a.HARGA/(1.0 +(a.nilaippn/100.00))) end)-
      -- sum(case when A.PPN<>2 then DISCTOT else DISCTOT / (1.0 +(a.nilaippn/100.00)) End ) Tndpprp,
      sum(case when b.ppn<>2 then (a.qnt*a.harga) else a.qnt * (a.HARGA/(1.0 +(a.nilaippn/100.00))) end) - 
       CASE WHEN A.PPN<>2 THEN
       ISnull(case when sum(A.DISCTOT)<> 0 then case when A.PPN=2 then sum((A.qnt*(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-round((case when A.PPN<>2 then a.NNET else a.NNET-(a.NNET * (a.nilaippn/100.00)) End),0)) else
       sum((A.qnt*(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-case when b.KODEVLS='IDR' then round((case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End),0) else (case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End) end) end End,0) + (SUM((case when A.PPN<>2 then a.SUBTOTAL else a.SUBTOTAL-(a.SUBTOTAL * (a.nilaippn/100.00)) End)) * B.DISC/100)          
      ELSE
       sum(case when A.PPN<>2 then DISCTOT * case when DISCTOT<>0 then QNT else 0 end else (DISCTOT / (1.0 +(a.nilaippn/100.00))) * case when DISCTOT<>0 then QNT else 0 end End ) 
      END TNDPPRP, 
      
      sum(case when b.ppn<>2 then ((a.qnt-ISNULL(a.QntBatal,0))*a.harga) else (a.qnt-ISNULL(a.QntBatal,0)) * (a.HARGA/(1.0 +(a.nilaippn/100.00))) end) - 
       CASE WHEN A.PPN<>2 THEN
       ISnull(case when sum(A.DISCTOT)<> 0 then case when A.PPN=2 then sum(((A.qnt-ISNULL(a.QntBatal,0))*(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-round((case when A.PPN<>2 then a.NNET else a.NNET-(a.NNET * (a.nilaippn/100.00)) End),0)) else
       sum(((A.qnt-ISNULL(a.QntBatal,0))*(case when A.PPN<>2 then a.HARGA else a.HARGA-(a.HARGA * (a.nilaippn/100.00)) End))-case when b.KODEVLS='IDR' then round((case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End),0) else (case when A.PPN<>2 then a.NDPP else a.NDPP-(a.NDPP * (a.nilaippn/100.00)) End) end) end End,0) + (SUM((case when A.PPN<>2 then a.SUBTOTAL else a.SUBTOTAL-(a.SUBTOTAL * (a.nilaippn/100.00)) End)) * B.DISC/100)          
      ELSE
       sum(case when A.PPN<>2 then DISCTOT else (DISCTOT / (1.0 +(a.nilaippn/100.00)))  End )
      END TNDPPRPBATAL, 
      
       sum((case when A.PPN<>2 then a.NPPN else a.NPPN End)) TnppnRp, 
       sum((case when a.[ppn]=(1) then (a.[Qnt]-ISNULL(a.QntBatal,0))*(a.[HARGA]-a.[DISCTOT])-(((a.[Qnt]-ISNULL(a.QntBatal,0))*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100) when a.[ppn]=(2) then ((a.[Qnt]-ISNULL(a.QntBatal,0))*(a.[HARGA]-a.[DISCTOT])-(((a.[Qnt]-ISNULL(a.QntBatal,0))*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100))/((1.0 +(a.nilaippn/100.00))) else (0) end*((a.nilaippn/100.00)))) TnppnRpbATAL,  
       sum((case when A.PPN<>2 then a.NNET else a.NDPP+(a.NDPP * (a.nilaippn/100.00)) End)) TnnetRp,
       sum((case when A.PPN<>2 then (case when a.[ppn]=(1) OR a.[ppn]=(0) then a.[Qnt]*(a.[HARGA]-a.[DISCTOT])-((a.[Qnt]*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100) when a.[ppn]=(2) then (a.[Qnt]*(a.[HARGA]-a.[DISCTOT])-((a.[Qnt]*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100))/((1.0 +(a.nilaippn/100.00))) else (0) end+case when a.[ppn]=(1) then a.[Qnt]*(a.[HARGA]-a.[DISCTOT])-((a.[Qnt]*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100) when a.[ppn]=(2) then (a.[Qnt]*(a.[HARGA]-a.[DISCTOT])-((a.[Qnt]*(a.[HARGA]-a.[DISCTOT]))*a.[DISC])/(100))/((1.0 +(a.nilaippn/100.00))) else (0) end*((a.nilaippn/100.00))) 
			else a.NDPP+(a.NDPP * (a.nilaippn/100.00)) End)) TnnetRpbATAL,
      /*
       sum(case when b.ppn<>2 then (a.qnt*a.harga) else a.qnt * (a.HARGA/(1.0 +(a.nilaippn/100.00))) end)-
       sum(case when A.PPN<>2 then DISCTOT else DISCTOT / (1.0 +(a.nilaippn/100.00)) End ) +
       sum((case when A.PPN<>2 then a.NPPN else a.NPPN End)) TnnetRp,*/
        
       sum(case when b.ppn<>2 then (a.qnt*a.harga) else a.qnt * (a.HARGA/(1.0 +(a.nilaippn/100.00))) end) tsub
       from DBPODET A
       LEFT OUTER JOIN DBPO B ON A.NOBUKTI=B.NOBUKTI
       where A.NOBUKTI=@NoBukti group by A.NOBUKTI,A.PPN,B.DISC) Y on Y.NOBUKTI=A.NOBUKTI
left outer join DBPERKIRAAN y1 on y1.Perkiraan=isnull(nullif(b.PERKIRAAN,''),a.PERKIRAAN)
left outer join DBFLPASS y2 on A.OtoUser2=y2.USERID
       
where	A.NOBUKTI=@NoBukti 
order by b.URUT

GO
