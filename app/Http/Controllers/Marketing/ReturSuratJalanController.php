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

// use App\Http\Controllers\NewMenuController;

class ReturSuratJalanController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041015';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryReturSuratJalanOtorisasi($tglawal, $tglakhir, 0);

    return view('marketing.retursuratjalan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() -- dulu tabel (Belum
  // Otorisasi, tombol Koreksi/Otorisasi) dan tabel2 (Sudah Otorisasi, tanpa
  // Koreksi) adalah 2 tab terpisah dengan query nyaris identik (cuma beda
  // IsOtorisasi1<>1/=1). Digabung jadi satu dengan filterrsj yang menyaring di
  // server, port 1:1 dari pola queryOutstanding() milik
  // PerintahReturJualController.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryReturSuratJalanOtorisasi ($tglawal, $tglakhir, $filterrsj) {
    return DB::connection('SML')->select("
      select * from (
        select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPB, C.Tanggal TglSPB, A.KODECUSTSUPP, B.NamaCust NamaCustSupp,
                A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser,
                C.Tipe, A.IsFlag,
                Case when A.isFlag=0 then 'Retur Surat Jalan Barang Jadi'
                     when A.isFlag=1 then 'Retur Surat Jalan Bahan Baku dan Lain-lain'
                     else ''
                end MyTipe,
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
        from	dbRSPB A
        left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp
        left Outer join (Select x.NoBukti, x.Tanggal, 'Ekpsor' Tipe
                         from DBSPB x
                         ) C on C.NoBukti=A.NoSPB
        where	A.Tanggal between ? and ?
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) <> 1)
         or (? = 2 and isnull(x.IsOtorisasi1,0) = 1)
      order by x.NOBUKTI
    ", [$tglawal, $tglakhir, $filterrsj, $filterrsj, $filterrsj]);
  }

  public function getDetail (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $nobukti = $req->nobukti;
    $header = DB::connection('SML')->select("
    declare @Tahun int, @Bulan int

    select @Tahun= :tahun , @Bulan= :bulan

    select  A.NOBUKTI, A.NOURUT, A.TANGGAL, AA.NOSPB, C.Tanggal TglSPB, A.KODECUSTSUPP, B.NamaCust NamaCustSupp,
            A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser,
            C.Tipe, A.IsFlag,
            Case when A.isFlag=0 then 'Retur Surat Jalan Barang Jadi'
                 when A.isFlag=1 then 'Retur Surat Jalan Bahan Baku dan Lain-lain'
                 else ''
            end MyTipe,
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
    from	dbRSPB A
    left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp
    left outer join (select NoBukti,NoSPB
    				 from DBRSPBDet
    				 group by NoBukti,NoSPB) AA on a.NoBukti=aa.NoBukti
    left Outer join (Select x.NoBukti, x.Tanggal, 'Ekpsor' Tipe
                     from DBSPB x
                     ) C on C.NoBukti=A.NoSPB
    where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and A.nobukti = :nobukti
    order by A.NoBukti
", ["tahun" => $periode->tahun,
"bulan" => $periode->bulan, "nobukti" => $nobukti ]);

