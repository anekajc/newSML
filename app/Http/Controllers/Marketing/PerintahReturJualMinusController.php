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

// Perintah Retur Jual (-) / PRJ Minus: retur jual tanpa invoice (dbPRRJual.pmin = 1).
// Pola tampilan & pengaman disamakan dengan PerintahReturJualController; pembedanya
// mengikuti programajcSML: No Invoice & No SO berisi '-', gudang dipilih sendiri, barang
// diambil dari master barang (aktif, grup BJ/JS), tanpa cek qty sisa, dan tanpa cetak.
class PerintahReturJualMinusController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041051';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, 0, 0);

    $listGudang = DB::connection('SML')->select("select KODEGDG , NAMA , ALAMAT from dbgudang");
    return view('marketing.perintahreturjualminus' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses,
      "listGudang" => $listGudang
    ]);

  }

  // Sama dengan PerintahReturJualController::queryOutstanding, hanya pmin = 1.
  //   filterstatus: 0 = Semua, 1 = Belum Diproses, 2 = Proses Sebagian, 3 = Selesai
  //   filteroto:    0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryOutstanding ($tglawal, $tglakhir, $filterstatus, $filteroto) {
    return DB::connection("SML")->select("
      select * from (
        select month(a.Tanggal) Bulan, YEAR(a.Tanggal) Tahun, a.Tanggal, a.Catatan, a.NOBUKTI,
          a.KodeCustSupp, d.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ, a.NOSO, a.IDUser,
          case when isnull(qntspbr,0) = 0 then 'Belum'
               when isnull(qntspbr,0) < qntprr then 'Sebagian'
               else 'Selesai' end xstatus
        from dbPRRJual a
        left outer join (select nobukti, sum(qnt) qntprr from dbPRRJualDet group by NoBukti) b on b.NoBukti = a.NoBukti
        left outer join (select NoPRRJUAL, sum(qnt) qntspbr from dbSPBRJualdet group by NoPRRJUAL) c on c.NoPRRJUAL = a.NoBukti
        left outer join DBCUSTSUPP D on a.KodeCustSupp = d.KODECUSTSUPP
        where a.TANGGAL between ? and ? and a.pmin = 1
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
    $tglawal = $this->tanggalFilter($req->tglawal) ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterstatus = $req->filterstatus ?: 0;
    $filteroto = $req->filteroto ?: 0;

    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, $filterstatus, $filteroto);

    return ["tempOutstanding" => $tempOutstanding];
  }


  public function getDetail (Request $req ) {

    // Kolom sama dengan programajcSML. PRJ Minus tidak punya invoice (NoINV '-') dan tidak
    // memakai qty sisa, jadi join ke vwBrowsOutPLPR (view agregat yang berat) diganti NULL
    // - kolom QntSisaView/IsiSisaView tetap ada, isinya memang selalu kosong.
    $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
    B.NOBUKTI,A.NOURUT, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,b.NAMABRG NAMAPRODUK,
            B.QNT,B.QNT1, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, b.ketdet KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,d.ALAMAT1, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX, b.KodeGdg, A.FlagTipe, A.TipePPN,
            b.NoSat, b.nobeli , b.urutbeli, cast(null as numeric(18,2)) QntSisaView, cast(null as numeric(18,2)) IsiSisaView,  b.FlagKembali,  c.ISI1, c.ISI2
    from	dbPRRJualDet B
    left outer join dbBarang C on C.KodeBrg=B.KodeBrg
    left outer join dbPRRJual a on b.NoBukti=a.NoBukti

    left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
    where a.Nobukti = cast(:nobukti as varchar(100))" , ["nobukti" => $req->nobukti]);

    // IsProses = 1 bila PRJ sudah dipakai Retur Penjualan Gudang (dbSPBRJualDet).
    if ($tempOutstanding) {
      $isProses = $this->sudahDiprosesGudang($req->nobukti) ? 1 : 0;
      foreach ($tempOutstanding as $row) {
        $row->IsProses = $isProses;
      }
    }
    return $tempOutstanding;
  }

  // PRJ yang sudah dipakai Retur Penjualan Gudang tidak boleh diubah / dibatal otorisasi.
  private function sudahDiprosesGudang ($nobukti) {
    $cek = DB::connection('SML')->select("select top 1 1 ada from dbSPBRJualDet where NoPRRJUAL = cast(? as varchar(100))", [$nobukti]);
    return count($cek) > 0;
  }

  private function headerPRJ ($nobukti) {
    $cek = DB::connection('SML')->select("select NoBukti, isnull(IsOtorisasi1,0) IsOtorisasi1 from dbPRRJual where NoBukti = cast(? as varchar(100)) and pmin = 1", [$nobukti]);
    return $cek ? $cek[0] : null;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select PRJ from DBNOMOR');


      $values = [
          $inisial[0]->PRJ,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    } else {
      $values = [
          'PRJN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    }
  }


  public function listCustomer (Request $req) {

    // Hanya kolom yang dipakai modal + distinct (lihat PerintahReturJualController::listCustomer).
    $listData = DB::connection('SML')->select("select distinct KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1, NamaKota, PPN
                from vwBrowsCustSupp where IsCustomer=1");
    return $listData;
  }

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select KODEGDG , NAMA , ALAMAT from dbgudang");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    $listData = DB::connection('SML')->select("select distinct A.NOBUKTI, A.TANGGAL, A.NoSO,A.KODEGDG,A.NAMAGDG , a.ppn, a.flagtipe
                from vwBrowsOutInvoicePL A
                where A.KodeCustSupp= cast(:kodecustsupp as varchar(50))" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    // Dulu teks pencarian ditempel langsung ke SQL (bisa disalahgunakan / error bila ada
    // tanda kutip) dan "... and KodeBrg like X or NamaBrg like X" tanpa kurung membuat
    // pencarian lewat nama menampilkan semua barang termasuk yang tidak aktif / grup lain.
    // Sekarang parameter terikat dan keduanya dibatasi barang aktif grup BJ/JS (keputusan
    // user 2026-10-13).
    $cari = '%' . str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], (string) $req->input('search')) . '%';
    $listData = DB::connection('SML')->select("select KodeBrg, NamaBrg, Isi1, Isi2 , SAT1, SAT2 from dbbarang A
                where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and (A.KodeBrg like cast(? as varchar(200)) or a.NamaBrg like cast(? as varchar(200)))
                    order by a.Kodebrg ASC", [$cari, $cari]);
    return $listData;
  }

  public function listBarangDetail (Request $req) {

    $listData = DB::connection('SML')->select("select KodeBrg, NamaBrg, Isi1, Isi2 , SAT1, SAT2 from dbbarang A
                where a.KodeBrg = cast(:kodebrg as varchar(50))" , ["kodebrg" =>  $req->input('kodebrg') ] );
    return $listData;
  }

  public function listNoBeli (Request $req) {

    $listData = DB::connection('SML')->select(" select A.NOBUKTI,A.urut
                       from DBBELIDET A
                       Left Outer join dbPRRJualdet b on a.NOBUKTI=b.nobeli and a.URUT=b.urutbeli
                      left outer join DBPO C on a.NoPO=c.NOBUKTI
                       where A.kodebrg= cast(:kodebrg as varchar(50)) and B.NoBukti is null
                      and C.Noso= cast(:noso as varchar(100))
                       group by A.NOBUKTI,A.Urut" , ["kodebrg" => $req->kodebrg , "noso" => $req->noso]);
    return $listData;
  }

  // Jawaban penolakan berbentuk {status: 0, pesan}; jawaban sukses tetap jumlah baris / 1.
  public function spOtorisasi (Request $req) {
    $header = $this->headerPRJ($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data PRJ " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diotorisasi"];
    }

    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1,0) <> 1", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diotorisasi"];
    }
    // programajcSML mengotorisasi PRJ Minus lewat endpoint PRJ biasa yang mencatat 'oto';
    // di sini dicatat langsung dengan cara yang sama.
    app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');
    return $res;
  }

  public function spBatalOtorisasi (Request $req) {
    if (trim((string) $req->pket) === '') {
      return ["status" => 0, "pesan" => "Keterangan batal otorisasi harus diisi"];
    }
    $header = $this->headerPRJ($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data PRJ " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 !== 1) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " belum diotorisasi"];
    }
    if ($this->sudahDiprosesGudang($req->nobukti)) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diproses Retur Penjualan Gudang, tidak bisa batal otorisasi"];
    }

    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1", [ "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " belum diotorisasi"];
    }
    app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');
    return $res;
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;

    if (!in_array($choice, ['I', 'U', 'D'], true) || trim((string) $nobukti) === '') {
      return ["status" => 0, "pesan" => "Data tidak lengkap, silahkan ulangi"];
    }

    $tanggal = $req->tanggal;
    if ($choice == 'I' && $jmlrecord == 0) {
      // PRJ baru: tanggal selalu tanggal server dan harus masuk periode kerja aktif.
      $tglServer = DB::connection('SML')->select("select convert(varchar(10), getdate(), 23) tanggal")[0]->tanggal;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      if ((int) substr($tglServer, 0, 4) !== (int) $periode->tahun || (int) substr($tglServer, 5, 2) !== (int) $periode->bulan) {
        return ["status" => 0, "pesan" => "Tanggal server (" . date('d/m/Y', strtotime($tglServer)) . ") tidak sesuai periode kerja aktif"];
      }
      $tanggal = $tglServer;

      $check = DB::connection('SML')->select('select * from dbprrjual where Nobukti = cast(:nobukti as varchar(100))',["nobukti" => $nobukti]);
      if ($check) {
        return 2;
      }
    } else {
      // Tambah/ubah/hapus item PRJ yang sudah ada: tolak bila PRJ sudah hilang, sudah
      // diotorisasi, atau sudah diproses gudang.
      $header = $this->headerPRJ($nobukti);
      if (!$header) {
        return ["status" => 0, "pesan" => "Data PRJ " . $nobukti . " tidak ditemukan, mungkin sudah dihapus"];
      }
      if ((int) $header->IsOtorisasi1 === 1) {
        return ["status" => 0, "pesan" => "PRJ " . $nobukti . " sudah diotorisasi, item tidak bisa diubah"];
      }
      if ($this->sudahDiprosesGudang($nobukti)) {
        return ["status" => 0, "pesan" => "PRJ " . $nobukti . " sudah diproses Retur Penjualan Gudang, item tidak bisa diubah"];
      }
    }

    if ($choice != 'D') {
      if (!((float) $req->qnt > 0)) {
        return ["status" => 0, "pesan" => "Qty harus lebih dari 0"];
      }
      if (trim((string) $req->kodegdg) === '') {
        return ["status" => 0, "pesan" => "Gudang belum dipilih"];
      }
      // Satu PRJ Minus = satu gudang (keputusan user 2026-10-13): item tambahan/ubahan
      // harus memakai gudang yang sama dengan item yang sudah tersimpan.
      if ($choice == 'I' && $jmlrecord != 0) {
        $gdg = DB::connection('SML')->select("select top 1 KodeGdg from dbPRRJualDet where NoBukti = cast(? as varchar(100)) and isnull(KodeGdg,'') <> cast(? as varchar(50))", [$nobukti, $req->kodegdg]);
        if ($gdg) {
          return ["status" => 0, "pesan" => "Gudang harus sama dengan item sebelumnya (" . $gdg[0]->KodeGdg . ")"];
        }
      }
    }

    $xurut = 0;
    if ($choice == 'I') {
      $purut = DB::connection('SML')->select('select isnull(max(urut),0)+1 xurut from DBPRRJUALDET where Nobukti = cast(:nobukti as varchar(100))', ['nobukti' => $nobukti]);
      $xurut = $purut ? $purut[0]->xurut : 1;
    } else {
      $xurut = $req->urut;
    }

    if ($choice == 'D') {
      app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }

    $values = [
      $choice,
      $nobukti,
      $req->nourut,
      $tanggal,
      $req->noinvoice,
      $req->kodecustsupp,
      '', // nopolkend
      '',
      '',
      '', // noseal 10
      $req->catatan,
      $req->urut,
      $req->urutinvoice,
      $req->kodebrg,
      $req->qnt,
      $req->qnt1,
      $req->qnt2,
      $req->sat1,
      $req->nosat,
      $req->isi, // 20
      0, // netw
      0,
      \Auth::User()->username,
      $req->jmlrecord,
      $req->namabrg, // 25
      '', // sopir
      0,
      $req->kodegdg,
      $req->flagtipe,
      $req->ppn, // 30
      $req->noso,
      '', // nobatch
      $req->retursupp,
      $req->sat2,
      '', //satx
      1, // pmin 50
      $req->ketdet,
      $req->nobeli,
      $req->urutbeli

    ];

    DB::connection('SML')->statement('exec sp_TransPRRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

    if ($choice != 'D') {
      app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }
    return 1;

  }

}
