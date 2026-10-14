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

class ReturPenjualanGudangController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04105';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $tulisan = $req->get('tulisan');
    // $menumenus = $req->get('menul0');


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


//     $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
// B.NOBUKTI, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,
//         B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, '' KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX
// from	dbPRRJualDet B
// left outer join dbBarang C on C.KodeBrg=B.KodeBrg
// left outer join dbPRRJual a on b.NoBukti=a.NoBukti
//
// left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
//  where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun
// ",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

[$tglawalprj, $tglakhirprj] = $this->rentangPeriode($periode);
$tempOutstanding = $this->queryOutstanding($tglawalprj, $tglakhirprj);

$tglawal = $tglawalprj;
$tglakhir = $tglakhirprj;
$tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir, 0, 0);

    return view('marketing.returpenjualangudang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tglawalperiode" => $tglawalprj,
      "tglakhirperiode" => $tglakhirprj,
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "akses" => $akses
    ]);

  }

  // Awal & akhir bulan periode kerja aktif. Dulu Carbon::now()->month(bulan): tahunnya ikut
  // tahun berjalan (bukan tahun periode) dan pada tanggal 29-31 bulannya meluap ke bulan
  // berikutnya (mis. 31 Jan -> month(2) = 3 Maret).
  private function rentangPeriode ($periode) {
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  private function headerSPR ($nobukti) {
    $cek = DB::connection('SML')->select("select NoBukti, isnull(IsOtorisasi1,0) IsOtorisasi1 from dbSPBRJual where NoBukti = cast(? as varchar(100))", [$nobukti]);
    return $cek ? $cek[0] : null;
  }

  // SPR yang sudah ditarik ke Nota Retur Penjualan (DBINVOICERPJDet.NOSPR). Nota Retur hanya
  // mengambil SPR yang sudah diotorisasi (vwBrowsOutSPBRJual: NilaiOL=MaxOL), jadi SPR seperti
  // ini tidak boleh dibatalkan otorisasinya - pola sama dengan PRJ yang sudah dipakai gudang.
  private function sudahDipakaiNotaRetur ($nobukti) {
    $cek = DB::connection('SML')->select("select top 1 1 ada from DBINVOICERPJDet where NOSPR = cast(? as varchar(100))", [$nobukti]);
    return count($cek) > 0;
  }

  // Penjaga Edit/Hapus item & ubah header: SPR harus masih ada dan belum diotorisasi
  // (bisa saja user lain mengotorisasi saat form Edit masih terbuka).
  private function tolakJikaTidakBisaDiubah ($nobukti) {
    $header = $this->headerSPR($nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data SPR " . $nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "SPR " . $nobukti . " sudah diotorisasi, tidak bisa diubah"];
    }
    return null;
  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Outstanding PRJ"
  // -- dulu difilter fix ke periode aktif (YEAR/MONTH(A.Tanggal)=periode), sekarang
  // pakai filter rentang tanggal spy halaman ini punya filter periode tanggal juga,
  // sama seperti "Transaksi Retur Gudang" (queryPenerimaan()).
  private function queryOutstanding ($tglawal, $tglakhir) {
    return DB::connection("SML")->select("
select distinct d.PPN,A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, A.NAMACUSTSUPP, A.NamaKota,
	A.NoRPJ, A.NoSO, A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,A.Noinv,A.KodeGdg
from vwTransPRRJual A
Left Outer Join (  SELECT A.NOBUKTI
				 FROM dbPRRJualDet A
				 LEFT OUTER JOIN (select noprrjual,urutprrjual,sum(QNT1)Qnt1,SUM(QNT2) Qnt2
                                                from dbSPBRJualDet  group by noprrjual,urutprrjual
						) B on A.NoBukti=B.NoprRJual AND A.Urut=B.URUTPRRJUAL
				 WHERE ISNULL(A.QNT1,0)-ISNULL(B.Qnt1,0) >0
				 GROUP BY A.NoBukti
				)B ON A.NoBukti=B.NoBukti
left outer join DBCUSTSUPP d on a.KodeCustSupp = d.KODECUSTSUPP
where A.Tanggal >= :tglawal and A.Tanggal < dateadd(day, 1, cast(:tglakhir as datetime))
and A.Kodegdg In(select KodeGdg from DBPemakaiGdg where UserId=:user)
and NeedOtorisasi=0
AND B.NoBukti IS NOT NULL
order by A.NoBukti", ["tglawal" => $tglawal, "tglakhir" => $tglakhir, "user" => \Auth::User()->username]);
  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Transaksi Retur
  // Gudang" -- dulu ada 2 salinan query nyaris identik ($tempPenerimaan = belum
  // otorisasi, $tempPenerimaan2 = sudah otorisasi) yang dirender di tab terpisah.
  // Digabung jadi satu tabel dengan filterstatus (status invoice) dan filteroto (status
  // otorisasi) sebagai dua filter independen yang di-AND, port 1:1 dari pola yang sama
  // dipakai PerintahReturJualController::queryOutstanding() (dua dropdown terpisah,
  // bukan satu dropdown gabungan yang saling eksklusif). $tempOutstanding (tab
  // "Outstanding PRJ") sengaja tidak disentuh.
  //   filterstatus: 0 = Semua, 1 = Belum, 2 = Sebagian, 3 = Selesai
  //   filteroto:    0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryPenerimaan ($tglawal, $tglakhir, $filterstatus, $filteroto) {
    return DB::connection("SML")->select("
      select * from (
        select a.NoBukti, a.Tanggal, a.NoUrut, a.KodeCustSupp, b.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.IDUser, c.Noinv, a.NOSO,
          case when isnull(qntinvr,0) = 0 then 'Belum'
               when isnull(qntinvr,0) < qntspbr then 'Sebagian'
               else 'Selesai' end xstatus
        from dbSPBRJual a
        left outer join DBCUSTSUPP b on a.KodeCustSupp = b.KODECUSTSUPP
        left outer join (select nobukti, noinv, sum(qnt) qntspbr from dbSPBRJualDet group by NoBukti, Noinv) c on a.NoBukti = c.NoBukti
        left outer join (select nospr, sum(qnt) qntinvr from DBINVOICERPJDet group by NOSPR) d on a.NoBukti = d.nospr
        where a.nobukti like '%SPR%' and a.Tanggal >= ? and a.Tanggal < dateadd(day, 1, cast(? as datetime))
        group by a.NoBukti, a.Tanggal, a.NoUrut, a.KodeCustSupp, b.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.IDUser, c.Noinv, a.NOSO, c.qntspbr, d.qntinvr
      ) x
      where (
              (? = 0)
           or (? = 1 and x.xstatus = 'Belum')
           or (? = 2 and x.xstatus = 'Sebagian')
           or (? = 3 and x.xstatus = 'Selesai')
      )
      and (
              (? = 0)
           or (? = 1 and x.IsOtorisasi1 <> 1)
           or (? = 2 and x.IsOtorisasi1 = 1)
      )
    ", [
      $tglawal, $tglakhir,
      $filterstatus, $filterstatus, $filterstatus, $filterstatus,
      $filteroto, $filteroto, $filteroto,
    ]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    [$defaultawal, $defaultakhir] = $this->rentangPeriode($periode);

    $tglawalprj = $this->tanggalFilter($req->tglawalprj) ?: $defaultawal;
    $tglakhirprj = $this->tanggalFilter($req->tglakhirprj) ?: $defaultakhir;
    $tempOutstanding = $this->queryOutstanding($tglawalprj, $tglakhirprj);

    $tglawal = $this->tanggalFilter($req->tglawal) ?: $defaultawal;
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: $defaultakhir;
    $filterstatus = (int) $req->filterstatus;
    $filteroto = (int) $req->filteroto;
    $tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir, $filterstatus, $filteroto);

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
  ];
  }


  public function getDetail (Request $req ) {
    DB::connection('SML')->statement('exec sp_ReTempRJualGdg ?,?,?', [\Auth::User()->username, $req->nobukti, 'SPR']);

    $tempOutstanding = DB::connection("SML")->select("select * from tempoutstanding where IDUser = :username", ["username" => \Auth::User()->username]);


    // $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int, @Flagmenu tinyint,@UserID Varchar(30)
    //
    // select @Tahun= '' , @Bulan= '', @UserID= :user
    //
    // select distinct A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, A.NAMACUSTSUPP, A.NamaKota,
    //   A.NoRPJ, A.NoSO, A.IDUser,
    //   A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.NeedOtorisasi,
    //     A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,A.Noinv,A.KodeGdg , A.KodeBrg, A.NamaBrg ,A.QNT, A.QNT1, A.QNT2, A.SAT_1, A.SAT_2, A.NOSAT, A.ISI,A.URUT
    // from vwTransPRRJual A
    // Left Outer Join (  SELECT A.NOBUKTI
    //          FROM dbPRRJualDet A
    //          LEFT OUTER JOIN (select noprrjual,urutprrjual,sum(QNT1)Qnt1,SUM(QNT2) Qnt2
    //                                                 from dbSPBRJualDet  group by noprrjual,urutprrjual
    //             ) B on A.NoBukti=B.NoprRJual AND A.Urut=B.URUTPRRJUAL
    //          WHERE ISNULL(A.QNT1,0)-ISNULL(B.Qnt1,0) >0
    //          GROUP BY A.NoBukti
    //         )B ON A.NoBukti=B.NoBukti
    // where A.Kodegdg In(select KodeGdg from DBPemakaiGdg where UserId=@UserId)
    // and NeedOtorisasi=0
    // AND B.NoBukti = :nobukti
    // order by A.NoBukti",[ "user" => \Auth::User()->username , "nobukti" => $req->nobukti]);

    return $tempOutstanding;
  }

  public function getDetailPenerimaan (Request $req) {
    $tempPenerimaan = DB::connection("SML")->select("

    select 	b.* ,C.NAMABRG, '' Jns_Kertas, '' Ukr_Kertas,
        '' KetDetail, d.ALAMAT1, d.NAMACUSTSUPP,  E.CATATAN, e.NoPolKend, e.Sopir, e.NOSO, e.IsOtorisasi1,f.QntSisa
from	dbSPBRJualDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbSPBRJual E on e.NoBukti = b.NoBukti
left outer join DBCUSTSUPP d on d.KODECUSTSUPP = e.KodeCustSupp
left outer join (select b.NoPRRJUAL,b.UrutPRRJUAL,
				case when a.NOSAT=1 then a.QNT1 else a.QNT2 end - sum( ISNULL(case when a.NOSAT=1 then b.QNT1 else b.QNT2 End ,0)) QntSisa
				 from dbPRRJualDet a
				 left outer join dbSPBRJualDet b on a.NoBukti=b.NoPRRJUAL and a.Urut=b.UrutPRRJUAL
				 group by b.NoPRRJUAL,b.UrutPRRJUAL,A.QNT1,a.QNT2,a.NOSAT
				 )F on B.NoPRRJUAL=F.NoPRRJUAL and b.UrutPRRJUAL=f.UrutPRRJUAL
where b.NoBukti = :nobukti
order by B.Urut

    ", ["nobukti" =>$req->nobukti]);

    return $tempPenerimaan;

  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');


      $values = [
          $inisial[0]->SPR,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    } else {
      // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');


      $values = [
          'SPRN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
    }

  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select * from vwBrowsCustSupp where IsCustomer=1");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    $listData = DB::connection('SML')->select("select distinct A.NOBUKTI, A.TANGGAL, A.NoSO,A.KODEGDG,A.NAMAGDG , a.ppn, a.flagtipe
                from vwBrowsOutInvoicePL A
                where A.KodeCustSupp= :kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select A.Urut Urut, A.KodeBrg, A.NamaBrg, A.NoSat, A.Satuan,
                case when A.NOSAT=1 then 1 else A.ISI2 end Isi, A.Isi1, A.Isi2,
                A.QntSisa, A.Qnt1Sisa, A.Qnt2Sisa, A.NFix,a.SATX , b.NAMABRG NamaBrgx, a.SAT1 , a.SAT2

                from vwBrowsOutPLPR A
                left join dbbarang b on a.KODEBRG = b.KODEBRG
                where A.NoBukti= :noinvoice

                order by A.Urut" , ["noinvoice" => $req->noinvoice]);
    return $listData;
  }

  public function listNoBeli (Request $req) {

    $listData = DB::connection('SML')->select(" select A.NOBUKTI,A.urut
                       from DBBELIDET A
                       Left Outer join dbPRRJualdet b on a.NOBUKTI=b.nobeli and a.URUT=b.urutbeli
                      left outer join DBPO C on a.NoPO=c.NOBUKTI
                       where A.kodebrg= :kodebrg and B.NoBukti is null
                      and C.Noso= :noso
                       group by A.NOBUKTI,A.Urut" , ["kodebrg" => $req->kodebrg , "noso" => $req->noso]);
    return $listData;
  }

  // Jawaban ditolak berbentuk {status:0, pesan} (dibaca rpgDitolak() di view); jawaban
  // sukses tetap seperti semula (jumlah baris).
  public function spOtorisasi (Request $req) {
    $header = $this->headerSPR($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data SPR " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "SPR " . $req->nobukti . " sudah diotorisasi"];
    }

    $tanggal = date('Y-m-d H:i:s');
    // isnull(isOtorisasi1,0) <> 1: penjaga terakhir kalau dua user menekan Otorisasi bersamaan.
    $res = DB::connection('SML')->update("update dbSPBRJUAL set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1,0) <> 1", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "SPR " . $req->nobukti . " sudah diotorisasi"];
    }
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','RPJ',$req->nobukti,'',0,'dbSPBRJUAL');

    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    if (trim((string) $req->pket) === '') {
      return ["status" => 0, "pesan" => "Keterangan batal otorisasi harus diisi"];
    }
    $header = $this->headerSPR($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data SPR " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 !== 1) {
      return ["status" => 0, "pesan" => "SPR " . $req->nobukti . " belum diotorisasi"];
    }
    if ($this->sudahDipakaiNotaRetur($req->nobukti)) {
      return ["status" => 0, "pesan" => "SPR " . $req->nobukti . " sudah dipakai Nota Retur Penjualan, tidak bisa batal otorisasi"];
    }

    $res = DB::connection('SML')->update("update dbSPBRJUAL set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1", [ "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "SPR " . $req->nobukti . " belum diotorisasi"];
    }
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','RPJ',$req->nobukti,trim((string) $req->pket),0,'dbSPBRJUAL');
    return $res;
  }

  // Nomor bukti SPR untuk PRJ ini: inisial DBNOMOR.SPR bila customer PPN, SPRN bila tidak
  // (sama dengan getNoBukti()).
  private function buatNoBukti ($noprj) {
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $ppn = DB::connection('SML')->select("select top 1 isnull(d.PPN,0) PPN from dbPRRJual a left outer join DBCUSTSUPP d on a.KodeCustSupp = d.KODECUSTSUPP where a.NoBukti = cast(? as varchar(100))", [$noprj]);
    if ($ppn && (int) $ppn[0]->PPN === 1) {
      $inisial = DB::connection("SML")->select('select SPR from DBNOMOR')[0]->SPR;
    } else {
      $inisial = 'SPRN';
    }
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?', [$inisial, $periode->bulan, $periode->tahun, $username]);
    return $noBukti ? $noBukti[0] : null;
  }

  // Simpan SPR baru dari PRJ. Dulu: TempOutstanding dipakai apa adanya (bisa sudah tertimpa
  // form lain milik user yang sama), qty tidak dicek ulang di server, nomor bukti diambil saat
  // form dibuka (bisa keduluan user lain -> SP_InsertPRRSPB rollback diam-diam karena
  // PK_dbSPBRJual) dan jawabannya selalu 1 walau tidak ada yang tersimpan.
  public function spAdd (Request $req) {
    $tempData = $req->input('tempData') ?: [];
    $username = \Auth::user()->username;
    $noprj = (string) $req->noprj;
    // SPR baru selalu bertanggal server SQL (input tanggal di form disabled), bukan tanggal
    // kiriman browser.
    $tanggal = DB::connection('SML')->select("select convert(varchar(10), getdate(), 23) tanggal")[0]->tanggal;

    if (!count($tempData)) {
      return ["status" => 0, "pesan" => "Tidak ada item dipilih"];
    }
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    if ((int) substr($tanggal, 0, 4) !== (int) $periode->tahun || (int) substr($tanggal, 5, 2) !== (int) $periode->bulan) {
      return ["status" => 0, "pesan" => "Tanggal server (" . date('d/m/Y', strtotime($tanggal)) . ") tidak sesuai periode"];
    }

    $prj = DB::connection('SML')->select("select top 1 NeedOtorisasi from vwTransPRRJual where NoBukti = cast(? as varchar(100))", [$noprj]);
    if (!$prj) {
      return ["status" => 0, "pesan" => "PRJ " . $noprj . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $prj[0]->NeedOtorisasi !== 0) {
      return ["status" => 0, "pesan" => "PRJ " . $noprj . " belum / sudah batal diotorisasi"];
    }

    // Isi ulang TempOutstanding dari data terbaru supaya sisa qty yang dicek & disimpan
    // adalah sisa saat ini (bisa sudah dipakai SPR lain sejak form dibuka).
    DB::connection('SML')->statement('exec sp_ReTempRJualGdg ?,?,?', [$username, $noprj, 'SPR']);
    $temp = [];
    foreach (DB::connection('SML')->select("select URUT, KODEBRG, NAMABRG, QntSisa from TempOutstanding where IDUser = ? and NOBUKTI = cast(? as varchar(100))", [$username, $noprj]) as $t) {
      $temp[(string) $t->URUT] = $t;
    }

    foreach ($tempData as $d) {
      $urut = (string) ($d['URUT'] ?? '');
      $t = $temp[$urut] ?? null;
      if (!$t || (string) ($d['NOBUKTI'] ?? '') !== $noprj) {
        return ["status" => 0, "pesan" => "Barang " . ($d['KODEBRG'] ?? '') . " sudah tidak outstanding di PRJ " . $noprj . ", silahkan buka ulang form"];
      }
      $terima = (float) ($d['inputQntTerima'] ?? 0);
      $reject = (float) ($d['inputQntReject'] ?? 0);
      $sisa = (float) $t->QntSisa;
      if ($terima <= 0) {
        return ["status" => 0, "pesan" => "Qty terima barang " . $t->KODEBRG . " harus lebih dari 0"];
      }
      if ($reject < 0) {
        return ["status" => 0, "pesan" => "Qty reject barang " . $t->KODEBRG . " tidak boleh kurang dari 0"];
      }
      if ($terima > $sisa + 0.00001 || $reject > $sisa + 0.00001) {
        return ["status" => 0, "pesan" => "Qty terima / reject barang " . $t->KODEBRG . " melebihi qty sisa (" . number_format($sisa, 2) . ")"];
      }
      // Reject = bagian barang diterima yang rusak/ditolak (pola kartu stok: terima - reject),
      // jadi tidak mungkin melebihi qty terima.
      if ($reject > $terima + 0.00001) {
        return ["status" => 0, "pesan" => "Qty reject barang " . $t->KODEBRG . " tidak boleh melebihi qty terima"];
      }

      DB::connection("SML")->update('update TempOutstanding set isTerima = 1 , QntTerima = :qntterima , QntReject = :qntreject
where nobukti = :nobukti and urut = :urut and iduser = :username' ,["qntterima" => $terima , "qntreject" => $reject, "nobukti" => $noprj , "urut"=> $urut , "username" => $username ]);
    }

    // Nomor bukti diambil saat simpan; bila tetap bentrok dengan user lain SP_InsertPRRSPB
    // rollback (TempOutstanding ikut kembali), jadi dicoba lagi dengan nomor baru.
    for ($coba = 0; $coba < 3; $coba++) {
      $nomor = $this->buatNoBukti($noprj);
      if (!$nomor) {
        break;
      }
      DB::connection('SML')->statement('exec SP_InsertPRRSPB ?,?,?,?,?,?,?', [
        $nomor->Nobukti,
        $nomor->Nourut,
        $noprj,
        $username,
        -1,
        $tanggal,
        'SPR'
      ]);

      $cek = DB::connection('SML')->select("select top 1 1 ada from dbSPBRJualDet where NoBukti = cast(? as varchar(100)) and NoPRRJUAL = cast(? as varchar(100))", [$nomor->Nobukti, $noprj]);
      if ($cek) {
        // SP_InsertPRRSPB tidak mengisi IDUser, kolom "User" di daftar jadi kosong.
        DB::connection('SML')->update("update dbSPBRJual set IDUser = ? where NoBukti = cast(? as varchar(100)) and isnull(IDUser,'') = ''", [$username, $nomor->Nobukti]);
        // Rumus Qnt2Reject di SP_InsertPRRSPB menghasilkan NULL untuk NOSAT=1; diisi dengan
        // rumus yang sama seperti Edit item (QNT2REJECT = qty reject).
        DB::connection('SML')->update("update dbSPBRJualDet set QNT2REJECT = QNTREJECT where NoBukti = cast(? as varchar(100)) and QNT2REJECT is null", [$nomor->Nobukti]);
        return ["status" => 1, "nobukti" => $nomor->Nobukti];
      }
    }

    DB::connection('SML')->delete("delete TempOutstanding where IDUser = ? and Trans = 'SPR'", [$username]);
    return ["status" => 0, "pesan" => "SPR gagal disimpan, silahkan coba lagi"];

  }

  // Edit (U) / Hapus (D) satu item SPR. Data barang (kode, satuan, isi, gudang) diambil dari
  // DB, bukan dari browser; qty 1/2 dihitung dengan rumus yang sama seperti di browser dulu
  // (QNT1 = qty x ISI, QNT2 = qty). Dulu QNT1REJECT selalu NULL karena browser mengirim
  // "qnt1Reject" sedangkan di sini dibaca "qnt1reject".
  public function spKoreksi (Request $req) {
    $choice = $req->choice;
    $nobukti = $req->nobukti;

    if ($choice !== 'U' && $choice !== 'D') {
      return ["status" => 0, "pesan" => "Aksi tidak dikenal"];
    }
    $ditolak = $this->tolakJikaTidakBisaDiubah($nobukti);
    if ($ditolak) {
      return $ditolak;
    }

    $baris = DB::connection('SML')->select("
      select b.*, f.QntSisa
      from dbSPBRJualDet b
      left outer join (select b.NoPRRJUAL, b.UrutPRRJUAL,
          case when a.NOSAT=1 then a.QNT1 else a.QNT2 end - sum(isnull(case when a.NOSAT=1 then b.QNT1 else b.QNT2 end, 0)) QntSisa
        from dbPRRJualDet a
        left outer join dbSPBRJualDet b on a.NoBukti=b.NoPRRJUAL and a.Urut=b.UrutPRRJUAL
        group by b.NoPRRJUAL, b.UrutPRRJUAL, a.QNT1, a.QNT2, a.NOSAT
      ) f on b.NoPRRJUAL=f.NoPRRJUAL and b.UrutPRRJUAL=f.UrutPRRJUAL
      where b.NoBukti = cast(? as varchar(100)) and b.Urut = ?", [$nobukti, (int) $req->urut]);
    if (!$baris) {
      return ["status" => 0, "pesan" => "Item sudah tidak ada di SPR " . $nobukti . ", silahkan buka ulang form"];
    }
    $item = $baris[0];
    $isi = (float) $item->ISI ?: 1;

    if ($choice === 'D') {
      $qntTerima = 0;
      $qntReject = 0;
      $retursupp = 0;
    } else {
      $qntTerima = (float) $req->qntTerima;
      $qntReject = (float) $req->qntReject;
      $retursupp = (int) $req->retursupp === 1 ? 1 : 0;
      $batas = (float) $item->QntSisa + (float) $item->QNT;
      if ($qntTerima <= 0) {
        return ["status" => 0, "pesan" => "Qty harus lebih dari 0, gunakan tombol hapus untuk menghapus item"];
      }
      if ($qntReject < 0) {
        return ["status" => 0, "pesan" => "Qty kurang dari 0"];
      }
      if ($qntTerima > $batas + 0.00001 || $qntReject > $batas + 0.00001) {
        return ["status" => 0, "pesan" => "Qty melebihi qty sisa (" . number_format($batas, 2) . ")"];
      }
      if ($qntReject > $qntTerima + 0.00001) {
        return ["status" => 0, "pesan" => "Qty reject tidak boleh melebihi qty terima"];
      }
    }

    $values = [
      $choice,
      $nobukti,
      '',
      '',
      '',
      '',
      '', // nopolkend
      '',
      '',
      '', // noseal 10
      '',
      $item->Urut,
      '',
      $item->KodeBrg,
      $qntTerima,
      $qntTerima * $isi,
      $qntTerima,
      $item->SAT_1,
      $item->NOSAT,
      $item->ISI, // 20
      0, // netw
      0,
      \Auth::User()->username,
      0,
      $item->Namabrg, // 25
      '', // sopir
      0,
      $item->KodeGdg,
      0,
      0, // 30
      '',
      '', // nobatch
      $retursupp,
      $qntReject,
      $qntReject * $isi,
      $qntReject,
      $item->SAT_2

    ];

    DB::connection('SML')->statement('exec sp_TransSPBRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    return 1;

  }



  // Nama kolom dulu disambung langsung dari browser ke SQL; sekarang dibatasi ke kolom yang
  // memang bisa diubah di form Edit, dan ditolak bila SPR sudah diotorisasi.
  public function onChangeHeader (Request $req) {
    $kolom = ['nopolkend' => 'NoPolKend', 'sopir' => 'Sopir', 'catatan' => 'Catatan'];
    $field = strtolower((string) $req->field);
    if (!isset($kolom[$field])) {
      return ["status" => 0, "pesan" => "Kolom tidak bisa diubah"];
    }
    $ditolak = $this->tolakJikaTidakBisaDiubah($req->nobukti);
    if ($ditolak) {
      return $ditolak;
    }

    $query = 'update dbspbrjual set ' . $kolom[$field] . ' = :value where nobukti = cast(:nobukti as varchar(100))';



    $res = DB::connection('SML')->update($query, ["value" => (string) $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }











}
