<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use Illuminate\Auth;

class ClosingPOController extends Controller
{
  // Href penyimpanan pengaturan kolom di DBHEADERTABLE. Nilainya tidak boleh berubah, karena
  // pengaturan kolom milik tiap user disimpan per href.
  const CPO_HREF = 'closingpurchaseorder';

  // Kolom default tab "Outstanding PO" (urut 1), sumber vwOutPOBatal. Casing nama field harus
  // persis sama dengan yang dikembalikan dataOutstanding(): SQL Server tidak peduli casing,
  // tapi PHP (dan JS) peduli - lihat catatan casing di CPO_KOLOM_2.
  const CPO_KOLOM_1 = [
    ['field' => 'Nobukti',      'label' => 'No. Bukti',   'tipe' => 0],
    ['field' => 'TANGGAL',      'label' => 'Tanggal',     'tipe' => 2],
    ['field' => 'NAMACUSTSUPP', 'label' => 'Supplier',    'tipe' => 0],
    ['field' => 'kodebrg',      'label' => 'Kode Barang', 'tipe' => 0],
    ['field' => 'NamaBrg',      'label' => 'Nama Barang', 'tipe' => 0],
    ['field' => 'Satuan',       'label' => 'Satuan',      'tipe' => 0],
    ['field' => 'Qnt',          'label' => 'Qty PO',      'tipe' => 1],
    ['field' => 'QntBatal',     'label' => 'Qty Batal',   'tipe' => 1],
    ['field' => 'qntterima',    'label' => 'Qty Terima',  'tipe' => 1],
    ['field' => 'QntSisa',      'label' => 'Qty Sisa',    'tipe' => 1],
  ];

  // Kolom default tab "Closing PO" (urut 2), sumber dbPO + vwMasterPOOut. Casing field-nya
  // berbeda dari urut 1 ('NoBukti', 'satuan') karena sumber tabelnya memang berbeda - lihat
  // SELECT eksplisit di dataClosing().
  const CPO_KOLOM_2 = [
    ['field' => 'NoBukti',      'label' => 'No. Bukti',   'tipe' => 0],
    ['field' => 'Tanggal',      'label' => 'Tanggal',     'tipe' => 2],
    ['field' => 'NamaCustSupp', 'label' => 'Supplier',    'tipe' => 0],
    ['field' => 'kodebrg',      'label' => 'Kode Barang', 'tipe' => 0],
    ['field' => 'namabrg',      'label' => 'Nama Barang', 'tipe' => 0],
    ['field' => 'satuan',       'label' => 'Satuan',      'tipe' => 0],
    ['field' => 'qnt',          'label' => 'Qty PO',      'tipe' => 1],
    ['field' => 'qntbatal',     'label' => 'Qty Batal',   'tipe' => 1],
    ['field' => 'qntterima',    'label' => 'Qty Terima',  'tipe' => 1],
    ['field' => 'qntsisa',      'label' => 'Qty Sisa',    'tipe' => 1],
    ['field' => 'UserBatal',    'label' => 'User Close',  'tipe' => 0],
    ['field' => 'TglBatal',     'label' => 'Tgl. Close',  'tipe' => 2],
    ['field' => 'KetBatal',     'label' => 'Ket. Close',  'tipe' => 0],
  ];

  // Baris yang boleh muncul di tab "Outstanding PO". Sengaja dipisah jadi konstanta karena
  // dipakai dua kali: untuk daftarnya (dataOutstanding) dan untuk mencari sisa saat menutup
  // per No. Bukti (cekSisa/updateCloseHeader), supaya keduanya tidak bisa berbeda aturan.
  const CPO_FILTER_OUTSTANDING = 'A.QntSisa > 0 and A.Qnt - ISNULL(A.QntBatal, 0) > 0';

  // Sumber tab "Closing PO". Sama dengan query lama di loadAll(), tapi hanya kolom yang benar
  // benar dipakai (dulu ikut menarik seluruh kolom otorisasi dan total-total dbPO yang tidak
  // pernah ditampilkan), dan qntbatal ikut diambil supaya Qty Batal bisa dilihat.
  private function sqlDasarClosing () {
    return "
      select
          A.NoBukti,
          A.Tanggal,
          B.NamaCustSupp,
          B.kodebrg,
          B.namabrg,
          B.satuan,
          B.qnt,
          B.qntterima,
          B.qntbatal,
          -- qntsisa bawaan vwMasterPOOut belum mengurangi qntbatal, jadi dihitung ulang di sini
          (ISNULL(B.qnt, 0) - ISNULL(B.qntbatal, 0) - ISNULL(B.qntterima, 0)) as qntsisa,
          B.UserBatal,
          B.TglBatal,
          B.KetBatal,
          B.Urut
      from dbPO A
      Left Outer Join vwMasterPOOut B on A.NoBukti = B.NoBukti
      where ISNULL(B.qntbatal, 0) <> 0
    ";
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path);
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($cpoTglAwal, $cpoTglAkhir) = $this->periodeRange($periode);

