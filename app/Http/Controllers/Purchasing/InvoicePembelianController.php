<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Traits\PenjagaTransaksi;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\vwBrowsOutBeli;
use App\Models\vwMasterBeli;
use App\Models\NEWDBBELI;

class InvoicePembelianController extends Controller
{
  use PenjagaTransaksi;


  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti NewPOController).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index (Request $req) {
    $kodemenu = '030401';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);
    list($ipbTglAwal, $ipbTglAkhir) = $this->periodeRange($periode);

    // Data tabel dimuat lewat AJAX (getAlloutbeliinv/getAllInvoiceBeli) - sama pola dengan
    // newpo.blade.php - supaya rentang tanggal bisa diubah tanpa reload halaman.
    return view('purchasing.invoicepembelian' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "gudang" => [],
      "ipbTglAwal" => $ipbTglAwal,
      "ipbTglAkhir" => $ipbTglAkhir,
      "akses" => $akses
    ]);


  }



  public function getBeliDet (Request $req) {
    // VWtampilbeli::where()->get() menyaring di SQL, bukan menarik seluruh view ke memori
    // PHP dulu (::all()->where()) baru menyaring - jauh lebih ringan untuk view besar.
    // Hasilnya kini array baris DATAR (bukan array-berisi-satu-grup seperti sebelumnya),
    // jadi pemanggil (buttonDetail/buttonDetailout) membaca langsung dari res, bukan res[0].
    // Query di bawah adalah isi view VWtampilbeli TANPA klausa "a.pJasa = 0" - view itu
    // menyaring habis dokumen jasa (SML/PBJ/...), sehingga tombol Detail pada baris PBJ di
    // tab Outstanding dulu selalu menerima array kosong. View-nya sendiri TIDAK diubah
    // (dipakai modul lain); alias kolom dijaga sama persis supaya blade tidak perlu diubah.
    return DB::connection('SML')->select("
Select
A.pQC, MONTH(A.TANGGAL) Bulan, YEAR(A.TANGGAL) Tahun, a.TANGGAL, A.KODESUPP, J.NAMACUSTSUPP NamaSupplier, A.KETERANGAN,
A.NoBukti, A.NoUrut, B.NoPO, B.UrutPO, B.KODEGDG, K.NAMA NAMAGUDANG, A.FAKTURSUPP,
-- Kolom header dokumen (dbBeli) + alamat supplier: tidak ada di view VWtampilbeli, padahal
-- form Detail memakainya (Alamat, Valas, Kurs, PPN, Pembayaran, TOP, Jth Tempo, Uang Muka,
-- dan blok total). Tanpa ini semuanya undefined di JS lalu dikosongkan oleh operator nullish.
-- Catatan: JANGAN tulis tanda tanya di komentar SQL ini - PDO membacanya sebagai placeholder
-- posisional dan bentrok dengan parameter bernama :nobukti (hasilnya error 500).
J.ALAMAT1, A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TglJatuhTempo,
A.NOUMK, A.NuangMuka, A.DISC disc, A.DISCRP,
A.NILAIDPP TotDPP, A.NILAIPPN TotPPN, A.NILAINET TotNet,
    B.Urut, B.KodeBrg, B.NamaBrg, B.Qnt, B.NoSat, B.Isi, case when isNull (B.Satuan, '') = '' then 'PCS' else B.Satuan end Satuan,
        0.00 Qnt2, '' SatuanRoll, B.Harga, B.HrgNetto,
        B.DiscP DiscP1, B.DiscTot DiscRp1, B.DiscTot,
        B.SubTotal TotalUSD, B.SubTotal TotalIDR, B.NDPP NDPP,
        B.NPPN NPPN, B.BYAngkut Beban, B.SubTotal + B.BYAngkut Total,
        H.PARTNUMBER, I.QNT QNTPO, I.QNTOUT, H.ISI1, H.ISI2, b.QntTerima, h.NAMABRG namabrgx, l.NAMAMERK, a.IsOtorisasi1
From dbBeliDet B
Left Outer Join dbBeli A On A.NoBukti=b.NoBukti
Left Outer Join dbBarang H on H.KodeBrg=B.KodeBrg
LEFT OUTER JOIN (SELECT y.NoPO NOBUKTI, y.UrutPO Urut, x.QNT - sum(y.QntTerima - ISNULL(QntReject,0)) QNTOUT, x.QNT QNT
                FROM DBBELIDET y
                left outer join DBPODET x on x.NOBUKTI=y.nopo and y.UrutPO=x.urut
                group by y.NoPO, y.UrutPO, x.QNT
                ) I ON B.NoPO=I.NOBUKTI AND B.UrutPO=I.Urut
left outer join DBCUSTSUPP J on A.KODESUPP=J.KODECUSTSUPP
Left Outer join DBGUDANG K ON B.KodeGdg=K.KODEGDG
left outer join DBMERK L on h.KodeMerk = L.KODEMERK
where B.NoBukti = :nobukti
order by B.Urut
    ", ['nobukti' => $req->input('NoBukti')]);
  }

  // Hak akses dibaca dari DBMENUWEB/DBFLMENUWEB (menu web 0305, href invoicepembelian) lewat
  // GlobalController@getAkses - sama dengan index(). Dulu memakai model DBFLMENU (namespace
  // App\Model, file di app/Models) sehingga class tidak ditemukan dan route ini error 500.
  public function getAkses () {
    return app('App\Http\Controllers\GlobalController')->getAkses('0305', 'invoicepembelian');
  }

  public function getAllPO (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    $tglakhirPlus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));

    // Satu query saja mengembalikan seluruh baris (IsOtorisasi1 = 0 maupun 1) - status
    // otorisasi disaring di klien lewat dropdown #ipbOtoStatus, bukan dua query terpisah.
    $tampilinvoicebeli = DB::connection("SML")->select("
    declare @TglAwal date, @TglAkhirPlus date

select @TglAwal=:tglawal , @TglAkhirPlus=:tglakhirplus

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
	A.NoPO,
	Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice
    , case when YEAR(A.TglInvoice)=1899 then '' else CAST(DATEPART(DD,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglInvoice) AS VARCHAR(4)) End TglInvoice
    , A.NoFakturPajak
    ,case when YEAR(A.TglFakturPajak)=1899 then '' else CAST(DATEPART(DD,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglFakturPajak) AS VARCHAR(4)) End TglFakturPajak
    , A.NoBuktiPotong
    ,case when YEAR(A.TglBuktiPotong)=1899 then '' else CAST(DATEPART(DD,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglBuktiPotong) AS VARCHAR(4)) End TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
From vwTransInvoice A
where A.Tanggal >= @TglAwal and A.Tanggal < @TglAkhirPlus
and A.FlagTipe<>9


group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
	A.NoPO, A.NamaKota, 
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
order by A.NOBUKTI


       " , [ "tglawal" => $tglawal , "tglakhirplus" => $tglakhirPlus]);

    return $tampilinvoicebeli;
  }

  // Sumber item LPB yang masih outstanding untuk di-invoice (per item dbBeliDet).
  // vwBrowsOutBeli sudah menyaring Qnt1Sisa > 0 per item (sisa dihitung dari
  // DBInvoiceDET.UrutBeli); NOT EXISTS menjaga item yang sudah masuk invoice mana pun
  // tidak ikut lagi. Alias: A = vwBrowsOutBeli, C = dbBeli (header LPB), D = dbBeliDet,
  // P = dbPO. Dipakai modal Buat Invoice (daftar LPB) dan saat simpan invoice.
  private function sqlItemOutstanding () {
    return "
      from vwBrowsOutBeli A
      inner join DBBELI C on C.NOBUKTI = A.NOBUKTI
      inner join DBBELIDET D on D.NOBUKTI = A.NOBUKTI and D.URUT = A.URUT
      left outer join DBPO P on P.NOBUKTI = A.NOPO
      where A.FlagTipe <> 9
        and A.TNDPP is not null
        and year(C.TANGGAL) > 2017
        and isnull(C.pCloseInv, 0) = 0
        and not exists (select 1 from DBInvoiceDET I where I.NoBeli = A.NOBUKTI and I.UrutBeli = A.URUT)
    ";
  }

  public function getAllPembelian (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    $tglakhirPlus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));

       $tempoutstanding = DB::connection("SML")->select("
       declare @TglAwal date, @TglAkhirPlus date
       select @TglAwal=:tglawal, @TglAkhirPlus=:tglakhirplus
       select A.NoBukti, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
       	A.NoPO, A.KodeGdg,A.PPN
       	,CASE WHEN  A.PPN=0 THEN 'NONE'
       	      WHEN A.PPN=1 THEN 'EXCLUDE'
       	      WHEN A.PPN=2 THEN 'INCLUDE' END KETPPN,isNULL(C.NuangMuka,0)-ISNULL(b.DPP,0) sISANUM ,
                     A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
       from vwBrowsOutBeli A
       left outer join DBUMJUAL b on a.NOPO=b.NOSO
       left outer join DBBELI C ON A.NOBUKTI=C.NOBUKTI
       where A.TANGGAL >= @TglAwal and A.TANGGAL < @TglAkhirPlus
               and A.FlagTipe<>9
            and a.TNDPP is not null  and year(C.tanggal)>2017

        and Isnull(pCLoseInv,0)=0


       group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
       	A.NoPO, A.KodeGdg,A.PPN,b.DPP,C.NuangMuka,b.DPP  ,A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
       order by A.NOBUKTI


       " , [ "tglawal" => $tglawal , "tglakhirplus" => $tglakhirPlus]);


    return $tempoutstanding;

  }



  public function spOtorisasi1 (Request $req) {
      // Hak akses Otorisasi dicek juga di server (bukan hanya di JS) sesuai Set Pemakai.
      $akses = $this->getAkses();
      if (!$akses || !$akses->IsOtorisasi1) {
        return $this->tolak('Anda tidak memiliki akses otorisasi.');
      }
      $username = \Auth::user()->username;
      $nobukti =  $req->nobukti;
      $otorisasi = $req->otorisasi;

      if ($otorisasi == 0 ) {
        $username = '';
        $tanggal = null;
      }


      $inv = $this->headerDokumen('DBINVOICE', $nobukti);
      if (!$inv) {
        return $this->tolak('Invoice ' . $nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
      }
      // Syarat belum oto di WHERE: dua user bersamaan tidak saling menimpa user/tanggal otorisasi
      $update = DB::connection('SML')->update("update dbinvoice set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = getDate() , MaxOL = 1 where nobukti = :nobukti and isnull(IsOtorisasi1, 0) = 0", ['otorisasi' => $otorisasi, 'username' => $username,  'nobukti' => $nobukti ] );
      if (!$update) {
        return $this->tolak('Invoice ' . $nobukti . ' sudah diotorisasi oleh ' . $inv->otouser1 . '.');
      }
      return $update;

    }


    public function spUnOtorisasi1 (Request $req) {
        // Hak akses Batal Otorisasi dicek juga di server (bukan hanya di JS) sesuai Set Pemakai.
        $akses = $this->getAkses();
        if (!$akses || !$akses->IsBatal) {
          return $this->tolak('Anda tidak memiliki akses batal otorisasi.');
        }
        if (trim((string) $req->pket) === '') {
          return $this->tolak('Keterangan batal otorisasi harus diisi.');
        }
        $nobukti =  $req->nobukti;
        $inv = $this->headerDokumen('DBINVOICE', $nobukti);
        if (!$inv) {
          return $this->tolak('Invoice ' . $nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
        }
        if ((int) $inv->isotorisasi1 !== 1) {
          return $this->tolak('Invoice ' . $nobukti . ' belum diotorisasi.');
        }
        $bayar = $this->dibayarInvoice($nobukti);
        if ($bayar) {
          return $this->tolak('Invoice ' . $nobukti . ' sudah dibayar di ' . $bayar . ', otorisasi tidak bisa dibatalkan.');
        }

        $update = DB::connection('SML')->update("update dbinvoice set IsOtorisasi1 = 0 , OtoUser1 = '' , TglOto1 = NULL, MaxOL = -1 where nobukti = :nobukti", [  'nobukti' => $nobukti ] );

        if ($update > 0) {
          app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'INV', $req->nobukti, $req->pket, 0, 'dbinvoice');
        }

        return $update;

      }

    public function deleteinvoice (Request $req) {
      
        $nobukti =  $req->nobukti;
        $inv = $this->headerDokumen('DBINVOICE', $nobukti);
        if (!$inv) {
          return $this->tolak('Invoice ' . $nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
        }
        if ((int) $inv->isotorisasi1 === 1) {
          return $this->tolak('Invoice ' . $nobukti . ' sudah diotorisasi, tidak bisa dihapus.');
        }
        $bayar = $this->dibayarInvoice($nobukti);
        if ($bayar) {
          return $this->tolak('Invoice ' . $nobukti . ' sudah dibayar di ' . $bayar . ', tidak bisa dihapus.');
        }
       
        $update = DB::connection('SML')->update("
        
        declare @NoBukti varchar(30)
        set @NoBukti= :nobukti
        
        
        delete dbinvoicedet where nobukti = @NoBukti
        
        
         if not exists( select NoBukti from dbinvoicedet where NoBukti=@NoBukti)
        begin
        delete dbinvoice where NoBukti=@NoBukti
      end
        
        ", [  'nobukti' => $nobukti ] );
        return $update;

      }  



      public function spedit (Request $req) {
          $nobukti =  $req->input('nobukti');
          $tglfpajak =  $req->input('tglfpajak') ?: null;
          $tglbpotong =  $req->input('tglbpotong') ?: null;
          $tglinvoice = $req->input('tglinvoice') ?: null;
          $nopajak = $req->input('nopajak');
          $nobuktipotong=  $req->input('nobuktipotong');
          $noinvoice = $req->input('noinvoice');
          // Tanggal setengah diketik (mis. tahun 0202) ditolak SQL Server dengan error out-of-range
          foreach (['Tgl faktur pajak' => $tglfpajak, 'Tgl bukti potong' => $tglbpotong, 'Tgl invoice' => $tglinvoice] as $label => $tgl) {
            if ($tgl !== null && (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $tgl, $m) || (int) $m[1] < 1900 || !checkdate((int) $m[2], (int) $m[3], (int) $m[1]))) {
              return $this->tolak($label . ' tidak valid.');
            }
          }

          // Biaya per LPB (tombol + di form Edit) hanya disinkronkan kalau user membuka modal
          // Biaya (biayaSync = 1); daftar yang dikirim = isi akhir biaya invoice ini.
          $sync = (string) $req->input('biayaSync') === '1';
          $biayaBersih = [];
          if ($sync) {
            $lpbInv = array_map(function ($r) { return trim($r->NoBeli); },
              DB::connection('SML')->select("select distinct NoBeli from DBInvoiceDET where NOBUKTI = :nobukti", ['nobukti' => $nobukti]));
            $biaya = $req->input('biaya');
            list($err, $biayaBersih) = $this->validasiBiaya(is_array($biaya) ? $biaya : [], $lpbInv);
            if ($err) { return $this->tolak($err); }
          }

          // Tanggal transaksi (kolom Tanggal) tidak boleh diubah dari modal kelengkapan
          // dokumen, dan hanya dokumen yang belum diotorisasi yang boleh diedit.
          $db = DB::connection('SML');
          $db->beginTransaction();
          try {
            $update = $db->update("update dbinvoice set
            Tglfakturpajak =:tglfpajak, nofakturpajak =:nopajak,nobuktipotong=:nobuktipotong,
            tglbuktipotong=:tglbuktipotong,noinvoice=:noinvoice,tglinvoice=:tglinvoice
            where nobukti = :nobukti and isnull(IsOtorisasi1,0) = 0",
          ['tglfpajak'=>$tglfpajak,'nopajak'=>$nopajak,'nobuktipotong'=>$nobuktipotong,
            'tglbuktipotong'=>$tglbpotong,'noinvoice'=>$noinvoice,'tglinvoice'=>$tglinvoice, 'nobukti' => $nobukti ] );
            if (!$update) {
              $db->rollBack();
              return $update;
            }
            if ($sync) {
              $this->sinkronBiaya($nobukti, $biayaBersih);
            }
            $db->commit();
          } catch (\Throwable $e) {
            $db->rollBack();
            return $this->tolak('Gagal menyimpan: ' . $e->getMessage());
          }
          return $update;

        }

// Validasi daftar biaya dari form (Add / Edit). $lpbBoleh = LPB yang boleh punya biaya.
// Hasil: [pesanError|null, daftar bersih]. Urut/OldKode diisi untuk biaya yang sudah ada.
private function validasiBiaya (array $biaya, array $lpbBoleh) {
  $valas = [];
  foreach (DB::connection('SML')->select("select KODEVLS from DBVALAS") as $v) {
    $valas[strtoupper(trim($v->KODEVLS))] = trim($v->KODEVLS);
  }
  // Kode biaya wajib dari master Daftar Biaya (DBBIAYA)
  $master = [];
  foreach (DB::connection('SML')->select("select Kodebiaya from DBBIAYA") as $m) {
    $master[strtoupper(trim($m->Kodebiaya))] = trim($m->Kodebiaya);
  }
  $bersih = [];
  foreach ($biaya as $b) {
    $lpb = trim((string) ($b['NoBukti'] ?? ''));
    if (!in_array($lpb, $lpbBoleh, true)) {
      return ['LPB ' . $lpb . ' punya biaya tapi belum dicentang.', []];
    }
    $kodeInput = trim((string) ($b['KodeBiaya'] ?? ''));
    $kode = $master[strtoupper($kodeInput)] ?? null;
    if ($kodeInput === '') { return ['Kode biaya LPB ' . $lpb . ' belum dipilih.', []]; }
    if (!$kode) { return ['Kode biaya ' . $kodeInput . ' (LPB ' . $lpb . ') tidak ada di master Daftar Biaya.', []]; }
    $vls = $valas[strtoupper(trim((string) ($b['KodeVls'] ?? '')))] ?? null;
    $nilai = (float) ($b['Nilai'] ?? 0);
    $kurs = strtoupper((string) $vls) === 'IDR' ? 1 : (float) ($b['Kurs'] ?? 0);
    $eksp = trim((string) ($b['KodeCustSupp'] ?? ''));
    if (!$vls) { return ['Valas biaya LPB ' . $lpb . ' tidak valid.', []]; }
    if ($nilai <= 0) { return ['Nilai biaya LPB ' . $lpb . ' harus lebih dari 0.', []]; }
    if ($kurs <= 0) { return ['Kurs biaya LPB ' . $lpb . ' harus lebih dari 0.', []]; }
    if ($eksp === '') { return ['Ekspedisi biaya LPB ' . $lpb . ' belum dipilih.', []]; }
    $bersih[] = [
      'lpb' => $lpb, 'kode' => $kode, 'vls' => $vls, 'nilai' => $nilai, 'kurs' => $kurs, 'eksp' => $eksp,
      'ket' => mb_substr(trim((string) ($b['Keterangan'] ?? '')), 0, 200),
      'urut' => (int) ($b['Urut'] ?? 0),
    ];
  }
  return [null, $bersih];
}

// Samakan dbPBiaya milik invoice ini dengan daftar dari form Edit: yang hilang dihapus
// (sp_PBiaya 'D'), yang ada Urut-nya diubah ('U'), sisanya ditambah ('I'). Dipanggil di
// dalam transaksi. KodeBiaya = kode master DBBIAYA, NoBuktiInv = No LPB; biaya milik invoice
// = biaya semua LPB di DBInvoiceDET invoice ini (satu LPB hanya masuk satu invoice).
private function sinkronBiaya ($nobukti, array $biayaBersih) {
  $db = DB::connection('SML');
  $lama = $db->select("
    select rtrim(Kodebiaya) Kodebiaya, rtrim(NoBuktiInv) NoBuktiInv, Urut from dbPBiaya
    where NoBuktiInv in (select NoBeli from DBInvoiceDET where NOBUKTI = :nobukti)
  ", ['nobukti' => $nobukti]);
  $kodeLama = [];
  foreach ($lama as $l) { $kodeLama[$l->NoBuktiInv . '|' . (int) $l->Urut] = $l->Kodebiaya; }
  $dipakai = [];
  foreach ($biayaBersih as $b) {
    if ($b['urut'] > 0) { $dipakai[$b['lpb'] . '|' . $b['urut']] = 1; }
  }
  foreach ($lama as $l) {
    if (!isset($dipakai[$l->NoBuktiInv . '|' . (int) $l->Urut])) {
      $db->statement('exec sp_PBiaya ?,?,?,?,?,?,?,?,?', ['D', $l->Kodebiaya, '', 0, '', 0, $l->NoBuktiInv, $l->Kodebiaya, (int) $l->Urut]);
    }
  }
  foreach ($biayaBersih as $b) {
    $kunci = $b['lpb'] . '|' . $b['urut'];
    if ($b['urut'] > 0 && isset($kodeLama[$kunci])) {
      // @OldKode = KodeBiaya lama baris itu (kode biaya boleh diganti saat edit)
      $db->statement('exec sp_PBiaya ?,?,?,?,?,?,?,?,?,?,?,?', [
        'U', $b['kode'], $b['ket'], $b['nilai'], $b['vls'], $b['kurs'], $b['lpb'], $kodeLama[$kunci], $b['urut'], '', 0, $b['eksp'],
      ]);
    } else {
      $po = $db->selectOne("select NoPOHd from DBBELI where NOBUKTI = :lpb", ['lpb' => $b['lpb']]);
      $db->statement('exec sp_PBiaya ?,?,?,?,?,?,?,?,?,?,?,?', [
        'I', $b['kode'], $b['ket'], $b['nilai'], $b['vls'], $b['kurs'], $b['lpb'], '', 0, (string) ($po->NoPOHd ?? ''), 0, $b['eksp'],
      ]);
    }
  }
}

// Biaya yang sudah tersimpan untuk satu invoice + info LPB-nya (PO & ekspedisi PO), untuk
// modal Biaya yang dibuka dari form Edit tab Pembelian Invoice.
public function getBiayaInvoice (Request $req) {
  $nobukti = (string) $req->input('nobukti');
  $biaya = DB::connection('SML')->select("
    select rtrim(B.NoBuktiInv) NoBukti, B.Urut, rtrim(B.Kodebiaya) KodeBiaya, isnull(M.Keterangan, '') NamaBiaya,
      B.Keterangan, B.Nilai, rtrim(B.KodeVls) KodeVls, B.Kurs,
      rtrim(B.KODECUSTSUPP) KodeCustSupp,
      rtrim(B.KODECUSTSUPP) + ' - ' + isnull(E.NAMACUSTSUPP, '') NamaCustSupp
    from dbPBiaya B
    left outer join DBCUSTSUPP E on E.KODECUSTSUPP = B.KODECUSTSUPP
    left outer join DBBIAYA M on M.Kodebiaya = B.Kodebiaya
    where B.NoBuktiInv in (select NoBeli from DBInvoiceDET where NOBUKTI = :nobukti)
    order by B.NoBuktiInv, B.Urut
  ", ['nobukti' => $nobukti]);
  $lpb = DB::connection('SML')->select("
    select L.NoBukti, L.Tanggal, L.NoPO, P.TANGGAL TglPO,
      ltrim(rtrim(isnull(P.KODEEXP, ''))) KODEEXP, isnull(E.NAMACUSTSUPP, '') NAMAEXP
    from (select distinct rtrim(D.NoBeli) NoBukti, B.TANGGAL Tanggal, B.NoPOHd NoPO
          from DBInvoiceDET D left outer join DBBELI B on B.NOBUKTI = D.NoBeli
          where D.NOBUKTI = :nobukti) L
    left outer join DBPO P on P.NOBUKTI = L.NoPO
    left outer join DBCUSTSUPP E on E.KODECUSTSUPP = P.KODEEXP
    order by L.Tanggal, L.NoBukti
  ", ['nobukti' => $nobukti]);
  return ['biaya' => $biaya, 'lpb' => $lpb];
}


// Detail satu dokumen invoice cukup dicari lewat NoBukti. Dulu ikut disaring bulan/tahun
// periode kerja user, padahal tabel Kelengkapan Dokumen memakai rentang tanggal bebas -
// dokumen di luar bulan periode menghasilkan array kosong dan tombol Detail/Edit error
// (Cannot read properties of undefined reading Tanggal).
public function getDetailPembelian (Request $req) {

  $tampilinvoicebeli = DB::connection("SML")->select("

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
A.NoPO,
A.NDPPRp TotDPPRp, A.NPPNRp TotPPNRp, A.NNETRp TotNetRp,
A.IDUser,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
  A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
  A.NoInvoice
  ,  A.TglInvoice
  , A.NoFakturPajak
  ,A.TglFakturPajak
  , A.NoBuktiPotong
  ,A.TglBuktiPotong,
  A.BlnMasaPajak, A.ThnMasaPajak,a.myppn, A.NoBeli,A.tglbeli,A.TglPO,
  A.KodeGdg,A.kodebrg KODEBARANG,A.namabrg NAMABARANG,A.QNT,A.SATUAN,A.HARGA,A.NNET
From vwTransInvoice A
where A.FlagTipe<>9
and a.noBukti =:nobukti

/*
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
A.NoPO, A.NamaKota,
A.IDUser,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi, 
  A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
  A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
  A.BlnMasaPajak, A.ThnMasaPajak,a.myppn*/
order by A.NOBUKTI


  " , [ "nobukti" => $req->NoBukti]);

  return $tampilinvoicebeli;
}


// Modal Buat Invoice: daftar LPB outstanding (satu baris per LPB) yang Supplier, Tipe PPN dan
// Valuta-nya sama dengan LPB yang diklik di tab Outstanding, dalam rentang PERIODE yang sama
// dengan tab Outstanding (tglawal/tglakhir, default satu bulan periode kerja).
public function getDetailPO (Request $req) {
  $acuan = DB::connection("SML")->selectOne("
    select B.KODESUPP, B.PPN, isnull(B.KODEVLS, '') KODEVLS,
      isnull((select top 1 isnull(D.NoPO, '') from DBBELIDET D where D.NOBUKTI = B.NOBUKTI), '') NOPO
    from DBBELI B where B.NOBUKTI = :nobukti
  ", ['nobukti' => (string) $req->input('NoBukti')]);
  if (!$acuan) {
    return [];
  }

  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  list($tglawal, $tglakhir) = $this->periodeRange($periode);
  if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
  if (preg_match('/^(19|20)\d{2}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
  if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
  $tglakhirPlus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));

  // KODEEXP/NAMAEXP = ekspedisi di PO LPB itu. WajibBiaya = 1 kalau PO punya ekspedisi
  // (KODEEXP bukan kosong / '-'); LPB seperti itu wajib diisi biaya sebelum invoice disimpan.
  return DB::connection("SML")->select("
    select L.*, isnull(E.NAMACUSTSUPP, '') NAMAEXP,
      case when isnull(L.KODEEXP, '') in ('', '-') then 0 else 1 end WajibBiaya
    from (
      select A.NOBUKTI NoBukti, max(A.TANGGAL) Tanggal, max(A.NOPO) NoPO, max(P.TANGGAL) TglPO,
        max(A.KODECUSTSUPP) KODECUSTSUPP, max(A.NAMACUSTSUPP) NAMACUSTSUPP, max(A.PPN) PPN,
        max(isnull(C.KODEVLS, '')) KODEVLS, max(ltrim(rtrim(isnull(P.KODEEXP, '')))) KODEEXP,
        case max(A.PPN) when 0 then 'NONE' when 1 then 'EXCLUDE' when 2 then 'INCLUDE' end MyPPN
      " . $this->sqlItemOutstanding() . "
        and A.KODECUSTSUPP = :kodesupp and A.PPN = :ppn and isnull(C.KODEVLS, '') = :kodevls
        and isnull(A.NOPO, '') = :nopo
        and A.TANGGAL >= :tglawal and A.TANGGAL < :tglakhirplus
      group by A.NOBUKTI
    ) L
    left outer join DBCUSTSUPP E on E.KODECUSTSUPP = L.KODEEXP
    order by L.Tanggal, L.NoBukti
  ", ['kodesupp' => $acuan->KODESUPP, 'ppn' => (int) $acuan->PPN, 'kodevls' => $acuan->KODEVLS,
      'nopo' => $acuan->NOPO,
      'tglawal' => $tglawal, 'tglakhirplus' => $tglakhirPlus]);
}


// Dropdown valas di modal Biaya
public function listValas () {
  // IDR paling atas (default di form Biaya), sisanya urut kode
  return DB::connection('SML')->select("
    select rtrim(KODEVLS) KODEVLS, NAMAVLS, KURS from DBVALAS
    order by case when rtrim(KODEVLS) = 'IDR' then 0 else 1 end, KODEVLS
  ");
}

// Browse ekspedisi di modal Biaya (master ekspedisi = dbcustsupp JENIS 3, hanya yang aktif)
public function listEkspedisi () {
  return DB::connection('SML')->select("select KODECUSTSUPP, NAMACUSTSUPP from DBCUSTSUPP where JENIS = 3 and IsAktif = 1 order by NAMACUSTSUPP");
}

// Browse kode biaya di modal Biaya (master Daftar Biaya = DBBIAYA)
public function listBiaya () {
  return DB::connection('SML')->select("select rtrim(Kodebiaya) Kodebiaya, Keterangan from DBBIAYA order by Kodebiaya");
}

// Simpan invoice per LPB: setiap LPB yang dicentang masuk beserta SEMUA item outstanding-nya
// (qty penuh, kolom sama dengan SP_INVB). LPB yang bisa dipilih sudah dibatasi satu PO
// (filter di getDetailPO), selain Supplier + Tipe PPN + Valuta sama. Header dibuat dari PO LPB pertama (salinan bagian header SP_INVB) dengan
// No/Tgl Invoice, Faktur Pajak dan Bukti Potong kosong (diisi nanti lewat tab Pembelian
// Invoice > Edit). Biaya per LPB disimpan lewat sp_PBiaya 'I' dalam transaksi yang sama -
// kalau invoice gagal, biaya ikut batal.
public function spBeliGudang (Request $req) {
  $username = \Auth::user()->username;
  $lpbs = $req->input('lpbs');
  if (!is_array($lpbs) || !count($lpbs)) {
    return $this->tolak('Tidak ada LPB dipilih.');
  }
  $lpbs = array_values(array_unique(array_filter(array_map(function ($x) { return trim((string) $x); }, $lpbs), 'strlen')));
  $biaya = $req->input('biaya');
  $biaya = is_array($biaya) ? $biaya : [];

  $nobuktiInv = trim((string) $req->input('reqNobukti'));
  if ($nobuktiInv === '') {
    return $this->tolak('No bukti invoice kosong. Silakan buka ulang form.');
  }
  $tanggal = (string) $req->input('reqTanggal');
  if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $m) || (int) $m[1] < 1900 || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
    return $this->tolak('Tanggal tidak valid.');
  }
  $inv = $this->headerDokumen('DBINVOICE', $nobuktiInv);
  if ($inv && (int) $inv->isotorisasi1 === 1) {
    return $this->tolak('Invoice ' . $nobuktiInv . ' sudah diotorisasi, tidak bisa diubah.');
  }

  // Validasi biaya sebelum menyentuh database
  list($errBiaya, $biayaBersih) = $this->validasiBiaya($biaya, $lpbs);
  if ($errBiaya) { return $this->tolak($errBiaya); }
  $lpbAdaBiaya = array_flip(array_column($biayaBersih, 'lpb'));

  // Catatan kecepatan: server DB jauh (tiap query bolak-balik jaringan), jadi jumlah query
  // ditekan - kunci + ambil item + data ekspedisi PO digabung SATU query per LPB, dan item
  // satu LPB di-INSERT dengan SATU statement (bukan per item).
  $db = DB::connection('SML');
  $db->beginTransaction();
  try {
    $rows = [];
    $poLpb = [];
    $urutLpb = [];
    foreach ($lpbs as $lpb) {
      // Baris pertama: kunci DBInvoiceDET LPB ini supaya dua user tidak meng-invoice LPB
      // yang sama bersamaan (hasilnya ditampung variabel, tidak dikembalikan).
      $items = $db->select("
        set nocount on;
        declare @kunci varchar(30);
        select top 1 @kunci = NOBUKTI from DBInvoiceDET with (updlock, holdlock) where NoBeli = :lpbkunci;
        select A.NOBUKTI, A.URUT, A.NOPO, A.KODECUSTSUPP, A.PPN, isnull(C.KODEVLS, '') KODEVLS,
          ltrim(rtrim(isnull(P.KODEEXP, ''))) KODEEXP
        " . $this->sqlItemOutstanding() . "
          and A.NOBUKTI = :lpb
        order by A.URUT
      ", ['lpbkunci' => $lpb, 'lpb' => $lpb]);
      if (!$items) {
        $db->rollBack();
        return $this->tolak('LPB ' . $lpb . ' sudah tidak outstanding (sudah masuk invoice lain). Silakan refresh.');
      }
      // PO memakai ekspedisi (KODEEXP bukan kosong / '-') -> biaya wajib diisi
      if (!isset($lpbAdaBiaya[$lpb]) && !in_array($items[0]->KODEEXP, ['', '-'], true)) {
        $db->rollBack();
        return $this->tolak('Biaya wajib diisi karena PO dari LPB ' . $lpb . ' memiliki ekspedisi.');
      }
      $poLpb[$lpb] = $items[0]->NOPO;
      foreach ($items as $it) {
        $rows[] = $it;
        $urutLpb[$lpb][] = (int) $it->URUT;
      }
    }

    $acuan = $rows[0];
    foreach ($rows as $r) {
      if ($r->KODECUSTSUPP !== $acuan->KODECUSTSUPP || (int) $r->PPN !== (int) $acuan->PPN || $r->KODEVLS !== $acuan->KODEVLS) {
        $db->rollBack();
        return $this->tolak('LPB yang dipilih harus satu supplier, tipe PPN dan valuta yang sama.');
      }
    }

    if ($inv) {
      if (trim((string) $inv->kodesupp) !== trim((string) $acuan->KODECUSTSUPP)) {
        $db->rollBack();
        return $this->tolak('Invoice ' . $nobuktiInv . ' milik supplier lain. Silakan buka ulang form.');
      }
    } else {
      $db->insert("
        insert into DBInvoice (NOBUKTI, NOURUT, TANGGAL, KETERANGAN, KodeSupp, NoPO, NoFaktur, TglJatuhTempo,
          KodeVls, Kurs, PPN, TipeBayar, Hari, TipeDisc, DISC, DISCRP, ISCETAK, NilaiCetak, IDUser,
          IsBatal, UserBatal, KodeExp, cetakke,
          Flagtipe, TipePPN, TglInvoice, TglFakturPajak, TglBuktiPotong, NoInvoice, NoFakturPajak, NoBuktiPotong, PN)
        select top 1 :nobukti, :nourut, :tanggal, '', A.KODESUPP, A.NOBUKTI, '', A.TglJatuhTempo,
          A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TipeDisc, A.DISC, A.DISCRP, A.ISCETAK, A.NilaiCetak, :iduser,
          0, '', A.KODEEXP, 0,
          A.flagtipe, A.TipePPN, null, null, null, '', '', '', 0
        from DBPO A
        where A.NOBUKTI = :nopo
      ", [
        'nobukti' => $nobuktiInv, 'nourut' => (string) $req->input('reqNourut'), 'tanggal' => $tanggal,
        'iduser' => $username, 'nopo' => $acuan->NOPO,
      ]);
      if (!$this->headerDokumen('DBINVOICE', $nobuktiInv)) {
        $db->rollBack();
        return $this->tolak('PO ' . $acuan->NOPO . ' tidak ditemukan, invoice gagal dibuat.');
      }
    }

    // Satu INSERT per LPB untuk semua item outstanding-nya. URUT invoice melanjutkan urut
    // terakhir + nomor baris (urut item LPB), hasilnya sama dengan insert per item dulu.
    foreach ($urutLpb as $lpb => $daftarUrut) {
      $inUrut = implode(',', array_map('intval', $daftarUrut));
      $db->insert("
        insert into DBInvoiceDET (NOBUKTI, URUT, NoBeli, KODEBRG, KodeGdg,
          PPN, KURS, DISC, QNT, NOSAT, SATUAN, SAT_1, SAT_2, ISI,
          HARGA, DISCP, DISCTOT, BYANGKUT, NoPO, UrutPO,
          QntTerima, Qnt1Terima, Qnt2Terima, UrutBeli,
          KetReject, DiscP2, DiscP3, DiscP4, DiscP5, NoSPJ, NamaBrg, NILAIPPN)
        select :nobukti, isnull((select max(URUT) from DBInvoiceDET where NOBUKTI = :nobukti2), 0) + ROW_NUMBER() over (order by A.URUT),
          A.NOBUKTI, A.KODEBRG, A.KodeGdg, A.PPN, A.KURS, A.DISC, A.QntTerima, A.NOSAT, A.SATUAN, A.Sat_1, A.Sat_2,
          A.ISI, A.HARGA, A.DISCP, A.DISCTOT, A.BYANGKUT, A.NoPO, A.UrutPO, A.QntTerima, A.Qnt1Terima, A.Qnt2Terima, A.URUT,
          A.KetReject, A.DiscP2, A.DiscP3, A.DiscP4, A.DiscP5, '', A.NamaBrg, A.NilaiPPN
        from DBBELIDET A
        where A.NOBUKTI = :lpb and A.URUT in ($inUrut)
      ", ['nobukti' => $nobuktiInv, 'nobukti2' => $nobuktiInv, 'lpb' => $lpb]);
    }

    // Biaya per LPB: KodeBiaya = kode master DBBIAYA, NoBuktiInv = No LPB, NoBiayaPO = PO LPB,
    // UrutBiayaPO = 0. Urut dinomori sp_PBiaya sendiri (max urut per LPB + 1).
    foreach ($biayaBersih as $b) {
      $db->statement('exec sp_PBiaya ?,?,?,?,?,?,?,?,?,?,?,?', [
        'I', $b['kode'], $b['ket'], $b['nilai'], $b['vls'], $b['kurs'], $b['lpb'],
        '', 0, (string) $poLpb[$b['lpb']], 0, $b['eksp'],
      ]);
    }

    if ($this->jumlahBaris('DBInvoiceDET', $nobuktiInv) == 0) {
      $db->rollBack();
      return $this->tolak('Invoice gagal disimpan. Silakan coba lagi.');
    }
    $db->commit();
  } catch (\Throwable $e) {
    $db->rollBack();
    return $this->tolak('Invoice gagal disimpan: ' . $e->getMessage());
  }

  return 1;
}



public function getNoBukti (Request $req) {
 
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $inisial = DB::connection("SML")->select('select BPL from DBNOMOR');
  $username = \Auth::user()->username;
  $values = [
      $inisial[0]->BPL,
      $periode->bulan,
      $periode->tahun,
      $username
  ];
  $resnobukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    // $nobuktib =  $resnobukti[0]->Nobukti;
    // $nourutb =  $resnobukti[0]->Nourut;

    return $resnobukti;


}

public function addDBBeli (Request $req) {
  // try {

        $data = $req->input('data');
        $suratJalan = $req->input('suratJalan');
        $noKend = $req->input('noKend');
        $noPO = $req->input('noPO');
        // $date = date("Y-m-d H:i:s");
        $date = $req->input('inputTanggal');
        $gudang = $req->input('gudang');
        $noBukti = $req->input('noBukti');
        $noUrut = $req->input('noUrut');
        $username = \Auth::user()->username;
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


        $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :nobukti',["nobukti" => $noBukti]);
        if ($check) {
           2;
        }
        // delete	TempOutstandingPO where IDUser=@IDUser
        DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);
        foreach ($data as $d) {
          $values = [$username,$noPO,$periode->tahun,$periode->bulan, 0,$d['inputQntTerima'], 1, $d['Urut']];
          DB::connection("SML")->statement('exec sp_RefreshTempOutstandingPOweb ?,?,?,?,?,?,?,?',$values);

        }

          $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 0, $username];

        DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
         1;

}

  // Nomor pelunasan hutang (DPH) yang sudah membayar invoice ini, atau null.
  private function dibayarInvoice($nobukti) {
    foreach (['NoInvoice', 'NoinvoiceBeli', 'NOFAKTUR'] as $kolom) {
      $dph = $this->dipakaiOleh('DBDPHDET', $kolom, $nobukti);
      if ($dph) {
        return $dph;
      }
    }
    return null;
  }
}
