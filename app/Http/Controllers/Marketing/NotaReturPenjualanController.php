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



class NotaReturPenjualanController extends Controller
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

    select b.PPN,A.NOURUT, A.NOBUKTI, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP, A.NoRPJ,A.Noinv

    from vwBrowsOutSPBRJual A
    left outer join DBCUSTSUPP b on a.KODECUSTSUPP =  b.KODECUSTSUPP
    where YEAR(a.tanggal) > 2024

    group by  A.NOURUT, A.NOBUKTI, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP, A.NoRPJ,A.Noinv, b.PPN
" );


    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding2 = $this->queryInvoiceOtorisasi($tglawal, $tglakhir, 0);

    return view('marketing.notareturpenjualan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Invoice
  // Otorisasi" -- dulu tabel2 (Belum, tombol Koreksi/Otorisasi/Delete) dan
  // tabel3 (Sudah, tombol Koreksi/Batal Otorisasi) adalah 2 tab terpisah dengan
  // query nyaris identik (cuma beda IsOtorisasi1=0/<>0). Digabung jadi satu
  // dengan filternrp yang menyaring di server, port 1:1 dari pola
  // queryOutstanding() milik PerintahReturJualController. Tabel "SPB Retur
  // Gudang" (tabel, bukan konsep otorisasi) sengaja tidak disentuh.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryInvoiceOtorisasi ($tglawal, $tglakhir, $filternrp) {
    return DB::connection("SML")->select("
      select * from (
        select  A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KODECUSTSUPP, A.NAMACUSTSUPP,
                A.Alamat, A.ALAMATKOTA, A.KOTA, A.NamaKota, A.NoInvoice, A.TglInvoice,
                A.NORPJ, A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.IDUser,
                Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
                A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
                A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
                A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
                A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
                A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
                A.NeedOtorisasi,
                A.NoJurnal, A.NoUrutJurnal, A.TglJurnal,
                A.IsFlag, A.MaxOL, A.IsCetak, A.CetakKe, A.KodeSls,
                A.IsBatal, A.UserBatal, A.TglBatal, A.Flagtipe, A.TipePPN
        from	vwTransInvoiceRPJ A
        where	A.Tanggal between ? and ?
        group by A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KODECUSTSUPP, A.NAMACUSTSUPP,
                A.Alamat, A.ALAMATKOTA, A.KOTA, A.NamaKota, A.NoInvoice, A.TglInvoice,
                A.NORPJ, A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.IDUser,
                A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
                A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
                A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
                A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
                A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
                A.NeedOtorisasi,
                A.NoJurnal, A.NoUrutJurnal, A.TglJurnal,
                A.IsFlag, A.MaxOL, A.IsCetak, A.CetakKe, A.KodeSls,
                A.IsBatal, A.UserBatal, A.TglBatal, A.Flagtipe, A.TipePPN
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) = 0)
         or (? = 2 and isnull(x.IsOtorisasi1,0) <> 0)
      order by x.NoBukti
    ", [$tglawal, $tglakhir, $filternrp, $filternrp, $filternrp]);
  }



  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select  *
from vwBrowsOutSPBRJual A

