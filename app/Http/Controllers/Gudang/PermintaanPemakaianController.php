<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWMASTERPRPENYERAHANBHN;


class PermintaanPemakaianController extends Controller
{

    // Satu list gabungan (belum + sudah otorisasi) untuk rentang tanggal tertentu.
    // TANGGAL membawa komponen waktu (lihat CetakPRPemakaianBahan), jadi dipakai
    // rentang setengah-terbuka [date1, date2+1hari) bukan BETWEEN supaya baris yang
    // timestamp-nya di tanggal akhir tidak ikut terbuang.
    private function fetchList(string $date1, string $date2)
    {
        $rows = VWMASTERPRPENYERAHANBHN::where('TANGGAL', '>=', $date1)
            ->where('TANGGAL', '<', date('Y-m-d', strtotime($date2 . ' +1 day')))
            ->orderBy('Tanggal', 'desc')->orderBy('NOBUKTI', 'asc')->orderBy('URUT', 'asc')
            ->get()->groupBy('NOBUKTI');

        $out = [];
        foreach ($rows as $g) {
            $row = $g[0];

            // QntOS di VWMASTERPRPENYERAHANBHN berlevel detail, sedangkan list ini hanya
            // memakai baris pertama tiap NOBUKTI. Status header baru benar kalau seluruh
            // baris dijumlahkan: 0 = semua item sudah terkirim, > 0 = masih ada sisa.
            $key = null;
            foreach (array_keys($row->getAttributes()) as $k) {
                if (strcasecmp($k, 'QntOS') === 0) {
                    $key = $k;
                    break;
                }
            }

            if ($key !== null) {
                $total = 0;
                foreach ($g as $d) {
                    $total += (float) $d->{$key};
                }
                $row->{$key} = $total;
            }

            array_push($out, $row);
        }
        return $out;
    }

    public function index(Request $req)
    {
        // kalo nosat 1 qnt , qnt2 langsung dr input
        //kalo nosat 2/3 , qnt dikali isi qnt 2 dr input
        $kodemenu = '06011';
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu, $req->path());
        if (!$akses || !$akses->HASACCESS) {
            return redirect('/home');
        }

        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

        $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

        return view('gudang.permintaanpemakaian', [
            "periode" => $periode,
            "menul0" => $menul0,
            "date1" => $date1,
            "date2" => $date2,
            "penerimaanArray" => $this->fetchList($date1, $date2),
            "akses" => $akses
        ]);
    }

    public function listBarang(Request $req)
    {
        $search = trim($req->input('search'));
        $query  = "select KODEBRG,NAMABRG, PartNumber, SAT1,SAT2,SAT3, ISI1,ISI2,ISI3 from DBBARANG
               where isnull(ISAKTIF,0)=1 and KODEGRP = 'BJ'";
        $params = [];

        if ($search !== '') {
            $query .= " and ((KodeBrg like :filterKode) or (NamaBrg like :filterNama))";
            $params = ["filterKode" => "%$search%", "filterNama" => "%$search%"];
        }

        $query .= " order by KODEBRG";

        return DB::connection('SML')->select($query, $params);
    }

    public function listGudang(Request $req)
    {
        $listLokasi = DB::connection('SML')->select("select KODEGDG, NAMA from DBGUDANG");
        return $listLokasi;
    }


    public function detailPenerimaan(Request $req)
    {
        $penerimaan = VWMASTERPRPENYERAHANBHN::all()->where('NOBUKTI', $req->input('nobukti'))->sortBy('URUT');
        $tempPenerimaan = [];
        foreach ($penerimaan as $p) {
            array_push($tempPenerimaan, $p);
        }
        return $tempPenerimaan;
    }

    public function updateOtorisasi(Request $req)
    {
        $tanggal = now();
        $res = DB::connection('SML')->update(
            "UPDATE dbPRPenyerahanBhn SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti",
            [
                "username" => \Auth::user()->username,
                "tanggal" => $tanggal,
                "nobukti" => $req->nobukti
            ]
        );
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'PRP', $req->nobukti, '', 0, 'dbPRPenyerahanBhn');
        return $res;
    }
    public function updateBatalOtorisasi(Request $req)
    {
        $res = DB::connection('SML')->update(
            "UPDATE dbPRPenyerahanBhn SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
            [
                "nobukti" => $req->nobukti
            ]


        );
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'PR', $req->nobukti, $req->pket, 0, 'dbPRPenyerahanBhn');
        return $res;
    }

    public function getDetailCetak(Request $req)
    {
        $noBukti = $req->input('NOBUKTI');

        $cetak = DB::connection("SML")->select(
            "EXEC dbo.CetakPRPemakaianBahan ?",
            [$noBukti]
        );

        $tempCetak1 = [];
        foreach ($cetak as $p) {
            array_push($tempCetak1, $p);
        }

        return $tempCetak1;
    }

    public function loadAll(Request $req)
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        $date1 = $req->input('date1');
        $date2 = $req->input('date2');
        if (!$date1 || !$date2) {
            $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
            $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        }

        return [
            "penerimaan" => $this->fetchList($date1, $date2),
        ];
    }


    public function spAdd(Request $req)
    {


        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $username = \Auth::user()->username;
        $choice = $req->input('choice');
        $nobukti = $req->input('nobukti');
        $nourut = $req->input('nourut');
        $tanggal = $req->input('tanggal');
        $kodebarang = $req->input('kodebrg');
        $kodegdg = $req->input('kodegdg');
        $qnt1 = $req->input('qnt1');
        $qnt2 = $req->input('qnt2');
        $qntx = $req->input('qntx');
        $nosat = $req->input('nosat');
        $urut = $req->input('urut');
        $jmlRecord = $req->input('jmlRecord');
        $xurut = 0;

        $purut = DB::connection('SML')->select('select * from dbPRPenyerahanBhnDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        if ($purut) {

            if ($choice == 'I') {

                $purut = DB::connection('SML')->select('select max(urut)+1 xurut from dbPRPenyerahanBhnDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
                $xurut = $purut[0]->xurut;
            } else {
                $xurut = $req->urut;
            }
        } else {
            $xurut = 1;
        }

        if ($choice == 'D') {
            $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($req->choice, 'PRP', $nobukti, '', $xurut, 'dbPRPenyerahanBhnDET');
        }

        if ($jmlRecord == 0 && $choice == 'I') {
            $check = DB::connection('SML')->select('select * from dbPRPenyerahanBhnDET where NOBUKTI = :nobukti', ["nobukti" => $nobukti]);
            if ($check) {
                return 2;
            }
        }



        $values = [
            $choice,
            $nobukti,
            $nourut,
            $tanggal,
            $kodegdg,
            $urut,
            $kodebarang,
            $qnt1,
            $nosat,
            '',
            0,
            '',
            $qnt2,
            '',
            0,
            0,
            0,
            '',
            '',
            $qntx
        ];

        DB::connection("SML")->statement('exec SP_PRPenyerahanBhnSampleWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);


        if ($choice != 'D') {
            $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($req->choice, 'PR', $nobukti, '', $xurut, 'dbPRPenyerahanBhnDET');
        }
        return 1;
    }
}
