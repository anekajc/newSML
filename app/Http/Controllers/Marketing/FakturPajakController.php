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
use Maatwebsite\Excel\Facades\Excel;
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



    $tempOutstanding = DB::connection("SML")->select("




  select  A.NoBukti, A.NoUrut, A.Tanggal,
          C.NoSPP, D.Tanggal TglSPP,D.NoSO,E.Tanggal TGLSO, A.KodeCustSupp, F.NAMACUSTSUPP NamaCustSupp, A.Consignee, A.NotifyParty,
          A.ContractNo, A.PONo, A.PaymentTerm, A.DocCreditNo, A.PoL, A.PoD,
          A.NameOfVessel, A.ShipOnBoardDate, A.Packing, A.Others, A.ISLOKAL,
          B.NoSPB, C.Tanggal TglSPB,
          A.IsCetak, A.IDUser,
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
          A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
          A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
          A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
          A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
          A.NoPajak,TglFPJ,E.NOpesanan,G.DPP,G.PPN,G.NNET
  from	dbInvoicePL A
  Left Outer join (Select x.NoBukti, x.NoSPB
                   from dbInvoicePLDet x
                   Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
  Left Outer join (Select x.NoBukti, x.Tanggal, y.NoSPP
                   from dbSPB x
                        left Outer join dbSPBDet y on y.NoBukti=x.NoBukti
                   group by x.NoBukti, x.Tanggal, y.NoSPP) C on C.nobukti=B.NoSPB
  left Outer join (Select x.NoBukti, x.Tanggal, y.NoSO
                   from dbSPP x
                        left Outer join dbSPPDet y on y.NoBukti=x.NoBukti
                   group by x.NoBukti, x.Tanggal, y.NoSO) D on D.NoBukti=C.NoSPP
  left Outer join dbSO E on E.Nobukti=D.NoSO
  --left outer join vwBrowsCustomer F on F.KodeCust=A.KodeCustSupp and F.Sales=E.KODESLS
  left outer join DBCUSTSUPP F on F.KODECUSTSUPP=A.KodeCustSupp
  Left Outer join (select nobukti,Sum(NDPP) DPP,Sum(Floor(NPPN)) PPN,Sum(NNET) NNET
                  from dbinvoicepldet
                  group by Nobukti) G on A.nobukti=g.Nobukti
  where month(A.tanggal) = :bulan and year(A.tanggal) = :tahun
  order by A.NoBukti

  " , [ "bulan" => $periode->bulan , "tahun" =>$periode->tahun ]);




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

  select  A.NoBukti, A.NoUrut, A.Tanggal,
          C.NoSPP, D.Tanggal TglSPP,D.NoSO,E.Tanggal TGLSO, A.KodeCustSupp, F.NAMACUSTSUPP NamaCustSupp, A.Consignee, A.NotifyParty,
          A.ContractNo, A.PONo, A.PaymentTerm, A.DocCreditNo, A.PoL, A.PoD,
          A.NameOfVessel, A.ShipOnBoardDate, A.Packing, A.Others, A.ISLOKAL,
          B.NoSPB, C.Tanggal TglSPB,
          A.IsCetak, A.IDUser,
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
          A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
          A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
          A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
          A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
          A.NoPajak,TglFPJ,E.NOpesanan,G.DPP,G.PPN,G.NNET
  from	dbInvoicePL A
  Left Outer join (Select x.NoBukti, x.NoSPB
                   from dbInvoicePLDet x
                   Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
  Left Outer join (Select x.NoBukti, x.Tanggal, y.NoSPP
                   from dbSPB x
                        left Outer join dbSPBDet y on y.NoBukti=x.NoBukti
                   group by x.NoBukti, x.Tanggal, y.NoSPP) C on C.nobukti=B.NoSPB
  left Outer join (Select x.NoBukti, x.Tanggal, y.NoSO
                   from dbSPP x
                        left Outer join dbSPPDet y on y.NoBukti=x.NoBukti
                   group by x.NoBukti, x.Tanggal, y.NoSO) D on D.NoBukti=C.NoSPP
  left Outer join dbSO E on E.Nobukti=D.NoSO
  --left outer join vwBrowsCustomer F on F.KodeCust=A.KodeCustSupp and F.Sales=E.KODESLS
  left outer join DBCUSTSUPP F on F.KODECUSTSUPP=A.KodeCustSupp
  Left Outer join (select nobukti,Sum(NDPP) DPP,Sum(Floor(NPPN)) PPN,Sum(NNET) NNET
                  from dbinvoicepldet
                  group by Nobukti) G on A.nobukti=g.Nobukti
  where A.tanggal between @tglawal and @tglakhir
  order by A.NoBukti

  " , [ "tglawal" => $req->tglawal ,"tglakhir" => $req->tglakhir ]);



    return ["tempOutstanding" => $tempOutstanding];
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


public function importExcel(Request $request)
    {

    // return 1;

    //  return('import Excel  ================');
        $request->validate([
            'import_file' => 'required'
        ]);
        // return('import Excel');
        $path = $request->file('import_file')->getRealPath();
        $data = Excel::load($path)->get();
        //  dd($data);
        // return $data;
 
        if($data->count()){
            foreach ($data as $key => $value) {
                $arr[] = ['title' => $value->title, 'description' => $value->description];
                $npwp = $value['npwp_pembeli_identitas_lainnya'];
                $nobukti = $value['referensi'];
                $nopajak = $value['nomor_faktur_pajak'];
                $tglfpj = $value['tanggal_faktur_pajak'];
                  // dd($value, $npwp,'nopajak : ', $nopajak,'nobukti : ', @$nobukti,'tglfpj : ',  $tglfpj);


                if ($nobukti != null) {
                          DB::connection('SML')->update("update dbinvoicepl set nopajak = :nopajak,tglfpj = :tglfpj where nobukti = :nobukti",
                              [
                                  "nopajak" => $nopajak,
                                  "tglfpj" => $tglfpj,
                                  "nobukti" => $nobukti
                              ]
                          );
                }

            }
 
            // if(!empty($arr)){
            //     Item::insert($arr);
            // }
        }

        // return redirect()->route('faktur.index')->with('success', 'Data Faktur Pajak berhasil disimpan!');
       
        return back()->with('success', 'Insert Record successfully.ccccccccccccccccccccccc');
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


public function spExport(Request $req)
{
    $nobuktiList = $req->nobukti;

    $allHeaders = [];
    $allDetails = [];

    foreach ($nobuktiList as $nobukti) {
        $header = DB::connection('SML')->select(
            "exec SPHeadCortax :nobukti",
            ["nobukti" => $nobukti]
        );

        $detail = DB::connection('SML')->select(
            "exec SPDetailCortax :nobukti",
            ["nobukti" => $nobukti]
        );

        $allHeaders = array_merge($allHeaders, $header);
        $allDetails = array_merge($allDetails, $detail);
    }

    return response()->json([
        'header' => $allHeaders,
        'detail' => $allDetails
    ]);
}



}
