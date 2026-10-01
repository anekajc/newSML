<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;

class PembelianClosingPRController extends Controller
{
  const CL_HREF = 'pembelianclosingpr';

  // Kolom default tab "Outstanding PR" (urut 1), sumber vwOutPPL. Nama field harus persis
  // sama dengan yang dikembalikan dataOutstanding() - lihat catatan casing di CL_KOLOM_2.
  const CL_KOLOM_1 = [
    ['field' => 'Nobukti',    'label' => 'No. Bukti',   'tipe' => 0],
    ['field' => 'Tanggal',    'label' => 'Tanggal',     'tipe' => 2],
    ['field' => 'kodebrg',    'label' => 'Kode Barang', 'tipe' => 0],
    ['field' => 'NamaBrg',    'label' => 'Nama Barang', 'tipe' => 0],
    ['field' => 'sat',        'label' => 'Satuan',      'tipe' => 0],
    ['field' => 'Qnt',        'label' => 'Qty PR',      'tipe' => 1],
    ['field' => 'QNTPO',      'label' => 'Qty PO',      'tipe' => 1],
    ['field' => 'SisaPPL',    'label' => 'Sisa PR',     'tipe' => 1],
    ['field' => 'Keterangan', 'label' => 'Keterangan',  'tipe' => 0],
  ];

  // Kolom default tab "Closing PR" (urut 2), sumber DBPPLDET+DBPPL. Casing field ('Kodebrg',
  // 'Sat' berhuruf besar) berbeda dari urut 1 karena tabel sumbernya berbeda - lihat
  // SELECT eksplisit di dataClosing().
  const CL_KOLOM_2 = [
    ['field' => 'Nobukti',   'label' => 'No. Bukti',        'tipe' => 0],
    ['field' => 'Tanggal',   'label' => 'Tanggal',          'tipe' => 2],
    ['field' => 'Kodebrg',   'label' => 'Kode Barang',      'tipe' => 0],
    ['field' => 'NamaBrg',   'label' => 'Nama Barang',      'tipe' => 0],
    ['field' => 'Sat',       'label' => 'Satuan',           'tipe' => 0],
    ['field' => 'Qnt',       'label' => 'Qty PR',           'tipe' => 1],
    ['field' => 'QNTPO',     'label' => 'Qty PO',           'tipe' => 1],
    ['field' => 'Qntbatal',  'label' => 'Qty Batal',        'tipe' => 1],
    ['field' => 'KetBatal',  'label' => 'Keterangan Batal', 'tipe' => 0],
  ];

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index(Request $req) {
    $kodemenu = '030110';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path);
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($clTglAwal, $clTglAkhir) = $this->periodeRange($periode);