    // Isi kedua tabel tidak lagi ditarik di sini. Dulu halaman ini menarik SELURUH isi
    // vwOutPOBatal dan seluruh dbPO+vwMasterPOOut sekali jalan (dua kali malah: sekali di
    // index() untuk @foreach blade, sekali lagi lewat loadAll() dari JS). Sekarang datanya
    // diambil per halaman lewat dataOutstanding()/dataClosing(), dan loadAll() hanya
    // mengirim konfigurasi kolom - sejajar PembelianClosingPRController.
    return view('purchasing.closingPurchaseOrder' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "cpoTglAwal" => $cpoTglAwal,
      "cpoTglAkhir" => $cpoTglAkhir,
      "akses" => $akses
    ]);

}

  // Dipanggil AJAX sinkron sekali di awal halaman untuk mengambil konfigurasi kolom kedua
  // tabel. Tidak membawa data sama sekali.
  public function loadAll (Request $req) {
    return response()->json([
      "kolom1" => $this->ambilKolom(1),
      "kolom2" => $this->ambilKolom(2),
    ]);
  }

  // Konfigurasi kolom mandiri untuk halaman ini - tidak lewat HeaderTableController supaya
  // file bersama itu tidak perlu disentuh. Penyimpanan tetap lewat endpoint generik
  // saveheadertable/DBHEADERTABLE, hanya pengambilannya yang khusus di sini.
  public function headerTable (Request $req) {
    $urut = (int) $req->input('urut', 1);
    if ($urut !== 2) { $urut = 1; }

    if ($req->input('reset')) {
      DB::connection('SML')->update(
        "delete from DBHEADERTABLE where username = :username and href = :href and urut = :urut",
        ["username" => \Auth::user()->username, "href" => self::CPO_HREF, "urut" => $urut]
      );
    }

    return response()->json($this->ambilKolom($urut));
  }

  private function ambilKolom ($urut) {
    $default = $urut === 2 ? self::CPO_KOLOM_2 : self::CPO_KOLOM_1;
    $username = \Auth::user()->username;

    $saved = DB::connection('SML')->select(
      "select * from DBHEADERTABLE where username = :username and href = :href and urut = :urut",
      ["username" => $username, "href" => self::CPO_HREF, "urut" => $urut]
    );

    if (count($saved) > 0) {
      $valueSaved = json_decode($saved[0]->value);

      // aliasordered HARUS sejajar indeksnya dengan headertablevalue (yang bisa sudah digeser
      // urutannya oleh user), jadi label dicari per nilai lewat $labelByField - BUKAN diambil
      // dari $default apa adanya (urutan $default selalu tetap, labelnya akan salah pasang
      // begitu user pernah menggeser kolom).
      $labelByField = [];
      foreach ($default as $k) { $labelByField[$k['field']] = $k['label']; }

      $aliasordered = [];
      foreach ($valueSaved as $v) {
        array_push($aliasordered, ["value" => $v, "alias" => isset($labelByField[$v]) ? $labelByField[$v] : $v]);
      }

      return [
        "headertableheader" => json_decode($saved[0]->header),
        "headertablevalue"  => $valueSaved,
        "isnumeric"         => json_decode($saved[0]->isnumber),
        "isshown"           => json_decode($saved[0]->isshown),
        // DBHEADERTABLE tidak punya kolom desimal tersendiri - kolom `tipe` dipakai untuk itu.
        "desimal"           => json_decode($saved[0]->tipe),
        "aliasordered"      => $aliasordered,
      ];
    }

    $header = []; $value = []; $isnumeric = []; $isshown = []; $desimal = []; $aliasordered = [];
    foreach ($default as $k) {
      array_push($header, $k['field']);
      array_push($value, $k['field']);
      array_push($isnumeric, $k['tipe']);
      array_push($isshown, 1);
      array_push($desimal, $k['tipe'] === 1 ? 2 : 0);
      array_push($aliasordered, ["value" => $k['field'], "alias" => $k['label']]);
    }

    return [
      "headertableheader" => $header,
      "headertablevalue"  => $value,
      "isnumeric"         => $isnumeric,
      "isshown"           => $isshown,
      "desimal"           => $desimal,
      "aliasordered"      => $aliasordered,
    ];
  }

  // Data tab "Outstanding PO" dengan server-side paging DataTables.
  public function dataOutstanding (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $draw   = (int) $req->input('draw', 1);
    $start  = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    if ($start < 0) { $start = 0; }
    $semua = ($length === -1);
    if (!$semua && ($length < 1 || $length > 500)) { $length = 10; }

    $allowedOrder = ['Nobukti', 'TANGGAL', 'NAMACUSTSUPP', 'kodebrg', 'NamaBrg', 'Satuan', 'Qnt', 'qntterima', 'QntBatal', 'QntSisa', 'urut'];
    $orderCol = (string) $req->input('orderCol', '');
    $orderDir = strtolower((string) $req->input('orderDir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

    if (in_array($orderCol, $allowedOrder, true)) {
      $orderBy = 'A.[' . $orderCol . '] ' . $orderDir . ', A.NoBukti, A.Urut';
    } else {
      // Default: data terbaru di atas. A.Urut sengaja tetap ASC - itu nomor urut
      // barang di dalam satu No. Bukti, bukan bagian dari "yang terbaru".
      $orderBy = 'A.Tanggal DESC, A.NoBukti DESC, A.Urut';
    }

    $where = self::CPO_FILTER_OUTSTANDING . " and A.Tanggal between :tglawal and :tglakhir";
    $bind  = ["tglawal" => $tglawal, "tglakhir" => $tglakhir];
    $search = trim((string) $req->input('search', ''));
    if ($search !== '') {
      $where .= " and (A.NoBukti like :cari1 or A.kodebrg like :cari2 or A.NamaBrg like :cari3 or A.NAMACUSTSUPP like :cari4)";
      $like = '%' . $search . '%';
      $bind = array_merge($bind, ["cari1" => $like, "cari2" => $like, "cari3" => $like, "cari4" => $like]);
    }

    $jml = DB::connection("SML")->select("
      SET NOCOUNT ON
      select count(1) as jml
      from vwOutPOBatal A WITH(NOLOCK)
      where $where
    ", $bind);
    $total = count($jml) ? (int) $jml[0]->jml : 0;

    $batasBaris = '';
    if (!$semua) {
      $batas = $start + $length;
      $batasBaris = "where X.NoBaris > $start and X.NoBaris <= $batas";
    }

    $rows = DB::connection("SML")->select("
      SET NOCOUNT ON
      select X.* from (
        select ROW_NUMBER() over (order by $orderBy) as NoBaris,
               A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
               A.Nobukti, A.TANGGAL, A.NAMACUSTSUPP, A.kodebrg, A.NamaBrg, A.Satuan,
               A.Qnt, A.qntterima, A.QntBatal, A.urut,
               -- QntSisa bawaan vwOutPOBatal belum mengurangi QntBatal, jadi dihitung ulang
               (ISNULL(A.Qnt, 0) - ISNULL(A.QntBatal, 0) - ISNULL(A.qntterima, 0)) as QntSisa
        from vwOutPOBatal A WITH(NOLOCK)
        where $where
      ) X
      $batasBaris
      order by X.NoBaris
    ", $bind);

    return [
      "draw" => $draw,
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $rows,
    ];
  }

  // Data tab "Closing PO" dengan server-side paging DataTables.
  public function dataClosing (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $draw   = (int) $req->input('draw', 1);
    $start  = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    if ($start < 0) { $start = 0; }
    $semua = ($length === -1);
    if (!$semua && ($length < 1 || $length > 500)) { $length = 10; }

    $allowedOrder = ['NoBukti', 'Tanggal', 'NamaCustSupp', 'kodebrg', 'namabrg', 'satuan', 'qnt', 'qntterima', 'qntbatal', 'qntsisa', 'UserBatal', 'TglBatal', 'KetBatal', 'Urut'];
    $orderCol = (string) $req->input('orderCol', '');
    $orderDir = strtolower((string) $req->input('orderDir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

    if (in_array($orderCol, $allowedOrder, true)) {
      $orderBy = 'X.[' . $orderCol . '] ' . $orderDir . ', X.NoBukti, X.Urut';
    } else {
      // sqlDasarClosing() sudah menyertakan X.Tanggal (lihat select-nya), jadi
      // default di sini juga bisa dibalik pakai tanggal, bukan cuma No. Bukti.
      $orderBy = 'X.Tanggal DESC, X.NoBukti DESC, X.Urut';
    }

    $where = '1 = 1 and X.Tanggal between :tglawal and :tglakhir';
    $bind  = ["tglawal" => $tglawal, "tglakhir" => $tglakhir];
    $search = trim((string) $req->input('search', ''));
    if ($search !== '') {
      $where .= " and (X.NoBukti like :cari1 or X.kodebrg like :cari2 or X.namabrg like :cari3 or X.NamaCustSupp like :cari4 or X.KetBatal like :cari5)";
      $like = '%' . $search . '%';
      $bind = array_merge($bind, ["cari1" => $like, "cari2" => $like, "cari3" => $like, "cari4" => $like, "cari5" => $like]);
    }

    $sqlDasar = $this->sqlDasarClosing();

    $jml = DB::connection("SML")->select("
      SET NOCOUNT ON
      select count(1) as jml from ( $sqlDasar ) X where $where
    ", $bind);
    $total = count($jml) ? (int) $jml[0]->jml : 0;

    $batasBaris = '';
    if (!$semua) {
      $batas = $start + $length;
      $batasBaris = "where Y.NoBaris > $start and Y.NoBaris <= $batas";
    }

    $rows = DB::connection("SML")->select("
      SET NOCOUNT ON
      select Y.* from (
        select ROW_NUMBER() over (order by $orderBy) as NoBaris, X.*
        from ( $sqlDasar ) X
        where $where
      ) Y
      $batasBaris
      order by Y.NoBaris
    ", $bind);

    return [
      "draw" => $draw,
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $rows,
    ];
  }

  // Dipanggil sebelum modal alasan dibuka, supaya user tahu berapa Qty Sisa yang akan
  // dibatalkan sebelum mengisi alasan - dan supaya baris yang sisanya sudah 0 ditolak lebih
  // awal, bukan baru ketahuan setelah alasan diketik.
  public function cekSisa (Request $req) {
    $nobukti = $req->input('nobukti');
    $mode    = $req->input('mode', 'item');
    $urut    = $req->input('urut');

    $rows = $this->barisOutstanding($nobukti, $mode === 'item' ? $urut : null);

    $rowsSisa  = array_filter($rows, [$this, 'bisaDiclose']);
    $totalSisa = array_sum(array_map(function ($r) { return (float) $r->QntSisa; }, $rowsSisa));

    return response()->json([
      'jml' => count($rows),
      'jmlSisa' => count($rowsSisa),
      'totalSisa' => $totalSisa,
    ]);
  }

  // Syarat sebuah baris PO masih boleh diclose. Sengaja sama persis dengan
  // CPO_FILTER_OUTSTANDING (aturan tab Outstanding), supaya tidak mungkin ada baris yang
  // tidak tampil di tab Outstanding tapi tetap ikut tertutup lewat Close per No. Bukti.
  // Sisi PHP, bukan SQL, karena pemanggilnya juga perlu menghitung baris yang dilewati.
  public function bisaDiclose ($r) {
    return (float) $r->QntSisa > 0 && ((float) $r->Qnt - (float) $r->QntBatal) > 0;
  }

  // Baris PO yang jadi bahan penguncian, dibaca dari vwOutPOBatal. $urut null berarti seluruh
  // barang pada No. Bukti tersebut. Sengaja TIDAK memakai CPO_FILTER_OUTSTANDING: yang dipanggil
  // butuh melihat juga baris yang Qty Sisa-nya sudah 0, supaya bisa menghitung berapa item yang
  // dilewati dan melaporkannya ke user.
  private function barisOutstanding ($nobukti, $urut = null) {
    $where = "A.NoBukti = :nobukti";
    $bind  = ["nobukti" => $nobukti];

    if ($urut !== null && $urut !== '') {
      $where .= " and A.Urut = :urut";
      $bind["urut"] = $urut;
    }

    return DB::connection('SML')->select("
      SET NOCOUNT ON
      select A.* from vwOutPOBatal A WITH(NOLOCK) where $where
    ", $bind);
  }

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from dbpo where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbpo set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  
  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    return $res;
  }

  public function onChangeHeaderSP (Request $req) {
    $query = 'update dbso set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);

    $res2 = DB::connection('SML')->select('exec Sp_UpdateSO ?', [$req->bukti]);

    return $res;

  }

  public function spUpdatePO (Request $req) {
    $res = DB::connection('SML')->update('exec Sp_UpdatePO ?', [$req->nobukti]);
    
    return $res;
  }

 public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC Sp_CetakPO ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PO from DBNOMOR');

    $values = [
        $inisial[0]->PO,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listPelanggan (Request $req) {

    $listData = DB::connection('SML')->select("select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat,  
                       Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota ,Y.NPPH23  ,Y.NPPH22 NPPH21     
                       from  DBCUSTSUPP Y         
                       Left Outer Join Dbkota Z on Y.kota=Z.KodeKota   
                       where isnull(Y.JENIS,0)=0    
                     and Y.IsAktif=1 
                       order by Y.KODECUSTSUPP");
    return $listData;
  }

  public function listSales (Request $req) {

    $listData = DB::connection('SML')->select("SELECT keynik, nama FROM dbkaryawan where IsSales = 1");
    return $listData;
  }

  public function listValas (Request $req) {

    $listData = DB::connection('SML')->select("SELECT kodevls, namavls, kurs FROM dbvalas");
    return $listData;
  }

    public function loadOutstandingPPL (Request $req) {

    $listData = DB::connection('SML')->select("declare @Tahun int, @Bulan int

              select @Tahun=2018, @Bulan=78

              SET NOCOUNT ON 
              select  A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
              A.*
              from DBO.vwOutPPL A WITH(NOLOCK)
              where A.SisaPPL>0
              and A.pjasa=0
              order by A.Tanggal, A.NoBukti, A.Urut");
    return $listData;
  }

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select KODEGDG, NAMA, Alamat from DBGUDANG");
    return $listData;
  }

  public function listPIC (Request $req) {

    $listData = DB::connection('SML')->select("select kodepic, nama from DBPICCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listPWO (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.no_bukti,a.tanggal,a.supplier,d.NAMACUSTSUPP, 
                        b.kode,c.NAMABRG,F.QNT qty,F.nmsat satuan    
                        ,F.NOSAT NOsat ,B.harga  
                        from penawaran_po A     
                        left outer join detail_penawaran_po_barang B on A.id= 
                        B.penawaran_id    

                        left outer join DBBARANG c on b.kode=c.KODEBRG 
                        left outer join DBCUSTSUPP d on A.supplier=d.KODECUSTSUPP 
                        left outer join DBREFPRDET E on B.id_rfq=E.ID 
                        left outer join DBPENAWARANSODET F on E.NOBUKTI=F.NoRPR  
                        and E.URUT=F.UrutRPR 
                        left outer join DBSOdet G on F.NOBUKTI=G.NOtawar and  
                        f.URUT=G.urutTawar 

                        where G.NOBUKTI= :noSo" , ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listBarangFOC (Request $req)
  {
    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,A.partNumber,B.NamaMerk 
                                                from Dbbarang a 
                                                Left Outer join dbmerk B on A.kodemerk=b.KodeMerk
                                                where a.isaktif=1");
    return $listData;
  }

  public function listBarangNonFOC1 (Request $req) 
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust 
                                                from vwOutPPL a  
                                                where Isjasa= 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti");
    return $listData;
  }

  public function listBarangNonFOC2 (Request $req) 
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, B.NamaBrg, a.Qnt,a.Qnt2, a.SATUAN Sat,A.Qnt-ISnull(C.Qnt,0) SisaPPL,
                        A.Qnt2- Case When a.NoSAT=2 Then ISnull(C.Qnt2,0) When a.NoSAT=3 Then ISnull(C.Qnt2,0) else ISnull(C.Qnt2,0)*a.ISI end  Sisa2PPL, 
                        a.NoSat, a.Isi, a.NoBukti, a.Urut,0 Tolerate 
                        , B.PartNumber 
                         from DBSODET a   
                        Left Outer Join Dbbarang B on A.kodebrg=B.Kodebrg 
                        left Outer Join (select NoPPL,UrutPPL,Sum(case when nosat=1 then Qnt else Qnt*ISI End) - Sum(case when nosat=1 then QntBatal else QntBatal*ISI End) Qnt   
                        ,Sum(case when Nosat=2 then Qnt   
                        when NOSAT=3 then Qnt                         
                        when NOSAT=1 then Qnt/ISI  End )-            
                        Sum(case when Nosat=2 then QntBatal          
                        when NOSAT=3 then QntBatal                    
                        when NOSAT=1 then QntBatal/ISI  End ) Qnt2 from dbPOdet group by NoPPL,UrutPPL) 
                        C on A.nobukti=C.noppl and A.urut=C.urutPPL 
                         where  isnull(B.Isjasa,0)=0 and  nobukti= :noSo and IsCetakKitir=1 
                        And A.Qnt-ISnull(C.Qnt,0)>0        
                        ", ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listNoSo (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.NOBUKTI,A1.Tanggal, A1.NoPesanan    
                                                from DBSODET A   
                                                Left Outer join DBSO A1 ON A.NOBUKTI=A1.NOBUKTI   
                                                Left Outer Join DBBarang B on  A.KOdebrg=B.Kodebrg   
                                                where A.iscetakkitir=1 and 
                                                Cast(Case when Case when A1.IsOtorisasi1=1 then 1 else 0 end+   
                                                Case when A1.IsOtorisasi2=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi3=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi4=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi5=1 then 1 else 0 end=A1.MaxOL then 0     
                                                else 1             
                                                end As Bit)=0  and A.Nobukti in ( 
                                                  select A.NOBUKTI 
                                                  from DBSODET a    
                                                  left Outer Join (select NoPPL,UrutPPL,Sum(Qnt)- Sum(QntBatal) Qnt from dbPOdet group by NoPPL,UrutPPL )   
                                                  C on A.nobukti=C.noppl and A.urut=C.urutPPL      
                                                  where   IsCetakKitir=1 And A.Qnt-ISnull(C.Qnt,0)>0)  
                                                Group By A.NOBUKTI,A1.Tanggal, A1.NoPesanan  
                                                order by A.NOBUKTI,A1.Tanggal");
    return $listData;
  }

  public function listLokasiPenerima (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeCustsupp, a.NamaCustSupp NamaCust, A.Alamat, A.Telpon 
                            from vwBrowsExpedisi A 
                            where a.isaktif=1 
                            Order by a.kodecustsupp");
    return $listData;
  }

  public function listBackOffice (Request $req) {

    $listData = DB::connection('SML')->select("select keynik, fullname from [user] order by keynik" );
    return $listData;
  }

  public function listBarang (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1

    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                    A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                    from DBbarang a
                    left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                    Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                    where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and (A.KodeBrg like '%" . $req->input('search') . "%') or (a.NamaBrg like '%" . $req->input('search') . "%')
                    and isnull(A.Isaktif,0)=1
                    order by a.Kodebrg ASC");
    return $listData;
  }

  public function cekKreditHari (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $listData = DB::connection('SML')->select("select hari from dbcustsupp where KODECUSTSUPP = :kodepelanggan", ["kodepelanggan" => $req->kodepelanggan]);
    return $listData;
  }

  public function getSatuanBarang (Request $req) {
    return DB::connection('SML')->select("select SAT1, SAT2,SAT3 , ISI1,ISI2,ISI3 from dbbarang where kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang]);

  }

  // Close satu barang. Qty Batal TIDAK lagi diambil dari kiriman browser: nilainya dibaca
  // ulang dari vwOutPOBatal di sini, supaya yang tersimpan pasti Qty Sisa baris itu apa adanya
  // dan tidak bisa dikarang dari sisi klien.
  public function updateCloseBarang (Request $req) {
    $nobukti = $req->Nobukti;
    $urut    = $req->Urut;
    $reason  = trim((string) $req->KetBatal);
    $tanggal = $req->TglBatal ?: date('Y-m-d H:i:s');

    if (!$reason) {
      return response()->json(['success' => false, 'message' => 'Keterangan batal tidak boleh kosong.'], 422);
    }

    if ($urut === null || $urut === '') {
      return response()->json(['success' => false, 'message' => 'Urut wajib dikirim untuk close per barang.'], 422);
    }

    // Validasi dilakukan SEBELUM beginTransaction(), supaya jalur penolakan tidak pernah
    // meninggalkan transaksi menggantung tanpa commit/rollback.
    $baris = $this->barisOutstanding($nobukti, $urut);
    if (!count($baris)) {
      return response()->json(['success' => false, 'message' => 'Data tidak ditemukan di vwOutPOBatal.'], 422);
    }

    $data = $baris[0];
    if (!$this->bisaDiclose($data)) {
      return response()->json(['success' => false, 'message' => 'Qty Sisa sudah 0, barang ini tidak bisa diclose.'], 422);
    }

    DB::connection('SML')->beginTransaction();
    try {
      DB::connection('SML')->update("
        Update DBPODET set QntBatal = :QntBatal, UserBatal = :UserBatal, TglBatal = :TglBatal,
                           isbatal = 1, Ketbatal = :KetBatal
        where Nobukti = :Nobukti and Urut = :Urut
      ", [
        "QntBatal"  => $data->QntSisa,
        "UserBatal" => \Auth::User()->username,
        "TglBatal"  => $tanggal,
        "KetBatal"  => $reason,
        "Nobukti"   => $nobukti,
        "Urut"      => $urut,
      ]);

      DB::connection('SML')->commit();
      return response()->json(['success' => true, 'diclose' => 1, 'dilewati' => 0]);
    } catch (\Throwable $e) {
      DB::connection('SML')->rollBack();
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  // Close seluruh barang pada satu No. Bukti. Versi lama menjalankan satu UPDATE massal
  // "QntBatal = QNT WHERE Nobukti = ?" tanpa filter apa pun - barang yang sudah diterima
  // sebagian ikut dibatalkan sebesar Qty PO penuh, dan barang yang Qty Sisa-nya sudah 0 pun
  // ikut ditandai batal. Sekarang tiap baris dibatalkan sebesar Qty Sisa-nya sendiri, dan
  // baris bersisa 0 dilewati lalu dilaporkan jumlahnya ke user.
  public function updateCloseHeader (Request $req) {
    $nobukti = $req->Nobukti;
    $reason  = trim((string) $req->KetBatal);
    $tanggal = $req->TglBatal ?: date('Y-m-d H:i:s');

    if (!$reason) {
      return response()->json(['success' => false, 'message' => 'Keterangan batal tidak boleh kosong.'], 422);
    }

    $baris = $this->barisOutstanding($nobukti);
    if (!count($baris)) {
      return response()->json(['success' => false, 'message' => 'Data tidak ditemukan di vwOutPOBatal.'], 422);
    }

    $bisaDiclose = array_filter($baris, [$this, 'bisaDiclose']);
    if (!count($bisaDiclose)) {
      return response()->json(['success' => false, 'message' => 'Semua barang pada No. Bukti ini Qty Sisa-nya sudah 0.'], 422);
    }

    DB::connection('SML')->beginTransaction();
    try {
      foreach ($bisaDiclose as $data) {
        DB::connection('SML')->update("
          Update DBPODET set QntBatal = :QntBatal, UserBatal = :UserBatal, TglBatal = :TglBatal,
                             isbatal = 1, Ketbatal = :KetBatal
          where Nobukti = :Nobukti and Urut = :Urut
        ", [
          "QntBatal"  => $data->QntSisa,
          "UserBatal" => \Auth::User()->username,
          "TglBatal"  => $tanggal,
          "KetBatal"  => $reason,
          // vwOutPOBatal menamai kolomnya 'urut' huruf kecil - PHP peduli casing walau SQL
          // Server tidak, jadi jangan diganti jadi $data->Urut.
          "Nobukti"   => $nobukti,
          "Urut"      => $data->urut,
        ]);
      }

      DB::connection('SML')->commit();
      return response()->json([
        'success'  => true,
        'diclose'  => count($bisaDiclose),
        'dilewati' => count($baris) - count($bisaDiclose),
      ]);
    } catch (\Throwable $e) {
      DB::connection('SML')->rollBack();
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  public function updateOpenHeader (Request $req) {
    return $this->bukaKunci($req->Nobukti, null);
  }

  public function updateOpenBarang (Request $req) {
    if ($req->Urut === null || $req->Urut === '') {
      return response()->json(['success' => false, 'message' => 'Urut wajib dikirim untuk open per barang.'], 422);
    }
    return $this->bukaKunci($req->Nobukti, $req->Urut);
  }

  private function bukaKunci ($nobukti, $urut) {
    if (!$nobukti) {
      return response()->json(['success' => false, 'message' => 'No. Bukti tidak boleh kosong.'], 422);
    }

    DB::connection('SML')->beginTransaction();
    try {
      $where = "Nobukti = :Nobukti";
      $bind  = ["Nobukti" => $nobukti];
      if ($urut !== null) {
        $where .= " and Urut = :Urut";
        $bind["Urut"] = $urut;
      }

      $affected = DB::connection('SML')->update("
        Update DBPODET set QntBatal = 0, isbatal = 0, UserBatal = '', TglBatal = Null, Ketbatal = ''
        where $where
      ", $bind);

      if ($affected === 0) {
        throw new \Exception("Tidak ada data yang diupdate. Pastikan No. Bukti dan Urut sesuai.");
      }

      DB::connection('SML')->commit();
      return response()->json(['success' => true]);
    } catch (\Throwable $e) {
      DB::connection('SML')->rollBack();
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  public function spAdd (Request $req) {
    $choice = $req->Choice;
    $jmlrecord = $req->Jmlrecord;
    $nobukti = $req->NoBukti;
    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbpo where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

      $values = [
        $choice, //Choice
        $nobukti, //NoBukti
        $req->NoUrut, //NoUrut
        $req->Tanggal, //Tanggal
        $req->TglJatuhTempo, //TglJatuhTempo
        $req->KodeSupp, //KodeSupp
        0, //Handling
        $req->KodeExp, //KodeExp
        $req->Keterangan, //Keterangan
        '', // FakturSupp
        $req->KodeVls, //KodeVls
        $req->Kurs, //Kurs
        $req->PPn, //PPn
        $req->TipeBayar, //TipeBayar
        $req->Hari, //Hari
        0, //TipeDisc
        0,//Disc
        0, //DiscRp
        $req->Urut, //Urut
        $req->KodeBrg, // KodeBrg
        $req->Qnt, //Qnt
        $req->NoSat, //NoSat
        $req->Satuan, //Satuan
        $req->Isi, //Isi
        $req->Harga, //Harga
        $req->DiscP, //DiscP
        $req->DiscTot, //DiscTot
        $req->NoPPL, //NoPPL
        0,  //IsClose
        0,  //IsCloseD
        '', //Catatan
        0, //IsExp
        0, //Tolerate
        $req->UrutPPL, //UrutPPL
        $req->Kodegdg, //Kodegdg 
        $req->Discpdet2, //Discpdet2
        $req->Discpdet3, //Discpdet3
        0, //Discpdet4
        0, //Discpdet5
        1, //FlagTipe
        $req->NamaBrg, //Namabrg
        0, //IsJasa
        0, //pFirst
        $req->pFOC, //pFOC
        $req->Noso, //Noso
        $req->Jmlrecord, //Jmlrecord
        $req->NOPOCUST, //NOPOCUST
        \Auth::User()->username, //IdUser
        0,  //pJasa
        0,  //NPPH23
        '', //PERKIRAAN
        '', //SatX 
        '', //COST
        '', //SUBCOST
        $req->TglKirim, //Tglkirim
        0, //PPH21
        $req->NOPNw, //NOPNw
        $req->UrutPNW //UrutPNW

      ];
      DB::connection('SML')->statement('exec sp_PO ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
      DB::connection('SML')->update('exec Sp_UpdatePO ?', [$nobukti]);
      return 1;
  }

  public function spCekHarga (Request $req) {
        $harga = DB::connection('SML')->select("Declare @Kodebrg varchar(15)
                                                Set @Kodebrg=:kodebarang
                                                select top 4 b.NOBUKTI,b.TANGGAL,a.KODEBRG,c.NAMABRG,
                                                a.SATUAN,a.QNT,b.KODEVLS,b.KURS,A.HARGA,b.DISCRP,A.NDPP,
                                                ROW_NUMBER() over(PARTITION By A.kodebrg Order by A.kodebrg) as LineNum
                                                ,A.DISCP,A.HrgNetto,A.DiscTot,D.NamaCustSupp
                                                from DBBELIDET A
                                                left outer join DBBELI b on a.NOBUKTI=b.NOBUKTI
                                                left outer join DBBARANG c on a.KODEBRG=c.KODEBRG
                                                Left Outer join dbcustsupp D on B.kodesupp=D.KodeCustSupp
                                                where A.KODEBRG=@Kodebrg
                                                order by b.TANGGAL desc" ,["kodebarang" => $req->kodebarang]);

      return $harga;
  }

  public function cekPoDet (Request $req) {
        $cekPoDet = DB::connection('SML')->select("SELECT * FROM DBPODET WHERE NOBUKTI = 'MGL/PO/00001/0625'");

      return $cekPoDet;
  }

   public function cekSatuanBarang (Request $req) {
        $cekSatuanBarang = DB::connection('SML')->select("select SAT1, ISI1, SAT2, ISI2, SAT3, ISI3 from DBBARANG where KODEBRG = :KodeBrg", ["KodeBrg"=>$req->KodeBrg]);

      return $cekSatuanBarang;
  }

  public function detailBarangAll (Request $req) {
    $barang = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                                              A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                                              from DBbarang a
                                              left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                                              Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                                              where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                                              and A.KodeBrg = :kodebrg
                                              and isnull(A.Isaktif,0)=1
                                              order by a.Kodebrg ASC" , 
                                              ["kodebrg" => $req->kodebrg] );

    $harga = DB::connection('SML')->select("declare @kodebrg varchar(50),@nosat tinyint
                                            select @kodebrg= :kodebarang ,@nosat= :nosat
                                            select top 1 B.KODEBRG,b.NOSAT,b.HARGA,c.ISI2,c.ISI3, a.TANGGAL, b.SATUAN,
                                            case when @nosat=1 then              +
                                                            case when NOSAT=1 then HARGA when NOSAT=2 then HARGA/c.ISI2 when NOSAT=3 then HARGA/ISI3 end
                                                    when @nosat=2 then
                                                            case when NOSAT=2 then HARGA when NOSAT=1 then HARGA*c.ISI2 when NOSAT=3 then (HARGA/ISI3)*c.ISI2 end
                                                    when @nosat=3 then
                                                            case when NOSAT=3 then HARGA when NOSAT=1 then HARGA*c.ISI3 when NOSAT=2 then (HARGA/ISI2)*c.ISI3 end
                                            End Xharga
                                            from DBBELI a
                                            left outer join DBBELIDET b on a.NOBUKTI=b.NOBUKTI
                                            left outer join DBBARANG c on b.KODEBRG=c.KODEBRG
                                            where b.KODEBRG=@kodebrg
                                            order by a.TANGGAL desc  ",
                                            ["kodebarang" => $req->kodebrg , "nosat" => $req->nosat]);

    return ["barang" => $barang , "harga" => $harga];
  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("
    declare @NoBukti varchar(30)

select 	@NoBukti= :nobukti

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KodeSupp, C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
        C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
	A.Handling, A.Keterangan, A.FakturSupp,IsExp,
	A.KodeVls, D.NamaVls, A.Kurs, A.PPN, A.TipeBayar, A.Hari, A.Disc,
	B.Urut, B.KodeBrg, case when B.NAMABRG='' then E.NAMABRG else B.NamaBrg End NamaBrg, B.Satuan, B.Qnt, B.Nosat, B.Isi,
        B.Harga, B.DISCP, B.DISCTOT, B.NoPPL, B.UrutPPL, A.IsClose,B.IsClose IsCloseD,
        case when A.Kurs=1 then 0.0 else B.SubTotal end TotalUSD,a.KodeExp,F.namaCustSupp NamaExp,
	round(B.SubTotal*A.Kurs,2) TotalIDR, round(B.NDPP*A.Kurs,2) NDPP,
        round(B.NPPN*A.Kurs,2) NPPN,isnull(B.Tolerate,0) Tolerate,
	B.BYAngkut Beban,
	round(B.SubTotal*A.Kurs,2) + B.BYAngkut Total,        
        H.TotDiskon, H.TotDPP, H.TotPPN, H.TotNet,
        A.Kodegdg, I.Nama NamaGDG, I.Alamat ALamatGdg,
        x.TotalX,dbo.terbilang(x.totalx) Terbilang,B.Discp2,B.Discp3,B.Discp4,B.Discp5,
        A.PPN PPNTrans,isnull(B.Tolerate,0) ToleratePO,Isnull(B.Isjasa,0) Isjasa,
        B.Discp DiscP1,x1.NosoCust,Isnull(B.pFoc,0) PFOC,A.NOSO,M1.Nopesanan,A.npph23,A.perkiraan,m2.Keterangan Nmperkiraan,B.SatX
        ,B.Cost,b.subcost, Cs.NamaCost, SCs.NamaSubCost,A.TglKirim ,H.TOtSubtotalRP ,A.NPPH21,'' NoPNW,0 UrutPNW,A.FlagTipe
        From dbPO A
Left Outer join dbPODet B on B.NoBukti=a.NoBukti
Left Outer Join dbCustSupp C on c.KodeCustSupp=a.KodeSupp
Left Outer join dbValas D on D.KodeVls=A.KodeVls
Left Outer join dbBarang E on E.KodeBrg=B.KodeBrg
Left Outer join vwBrowsExpedisi F On F.KodeCustSupp=A.KodeExp
Left Outer Join vwMasterPO H on H.NoBukti=A.NoBukti
Left Outer Join (select KodeBrg,NoBukti,Min(Tanggal)Tanggal from dbKirimDet group by KodeBrg,NoBukti) J On J.KodeBrg=B.KodeBrg and J.NoBukti=B.NoBukti
left outer join (select A.Nobukti, sum(round(B.SubTotal*A.Kurs,2) + B.BYAngkut)  totalx from Dbpo A
                        left outer join dbPODet B on A.nobukti = B.nobukti group By A.NObukti) X on A.nobukti = x.nobukti
Left Outer join dbgudang I on I.Kodegdg=A.kodegdg
Left Outer Join DBPPLDet X1 on B.Noppl=X1.Nobukti and B.UrutPPL=X1.Urut
Left Outer join DbSo M1 on A.Noso=M1.nobukti
Left Outer join dbperkiraan M2 on A.perkiraan=M2.Perkiraan
left outer join dbCost Cs on Cs.KodeCost=B.Cost
left outer join vwSubCost SCs on SCs.KodeCost=b.Cost and SCs.KodeSubCost=b.SubCost
where	A.NoBukti=@NoBukti
order by B.Urut", ["nobukti" => $nobukti]);

    return [
      "list" => $list
    ];
  }
}