where YEAR(a.tanggal) > 2024 and nobukti = :noretur
order by A.TANGGAL, A.NoBUKTI"
, ["noretur" => $req->noretur]);
    return $listData;
  }


  public function listValas (Request $req) {

    $listData = DB::connection('SML')->select("SELECT kodevls, namavls, kurs FROM dbvalas");
    return $listData;
  }




  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select TT from DBNOMOR');


      $values = [
          $inisial[0]->TT,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
    } else {
      $values = [
          'IVRJN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    }


  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbinvoicerpj set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->valuex , "nobukti" => $req->nobukti]);
    if($req->field == 'PPN') {
      DB::connection('SML')->update('update dbinvoicerpjdet set PPN = :value where nobukti = :nobukti',["value" => $req->valuex , "nobukti" => $req->nobukti] );
    }
    return $res;

  }

  public function getDetailPenerimaan (Request $req) {
    $detailAdd = DB::connection("SML")->select("


    select  *
    from vwBrowsOutSPBRJual A
    where nobukti = :noretur



    " , [ "noretur" => $req->noretur]);

    $detailEdit = DB::connection("SML")->select("

    Select  A.*
    From vwTransINVOICERPJ A
    where  a.nobukti = :nobukti
    order By A.Urut
    " , [ "nobukti" => $req->nobukti ]);

        return [
          "detailAdd" => $detailAdd,
          "detailEdit" => $detailEdit,

        ];
  }

  public function getDetail (Request $req) {



//     select  *
// from vwBrowsOutSPBRJual A
// --where A.NOBUKTI = 'MGL/SPR/00004/0325'
// where YEAR(a.tanggal) > 2024
// order by A.TANGGAL, A.NoBUKTI
//




$detail = DB::connection("SML")->select("


select  a.* , b.PPN PPNCUST
from vwBrowsOutSPBRJual A
left outer join DBCUSTSUPP b on a.KODECUSTSUPP =  b.KODECUSTSUPP
where a.nobukti = :nobukti



" , [ "nobukti" => $req->nobukti]);

    return $detail;
  }


    public function getDetailNew (Request $req) {


      $listData = DB::connection('SML')->select("select  *
  from vwBrowsOutSPBRJual A

  where YEAR(a.tanggal) > 2024 and nobukti = :noretur
  order by A.TANGGAL, A.NoBUKTI"
  , ["noretur" => $req->nobukti]);
      // return $listData;

  $detail = DB::connection("SML")->select("


  select  a.* , b.PPN PPNCUST
  from vwBrowsOutSPBRJual A
  left outer join DBCUSTSUPP b on a.KODECUSTSUPP =  b.KODECUSTSUPP
  where a.nobukti = :nobukti



  " , [ "nobukti" => $req->nobukti]);

      return [
        "detail" => $detail ,
        "listData" => $listData

        ]
        ;
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


  // public function spAddAll (Request $req) {
  //
  //   // sp_TransInvoiceRPJ
  //   $choice = 'I';
  //   $nobukti = $req->nobukti;
  //
  //
  //   $tempData = $req->tempData;
  //
  //   foreach ($tempData as $d) {
  //     // code...
  //
  //     $values = [
  //       $req->choice,
  //       $req->nobukti,
  //       $req->nourut,
  //       $req->tanggal,
  //       $req->kodecustsupp,
  //       $req->noinvoice ? $req->noinvoice : '',
  //       $req->disc,
  //       $req->kodevalas,
  //       $req->kurs,
  //       $req->ppn, // 10
  //       $req->tipebayar,
  //       $req->hari,
  //       \Auth::User()->username,
  //       $req->urut,
  //       $req->kodebrg,
  //       $req->norpj,
  //       $req->urutrpj,
  //       $req->sat_1,
  //       $req->qnt,
  //       $req->qnt1, //20
  //       $req->qnt2,
  //       $req->nosat,
  //       $req->isi,
  //       $req->harga,
  //       $req->discp,
  //       $req->discrp,
  //       $req->disctot,
  //       '',
  //       $req->flagmenu,
  //       $req->flagtipe, // 30
  //       0,
  //       0,
  //       0,
  //       0,
  //       $req->tipeppn,
  //       $req->kodesls,
  //       $req->catatan
  //
  //     ];
  //
  //     $res = DB::connection('SML')->statement('exec sp_TransInvoiceRPJ ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
  //
  //
  //
  //
  //
  //
  //   }
  //
  //
  //         return 1;
  //
  //
  // }



public function spAdd (Request $req) {

// sp_TransInvoiceRPJ
$choice = $req->choice;
$tipeform = $req->tipeform;
$nobukti = $req->nobukti;
$xurut=0;





//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBINVOICERPJdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBINVOICERPJdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            //  return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            //  return 'mmm';
            $xurut = $req->urut;
        }
        
    }else{
        //  return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;

    if ($choice=='D'){
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'IVRJ',$nobukti,'',$xurut,'DBINVOICERPJ');

    }



if ($choice == 'I' && $tipeform == 'add' ) {
  $check = DB::connection('SML')->select('select * from DBINVOICERPJ where Nobukti = :nobukti',["nobukti" => $nobukti]);
    if ($check) {
      return 2;
  }
}



$values = [
  $req->choice,
  $req->nobukti,
  $req->nourut,
  $req->tanggal,
  $req->kodecustsupp,
  $req->noinvoice ? $req->noinvoice : '',
  $req->disc,
  $req->kodevalas,
  $req->kurs,
  $req->ppn, // 10
  $req->tipebayar,
  $req->hari,
  \Auth::User()->username,
  $req->urut,
  $req->kodebrg,
  $req->norpj,
  $req->urutrpj,
  $req->sat_1,
  $req->qnt,
  $req->qnt1, //20
  $req->qnt2,
  $req->nosat,
  $req->isi,
  $req->harga,
  $req->discp,
  $req->discrp,
  $req->disctot,
  '',
  $req->flagmenu,
  $req->flagtipe, // 30
  0,
  0,
  0,
  0,
  $req->tipeppn,
  $req->kodesls,
  $req->catatan

];

$res = DB::connection('SML')->statement('exec sp_TransInvoiceRPJ ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);

if ($choice !='D'){

  //  return [$req->choice,'IVR',$nobukti,'',$xurut,'DBINVOICERPJ'];
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($req->choice,'IVR',$nobukti,'',$xurut,'DBINVOICERPJDET');

    }




return 1;


}

public function spAddAllNew (Request $req) {


  $listData = DB::connection('SML')->select("select  *
from vwBrowsOutSPBRJual A

where YEAR(a.tanggal) > 2024 and nobukti = :noretur
order by A.TANGGAL, A.NoBUKTI"
, ["noretur" => $req->nobukti]);
  // return $listData;

 $detail = DB::connection("SML")->select("


select  a.* , b.PPN PPNCUST
from vwBrowsOutSPBRJual A
left outer join DBCUSTSUPP b on a.KODECUSTSUPP =  b.KODECUSTSUPP
where a.nobukti = :nobukti



" , [ "nobukti" => $req->nobukti]);
$xppn =0 ;
if ($detail) {
  if ($detail[0]->PPNCUST) {
    $xppn = $detail[0]->PPNCUST;
  }

} else {
  return [
    "status" => 2
  ];
}

$username = \Auth::user()->username;
$periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($xppn == 1) {
      $inisial = DB::connection("SML")->select('select TT from DBNOMOR');


      $values = [
          $inisial[0]->TT,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      // return $noBukti;
    } else {
      $values = [
          'IVRJN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      // return $noBukti;

    }

    $choice = 'I';
    // $tempData = $req->tempData;
    // return $tempData;

    // $tipeform = $req->tipeform;
    // $nobukti = $req->nobukti;
    // if ($choice == 'I' && $tipeform == 'add' ) {
      // $check = DB::connection('SML')->select('select * from DBINVOICERPJ where Nobukti = :nobukti',["nobukti" => $nobukti]);
      //   if ($check) {
      //     return 2;
      // }
    // }

    foreach ($detail as $d)  {

     $values = [
      $choice,
      $noBukti[0]->Nobukti,
      $noBukti[0]->Nourut,
      date('Y-m-d'),
      $listData[0]->KODECUSTSUPP,
      $listData[0]->Noinv ? $listData[0]->Noinv : '',
      0,
      $listData[0]->Valas,
      $listData[0]->Kurs,
      $xppn, // 10
      $listData[0]->HARI > 0 ? 1 : 0,
      $listData[0]->HARI ? $listData[0]->HARI : 0 ,
      \Auth::User()->username,
      0,
      $d->KODEBRG,
      $listData[0]->NOBUKTI,
      $d->URUT,
      $d->SAT1,
      $d->QntSisa,
      $d->Qnt1Sisa, //20
      $d->Qnt2Sisa,
      $d->NoSat,
      $d->Isi,
      $d->HARGA,
      0,
      0,
      0,
      '',
      0,
      $xppn != 0 ? 1 : 0, // 30
      0,
      0,
      0,
      0,
      $xppn,
      $listData[0]->kodeSls,
      ''

    ];

    // return $values;

    $res = DB::connection('SML')->statement('exec sp_TransInvoiceRPJ ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);


  }



    return [
      "status" => 1 ,
      "nobukti" => $noBukti[0]->Nobukti

    ];


}

public function spDeleteAll (Request $req) {

  DB::connection('SML')->update("delete from DBINVOICERPJDet where NoBukti = :nobukti
  delete from DBINVOICERPJ where NoBukti = :nobukti1
",["nobukti" => $req->nobukti , "nobukti1" => $req->nobukti]);
  return 1;

}


  public function spAddAll (Request $req) {

    // sp_TransInvoiceRPJ
    $choice = 'I';
    $tempData = $req->tempData;
    // return $tempData;

    // $tipeform = $req->tipeform;
    $nobukti = $req->nobukti;
    // if ($choice == 'I' && $tipeform == 'add' ) {
      $check = DB::connection('SML')->select('select * from DBINVOICERPJ where Nobukti = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    // }

    foreach ($tempData as $d)  {

    $values = [
      $choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal,
      $req->kodecustsupp,
      $req->noinvoice ? $req->noinvoice : '',
      0,
      $req->kodevalas,
      $req->kurs,
      $req->ppn, // 10
      $req->tipebayar,
      $req->hari,
      \Auth::User()->username,
      0,
      $d['KODEBRG'],
      $req->norpj,
      $d['URUT'],
      $d['SAT1'],
      $d['QntSisa'],
      $d['Qnt1Sisa'], //20
      $d['Qnt2Sisa'],
      $d['NoSat'],
      $d['Isi'],
      $d['HARGA'],
      0,
      0,
      0,
      '',
      $req->flagmenu,
      $req->flagtipe, // 30
      0,
      0,
      0,
      0,
      $req->tipeppn,
      $req->kodesls,
      $req->catatan ? $req->catatan : ''

    ];

    // return $values;

    $res = DB::connection('SML')->statement('exec sp_TransInvoiceRPJ ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);


  }



    return 1;


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

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('D','IVRJ',$nobukti,'',$req->urut,'DBINVOICERPJ');


    return 1;
  }





  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("


    select b.PPN,A.NOURUT, A.NOBUKTI, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP, A.NoRPJ,A.Noinv

    from vwBrowsOutSPBRJual A
    left outer join DBCUSTSUPP b on a.KODECUSTSUPP =  b.KODECUSTSUPP
    where YEAR(a.tanggal) > 2024

    group by  A.NOURUT, A.NOBUKTI, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP, A.NoRPJ,A.Noinv, b.PPN

" );


    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filternrp = $req->filternrp ?: 0;
    $tempOutstanding2 = $this->queryInvoiceOtorisasi($tglawal, $tglakhir, $filternrp);

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

    $res = DB::connection('SML')->update("update DBINVOICERPJ set IsOtorisasi1 = 1, OtoUser1 = :username , TglOto1 = getDate() , IsBatal = NULL, UserBatal = NULL , TglBatal = NULL , MaxOL = 1  where NoBukti = :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','IVRJ',$req->nobukti,'',0,'DBINVOICERPJ');
    
    $values = [
      '',
      'DBINVOICERPJ',
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
    $res = DB::connection('SML')->update("update DBINVOICERPJ set IsBatal = 1, UserBatal = :username , TglBatal = GETDATE() , IsOtorisasi1 = 0, OtoUser1 = '' , TglOto1 = NULL , maxol = -1 where NoBukti = :nobukti ", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','IVRJ',$req->nobukti,$req->pket,0,'DBINVOICERPJ');
    
    $values = [
      '',
      'DBINVOICERPJ',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
     DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    
    
     return 1;
  }

  public function cekKreditHari (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $listData = DB::connection('SML')->select("select hari from dbcustsupp where KODECUSTSUPP = :kodepelanggan", ["kodepelanggan" => $req->kodepelanggan]);
    return $listData;
  }

  public function onChangeDetail (Request $req) {
    $query = 'update dbinvoicepldet set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }






}
