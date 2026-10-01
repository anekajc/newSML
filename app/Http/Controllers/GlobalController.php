<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENUWEB;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class GlobalController extends Controller


{
  public function getNoBuktiSimbol (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');

    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        $req->simbol
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?,?',$values);

    return $noBukti;
  }

  public function getNoBukti (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');
    // $inisialx = 'KN';
    // return $inisialx;
    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }



// public function LoggingData ($paktivitas, $psumber, $pnoBukti, $pketerangan,$purut,$ptable) {
//     $username = \Auth::user()->username;
//     $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);

//     // if ($paktivitas=='I') {
//     //       $purut=1;
//     //     }else {$purut=$purut;
//     //     }
//     // @dd($z);
//     // @dd($pketx);


//     // $z =DB::connection("SML")->update("exec SP_KetLogfile 'SML/KRS/00001/1025','DBKOREKSIDET',CAST(1 AS INT), '','SA' " , []);

//     if ($paktivitas != 'btloto' and $paktivitas != 'oto')  {

//        $z =DB::connection("SML")->statement("exec SP_KetLogfileMy :nobukti,:table,:urut, '',:pemakai " ,
//         ["nobukti" => $pnoBukti,"table" => $ptable,"urut"=>$purut,"pemakai"=>$username]);


//        $pketx = DB::connection("SML")->select("select * from tempketlog where iduser=:pemakai and keterangan<>'' ",["pemakai"=>$username] );
//          DB::connection("SML")->update("delete tempketlog  where iduser=:pemakai",["pemakai"=>$username] );
//          DB::connection("SML")->update("delete xMytable  where iduser=:pemakai",["pemakai"=>$username] );

//           $pketerangan = $pketx[0]->keterangan;

//     } else {
//             $pketerangan = $pketerangan;
//         }



//     $values = [
//       $periode[0]->tahun,
//       $periode[0]->bulan,
//       $username,
//       $paktivitas,
//       $psumber,
//       $pnoBukti,
//       $pketerangan
//     ];

//     DB::connection('SML')->statement('Insert into dbLogFile (Tahun, Bulan, Tanggal,Pemakai,Aktivitas,Sumber,NoBukti,Keterangan)
//                         values (?,?,Getdate(),?,?,?,?,?)', $values);

//   }



  public function LoggingData ($paktivitas, $psumber, $pnoBukti, $pketerangan,$purut,$ptable) {
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);

    // if ($paktivitas=='I') {
    //       $purut=1;
    //     }else {$purut=$purut;
    //     }
    // @dd($z);
    // @dd($pketx);


    // $z =DB::connection("SML")->update("exec SP_KetLogfile 'SML/KRS/00001/1025','DBKOREKSIDET',CAST(1 AS INT), '','SA' " , []);

    if ($paktivitas != 'btloto' and $paktivitas != 'oto')  {
         DB::connection("SML")->update("delete tempketlog  where iduser=:pemakai",["pemakai"=>$username] );
         DB::connection("SML")->update("delete xMytable  where iduser=:pemakai",["pemakai"=>$username] );
        DB::connection("SML")->update("insert into tempketlog (keterangan,iduser) values ('',:pemakai)",["pemakai"=>$username] );


 // :nobukti,:table,:urut, '',:pemakai "

       $z =DB::connection("SML")->update("declare
@Nobukti varchar(30),
@Nama_Table varchar(500),
@UrutX numeric(18,0),
@KetLog varchar(8000) ,
@Iduser varchar(30)



select
@Nobukti =:nobukti,
@Nama_Table =:table,
@UrutX =:urut,
@KetLog ='' ,
@Iduser =:pemakai






--Create Table #Mytable (Table_Name varchar(500),Column_Name varchar(500), Value varchar(8000), Column_Type varchar(500))
insert into xMytable
Select a.name,b.name, '' Value, c.name Column_Type,@iduser
From sys.tables a
     left Outer join sys.columns b on b.object_id=a.object_id
     left Outer join sys.types c on c.system_type_id =b.user_type_id

where a.name=@Nama_Table

Declare @Table_Name varchar(500), @Column_Name varchar(500), @Value varchar(8000), @Script varchar(max), @Column_Type varchar(500)
Set @Script=''
Declare Mydata Cursor For
Select Table_Name, Column_Name, isnull(Value,'') Value, Column_Type from xMytable where isnull(iduser,'')=@Iduser
open mydata
fetch next From mydata into @Table_name,@Column_name, @Value,@Column_Type
while @@FETCH_STATUS=0
begin

if @UrutX=0

   Set @Script='update xmyTable Set Value=B.'+isnull(@Column_Name,'')+CHAR(13)+
              'From '+@Table_Name+' B '+CHAR(13)+--'+@Table_Name+'
              'Where B.nobukti='+''''+@Nobukti+''''+CHAR(13)+ --' And B.Urut='+CAST(@UrutX as Varchar(5))+CHAR(13)+
              '   and isnull(xmyTable.iduser,'''')= '''+@Iduser+''''+'  and xmyTable.Column_Name='''+@Column_Name+''''+' and Table_Name='+''''+@Table_Name+''''

  else
      Set @Script='update xmyTable Set Value=B.'+isnull(@Column_Name,'')+CHAR(13)+
              'From '+@Table_Name+' B '+CHAR(13)+--'+@Table_Name+'
              'Where B.nobukti='+''''+@Nobukti+''''+CHAR(13)+ ' And B.Urut='+CAST(@UrutX as Varchar(5))+CHAR(13)+
              '   and isnull(xmyTable.iduser,'''')= '''+@Iduser+''''+'  and xmyTable.Column_Name='''+@Column_Name+''''+' and Table_Name='+''''+@Table_Name+''''
  --Print @Script
  Exec (@Script)
fetch next From mydata into @Table_name,@Column_name, @Value, @Column_Type
end
close mydata
deallocate mydata " , ["nobukti" => $pnoBukti,"table" => $ptable,"urut"=>$purut,"pemakai"=>$username]);






DB::connection("SML")->update("
--Select Table_Name, Column_Name, Value from #Mytable
Declare @Table_Name varchar(500), @Column_Name varchar(500), @Value varchar(8000), @Script varchar(max), @Column_Type varchar(500)
Declare @RowCount int,@i int, @Keterangan varchar(max), @iduser varchar(30)
set @iduser = :pemakai
Set @i=1
Set @Keterangan=''
Select @RowCount=COUNT(Column_Name) from xMytable where isnull(iduser,'')=@iduser
Declare Mydata1 Cursor For
Select Table_Name, Column_Name, Value, Column_Type from xMytable where isnull(iduser,'')=@iduser
open Mydata1
Fetch next from Mydata1 into @Table_Name, @Column_Name, @Value, @Column_Type
while @@FETCH_STATUS=0
begin

  update tempketlog set keterangan=isnull(keterangan,'') +isnull(@Column_Name,'')+' : '+isnull(@Value,'')+CHAR(13)
  where isnull(iduser,'')=@Iduser
  print isnull(@Column_Name,'')+' : '+isnull(@Value,'')+CHAR(13)
 /*
  if @i=@RowCount
  begin
    --Set @Keterangan=@Keterangan+@Column_Name+' : '+isnull(@Value,'')
    --insert into tempketlog (keterangan,iduser) values (isnull(@Keterangan,''),@Iduser)
    --update tempketlog set keterangan=isnull(keterangan,'') +@Column_Name+' : '+isnull(@Value,'')+CHAR(13) where isnull(iduser,'')=@Iduser
    print 'insert'
  end
  else
  begin
    --Set @Keterangan=@Keterangan+@Column_Name+' : '+isnull(@Value,'')+CHAR(13)
    update tempketlog set keterangan=isnull(keterangan,'') +@Column_Name+' : '+isnull(@Value,'')+CHAR(13) where isnull(iduser,'')=@Iduser
    --print 'update'
  end*/

  Set @i=@i+1
Fetch next from Mydata1 into @Table_Name, @Column_Name, @Value, @Column_Type
end


close mydata1
Deallocate Mydata1" , ["pemakai"=>$username]);


       $pketx = DB::connection("SML")->select("select * from tempketlog where iduser=:pemakai and keterangan<>'' ",["pemakai"=>$username] );
         // DB::connection("SML")->update("delete tempketlog  where iduser=:pemakai",["pemakai"=>$username] );
         // DB::connection("SML")->update("delete xMytable  where iduser=:pemakai",["pemakai"=>$username] );

          $pketerangan = $pketx[0]->keterangan;

    } else {
            $pketerangan = $pketerangan;
        }



    $values = [
      $periode[0]->tahun,
      $periode[0]->bulan,
      $username,
      $paktivitas,
      $psumber,
      $pnoBukti,
      $pketerangan
    ];

    DB::connection('SML')->statement('Insert into dbLogFile (Tahun, Bulan, Tanggal,Pemakai,Aktivitas,Sumber,NoBukti,Keterangan)
                        values (?,?,Getdate(),?,?,?,?,?)', $values);

  }



  public function getLockPeriode () {
   $username = \Auth::user()->username;
    $periode = DB::connection('SML')->select('select top 1 user_id, bulan, tahun  from dbperiode where user_id = :username', ["username" => $username] );

    $lockperiode = DB::connection('SML')->select('select *  from dblockperiode where bulan = :bulan and tahun = :tahun', [
      "bulan" => $periode[0]->bulan,
      "tahun" => $periode[0]->tahun,
    ]);

    // return (object) [
    //   "bulan" => $periode[0]->bulan,
    //   "tahun" => $periode[0]->tahun,
    // ];
    return $lockperiode;

  }

  public function getLockPeriodeInput (Request $req) {
   $username = \Auth::user()->username;
    // $periode = DB::connection('SML')->select('select top 1 user_id, bulan, tahun  from dbperiode where user_id = :username', ["username" => $username] );

    $lockperiode = DB::connection('SML')->select('select *  from dblockperiode where bulan = :bulan and tahun = :tahun', [
      "bulan" => $req->bulan,
      "tahun" => $req->tahun,
    ]);

    // return (object) [
    //   "bulan" => $periode[0]->bulan,
    //   "tahun" => $periode[0]->tahun,
    // ];
    return $lockperiode;

  }



  public function LoggingDataTrans ($paktivitas, $psumber, $pnoBukti, $pketerangan,$purut,$ptable) {
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);

    // if ($paktivitas=='I') {
    //       $purut=1;
    //     }else {$purut=$purut;
    //     }
    // @dd($z);
    // @dd($pketx);


    // $z =DB::connection("SML")->update("exec SP_KetLogfile 'SML/KRS/00001/1025','DBKOREKSIDET',CAST(1 AS INT), '','SA' " , []);

    if ($paktivitas != 'btloto' and $paktivitas != 'oto')  {
         DB::connection("SML")->update("delete tempketlog  where iduser=:pemakai",["pemakai"=>$username] );
         DB::connection("SML")->update("delete xMytable  where iduser=:pemakai",["pemakai"=>$username] );
        DB::connection("SML")->update("insert into tempketlog (keterangan,iduser) values ('',:pemakai)",["pemakai"=>$username] );


 // :nobukti,:table,:urut, '',:pemakai "

       $z =DB::connection("SML")->update("declare
@Nobukti varchar(30),
@Nama_Table varchar(500),
@UrutX numeric(18,0),
@KetLog varchar(8000) ,
@Iduser varchar(30)



select
@Nobukti =:nobukti,
@Nama_Table =:table,
@UrutX =:urut,
@KetLog ='' ,
@Iduser =:pemakai






--Create Table #Mytable (Table_Name varchar(500),Column_Name varchar(500), Value varchar(8000), Column_Type varchar(500))
insert into xMytable
Select a.name,b.name, '' Value, c.name Column_Type,@iduser
From sys.tables a
     left Outer join sys.columns b on b.object_id=a.object_id
     left Outer join sys.types c on c.system_type_id =b.user_type_id

where a.name=@Nama_Table

Declare @Table_Name varchar(500), @Column_Name varchar(500), @Value varchar(8000), @Script varchar(max), @Column_Type varchar(500)
Set @Script=''
Declare Mydata Cursor For
Select Table_Name, Column_Name, isnull(Value,'') Value, Column_Type from xMytable where isnull(iduser,'')=@Iduser
open mydata
fetch next From mydata into @Table_name,@Column_name, @Value,@Column_Type
while @@FETCH_STATUS=0
begin

if @UrutX=0

   Set @Script='update xmyTable Set Value=B.'+isnull(@Column_Name,'')+CHAR(13)+
              'From '+@Table_Name+' B '+CHAR(13)+--'+@Table_Name+'
              'Where B.nobukti='+''''+@Nobukti+''''+CHAR(13)+ --' And B.Urut='+CAST(@UrutX as Varchar(5))+CHAR(13)+
              '   and isnull(xmyTable.iduser,'''')= '''+@Iduser+''''+'  and xmyTable.Column_Name='''+@Column_Name+''''+' and Table_Name='+''''+@Table_Name+''''

  else
      Set @Script='update xmyTable Set Value=B.'+isnull(@Column_Name,'')+CHAR(13)+
              'From '+@Table_Name+' B '+CHAR(13)+--'+@Table_Name+'
              'Where B.nobukti='+''''+@Nobukti+''''+CHAR(13)+ ' And B.Urut='+CAST(@UrutX as Varchar(5))+CHAR(13)+
              '   and isnull(xmyTable.iduser,'''')= '''+@Iduser+''''+'  and xmyTable.Column_Name='''+@Column_Name+''''+' and Table_Name='+''''+@Table_Name+''''
  --Print @Script
  Exec (@Script)
fetch next From mydata into @Table_name,@Column_name, @Value, @Column_Type
end
close mydata
deallocate mydata " , ["nobukti" => $pnoBukti,"table" => $ptable,"urut"=>$purut,"pemakai"=>$username]);






DB::connection("SML")->update("
--Select Table_Name, Column_Name, Value from #Mytable
Declare @Table_Name varchar(500), @Column_Name varchar(500), @Value varchar(8000), @Script varchar(max), @Column_Type varchar(500)
Declare @RowCount int,@i int, @Keterangan varchar(max), @iduser varchar(30)
set @iduser = :pemakai
Set @i=1
Set @Keterangan=''
Select @RowCount=COUNT(Column_Name) from xMytable where isnull(iduser,'')=@iduser
Declare Mydata1 Cursor For
Select Table_Name, Column_Name, Value, Column_Type from xMytable where isnull(iduser,'')=@iduser
open Mydata1
Fetch next from Mydata1 into @Table_Name, @Column_Name, @Value, @Column_Type
while @@FETCH_STATUS=0
begin

  update tempketlog set keterangan=isnull(keterangan,'') +isnull(@Column_Name,'')+' : '+isnull(@Value,'')+CHAR(13)
  where isnull(iduser,'')=@Iduser
  print isnull(@Column_Name,'')+' : '+isnull(@Value,'')+CHAR(13)
 /*
  if @i=@RowCount
  begin
    --Set @Keterangan=@Keterangan+@Column_Name+' : '+isnull(@Value,'')
    --insert into tempketlog (keterangan,iduser) values (isnull(@Keterangan,''),@Iduser)
    --update tempketlog set keterangan=isnull(keterangan,'') +@Column_Name+' : '+isnull(@Value,'')+CHAR(13) where isnull(iduser,'')=@Iduser
    print 'insert'
  end
  else
  begin
    --Set @Keterangan=@Keterangan+@Column_Name+' : '+isnull(@Value,'')+CHAR(13)
    update tempketlog set keterangan=isnull(keterangan,'') +@Column_Name+' : '+isnull(@Value,'')+CHAR(13) where isnull(iduser,'')=@Iduser
    --print 'update'
  end*/

  Set @i=@i+1
Fetch next from Mydata1 into @Table_Name, @Column_Name, @Value, @Column_Type
end


close mydata1
Deallocate Mydata1" , ["pemakai"=>$username]);


       $pketx = DB::connection("SML")->select("select * from tempketlog where iduser=:pemakai and keterangan<>'' ",["pemakai"=>$username] );
         // DB::connection("SML")->update("delete tempketlog  where iduser=:pemakai",["pemakai"=>$username] );
         // DB::connection("SML")->update("delete xMytable  where iduser=:pemakai",["pemakai"=>$username] );

          $pketerangan = $pketx[0]->keterangan;

    } else {
            $pketerangan = $pketerangan;
        }



    $values = [
      $periode[0]->tahun,
      $periode[0]->bulan,
      $username,
      $paktivitas,
      $psumber,
      $pnoBukti,
      $pketerangan
    ];

    DB::connection('SML')->statement('Insert into dbLogFile (Tahun, Bulan, Tanggal,Pemakai,Aktivitas,Sumber,NoBukti,Keterangan)
                        values (?,?,Getdate(),?,?,?,?,?)', $values);

  }






  public function getPeriode () {
    $username = \Auth::user()->username;

    $periode = DB::connection('SML')->select('select top 1 user_id, bulan, tahun  from dbperiode where user_id = :username', ["username" => $username] );

    return (object) [
      "bulan" => $periode[0]->bulan,
      "tahun" => $periode[0]->tahun,
    ];

  }

  public function getAkses ($kodemenu, $path) {
    // $akses = DB::connection('SML')->select('select b.* from dbmenu a join dbflmenu b on a.kodemenu = b.l1 where a.href = :path', ["path" => $path] );

    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1' ,'=' , 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID' , \Auth::user()->username)->where('DBMENUWEB.href' , $path)->first();

    // $akses = DBFLMENUWEB::where('USERID', \Auth::user()->username)-> where('href', $path)->first();
    // $akses = collect($akses);
    // $akses = $akses->first();
    return $akses;
  }

  public function getAksesX ($kodemenu, $path) {
    // $akses = DB::connection('SML')->select('select b.* from dbmenu a join dbflmenu b on a.kodemenu = b.l1 where a.href = :path', ["path" => $path] );

    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1' ,'=' , 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID' , \Auth::user()->username)->where('DBMENUWEB.href' , $path)->first();

    // $akses = DBFLMENUWEB::where('USERID', \Auth::user()->username)-> where('href', $path)->first();
    // $akses = collect($akses);
    // $akses = $akses->first();
    return $akses;
  }

public function getStockAkhir(Request $req) {
    $stock = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$req->nosat, $req->date, $req->kodegdg,  $req->kodebrg]);

    return [
      "stock" => $stock
    ];
  }

public function getAkses1 ($kodemenu, $path) {

    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1' ,'=' , 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID' , \Auth::user()->username)->where('DBMENUWEB.href' , $path)->first();

    return $akses;
  }

}