$detail = DB::connection('SML')->select("
select 	B.NOBUKTI, B.URUT, B.NoSPB NoSC, B.UrutSPB UrutSC, B.KODEBRG, C.NAMABRG, '' Jns_Kertas, '' Ukr_Kertas,
        B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, '' KetDetail ,B.NOSAT , b.ISI , b.FlagKembali
from	dbRSPBDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
where	B.NoBukti= :nobukti
order by B.Urut
", ["nobukti" => $nobukti]);


$dataForm = [];

if ($header) {
  $dataForm = DB::connection('SML')->select("
  Select  Distinct A.NoBukti NoSPB, A.Tanggal TglSPB, B.NoSO NoSC, B1.TANGGAL TglSC,0 islokal,
                        A.KodeCustSupp,c.NAMACUSTSUPP , c.ALAMAT1+ '\n' +D.NamaKota Alamat ,A.FlagTipe,a.NoPolKend,B.Kodegdg,D1.Nama
                        from dbSPB a
                        Left Outer Join (Select Nobukti,NoSO,Kodegdg from dbSPBDet Group By NoBukti,NoSO,Kodegdg) B on  A.NoBukti=B.NoBukti
                        Left Outer join DBSO b1 on B.NoSO=b1.NOBUKTI
                        left Outer join DBCUSTSUPP c on c.KODECUSTSUPP=A.KodeCustSupp
                        Left Outer join DBKOTA d on C.Kota=D.KodeKota
                        Left Outer join DBGudang d1 on d1.Kodegdg=B.KodeGdg
                         LEFT OUTER JOIN dbSPBDet A1 ON A1.NoBukti=A.NOBUKTI
                       LEFT OUTER JOIN (Select a.NoBukti ,A.Urut
                from dbSPBDet a
                left outer join (select NoSPB,UrutSPB,Sum(Qnt) QntInv
                from dbInvoicePLDet
                group by NoSPB,UrutSPB ) b on a.NoBukti=b.NoSPB and A.Urut=b.UrutSPB
                --where A.QNT - ISNULL(b.QntInv,0)=0
                group by a.NoBukti,A.Urut) F ON A1.NoBukti=F.NoBukti AND A1.Urut=F.Urut
                        where /*F.NoBukti IS NULL and*/ year(a.tanggal)>=2020 and a.nobukti = :nobukti
                              Order by A.NoBukti
  ", ["nobukti" => $header[0]->NOSPB]);
}





    return [
      "header" => $header,
      "detail" => $detail,
      "dataForm" => $dataForm
    ];
  }

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select RSPB from DBNOMOR');

      $values = [
          $inisial[0]->RSPB,
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
          'RSPBN',
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




  public function listSJ (Request $req) {

        $listData = DB::connection('SML')->select("  Select  Distinct A.NoBukti NoSPB, A.Tanggal TglSPB, B.NoSO NoSC, B1.TANGGAL TglSC,0 islokal, c.PPN PPNCUST,
                              A.KodeCustSupp,c.NAMACUSTSUPP , c.ALAMAT1+ '\n' +D.NamaKota Alamat ,A.FlagTipe,a.NoPolKend,B.Kodegdg,D1.Nama
                              from dbSPB a
                              Left Outer Join (Select Nobukti,NoSO,Kodegdg from dbSPBDet Group By NoBukti,NoSO,Kodegdg) B on  A.NoBukti=B.NoBukti
                              Left Outer join DBSO b1 on B.NoSO=b1.NOBUKTI
                              left Outer join DBCUSTSUPP c on c.KODECUSTSUPP=A.KodeCustSupp
                              Left Outer join DBKOTA d on C.Kota=D.KodeKota
                              Left Outer join DBGudang d1 on d1.Kodegdg=B.KodeGdg
                               LEFT OUTER JOIN dbSPBDet A1 ON A1.NoBukti=A.NOBUKTI
                             LEFT OUTER JOIN (Select a.NoBukti ,A.Urut
        		    			from dbSPBDet a
        		    			left outer join (select NoSPB,UrutSPB,Sum(Qnt) QntInv
        		    			from dbInvoicePLDet
        		    			group by NoSPB,UrutSPB ) b on a.NoBukti=b.NoSPB and A.Urut=b.UrutSPB
        		    			where A.QNT - ISNULL(b.QntInv,0)=0
        		    			group by a.NoBukti,A.Urut) F ON A1.NoBukti=F.NoBukti AND A1.Urut=F.Urut
                              where F.NoBukti IS NULL and year(a.tanggal)>=2020 and a.KodeCustSupp = :kodecustsupp and a.IsOtorisasi1 = 1
                                    Order by A.NoBukti" , ["kodecustsupp" => $req->kodecustsupp] );
        return $listData;
  }

  public function listCustSuppBaru (Request $req)
  {

    $listData = DB::connection('SML')->select("Select  Distinct
                              A.KodeCustSupp,c.NAMACUSTSUPP , c.ALAMAT1+ '\n' +D.NamaKota Alamat
                              from dbSPB a
                              Left Outer Join (Select Nobukti,NoSO,Kodegdg from dbSPBDet Group By NoBukti,NoSO,Kodegdg) B on  A.NoBukti=B.NoBukti
                              Left Outer join DBSO b1 on B.NoSO=b1.NOBUKTI
                              left Outer join DBCUSTSUPP c on c.KODECUSTSUPP=A.KodeCustSupp
                              Left Outer join DBKOTA d on C.Kota=D.KodeKota
                              Left Outer join DBGudang d1 on d1.Kodegdg=B.KodeGdg
                               LEFT OUTER JOIN dbSPBDet A1 ON A1.NoBukti=A.NOBUKTI
                             LEFT OUTER JOIN (Select a.NoBukti ,A.Urut
        		    			from dbSPBDet a
        		    			left outer join (select NoSPB,UrutSPB,Sum(Qnt) QntInv
        		    			from dbInvoicePLDet
        		    			group by NoSPB,UrutSPB ) b on a.NoBukti=b.NoSPB and A.Urut=b.UrutSPB
        		    			where A.QNT - ISNULL(b.QntInv,0)=0
        		    			group by a.NoBukti,A.Urut) F ON A1.NoBukti=F.NoBukti AND A1.Urut=F.Urut
                              where F.NoBukti IS NULL and year(a.tanggal)>=2020 and a.IsOtorisasi1 = 1
                                    group by A.KodeCustSupp,c.NAMACUSTSUPP ,  Alamat , c.ALAMAT1, D.NamaKota");

        return $listData;
  }

  public function listBarang (Request $req) {

        $listData = DB::connection('SML')->select("Select a.nobukti, a.urut, a.kodebrg, b.Namabrg, a.Namabrg NamabrgKom,
                            Case when a.nosat=1 then a.QntSisa
                                 when a.nosat=2 then a.Qnt2Sisa
                                 when a.nosat=3 then a.Qnt2Sisa
                                 else 0
                            end Qty,
                            Case when a.nosat=1 then a.Sat_1
                                 when a.nosat=2 then a.Sat_2
                                 when a.nosat=3 then a.Sat_2
                                 else ''
                            end Satuan,
                      a.Sat_1, a.sat_2, a.nosat, a.isi, a.qnt, a.qnt2, a.qntRSPB, a.Qnt2RSPB,
                            a.QntSisa, A.qnt2Sisa, a.netw, a.GrossW,B.NFix
                      from vwBrowsOutspB_RSPB a
                          left Outer join (Select Kodebrg, Namabrg,Nfix from DBBARANG) b on b.KODEBRG=a.kodebrg
                      LEFT OUTER JOIN (Select a.NoBukti ,A.Urut
		     			from dbSPBDet a
		     			left outer join (select NoSPB,UrutSPB,Sum(Qnt) QntInv
		     			from dbInvoicePLDet
		     			group by NoSPB,UrutSPB ) b on a.NoBukti=b.NoSPB and A.Urut=b.UrutSPB
		     			where A.QNT - ISNULL(b.QntInv,0)=0
		     			group by a.NoBukti,A.Urut) F ON A.NoBukti=F.NoBukti AND A.Urut=F.Urut
                      Where a.nobukti= :nobukti  AND F.NoBukti IS NULL
                      Order by a.nobukti,a.urut" , ["nobukti" => $req->nobukti] );
        return $listData;
  }

  public function spAdd (Request $req) {
    if ($req->choice == "I" && $req->isempty == 0) {
      $check = DB::connection('SML')->select('select * from dbrspb where Nobukti = :nobukti', ["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }
    $username = \Auth::user()->username;

    $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->nosj,
        $req->kodecustsupp,
        $req->nopol,
        $req->container,
        $req->nocontainer,
        $req->noseal,
        $req->catatan,
        $req->urut,
        $req->urutsj,
        $req->kodebrg,
        $req->qnt,
        $req->qnt2,
        $req->sat1,
        $req->sat2,
        $req->nosat,
        $req->isi,
        $req->netw ? $req->netw : 0,
        $req->grossw ? $req->grossw : 0,
        $username,
        $req->isempty,
        $req->namabrg,
        $req->flagmenu,
        $req->flagtipe,
        $req->retursupp,

    ];




    DB::connection('SML')->statement('exec Sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;

  }

  public function spDataTableDetail (Request $req) {
    $list = DB::connection('SML')->select("select * from dbinvoicePldet where nobukti = :nobukti " , [ "nobukti" => $req->nobukti]);
    if (!$list) {

      return $list;
    }

    return $list;
  }


  public function spOtorisasi ( Request $req) {
    $res = DB::connection('SML')->update("update dbrSPB set IsOtorisasi1 = 1, OtoUser1 = :username , TglOto1 = getDate(), MaxOL = 1  where NoBukti = :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
    return 1;
  }

  public function spBatalOtorisasi ( Request $req) {
    $res = DB::connection('SML')->update("update dbrSPB set IsOtorisasi1 = 0, OtoUser1 = '' , TglOto1 = NULL , maxol = -1 where NoBukti = :nobukti ", [ "nobukti" => $req->nobukti ]);
    app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'RSJ', $req->nobukti, $req->pket, 0, 'dbrSPB');
    return 1;
  }


  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterrsj = $req->filterrsj ?: 0;
    $tempOutstanding = $this->queryReturSuratJalanOtorisasi($tglawal, $tglakhir, $filterrsj);

    return ["tempOutstanding" => $tempOutstanding];
  }

  public function loadHeader(Request $req) {
    $header = DB::connection('SML')->select('select * from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
    ]);

    return $header;
  }

  public function simpanHeader(Request $req) {
    DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
    ]);

    DB::connection('SML')->insert('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
      'header' => $req->header,
      'issubtotal' => $req->issubtotal,
      'isgrandtotal' => $req->isgrandtotal,
    ]);

    return 1;
  }





}
