<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use App\Traits\PenjagaTransaksi;


// use App\Http\Controllers\NewMenuController;

// Menu PR gabungan: PR Agen, PR Non Agen, dan PR Non Stock dilayani satu halaman
// (/pembelianpermintaanagen). Menu lama /pembelianpermintaannonagen dan
// /pembelianpermintaannonstock sudah dimatikan (route di-comment, href di DBMENU/DBMENUWEB
// dikosongkan) - lihat docs/update-db-DBSMLBARU/purchasing/CATATAN-UPDATE-DB-PURCHASING.md.
//
// Ketiga jenis tetap dibedakan seperti waktu masih 3 menu:
// - hak akses tetap per kode menu lamanya (030102 / 030101 / 030103),
// - prefix nomor bukti tetap per jenis (DBNOMOR.PRA / PR / PNS),
// - nilai pagen/isjasa/pjasa yang dikirim ke sp_PPL tetap per jenis,
// - browse barang tetap per jenis (lihat listBarang()).
class PembelianPermintaanAgenController extends Controller
{
  use PenjagaTransaksi;

    // Kunci jenis dipakai juga di JS (PR_JENIS di pembelianpermintaanagen.blade.php).
    // 'nomor' = nama kolom DBNOMOR yang menyimpan inisial nomor bukti jenis itu.
    const JENIS = [
        'agen'     => ['label' => 'Agen',      'kodemenu' => '030102', 'nomor' => 'PRA', 'isjasa' => 0, 'pagen' => 1, 'pjasa' => 0],
        'nonagen'  => ['label' => 'Non Agen',  'kodemenu' => '030101', 'nomor' => 'PR',  'isjasa' => 0, 'pagen' => 0, 'pjasa' => 0],
        'nonstock' => ['label' => 'Non Stock', 'kodemenu' => '030103', 'nomor' => 'PNS', 'isjasa' => 1, 'pagen' => 0, 'pjasa' => 1],
    ];

    private $cacheAkses = null;

    // Hak akses user per jenis, diambil dari DBFLMENUWEB berdasarkan KODEMENU masing-masing
    // jenis (bukan lewat href seperti GlobalController@getAkses, karena href menu Non Agen dan
    // Non Stock sudah dikosongkan). Hanya jenis yang HASACCESS = 1 yang ikut.
    private function aksesJenis()
    {
        if ($this->cacheAkses !== null) {
            return $this->cacheAkses;
        }

        $kodeKeJenis = [];
        foreach (self::JENIS as $jenis => $j) {
            $kodeKeJenis[$j['kodemenu']] = $jenis;
        }

        $rows = DB::connection('SML')->select(
            "SELECT m.KODEMENU, f.HASACCESS, f.ISTAMBAH, f.ISKOREKSI, f.ISHAPUS, f.ISCETAK, f.IsOtorisasi1, f.IsBatal
             FROM DBMENUWEB m
             JOIN DBFLMENUWEB f ON f.L1 = m.KODEMENU
             WHERE f.USERID = ? AND f.HASACCESS = 1 AND m.KODEMENU IN (?, ?, ?)",
            [\Auth::user()->username, '030102', '030101', '030103']
        );

        $akses = [];
        foreach ($rows as $r) {
            $jenis = $kodeKeJenis[trim($r->KODEMENU)] ?? null;
            if (!$jenis) {
                continue;
            }
            $akses[$jenis] = [
                'HASACCESS'    => (int) $r->HASACCESS,
                'ISTAMBAH'     => (int) $r->ISTAMBAH,
                'ISKOREKSI'    => (int) $r->ISKOREKSI,
                'ISHAPUS'      => (int) $r->ISHAPUS,
                'ISCETAK'      => (int) $r->ISCETAK,
                'IsOtorisasi1' => (int) $r->IsOtorisasi1,
                'IsBatal'      => (int) $r->IsBatal,
            ];
        }

        // Urutan tetap Agen, Non Agen, Non Stock (urutan dropdown Jenis PR).
        $urut = [];
        foreach (array_keys(self::JENIS) as $jenis) {
            if (isset($akses[$jenis])) {
                $urut[$jenis] = $akses[$jenis];
            }
        }
        return $this->cacheAkses = $urut;
    }

