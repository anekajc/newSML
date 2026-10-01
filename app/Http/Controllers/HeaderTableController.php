<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class HeaderTableController extends Controller


{
  public function saveHeaderTable (Request $req) {
    $username = \Auth::user()->username;
    $res=[];
    if($req->urut){
      $res = DB::connection('SML')->select(
          "select * from  DBHEADERTABLE where username = :username and href = :href and urut = :urut",
          [
              "username" => $username,
              "href" => $req->href,
              "urut" => $req->urut
          ]
      );
    } else {
      $res = DB::connection('SML')->select(
          "select * from  DBHEADERTABLE where username = :username and href = :href ",
          [
              "username" => $username,
              "href" => $req->href
          ]
      );
    }
    // $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    if($req->urut){
      if ($res) {

      $res = DB::connection('SML')->update(
          "UPDATE DBHEADERTABLE SET header = :header , tipe = :tipe , isnumber = :isnumber , value = :value , isshown = :isshown  where username = :username and href = :href and urut = :urut",
          [
            "header" => $req->header ,
             "tipe" => $req->tipe ,
              "isnumber" => $req->isnumber ,
               "value" => $req->value ,
                "isshown" => $req->isshown ,
              "username" => \Auth::user()->username,

              "href" => $req->href,
              "urut" => $req->urut

          ]
      );
    } else {
      $res = DB::connection('SML')->statement(
          "INSERT INTO DBHEADERTABLE (username, urut , href, header, tipe, isshown , value, isnumber)
  VALUES (:username , :urut , :href , :header , :tipe , :isshown , :value , :isnumber);",
          [
            "header" => $req->header ,
            "urut" => $req->urut,
             "tipe" => $req->tipe ,
              "isnumber" => $req->isnumber ,
               "value" => $req->value ,
                "isshown" => $req->isshown ,
              "username" => \Auth::user()->username,

              "href" => $req->href
          ]
      );

    }


    } else {
      if ($res) {

      $res = DB::connection('SML')->update(
          "UPDATE DBHEADERTABLE SET header = :header , tipe = :tipe , isnumber = :isnumber , value = :value , isshown = :isshown  where username = :username and href = :href ",
          [
            "header" => $req->header ,
             "tipe" => $req->tipe ,
              "isnumber" => $req->isnumber ,
               "value" => $req->value ,
                "isshown" => $req->isshown ,
              "username" => \Auth::user()->username,

              "href" => $req->href

          ]
      );
    } else {
      $res = DB::connection('SML')->statement(
          "INSERT INTO DBHEADERTABLE (username, href, header, tipe, isshown , value, isnumber)
  VALUES (:username , :href , :header , :tipe , :isshown , :value , :isnumber);",
          [
            "header" => $req->header ,
             "tipe" => $req->tipe ,
              "isnumber" => $req->isnumber ,
               "value" => $req->value ,
                "isshown" => $req->isshown ,
              "username" => \Auth::user()->username,

              "href" => $req->href
          ]
      );

    }

    }

  return 1;

  }

  public function saveSetHeaderTable (Request $req) {
    // $username = \Auth::user()->username;

    // $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->urut) {
      $res = DB::connection('SML')->select(
          "select * from  DBHEADERTABLEALIAS where href = :href and urut = :urut",
          [
              "href" => $req->href,
              "urut" => $req->urut
          ]
      );
    } else {
      $res = DB::connection('SML')->select(
          "select * from  DBHEADERTABLEALIAS where href = :href ",
          [
              "href" => $req->href
          ]
      );

    }

    if ($req->urut) {
      if ($res) {

      $res = DB::connection('SML')->update(
          "UPDATE DBHEADERTABLEALIAS SET  value = :value   where  href = :href and urut = :urut",
          [
            "value" => $req->value ,
             "href" => $req->href,

              "urut" => $req->urut
          ]
      );
    } else {
      $res = DB::connection('SML')->statement(
          "INSERT INTO DBHEADERTABLEALIAS (value, href , urut)
  VALUES (:value , :href , :urut );",
  [
    "value" => $req->value ,
     "href" => $req->href,

      "urut" => $req->urut
  ]
      );

    }
    } else {
      if ($res) {

      $res = DB::connection('SML')->update(
          "UPDATE DBHEADERTABLEALIAS SET  value = :value   where  href = :href ",
          [
            "value" => $req->value ,
             "href" => $req->href
          ]
      );
    } else {
      $res = DB::connection('SML')->statement(
          "INSERT INTO DBHEADERTABLEALIAS (value, href)
  VALUES (:value , :href );",
  [
    "value" => $req->value ,
     "href" => $req->href
  ]
      );

    }

    }


  return 1;

  }




  public function getHeaderTable (Request $req) {
    // $bobIndex = array_find_key($users, fn($user) => $user->name === 'Bob');
    $isnumberheadertable = [];
    $headertablevalue = [];
    $headertableheader = [];
    $headerisshown = [];
    $username = \Auth::user()->username;
    $statusset = 0;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $aliasOrdered = [];
    $isparsed = 0;
    $headertablealias = [];

    $menuMoreThanOne = ['purchaseorder', 'pononstock', 'newpo', 'newpojasa', 'invoicepembelian', 'perintahreturbeli', 'returpembeliangudang'];
    $desimal = [];
    $desimal2 = [];
    $aliasOrdered2 = [];
    $headertablealias2 = [];
    $isparsed2 = [];
    $headertableheader2 = [];
    $isnumberheadertable2 = [];
    $headertablevalue2 = [];
    $headerisshown2 = [];

    // urut 3 = tab "Outstanding SO" di halaman Purchase Order.
    $desimal3 = [];
    $aliasOrdered3 = [];
    $headertablealias3 = [];
    $isparsed3 = [];
    $headertableheader3 = [];
    $isnumberheadertable3 = [];
    $headertablevalue3 = [];
    $headerisshown3 = [];

    $xxx= [];
    $isMoreThanOne = 0;
    if (in_array($req->href , $menuMoreThanOne)) {
      $isMoreThanOne = 1;
    } else {
      $isMoreThanOne = 0;
    }

    // Tombol "Reset kolom": hapus pengaturan tersimpan milik user untuk tabel ini,
    // supaya sisa method di bawah jatuh ke cabang "belum ada konfigurasi" dan
    // menurunkan lagi kolom default dari datanya. Aditif - pemanggilan lama yang
    // tidak mengirim reset sama sekali tidak berubah.
    //
    // Halaman dengan lebih dari satu tabel (mis. purchaseorder) mengirim `urut`, dan
    // konfigurasinya memang disimpan per urut. Halaman dengan satu tabel saja (mis.
    // pembelianpermintaannonagen) tidak pernah mengirim `urut`, dan konfigurasinya
    // disimpan tanpa urut juga - reset di halaman itu perlu cabang terpisah.
    if ($req->reset) {
      if ($req->urut) {
        DB::connection("SML")->update("delete from DBHEADERTABLE where username = :username and href = :href and urut = :urut" , ["username" => $username , "href" => $req->href , "urut" => $req->urut ]);
      } else {
        DB::connection("SML")->update("delete from DBHEADERTABLE where username = :username and href = :href" , ["username" => $username , "href" => $req->href ]);
      }
    }

    if ($isMoreThanOne ) {
      $xxx = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS  where  href = :href and urut = :urut"  , ["href" => $req->href ,"urut" => 1 ]);
      if ($xxx ) {
        $headertablealias = json_decode($xxx[0]->value);
      } else {
        $headertablealias = [];
      }

      $xxx2 = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS  where  href = :href and urut = :urut"  , ["href" => $req->href ,"urut" => 2 ]);
      if ($xxx2 ) {
        $headertablealias2 = json_decode($xxx2[0]->value);
      } else {
        $headertablealias2 = [];
      }

      $xxx3 = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS  where  href = :href and urut = :urut"  , ["href" => $req->href ,"urut" => 3 ]);
      if ($xxx3 ) {
        $headertablealias3 = json_decode($xxx3[0]->value);
      } else {
        $headertablealias3 = [];
      }
    } else {
      $xxx = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS  where  href = :href "  , ["href" => $req->href ]);
      if ($xxx ) {
        $headertablealias = json_decode($xxx[0]->value);
      } else {
        $headertablealias = [];
      }
    }

    // pr non agen
    if ($req->href == 'pembelianpermintaannonagen') {
      $statusset = 1;
      $otorisasi = DB::connection("SML")->select("select NoBukti , Tanggal  , IsOtorisasi1, TglOto1, OtoUser1  from vwppl where  bulan = :bulan and tahun = :tahun and IsJasa = 0 and pAgen = 0 "  , ["bulan" => $periode->bulan , "tahun" => $periode->tahun ]);
      // $isparsed = 0;
      $otorisasi = collect($otorisasi)->groupBy('NoBukti');
      $tempOtorisasi = [];
      foreach ($otorisasi as $groupedData) {
        $tempOtorisasi[] = $groupedData;
      }
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        if(!$tempOtorisasi) {
        } else {
          $isparsed = 1;
          foreach ($tempOtorisasi[0][0] as $key => $value) {
            if (str_contains($key, "Oto")) {
            } else {
            array_push($headertablevalue, $key);
            array_push($headertableheader, $key);
            array_push($headerisshown, 1);

              // if (strtotime($value)) {
              //
              //       array_push( $isnumberheadertable , 2);
              // } else if (is_numeric($value)) {
              //     array_push( $isnumberheadertable , 1);
              // } else {
              //
              //       array_push($isnumberheadertable,0);
              // }

            $date = \DateTime::createFromFormat('Y-m-d', trim((string) $value));

          if ($date !== false && $date->format('Y-m-d') === trim((string) $value)) {
            $isnumberheadertable[] = 2;
          } elseif (is_numeric($value)) {
            $isnumberheadertable[] = 1;
          } else {
            $isnumberheadertable[] = 0;
          }
        }
      }
    }
  }
  $aliasOrdered = [];
  if ($headertablealias) {
    foreach ($headertablevalue as $header) {
        foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

    }
    // PR AGEN
    else if ($req->href == 'pembelianpermintaanagen') {
      $statusset = 1;
      $otorisasi = DB::connection("SML")->select("select NoBukti , Tanggal  , IsOtorisasi1, TglOto1, OtoUser1  from vwppl where  bulan = :bulan and tahun = :tahun and IsJasa = 0 and pAgen = 1"  , ["bulan" => $periode->bulan , "tahun" => $periode->tahun ]);
      // $isparsed = 0;
      $otorisasi = collect($otorisasi)->groupBy('NoBukti');
      $tempOtorisasi = [];
      foreach ($otorisasi as $groupedData) {
        $tempOtorisasi[] = $groupedData;
      }
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        // $headertable = [];

        if(!$tempOtorisasi) {
        } else {
          $isparsed = 1;
          foreach ($tempOtorisasi[0][0] as $key => $value) {
            if (str_contains($key, "Oto")) {
            } else {
            array_push($headertablevalue, $key);
            array_push($headertableheader, $key);
            array_push($headerisshown, 1);

              // if (strtotime($value)) {
              //
              //       array_push( $isnumberheadertable , 2);
              // } else if (is_numeric($value)) {
              //     array_push( $isnumberheadertable , 1);
              // } else {
              //
              //       array_push($isnumberheadertable,0);
              // }

            $date = \DateTime::createFromFormat('Y-m-d', trim((string) $value));

          if ($date !== false && $date->format('Y-m-d') === trim((string) $value)) {
            $isnumberheadertable[] = 2;
          } elseif (is_numeric($value)) {
            $isnumberheadertable[] = 1;
          } else {
            $isnumberheadertable[] = 0;
          }
        }
      }
    }
  }
  $aliasOrdered = [];
  if ($headertablealias) {
    foreach ($headertablevalue as $header) {
        foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

  }
    // PR NON STOCK
    else if ($req->href == 'pembelianpermintaannonstock') {
      $statusset = 1;
      $otorisasi = DB::connection("SML")->select("select NoBukti , Tanggal  , IsOtorisasi1, TglOto1, OtoUser1  from vwppl where  bulan = :bulan and tahun = :tahun and IsJasa = 1 and pAgen = 0"  , ["bulan" => $periode->bulan , "tahun" => $periode->tahun ]);
      // $isparsed = 0;
      $otorisasi = collect($otorisasi)->groupBy('NoBukti');
      $tempOtorisasi = [];
      foreach ($otorisasi as $groupedData) {
        $tempOtorisasi[] = $groupedData;
      }
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        // $headertable = [];

        if(!$tempOtorisasi) {
        } else {
          $isparsed = 1;
          foreach ($tempOtorisasi[0][0] as $key => $value) {
            if (str_contains($key, "Oto")) {
            } else {
            array_push($headertablevalue, $key);
            array_push($headertableheader, $key);
            array_push($headerisshown, 1);

            $date = \DateTime::createFromFormat('Y-m-d', trim((string) $value));

          if ($date !== false && $date->format('Y-m-d') === trim((string) $value)) {
            $isnumberheadertable[] = 2;
          } elseif (is_numeric($value)) {
            $isnumberheadertable[] = 1;
          } else {
            $isnumberheadertable[] = 0;
          }
        }
      }
    }
  }
  $aliasOrdered = [];
  if ($headertablealias) {
    foreach ($headertablevalue as $header) {
        foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

  }
    // PO
    else if ($req->href == 'purchaseorder') {
      $formats = [
      'Y-m-d',
      'Y-m-d H:i:s',
      'Y-m-d H:i:s.v'];

      $statusset = 1;
      $isparsed = 0;
      // Konfigurasi header milik user diambil lebih dulu. Dua query berat di bawah
      // (vwOutPPL dan union dbPO) hasilnya HANYA dipakai untuk menebak nama & tipe kolom
      // ketika konfigurasi belum ada, jadi tidak perlu dijalankan kalau konfigurasi sudah tersimpan.
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable3 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 3"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      $otorisasi = [];
      if (count($headertable) == 0) {
      $otorisasi = DB::connection("SML")->select("DECLARE @Tahun int, @Bulan int
      SELECT @Tahun=2018, @Bulan=78

      SET NOCOUNT ON
      SELECT TOP 1
      A.Nobukti , A.Tanggal , A.sat , ' ' + CAST(A.kodebrg AS VARCHAR(50)) AS kodebrg  , A.NamaBrg , isnull(A.Qnt , '0.00') Qnt , A.QNTPO , A.SisaPPL , A.Keterangan , A.QntoutSO , A.QntStock
      from DBO.vwOutPPL A WITH(NOLOCK)
      where A.SisaPPL>0
      and A.pjasa=0
      order by A.Tanggal, A.NoBukti, A.Urut"  );
      }

      $otorisasi2 = [];
      if (count($headertable2) == 0) {
      $otorisasi2 = DB::connection("SML")->select("declare @Tahun int, @Bulan int  ,@pJasa Bit

      select @Tahun= :tahun , @Bulan= :bulan
      Select  a.NoBukti, CONVERT(varchar(10), a.Tanggal, 23) AS Tanggal,a.KodeSupp, b.NamaCustSupp,  b.FakturSupp,
        TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        NOSO,NOPOCUST ,CONVERT(varchar(10), A.tglKirim, 23) AS tglKirim
        From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
        where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
        and  /*Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
        Case when A.IsOtorisasi2=1 then 1 else 0 end+
        Case when A.IsOtorisasi3=1 then 1 else 0 end+
        Case when A.IsOtorisasi4=1 then 1 else 0 end+
        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
      else 1
      end As Bit)=1  */    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
        Case when A.IsOtorisasi2=1 then 1 else 0 end+
        Case when A.IsOtorisasi3=1 then 1 else 0 end+
        Case when A.IsOtorisasi4=1 then 1 else 0 end+
        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
      else 1
      end As Bit)= 1  AND TotTotalRp>=200000000
      and B.pJasa= 0
      UNION ALL

      Select  a.NoBukti,    CONVERT(varchar(10), a.Tanggal, 23) AS Tanggal,a.KodeSupp, b.NamaCustSupp, b.FakturSupp,
        TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,

      NOSO,NOPOCUST ,CONVERT(varchar(10), A.tglKirim, 23) AS tglKirim
      From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
      where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
      and  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
        Case when A.IsOtorisasi2=1 then 1 else 0 end+
        Case when A.IsOtorisasi3=1 then 1 else 0 end+
        Case when A.IsOtorisasi4=1 then 1 else 0 end+
        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
      else 1
      end As Bit)=1       AND TotTotalRp < 200000000
      and B.pJasa= 0

      order by NoBukti"  , ["bulan" => 5 , "tahun" => 2026 ]);
      }

      // Tabel urut 3 = tab "Outstanding SO". Query-nya diambil dari POController supaya
      // nama kolom di sini tidak pernah berbeda dengan data yang nanti dikirim
      // dataOutstandingSO(). TOP 1 karena satu baris contoh sudah cukup untuk menurunkan
      // nama & tipe kolom, dan seperti dua tabel di atas query ini hanya jalan kalau
      // user memang belum punya konfigurasi tersimpan.
      $otorisasi3 = [];
      if (count($headertable3) == 0) {
        $sqlSO = \App\Http\Controllers\Purchasing\POController::sqlOutstandingSO();
        $otorisasi3 = DB::connection("SML")->select("
          SET NOCOUNT ON
          select top 1 A.* from ( $sqlSO ) A
        ");
      }

      // @dd(count($headertable2));
      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        // $headertable = [];
        if(!$otorisasi2) {
        } else {
          $isparsed2 = 1;
          foreach ($otorisasi2[0] as $key => $value) {
            if (str_contains($key, "Oto")) {
            } else {
            array_push($headertablevalue2, $key);
            array_push($headertableheader2, $key);
            array_push($headerisshown2, 1);
            $isDate = false;
            $checkValue = (string) $value;

            foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $checkValue);

          if ($date !== false && $date->format($format) === $checkValue) {
              $isDate = true;
              break;
          }
        }

// if ($date !== false && $date->format('Y-m-d') === trim((string) $value)) {

          if ($isDate) {
            $isnumberheadertable2[] = 2;
          } else if ($key == 'kodebrg') {
            $isnumberheadertable2[] = 0;
          }

          elseif (is_numeric($value)) {
            $isnumberheadertable2[] = 1;
          } else {
            $isnumberheadertable2[] = 0;
          }
        }
      }
    }
  }

    if (count($headertable3) > 0) {
      $isnumberheadertable3 = json_decode($headertable3[0]->isnumber);
      $headertablevalue3 = json_decode($headertable3[0]->value);
      $headertableheader3 = json_decode($headertable3[0]->header);
      $headerisshown3 = json_decode($headertable3[0]->isshown);
      $isparsed3 = 0;
    } else {
      if (!$otorisasi3) {
      } else {
        // Kolom teks yang isinya bisa saja terbaca sebagai angka (kode barang / part
        // number berupa digit semua, no. bukti tertentu). Tanpa daftar ini kolomnya
        // ikut jadi kolom angka: rata kanan dan diberi pemisah ribuan. Perlakuan yang
        // sama dipakai tabel urut 1 & 2 lewat pengecualian khusus 'kodebrg' di atas.
        $kolomTeks3 = ['kodebrg', 'namabrg', 'nobukti', 'sat', 'partnumber'];
        $isparsed3 = 1;
        foreach ($otorisasi3[0] as $key => $value) {
          array_push($headertablevalue3, $key);
          array_push($headertableheader3, $key);
          array_push($headerisshown3, 1);

          $isDate = false;
          $checkValue = (string) $value;
          foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $checkValue);
            if ($date !== false && $date->format($format) === $checkValue) {
              $isDate = true;
              break;
            }
          }

          if ($isDate) {
            $isnumberheadertable3[] = 2;
          } else if (in_array(strtolower($key), $kolomTeks3, true)) {
            $isnumberheadertable3[] = 0;
          } elseif (is_numeric($value)) {
            $isnumberheadertable3[] = 1;
          } else {
            $isnumberheadertable3[] = 0;
          }
        }
      }
    }

    if (count($headertable) > 0) {
      $isnumberheadertable = json_decode($headertable[0]->isnumber);
      $headertablevalue = json_decode($headertable[0]->value);
      $headertableheader = json_decode($headertable[0]->header);
      $headerisshown = json_decode($headertable[0]->isshown);
      $isparsed = 0;
    } else {
        // $headertable = [];
    if(!$otorisasi) {
      } else {
        $isparsed = 1;
        foreach ($otorisasi[0] as $key => $value) {
          if (str_contains($key, "Oto")) {
          } else {
            array_push($headertablevalue, $key);
            // Label default kolom SisaPPL diganti jadi "SisaPR" - nama field data
            // (dipakai untuk mengambil nilainya) tetap SisaPPL, hanya judul tampilannya
            // yang berubah. Hanya berlaku untuk user yang belum punya konfigurasi
            // kolom tersimpan sendiri (lihat dbheadertable di atas).
            array_push($headertableheader, $key === 'SisaPPL' ? 'SisaPR' : $key);
            array_push($headerisshown, 1);
              // if (strtotime($value)) {
              //
              //       array_push( $isnumberheadertable , 2);
              // } else if (is_numeric($value)) {
              //     array_push( $isnumberheadertable , 1);
              // } else {
              //
              //       array_push($isnumberheadertable,0);
              // }
              // $date = \DateTime::createFromFormat('Y-m-d', trim((string) $value));

            $isDate = false;
            $checkValue = (string) $value;
            foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $checkValue);

            if ($date !== false && $date->format($format) === $checkValue) {
              $isDate2 = true;
              break;
            }
          }

            if ($isDate) {
              $isnumberheadertable[] = 2;
            } else if ($key == 'kodebrg') {
              $isnumberheadertable[] = 0;
            } elseif (is_numeric($value)) {
              $isnumberheadertable[] = 1;
            } else {
              $isnumberheadertable[] = 0;
            }
          }
        }
      }
    }

    $aliasOrdered = [];
    if ($headertablealias) {
      foreach ($headertablevalue as $header) {
        foreach ($headertablealias as $item) {
          if ($item->value === $header) {
            array_push( $aliasOrdered , $item);
            break;
          }
        }
      }
    } else {
      foreach ($headertablevalue as $header) {
        array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
      }
    }

    $aliasOrdered2 = [];
    if ($headertablealias2) {
      foreach ($headertablevalue2 as $header) {
        foreach ($headertablealias2 as $item) {
          if ($item->value === $header) {
            array_push( $aliasOrdered2 , $item);
            break;
          }
        }
      }
    } else {
      foreach ($headertablevalue2 as $header) {
        array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
      }
    }

    $aliasOrdered3 = [];
    if ($headertablealias3) {
      foreach ($headertablevalue3 as $header) {
        foreach ($headertablealias3 as $item) {
          if ($item->value === $header) {
            array_push( $aliasOrdered3 , $item);
            break;
          }
        }
      }
    } else {
      foreach ($headertablevalue3 as $header) {
        array_push( $aliasOrdered3 , ["value" => $header , "alias" => $header]);
      }

    }

    $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
    $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    $desimal3 = $this->desimalHeaderTable($headertable3 , $isnumberheadertable3);

    // desimalHeaderTable() memberi 2 desimal untuk SEMUA kolom angka, sehingga nomor
    // urut & kode satuan tampil sebagai "2.00". Hanya berlaku sebagai nilai awal:
    // begitu user mengatur sendiri desimalnya lewat menu roda gigi, konfigurasi
    // tersimpan itu yang dipakai dan blok ini tidak lagi ikut campur.
    if (count($headertable3) == 0) {
      $kolomBulat3 = ['urut', 'nosat', 'tolerate'];
      foreach ($headertablevalue3 as $i => $namaKolom) {
        if (in_array(strtolower($namaKolom), $kolomBulat3, true)) {
          $desimal3[$i] = 0;
        }
      }
    }
  }
    // Uang Muka Beli
    else if ($req->href == 'uangmukabeli') {
      $statusset = 1;
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Kolom default DITULIS EKSPLISIT, tidak lagi diturunkan dengan mengintip satu baris
        // data ("select top 1"): kalau periode berjalan belum punya UMB sama sekali, sample
        // row-nya kosong dan daftar kolom ikut kosong - tabel cuma menyisakan
        // Actions/Oto/User Oto/Tgl Oto. Sama seperti cabang 'pononstock' di bawah.
        //
        // Nama field HARUS sama persis dengan alias kolom di UangMukaBeliController@loadAll
        // (spasi dan besar-kecil huruf ikut dihitung) - alias itu juga dipakai sebagai nama
        // field data di JS (lihat umbBuatCart()/umbRenderNilai() di uangmukabeli.blade.php).
        // field => tipe (0 varchar, 1 float, 2 date)
        $default = [
          'No Bukti' => 0,
          'Tanggal'  => 2,
          'No PO'    => 0,
          'Supplier' => 0,
          'Valas'    => 0,
          'DPP'      => 1,
          'PPN'      => 1,
          'Persen'   => 1,
          'Tgl Est'  => 2,
          'Bayar'    => 0,
        ];
        foreach ($default as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal = $this->desimalHeaderTable($headertable , $isnumberheadertable);
    }
    // Debet Note
    else if ($req->href == 'pembelianpermintaandebetnote') {
      $statusset = 1;
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Sama seperti cabang 'uangmukabeli' - kolom default DITULIS EKSPLISIT supaya tabel
        // tetap punya header meski periode berjalan belum punya data Debet Note sama sekali.
        // Nama field HARUS sama persis dengan alias kolom di
        // PembelianPermintaanDebetNoteController@loadAll (dipakai juga sebagai nama field data
        // di JS, lihat dnBuatCart()/dnRenderNilai() di pembelianpermintaandebetnote.blade.php).
        // IsOtorisasi1/OtoUser1/TglOto1 SENGAJA tidak dimasukkan - itu kolom teknis untuk filter
        // otorisasi & kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS.
        // field => tipe (0 varchar, 1 float, 2 date)
        $default = [
          'No Bukti'  => 0,
          'Tanggal'   => 2,
          'Kode Supp' => 0,
          'Supplier'  => 0,
          'Nilai DN'  => 1,
        ];
        foreach ($default as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal = $this->desimalHeaderTable($headertable , $isnumberheadertable);
    }
    // Invoice Retur Beli
    else if ($req->href == 'invoicereturbeli') {
      $statusset = 1;
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Sama seperti cabang 'uangmukabeli' - kolom default DITULIS EKSPLISIT supaya
        // tabel tetap punya header meski periode berjalan belum punya data sama sekali.
        // Nama field HARUS sama persis dengan alias kolom di
        // InvoiceReturBeliController@loadAll (dipakai juga sebagai nama field data di JS,
        // lihat irbBuatCart()/irbRenderNilai() di invoicereturbeli.blade.php). IsOtorisasi1/
        // OtoUser1/TglOto1 SENGAJA tidak dimasukkan - itu kolom teknis untuk filter otorisasi
        // & kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS, bukan kolom yang
        // bisa digeser/disembunyikan pengguna.
        // field => tipe (0 varchar, 1 float, 2 date)
        $default = [
          'NoBukti'     => 0,
          'Tanggal'     => 2,
          'KodeSupp'    => 0,
          'NamaCustSupp'=> 0,
          'NoBeli'      => 0,
          'TotDPPRp'    => 1,
          'TotPPNRp'    => 1,
          'TotNetRp'    => 1,
        ];
        foreach ($default as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal = $this->desimalHeaderTable($headertable , $isnumberheadertable);
    }
    // Penerimaan ACC (gabungan Acc Tunai/Kredit + Jasa Acc Tunai/Kredit)
    else if ($req->href == 'newpobeliacc') {
      $statusset = 1;
      $isparsed = 0;
      $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Sama seperti cabang 'uangmukabeli' - kolom default DITULIS EKSPLISIT supaya
        // tabel tetap punya header meski periode berjalan belum punya data sama sekali.
        // Nama field HARUS sama persis dengan alias kolom di NewPOBeliAccController@loadAll.
        $default = [
          'Jenis'         => 0,
          'No Bukti'      => 0,
          'Tanggal'       => 2,
          'Nama Supplier' => 0,
          'Keterangan'    => 0,
          'No PO'         => 0,
          'Gudang'        => 0,
          'Faktur Supp'   => 0,
          'DPP'           => 1,
          'PPN'           => 1,
          'Total'         => 1,
        ];
        foreach ($default as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal = $this->desimalHeaderTable($headertable , $isnumberheadertable);
    }
    // PO Non Stock
    else if ($req->href == 'pononstock') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;
      // Sama seperti cabang 'purchaseorder' di atas, hanya urut 1 (Outstanding PR) & urut 2
      // (Purchase Order) - PO Non Stock tidak punya tab Outstanding SO (urut 3 dibiarkan
      // kosong, sudah diinisialisasi di awal method). Bedanya cuma filter pjasa/pJasa = 1
      // (non stock); cabang 'purchaseorder' memakai 0 (stock).
      //
      // Kolom default DULU diturunkan dengan mengintip satu baris data (query "TOP 1" /
      // otorisasi bulan berjalan) - kalau periode/bulan itu kebetulan tidak punya baris
      // yang cocok (mis. semua PO non stock bulan ini sudah diotorisasi), daftar kolom
      // jadi kosong dan header tabel tidak tergambar sama sekali (tidak ada yang bisa
      // diseret). Sekarang daftarnya ditulis eksplisit di bawah - field-nya harus sama
      // persis dengan field yang dikembalikan dataOutstandingPR() / loadPurchaseOrder()
      // (PONonStockController.php).
      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'Nobukti' => 0, 'Tanggal' => 2, 'kodebrg' => 0, 'NamaBrg' => 0, 'sat' => 0,
          'Qnt' => 1, 'QNTPO' => 1, 'SisaPPL' => 1, 'Keterangan' => 0,
          'QntoutSO' => 1, 'QntStock' => 1,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          // Sama seperti cabang 'purchaseorder' di atas: label SisaPPL diganti "SisaPR",
          // field data tetap SisaPPL. Tidak berpengaruh ke $default1 milik href lain
          // karena field itu memang cuma ada di daftar kolom pononstock.
          array_push($headertableheader, $key === 'SisaPPL' ? 'SisaPR' : $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        $isparsed2 = 1;
        // NOSO/NOPOCUST sengaja tidak ikut - kedua field itu sudah dibuang dari form
        // PO Non Stock, jadi tidak perlu jadi kolom tabel juga.
        $default2 = [
          'NoBukti' => 0, 'Tanggal' => 2, 'KodeSupp' => 0, 'NamaCustSupp' => 0,
          'FakturSupp' => 0, 'TotDPPRp' => 1, 'TotPPNRp' => 1, 'TotNetRp' => 1,
          'tglKirim' => 2,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      // Blok ini sebelumnya HANYA ada di cabang 'purchaseorder', padahal halaman ini juga
      // memakainya: tanpa ini getHeaderTable() mengembalikan aliasordered/aliasordered2 dan
      // desimal/desimal2 kosong, sehingga nama kolom hasil rename dan jumlah desimal yang
      // sudah user simpan lewat menu roda gigi tidak pernah dipakai di PO Non Stock.
      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Penerimaan Gudang (newpo). urut 1 = tab "Outstanding PO", urut 2 = tab
    // "Penerimaan Barang" - sama pola dengan cabang 'pononstock' di atas. Daftar
    // kolom default DITULIS EKSPLISIT supaya seluruh kolom tampil sejak kunjungan
    // pertama, tanpa perlu menyiapkan baris apa pun di DBHEADERTABLE/DBHEADERTABLEALIAS.
    else if ($req->href == 'newpo') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;

      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Nama field HARUS sama persis dengan field data di JS setelah item[0] diratakan
        // (lihat npoBuatCart() di newpo.blade.php). field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'NoBukti' => 0, 'TANGGAL' => 2, 'NAMACUSTSUPP' => 0, 'NAMAGDG' => 0, 'NAMAEXP' => 0,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          // Sama seperti cabang 'purchaseorder' di atas: label SisaPPL diganti "SisaPR",
          // field data tetap SisaPPL. Tidak berpengaruh ke $default1 milik href lain
          // karena field itu memang cuma ada di daftar kolom pononstock.
          array_push($headertableheader, $key === 'SisaPPL' ? 'SisaPR' : $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        $isparsed2 = 1;
        // Field dari dbo.fnc_masterbeli (lihat NewPOController@getAllPembelian).
        $default2 = [
          'NoBukti' => 0, 'TANGGAL' => 2, 'NAMACUSTSUPP' => 0, 'NoPO' => 0,
          'NAMAGUDANG' => 0, 'FAKTURSUPP' => 0,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Penerimaan Non Stock (newpojasa). urut 1 = tab "Outstanding PO Non Stock", urut 2 =
    // tab "Penerimaan Non Stock" - sama pola persis dengan cabang 'newpo' di atas. Daftar
    // kolom default DITULIS EKSPLISIT supaya seluruh kolom tampil sejak kunjungan pertama,
    // tanpa perlu menyiapkan baris apa pun di DBHEADERTABLE/DBHEADERTABLEALIAS.
    else if ($req->href == 'newpojasa') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;

      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Nama field HARUS sama persis dengan field data di JS setelah item[0] diratakan
        // (lihat npoBuatCart() di newpojasa.blade.php). field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'NoBukti' => 0, 'TANGGAL' => 2, 'NAMACUSTSUPP' => 0, 'NAMAGDG' => 0, 'NAMAEXP' => 0,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          // Sama seperti cabang 'purchaseorder' di atas: label SisaPPL diganti "SisaPR",
          // field data tetap SisaPPL. Tidak berpengaruh ke $default1 milik href lain
          // karena field itu memang cuma ada di daftar kolom pononstock.
          array_push($headertableheader, $key === 'SisaPPL' ? 'SisaPR' : $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        $isparsed2 = 1;
        // NewPOJasaController@getAllPembelian sekarang memakai dbo.fnc_masterbeli sama seperti
        // cabang 'newpo' (beda hanya pjasa=1), jadi daftar field defaultnya identik.
        $default2 = [
          'NoBukti' => 0, 'TANGGAL' => 2, 'NAMACUSTSUPP' => 0, 'NoPO' => 0,
          'NAMAGUDANG' => 0, 'FAKTURSUPP' => 0,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Invoice Pembelian (invoicepembelian). urut 1 = tab "Outstanding Pembelian" (dari
    // vwBrowsOutBeli, lihat InvoicePembelianController@getAllPembelian), urut 2 = tab
    // "Transaksi Kelengkapan Dokumen" (dari vwTransInvoice, lihat
    // InvoicePembelianController@getAllPO) - sama pola dengan cabang 'newpo' di atas.
    else if ($req->href == 'invoicepembelian') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;

      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Nama field HARUS sama persis dengan field data di JS (lihat ipbBuatCart() di
        // invoicepembelian.blade.php). field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'NoBukti' => 0, 'TANGGAL' => 2, 'KODECUSTSUPP' => 0, 'NAMACUSTSUPP' => 0, 'NoPO' => 0,
          'TNDPP' => 1, 'TNPPN' => 1, 'TSUBTOTAL' => 1,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          // Sama seperti cabang 'purchaseorder' di atas: label SisaPPL diganti "SisaPR",
          // field data tetap SisaPPL. Tidak berpengaruh ke $default1 milik href lain
          // karena field itu memang cuma ada di daftar kolom pononstock.
          array_push($headertableheader, $key === 'SisaPPL' ? 'SisaPR' : $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        $isparsed2 = 1;
        // Field dari vwTransInvoice (lihat InvoicePembelianController@getAllPO).
        $default2 = [
          'NoBukti' => 0, 'Tanggal' => 2, 'KODECUSTSUPP' => 0, 'NamaCustSupp' => 0, 'NoPO' => 0,
          'TotDPPRp' => 1, 'TotPPNRp' => 1, 'TotNetRp' => 1, 'OtoUser1' => 0, 'TglOto1' => 0,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Retur Pembelian Gudang (returpembeliangudang). urut 1 = tab "Outstanding Retur Beli"
    // (dari DBPRRBELIDET, lihat ReturPembelianGudangController@loadAll - $outstandingArray),
    // urut 2 = tab "Retur Beli" (dari dbRBeli, $penerimaanArray) - sama pola dengan cabang
    // 'newpo' di atas. BUKAN filter otorisasi: kedua tab isinya data berbeda (outstanding
    // vs transaksi retur yang sudah jadi), jadi tidak digabung jadi satu tabel seperti
    // 'perintahreturbeli'/'uangmukabeli'. Daftar kolom default DITULIS EKSPLISIT supaya
    // seluruh kolom tampil sejak kunjungan pertama.
    else if ($req->href == 'returpembeliangudang') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;

      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Nama field HARUS sama persis dengan field data di JS setelah item[0] diratakan
        // (lihat rpgBuatCart() di returpembeliangudang.blade.php - field berasal dari query
        // DBPRRBELIDET/DBPRRBELI/DBCUSTSUPP, tanpa alias AS jadi ikut nama kolom aslinya).
        // field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'NOBUKTI' => 0, 'TANGGAL' => 2, 'NAMACUSTSUPP' => 0,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;
      } else {
        $isparsed2 = 1;
        // Field dari dbRBeli/dbCustSupp (lihat ReturPembelianGudangController@loadAll -
        // $penerimaanArray), nama kolom ikut casing aslinya (tanpa alias AS).
        $default2 = [
          'NoBukti' => 0, 'Tanggal' => 2, 'NamaCustSupp' => 0,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Perintah Retur Beli (perintahreturbeli). urut 1 = tabel PRB gabungan (dulu tab
    // "Perintah Retur Beli" + "Sudah Otorisasi", sekarang satu tabel dengan filter
    // otorisasi lewat modal, sama pola dengan 'uangmukabeli'), urut 2 = tabel "List Retur
    // Jual" (dari VwOUtPRJUALPRBELI, lihat PerintahReturBeliController@loadAll) - sama pola
    // dengan cabang 'newpo' di atas. Daftar kolom default DITULIS EKSPLISIT supaya seluruh
    // kolom tampil sejak kunjungan pertama. IsOtorisasi1/OtoUser1/TglOto1 SENGAJA tidak
    // dimasukkan ke default urut 1 - itu kolom teknis untuk filter otorisasi & kolom
    // Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS, sama seperti 'invoicereturbeli'.
    else if ($req->href == 'perintahreturbeli') {
      $statusset = 1;
      $isparsed = 0;
      $isparsed2 = 0;

      $headertable  = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 1"  , ["username" => \Auth::user()->username , "href" => $req->href ]);
      $headertable2 = DB::connection("SML")->select("select *  from dbheadertable where  href= :href  and username = :username and urut = 2"  , ["username" => \Auth::user()->username , "href" => $req->href ]);

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      } else {
        $isparsed = 1;
        // Nama field HARUS sama persis dengan alias kolom di
        // PerintahReturBeliController@loadAll (dipakai juga sebagai nama field data di JS,
        // lihat prbBuatCart()/prbRenderNilai() di perintahreturbeli.blade.php).
        // field => tipe (0 varchar, 1 float, 2 date)
        $default1 = [
          'NoBukti' => 0, 'Tanggal' => 2,
        ];
        foreach ($default1 as $key => $tipe) {
          array_push($headertablevalue, $key);
          array_push($headertableheader, $key);
          array_push($headerisshown, 1);
          $isnumberheadertable[] = $tipe;
        }
      }

      if (count($headertable2) > 0) {
        $isnumberheadertable2 = json_decode($headertable2[0]->isnumber);
        $headertablevalue2 = json_decode($headertable2[0]->value);
        $headertableheader2 = json_decode($headertable2[0]->header);
        $headerisshown2 = json_decode($headertable2[0]->isshown);
        $isparsed2 = 0;

        // Kolom Tanggal ditambahkan belakangan (lihat $default2 di bawah). Susunan kolom
        // yang sudah tersimpan milik user tidak memuatnya, jadi disisipkan di sini supaya
        // tetap muncul tanpa perlu mereset dbheadertable tiap user.
        if (!in_array('Tanggal', $headertablevalue2)) {
          array_splice($headertablevalue2,     1, 0, ['Tanggal']);
          array_splice($headertableheader2,    1, 0, ['Tanggal']);
          array_splice($headerisshown2,        1, 0, [1]);
          array_splice($isnumberheadertable2,  1, 0, [2]);
        }
      } else {
        $isparsed2 = 1;
        // Field dari VwOUtPRJUALPRBELI (lihat PerintahReturBeliController@loadAll).
        $default2 = [
          'NomorRetur' => 0, 'Tanggal' => 2, 'KodeBrg' => 0, 'NamaBrg' => 0, 'Satuan' => 0, 'Qty' => 1,
        ];
        foreach ($default2 as $key => $tipe) {
          array_push($headertablevalue2, $key);
          array_push($headertableheader2, $key);
          array_push($headerisshown2, 1);
          $isnumberheadertable2[] = $tipe;
        }
      }

      $aliasOrdered = [];
      if ($headertablealias) {
        foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue as $header) {
          array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);
        }
      }

      $aliasOrdered2 = [];
      if ($headertablealias2) {
        foreach ($headertablevalue2 as $header) {
          foreach ($headertablealias2 as $item) {
            if ($item->value === $header) {
              array_push( $aliasOrdered2 , $item);
              break;
            }
          }
        }
      } else {
        foreach ($headertablevalue2 as $header) {
          array_push( $aliasOrdered2 , ["value" => $header , "alias" => $header]);
        }
      }

      $desimal  = $this->desimalHeaderTable($headertable  , $isnumberheadertable);
      $desimal2 = $this->desimalHeaderTable($headertable2 , $isnumberheadertable2);
    }
    // Generic fallback for every page ported to the "changeable headers"
    // (window.ReportTable) pattern that isn't one of the hardcoded branches above
    // (returpenjualangudang/perintahreturjual/kreditnote/notareturpenjualan, etc.).
    // Those pages call doSimpanHeader() on first load via saveHeaderTable() (generic,
    // works for any href already), but without this branch getHeaderTable() never
    // read a saved config back for them -- headertableheader stayed permanently
    // empty, so doSetHeader() always fell into its "no saved config" branch and
    // re-applied the page's own default column set on every reload, silently
    // discarding any column reorder/hide/show the user made in a previous visit.
    else if ($req->href) {
      $headertable = $req->urut
        ? DB::connection("SML")->select(
            "select * from DBHEADERTABLE where username = :username and href = :href and urut = :urut",
            ["username" => $username, "href" => $req->href, "urut" => $req->urut]
          )
        : DB::connection("SML")->select(
            "select * from DBHEADERTABLE where username = :username and href = :href",
            ["username" => $username, "href" => $req->href]
          );

      if (count($headertable) > 0) {
        $isnumberheadertable = json_decode($headertable[0]->isnumber);
        $headertablevalue = json_decode($headertable[0]->value);
        $headertableheader = json_decode($headertable[0]->header);
        $headerisshown = json_decode($headertable[0]->isshown);
        $isparsed = 0;
      }
      // Kalau belum ada baris tersimpan, headertableheader dibiarkan kosong --
      // JS halaman (doSetHeader) sudah menangani ini sendiri dengan jatuh ke
      // default cart miliknya lalu langsung menyimpannya lewat doSimpanHeader().

      $desimal = $this->desimalHeaderTable($headertable, $isnumberheadertable);
    }
    return [
      "aliasordered" => $aliasOrdered ,
      "alias" => $headertablealias ,
      "statusset" => $statusset ,
      "isparsed" => $isparsed ,
      "headertableheader" => $headertableheader ,
      "isnumeric" => $isnumberheadertable,
      "headertablevalue" => $headertablevalue,
      "isshown" => $headerisshown,
      "aliasordered2" => $aliasOrdered2 ,
      "alias2" => $headertablealias2 ,
      "isparsed2" => $isparsed2 ,
      "headertableheader2" => $headertableheader2 ,
      "isnumeric2" => $isnumberheadertable2,
      "headertablevalue2" => $headertablevalue2,
      "isshown2" => $headerisshown2,
      "aliasordered3" => $aliasOrdered3 ,
      "alias3" => $headertablealias3 ,
      "isparsed3" => $isparsed3 ,
      "headertableheader3" => $headertableheader3 ,
      "isnumeric3" => $isnumberheadertable3,
      "headertablevalue3" => $headertablevalue3,
      "isshown3" => $headerisshown3,
      "desimal" => $desimal,
      "desimal2" => $desimal2,
      "desimal3" => $desimal3
    ];
    //     $req = new Request([
    //     'href' => 'aaa'
    // ]);
    //
    // $xx = app('App\Http\Controllers\NewMenuController')
    //     ->saveHeaderTable($req);
  }

  /**
   * Jumlah desimal per kolom.
   *
   * DBHEADERTABLE tidak punya kolom khusus untuk ini, jadi nilainya dititipkan
   * di kolom `tipe` dalam bentuk JSON array. Kolom itu memang ditulis oleh
   * saveHeaderTable() tapi tidak pernah dibaca balik, dan semua halaman yang
   * memakai sistem ini selalu mengirimnya sebagai string kosong - jadi aman
   * dipakai tanpa perlu mengubah struktur tabel di SQL Server.
   *
   * Kalau isinya kosong / bukan JSON / panjangnya tidak cocok, kolom yang
   * bersangkutan jatuh ke bawaan: 2 desimal untuk kolom angka, 0 untuk lainnya.
   */
  private function desimalHeaderTable ($headertable , $isnumeric) {
    $tersimpan = [];
    if (count($headertable) > 0) {
      $decoded = json_decode($headertable[0]->tipe);
      if (is_array($decoded)) {
        $tersimpan = $decoded;
      }
    }

    $desimal = [];
    foreach ($isnumeric as $i => $tipe) {
      if (isset($tersimpan[$i]) && is_numeric($tersimpan[$i])) {
        $nilai = (int) $tersimpan[$i];
        if ($nilai < 0) { $nilai = 0; }
        if ($nilai > 4) { $nilai = 4; }
        array_push($desimal , $nilai);
      } else {
        array_push($desimal , ((int) $tipe === 1) ? 2 : 0);
      }
    }

    return $desimal;
  }


}