    // Konfigurasi kolom kedua tabel diambil AJAX lewat loadAll() saat halaman siap -
    // sejajar POController@index, yang juga tidak mem-pre-fetch header tabel di sini.
    return view('purchasing.pembelianclosingpr', [
        "menul0" => $menul0,
        "periode" => $periode,
        "clTglAwal" => $clTglAwal,
        "clTglAkhir" => $clTglAkhir,
        "akses" => $akses,
    ]);
  }

  // Dipanggil AJAX sinkron sekali di awal halaman untuk mengambil konfigurasi kolom kedua
  // tabel - sejajar POController@loadAll, tapi tanpa data (data ditarik per halaman lewat
  // dataOutstanding()/dataClosing()).
  public function loadAll (Request $req) {
    return response()->json([
      "kolom1" => $this->ambilKolom(1),
      "kolom2" => $this->ambilKolom(2),
    ]);
  }

  // Konfigurasi kolom mandiri untuk halaman ini - tidak lewat HeaderTableController supaya
  // file bersama itu (dipakai 4 halaman lain) tidak perlu disentuh. Penyimpanan tetap lewat
  // endpoint generik saveheadertable/DBHEADERTABLE, hanya pengambilannya yang khusus di sini.
  public function headerTable (Request $req) {
    $urut = (int) $req->input('urut', 1);
    if ($urut !== 2) { $urut = 1; }

    if ($req->input('reset')) {
      DB::connection('SML')->update(
        "delete from DBHEADERTABLE where username = :username and href = :href and urut = :urut",
        ["username" => \Auth::user()->username, "href" => self::CL_HREF, "urut" => $urut]
      );
    }

    return response()->json($this->ambilKolom($urut));
  }

  private function ambilKolom ($urut) {
    $default = $urut === 2 ? self::CL_KOLOM_2 : self::CL_KOLOM_1;
    $username = \Auth::user()->username;

    $saved = DB::connection('SML')->select(
      "select * from DBHEADERTABLE where username = :username and href = :href and urut = :urut",
      ["username" => $username, "href" => self::CL_HREF, "urut" => $urut]
    );

    if (count($saved) > 0) {
      $valueSaved = json_decode($saved[0]->value);

      // aliasordered HARUS sejajar indeksnya dengan headertablevalue (yang bisa sudah
      // digeser urutannya oleh user), jadi label dicari per nilai lewat $labelByField -
      // BUKAN diambil langsung dari $default apa adanya (urutannya selalu tetap default,
      // akan salah pasang label begitu user pernah menggeser kolom). Sama seperti cara
      // HeaderTableController::getHeaderTable() menyusun aliasOrdered lewat pencocokan
      // value, bukan posisi.
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
        // DBHEADERTABLE tidak punya kolom desimal tersendiri - kolom `tipe` dipakai untuk
        // itu, sama seperti purchaseOrder.blade.php.
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

  // Data tab "Outstanding PR" dengan server-side paging DataTables - sejajar
  // POController@dataOutstandingPR.
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

    $allowedOrder = ['Nobukti', 'Tanggal', 'kodebrg', 'NamaBrg', 'sat', 'Qnt', 'QNTPO', 'SisaPPL', 'Keterangan', 'urut'];
    $orderCol = (string) $req->input('orderCol', '');
    $orderDir = strtolower((string) $req->input('orderDir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

    if (in_array($orderCol, $allowedOrder, true)) {
      $orderBy = 'A.[' . $orderCol . '] ' . $orderDir . ', A.NoBukti, A.Urut';
    } else {
      // Default: data terbaru di atas. A.Urut sengaja tetap ASC - itu nomor urut
      // barang di dalam satu No. Bukti, bukan bagian dari "yang terbaru".
      $orderBy = 'A.Tanggal DESC, A.NoBukti DESC, A.Urut';
    }

    $where = 'A.SisaPPL > 0 and ISNULL(A.QntBatal, 0) = 0 and A.Tanggal between :tglawal and :tglakhir';
    $bind  = ["tglawal" => $tglawal, "tglakhir" => $tglakhir];
    $search = trim((string) $req->input('search', ''));
    if ($search !== '') {
      $where .= " and (A.NoBukti like :cari1 or A.kodebrg like :cari2 or A.NamaBrg like :cari3 or A.Keterangan like :cari4)";
      $like = '%' . $search . '%';
      $bind = array_merge($bind, ["cari1" => $like, "cari2" => $like, "cari3" => $like, "cari4" => $like]);
    }

    $jml = DB::connection("SML")->select("
      SET NOCOUNT ON
      select count(1) as jml
      from DBO.vwOutPPL A WITH(NOLOCK)
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
               A.*
        from DBO.vwOutPPL A WITH(NOLOCK)
        where $where
      ) X
      $batasBaris
      order by X.NoBaris
    ", $bind);

    // QNTPO dari view belum dikurangi QntBatalPO (qty PO yang dibatalkan) - dikurangi
    // di sini biar Qty PO yang tampil sudah bersih dari yang dibatalkan, tanpa mengubah
    // definisi view. Catatan: ini beda dengan QntBatal (batal di level PR), yang sudah
    // difilter = 0 lewat $where di atas.
    foreach ($rows as $r) {
      $r->QNTPO = (float) $r->QNTPO - (float) ($r->QntBatalPO ?? 0);
    }

    return [
      "draw" => $draw,
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $rows,
    ];
  }

  // Data tab "Closing PR" dengan server-side paging DataTables. Sumbernya sama dengan
  // loadAll() lama (DBPPLDET+DBPPL+subquery DBPODET), dibungkus jadi derived table supaya
  // bisa diberi ROW_NUMBER().
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

    $allowedOrder = ['Nobukti', 'Tanggal', 'Kodebrg', 'NamaBrg', 'Sat', 'Qnt', 'QNTPO', 'Qntbatal', 'KetBatal', 'Urut'];
    $orderCol = (string) $req->input('orderCol', '');
    $orderDir = strtolower((string) $req->input('orderDir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

    if (in_array($orderCol, $allowedOrder, true)) {
      $orderBy = 'X.[' . $orderCol . '] ' . $orderDir . ', X.Nobukti, X.Urut';
    } else {
      $orderBy = 'X.Tanggal DESC, X.Nobukti DESC, X.Urut';
    }

    $where = '1 = 1 and X.Tanggal between :tglawal and :tglakhir';
    $bind  = ["tglawal" => $tglawal, "tglakhir" => $tglakhir];
    $search = trim((string) $req->input('search', ''));
    if ($search !== '') {
      $where .= " and (X.Nobukti like :cari1 or X.Kodebrg like :cari2 or X.NamaBrg like :cari3 or X.KetBatal like :cari4)";
      $like = '%' . $search . '%';
      $bind = array_merge($bind, ["cari1" => $like, "cari2" => $like, "cari3" => $like, "cari4" => $like]);
    }

    $sqlDasar = "
      select
          A.Nobukti,
          B.Tanggal,
          A.NamaBrg,
          A.Sat,
          A.Qnt,
          ISNULL(C.Qnt, 0.00) AS QNTPO,
          A.Qntbatal,
          A.KetBatal,
          A.Kodebrg,
          A.Urut
      from DBPPLDET A
      LEFT OUTER JOIN DBPPL B ON A.Nobukti = B.Nobukti
      LEFT OUTER JOIN (
          SELECT NoPPL, UrutPPL, SUM(Qnt) AS Qnt
          FROM DBPODET
          GROUP BY NoPPL, UrutPPL
      ) C ON A.Nobukti = C.NoPPL AND A.Urut = C.UrutPPL
      WHERE ISNULL(A.QntBatal, 0) > 0
    ";

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

  // Dipanggil sebelum modal alasan penguncian dibuka, supaya user tahu berapa sisa PR
  // sebelum mengisi alasan - lihat memory pilihan-user-vs-batas-aman soal transparansi ke
  // user, bukan menyembunyikan keputusan di belakang layar.
  public function cekSisa (Request $req) {
    $nobukti = $req->input('nobukti');
    $mode    = $req->input('mode', 'item');
    $urut    = $req->input('urut');

    $query = DB::connection('SML')->table('vwOutPPL')->where('Nobukti', $nobukti);
    if ($mode === 'item') {
      $query->where('urut', $urut);
    }

    $rows = $query->get();
    $jml = $rows->count();
    $rowsSisa = $rows->filter(function ($r) { return (float) $r->SisaPPL > 0; });
    $jmlSisa = $rowsSisa->count();
    $totalSisa = $rowsSisa->sum(function ($r) { return (float) $r->SisaPPL; });

    return response()->json([
      'jml' => $jml,
      'jmlSisa' => $jmlSisa,
      'totalSisa' => $totalSisa,
    ]);
  }

  public function lock(Request $req)
  {
    $nobukti = $req->nobukti;
    $mode    = $req->mode;
    $urut    = $req->urut;
    $reason  = trim($req->reason);
    $user    = \Auth::user()->username;
    $tanggal = date('Y-m-d');

    if (!$reason) {
        return response()->json([
            'success' => false,
            'message' => 'Alasan penguncian tidak boleh kosong.'
        ], 422);
    }

    // Validasi (termasuk cek Sisa PR) dilakukan SEBELUM beginTransaction(), supaya jalur
    // penolakan tidak pernah meninggalkan transaksi menggantung tanpa commit/rollback.
    if ($mode === 'item') {
        if (is_null($urut)) {
            return response()->json([
                'success' => false,
                'message' => 'URUT wajib dikirim untuk mode=item'
            ], 422);
        }

        $data = DB::connection('SML')
            ->table('vwOutPPL')
            ->where('Nobukti', $nobukti)
            ->where('urut', $urut)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan di vwOutPPL.'
            ], 422);
        }

        if ((float) $data->SisaPPL <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sisa PR sudah 0, item ini tidak bisa diclose.'
            ], 422);
        }

        DB::connection('SML')->beginTransaction();
        try {
            DB::connection('SML')->table('DBPPLDET')
                ->where('Nobukti', $data->Nobukti)
                ->where('Urut', $data->urut)
                ->update([
                    'Qntbatal'  => $data->SisaPPL,
                    'KetBatal'  => $reason,
                    'userbatal' => $user,
                    'Tglbatal'  => $tanggal,
                    'IsBatal'   => 1
                ]);

            DB::connection('SML')->commit();
            return response()->json(['success' => true, 'diclose' => 1, 'dilewati' => 0]);
        } catch (\Throwable $e) {
            DB::connection('SML')->rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    } else {
        $dataList = DB::connection('SML')
            ->table('vwOutPPL')
            ->where('Nobukti', $nobukti)
            ->get();

        if ($dataList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan di vwOutPPL.'
            ], 422);
        }

        // Item bersisa 0 dilewati (tidak di-close) - lihat memory pilihan-user-vs-batas-aman
        // dan diskusi: kalau sisa PR 0, Qty Batal tidak boleh diisi 0 juga.
        $bisaDiclose = $dataList->filter(function ($d) { return (float) $d->SisaPPL > 0; });

        if ($bisaDiclose->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua item pada No. Bukti ini sisa PR-nya sudah 0.'
            ], 422);
        }

        DB::connection('SML')->beginTransaction();
        try {
            foreach ($bisaDiclose as $data) {
                DB::connection('SML')->table('DBPPLDET')
                    ->where('Nobukti', $data->Nobukti)
                    ->where('Urut', $data->urut)
                    ->update([
                        'Qntbatal'  => $data->SisaPPL,
                        'KetBatal'  => $reason,
                        'userbatal' => $user,
                        'Tglbatal'  => $tanggal,
                        'IsBatal'   => 1
                    ]);
            }

            DB::connection('SML')->commit();
            return response()->json([
                'success' => true,
                'diclose' => $bisaDiclose->count(),
                'dilewati' => $dataList->count() - $bisaDiclose->count(),
            ]);
        } catch (\Throwable $e) {
            DB::connection('SML')->rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
  }

  public function unlock(Request $req)
  {
    $nobukti = $req->nobukti;
    $urut    = $req->urut;
    $mode    = $req->mode ?? 'all';

    if (!$nobukti) {
        return response()->json([
            'success' => false,
            'message' => 'No. Bukti tidak boleh kosong.'
        ], 422);
    }

    if ($mode === 'item' && !$urut) {
        return response()->json([
            'success' => false,
            'message' => 'URUT tidak boleh kosong untuk mode=item'
        ], 422);
    }

    DB::connection('SML')->beginTransaction();
    try {
        $query = DB::connection('SML')->table('DBPPLDET')->where('NOBUKTI', $nobukti);

        if ($mode === 'item') {
            $query->where('URUT', $urut);
        }

        $affected = $query->update([
            'Isbatal'   => 0,
            'Tglbatal'  => null,
            'UserBatal' => '',
            'KetBatal'  => '',
            'Qntbatal'  => 0
        ]);

        if ($affected === 0) {
            throw new \Exception("Tidak ada data yang diupdate. Pastikan No. Bukti dan URUT sesuai.");
        }

        DB::connection('SML')->commit();
        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        DB::connection('SML')->rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
  }

}