    // Jenis dari kiriman browser, hanya kalau kuncinya dikenal.
    private function jenisRequest(Request $req)
    {
        $jenis = (string) $req->input('jenis');
        return isset(self::JENIS[$jenis]) ? $jenis : null;
    }

    // Jenis PR yang sudah tersimpan, dibaca dari header DBPPL. Data lama ada yang pJasa-nya
    // NULL, jadi NULL dianggap 0 (sama seperti filter vwPPL waktu masih 3 menu).
    private function jenisDokumen($pr)
    {
        if ((int) ($pr->pjasa ?? 0) === 1) { return 'nonstock'; }
        if ((int) ($pr->pagen ?? 0) === 1) { return 'agen'; }
        return 'nonagen';
    }

    // null = boleh, selain itu jawaban penolakan.
    private function cekAkses($jenis, $hak)
    {
        $akses = $this->aksesJenis();
        if (!$jenis || !isset($akses[$jenis])) {
            return $this->tolak('Anda tidak punya akses PR ' . ($jenis ? self::JENIS[$jenis]['label'] : '') . '.');
        }
        if ($hak && empty($akses[$jenis][$hak])) {
            return $this->tolak('No access');
        }
        return null;
    }

    public function index(Request $req)
    {
        $akses = $this->aksesJenis();

        if (!$akses) {
            return redirect('/home');
        }

        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $menul0  = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

        // Label jenis untuk dropdown Jenis PR & filter, hanya jenis yang boleh diakses user.
        $jenisList = [];
        foreach ($akses as $jenis => $a) {
            $jenisList[] = ['kode' => $jenis, 'label' => self::JENIS[$jenis]['label']];
        }

        // ?jenis=nonagen dst. dipakai sebagai filter awal tabel (mis. dari link lama).
        $jenisAwal = $this->jenisRequest($req);
        if ($jenisAwal && !isset($akses[$jenisAwal])) {
            $jenisAwal = null;
        }

        // Data tabel (listData1/listData2, header kolom, dsb) TIDAK disiapkan di sini karena
        // blade memuatnya sendiri lewat AJAX loadAll() saat halaman ready - menyiapkannya di
        // sini dobel kerja dan bikin buka halaman lemot.
        return view('purchasing.pembelianpermintaanagen', [
            "menul0"    => $menul0,
            "periode"   => $periode,
            "aksesJenis"=> $akses,
            "jenisList" => $jenisList,
            "jenisAwal" => $jenisAwal,
        ]);
    }


