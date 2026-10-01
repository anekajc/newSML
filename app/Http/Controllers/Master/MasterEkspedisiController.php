<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;

class MasterEkspedisiController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis from DBCUSTSUPP where Jenis = 3');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterekspedisi' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

public function loadAll () {
    $listData = DB::connection('SML')->select("Declare @Isaktif tinyint,@Perkiraan varchar(100), @Flagmenu int
set @isaktif=2
Select @Perkiraan='semua',@Flagmenu=3
select  distinct 
A.[KODECUSTSUPP]
      ,A.[NAMACUSTSUPP]
      ,A.[ALAMAT1]
      ,A.[ALAMAT2]
      ,A.[Kota]
      ,A.[TELPON]
      ,A.[FAX]
      ,A.[EMAIL]
      ,A.[KODEPOS]
      ,A.[NEGARA]
      ,A.[NPWP]
      ,A.[Tanggal]
      ,A.[PLAFON]
      ,A.[HARI]
      ,A.[HARIHUTPIUT]
      ,A.[BERIKAT]
      ,A.[USAHA]
      ,A.[PERKIRAAN]
      ,A.[JENIS]
      ,A.[NAMAPKP]
      ,A.[ALAMATPKP1]
      ,A.[ALAMATPKP2]
      ,A.[KOTAPKP]
      ,A.[Sales]
      ,A.[KodeVls]
      ,A.[KodeExp]
      ,A.[KodeTipe]
      ,A.[IsPpn]
      ,A.[IsAktif]
      ,A.[Kind]
      ,A.[ContactP]
      ,A.[Alamat1ContP]
      ,A.[Alamat2ContP]
      ,A.[KotaContP]
      ,A.[NegaraContP]
      ,A.[TelpContP]
      ,A.[FaxContP]
      ,A.[EmailContP]
      ,A.[KODEPOSContP]
      ,A.[HPContP]
      ,A.[SyaratPenerimaan]
      ,A.[SyaratPembayaran]
      ,A.[Agent]
      ,A.[Alamat1A]
      ,A.[Alamat2A]
      ,A.[KotaA]
      ,A.[NegaraA]
      ,A.[ContactA]
      ,A.[TelpA]
      ,A.[FaxA]
      ,A.[EmailA]
      ,A.[KODEPOSA]
      ,A.[HPA]
      ,A.[EmailContA]
     
      ,A.[PortOfLoading]
      ,A.[CountryOfOrigin]
      ,A.[TglInput]
      ,A.[iskontrak]
      ,A.[PPN]
      ,A.[HargaKe]
      ,A.[Att]
      ,A.[bank]
      ,A.[NoAcc]
      ,A.[IsMember]
      ,A.[TanggalValid]
      ,A.[DiscMember]
      ,A.[AttPhone]
      ,A.[ket]
      ,A.[JenisCustSupp]
      ,A.[IntCode]
      ,A.[CompCode]
      ,A.[AttDepart]
      ,A.[ATN]
      ,A.[pBlackList]
      ,A.[KETBLACKLIST]
      ,A.[NPPH23]
      ,A.[NPPH22]
      ,A.[BankTemp]
      ,A.[NoaccTemp]
      ,A.[ATNTemp]
      ,A.[CustCode]
      ,A.[BOffice]
      ,A.[KodeSls]
      ,A.[pCounter],

        a.ALAMAT1+case when ltrim(a.Alamat2)='' then '' else CHAR(13)+a.ALAMAT2 end ALAMAT,
        a.Usaha+Case when isnull(a.Usaha,'')='' then '' else '. ' end+a.NamacustSupp Nama,case when isnull(A.ppn,0)=1 then 'Ya' else 'Tidak' End PPNX,
        case when A.iskontrak is null then 0 when A.iskontrak=0 then 0 when A.iskontrak=1 then 1 end xkontrak, NamaJenis,Cd.NamaGroupCustSUpp MyAgent,M1.nama NamaSls,
        D.namaKota,D.KodeArea,E.namaArea,[dbo].[DataPostHutPiut](A.KodecustSupp,Case when @Flagmenu=0 then 'HT' else 'PT' end) DetailAkun,A.BoFFice,Mx.Nama NamaBoFFice 
from dbCustSupp  a         
     left Outer join dbperkcustsupp b on b.kodecustsupp=a.kodecustsupp  left outer join VwBoFfice Mx on a.boffice=Mx.Boffice
     left Outer join dbPerkiraan c on c.perkiraan=b.perkiraan and c.tipe=1  left outer join dbkaryawan M1 on cast(m1.KeyNIK as varchar(3))=A.KOdeSls
     Left Outer join dbkota D on a.kota=D.KodeKota
     Left outer join dbarea E on D.KodeArea=E.KodeArea                  
     Left Outer Join DbJenisCustSupp F on A.JenisCustSupp=F.KodeJenis
      left outer join DBGROUPCUSTSUPP cd on  A.agent=cd.KODEGROUPCUSTSUPP
where (a.IsAktif  Like (Case when @isAktif=0 then 0
                             when @isAktif=1 then 1
                        end) or
     (Case when @isAktif=0 then 0
           when @isAktif=1 then 1
           else 2
      end)=2) and isnull(c.Keterangan,'')+' ('+isnull(b.Perkiraan,'')+')' like Case when @Perkiraan='Semua' then '%' else @Perkiraan end
and a.Jenis=3");
    return $listData;
}

  public function submitDelete (Request $req) {

    $delete = DB::connection('SML')->update('delete from DBCUSTSUPP where KODECUSTSUPP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select("Declare @Isaktif tinyint,@Perkiraan varchar(100), @Flagmenu int
set @isaktif=2
Select @Perkiraan='semua',@Flagmenu=3
select  distinct 
A.[KODECUSTSUPP]
      ,A.[NAMACUSTSUPP]
      ,A.[ALAMAT1]
      ,A.[ALAMAT2]
      ,A.[Kota]
      ,A.[TELPON]
      ,A.[FAX]
      ,A.[EMAIL]
      ,A.[KODEPOS]
      ,A.[NEGARA]
      ,A.[NPWP]
      ,A.[Tanggal]
      ,A.[PLAFON]
      ,A.[HARI]
      ,A.[HARIHUTPIUT]
      ,A.[BERIKAT]
      ,A.[USAHA]
      ,A.[PERKIRAAN]
      ,A.[JENIS]
      ,A.[NAMAPKP]
      ,A.[ALAMATPKP1]
      ,A.[ALAMATPKP2]
      ,A.[KOTAPKP]
      ,A.[Sales]
      ,A.[KodeVls]
      ,A.[KodeExp]
      ,A.[KodeTipe]
      ,A.[IsPpn]
      ,A.[IsAktif]
      ,A.[Kind]
      ,A.[ContactP]
      ,A.[Alamat1ContP]
      ,A.[Alamat2ContP]
      ,A.[KotaContP]
      ,A.[NegaraContP]
      ,A.[TelpContP]
      ,A.[FaxContP]
      ,A.[EmailContP]
      ,A.[KODEPOSContP]
      ,A.[HPContP]
      ,A.[SyaratPenerimaan]
      ,A.[SyaratPembayaran]
      ,A.[Agent]
      ,A.[Alamat1A]
      ,A.[Alamat2A]
      ,A.[KotaA]
      ,A.[NegaraA]
      ,A.[ContactA]
      ,A.[TelpA]
      ,A.[FaxA]
      ,A.[EmailA]
      ,A.[KODEPOSA]
      ,A.[HPA]
      ,A.[EmailContA]
     
      ,A.[PortOfLoading]
      ,A.[CountryOfOrigin]
      ,A.[TglInput]
      ,A.[iskontrak]
      ,A.[PPN]
      ,A.[HargaKe]
      ,A.[Att]
      ,A.[bank]
      ,A.[NoAcc]
      ,A.[IsMember]
      ,A.[TanggalValid]
      ,A.[DiscMember]
      ,A.[AttPhone]
      ,A.[ket]
      ,A.[JenisCustSupp]
      ,A.[IntCode]
      ,A.[CompCode]
      ,A.[AttDepart]
      ,A.[ATN]
      ,A.[pBlackList]
      ,A.[KETBLACKLIST]
      ,A.[NPPH23]
      ,A.[NPPH22]
      ,A.[BankTemp]
      ,A.[NoaccTemp]
      ,A.[ATNTemp]
      ,A.[CustCode]
      ,A.[BOffice]
      ,A.[KodeSls]
      ,A.[pCounter],

        a.ALAMAT1+case when ltrim(a.Alamat2)='' then '' else CHAR(13)+a.ALAMAT2 end ALAMAT,
        a.Usaha+Case when isnull(a.Usaha,'')='' then '' else '. ' end+a.NamacustSupp Nama,case when isnull(A.ppn,0)=1 then 'Ya' else 'Tidak' End PPNX,
        case when A.iskontrak is null then 0 when A.iskontrak=0 then 0 when A.iskontrak=1 then 1 end xkontrak, NamaJenis,Cd.NamaGroupCustSUpp MyAgent,M1.nama NamaSls,
        D.namaKota,D.KodeArea,E.namaArea,[dbo].[DataPostHutPiut](A.KodecustSupp,Case when @Flagmenu=0 then 'HT' else 'PT' end) DetailAkun,A.BoFFice,Mx.Nama NamaBoFFice 
from dbCustSupp  a         
     left Outer join dbperkcustsupp b on b.kodecustsupp=a.kodecustsupp  left outer join VwBoFfice Mx on a.boffice=Mx.Boffice
     left Outer join dbPerkiraan c on c.perkiraan=b.perkiraan and c.tipe=1  left outer join dbkaryawan M1 on cast(m1.KeyNIK as varchar(3))=A.KOdeSls
     Left Outer join dbkota D on a.kota=D.KodeKota
     Left outer join dbarea E on D.KodeArea=E.KodeArea                  
     Left Outer Join DbJenisCustSupp F on A.JenisCustSupp=F.KodeJenis
      left outer join DBGROUPCUSTSUPP cd on  A.agent=cd.KODEGROUPCUSTSUPP
where (a.IsAktif  Like (Case when @isAktif=0 then 0
                             when @isAktif=1 then 1
                        end) or
     (Case when @isAktif=0 then 0
           when @isAktif=1 then 1
           else 2
      end)=2) and isnull(c.Keterangan,'')+' ('+isnull(b.Perkiraan,'')+')' like Case when @Perkiraan='Semua' then '%' else @Perkiraan end
and a.Jenis=3 and a.KODECUSTSUPP = :kode" , ['kode' => $req->kode]);
    return $detail;
  }

  public function submitAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where KODECUSTSUPP = :kodeCustSupp' , ['kodeCustSupp' => $req->kodeCustSupp]);

    if ($check) {
      return 'Kode supplier/customer sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into DBCUSTSUPP (
    USAHA, KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1, ALAMAT2, 
    Kota,  KODEPOS, NEGARA, TELPON, FAX, 
    EMAIL, NPPH23, NPPH22, IsAktif, Att,
    AttPhone, AttDepart, NPWP, NAMAPKP, ALAMATPKP1, 
    ALAMATPKP2, KOTAPKP, pCounter, NoAcc, Bank, ATN, IsPpn, 
    JENIS, PERKIRAAN, pBlackList, KETBLACKLIST) 
    VALUES 
    (
    :bentukUsaha, :kodeCustSupp, :namaCustSupp, :alamat1, :alamat2, 
    :kodeKota, :kodePos, :negara, :telepon, :fax, 
    :email, :npph23, :npph21, :isAktif, :att, 
    :attPhone, :attDepart, :npwp, :namaPkp, :alamatPkp, 
    :alamatPkp2, :kotaPkp, :counter, :accNo, :bank, :atasNama, :isPpn, 
    :jenis, :perkiraan, :blacklist, :ketblacklist)', 
   
    [
    'bentukUsaha'=> $req->bentukUsaha, 
    'kodeCustSupp' => $req->kodeCustSupp,
    'namaCustSupp' => $req->namaCustSupp, 
    'alamat1' => $req->alamat1, 
    'alamat2' => $req->alamat2, 
    'kodeKota'=> $req->kodeKota, 
    
    'kodePos' => $req->kodePos,  
    'negara' => $req->negara, 
    'telepon' => $req->telepon,
    'fax' => $req->fax, 
    'email' => $req->email, 

    'npph23' => $req->npph23, 
    'npph21' => $req->npph21, 
    'isAktif'=> $req->isAktif, 
    'att'=> $req->att, 

    'attPhone'=> $req->attPhone,
    'attDepart'=>$req->attDepart, 
    'npwp' => $req->npwp, 
    'namaPkp' => $req->namaPkp,
    'alamatPkp' => $req->alamatPkp, 

    'alamatPkp2' => $req->alamatPkp2,
    'kotaPkp' => $req->kotaPkp, 
    'counter' => 0,
    'accNo' => $req->accNo,
    'bank' => $req->bank,
    'atasNama' => $req->atasNama,
    // Dulu selalu 0 - centang PKP di form Add tidak pernah tersimpan.
    'isPpn' => $req->IsPpn ? 1 : 0,
    'jenis' => 3,
    'perkiraan' => 'SEMUA',
    'blacklist' => 0,
    'ketblacklist' => '',
  ]);

    return 1;

  }

  public function submitEdit (Request $req) {
        $listData = DB::connection('SML')->update('UPDATE DBCUSTSUPP SET 
        USAHA = :bentukUsaha, 
        NAMACUSTSUPP = :namaCustSupp, 
        ALAMAT1 = :alamat1, 
        ALAMAT2 = :alamat2, 
        Kota = :kodeKota, 
        KODEPOS = :kodePos, 
        NEGARA = :negara, 
        TELPON = :telepon, 
        FAX = :fax, 
        EMAIL = :email, 
        NPPH23 = :npph23, 
        NPPH22 = :npph21, 
        IsAktif = :isAktif, 
        Att = :att, 
        AttPhone = :attPhone, 
        AttDepart = :attDepart, 
        NPWP = :npwp, 
        NAMAPKP = :namaPkp, 
        ALAMATPKP1 = :alamatPkp, 
        ALAMATPKP2 = :alamatPkp2, 
        KOTAPKP = :kotaPkp, 
        pCounter = :counter, 
        NoAcc = :accNo, 
        Bank = :bank, 
        ATN = :atasNama, 
        IsPpn = :isPpn, 
        JENIS = :jenis, 
        PERKIRAAN = :perkiraan, 
        pBlackList = :blacklist, 
        KETBLACKLIST = :ketblacklist 
        WHERE KODECUSTSUPP = :kodeCustSupp', 
        [
            'bentukUsaha'=> $req->bentukUsaha, 
            'kodeCustSupp' => $req->kodeCustSupp,
            'namaCustSupp' => $req->namaCustSupp, 
            'alamat1' => $req->alamat1, 
            'alamat2' => $req->alamat2, 
            'kodeKota'=> $req->kodeKota, 
            'kodePos' => $req->kodePos,  
            'negara' => $req->negara, 
            'telepon' => $req->telepon,
            'fax' => $req->fax, 
            'email' => $req->email, 
            'npph23' => $req->npph23, 
            'npph21' => $req->npph21, 
            'isAktif'=> $req->isAktif, 
            'att'=> $req->att, 
            'attPhone'=> $req->attPhone,
            'attDepart'=>$req->attDepart, 
            'npwp' => $req->npwp, 
            'namaPkp' => $req->namaPkp,
            'alamatPkp' => $req->alamatPkp, 
            'alamatPkp2' => $req->alamatPkp2,
            'kotaPkp' => $req->kotaPkp, 
            'counter' => 0,
            'accNo' => $req->accNo,
            'bank' => $req->bank,
            'atasNama' => $req->atasNama,
            'isPpn' => $req->isPpn,
            'jenis' => 3,
            'perkiraan' => 'SEMUA',
            'blacklist' => 0,
            'ketblacklist' => '',
        ]);

    return $listData;
  }

  public function loadDetailAkun (Request $req) {
    $listData = DB::connection('SML')->select('Select a.*, b.Keterangan NamaPerkiraan,
       b.Keterangan+\' (\'+b.Perkiraan+\')\' As Perk_Perkiraan,
       c.NamaCustSupp
from dbPerkCustSupp a
     left outer join DBPERKIRAAN b on b.Perkiraan=a.perkiraan and b.Tipe=1
     left Outer join dbcustSupp c on c.KodecustSupp=a.kodecustSupp
where A.kodeCustSupp= :kodeCustSupp
Order by a.kodeCustSupp',['kodeCustSupp'=>$req->kodeCustSupp]);
    return $listData;
  }

  public function loadDetailAkunEdit (Request $req) {
    $listData = DB::connection('SML')->select('Select a.*, b.Keterangan NamaPerkiraan,
       b.Keterangan+\' (\'+b.Perkiraan+\')\' As Perk_Perkiraan,
       c.NamaCustSupp
from dbPerkCustSupp a
     left outer join DBPERKIRAAN b on b.Perkiraan=a.perkiraan and b.Tipe=1
     left Outer join dbcustSupp c on c.KodecustSupp=a.kodecustSupp
where A.kodeCustSupp= :kodeCustSupp and A.Urut = :urutData
Order by a.kodeCustSupp',['kodeCustSupp'=>$req->kodeCustSupp, 'urutData'=>$req->urutData]);
    return $listData;
  }

  public function loadHutangPiutang (Request $req) {
    $listData = DB::connection('SML')->select('select a.Perkiraan, b.keterangan 
from dbposthutpiut a 
left outer join dbperkiraan b on b.perkiraan=a.perkiraan 
where a.Kode in (\'HT\',\'PT\',\'UHT\',\'UPT\') and 
a.Perkiraan not in (Select Perkiraan from dbperkcustsupp where kodecustsupp= :kodeCustSuppTemp) 
order by a.Perkiraan',['kodeCustSuppTemp'=>$req->kodeCustSuppTemp]);
    return $listData;
  }


  public function submitAddDetailAkun (Request $req) {
    $add = DB::connection('SML')->update('exec Sp_PerkCustSupp :choice, :kodeCustSupp, :Urut, :Perkiraan, :OldKodeCustSupp, :OldMUrut' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'choice' => $req->choice,
      'Perkiraan' => $req->Perkiraan,
      'Urut' => 0,
      'OldKodeCustSupp' => '',
      'OldMUrut' => 0
    ]);

    return $add;
  }

  public function submitDeleteDetailAkun (Request $req) {
    $add = DB::connection('SML')->update('exec Sp_PerkCustSupp :choice, :kodeCustSupp, :Urut, :Perkiraan, :OldKodeCustSupp, :OldMUrut' , [
      'OldKodeCustSupp' => $req->kodeCustSupp,
      'choice' => $req->choice,
      'Perkiraan' => '',
      'OldMUrut' => $req->urutData,
      'kodeCustSupp' => '',
      'Urut' => 0
    ]);

    return $add;
  }

  public function submitEditDetailAkun (Request $req) {
    $add = DB::connection('SML')->update('exec Sp_PerkCustSupp :choice, :kodeCustSupp, :Urut, :Perkiraan, :OldKodeCustSupp, :OldMUrut' , [
      'OldKodeCustSupp' => $req->OldKodeCustSupp,
      'choice' => $req->choice,
      'Perkiraan' => $req->Perkiraan,
      'OldMUrut' => $req->urutData,
      'kodeCustSupp' => $req->kodeCustSupp,
      'Urut' => 0
    ]);

    return $add;
  }
  public function loadAlamatKirim (Request $req) {
    $listData = DB::connection('SML')->select('select A.KodeCustSupp, A.Nomor, A.Nama, A.Alamat, A.Telp, A.Fax,
       cast(A.Alamat as text) AlamatTxt ,A.UP
from dbAlamatCust A
where A.kodecustSupp= :kodeCustSupp and nomor<>0  and isnull(pSurat,0)= 0
Order by A.Nomor',['kodeCustSupp'=>$req->kodeCustSupp]);
    return $listData;
  }

    public function submitAddAlamatKirim (Request $req) {
    $add = DB::connection('SML')->update('Insert into dbAlamatCust (KodeCustSupp, Nomor, Alamat, Telp, Fax, Nama,pSurat,up)
values(:kodeCustSupp,:Nomor,:Alamat,:Telp, :Fax, :Nama, :pSurat, :up)' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'Alamat' => $req->Alamat,
      'Nama' => $req->Nama,
      'Telp' => $req->Telp,
      'Fax' => $req->Fax,
      'up' => $req->up,
      'Nomor' => $req->Nomor,
      'pSurat' => '',
      
    ]);

    return $add;
  }

  public function getNewNomorAlamatKirim (Request $req) {
    $nomor = DB::connection('SML')->select('select max(Nomor)Nomor from dbAlamatCust where KodeCustSupp=:kodeCustSupp and isnull(psurat,0)=0',['kodeCustSupp'=>$req->kodeCustSupp]);

    return $nomor;
  }

  public function submitDeleteAlamatKirim (Request $req) {
    $add = DB::connection('SML')->update('delete from DBALAMATCUST where Nomor = :Nomor and KODECUSTSUPP = :kodeCustSupp' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'Nomor' => $req->Nomor,
    ]);

    return $add;
  }

    public function loadAlamatKirimEdit (Request $req) {
    $listData = DB::connection('SML')->select('select A.KodeCustSupp, A.Nomor, A.Nama, A.Alamat, A.Telp, A.Fax,
       cast(A.Alamat as text) AlamatTxt ,A.UP
from dbAlamatCust A
where A.kodecustSupp=:kodeCustSupp and nomor = :Nomor and isnull(pSurat,0)= 0
Order by A.Nomor',['kodeCustSupp'=>$req->kodeCustSupp, 'Nomor'=>$req->Nomor]);
    return $listData;
  }

  public function submitEditAlamatKirim (Request $req) {
    $listData = DB::connection('SML')->update('update DBALAMATCUST
    set Nama = :nama, Alamat = :alamat, Telp = :telp, Fax = :fax, up = :up
    where kodeCustsupp = :kodeCustSupp and Nomor = :nomor',
    [
    'kodeCustSupp'=>$req->kodeCustSupp, 
    'nama'=>$req->nama,
    'alamat'=>$req->alamat,
    'telp'=>$req->telp,
    'fax'=>$req->fax,
    'nomor'=>$req->nomor,
    'up'=>$req->up

    ]);
    return $listData;
  }
  
  public function loadKota (Request $req) {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');
    return $listData;
  }

    public function loadKotaEdit (Request $req) {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeKota = :kodeKota', ['kodeKota' => $req->kodeKota]);
    return $listData;
  }
  
}
