<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class MasterSupplierController extends Controller
{

  public function index(Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');

    $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, PPN, Jenis,namakota from vwbrowssupp where Jenis = 0');
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersupplier' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function spListKota () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');
    return $listData;

  }

public function loadAll()
{
    // Only ~2,100 active suppliers total, so this loads the full set once and
    // lets DataTables page/sort/search it client-side instead of round-
    // tripping to the server on every click. The query only pulls the
    // columns the table actually displays or searches on: dbperkcustsupp/
    // dbPerkiraan/dbarea/DbJenisCustSupp/the Agent self-join and the
    // DataPostHutPiut scalar UDF were only feeding columns nothing renders,
    // and DISTINCT was only needed to collapse the duplicate rows the
    // dbperkcustsupp join produced.
    $query = DB::connection('SML')
        ->table('vwcustsupp')
        ->where('Jenis', 1);

    $data = DB::connection('SML')
        ->table('dbCustSupp as a')
        ->leftJoin('dbkota as D', 'a.Kota', '=', 'D.KodeKota')
        ->where('a.IsAktif', 1)
        ->where ('a.Jenis', 0)
        ->select(
            'a.KODECUSTSUPP',
            'a.USAHA',
            'a.NAMACUSTSUPP',
            'a.ALAMAT1',
            'a.KODEPOS',
            'a.NEGARA',
            'a.TELPON',
            'a.FAX',
            'a.EMAIL',
            'D.namaKota'
        )
        ->orderBy('a.KODECUSTSUPP')
        ->get();

    // Some column here can come back as raw bytes that aren't valid UTF-8,
    // which makes json_encode() fail outright. Substitute those bytes
    // instead of 500'ing the whole response over one bad field.
    return response()->json($data, 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
}

public function spAdd(Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where KODECUSTSUPP = :kode', ['kode' => $req->kode]);
    
    // NPWP kosong tidak dicek: form kosong sampai ke sini sebagai null, dan koneksi SQL Server
    // produksi (ANSI_NULLS OFF) mencocokkan "NPWP = NULL" dengan baris yang NPWP-nya null -
    // akibatnya supplier tanpa NPWP selalu ditolak "NPWP sudah ada".
    $checkNIK = trim((string) $req->npwp) === '' ? [] :
      DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where NPWP = :npwp', ['npwp' => $req->npwp]);

    if ($check) {
        return 'Kode supplier sudah ada di database';
    }

     if ($checkNIK) {
        return 'NPWP sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into DBCUSTSUPP (HARI, USAHA, KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1, Kota, KODEPOS, NEGARA, TELPON, FAX, EMAIL, NPPH23, NPPH22, HARIHUTPIUT, IsAktif, Att, AttPhone, AttDepart, bank, NoAcc, ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis) VALUES (:haribiasa, :usaha, :kode, :nama, :alamat, :kota, :kodepos, :negara, :telp, :fax, :email, :npph23, :npph21, :haripiutang, :isaktif, :att, :attphone, :attdepart, :bank, :accno, :atn, :npwp, :namapkp, :alamatpkp, :kotapkp, :isppn, :jenis)', [
        'haribiasa' => $req->haribiasa,  
        'usaha' => $req->bentukusaha,
        'kode' => $req->kode,
        'nama' => $req->nama,
        'alamat' => $req->alamat,
        'kota' => $req->kota,
        'kodepos' => $req->kodepos,
        'negara' => $req->negara,
        'telp' => $req->telp,
        'fax' => $req->fax,
        'email' => $req->email,
        'npph23' => $req->pph23,
        'npph21' => $req->pph21,
        'haripiutang' => $req->haripiutang,
        'isaktif' => $req->isaktif,
        'att' => $req->att,
        'attphone' => $req->attphone,
        'attdepart' => $req->attdepart,
        'bank' => $req->bank,
        'accno' => $req->accno,
        'atn' => $req->atn,
        'npwp' => $req->npwp,
        'namapkp' => $req->namapkp,
        'alamatpkp' => $req->alamatpkp,
        'kotapkp' => $req->kotapkp,
        'isppn' => $req->isppn,
        'jenis' => $req->jenis
    ]);

    return 1;
}

  public function spDelete (Request $req) {

    // Customer/supplier yang sudah dipakai transaksi tidak boleh dihapus (dulu tidak dicek sama sekali).
    $pemakai = [
      ['DBHUTPIUT',   'KodeCustSupp', 'Hutang/Piutang'],
      ['DBPO',        'KODESUPP',     'Purchase Order'],
      ['DBBELI',      'KODESUPP',     'Pembelian'],
      ['DBPembelian', 'KodeCustSupp', 'Pembelian'],
      ['DBRBELI',     'KODESUPP',     'Retur Pembelian'],
      ['DBInvoice',   'KodeSupp',     'Invoice'],
      ['DBSO',        'KODECUST',     'Sales Order'],
      ['DBJUAL',      'KODECUST',     'Penjualan'],
      ['DBPenjualan', 'KodeCustSupp', 'Penjualan'],
      ['dbSPB',       'KodeCustSupp', 'SPB'],
    ];
    foreach ($pemakai as $p) {
      $check = DB::connection('SML')->select("select top 1 1 as ada from {$p[0]} where {$p[1]} = :kode", ['kode' => $req->kode]);
      if ($check) {
        return 'Data digunakan di ' . $p[2];
      }
    }
    $delete = DB::connection('SML')->update('delete from DBCUSTSUPP where KODECUSTSUPP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBCUSTSUPP set USAHA =:usaha , NAMACUSTSUPP = :nama , HARI = :haribiasa, ALAMAT1 = :alamat, Kota = :kota, KODEPOS = :kodepos , NEGARA= :negara, TELPON = :telp , FAX  = :fax, EMAIL = :email, NPPH23 = :npph23, NPPH22 = :npph21, HARIHUTPIUT = :haripiutang , IsAktif = :isaktif, Att = :att , AttPhone = :attphone, AttDepart = :attdepart , bank = :bank , NoAcc = :accno, ATN = :atn, NPWP = :npwp , NAMAPKP = :namapkp , ALAMATPKP1 = :alamatpkp , KOTAPKP = :kotapkp , IsPpn = :isppn  where KODECUSTSUPP = :kode' , ['usaha'=> $req->bentukusaha , 'nama' => $req->nama , 'alamat' => $req->alamat , 'haribiasa' => $req->haribiasa , 'kota'=> $req->kota , 'kodepos' => $req->kodepos ,  'negara' => $req->negara, 'telp' => $req->telp ,'fax' => $req->fax , 'email' => $req->email , 'npph23' => $req->pph23 , 'npph21' => $req->pph21 , 'haripiutang' => $req->haripiutang , 'isaktif'=> $req->isaktif , 'att'=>$req->att, 'attphone'=> $req->attphone , 'attdepart'=>$req->attdepart, 'bank'=>$req->bank,'accno'=> $req->accno , 'atn' => $req->atn , 'npwp' => $req->npwp , 'namapkp' => $req->namapkp ,'alamatpkp' => $req->alamatpkp , 'kotapkp' => $req->kotapkp , 'isppn'=> $req->isppn , 'kode' => $req->kode]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis from DBCUSTSUPP where KODECUSTSUPP = :kode', ['kode' => $req->kode]);
    // $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, PPN, Jenis from DBCUSTSUPP where Jenis = 0');
    return $detail;
  }

  public function loadAlamat(Request $req) {
  $listData = DB::connection('SML')->select(" SELECT A.KodeCustSupp, A.Nomor, A.Nama, A.Alamat, A.Telp, A.Fax,
                                              cast(A.Alamat as text) AlamatTxt ,A.UP
                                              from dbAlamatCust A
                                              where A.kodecustSupp= :kodeCustSupp and nomor<>0  and isnull(pSurat,0)=0 
                                              Order by A.Nomor",['kodeCustSupp'=>$req->kodeDetail]);
                                              return $listData;
                                        }

public function submitAlamat(Request $req)
{
    DB::connection('SML')->insert("
        INSERT INTO dbAlamatCust (KODECUSTSUPP, Nomor, Alamat, Telp, Fax, Nama, pSurat, up, Area)
        SELECT :kodeCustSupp, ISNULL(MAX(Nomor), 0) + 1, :alamat, :telp, :fax, :nama, 0, :up, 'blackpink'
        FROM dbAlamatCust WITH (UPDLOCK, HOLDLOCK)
        WHERE KODECUSTSUPP = :kodeCustSupp2
    ", [
        'kodeCustSupp' => $req->kodeCustSupp,
        'alamat' => $req->alamat,
        'telp' => $req->telp,
        'fax' => $req->fax,
        'nama' => $req->nama,
        'up' => $req->up,
        'kodeCustSupp2' => $req->kodeCustSupp,
    ]);

    return 1;
}

  public function spDeleteAlamat (Request $req) {

    $delete = DB::connection('SML')->update('DELETE dbAlamatCust where KodeCustsupp= :kode and Nomor= :nomor' , ['kode' => $req->kodeCustSupp,'nomor' => $req->nomor]);
    return $delete;
  }

  public function spEditAlamat (Request $req) 
  {
    $edit = DB::connection('SML')->update('UPDATE dbAlamatCust set Alamat = :alamat, Telp = :telp, Fax = :fax, Nama = :nama, up =:up where KodeCustSupp = :kodeCustSupp and Nomor = :nomor' , 
    ['alamat'=> $req->alamat, 
    'nama' => $req->nama, 
    'nomor' => $req->nomor, 
    'kodeCustSupp' => $req->kodeCustSupp, 
    'telp'=> $req->telp,
    'fax' => $req->fax,
    'up' => $req->up]);

    return $edit;
  }

}
