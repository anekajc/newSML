<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class MasterCustomerController extends Controller
{

  public function index(Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');

    $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis,namakota from vwcustsupp where Jenis = 1');
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastercustomer' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }


  public function spListSelect () {
    $listDataGroup = DB::connection('SML')->select('SELECT * FROM DBGROUPCUSTSUPP');
    $listDataJenis = DB::connection('SML')->select('SELECT * FROM DBJENISCUSTSUPP');
    $listDataKota = DB::connection('SML')->select('SELECT * FROM DBKOTA');
    return ["listDataGroup" => $listDataGroup , "listDataJenis" => $listDataJenis , "listDataKota" => $listDataKota];

  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where KODECUSTSUPP = :kode' , ['kode' => $req->kode]);
    // NPWP kosong tidak dicek: form kosong sampai ke sini sebagai null, dan koneksi SQL Server
    // produksi (ANSI_NULLS OFF) mencocokkan "NPWP = NULL" dengan baris yang NPWP-nya null -
    // akibatnya customer tanpa NPWP selalu ditolak "NPWP sudah ada".
    $checkNPWP = trim((string) $req->npwp) === '' ? [] :
      DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where NPWP = :npwp' , ['npwp' => $req->npwp]);

    if ($check) {
      return 'Kode supplier/customer sudah ada di database';
    }

     if ($checkNPWP) {
      return 'NPWP sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into DBCUSTSUPP (USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis, HARI, PLAFON , JenisCustSupp, Agent, IntCode, CompCode, CustCode , BERIKAT, pBlackList ) VALUES (:usaha, :kode , :nama , :alamat , :kota , :kodepos ,  :negara, :telp,:fax , :email , :npph23 , :npph21 , :haripiutang, :isaktif , :att, :attphone ,:attdepart,  :npwp ,:namapkp ,:alamatpkp ,:kotapkp, :isppn , :jenis , :top , :plafon , :jeniscustomer, :groupcustomer, :intcode, :compcode, :custcode , :berikat, :blacklist)' , 
   
    ['usaha'=> $req->bentukusaha , 
    'kode' => $req->kode , 
    'nama' => $req->nama , 
    'alamat' => $req->alamat , 
    'kota'=> $req->kota , 
    'kodepos' => $req->kodepos ,  
    'negara' => $req->negara, 
    'telp' => $req->telp ,
    'fax' => $req->fax , 
    'email' => $req->email , 
    'npph23' => $req->pph23 , 
    'npph21' => $req->pph21 , 
    'haripiutang' => $req->haripiutang , 
    'isaktif'=> $req->isaktif , 
    'att'=>$req->att, 
    'attphone'=> $req->attphone , 
    'attdepart'=>$req->attdepart, 
    'npwp' => $req->npwp , 
    'namapkp' => $req->namapkp ,
    'alamatpkp' => $req->alamatpkp , 
    'kotapkp' => $req->kotapkp , 
    'isppn'=> $req->isppn , 
    'jenis' => $req->jenis , 
    'top' => $req->top , 
    'plafon' => $req->plafon , 
    'jeniscustomer' => $req->jeniscustomer, 
    'groupcustomer' => $req->groupcustomer, 
    'intcode' => $req->intcomp , 
    'compcode' => $req->compcode, 
    'custcode' => $req->custcode , 
    'berikat' => $req->berikat, 
    'blacklist' => $req->blacklist ]);

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

  public function spDeleteDetailAkun (Request $req) {

    $delete = DB::connection('SML')->update('delete from DBPERKCUSTSUPP where Perkiraan = :kode and KodeCustSupp = :kodecust' , ['kode' => $req->kode, 'kodecust' => $req->kodecust]);
    return $delete;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis,HARI,PLAFON , JenisCustSupp, Agent, IntCode, CompCode, CustCode , BERIKAT, pBlackList from DBCUSTSUPP where KODECUSTSUPP = :kode', ['kode' => $req->kode]);
    // $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis from DBCUSTSUPP where Jenis = 0');
    return $detail;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBCUSTSUPP set HARI = :top ,PLAFON = :plafon , JenisCustSupp = :jeniscustomer , Agent = :groupcustomer , IntCode = :intcode , CompCode = :compcode , CustCode = :custcode , BERIKAT = :berikat , pBlackList = :blacklist ,  USAHA =:usaha , NAMACUSTSUPP = :nama ,  ALAMAT1 = :alamat, Kota = :kota, KODEPOS = :kodepos , NEGARA= :negara, TELPON = :telp , FAX  = :fax, EMAIL = :email, NPPH23 = :npph23, NPPH22 = :npph21, HARIHUTPIUT = :haripiutang , IsAktif = :isaktif, Att = :att , AttPhone = :attphone, AttDepart = :attdepart ,  NPWP = :npwp , NAMAPKP = :namapkp , ALAMATPKP1 = :alamatpkp , KOTAPKP = :kotapkp , IsPpn = :isppn  where KODECUSTSUPP = :kode' , ['top' => $req->top , 'plafon' => $req->plafon , 'jeniscustomer' => $req->jeniscustomer, 'groupcustomer' => $req->groupcustomer, 'intcode' => $req->intcomp , 'compcode' => $req->compcode, 'custcode' => $req->custcode , 'berikat' => $req->berikat, 'blacklist' => $req->blacklist ,'usaha'=> $req->bentukusaha , 'nama' => $req->nama , 'alamat' => $req->alamat , 'kota'=> $req->kota , 'kodepos' => $req->kodepos ,  'negara' => $req->negara, 'telp' => $req->telp ,'fax' => $req->fax , 'email' => $req->email , 'npph23' => $req->pph23 , 'npph21' => $req->pph21 , 'haripiutang' => $req->haripiutang , 'isaktif'=> $req->isaktif , 'att'=>$req->att, 'attphone'=> $req->attphone , 'attdepart'=>$req->attdepart,  'npwp' => $req->npwp , 'namapkp' => $req->namapkp ,'alamatpkp' => $req->alamatpkp , 'kotapkp' => $req->kotapkp , 'isppn'=> $req->isppn , 'kode' => $req->kode]);

    return $edit;
  }

public function loadAll(Request $request)
{
    $query = DB::connection('SML')
        ->table('vwcustsupp')
        ->where('Jenis', 1);

    // total data before filtering (cheap: single view, no joins)
    $total = (clone $query)->count();

    // ?? apply search if provided
    $search = $request->input('search.value');
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('KODECUSTSUPP', 'like', "%{$search}%")
              ->orWhere('USAHA', 'like', "%{$search}%")
              ->orWhere('NAMACUSTSUPP', 'like', "%{$search}%")
              ->orWhere('ALAMAT1', 'like', "%{$search}%")
              ->orWhere('Kota', 'like', "%{$search}%")
              ->orWhere('KODEPOS', 'like', "%{$search}%")
              ->orWhere('NEGARA', 'like', "%{$search}%")
              ->orWhere('TELPON', 'like', "%{$search}%")
              ->orWhere('FAX', 'like', "%{$search}%")
              ->orWhere('EMAIL', 'like', "%{$search}%");
        });
    }

    // apply pagination from datatables request
    $start = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 10);

    // DBSMLNEW's compatibility level doesn't support SQL Server's OFFSET/FETCH
    // syntax (Laravel's default ->offset()->limit() for sqlsrv), so page via
    // ROW_NUMBER() in a subquery instead. COUNT(*) OVER() rides along in the
    // same pass so the filtered total doesn't need its own separate query.
    $dataQuery = (clone $query)->select(
        'USAHA',
        'KODECUSTSUPP',
        'NAMACUSTSUPP',
        'ALAMAT1',
        'Kota',
        'KODEPOS',
        'NEGARA',
        'TELPON',
        'FAX',
        'EMAIL',
        'NPPH23',
        'NPPH22',
        'HARIHUTPIUT',
        'IsAktif',
        'Att',
        'AttPhone',
        'AttDepart',
        'bank',
        'NoAcc',
        'ATN',
        'NPWP',
        'NAMAPKP',
        'ALAMATPKP1',
        'KOTAPKP',
        'IsPpn',
        'Jenis',
        'namakota',
        DB::raw('ROW_NUMBER() OVER (ORDER BY KODECUSTSUPP) as RowNum'),
        DB::raw('COUNT(*) OVER () as TotalCount')
    );

    $paged = DB::connection('SML')->query()->fromSub($dataQuery, 'sub');

    // DataTables sends length = -1 for the "Semua" (All) option, meaning no limit
    if ($length >= 0) {
        $paged->whereBetween('RowNum', [$start + 1, $start + $length]);
    }

    $data = $paged->get();

    $filtered = $data->isNotEmpty() ? (int) $data->first()->TotalCount : (clone $query)->count();

    return response()->json([
        "draw" => intval($request->input('draw')),
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $data
    ]);
}

  public function loadDetailAkun(Request $req) {
    $listData = DB::connection('SML')->select('SELECT KodeCustSupp, Perkiraan FROM DBPERKCUSTSUPP WHERE KodeCustSupp = :kodeDetail', ['kodeDetail' => $req->input('kodeDetail')]);
    return $listData;
}

