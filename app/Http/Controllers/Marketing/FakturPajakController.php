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
use Carbon\Carbon;
use App\Item;
use ZipArchive;



class FakturPajakController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041041';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);



    $tempOutstanding = DB::connection("SML")->select($this->sqlDaftarInvoice("
  where month(A.tanggal) = :bulan and year(A.tanggal) = :tahun
  ") , [ "bulan" => $periode->bulan , "tahun" =>$periode->tahun ]);




    return view('marketing.fakturpajak' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $tempOutstanding = DB::connection("SML")->select("
    declare @tglawal datetime, @tglakhir datetime

  select @tglawal= :tglawal , @tglakhir= :tglakhir
  " . $this->sqlDaftarInvoice("
  where A.tanggal between @tglawal and @tglakhir
  ") , [ "tglawal" => $req->tglawal ,"tglakhir" => $req->tglakhir ]);



    return ["tempOutstanding" => $tempOutstanding];
  }

  // Daftar invoice untuk tabel fakturpajak (dipakai index & loadAll).
  // - Satu baris per invoice: dulu ada join ke dbInvoicePLDet per NoSPB lalu dbSPB/dbSPP,
  //   sehingga invoice gabungan beberapa Surat Jalan muncul berkali-kali dengan isi sama
  //   (kolomnya tidak ada yang dipakai). Join itu dibuang.
  // - No PO = No Pesanan SO (dbSO.NOpesanan lewat dbInvoicePL.NoSPP), sama seperti programajcSML.
  // - Hanya kolom yang dipakai tabel/export modal, supaya periode panjang tidak berat.
  private function sqlDaftarInvoice ($where) {
    return "
  select  A.NoBukti, A.Tanggal, F.NAMACUSTSUPP NamaCustSupp, E.NOpesanan PONo,
          A.NoPajak, A.TglFPJ, G.DPP, G.PPN, G.NNET
  from	dbInvoicePL A
  left Outer join dbSO E on E.Nobukti=A.NoSPP
  left outer join DBCUSTSUPP F on F.KODECUSTSUPP=A.KodeCustSupp
  Left Outer join (select nobukti,Sum(NDPP) DPP,Sum(Floor(NPPN)) PPN,Sum(NNET) NNET
                  from dbinvoicepldet
                  group by Nobukti) G on A.nobukti=g.Nobukti
  " . $where . "
  order by A.NoBukti
  ";
  }



  public function spAdd (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("Update dbinvoicepl set nopajak = :nopajak , tglfpj = :tglpajak  where nobukti= :nobukti", [ "nopajak" => $req->nopajak , "tglpajak" => $req->tglpajak , "nobukti" => $req->nobukti ]);
    return 1;
  }

  public function spDelete (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("Update DbInvoicePL set NoPajak='', TglFPJ= null where NoBukti= :nobukti ", [  "nobukti" => $req->nobukti ]);
    return 1;
  }


  // Import file Coretax. newSML tidak punya paket maatwebsite/excel (Excel::load() di
  // sini dulu selalu gagal "Class not found"), jadi file dibaca di browser dengan SheetJS
  // dan yang dikirim hanya kolom yang dipakai: referensi, nomor_faktur_pajak,
  // tanggal_faktur_pajak (nama kolom = judul kolom versi slug, sama seperti Excel::load()).
  // Isi proses sama dengan programajcSML: nobukti = 19 karakter pertama referensi
  // (referensi Coretax = NoBukti + ' ' + No Pesanan), lalu nomor pajak yang kehilangan
  // angka 0 di depan (dibaca Excel sebagai angka) dikembalikan.
  public function importExcel(Request $request)
  {
    $rows = json_decode($request->input('rows', '[]'), true);
    if (!is_array($rows) || !count($rows)) {
      return response()->json(['message' => 'File tidak berisi data faktur pajak'], 422);
    }

    $jumlah = 0;
    DB::connection('SML')->transaction(function () use ($rows, &$jumlah) {
      foreach ($rows as $value) {
        $referensi = $value['referensi'] ?? null;
        if ($referensi === null || $referensi === '') { continue; }

        $result = DB::connection('SML')->select("select left(:noreferensi,19) nobukti",["noreferensi" => $referensi]);
        $nobukti = $result[0]->nobukti ?? null;

        if ($nobukti != null) {
          $jumlah += DB::connection('SML')->update("update dbinvoicepl set nopajak =:nopajak,tglfpj = :tglfpj where nobukti = :nobukti",
            [
              "nopajak" => $value['nomor_faktur_pajak'] ?? null,
              "tglfpj" => ($value['tanggal_faktur_pajak'] ?? '') !== '' ? $value['tanggal_faktur_pajak'] : null,
              "nobukti" => $nobukti
            ]
          );
        }
      }

      DB::connection('SML')->update("Update  dbinvoicepl set NoPajak=:nodepan + NoPajak where NoPajak like '4%'  and year(tanggal)>=2026 ",
        ["nodepan" => '0']);
    });

    return ['jumlah' => $jumlah, 'baris' => count($rows)];
  }


  public function spHeaderExport (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->select("exec SPHeadCortax :nobukti", [  "nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetailExport (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->select("exec SPDetailCortax :nobukti", [  "nobukti" => $req->nobukti ]);
    return $res;
  }


  // Sama dengan programajcSML: invoice terpilih ditulis ke DBCUSTOMIZE (tipe '7777777')
  // milik user, lalu SPHeadCortaxweb/SPDetailCortaxweb membaca semuanya sekaligus.
  // (SPHeadCortax/SPDetailCortax di DBSMLBARU sekarang menerima @Iduser, bukan nobukti,
  // jadi pemanggilan lama per nobukti menghasilkan data kosong/salah.)
  public function spExport(Request $req)
  {
    $nobuktiList = $req->input('nobukti', []);
    if (!is_array($nobuktiList) || !count($nobuktiList)) {
      return response()->json(['message' => 'Pilih minimal satu data'], 422);
    }
    $iduser = \Auth::user()->username;

    DB::connection('SML')->update("
      delete DBCUSTOMIZE where iduser=:iduser and tipe in ('tax','7777777')", [ "iduser" => $iduser ]);

    foreach ($nobuktiList as $nobukti) {
      DB::connection('SML')->update(
        "INSERT INTO DBCUSTOMIZE (id,iduser,tipe) values (:id,:iduser,:tipe)",
        ["id" => $nobukti,"iduser" => $iduser,"tipe" => '7777777']
      );
    }

    $header = DB::connection('SML')->select(
      "exec SPHeadCortaxweb :nobukti,:iduser",
      ["nobukti" => $nobukti,"iduser" => $iduser]
    );
    $detail = DB::connection('SML')->select(
      "exec SPDetailCortaxweb :nobukti,:iduser",
      ["nobukti" => $nobukti,"iduser" => $iduser]
    );

    return response()->json([
      'header' => $header,
      'detail' => $detail
    ]);
  }



}
