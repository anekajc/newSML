<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



// Ubah PO Customer -- port dari programajcSML. Di programajcSML menu ini hanya untuk
// melihat: SO draft PO (pPO=1) yang belum direvisi, SO yang sudah direvisi, dan history
// revisi (DBHISPOCUST, diisi oleh aplikasi desktop). Tidak ada proses simpan di web.
// Tambahan newSML: filter Periode (default = bulan periode aktif, sama dengan perilaku lama).
class UbahPOCustomerController extends Controller
{

  public function index(Request $req) {
    // Akses dicek lewat href (DBMENUWEB/DBFLMENUWEB), kode menu di DBSMLBARU = 04016.
    $kodemenu = '04016';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    [$tglawal, $tglakhir] = $this->rangePeriode($req, $periode);

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $data = $this->ambilData($tglawal, $tglakhir);

    return view('marketing.ubahpocustomer' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "tempOutstanding" => $data['tempOutstanding'],
      "tempOutstanding2" => $data['tempOutstanding2'],
      "tempOutstanding3" => $data['tempOutstanding3'],
      "akses" => $akses,
    ]);

  }

  // Filter Periode di toolbar mengirim tglawal/tglakhir; kalau tidak dikirim (initial load)
  // jatuh ke bulan/tahun periode aktif -- sama dengan programajcSML (month/year(A.tanggal)).
  private function rangePeriode (Request $req, $periode) {
    if ($req->tglawal && $req->tglakhir) {
      return [$req->tglawal, $req->tglakhir];
    }
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->copy()->startOfMonth()->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  private function ambilData ($tglawal, $tglakhir) {

    // Tab 1: SO - Draft PO (pPO=1) yang belum ada di history revisi.
    $tempOutstanding = DB::connection("SML")->select("
Select 	A.NoBukti, A.NoSPB, B.UrutSPB, A.tanggal,
	A.NoBukti+right('0000000000'+cast(B.Urut as varchar(10)),10) NoBuktiUrut,
        B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.NoSat, B.Isi, H.Sat1 Satuan,
        B.Qnt2, H.Sat2 SatuanRoll, B.Harga,
        B.DiscP1, B.DiscRp1, B.DiscTot,
        B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
        B.NPPNRp NPPN, B.BYAngkut Beban, B.SubTotalRp + B.BYAngkut Total,
        B.Qnt Qnt1x,B.Qnt/case when isnull(H.Isi2,0)=0 then 1 else isnull(H.Isi2,0) End Qnt2x,B.Qnt/case when isnull(H.Isi3,0)=0 then 1 else isnull(H.Isi3,0) End Qnt3x,H.Sat1 SAT1X,H.SAT2 SAT2X,H.SAT3 SAT3X ,A.NoPesanan ,A.ISOtorisasi2
          ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO,C.PartNumber ,D.NAMAMERK,B.Noserah,A.KodeCUst,E.namaCustSUpp ,
          Cast(Case when Case when A.IsOtorisasi1=1 then 0 else 1 end+
                       Case when A.IsOtorisasi2=1 then 0 else 1 end+
                       Case when A.IsOtorisasi3=1 then 0 else 1 end+
                       Case when A.IsOtorisasi4=1 then 0 else 1 end+
                       Case when A.IsOtorisasi5=1 then 0 else 1 end=A.MaxOL then 0
                  else 1
             end As Bit) isoto ,A.IDPOCust

From dbSO A
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
left outer join dbbarang C on B.kodebrg=C.Kodebrg
Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
LEFT OUTER JOIN DBMERK D ON C.KODEMERK=D.KODEMERK
Left Outer Join DbCustSUpp E on A.KOdeCust=E.KodeCustSupp
where  A.tanggal >= :tglawal and A.tanggal < dateadd(day, 1, cast(:tglakhir as date))   and A.nobukti not in (select noso from  DBHISPOCUST)
and ISnull(pPO,0)=1

order by A.NoBukti, B.Urut
" , ["tglawal" => $tglawal , "tglakhir" => $tglakhir] );

    // Tab 2: Revisi PO Cust -- SO yang sudah ada di history revisi.
    // Pembagi Isi2/Isi3 diberi pengaman 0 seperti query tab 1 (programajcSML tanpa pengaman).
    $tempOutstanding2 = DB::connection("SML")->select("
Select 	A.NoBukti, A.NoSPB, B.UrutSPB, A.tanggal,
	A.NoBukti+right('0000000000'+cast(B.Urut as varchar(10)),10) NoBuktiUrut,
        B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.NoSat, B.Isi, H.Sat1 Satuan,
        B.Qnt2, H.Sat2 SatuanRoll, B.Harga,
        B.DiscP1, B.DiscRp1, B.DiscTot,
        B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
        B.NPPNRp NPPN, B.BYAngkut Beban, B.SubTotalRp + B.BYAngkut Total,
        B.Qnt Qnt1x,B.Qnt/case when isnull(H.Isi2,0)=0 then 1 else isnull(H.Isi2,0) End Qnt2x,B.Qnt/case when isnull(H.Isi3,0)=0 then 1 else isnull(H.Isi3,0) End Qnt3x,H.Sat1 SAT1X,H.SAT2 SAT2X,H.SAT3 SAT3X ,A.NoPesanan
          ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO,C.PartNumber ,D.NAMAMERK,B.Noserah,A.KodeCUst,E.namaCustSUpp
From dbSO A
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
left outer join dbbarang C on B.kodebrg=C.Kodebrg
Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
LEFT OUTER JOIN DBMERK D ON C.KODEMERK=D.KODEMERK
Left Outer Join DbCustSUpp E on A.KOdeCust=E.KodeCustSupp
where  A.tanggal >= :tglawal and A.tanggal < dateadd(day, 1, cast(:tglakhir as date))     and A.nobukti  in (select noso from  DBHISPOCUST)
order by A.NoBukti, B.Urut" , ["tglawal" => $tglawal , "tglakhir" => $tglakhir] );

    // Tab 3: History -- seluruh DBHISPOCUST, tanpa filter periode (sama seperti programajcSML).
    $tempOutstanding3 = DB::connection("SML")->select(" Select * from DBHISPOCUST order by Tanggal desc");

    return ["tempOutstanding" => $tempOutstanding,
            "tempOutstanding2" => $tempOutstanding2,
            "tempOutstanding3" => $tempOutstanding3
            ];
  }

  public function loadAll (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    [$tglawal, $tglakhir] = $this->rangePeriode($req, $periode);

    return $this->ambilData($tglawal, $tglakhir);
  }

  // Daftar PO Customer pengganti untuk modal Ubah PO: milik customer SO yang sama, belum
  // diganti (pGanti), belum di-close, dan belum dipakai SO lain; PO yang sedang terpasang
  // di SO ini tidak ikut ditampilkan.
  public function listPO (Request $req) {
    $so = DB::connection('SML')->select("select NOBUKTI, KODECUST, isnull(IDPOCust,0) IDPOCust from DBSO where NOBUKTI = :nobukti", ["nobukti" => $req->nobukti]);
    if (!$so) {
      return [];
    }

    return DB::connection('SML')->select("
      select A.ID, A.TglInput, A.TglTerima, A.POCustomer, A.KodeCustSupp, A.RpPO
      from DBPOCUSTSUPP A
      where A.KodeCustSupp = :kodecust
        and isnull(A.pGanti,0) = 0 and isnull(A.IsCLose,0) = 0
        and A.ID <> :idpocust
        and not exists (select 1 from DBSO S where S.KODECUST = A.KodeCustSupp and S.NoPesanan = A.POCustomer and S.NOBUKTI <> :nobukti)
      order by A.TglInput desc, A.ID desc
    ", ["kodecust" => $so[0]->KODECUST, "idpocust" => $so[0]->IDPOCust, "nobukti" => $so[0]->NOBUKTI]);
  }

  // Ubah No PO (NoPesanan) SO draft PO -- mengikuti cara aplikasi desktop:
  // 1. DBSO.NoPesanan = No PO baru, DBSO.IDPOCust = ID baris DBPOCUSTSUPP-nya;
  // 2. baris DBPOCUSTSUPP lama (IDPOCust sebelumnya) ditandai pGanti = 1;
  // 3. riwayat dicatat ke DBHISPOCUST, satu baris per barang SO (harga tidak diubah).
  public function simpan (Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses('04016', 'ubahpocust');
    if (!$akses || !$akses->HASACCESS || !$akses->ISKOREKSI) {
      return 'No access';
    }

    $so = DB::connection('SML')->select("
      select NOBUKTI, KODECUST, isnull(NoPesanan,'') NoPesanan, isnull(IDPOCust,0) IDPOCust, isnull(pPO,0) pPO
      from DBSO where NOBUKTI = :nobukti", ["nobukti" => $req->nobukti]);
    if (!$so) {
      return 'SO ' . $req->nobukti . ' tidak ditemukan';
    }
    $so = $so[0];
    if (!$so->pPO) {
      return 'SO ' . $so->NOBUKTI . ' bukan SO draft PO';
    }
    $sudah = DB::connection('SML')->select("select top 1 NOSO from DBHISPOCUST where NOSO = :nobukti", ["nobukti" => $so->NOBUKTI]);
    if ($sudah) {
      return 'No PO SO ' . $so->NOBUKTI . ' sudah pernah diubah';
    }

    $po = DB::connection('SML')->select("
      select ID, POCustomer, KodeCustSupp, isnull(pGanti,0) pGanti, isnull(IsCLose,0) IsCLose
      from DBPOCUSTSUPP where ID = :id", ["id" => $req->idpocust]);
    if (!$po) {
      return 'PO Customer belum dipilih';
    }
    $po = $po[0];
    if (trim($po->KodeCustSupp) !== trim($so->KODECUST)) {
      return 'PO Customer ' . $po->POCustomer . ' bukan milik customer SO ini';
    }
    if ($po->pGanti || $po->IsCLose) {
      return 'PO Customer ' . $po->POCustomer . ' sudah diganti / di-close';
    }
    if ((int) $po->ID === (int) $so->IDPOCust) {
      return 'No PO yang dipilih sama dengan No PO SO saat ini';
    }
    $dipakai = DB::connection('SML')->select("
      select top 1 NOBUKTI from DBSO where KODECUST = :kodecust and NoPesanan = :nopo and NOBUKTI <> :nobukti",
      ["kodecust" => $so->KODECUST, "nopo" => $po->POCustomer, "nobukti" => $so->NOBUKTI]);
    if ($dipakai) {
      return 'PO Customer ' . $po->POCustomer . ' sudah dipakai SO ' . $dipakai[0]->NOBUKTI;
    }

    $user = substr((string) \Auth::user()->username, 0, 10);
    $poAsal = $so->NoPesanan;

    try {
      DB::connection('SML')->transaction(function () use ($so, $po, $user, $poAsal) {
        DB::connection('SML')->update("update DBSO set NoPesanan = :nopo, IDPOCust = :id where NOBUKTI = :nobukti",
          ["nopo" => $po->POCustomer, "id" => $po->ID, "nobukti" => $so->NOBUKTI]);

        if ((int) $so->IDPOCust > 0) {
          DB::connection('SML')->update("update DBPOCUSTSUPP set pGanti = 1 where ID = :id", ["id" => $so->IDPOCust]);
        }

        $items = DB::connection('SML')->select("select Urut, KodeBrg, Harga from DBSODET where NOBUKTI = :nobukti order by Urut", ["nobukti" => $so->NOBUKTI]);
        foreach ($items as $item) {
          $harga = (float) $item->Harga;
          $ket = 'PO Asal/ID: ' . $poAsal . '/' . $so->IDPOCust . '-->' . $po->POCustomer . '/' . $po->ID
               . ' Harga Asal : ' . (0 + $item->Harga) . '-->' . number_format($harga, 2, '.', ',');
          DB::connection('SML')->insert("insert into DBHISPOCUST (NOSO, UrutSO, Ket, IdUser, Tanggal, Kodebrg) values (?, ?, ?, ?, getdate(), ?)",
            [$so->NOBUKTI, $item->Urut, substr($ket, 0, 200), $user, $item->KodeBrg]);
        }
      });
    } catch (\Exception $e) {
      return 'Gagal mengubah No PO: ' . $e->getMessage();
    }

    return 1;
  }

  // Detail barang satu SO untuk modal Ubah PO.
  public function listData (Request $req) {
    $harga = DB::connection('SML')->select("declare @NObukti varchar(30)

select @NObukti= :nobukti

Select 	A.NoBukti, A.NoSPB, B.UrutSPB, A.tanggal,
	A.NoBukti+right('0000000000'+cast(B.Urut as varchar(10)),10) NoBuktiUrut,
        B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.NoSat, B.Isi, H.Sat1 Satuan,
        B.Qnt2, H.Sat2 SatuanRoll, B.Harga,
        B.DiscP1, B.DiscRp1, B.DiscTot,
        B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
        B.NPPNRp NPPN, B.BYAngkut Beban, B.SubTotalRp + B.BYAngkut Total,
        B.Qnt Qnt1x,B.Qnt/case when isnull(H.Isi2,0)=0 then 1 else isnull(H.Isi2,0) End Qnt2x,B.Qnt/case when isnull(H.Isi3,0)=0 then 1 else isnull(H.Isi3,0) End Qnt3x,H.Sat1 SAT1X,H.SAT2 SAT2X,H.SAT3 SAT3X ,A.NoPesanan
          ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO,C.PartNumber ,D.NAMAMERK,B.Noserah,A.KodeCUst,E.namaCustSUpp
From dbSO A
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
left outer join dbbarang C on B.kodebrg=C.Kodebrg
Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
LEFT OUTER JOIN DBMERK D ON C.KODEMERK=D.KODEMERK
Left Outer Join DbCustSUpp E on A.KOdeCust=E.KodeCustSupp
where   A.NObukti=@NObukti
order by A.NoBukti, B.Urut" , ['nobukti' => $req->nobukti]);
    return $harga;
  }

}