    // Tanggal filter dari browser hanya dipakai kalau formatnya Y-m-d, tanggalnya ada di kalender,
    // dan tahunnya 1900-9999. Selain itu (mis. "0202-01-01" waktu user masih mengetik tahun)
    // SQL Server datetime menolak dengan error out-of-range, jadi dipakai satu bulan periode kerja.
    private function rentangTanggal (Request $req) {
      $sah = function ($tgl) {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $tgl, $m)) { return false; }
        return (int) $m[1] >= 1900 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
      };
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
      $awal  = $sah($req->tglawal)  ? $req->tglawal  : date('Y-m-01', $stamp);
      $akhir = $sah($req->tglakhir) ? $req->tglakhir : date('Y-m-t', $stamp);
      return [$awal, $akhir];
    }

    public function loadAll(Request $req)
    {
        list($tglawalSah, $tglakhirSah) = $this->rentangTanggal($req);

        $queryOtorisasi = '';

        if (in_array((string) $req->isoto, ['0', '1'], true)) {
            $queryOtorisasi = ' AND IsOtorisasi1 = ' . (int) $req->isoto;
        }

        // Hanya jenis yang boleh diakses user yang ikut dimuat. Syarat tiap jenis sama
        // persis dengan filter vwPPL waktu masih 3 menu terpisah.
        $syaratJenis = [
            'agen'     => '(IsJasa = 0 AND pAgen = 1)',
            'nonagen'  => '(IsJasa = 0 AND pAgen = 0)',
            'nonstock' => '(IsJasa = 1 AND pAgen = 0)',
        ];
        $syarat = [];
        foreach (array_keys($this->aksesJenis()) as $jenis) {
            $syarat[] = $syaratJenis[$jenis];
        }

        $outstanding = [];
        if ($syarat) {
            // Qnt/QntBatal/QntPO ikut diambil (dengan alias eksplisit, supaya key JSON-nya pasti
            // "Qnt"/"QntBatal"/"QntPO") untuk menghitung badge Status "Sudah/Belum/Batal" di
            // pembelianpermintaanagen.blade.php (prStatusPR()). JenisKode dipakai JS untuk filter
            // Jenis & hak akses per baris. Keempatnya dikecualikan dari penurunan kolom default
            // di bawah supaya tidak ikut jadi kolom tabel. Jenis sengaja diletakkan tepat
            // sesudah NoBukti supaya kolomnya tampil di sebelah No Bukti.
            $outstanding = DB::connection("SML")->select(
                "SELECT NoBukti,
                        CASE WHEN IsJasa = 1 THEN 'Non Stock' WHEN pAgen = 1 THEN 'Agen' ELSE 'Non Agen' END AS Jenis,
                        CONVERT(varchar(10), Tanggal, 23) AS Tanggal, IsOtorisasi1, TglOto1, OtoUser1,
                        Qnt AS Qnt, QntBatal AS QntBatal, QntPO AS QntPO,
                        CASE WHEN IsJasa = 1 THEN 'nonstock' WHEN pAgen = 1 THEN 'agen' ELSE 'nonagen' END AS JenisKode
                 FROM vwPPL
                 WHERE Tanggal BETWEEN :tglawal AND :tglakhir
                   AND (" . implode(' OR ', $syarat) . ")
                   $queryOtorisasi
                 ORDER BY Tanggal DESC, NoBukti DESC",
                [
                    "tglawal"  => $tglawalSah,
                    "tglakhir" => $tglakhirSah
                ]
            );
        }

        $collection = collect($outstanding)->groupBy('NoBukti');

        $tempOutstanding = [];
        foreach ($collection as $groupedData) {
            $tempOutstanding[] = $groupedData;
        }

        $tempOtorisasi = [];

        $username = \Auth::user()->username;

        $headertable = DB::connection("SML")->select("select * from dbheadertable where href= :href and username = :username", ["username" => $username, "href" => $req->href]);
        $isnumberheadertable = [];
        $headertablevalue = [];
        $headertableheader = [];
        $headerisshown = [];
        $aliasOrdered = [];

        $isparsed = 0;
        $headertablealias = [];

        $xxx = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS where href = :href", ["href" => $req->href]);
        if ($xxx) {
            $headertablealias = json_decode($xxx[0]->value) ?: [];
        } else {
            $headertablealias = [];
        }
        if (count($headertable) > 0) {
            $isnumberheadertable = json_decode($headertable[0]->isnumber) ?: [];
            $headertablevalue = json_decode($headertable[0]->value) ?: [];
            $headertableheader = json_decode($headertable[0]->header) ?: [];
            $isparsed = 0;
            $headerisshown = json_decode($headertable[0]->isshown) ?: [];

            // Pengaturan kolom yang disimpan sebelum menu digabung belum punya kolom Jenis -
            // disisipkan sesudah NoBukti supaya tetap tampil tanpa user harus reset kolom.
            if (!in_array('Jenis', $headertablevalue, true)) {
                $pos = array_search('NoBukti', $headertablevalue, true);
                $pos = $pos === false ? 0 : $pos + 1;
                array_splice($headertablevalue, $pos, 0, ['Jenis']);
                array_splice($headertableheader, $pos, 0, ['Jenis']);
                array_splice($headerisshown, $pos, 0, [1]);
                array_splice($isnumberheadertable, $pos, 0, [0]);
            }
        } else {
            $isparsed = 1;
            // Qnt/QntBatal/QntPO/JenisKode dipakai hanya di JS, bukan kolom tabel - lihat
            // komentar di query vwppl di atas.
            $kolomInternal = ['Qnt', 'QntBatal', 'QntPO', 'JenisKode'];
            if (!$tempOutstanding) {
            } else {
                foreach ($tempOutstanding[0][0] as $key => $value) {
                    if (str_contains($key, "Oto") || in_array($key, $kolomInternal, true)) {
                    } else {
                        array_push($headertablevalue, $key);
                        array_push($headertableheader, $key);
                        array_push($headerisshown, 1);
                        if ($key === 'Jenis') {
                            // strtotime() menganggap "Agen" dsb. bukan tanggal, tapi dipatok
                            // teks supaya tidak bergantung ke tebakan itu.
                            array_push($isnumberheadertable, 0);
                        } else if (strtotime($value)) {
                            array_push($isnumberheadertable, 2);
                        } else if (is_numeric($value)) {
                            array_push($isnumberheadertable, 1);
                        } else {
                            array_push($isnumberheadertable, 0);
                        }
                    }
                }
            }
        }

        // Satu alias per kolom, urutannya ikut kolom. Kolom yang belum punya alias (mis. Jenis
        // di DBHEADERTABLEALIAS lama) memakai namanya sendiri - tanpa ini indeks alias bergeser
        // dari indeks kolom dan judul kolom sesudahnya jadi salah.
        $aliasOrdered = [];
        foreach ($headertablevalue as $header) {
            $ketemu = null;
            foreach ($headertablealias as $item) {
                if (isset($item->value) && $item->value === $header) {
                    $ketemu = $item;
                    break;
                }
            }
            array_push($aliasOrdered, $ketemu ?: ["value" => $header, "alias" => $header]);
        }

        return [
            "aliasordered"      => $aliasOrdered,
            "headertableheader" => $headertableheader,
            "isnumeric"         => $isnumberheadertable,
            "headertablevalue"  => $headertablevalue,
            "isparsed"          => $isparsed,
            "isshown"           => $headerisshown,
            "listData1"         => $tempOutstanding,   // Belum Otorisasi
            "listData2"         => $tempOtorisasi      // Sudah Otorisasi
        ];
    }

  public function updateOtorisasi(Request $req) {
    $pr = $this->headerDokumen('DBPPL', $req->nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    $tolak = $this->cekAkses($this->jenisDokumen($pr), 'IsOtorisasi1');
    if ($tolak) {
      return $tolak;
    }
    if ((int) $pr->isotorisasi1 === 1) {
      return $this->tolak('PR ' . $req->nobukti . ' sudah diotorisasi oleh ' . $pr->otouser1 . '.');
    }
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti AND isnull(IsOtorisasi1, 0) = 0",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PR',$req->nobukti,'',0,'DBPPL');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $pr = $this->headerDokumen('DBPPL', $req->nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    $tolak = $this->cekAkses($this->jenisDokumen($pr), 'IsBatal');
    if ($tolak) {
      return $tolak;
    }
    if ((int) $pr->isotorisasi1 !== 1) {
      return $this->tolak('PR ' . $req->nobukti . ' belum diotorisasi.');
    }
    // PR yang sudah dibuat PO tidak boleh dibatalkan: sesudah batal otorisasi itemnya bisa
    // diedit/dihapus, padahal DBPODET.NoPPL/UrutPPL masih menunjuk item itu.
    $po = $this->dipakaiOleh('DBPODET', 'NoPPL', $req->nobukti);
    if ($po) {
      return $this->tolak('PR ' . $req->nobukti . ' sudah dibuat PO ' . $po . ', otorisasi tidak bisa dibatalkan.');
    }
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]


    );

     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPPL');
    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPPL where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    return DB::connection('SML')->select(
      "select * from VwPPL where NoBukti = :nobukti order by Urut",
      ["nobukti" => $req->nobukti]
    );
  }


  public function listDepartemen (Request $req) {
    $listDepartemen = DB::connection("SML")->select('select * from DBDEPART');
    return $listDepartemen;
  }

  // Nomor bukti baru per jenis: inisialnya dari kolom DBNOMOR milik jenis itu
  // (Agen = PRA, Non Agen = PR, Non Stock = PNS), sama seperti waktu masih 3 menu.
  public function getNoBukti (Request $req) {
    $jenis = $this->jenisRequest($req) ?: 'agen';
    $tolak = $this->cekAkses($jenis, 'ISTAMBAH');
    if ($tolak) {
      return $tolak;
    }

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // Nama kolom dari konstanta JENIS (bukan dari input user), aman disambung ke SQL.
    $kolom = self::JENIS[$jenis]['nomor'];
    $inisial = DB::connection("SML")->select("select $kolom as inisial from DBNOMOR");
    $values = [
        $inisial[0]->inisial,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  // Browse barang per jenis:
  // - Agen     : KODEGRP 'BJ', pAgen = 1
  // - Non Agen : KODEGRP 'BJ', pAgen = 0
  // - Non Stock: KODEGRP 'JS', IsJasa = 1 (merk boleh kosong, jadi LEFT JOIN)
  public function listBarang (Request $req) {
    if (!$req->filled('search')) {
        return response()->json([]);
    }

    $jenis = $this->jenisRequest($req) ?: 'agen';
    if (!isset($this->aksesJenis()[$jenis])) {
        return response()->json([]);
    }

    $search = "%" . $req->input('search') . "%";

    if ($jenis === 'nonstock') {
        $listData = DB::connection('SML')->select("
            SELECT
                b.NAMAMERK,
                a.KODEBRG,
                a.NAMABRG,
                a.KODEMERK,
                a.PartNumber,
                a.SAT1,
                a.SAT2,
                a.SAT3,
                a.ISI1,
                a.ISI2,
                a.ISI3
            FROM dbbarang a
            LEFT JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
            WHERE a.KODEGRP = 'JS'
            AND a.IsJasa = 1
            AND a.IsAktif = 1
            AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
            ORDER BY a.KODEBRG ASC
        ", [
            $search,
            $search
        ]);

        return response()->json($listData);
    }

    $listData = DB::connection('SML')->select("
        SELECT
            b.NAMAMERK,
            a.KODEBRG,
            a.NAMABRG,
            a.KODEMERK,
            a.PartNumber,
            a.SAT1,
            a.SAT2,
            a.SAT3,
            a.ISI1,
            a.ISI2,
            a.ISI3
        FROM dbbarang a
        JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
        WHERE a.KODEGRP = 'BJ'
	AND a.IsAktif = 1
        AND a.pAgen = ?
        AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
        ORDER BY a.KODEBRG ASC
    ", [
        self::JENIS[$jenis]['pagen'],
        $search,
        $search
    ]);

    return response()->json($listData);
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;

    // Jenis PR baru diambil dari dropdown Jenis PR; PR yang sudah ada selalu memakai jenis
    // header DBPPL-nya, supaya item yang ditambahkan belakangan tidak bisa beda jenis.
    $prAda = $this->headerDokumen('DBPPL', $nobukti);
    // No bukti sudah dipakai orang lain (item pertama PR baru): JS minta no bukti baru.
    if ($choice == "I" && $jmlrecord == 0 && $prAda) {
      return 2;
    }
    $jenis = $prAda ? $this->jenisDokumen($prAda) : $this->jenisRequest($req);
    // PR baru butuh hak Tambah. Untuk PR yang sudah ada, hak Tambah/Koreksi per tombol tetap
    // dicek di JS seperti sebelumnya; di sini cukup dipastikan user punya akses jenisnya.
    $tolak = $this->cekAkses($jenis, $prAda ? null : 'ISTAMBAH');
    if ($tolak) {
      return $tolak;
    }
    $j = self::JENIS[$jenis];

     $purut = DB::connection('SML')->select('select * from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else {
            // return 'mmm';
            $xurut = $req->urut;
        }

    }else{
        // return 'ttt';
        $xurut=1;
    }

    // Semua item dihapus di form edit (sp_PPL ikut menghapus header) lalu user menambah item
    // lagi: JS masih mengirim jmlrecord 1, jadi header dibuat ulang di sini, bukan ditolak.
    if ($choice == "I" && $jmlrecord != 0 && !$prAda) {
      $jmlrecord = 0;
    }
    if ($choice == "I" && $jmlrecord == 0) {
        // PR baru - sudah dipastikan belum ada di atas.
    } else {
      $tolak = $this->cekUbahPR($nobukti, $choice == 'U' ? $req->urut : null);
      if ($tolak) {
        return $tolak;
      }
    }
    $jmlSebelum = $this->jumlahBaris('DBPPLDET', $nobukti);
    $values = [
        $choice,
        $nobukti,
        strval($req->nourut),
        date('Y-m-d H:i:s', strtotime($req->tanggal)),
        (int) $req->urut,
        $req->kodebarang,
         $req->qnt,
        (int) $req->nosat,
        $req->satuan,
         $req->isi,
        $req->keterangan ?? '',
        (int) $req->isclose,
        (int) $req->isclosed,
        $req->kodedepartemen,
        $req->keterangannama ?? '',
        (int) $j['isjasa'],
        $req->noso ?? '',
        (int) $req->urutso,
        (int) $j['pagen'],
        $req->nopocust ?? '',
        (int) $jmlrecord,
        (int) $j['pjasa']
    ];
    DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    if ($choice == 'I' && $this->jumlahBaris('DBPPLDET', $nobukti) <= $jmlSebelum) {
      return $this->tolak('Item gagal disimpan. Silakan coba lagi.');
    }

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$nobukti,'',$xurut,'DBPPLDET');

    return 1;
  }


  public function spDelete (Request $req) {
    $pr = $this->headerDokumen('DBPPL', $req->nobukti);
    if ($pr) {
      $tolak = $this->cekAkses($this->jenisDokumen($pr), 'ISHAPUS');
      if ($tolak) {
        return $tolak;
      }
    }
    $tolak = $this->cekUbahPR($req->nobukti, $req->urut);
    if ($tolak) {
      return $tolak;
    }
    $j = self::JENIS[$this->jenisDokumen($pr)];

    // $listData = $req->listData;
    // foreach ($listData as $d) {
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$req->nobukti,'',$req->urut,'DBPPLDET');
      // code...
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->urut,
        $req->kodebarang,
        $req->qnt,
        $req->nosat,
        $req->satuan,
        $req->isi,
        $req->keterangan,
        $req->isclose,
        $req->isclosed,
        $req->kddep,
        $req->keterangannama,
        $j['isjasa'],
        $req->noso,
        $req->urutso,
        $j['pagen'],
        $req->nopocust,
        $req->jmlrecord,
        $j['pjasa']

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    // }
    return 1;
  }

  // Data cetak PR (semua jenis). Dipindah dari PembelianPermintaanNonAgenController supaya
  // route /purchaseRequestCetak tetap hidup setelah controller Non Agen tidak dipakai lagi.
  public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC CetakPR ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


  // PR yang sudah diotorisasi tidak boleh diubah, dan item yang sudah dibuat PO tidak boleh
  // diedit/dihapus. Mengembalikan jawaban penolakan, atau null kalau boleh.
  private function cekUbahPR($nobukti, $urut = null) {
    $pr = $this->headerDokumen('DBPPL', $nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $pr->isotorisasi1 === 1) {
      return $this->tolak('PR ' . $nobukti . ' sudah diotorisasi, tidak bisa diubah.');
    }
    if ($urut !== null) {
      $po = $this->dipakaiOleh('DBPODET', 'NoPPL', $nobukti, 'UrutPPL', $urut);
      if ($po) {
        return $this->tolak('Item ini sudah dibuat PO ' . $po . ', tidak bisa diubah atau dihapus.');
      }
    }
    return null;
  }

}
