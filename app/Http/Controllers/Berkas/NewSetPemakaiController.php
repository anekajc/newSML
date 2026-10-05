<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Http\Controllers\NewMenuController;

class NewSetPemakaiController extends Controller
{

  public function index(Request $req) {

    // Daftar user tidak lagi dirender di server: tabel & kartu ringkasan diisi loadAll()
    // lewat AJAX, jadi tidak ada dua versi baris (Blade vs JS) yang susunan kolomnya berbeda.
    // Menu samping layout newmasterTest diambil lewat AJAX /getmenu, jadi getMenuL0() (3 query
    // join DBFLMENUWEB) juga tidak perlu - makin sedikit query, makin kecil peluang halaman
    // jatuh ke 500 saat koneksi ke SQL Server sedang lambat.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.newsetpemakai' , [
      "menul0" => [],
      "periode" => $periode,
      "filter" => $req->filter ?? '',
    ]);

  }

  // Menu Berkas > Pemakai ACC / Pemakai Non ACC: halaman sama persis dengan Pemakai, hanya daftar
  // user-nya dibatasi menurut departemen (DBFLPASS.kodeBag) - lihat loadAll().
  public function indexAcc(Request $req) {
    $req->merge(['filter' => 'acc']);
    return $this->index($req);
  }

  public function indexNonAcc(Request $req) {
    $req->merge(['filter' => 'nonacc']);
    return $this->index($req);
  }

  public function loadAll (Request $req) {
    // filter=acc: hanya user departemen ACC; filter=nonacc: semua selain ACC (termasuk yang
    // departemennya kosong); tanpa filter: semua user (menu Pemakai).
    $where = '';
    if ($req->filter === 'acc') {
      $where = "where ltrim(rtrim(isnull(kodeBag, ''))) = 'ACC'";
    } else if ($req->filter === 'nonacc') {
      $where = "where ltrim(rtrim(isnull(kodeBag, ''))) <> 'ACC'";
    }
    $users = DB::connection("SML")->select('select USERID, username, FullName, TINGKAT, STATUS, kodeBag, KodeJab, KodeKasir, [limit], keynik from DBFLPASS ' . $where . ' order by USERID');
    return $users;

  }

  public function detailUser (Request $req ) {

    $list = DB::connection("SML")->select('select USERID, username, FullName, TINGKAT, STATUS, kodeBag, KodeJab, KodeKasir, [limit], keynik from DBFLPASS where username = :username ' ,["username" => $req->username]);
    return $list;

  }


  public function listAkses (Request $req) {
    $values = [
        $req->userid,
      ];

    if (!$req->userid || !DB::connection('SML')->select('select top 1 USERID from DBFLPASS where USERID = :u', ['u' => $req->userid])) {
      return [];
    }

    // return [$values];
    DB::connection('SML')->update('exec sp_updateMenuWeb1 ?',$values);

    // sp_updateMenuWeb1 ternyata mengisi DBFLMENU (tabel lama), bukan DBFLMENUWEB yang dibaca
    // web - akibatnya menu yang belum punya baris hak akses (mis. PO, PR, DPP, Invoice) tidak
    // muncul di daftar dan tidak bisa diberikan ke user mana pun. Baris yang kurang dibuatkan di
    // sini dengan semua hak 0 (= belum ada akses), TIPE 'STK' seperti baris lain.
    // SP-nya tetap dipanggil karena DBFLMENU masih dibaca (mis. otorisasi SO).
    DB::connection('SML')->insert(
      "insert into DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
         IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
       select :userid, m.KODEMENU, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'STK', 0, 0
       from DBMENUWEB m
       where not exists (select 1 from DBFLMENUWEB w where w.USERID = :cek and w.L1 = m.KODEMENU)",
      ['userid' => $req->userid, 'cek' => $req->userid]
    );


    $listAkses = DB::connection("SML")->select('select a.* , b.* from DBMENUWEB a join DBFLMENUWEB b ON a.KODEMENU = b.L1 where b.USERID = :username order by a.KODEMENU' , ['username' => $req->userid]);

    return $listAkses;
  }

  public function listAksesReport (Request $req) {
    if (!$req->userid || !DB::connection('SML')->select('select top 1 USERID from DBFLPASS where USERID = :u', ['u' => $req->userid])) {
      return [];
    }

    // Hak akses report web disimpan di DBFLMENUREPORTWEB, bukan DBFLMENUREPORT (desktop).
    // DBFLMENUREPORT punya FK ke DBMENUREPORT, jadi 40 report yang hanya ada di DBMENUREPORTWEB
    // (SO, SPB, Stock, dst.) tidak bisa disimpan di sana, dan 27 kode dipakai untuk report yang
    // berbeda di desktop (mis. 020502 = HPP di web, Laba Rugi di desktop). SP lama
    // sp_updateMenuReportWeb1 / SP_Flmenureportweb1 tidak dipanggil lagi dari sini.
    // Baris yang kurang dibuatkan dengan semua hak 0 (= belum ada akses), sama seperti DBFLMENUWEB.
    // DBSMLNEW..DBFLMENUREPORTWEB: UserID, L1, Access, IsDesign, Isexport (PK UserID + L1).
    DB::connection('SML')->insert(
      "insert into DBFLMENUREPORTWEB (UserID, L1, Access, IsDesign, Isexport)
       select :userid, m.KODEMENU, 0, 0, 0
       from DBMENUREPORTWEB m
       where not exists (select 1 from DBFLMENUREPORTWEB w where w.UserID = :cek and w.L1 = m.KODEMENU)",
      ['userid' => $req->userid, 'cek' => $req->userid]
    );

    $listAksesReport = DB::connection("SML")->select('select a.KODEMENU, a.Keterangan, a.L0, b.Access, b.IsDesign, b.Isexport from DBMENUREPORTWEB a join DBFLMENUREPORTWEB b on a.KODEMENU = b.L1 where b.UserID = :username order by a.KODEMENU', ['username' => $req->userid]);
    return $listAksesReport;


  }

  public function listCoa (Request $req) {
    $tes = DB::connection("SML")->update('update DBPERKIRAAN set iskirim = 0');
    $listCoa = DB::connection("SML")->select('select Perkiraan, Keterangan from DBPERKIRAAN where Perkiraan not in ( select Perkiraan from DBAKSESPERKIRAAN where UserID = :username ) order by Perkiraan', ['username' => $req->username]);
    // left join: akses ke perkiraan yang sudah dihapus dari DBPERKIRAAN tetap tampil (dulu hilang dari
    // daftar dan hanya bisa dibuang lewat "cabut semua").
    $listAksesCoa = DB::connection("SML")->select('select a.Keterangan , b.Perkiraan from DBAKSESPERKIRAAN b left join DBPERKIRAAN a on a.Perkiraan = b.Perkiraan where b.UserID = :username order by b.Perkiraan', ['username' => $req->username]);

    return [
      'listCoa' => $listCoa, 'listAksesCoa' => $listAksesCoa, 'tes'=>$tes
    ];
  }

  public function updateIsKirimCOA (Request $req) {


      $tes = DB::connection("SML")->update('update DBPERKIRAAN set iskirim = :nilai where Perkiraan = :perkiraan' , ['perkiraan' => $req->perkiraan, 'nilai' => $req->nilai]);
      return $tes;
  }

  public function updateAddAksesCOA (Request $req) {
    // insert into dbaksesperkiraan
    // Select '+QuotedStr(Myuser)+',Perkiraan from dbperkiraan
    // where Tipe=1 and perkiraan not in(select perkiraan from dbaksesperkiraan where userid='+QuotedStr(Myuser)+')
    // insert into DBAKSESPERKIRAAN select 'SA' , Perkiraan from DBPERKIRAAN where iskirim=1 and Perkiraan not in (select perkiraan from dbaksesperkiraan where userid='SA')
    $tes = DB::connection("SML")->insert('insert into DBAKSESPERKIRAAN select :username , Perkiraan from DBPERKIRAAN where iskirim=1 and Perkiraan not in (select perkiraan from dbaksesperkiraan where userid=:username1)', ['username' => $req->username , 'username1' => $req->username]);
    return json_encode($tes);

  }

  public function updateAddAllAksesCOA (Request $req) {
    $tes = DB::connection("SML")->insert('insert into DBAKSESPERKIRAAN select :username , Perkiraan from DBPERKIRAAN where Perkiraan not in (select perkiraan from dbaksesperkiraan where userid=:username1)' , ['username' => $req->username , 'username1' => $req->username]);
    return json_encode($tes);
  }

  public function deleteAllAksesCOA (Request $req) {
    // delete from DBAKSESPERKIRAAN where UserID= 'SA'

    $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username' , ['username' => $req->username ]);

    return $tes;
  }


  public function deleteUser (Request $req) {

    if (!$req->username) {
      return 'User belum dipilih';
    }

    // User yang sedang login tidak boleh menghapus dirinya sendiri - sesi yang sedang
    // berjalan langsung kehilangan data user-nya.
    if (strcasecmp(trim($req->username), trim(\Auth::user()->username)) === 0) {
      return 'User yang sedang dipakai login tidak bisa dihapus';
    }
    // Sama dengan pengaman Sp_FLpassWEB choice 'D' (UserID<>'SA').
    if (strcasecmp(trim($req->username), 'SA') === 0) {
      return 'User SA tidak bisa dihapus';
    }

      $tes = DB::connection("SML")->update('delete from dbflpass where username= :username' , ['username' => $req->username]);

    if (!$tes) {
      return 'User ' . $req->username . ' tidak ditemukan';
    }

    // Hak akses menu web & akses COA milik user yang dihapus ikut dibuang - kalau tertinggal,
    // user baru yang kelak dibuat dengan nama sama langsung mewarisi semua akses lamanya.
    DB::connection("SML")->update('delete from DBFLMENUWEB where USERID = :username', ['username' => $req->username]);
    DB::connection("SML")->update('delete from DBFLMENUREPORTWEB where UserID = :username', ['username' => $req->username]);
    DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID = :username', ['username' => $req->username]);

    return 1;
  }


  public function deleteAksesCOA (Request $req) {
    $perkiraan = $req->perkiraan;
    $username = $req->username;

    // Tanpa pilihan, $perkiraan null dan foreach di bawah membuat 500.
    if (!is_array($perkiraan) || !$perkiraan) {
      return 'Pilih perkiraan yang akan dihapus aksesnya';
    }
    // $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username and Perkiraan = :perkiraan' , ['username' => $req->username, 'perkiraan' => $req->perkiraan ]);
    // delete from DBAKSESPERKIRAAN where UserID= 'SA' and Perkiraan = '00'

    foreach ($perkiraan as $p) {
      // code...
      $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username and Perkiraan = :perkiraan' , ['username' => $username, 'perkiraan' => $p['Perkiraan'] ]);
    }
    return 1;
  }

  public function spUpdateAksesReport (Request $req) {
    $username = $req->input('username');

    $data = $req->input('tempData');

    if (!$username || !is_array($data) || empty($data['KODEMENU'])) {
      return 'Data akses report tidak lengkap';
    }

    // DB::connection('SML')->statement('delete	DBFLMENUREPORT where UserID = :USERID',['USERID' => $username ]);

    // foreach ($data as $d) {
      // $values = [
      //    $username,
      //    $data['KODEMENU'],
      //    $data['HASACCESS'],
      //    $data['ISDESIGN'],
      //    $data['ISEXPORT']
      //   ];
      // DB::connection('SML')->update('exec SP_Flmenureportweb1 ?,?,?,?,?',$values);
    // }

    // Disimpan ke DBFLMENUREPORTWEB (lihat listAksesReport), bukan lewat SP_Flmenureportweb1
    // yang meng-update DBFLMENUREPORT milik desktop.
    $tes = DB::connection('SML')->update(
      'update DBFLMENUREPORTWEB set Access = :akses, IsDesign = :design, Isexport = :export where UserID = :userid and L1 = :l1',
      [
        'akses' => !empty($data['HASACCESS']) ? 1 : 0,
        'design' => !empty($data['ISDESIGN']) ? 1 : 0,
        'export' => !empty($data['ISEXPORT']) ? 1 : 0,
        'userid' => $username,
        'l1' => $data['KODEMENU'],
      ]
    );

    if (!$tes) {
      return 'Akses report ' . $data['KODEMENU'] . ' tidak ditemukan';
    }

    return 1;
  }



  public function spUpdateAkses (Request $req) {

    $username = $req->input('username');

    $data = $req->input('tempData');

    if (!$username || !is_array($data) || empty($data['KODEMENU'])) {
      return 'Data akses menu tidak lengkap';
    }

    // DB::connection('SML')->statement('delete	DBFLMENUWEB where USERID = :USERID',['USERID' => $username ]);

    // foreach ($data as $d) {
      $values = [
         $username,
         $data['KODEMENU'],
         $data['HASACCESS'],
         $data['ISTAMBAH'],
         $data['ISKOREKSI'],
         $data['ISHAPUS'],
         $data['ISCETAK'],
         $data['ISEXPORT'],
         $data['ISOTO1'],
         $data['ISOTO2'],
         $data['ISOTO3'],
         $data['ISOTO4'],
         $data['ISOTO5'],
         $data['ISBATAL'],
         // @pembatalan tidak tampil di layar; dulu dikirim '' sehingga setiap centang di baris
         // itu mereset pembatalan jadi 0. Sekarang nilai lamanya (dari daftar akses) dikirim balik.
         !empty($data['PEMBATALAN']) ? 1 : 0
        ];
      DB::connection('SML')->update('exec SP_FlmenuWeb1 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
    // }

    return 1;


  }

  public function spUpdateAksesHeader (Request $req) {

    // $username = $req->input('username');

    // $data = $req->input('tempData');


    // DB::connection('SML')->statement('delete	DBFLMENUWEB where USERID = :USERID',['USERID' => $username ]);


    // foreach ($data as $d) {
      $values = [
         $req->input('headermenu'),
         $req->input('nilai'),
         $req->input('username'),
         $req->input('field'),
        ];
    // Nama field -> kolom DBFLMENUWEB (daftar putih, nama kolom tidak pernah diambil langsung
    // dari input).
    $kolom = [
      'hasaccess' => 'HASACCESS', 'istambah' => 'ISTAMBAH', 'isKoreksi' => 'ISKOREKSI',
      'isHapus' => 'ISHAPUS', 'isCetak' => 'ISCETAK', 'isExport' => 'ISEXPORT',
      'isOtorisasi1' => 'IsOtorisasi1', 'isOtorisasi2' => 'IsOtorisasi2', 'isOtorisasi3' => 'IsOtorisasi3',
      'isOtorisasi4' => 'IsOtorisasi4', 'isOtorisasi5' => 'IsOtorisasi5', 'isBatal' => 'IsBatal',
    ];
    $field = (string) $req->input('field');
    if (!isset($kolom[$field]) || !$req->input('username') || trim((string) $req->input('headermenu')) === '') {
      return 'Data akses menu tidak lengkap';
    }

      DB::connection('SML')->update('exec sp_updatesetmenuweb1 ?,?,?,?',$values);
    // }

    // sp_updatesetmenuweb1 ternyata meng-update DBFLMENU (tabel lama), bukan DBFLMENUWEB yang
    // dibaca web - centang di baris menu induk (mis. "Berkas") dulu tidak mengubah akses web sama
    // sekali. SP tetap dipanggil (DBFLMENU masih dibaca, mis. otorisasi SO), lalu DBFLMENUWEB
    // di-update dengan aturan yang sama: menu induk + semua sub menu yang kodenya diawali kode induk.
    $awal = str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], rtrim((string) $req->input('headermenu'))) . '%';
    DB::connection('SML')->update(
      'update DBFLMENUWEB set ' . $kolom[$field] . ' = :nilai where USERID = :userid and L1 like :awal',
      ['nilai' => $req->input('nilai') == 1 ? 1 : 0, 'userid' => $req->input('username'), 'awal' => $awal]
    );

    return 1;


  }

  public function loadKaryawan (){
    // KeyNIK (int) yang disimpan ke DBFLPASS.keynik; NIK berisi nomor KTP (varchar, 16 digit)
    // yang melebihi batas int @Keynik di Sp_FLpassWEB.
    $list = DB::connection('SML')->select('SELECT KeyNIK, NIK, Nama FROM DBKARYAWAN');

    return $list;
  }

  public function loadDepartemen (){
    $list = DB::connection('SML')->select('SELECT KDDEP,NMDEP FROM DBDEPART');

    return $list;
  }

  public function loadJabatan (){
    $list = DB::connection('SML')->select('SELECT KODEJAB, NamaJab FROM DBJABATAN');

    return $list;
  }

  public function submitAdduser (Request $req) {
    $choice = $req->input('choice');
    $user = trim((string) $req->input('user'));

    if ($choice !== 'I' && $choice !== 'U') {
      return 'Mode simpan tidak dikenal';
    }
    if ($user === '') {
      return 'User harus diisi';
    }
    if (trim((string) $req->input('namaLengkap')) === '') {
      return 'Nama Lengkap harus diisi';
    }
    // Batas panjang & tipe mengikuti parameter Sp_FLpassWEB - nilai yang melebihi membuat SP
    // gagal (500) atau terpotong diam-diam.
    // Sp_FLpassWEB menerima 15 karakter, tapi sp_updateMenuWeb1 & sp_updateMenuReportWeb1 hanya
    // varchar(10) - user yang lebih panjang terpotong di sana.
    if ($choice == 'I' && strlen($user) > 10) {
      return 'User maksimal 10 karakter';
    }
    if (strlen(trim((string) $req->input('namaLengkap'))) > 50) {
      return 'Nama Lengkap maksimal 50 karakter';
    }
    if (trim((string) $req->input('departemen')) === '' || strlen((string) $req->input('departemen')) > 15) {
      return 'Departemen harus diisi (maksimal 15 karakter)';
    }
    if (trim((string) $req->input('jabatan')) === '' || strlen((string) $req->input('jabatan')) > 15) {
      return 'Jabatan harus diisi (maksimal 15 karakter)';
    }
    if (strlen((string) $req->input('kodeKasir')) > 3) {
      return 'Kode Kasir maksimal 3 karakter';
    }
    if (!in_array((string) $req->input('level'), ['0', '1', '2'], true)) {
      return 'Level tidak dikenal';
    }
    if (!in_array((string) $req->input('status'), ['0', '1'], true)) {
      return 'Status tidak dikenal';
    }
    if ($req->input('limit') !== null && $req->input('limit') !== '' && (!is_numeric($req->input('limit')) || $req->input('limit') < 0)) {
      return 'Limit harus angka 0 atau lebih';
    }
    // @Keynik bertipe integer. Saat edit (U) SP tidak mengubah keynik, jadi hanya dicek saat tambah.
    if ($choice == 'I' && !ctype_digit((string) $req->input('nik'))) {
      return 'NIK harus berupa angka';
    }

    $ada = DB::connection('SML')->select(
      'select top 1 USERID from DBFLPASS where USERID = :userid or username = :username',
      ['userid' => $user, 'username' => $user]
    );

    if ($choice == 'I') {
      // Dulu tidak dicek: user yang sudah ada bisa "ditambah" lagi dan diam-diam tertimpa SP.
      if ($ada) {
        return 'User ' . $user . ' sudah ada';
      }
      if ((string) $req->input('password') === '') {
        return 'Password harus diisi';
      }
    } else if (!$ada) {
      return 'User ' . $user . ' tidak ditemukan';
    }

    $hashedPassword = 'xx';
    if ( $req->input('choice') == 'I') {

      $hashedPassword = Hash::make($req->input('password'));

    }

   //   [
   //     $req->input('choice'),
   //     $req->input('user'),
   //     'dariweb' ,
   //     $req->input('level'),
   //     $req->input('status'),
   //     $req->input('namaLengkap'),
   //     $req->input('departemen'),
   //     $req->input('jabatan'),
   //      $req->input('kodeKasir'),
   //      '',
   //     $req->input('nik'),
   //     $req->input('limit'),
   //     $hashedPassword
   // ];

  $check = DB::connection('SML')->update("EXEC Sp_FLpassWEB ?,?,?,?,?,?,?,?,?,?,?,?,?",
   [
      $req->input('choice'),
      $user,
      'dariweb' ,
      $req->input('level'),
      $req->input('status'),
      $req->input('namaLengkap'),
      $req->input('departemen'),
      $req->input('jabatan'),
      // Kode Kasir boleh kosong; null dari form kosong dikirim sebagai '' seperti Limit 0.
      (string) $req->input('kodeKasir'),
      '',
      // U: keynik tidak dipakai SP (baris update-nya di-comment di SP), dikirim apa adanya/0.
      ctype_digit((string) $req->input('nik')) ? (int) $req->input('nik') : 0,
      is_numeric($req->input('limit')) ? $req->input('limit') : 0,
      $hashedPassword
  ]);

  // Sp_FLpassWEB tidak membuat baris DBPERIODE, sedangkan layout membaca $periode->bulan di
  // setiap halaman - tanpa baris ini user baru langsung kena 500 begitu login (kasus LEON &
  // MSYAIDI). Formatnya sama dengan baris lain: userid & user_id = user, periode = bulan berjalan.
  if ($choice == 'I') {
    DB::connection('SML')->insert(
      'insert into DBPERIODE (userid, bulan, tahun, user_id)
       select :userid, cast(MONTH(GETDATE()) as varchar(2)), cast(YEAR(GETDATE()) as varchar(4)), :username
       where not exists (select 1 from DBPERIODE where user_id = :cek)',
      ['userid' => $user, 'username' => $user, 'cek' => $user]
    );
  }

  return 1;

}

}
