<?php

namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;

class InvoicePenjualanController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04104';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


    $tempOutstanding = DB::connection("SML")->select("

declare @Tahun int, @Bulan int, @Periode Varchar(30)

select @Tahun= :tahun , @Bulan= :bulan

Set @Periode=CAST(@Tahun as varchar(4))+Case when @Bulan<10 then '0' else '' end+CAST(@Bulan as varchar(2))
set nocount on
Select distinct A.NoBukti,A.Tanggal,A.KodeCustSupp,
       D.NOBUKTI Noso, m1.PPN PPNCUST,
       D.Tanggal TglSO,m1.NamaCustSupp, A.IsClose
From dbo.DBSPB A  WITH(NOLOCK)
     Left Outer Join (select NoBukti,NoSO from dbo.dbSPBDet WITH(NOLOCK) group by NoBukti,NoSO) C on A.NoBukti=C.NoBukti
     left outer join dbo.DBSO D WITH(NOLOCK) on D.Nobukti=C.NoSO
     left outer join dbo.vwBrowsCustomer E WITH(NOLOCK)on E.Kodecust=A.KodeCustSupp and E.Sales=D.KODESLS
     left outer join dbo.dbInvoicePLDet F WITH(NOLOCK)on F.NoSPB=A.NoBukti
     Left Outer Join dbo.DbCustSupp M1 WITH(NOLOCK)on A.KodeCustSupp=M1.KodeCustsupp
      Left Outer join (Select x.NoBukti,x.NoSPP, Sum(x.QNT-ISNULL(y.qnt,0)) Qnt,
                      Sum(x.QNT2-ISNULL(y.Qnt2,0)) Qnt2 ,
                      SUM(case when x.NOSAT=1 then x.QNT-ISNULL(y.qnt,0) else x.QNT2-ISNULL(y.Qnt2,0) end)Qntx
                      from dbo.dbSPBDet x  WITH(NOLOCK)
                      left Outer join (Select x.NoSPB, x.UrutSPB, Sum(x.QNT) Qnt, Sum(x.QNT2) Qnt2
                                       From dbo.DBRSPBDet x WITH(NOLOCK)
                                       Group by x.NoSPB, x.UrutSPB) y on y.NoSPB=x.NoBukti and y.UrutSPB=x.Urut
                      group by x.NoBukti, x.NoSPP, y.NoSPB) B on B.NoBukti=A.NoBukti
where Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                     Case when A.IsOtorisasi2=1 then 1 else 0 end+
                     Case when A.IsOtorisasi3=1 then 1 else 0 end+
                     Case when A.IsOtorisasi4=1 then 1 else 0 end+
                     Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                else 1
           end As Bit)=0 and F.NoSPB is null and CAST(YEAR(A.Tanggal) as varchar(4))+Case when month(A.Tanggal)<10 then '0' else '' end+CAST(month(A.Tanggal) as varchar(2))<=@Periode
and (B.Qntx>0 )
order by A.NoBukti

" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);





    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding2 = $this->queryInvoicePenjualanOtorisasi($tglawal, $tglakhir, 0);

    return view('marketing.invoicepenjualan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Invoice
  // Otorisasi" -- dulu tabel2 (Belum, tombol Koreksi/Otorisasi/Print/Detail)
  // dan tabel3 (Sudah, tombol Batal Otorisasi/Print/Detail) adalah 2 tab
  // terpisah dengan query nyaris identik (cuma beda IsOtorisasi1=0/1).
  // Digabung jadi satu dengan filterip yang menyaring di server, port 1:1
  // dari pola queryOutstanding() milik PerintahReturJualController. Tabel
  // "Surat Pengiriman Barang" (tabel, bukan konsep otorisasi) sengaja tidak
  // disentuh.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryInvoicePenjualanOtorisasi ($tglawal, $tglakhir, $filterip) {
    return DB::connection("SML")->select("
      select * from (
        select  A.NoBukti, A.NoUrut, A.Tanggal,
                A.KodeCustSupp, F.NAMACUSTSUPP NamaCustSupp, A.Consignee, A.NotifyParty,
                A.ContractNo, A.PONo, A.PaymentTerm, A.DocCreditNo, A.PoL, A.PoD,
                A.NameOfVessel, A.ShipOnBoardDate, A.Packing, A.Others, A.ISLOKAL,
                B.NoSPB, D.Tanggal TglSPB,
                A.IsCetak, A.IDUser,
                A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
                A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
                A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
                A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
                A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
                Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                        else 1
                   end As Bit) NeedOtorisasi
                ,Isnull(A.IsBatal,0) Isbatal,A.userBatal,A.tglBatal, A.NoPajak,E.NOBUKTI NOSO,E.TANGGAL TGLSO,E.NoPesanan,Isnull(A.CetakKe,0) CetakKe
                ,G.totdpp,G.totppn,G.totnet
        from	dbo.dbInvoicePL A WITH(NOLOCK)
        Left Outer join (Select x.NoBukti, x.NoSPB
                         from dbo.dbInvoicePLDet x  WITH(NOLOCK)
                         Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
        LEFT OUTER JOIN (SELECT NOBUKTI,NoSO FROM dbo.dbSPBDet WITH(NOLOCK) GROUP BY NoBukti,NoSO) C ON B.NoSPB=c.NoBukti
        LEFT OUTER JOIN dbo.dbSPB D WITH(NOLOCK)ON C.NoBukti=D.NoBukti
        left Outer join dbo.dbSO E WITH(NOLOCK) on E.Nobukti=C.NoSO
        left outer join dbo.DBCUSTSUPP F WITH(NOLOCK) on F.KODECUSTSUPP=A.KodeCustSupp
        left outer join vwRpDetInvoicePL G on A.NoBukti=G.NoBukti
        where	A.Tanggal between ? and ?
        and isnull(A.FlagKasir,0)=0
        And Isnull(A.pJasa,0)=0
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) = 0)
         or (? = 2 and isnull(x.IsOtorisasi1,0) <> 0)
      order by x.NoBukti
    ", [$tglawal, $tglakhir, $filterip, $filterip, $filterip]);
  }

  public function getListInvoiceCetak (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    select @Tahun= :tahun , @Bulan= :bulan

    set nocount on
    select  A.NoBukti, A.NoUrut, A.Tanggal,
            A.KodeCustSupp, F.NAMACUSTSUPP NamaCustSupp, A.Consignee, A.NotifyParty,
            A.ContractNo, A.PONo, A.PaymentTerm, A.DocCreditNo, A.PoL, A.PoD,
            A.NameOfVessel, A.ShipOnBoardDate, A.Packing, A.Others, A.ISLOKAL,
            B.NoSPB, D.Tanggal TglSPB,
            A.IsCetak, A.IDUser,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                         Case when A.IsOtorisasi2=1 then 1 else 0 end+
                         Case when A.IsOtorisasi3=1 then 1 else 0 end+
                         Case when A.IsOtorisasi4=1 then 1 else 0 end+
                         Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                    else 1
               end As Bit) NeedOtorisasi
            ,Isnull(A.IsBatal,0) Isbatal,A.userBatal,A.tglBatal, A.NoPajak,E.NOBUKTI NOSO,E.TANGGAL TGLSO,E.NoPesanan,Isnull(A.CetakKe,0) CetakKe
             ,G.totdpp,G.totppn,G.totnet
    from	dbo.dbInvoicePL A WITH(NOLOCK)
    Left Outer join (Select x.NoBukti, x.NoSPB
                     from dbo.dbInvoicePLDet x  WITH(NOLOCK)
                     Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
    LEFT OUTER JOIN (SELECT NOBUKTI,NoSO FROM dbo.dbSPBDet WITH(NOLOCK) GROUP BY NoBukti,NoSO) C ON B.NoSPB=c.NoBukti
    LEFT OUTER JOIN dbo.dbSPB D WITH(NOLOCK)ON C.NoBukti=D.NoBukti
    left Outer join dbo.dbSO E WITH(NOLOCK) on E.Nobukti=C.NoSO
    --left outer join dbo.vwBrowsCustomer F WITH(NOLOCK) on F.KodeCust=A.KodeCustSupp and F.Sales=E.KODESLS
    left outer join dbo.DBCUSTSUPP F WITH(NOLOCK) on F.KODECUSTSUPP=A.KodeCustSupp
    left outer join vwRpDetInvoicePL G on A.NoBukti=G.NoBukti
    where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
    and isnull(A.FlagKasir,0)=0
    And Isnull(A.pJasa,0)=0
    order by A.NoBukti" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);
    return $tempOutstanding;

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakInvoicePenjualan ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


  public function getDetailCetakAll (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');
      $tempData = $req->input("tempData");
      $tempCetak0 = [];
      foreach ($tempData as $d ) {
        // code...

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakInvoicePenjualan ?",
          [$d]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }
      array_push($tempCetak0 , $tempCetak1);
      }


      return $tempCetak0;
  }

    public function getDetailCetakSPB(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "SELECT A.NoSPB FROM dbInvoicePLDet A
                    where A.NoBukti= :noBukti
                   group by A.NoSPB order by A.NoSPB",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function getDetailPenerimaancetak (Request $req) {

    // $detailPenerimaan = Vwpersiapanspb::all()->where('nobukti',$req->input('NOBUKTI'))->where ->sortBy('Urut');

     $detailPenerimaan = DB::connection('SML')->select('
     select * from Vwpersiapanspb where NOBUKTI = :nobukti and qntcetak<>0 order by urut',
     ["nobukti" => $req->input('NOBUKTI')]
    );

    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function spDetailKoreksi (Request $req) {


    $nobukti = $req->nobukti;

    $list = DB::connection("SML")->select("
    declare @NoBukti varchar(30)

    select 	@NoBukti= :nobukti

    Select A.NoBukti,A.NoUrut,A.Tanggal,B.NoSPP, B.TglSPP,B.NoSO, B.TglSO,A.KodeCustSupp,E1.KODESLS,I.Nama NamaSls,
           A.Valas,A.Kurs,A.IsLokal,
           A.Consignee,A.NotifyParty,B.PONo,
           A.PaymentTerm, A.PoL,A.PoD,A.NameOfVessel,
           A.ShipOnBoardDate,A.Packing,
           B.Urut,B.UrutSPB,B.KodeBrg,E.PPN,A.DISC,B.QNT QNT1,B.QNT2,E1.TIPEBAYAR, E1.HARI,
           B.SAT_1,B.SAT_2,B.NOSAT,B.ISI,B.NetW,B.GrossW,
           B.HARGA,B.DiscP,B.DiscRp,B.DISCTOT,
           B.HrgNetto,B.SubTotal,B.NDiskon,B.NDPP,B.NPPN,B.NNET,
           B.SubTotalRp,B.NDiskonRp,B.NDPPRp,B.NPPNRp,B.NNETRp,B.KetDetail,
           A.Consignee ConsigneeSC,A.PaymentTerm,A.NotifyParty,
           A.PoL PoLSC,
           A.PoD,A.Packing, F.NAMABRG,
           Case when G.usaha<>'' then G.Usaha+'. ' else '' end+G.NAMACUSTSUPP NamaCustSupp,
           ltrim(G.ALAMAT1+case when ltrim(G.ALAMAT2)<>'' then char(13)+G.ALAMAT2 else '' end+
    		case when ltrim(isnull(G.KOTA,''))<>'' then char(13)+isnull(G.KOTA,'') else '' end) Alamat,
    		G.KOTA,'' Negara, B.Namabrg NamabrgKom,
           A.NoBL, A.NoteBeneficiary1, A.NoteBeneficiary2, A.NoteBeneficiary3,
           A.ShipmentAdvice1, A.ShipmentAdvice2, B.ShippingMark, A.ETADestination, A.ToShipmentAdvice2,
           A.FootNote,A.IssuingBank, H.NamaVls,
           Case when B.Nosat=1 then B.Qnt
                when B.Nosat=2 then B.Qnt2
                when B.Nosat=3 then B.Qnt2
                Else 0
           end Qnt,
           Case when B.Nosat=1 then B.Sat_1
                when B.Nosat=2 then B.Sat_2
                when B.Nosat=3 then B.Sat_2
                Else ''
           end Satuan, B.NoSPB,E1.NoAlamatKirim, J.Nama, J.Alamat AlamatX,J.Telp, J.Fax,
           Case when A.Valas='IDR' then B.SubTotalRp  else B.SubTotal end Total,
           Case when A.Valas='IDR' then K.TotDiskonRp  else K.TotDiskon end Diskon,
           Case when A.Valas='IDR' then K.TotDPPRp  else K.TotDPP end TotalDPP,
           Case when A.Valas='IDR' then K.TotPPnRp  else K.TotPPn end TotalPPn,
           Case when A.Valas='IDR' then K.TotNetRp  else K.TotNet end TotalNetto,
           B.UrutTrans,isnull(A.NuangMuka,0) nUangMuka,E1.NoPesanan ,K.TotSubTotal
    From dbInvoicePL A
         left outer join dbInvoicePLDet B on B.NoBukti=A.NoBukti
         left Outer join dbSPBDet C on B.NoSPB=C.NoBukti and B.UrutSPB=C.Urut
         Left Outer join dbSPB D on C.NoBukti=D.NoBukti
         Left outer join DBSODET E on C.NoSO=E.NOBUKTI and C.UrutSO=E.URUT
         Left Outer join DBSO E1 on E.NOBUKTI=E1.NOBUKTI
         left outer join DBBARANG F on F.KODEBRG=B.KodeBrg
         --left outer join vwBrowsCustomer G on G.Kodecust=A.kodeCustSupp and G.Sales=E.KODESLS
         left outer join DBCUSTSUPP G on G.KODECUSTSUPP=A.KodeCustSupp
         left Outer join dbkaryawan I on I.KeyNik=E1.KODESLS
         left Outer join dbValas H on H.kodevls=A.Valas
         Left Outer join dbAlamatCust J on J.KodecustSupp=A.KodecustSupp and J.Nomor=E1.NoAlamatKirim
         Left Outer join vwRpDetInvoicePL K on K.nobukti=A.Nobukti
    where A.NoBukti=@nobukti
    Order by B.Urut



    " , [ "nobukti" => $nobukti ]);



    return $list;


  }

  public function spAddDetailKoreksi(Request $req)
  {
      $nobukti = $req->nobukti;

      $sql = "
          declare @NoBukti varchar(30)

          select @NoBukti = :nobukti

          select A.NOBUKTI, A.NOURUT, A.TANGGAL, A.KODECUSTSUPP,
                 H.NAMACUSTSUPP, H.Alamat1A, H.Kota,
                 B.NOSo Nosc, A.NOSPP, A.NoPolKend,
                 A.Container, A.NoContainer, A.NoSeal,
                 A.ISCETAK, A.IDUser,
                 B.URUT, B.KODEBRG, C.NamaBrg,
                 '' Jns_Kertas, '' Ukr_Kertas,
                 B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI,
                 B.UrutSPP, B.netW, B.GrossW,
                 B.Namabrg Namabrgkom,
                 0.00 GSM,
                 B.NoBukti + Cast(B.urut as varchar(5)) Mykey,
                 A.Catatan, A.Sopir,

                 Case
                      when B.NOSAT = 1 then B.SAT_1
                      when B.NOSAT = 2 then B.SAT_2
                      when B.NOSAT = 3 then B.SAT_2
                 end Satuan,

                 B.Kodegdg,
                 C.NFix,
                 G.Nama,
                 G.Alamat,
                 G.Telp,
                 G.Fax,

                 A.kodeExp,
                 A.NoResi,
                 A.JumlahTagihan,

                 F.Namacustsupp NamaEXP,
                 F.Alamat1 ALamat1EXP,
                 F.Alamat2 ALamat2EXP,
                 F.Kota KotaEXP,

                 isnull(A.FlaGtipe,0) FlagTipe,
                 E.NoalamatKirim,
                 I.nama NamaKebun,
                 A.KodeKebun,

                 case
                      when B.Nosat = 1 then D.Qnt
                      when B.Nosat = 2 then D.Qnt2
                      when B.Nosat = 3 then D.Qnt2
                 end QNTSO,

                 case
                      when B.Nosat = 1 then D.Qnt
                      when B.Nosat = 2 then D.Qnt2
                      when B.Nosat = 3 then D.Qnt2
                 end QNTSPB,

                 B.NAMAbRG NAMAPRODUK,

                 case
                      when B.NOSAT = 1 then B.QNT - Isnull(M1.QNT1SPB,0) + Isnull(M4.QNT1RSPB,0)
                      when B.NOSAT = 2 then B.QNT2 - Isnull(M1.QNT2SPB,0) + Isnull(M4.QNT2RSPB,0)
                      when B.NOSAT = 3 then B.QNT2 - Isnull(M1.QNT2SPB,0) + Isnull(M4.QNT2RSPB,0)
                 end QntOut,

                 case
                    when B.NOSAT = 1 then Isnull(M4.QNT1RSPB,0)
                    else Isnull(M4.QNT2RSPB,0)
                 end QNTRSPB,

                 B.NOso,
                 B.urutSo,
                 B.Satx

          From DBSPB A

          Left Outer Join DBSPBDET B
              on B.NoBukti = A.NoBukti

          Left Outer Join dbBarang C
              On C.KodeBrg = B.KodeBrg

          Left Outer Join DbSODet D
              on B.Noso = D.Nobukti
             and B.urutSo = D.urut

          Left Outer Join DbSO E
              on D.Nobukti = E.nobukti

          Left Outer join dbcustsupp F
              on F.Kodecustsupp = A.KodeExp

          Left Outer Join DBAlamatCust G
              on E.NoAlamatKirim = G.NoMor
             and E.KODECUST = G.KODECUSTSUPP

          Left Outer Join DbCustSupp H
              on A.KodeCustSupp = H.KodeCustSupp

          Left Outer join DBKebunCustSupp I
              on A.KodeKebun = I.KodeKebun
             and A.kodecustSupp = I.KodecustSupp

          LEFT OUTER JOIN (
              SELECT NOSO,
                     UrutSO,
                     SUM(ISNULL(QNT,0)) QNT1SPB,
                     SUM(ISNULL(QNT2,0)) QNT2SPB
              FROM dbSPBDet
              GROUP BY NoSO, UrutSO
          ) M1
              ON D.NOBUKTI = M1.NoSO
             AND D.URUT = M1.UrutSO

          LEFT OUTER JOIN (
              SELECT B.NoSo,
                     B.UrutSo,
                     SUM(ISNULL(A.QNT,0)) QNT1RSPB,
                     SUM(ISNULL(A.QNT2,0)) QNT2RSPB
              FROM dbRSPBDet A
              LEFT OUTER JOIN dbSPBDet B
                  ON A.NoSPB = B.NoBukti
                 AND A.UrutSPB = B.Urut
              GROUP BY B.NoSo, B.UrutSo
          ) M4
              ON D.NOBUKTI = M4.NoSO
             AND D.URUT = M4.UrutSO

          where A.NoBukti = @NoBukti

          order By B.Urut
      ";

      $list = DB::connection("SML")->select($sql, [
          "nobukti" => $nobukti
      ]);

      return $list;
  }


  public function spAdd (Request $req) {

    $listData = $req->tempData;
    $finalData = [];

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $ppncust = DB::connection("SML")->select("select PPN from dbcustsupp where kodecustsupp = :kodecustsupp" , ["kodecustsupp" => $listData[0]['KodeCUstSupp']]);
    // return $ppncust[0]->PPN;
    foreach ($listData as $d) {
      // code...
      // return $d;

      $temp = DB::connection("SML")->select("declare @NoBukti varchar(30), @Bulan int, @Tahun int
select @NoBukti= :nobukti , @Bulan= :bulan , @Tahun= :tahun
Select A.Nobukti, A.Tanggal, B.Kodebrg,  case when isnull(B.NamaBrg , '') = '' then C.NAMABRG else b.Namabrg end NamaBrg,
       Case when B.Nosat=1 then B.SAT_1
            when B.Nosat=2 then B.SAT_2
            when B.Nosat=3 then B.SAT_2
            else ''
       end Satuan, B.Nosat,B.Isi, B.Qnt Qnt1, B.Qnt2,
       Case when B.Nosat=1 then B.Qnt
            when B.Nosat=2 then B.Qnt2
            when B.Nosat=3 then B.Qnt2
            else 0.00
       end Qnt, Isnull(D.Qnt1,0) Qnt1Inv, isnull(D.Qnt2,0) Qnt2Inv, isnull(D.Qnt,0) QntInv,
       B.Qnt-isnull(D.Qnt1,0)-isnull(H.QntRSPB,0) Qnt1Sisa, B.Qnt2-Isnull(D.Qnt2,0)-isnull(H.Qnt2RSPB,0) Qnt2Sisa,
       Case when B.Nosat=1 then B.Qnt
            when B.Nosat=2 then B.Qnt2
            when B.Nosat=3 then B.Qnt2
            else 0
       end-isnull(D.Qnt,0)-
        Case when B.Nosat=1 then isnull(H.QntRSPB,0)
             when B.Nosat=2 then isnull(H.Qnt2RSPB,0)
             when B.Nosat=3 then isnull(H.Qnt2RSPB,0)
             else 0
       end QntSisa, G.HARGA, G.DISCP1, G.DISCRP1, G.DISCTOT, F.KODEVLS,F.KURS, G.PPN, F.TIPEBAYAR, F.HARI, G.Disc,
       A.nobukti+Cast(B.Urut as Varchar(5)) KeyUrut,b.SATX,
       C.SAT1, case when B.nosat=1 then C.sat1 when B.nosat=2 then C.SAT2 when b.nosat=3 then C.sat3 End Sat2,f.NoPesanan, F.TglKirim, F.NoAlamatKirim, A.KodeCustSupp KodeCust, B.Urut,B.kodegdg,
       A.Catatan, G.NoBukti Noso, F.Tanggal TglSO, F.NoBukti NoSPP, F.Tanggal TglSPP,G.Discp2,G.Discp3,Discp4,Discp5,B.Nobatch,G.PPNBRG
From DBSPB A
     Left Outer join dbSPBDet B on B.Nobukti=A.Nobukti
     Left Outer join dbBarang C on C.kodebrg=B.Kodebrg
     Left Outer join (Select y.NoSPB, y.UrutSPB, Sum(y.Qnt) Qnt1, Sum(y.Qnt2) Qnt2,
                             Sum(Case when y.Nosat=1 then y.Qnt
                                      when y.Nosat=2 then y.Qnt2
                                      when y.Nosat=3 then y.Qnt2
                                      else 0.00
                                 end) Qnt
                      from DBInvoicePL x
                           Left Outer Join dbInvoicePLDet y on y.nobukti=x.nobukti
                      Group by  y.NoSPB, y.UrutSPB) D on D.NoSPB=A.Nobukti and D.UrutSPB=B.Urut
left Outer Join DBSODET G on B.NoSO=G.NOBUKTI And B.UrutSO=G.URUT
Left Outer join DBSO F on G.NOBUKTI=F.NOBUKTI
     Left Outer join (Select x.NoSPB, x.UrutSPB, sum(Qnt) QntRSPB, sum(Qnt2) Qnt2RSPB,
                             SUM(x.NetW) NetWRSPB, SUM(x.GrossW) GrossWRSPB
                      from DBRSPBDet x
                      Group by  x.NoSPB, x.UrutSPB) H on H.NoSPB=B.NoBukti and H.UrutSPB=B.Urut
Where A.NoBukti=@nobukti and
      Case when B.Nosat=1 then B.Qnt
           when B.Nosat=2 then B.Qnt2
           when B.Nosat=3 then B.Qnt2
           else 0.00
      end-isnull(D.Qnt,0)-
        Case when B.Nosat=1 then isnull(H.QntRSPB,0)
             when B.Nosat=2 then isnull(H.Qnt2RSPB,0)
             when B.Nosat=3 then isnull(H.Qnt2RSPB,0)
             else 0
       end>0
Order by B.Urut
  " , ["nobukti" => $d['Nobukti'], "bulan" => $periode->bulan , "tahun" =>$periode->tahun ]); ;




      array_push($finalData, $temp);
    }




    $tesData = [];

    if ($ppncust[0]->PPN == 1) {
      $inisial = DB::connection("SML")->select('select INVC from DBNOMOR');

      $values = [
          $inisial[0]->INVC,
          $periode->bulan,
          $periode->tahun,
          \Auth::user()->username,
          // $periode
          // $periode
      ];
      $resNoBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);


    } else {

      $values = [
          'INVCN',
          $periode->bulan,
          $periode->tahun,
          \Auth::user()->username,
          // $periode
          // $periode
      ];
      $resNoBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);


    }


    $nobukti =  $resNoBukti[0]->Nobukti;
    $nourut =  $resNoBukti[0]->Nourut;
    $tes123 = [];
	$xurut=0;

    foreach ($finalData as $fd) {

      // code...
      array_push($tes123 , 'a');
      foreach ($fd as $fd1) {


//      $purut = DB::connection('SML')->select('select * from DBINVOICEPLDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
//     if ($purut){


//         $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBINVOICEPLDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
//         $xurut= $purut[0]->xurut;

//     }else{
//         $xurut=1;
//     }


        // code...
        // array_push($tesData, $fd1->Kodebrg );


        $values = [
            'I',
            $nobukti,
            $nourut,
            $req->inputDate,
            $fd1->Nobukti,
            $fd1->KodeCust,
            '',
            '',
            '',
            $req->NoPesanan, // 10
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            \Auth::User()->username,
            0, // 20
            $fd1->Urut,
            $fd1->Kodebrg,
            $fd1->PPN,
            $fd1->Disc,
            $fd1->KURS,
            $fd1->Qnt1Sisa,
            $fd1->Qnt2Sisa,
            $fd1->SAT1,
            $fd1->Sat2,
            $fd1->Nosat, // 30
            $fd1->Isi,
            0,
            0,
            $fd1->HARGA,
            $fd1->DISCP1,
            $fd1->DISCRP1,
            $fd1->DISCTOT,
            $fd1->Catatan,
            $fd1->KODEVLS,
            0, //islokal // 40
            '', // NOBL
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            NULL, //50
            '', // 50
            '', // footnote
            '',
            $fd1->NamaBrg,
            $fd1->NoSPP,
            $fd1->TglSPP,
            $fd1->Noso,
            $fd1->TglSO,
            $fd1->Discp2,
            $fd1->Discp3, //60
            $fd1->Discp4, //60
            $fd1->Discp5,
            $fd1->PPN, // flagtipe
            $fd1->Nobatch,
            0, //pjasa
            $fd1->SATX,
            $fd1->NoAlamatKirim, //kodelokasi
            $fd1->HARI,
            $fd1->TIPEBAYAR,
            $fd1->PPNBRG, // 70

        ];
        array_push($tesData, $values );

        $response = DB::connection('SML')->statement('exec SP_InvoicePL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);



array_push($tes123 , $response);
          //   return ['I','INVC',$nobukti,'',$fd1->Urut,'DBINVOICEPLDET'];
          //  $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans('I','INVC',$nobukti,'',$fd1->Urut,'DBINVOICEPLDET');


      }

    }


    return ["finalData" => $finalData , "tesData" => $tesData , "nobukti" => $resNoBukti , "tes123" => $tes123 , "status" => 1 , "nobuktix" => $nobukti];


  }

  public function spDelete (Request $req) {

    $values = [
        'D',
        $req->nobukti,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '', // 10
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        \Auth::User()->username,
        $req->urut, // 20
        0,
        '',
        0,
        0,
        0,
        0,
        0,
        '',
        '',
        0, // 30
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        '',
        '',
        0, //islokal // 40
        '', // NOBL
        '',
        '',
        '',
        '',
        '',
        '',
        0, //meas
        NULL, //50
        '', // 50
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        0,
        0, //60
        0, //60
        0,
        0, // flagtipe
        '',
        0, //pjasa
        '',
        '', //kodelokasi
        0,
        0,
        0, // 70

    ];

    $response = DB::connection('SML')->statement('exec SP_InvoicePL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
	$tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('D','INVC',$req->nobukti,'',$req->urut,'DBINVOICEPLDET');
    return 1;
  }





  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("


  declare @Tahun int, @Bulan int, @Periode Varchar(30)

  select @Tahun= :tahun , @Bulan= :bulan

  Set @Periode=CAST(@Tahun as varchar(4))+Case when @Bulan<10 then '0' else '' end+CAST(@Bulan as varchar(2))
  set nocount on
  Select distinct A.NoBukti,A.Tanggal,A.KodeCustSupp,
       D.NOBUKTI Noso,
       D.Tanggal TglSO,m1.NamaCustSupp, A.IsClose
  From dbo.DBSPB A  WITH(NOLOCK)
     Left Outer Join (select NoBukti,NoSO from dbo.dbSPBDet WITH(NOLOCK) group by NoBukti,NoSO) C on A.NoBukti=C.NoBukti
     left outer join dbo.DBSO D WITH(NOLOCK) on D.Nobukti=C.NoSO
     left outer join dbo.vwBrowsCustomer E WITH(NOLOCK)on E.Kodecust=A.KodeCustSupp and E.Sales=D.KODESLS
     left outer join dbo.dbInvoicePLDet F WITH(NOLOCK)on F.NoSPB=A.NoBukti
     Left Outer Join dbo.DbCustSupp M1 WITH(NOLOCK)on A.KodeCustSupp=M1.KodeCustsupp
      Left Outer join (Select x.NoBukti,x.NoSPP, Sum(x.QNT-ISNULL(y.qnt,0)) Qnt,
                      Sum(x.QNT2-ISNULL(y.Qnt2,0)) Qnt2 ,
                      SUM(case when x.NOSAT=1 then x.QNT-ISNULL(y.qnt,0) else x.QNT2-ISNULL(y.Qnt2,0) end)Qntx
                      from dbo.dbSPBDet x  WITH(NOLOCK)
                      left Outer join (Select x.NoSPB, x.UrutSPB, Sum(x.QNT) Qnt, Sum(x.QNT2) Qnt2
                                       From dbo.DBRSPBDet x WITH(NOLOCK)
                                       Group by x.NoSPB, x.UrutSPB) y on y.NoSPB=x.NoBukti and y.UrutSPB=x.Urut
                      group by x.NoBukti, x.NoSPP, y.NoSPB) B on B.NoBukti=A.NoBukti

  where Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                     Case when A.IsOtorisasi2=1 then 1 else 0 end+
                     Case when A.IsOtorisasi3=1 then 1 else 0 end+
                     Case when A.IsOtorisasi4=1 then 1 else 0 end+
                     Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                else 1
           end As Bit)=0 and F.NoSPB is null and CAST(YEAR(A.Tanggal) as varchar(4))+Case when month(A.Tanggal)<10 then '0' else '' end+CAST(month(A.Tanggal) as varchar(2))<=@Periode
  and (B.Qntx>0 )
  order by A.NoBukti

  " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);





  $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
  $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
  $filterip = $req->filterip ?: 0;
  $tempOutstanding2 = $this->queryInvoicePenjualanOtorisasi($tglawal, $tglakhir, $filterip);


  return ["tempOutstanding" => $tempOutstanding , "tempOutstanding2" => $tempOutstanding2];


  }

  public function getListSO (Request $req) {
    $noso = $req->noso;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $list = DB::connection('SML')->select("declare @NoBukti varchar(30), @Bulan int, @Tahun int

select @NoBukti= :noso , @Bulan= :bulan , @Tahun= :tahun

Select distinct A.Nobukti, A.Tanggal,D.NOBUKTI Noso, E.TANGGAL TGLSO,
       A.nobukti KeyUrut ,ISnull(A.FlagTipe,0) FlagTipe,A.KodeCUstSupp,E.NoPesanan ,D.PPNBRG
From DBSPB A
     Left Outer join dbSPBDet B on B.Nobukti=A.Nobukti
     Left Outer join dbBarang C on C.kodebrg=B.Kodebrg
     Left Outer join DBSODET D on B.NoSO=D.NOBUKTI and B.UrutSO=D.URUT
     Left Outer join DBSO E on D.NOBUKTI=E.NOBUKTI
     Left Outer join (Select x.NoSPB, x.UrutSPB, sum(Qnt) QntRSPB, sum(Qnt2) Qnt2RSPB,
                            SUM(x.NetW) NetWRSPB, SUM(x.GrossW) GrossWRSPB
                      from DBRSPBDet x
                      Group by  x.NoSPB, x.UrutSPB) H on H.NoSPB=B.NoBukti and H.UrutSPB=B.Urut
     Left Outer join (Select NoSPB,UrutSPB,SUM(QNT) Qnt,SUM(QNT2) Qnt2  from dbInvoicePLDet Group by NoSPB,UrutSPB) I on B.NoBukti=I.NoSPB and B.Urut=I.UrutSPB
Where D.NoBukti=@nobukti

and
      Case when B.Nosat=1 then B.Qnt
           when B.Nosat=2 then B.Qnt2
           when B.Nosat=3 then B.Qnt2
           else 0
      end-
      Isnull(Case when B.Nosat=1 then I.Qnt
           when B.Nosat=2 then I.Qnt2
           when B.Nosat=3 then I.Qnt2
           else 0
      end,0)-
      Isnull(Case when B.Nosat=1 then isnull(H.QntRSPB,0)
           when B.Nosat=2 then isnull(H.Qnt2RSPB,0)
           when B.Nosat=3 then I.Qnt2
           else 0
      end,0) >0
      and isnull(A.isotorisasi1,0)=1

--Group by A.Nobukti, A.Tanggal, D.NOBUKTI, E.TANGGAL,A.FlagTipe,A.KodeCUstSupp
Order by A.NoBukti

    " , ["noso" => $noso  ,  "bulan" => $periode->bulan , "tahun" =>$periode->tahun ]) ;



    return ["list" => $list ];
  }


  public function spOtorisasi ( Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update dbinvoicepl set IsOtorisasi1 = 1, OtoUser1 = :username , TglOto1 = getDate() , IsBatal = NULL, UserBatal = NULL , TglBatal = NULL , MaxOL = 1  where NoBukti = :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','INVC',$req->nobukti,'',0,'DBINVOICEPL');

    $values = [
      '',
      'DBINVOICEPL',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      1
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);


    return 1;
  }

  public function spBatalOtorisasi ( Request $req) {
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $res = DB::connection('SML')->update("update dbinvoicepl set IsBatal = 1, UserBatal = :username , TglBatal = GETDATE() , IsOtorisasi1 = 0, OtoUser1 = '' , TglOto1 = NULL , maxol = -1 where NoBukti = :nobukti ", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
   $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','INVC',$req->nobukti,$req->pket,0,'DBINVOICEPL');
     $values = [
      '',
      'DBINVOICEPL',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);

    return 1;
  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbinvoicepl set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

  public function onChangeDetail (Request $req) {
    $query = 'update dbinvoicepldet set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }






}
