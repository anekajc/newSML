<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\App\Model\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;



class InvoiceJasaController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041040';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $listBarangAll = DB::connection('SML')->select("select Kodebrg , Namabrg , sat1 , isi1 from dbbarang where isjasa = 1 and IsAktif = 1" );

    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryInvoiceJasaOtorisasi($tglawal, $tglakhir, 0);




    return view('marketing.invoicejasa' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses,
      "listBarangAll" => $listBarangAll
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() -- dulu tabel (Belum
  // Otorisasi, tombol Koreksi/Otorisasi) dan tabel2 (Sudah Diotorisasi, tombol
  // Batal Otorisasi) adalah 2 tab terpisah dengan query nyaris identik (cuma
  // beda IsOtorisasi1=0/1). Digabung jadi satu dengan filterij yang menyaring
  // di server, port 1:1 dari pola queryOutstanding() milik
  // PerintahReturJualController.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryInvoiceJasaOtorisasi ($tglawal, $tglakhir, $filterij) {
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
                ,Isnull(A.IsBatal,0) Isbatal,A.userBatal,A.tglBatal, A.NoPajak,E.NOBUKTI NOSO,E.TANGGAL TGLSO
        from	dbInvoicePL A
        Left Outer join (Select x.NoBukti, x.NoSPB
                         from dbInvoicePLDet x
                         Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
        LEFT OUTER JOIN (SELECT NOBUKTI,NoSO FROM dbSPBDet GROUP BY NoBukti,NoSO) C ON B.NoSPB=c.NoBukti
        LEFT OUTER JOIN dbSPB D ON C.NoBukti=D.NoBukti
        left Outer join dbSO E on E.Nobukti=C.NoSO
        left outer join DBCUSTSUPP F on F.KODECUSTSUPP=A.KodeCustSupp
        where	A.Tanggal between ? and ?
        and  isnull(A.pJasa,0)=1
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) = 0)
         or (? = 2 and isnull(x.IsOtorisasi1,0) <> 0)
      order by x.NoBukti
    ", [$tglawal, $tglakhir, $filterij, $filterij, $filterij]);
  }




    public function getNoBukti (Request $req) {

      $username = \Auth::user()->username;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

      if ($req->ppn == 1) {

        $inisial = DB::connection("SML")->select('select INVJ from DBNOMOR');

        $values = [
            $inisial[0]->INVJ,
            $periode->bulan,
            $periode->tahun,
            $username,
            // $periode
            // $periode
        ];
        $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
        return $noBukti;

      } else {
        $values = [
            'INVJN',
            $periode->bulan,
            $periode->tahun,
            $username,
            // $periode
            // $periode
        ];
        $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
        return $noBukti;

      }




    }


    public function listCustomer (Request $req) {

      $listData = DB::connection('SML')->select("select kodecustsupp , replace(namacustsupp , '''' , ' ') namacustsupp, replace(alamat1 , '''' , ' ') alamat1, hari, ppn PPNCUST from DBCUSTSUPP where JENIS = 1 and IsAktif = 1");
      return $listData;
    }

    public function listSales (Request $req) {

      $listData = DB::connection('SML')->select("SELECT keynik, nama FROM dbkaryawan where IsSales = 1");
      return $listData;
    }

    public function listLokasiPenerima (Request $req) {

      $listData = DB::connection('SML')->select("select kodekebun, nama from DBKEBUNCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
      return $listData;
    }




  public function spDetail (Request $req) {


    $nobukti = $req->nobukti;






    $list = DB::connection("SML")->select("
declare @NoBukti varchar(30)

select 	@NoBukti= :nobukti

Select g.hari harikredit,A.NoBukti,A.NoUrut,A.Tanggal,B.NoSPP, B.TglSPP,B.NoSO, B.TglSO,A.KodeCustSupp,A.KODESLS,I.Nama NamaSls,
       A.Valas,A.Kurs,A.IsLokal,
       A.Consignee,A.NotifyParty,A.PONo,
       A.PaymentTerm, A.PoL,A.PoD,A.NameOfVessel,
       A.ShipOnBoardDate,A.Packing,
       B.Urut,B.UrutSPB,B.KodeBrg,A.PPN,E.DISC,B.QNT QNT1,B.QNT2,A.TIPEBAYAR, A.HARI,
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
            Else 0
       end Qnt,
       Case when B.Nosat=1 then B.Sat_1
            when B.Nosat=2 then B.Sat_2
            Else ''
       end Satuan, B.NoSPB,E1.NoAlamatKirim, J.Nama, J.Alamat AlamatX,J.Telp, J.Fax,
       Case when A.Valas='IDR' then B.SubTotalRp  else B.SubTotal end Total,
       Case when A.Valas='IDR' then K.TotDiskonRp  else K.TotDiskon end Diskon,
       Case when A.Valas='IDR' then K.TotDPPRp  else K.TotDPP end TotalDPP,
       Case when A.Valas='IDR' then K.TotPPnRp  else K.TotPPn end TotalPPn,
       Case when A.Valas='IDR' then K.TotNetRp  else K.TotNet end TotalNetto,
       B.UrutTrans,isnull(A.NuangMuka,0) nUangMuka,E1.NoPesanan,A.KodeLokasi KodeKebun,m4.nama NamaKebun
From dbInvoicePL A
     left outer join dbInvoicePLDet B on B.NoBukti=A.NoBukti
     left Outer join dbSPBDet C on B.NoSPB=C.NoBukti and B.UrutSPB=C.Urut
     Left Outer join dbSPB D on C.NoBukti=D.NoBukti
     Left outer join DBSODET E on C.NoSO=E.NOBUKTI and C.UrutSO=E.URUT
     Left Outer join DBSO E1 on E.NOBUKTI=E1.NOBUKTI
     left outer join DBBARANG F on F.KODEBRG=B.KodeBrg
     --left outer join vwBrowsCustomer G on G.Kodecust=A.kodeCustSupp and G.Sales=E.KODESLS
     left outer join DBCUSTSUPP G on G.KODECUSTSUPP=A.KodeCustSupp
     left Outer join dbkaryawan I on I.KeyNik=A.KODESLS
     left Outer join dbValas H on H.kodevls=A.Valas
     Left Outer join dbAlamatCust J on J.KodecustSupp=A.KodecustSupp and J.Nomor=E1.NoAlamatKirim
     Left Outer join vwRpDetInvoicePL K on K.nobukti=A.Nobukti
     lEFT OUTER JOIN DBKEBUNCUSTSUPP M4 ON A.KODELokasi=M4.KODEKEBUN  and A.KodeCustSupp=M4.KodeCustSupp
where A.NoBukti=@nobukti
Order by B.Urut




    " , [ "nobukti" => $nobukti ]);



    return $list;


  }



  public function spAdd (Request $req) {

   $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from dbinvoicepldet where Nobukti = :nobukti', ['nobukti' => $req->nobukti]);
    if ($purut){

        if ($req->choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from dbinvoicepldet where Nobukti = :nobukti', ['nobukti' => $req->nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            // return 'mmm';
            $xurut = $req->urut;
        }
        
    }else{
         //return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;






    if ($req->choice == "I" && $req->jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbinvoicepl where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

    $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->nobukti,
        $req->kodecustomer,
        '',
        '',
        '',
        $req->nopo, // 10
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
        0 , // urutspb
        $req->kodebarang,
        $req->tipeppn,
        0 ,
        $req->kurs,
        $req->qty,
        $req->qty,
        $req->satuan, // sat
        $req->satuan,
        $req->nosat, // 30
        $req->isi,
        0,
        0,
        $req->harga,
        0, // discp1
        0,
        0,
        $req->keterangan,
        $req->valas,
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
        $req->catatan, // footnote
        '',
        $req->namabarang,
        '',
        '',
        '',
        '',
        0,
        0, //60
        0, //60
        0,
        $req->flagtipe, // flagtipe
        '',
        1, //pjasa
        '',
        $req->kodelokasipenerima, //kodelokasi
        $req->hari,
        $req->pembayaran,
        $req->ppnbrg, // 70
        $req->kodesales,
        $req->uangmuka
    ];

    $response = DB::connection('SML')->statement('exec SP_InvoicePL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    //  return [$req->choice,'IVJ',$req->nobukti,'',$xurut,'DBINVOICEPLDET'];
    
    // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'IVJ',$req->nobukti,'',$xurut,'DBINVOICEPLDET');
    return 1;
  }



  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterij = $req->filterij ?: 0;
    $tempOutstanding = $this->queryInvoiceJasaOtorisasi($tglawal, $tglakhir, $filterij);

    return ["tempOutstanding" => $tempOutstanding];
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


  public function spOtorisasi ( Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update dbinvoicepl set IsOtorisasi1 = 1, OtoUser1 = :username , TglOto1 = getDate() , IsBatal = NULL, UserBatal = NULL , TglBatal = NULL , MaxOL = 1  where NoBukti = :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','IVJ',$req->nobukti,'',0,'DBINVOICEPL');

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
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','IVJ',$req->nobukti,'',0,'DBINVOICEPL');
    

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


  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?,?,?,?,?,?,?,?,?,?,
  // ?



}