public function loadDetailPerkiraan(Request $req) {
  $listData = DB::connection('SML')->select("SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                     left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                     where a.Kode='HT' OR a.Kode='PT' order by a.Perkiraan");
  return $listData;
}

public function loadPerkiraanEdit(Request $req) {
  $listData = DB::connection('SML')->select("SELECT * FROM DBPERKCUSTSUPP WHERE KodeCustSupp = :kodecust and Perkiraan = :perkiraan", ['kodecust'=> $req->kodecust,'perkiraan'=> $req->perkiraan]);
  return $listData;
}


public function spAddDetailAkun(Request $req)
{
    // $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode', ['kode' => $req->kode]);
    //
    // if ($check) {
    //     return 'Kode jenis sudah ada di database';
    // }

    $listData = DB::connection('SML')->update('INSERT INTO DBPERKCUSTSUPP (KodeCustSupp, Urut, Perkiraan) VALUES (:kode,  1, :nama)', ['kode' => $req->kode, 'nama' => $req->nama]);

    return 1;
}

public function spAddDetailAkunEdit (Request $req)
{
    // $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode', ['kode' => $req->kode]);
    //
    // if ($check) {
    //     return 'Kode jenis sudah ada di database';
    // }

    $listData = DB::connection('SML')->update('UPDATE DBPERKCUSTSUPP set Perkiraan = :perkiraan WHERE KodeCustSupp = :kodecust and Perkiraan = :perkiraanOld', ['perkiraan' => $req->perkiraan, 'kodecust' => $req->kodecust, 'perkiraanOld' => $req->perkiraanOld]);

    return $listData;
}


}
