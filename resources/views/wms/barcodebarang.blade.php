@extends('newmasterTest')
@section('page-title', 'Barcode Barang')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection

@section('buttons')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;

<button type="button" class="btn btn-secondary btn-sm btn-top" data-toggle="modal" onclick="buttonPrintPenerimaan()" rel="tooltip" data-placement="bottom" title="Rak/Lokasi" style="width: 30px;"><i class="bi bi-pin-map"></i></button>&nbsp;&nbsp;
@endsection

@section('content')
<div class="container-fluid">
{{-- <h1>Barcode Barang</h1> - judul pindah ke header (page-title) --}}
</div>

<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

</div>

<div class="container">


  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card wms-kartu">
    {{-- card-header lama (isi tab-nya sudah dikomentari) tidak ditampilkan:
    <div class="card-header">
      <div class="row">
        <!-- <nav style="width: 100%;">
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Outstanding PO</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Penerimaan Barang</a>
          </div>
        </nav> -->
      </div>
    </div> --}}
      <div class="card-body">
      <div class="row">
        <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          {{-- Ditambahkan 2026-10-20: kotak cari + pilihan jumlah baris (data dimuat per halaman dari server). --}}
          <div class="po-toolbar wms-toolbar">
            <input type="search" class="po-search-inp" id="cari_barcodebarang" placeholder="Cari kode / nama barang" autocomplete="off">
            <div class="po-len-wrap">
              <label for="tampil_barcodebarang">Tampilkan</label>
              <select class="po-len-inp" id="tampil_barcodebarang">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">Semua</option>
              </select>
            </div>
          </div>
          <table class="data-table" id="tabel_gudang" style=" background: white;" data-wms-asli>
            <thead>
              <tr>
                <th width="15%">Kode</th>
                <th width="30%">Nama</th>



              </tr>
            </thead>
            <tbody id="gudang_data">
              {{-- Diubah 2026-10-20: baris diisi DataTables per halaman (initTabelBarcodeBarang) - render lama:
              @if (count($gudang) > 0)
                @for ($i = 0; $i < count($gudang); $i++)
                  <tr id="{!! $i !!}-tr" onclick="select({!! $i !!}, {!! $i !!}, '{!! $gudang[$i]->KODEBRG !!}', '{!! $gudang[$i]->NAMABRG !!}','{!! $gudang[$i]->KODEBRG !!}', {!! $gudang[$i]->KODEBRG !!}, {!! $gudang[$i]->KODEBRG !!})">
                    <td>{!! $gudang[$i]->KODEBRG !!}</td>
                    <td>{!! $gudang[$i]->NAMABRG !!}</td>




                  </tr>
                @endfor
              @else
                <tr>
                  <td colspan="5">Tidak ada data gudang ditemukan.</td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>

                </tr>
              @endif
              --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- End modal edit gudang -->

<!-- start modal add detail Rak -->

<!-- End modal add detail Rak -->

<!-- start modal add detail Lokasi -->

<!-- End modal add detail Lokasi -->

@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">
  var g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
  var g_koderak = "", g_namarak = "", g_kodegdgrak = "", g_tingkat = "", g_kolom = "";
  var g_kodelokasi = "", g_namalokasi = "", g_kodegdgraklokasi = "", g_koderaklokasi = "" , g_urutLokasi = "" , g_qntMax= 0, g_barcodeloc = '' ;
  // start document ready
	$(document).ready(function(){
    // Diubah 2026-10-20: tabel diisi per halaman dari server (initTabelBarcodeBarang) - init lama:
    // var table = $('#tabel_gudang').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    // table.on('search' / 'order' / 'page', ...) -> sekarang di initTabelBarcodeBarang()
    initTabelBarcodeBarang();
	});

  // Ditambahkan 2026-10-20: tabel Barcode Barang per halaman dari server (barcodebarangdata). Kotak cari
  // mencari kode/nama barang di seluruh data, "Tampilkan" mengatur jumlah baris per halaman.
  var tabelBarcodeBarang = null;
  var timerCariBarcodeBarang = null;

  function teksBarcodeBarang(v) {
    return $('<div>').text(v == null ? '' : v).html();
  }

  function resetPilihBarcodeBarang() {
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
  }

  function initTabelBarcodeBarang() {
    tabelBarcodeBarang = $('#tabel_gudang').DataTable({
      serverSide: true,
      processing: true,
      ajax: {
        url: "{!! url('barcodebarangdata') !!}",
        type: "POST",
        data: function (d) {
          d._token = $("#_token").val();
          delete d.columns;
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Gagal memuat data barang, silahkan refresh browser');
        }
      },
      dom: "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      pageLength: Number($("#tampil_barcodebarang").val()) || 10,
      order: [[0, 'asc']],
      columns: [
        { data: 'KODEBRG', render: teksBarcodeBarang },
        { data: 'NAMABRG', render: teksBarcodeBarang }
      ],
      createdRow: function (row, data, index) {
        row.id = index + '-tr';
      },
      language: {
        emptyTable: 'Tidak ada data barang ditemukan.',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
        processing: 'Memuat data...'
      },
      drawCallback: function () {
        if (window.wmsAturTinggiTabel) { setTimeout(window.wmsAturTinggiTabel, 0); }
      }
    });
    tabelBarcodeBarang.on('search', resetPilihBarcodeBarang);
    tabelBarcodeBarang.on('order', resetPilihBarcodeBarang);
    tabelBarcodeBarang.on('page', resetPilihBarcodeBarang);
    tabelBarcodeBarang.on('length', resetPilihBarcodeBarang);
  }

  $(document).on('input', '#cari_barcodebarang', function () {
    var kata = this.value;
    clearTimeout(timerCariBarcodeBarang);
    timerCariBarcodeBarang = setTimeout(function () {
      if (tabelBarcodeBarang && tabelBarcodeBarang.search() !== kata) { tabelBarcodeBarang.search(kata).draw(); }
    }, 400);
  });

  $(document).on('change', '#tampil_barcodebarang', function () {
    if (tabelBarcodeBarang) { tabelBarcodeBarang.page.len(Number(this.value)).draw(); }
  });

  // Klik baris -> fungsi select() lama dengan data baris yang sama (kode dikirim sebagai teks).
  $(document).on('click', '#gudang_data > tr', function () {
    if (!tabelBarcodeBarang) { return; }
    var d = tabelBarcodeBarang.row(this).data();
    if (!d) { return; }
    var i = this.id.replace('-tr', '');
    select(i, i, d.KODEBRG, d.NAMABRG, d.KODEBRG, d.KODEBRG, d.KODEBRG);
  });

  function buttonPrintPenerimaan() {

    if (g_kode === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log(g_barcodeloc)


    // return
    let printContent1 = ''

    let printContent2 = document.getElementById(`tempqrcodeprint`).innerHTML;
    printContent1 += `<div style="break-inside: avoid">`
    printContent1 += `<div style="width: 125px; text-align: center">`
    printContent1 += printContent2
    printContent1 += `</div>`

    printContent1 += `<div style="margin-top: -20px;width: 125px; text-align: center">`
    printContent1 += `<h6>${g_barcodeloc}</h6>`
    printContent1 += `</div>`
    printContent1 += `</div>`

    document.getElementById("printContainer").innerHTML = printContent1



    w=window.open(' ');
    // w.opener = null;
    // w.refferer = null;
    // w.rel="noopener , noreferrer"

    w.document.write($(`#printContainer`).html());
    // console.log('a')
    w.print();
    // console.log('b')
    w.close();



  }

  function hanyaAngka(event) {
    var angka = (event.which) ? event.which : event.keyCode
    if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
        return false;
    return true;
  }

  $("#divTambahPostingRak").hide();
  $("#divUbahPostingRak").hide();
  $("#divTambahPostingLokasi").hide();
  $("#divUbahPostingLokasi").hide();
  // end document ready

  // start reset input
  // function reset(name = "") {
  //   $("#" + name + "kode").val("");
  //   $("#" + name + "nama").val("");
  //   $("#" + name + "alamat").val("");
  //   $("#" + name + "produksi").val("0");
  //   $("#" + name + "rusak").val("0");
  // }
  // end reset input

  function resetDetail() {
    $("#lkdgudang").html("");
    $("#lnmgudang").html("");
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
  }

  function resetDetailLokasi() {
    g_kode =$("#lkdgudangl").html();
    g_nama =$("#lnmgudangl").html();

    $("#lkdgudangl").html("");
    $("#lnmgudangl").html("");
    $("#lkdrak").html("");
    $("#lnmrak").html("");
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0


    $("#detaillokasi").modal('toggle');
    $("#detailRak").modal('toggle');
    showRak();
  }

  function resetTambahDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#divTambahPostingRak").hide();
  }

  function resetEditDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#divUbahPostingRak").hide();
  }

  function resetTambahDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0
    $("#divTambahPostingLokasi").hide();
  }

  function reseteditDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    // $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0
    // $("#qntMax").val(0);
    $("#divUbahPostingLokasi").hide();
  }

  // start refresh tabel
  // Diubah 2026-10-20: Refresh memuat ulang halaman tabel yang sedang tampil (cari & Tampilkan tetap).
  function loadAll() {
    resetPilihBarcodeBarang();
    if (tabelBarcodeBarang) { tabelBarcodeBarang.ajax.reload(null, false); }
  }

  {{-- loadAll lama (memuat semua barang sekaligus lewat loadAllbarcodebarang) - tidak dipakai sejak 2026-10-20:
  function loadAllLama() {
    $('#tabel_gudang').DataTable().destroy();
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    $.ajax({
      url     : "{!! url('loadAllbarcodebarang') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          $('#gudang_data').html("");
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-tr" onclick="select('+i+', '+i+', \''+result[i].KODEBRG+'\', \''+result[i].NAMABRG+'\',  \''+result[i].KODEBRG+'\', '+result[i].KODEBRG+', '+result[i].KODEBRG+')">\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].NAMABRG+ '</td>\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].KODEBRG + '</td>';


            str = str + '</tr>';
          }
          $('#gudang_data').html(str);
        }
        else {
          $('#gudang_data').html('<tr>\
            <td colspan="5">Tidak ada data gudang ditemukan.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    var table = $('#tabel_gudang').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    table.on('order', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    // Ditambahkan 2026-10-19: tabel sekarang berhalaman - pindah halaman juga mereset pilihan baris.
    table.on('page', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
  }
  --}}
  // end refresh table

  function select(_row, _id, _kode, _nama, _produksi, _rusak, _alamat) {
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-tr").css('background-color', 'gold');
    g_id = _id; g_kode = _kode; g_nama = _nama; g_produksi = _produksi; g_rusak = _rusak; g_alamat = _alamat;

    document.getElementById("tempPrintContainer").innerHTML = '<div id="tempqrcodeprint" style="width: 10px; height: 10px"></div>'

    new QRCode(document.getElementById(`tempqrcodeprint`), {text: `${g_kode}` , width: 125, height: 125});

  }

  function selectrak(_row, _koderak, _namarak, _kodegdgrak, _tingkat, _kolom) {
    $('#rakgudang > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#r"+_row+"-tr").css('background-color', 'gold');
    g_koderak = _koderak; g_namarak = _namarak; g_kodegdgrak = _kodegdgrak; g_tingkat = _tingkat; g_kolom = _kolom;
  }

  function selectlokasi(_row, _kodelokasi, _namalokasi, _kodegdgraklokasi, _koderaklokasi , _qntMax , _urutLokasi , _barcodeloc) {
    console.log(_row, _kodelokasi, _namalokasi, _kodegdgraklokasi, _koderaklokasi, _qntMax , _urutLokasi)
    $('#lokasirak > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#l"+_row+"-tr").css('background-color', 'gold');
    g_kodelokasi = _kodelokasi; g_namalokasi = _namalokasi; g_kodegdgraklokasi = _kodegdgraklokasi; g_koderaklokasi = _koderaklokasi; g_urutLokasi = _urutLokasi ; g_qntMax = _qntMax ; g_barcodeloc = _barcodeloc ;

    document.getElementById("tempPrintContainer").innerHTML = '<div id="tempqrcodeprint" style="width: 10px; height: 10px"></div>'

    new QRCode(document.getElementById(`tempqrcodeprint`), {text: `${g_barcodeloc}` , width: 125, height: 125});

  }

  function showDivTambahPostingRak() {
    console.log('showDivTambahPostingRak')
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if ($("#divTambahPostingRak").is(":hidden")) {
      $("#divTambahPostingRak").show();
      $("#divUbahPostingRak").hide();
    } else {
      $("#divTambahPostingRak").hide();
    }
  }

  function showDivTambahPostingLokasi() {
    console.log('showDivTambahPostingLokasi')
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if ($("#divTambahPostingLokasi").is(":hidden")) {
      $("#divTambahPostingLokasi").show();
      $("#divUbahPostingLokasi").hide();
    } else {
      $("#divTambahPostingLokasi").hide();
    }
  }

  // start add gudang
  // function add() {
  //   var _token = $("#_token").val();
  //   var _kode = $("#kode").val();
  //   var _nama = $("#nama").val();
  //   var _alamat = $("#alamat").val();
  //   var _produksi = $("#produksi").val();
  //   var _rusak = $("#rusak").val();
  //   if (_kode != "" && _nama != "") {
  //     $.ajax({
  //       url     : "{!! url('addGudang') !!}",
  //       type    : "POST",
  //       async   : false,
  //       data    : {
  //         _token : _token,
  //         kode : _kode,
  //         nama : _nama,
  //         alamat : _alamat,
  //         produksi : _produksi,
  //         rusak : _rusak
  //       },
  //       success : function(result) {
  //         if (result == 1) {
  //           reset();
  //           loadAll();
  //           alertify.success('Data gudang telah ditambahkan.');
  //         } else {
  //           alertify.alert('Gagal menambahkan data gudang!', 'Kode gudang sudah ada.', function(){ });
  //         }
  //       }
  //     });
  //   }
  //   else {
  //     alertify.alert('Gagal menambahkan data gudang!', 'Semua kolom harus terisi.', function(){ });
  //   }
  // }
  // end add gudang

  // start add Rak gudang
  function addDetailRak(name) {
    // console.log('addDetailRak')
    var _token = $("#_token").val();
    var _kode = $("#kodeRak").val();
    var _nama = $("#namaRak").val();
    var _tingkat = $("#tingkat").val();
    var _kolom = $("#kolom").val();
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'I';
    if (_kode != "" && _nama != "" && _tingkat != "" && _kolom != "") {
      $.ajax({
        url     : "{!! url('addRak') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          tingkat : _tingkat,
          kolom : _kolom,
          kodegdg : _kodegdg
        },
        success : function(result) {
          if (result == 1) {
            resetTambahDetailRak();
            showloadRak();
            alertify.success('Data Rak telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data Rak!', 'Kode Rak sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data Rak!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add Rak gudang

  // start add Rak gudang
  function addDetailL() {
    var _token = $("#_token").val();
    var _kode = $("#kodeLokasi").val();
    var _nama = $("#namaLokasi").val();
    var _qntMax = $("#qntMax").val();
    var _urutLokasi = $("#urutLokasi").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    var _Choice = 'I';
    // console.log('addLokasi')
    // console.log(_qntMax, _urutLokasi)
    // return
    if (_kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('addLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          kodegdg : _kodegdg,
          koderak : _koderak,
          qntMax: _qntMax,
          urutLokasi: _urutLokasi,
        },
        success : function(result) {
          if (result == 1) {
            resetTambahDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data Lokasi!', 'Kode Lokasi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add Rak gudang Lokasi

  // start tampilkan gudang
  // function show() {
  //   if (g_id === "" || g_kode === "" || g_nama === "" || g_produksi === "" || g_rusak === "") {
  //     alertify.warning("Tidak ada baris dipilih");
  //     return;
  //   }
  //   $("#eid").val(g_id);
  //   $("#ekode").val(g_kode);
  //   $("#enama").val(g_nama);
  //   $("#enama").val(g_alamat);
  //   $("#eproduksi").val(g_produksi);
  //   $("#erusak").val(g_rusak);
  //   $("#editGudang").modal('toggle');
  // }
  // end tampilkan gudang

  // start tampilkan Menu Rak gudang
  function showRak() {
    if (g_kode === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log('showRak')
    g_koderak = ""; g_namarak = ""; g_kodegdgrak = ""; g_tingkat = ""; g_kolom = "";
    $("#lkdgudang").html(g_kode);
    $("#lnmgudang").html(g_nama);
    $("#detailRak").modal('toggle');
    $("#divTambahPostingRak").hide();
    $("#divUbahPostingRak").hide();
    showloadRak();

  }
  // end tampilkan Menu Rak gudang

  // start tampilkan Rak gudang
  function showloadRak() {
    $("#rakgudang").html("");
    var _token = $("#_token").val();
    var _kode = g_kode;
    str = "";
    $.ajax({
      url     : "{!! url('showRak') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        KodeGdg : _kode
      },
      success : function(result) {
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="r'+i+'-tr" onclick="selectrak(' + i + ', \'' + result[i].KodeRak + '\', \'' + result[i].NamaRak + '\', \'' + _kode + '\', \'' + result[i].Tingkat + '\', \'' + result[i].Kolom + '\')">\
              <td>' + result[i].KodeRak + '</td>\
              <td>' + result[i].NamaRak + '</td>\
              <td>' + result[i].Tingkat + '</td>\
              <td>' + result[i].Kolom + '</td>\</tr>';
          }
        } else {
          str = str + "<td colspan='4'>Data Rak tidak ditemukan</td>";
        }
        $("#rakgudang").html(str);
      }
    });
  }
  // end tampilkan Rak gudang

  function showEditDetailRak() {
    console.log('showEditDetailRak')
    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_koderak === "" || g_namarak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingRak").hide();
    $("#ekodeRak").val(g_koderak);
    $("#enamaRak").val(g_namarak);
    $("#etingkat").val(g_tingkat);
    $("#ekolom").val(g_kolom);
    $("#divUbahPostingRak").show();
  }

  // start tampilkan Menu Rak gudang
  function showRakLokasi() {
    if (g_koderak === "" || g_namarak === "" || g_kodegdgrak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log('showRakLokasi')
    g_kodelokasi = ""; g_namalokasi = ""; g_kodegdgraklokasi = ""; g_koderaklokasi = "";
    $("#lkdgudangl").html(g_kodegdgrak);
    $("#lnmgudangl").html($("#lnmgudang").html());
    $("#lkdrak").html(g_koderak);
    $("#lnmrak").html(g_namarak);
    $("#divTambahPostingLokasi").hide();
    $("#divUbahPostingLokasi").hide();

    // $("#formAddListItem").modal('toggle')
    // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
    // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
    var modal = document.getElementById("detailRak");
    modal.style.display = "block";

    $("#detaillokasi").modal('toggle');
    // $("#detailRak").modal('toggle');
    showloadRakLokasi();
  }
  // end tampilkan Menu Rak gudang

  // start tampilkan Rak Lokasi
  function showloadRakLokasi() {
    $("#lokasirak").html("");
    var _token = $("#_token").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    str = "";
    $.ajax({
      url     : "{!! url('showLokasi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        Kodegdg : _kodegdg,
        Koderak : _koderak
      },
      success : function(result) {

        console.log('Result show Lokasi' , result)
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {

            str = str + '<tr id="l'+i+'-tr" onclick="selectlokasi('+ `${i} , '${result[i].KodeLokasi}', '${result[i].NamaLokasi}'  , '${_kodegdg}' , '${_koderak}' , ${result[i].QntMax} , '${result[i].Urut}' , '${result[i].BarcodeLoc}'` +')">\
              <td>' + result[i].KodeLokasi + "</td>\
              <td>" + result[i].NamaLokasi + '</td></tr>';
          }
        } else {
          str = str + "<td colspan='2'>Data Lokasi tidak ditemukan</td>";
        }
        $("#lokasirak").html(str);
      }
    });
  }
  // end tampilkan Rak Lokasi

  function showEditDetailLokasi() {
    console.log('showEditDetailLokasi')
    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingLokasi").hide();
    $("#ekodeLokasi").val(g_kodelokasi);
    $("#enamaLokasi").val(g_namalokasi);
    $("#eqntMax").val(g_qntMax);
    $("#eurutLokasi").val(g_urutLokasi);
    $("#divUbahPostingLokasi").show();
  }

  // start edit gudang
  // function edit() {
  //   var _token = $("#_token").val();
  //   var _id = $("#eid").val();
  //   var _kode = $("#ekode").val();
  //   var _nama = $("#enama").val();
  //   var _alamat = $("#ealamat").val();
  //   var _produksi = $("#eproduksi").val();
  //   var _rusak = $("#erusak").val();
  //   if (_id != "" && _kode != "" && _nama != "") {
  //     $.ajax({
  //       url     : "{!! url('editGudang') !!}",
  //       type    : "POST",
  //       async   : false,
  //       data    : {
  //         _token : _token,
  //         id : _id,
  //         kode : _kode,
  //         nama : _nama,
  //         alamat : _alamat,
  //         produksi : _produksi,
  //         rusak : _rusak
  //       },
  //       success : function(result) {
  //         if (result == 1) {
  //           $("#editGudang").modal('toggle');
  //           reset("e");
  //           loadAll();
  //           alertify.success('Data gudang telah diubah.');
  //         } else {
  //           alertify.alert('Gagal mengubah data gudang!', 'Kode gudang sudah ada.', function(){ });
  //         }
  //       }
  //     });
  //   }
  //   else {
  //     alertify.alert('Gagal mengubah data gudang!', 'Semua kolom harus terisi.', function(){ });
  //   }
  // }
  // end edit gudang

  // start edit Rak gudang
  function editDetailRak(name) {
    var _token = $("#_token").val();
    var _kode = $("#ekodeRak").val();
    var _nama = $("#enamaRak").val();
    var _tingkat = $("#etingkat").val();
    var _kolom = $("#ekolom").val();
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'U';
    if (_kode != "" && _nama != "" && _tingkat != "" && _kolom != "") {
      $.ajax({
        url     : "{!! url('addRak') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          tingkat : _tingkat,
          kolom : _kolom,
          kodegdg : _kodegdg
        },
        success : function(result) {
          if (result == 1) {
            resetEditDetailRak();
            showloadRak();
            alertify.success('Data Rak telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data Rak!', 'Kode Rak sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data Rak!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit Rak gudang

  // start edit Rak lokasi
  function editDetailL(name) {
    var _token = $("#_token").val();
    var _kode = $("#ekodeLokasi").val();
    var _nama = $("#enamaLokasi").val();
    var _qntMax = $("#eqntMax").val();
    var _urutLokasi = $("#eurutLokasi").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    var _Choice = 'U';
    if (_kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('addLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          kodegdg : _kodegdg,
          koderak : _koderak,
          qntMax: _qntMax,
          urutLokasi: _urutLokasi
        },
        success : function(result) {
          if (result == 1) {
            reseteditDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data Lokasi!', 'Kode Lokasi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit Rak Lokasi

  // start hapus gudang
  // function erase() {
  //   if (g_id === "" || g_nama === "") {
  //     alertify.warning("Tidak ada baris dipilih");
  //     return;
  //   }
  //   alertify.confirm('Hapus Gudang', 'Apakah yakin ingin menghapus gudang dengan nama ' + g_nama + ' ?',
  //     function(){
  //       var _token = $("#_token").val();
  //       $.ajax({
  //         url     : "{!! url('eraseGudang') !!}",
  //         type    : "POST",
  //         async   : false,
  //         data    : {
  //           _token : _token,
  //           id : g_id
  //         },
  //         success : function(result) {
  //           loadAll();
  //           alertify.success('Data gudang telah dihapus.');
  //         }
  //       });
  //   },function(){});
  // }
  // end hapus gudang

  // start hapus Rak gudang
  function eraseDetailRak() {
    console.log('eraseDetailRak')
    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_koderak === "" || g_namarak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Rak', 'Apakah yakin ingin menghapus Rak dengan nama ' + g_namarak + ' ?',
      function(){
        var _token = $("#_token").val();
        var _kode = g_koderak;
        var _nama = g_namarak;
        var _kodegdg = $("#lkdgudang").html();
        var _tingkat = g_tingkat;
        var _kolom = g_kolom;
        var _Choice = 'D';
        $.ajax({
          url     : "{!! url('addRak') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _Choice,
            kode : _kode,
            nama : _nama,
            tingkat : _tingkat,
            kolom : _kolom,
            kodegdg : _kodegdg
          },
          success : function(result) {
            if (result == 1) {
              showloadRak();
              alertify.success('Data Rak telah dihapus.');
            } else {
              alertify.alert('Gagal menghapus data Rak!', 'Kode Rak sudah ada transaksi.', function(){ });
            }
          }
        });
    },function(){});
  }
  // end hapus Rak gudang

  // start hapus Rak Lokasi
  function eraseDetailLokasi() {
    console.log('eraseDetailLokasi')
    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Lokasi', 'Apakah yakin ingin menghapus Lokasi dengan nama ' + g_namalokasi + ' ?',
      function(){
        var _token = $("#_token").val();
        var _kode = g_kodelokasi;
        var _nama = g_namalokasi;
        var _kodegdg = $("#lkdgudangl").html();
        var _kodegdg = $("#lkdrak").html();
        var _Choice = 'D';
        $.ajax({
          url     : "{!! url('addLokasi') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _Choice,
            kode : _kode,
            nama : _nama,
            kodegdg : _kodegdg,
            koderak : _koderak
          },
          success : function(result) {
            if (result == 1) {
              showloadRakLokasi();
              alertify.success('Data Lokasi telah dihapus.');
            } else {
              alertify.alert('Gagal menghapus data Lokasi!', 'Kode Lokasi sudah ada transaksi.', function(){ });
            }

          }
        });
    },function(){});
  }
  // end hapus Rak Lokasi

  function buttonPrintPenerimaan() {
   // Ditambahkan 2026-10-20: tanpa baris dipilih QR belum dibuat -> sebelumnya error JS, sekarang diberi pesan.
   if (g_kode === "" || g_nama === "" || !document.getElementById(`tempqrcodeprint`)) {
     alertify.warning("Tidak ada baris dipilih");
     return;
   }
   var xnama = g_nama +'/'+ g_kode;

    console.log(g_barcodeloc)


    // return
    let printContent1 = ''

    let printContent2 = document.getElementById(`tempqrcodeprint`).innerHTML;
    printContent1 += `<div style="break-inside: avoid">`
    printContent1 += `<div style="width: 125px; text-align: center">`
    printContent1 += printContent2
    printContent1 += `</div>`

    printContent1 += `<div style="margin-top: -20px;width: 125px; text-align: center">`
    printContent1 += `<h6>${xnama}</h6>`

    printContent1 += `</div>`
    printContent1 += `</div>`

    document.getElementById("printContainer").innerHTML = printContent1



    w=window.open(' ');
    // w.opener = null;
    // w.refferer = null;
    // w.rel="noopener , noreferrer"

    w.document.write($(`#printContainer`).html());
    // console.log('a')
    w.print();
    // console.log('b')
    w.close();



  }


</script>
@endsection
