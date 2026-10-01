@extends('report.masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }
  </style>
<!-- Warna centang -->

@section('header2')
  <div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="margin-top:-20px; margin-bottom:150px;">
    <!-- Kiri: ikon -->
    <div class="d-flex" style="gap: 10px;">
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          <input type="date" class="form-control mb-2" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <label for="inputDate2" class="mb-0">s/d</label>
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>
      </div> 
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOtorisasi" data-bs-toggle="dropdown" aria-expanded="false" title="Otorisasi" style="cursor: pointer;">
          <i class="fas fa-key"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOtorisasi" aria-labelledby="inputOtorisasi">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setOtorisasi('2')">Semua</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOtorisasi('1')">Non Otorisasi</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOtorisasi('0')">Otorisasi</a></li>
        </ul>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="B" onclick="setOrderBy('B')">Barang</a></li>
        </ul>
      </div>
    </div>

    <!-- Kanan: tombol aksi menempel ke ujung kanan layar -->
    <div class="d-flex ms-auto" style="gap: 8px;">
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormCustomizeTable()" title="Customize Table">
        <i class="fas fa-cog"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="makeTable('REPORT')" title="Submit">
        <i class="fas fa-check"></i>
      </button>
    </div>
  </div>

@endsection



{{-- @section('reportname')
      <h3>Report Pengadaan - Closing PR</h3>
@endsection --}}


{{-- @section('input')
                <div class="row" style="display: flex; justify-content: center;" hidden>
                  <div class="col-6 btn-primary text-center tombol-toggle" id="buttonMode0" onclick="doReportMode(0)">Detail</div>
                  <div class="col-6 btn-outline-primary text-center tombol-toggle" id="buttonMode1" onclick="doReportMode(1)">Rekap</div>
                </div>

                <br> --}}
                
                {{-- <div class="row rounded" style="background-color: #E8E8E8; padding: 10px; display: flex; justify-content: center;">
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate1" style="">Periode</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="date" class="form-control" id="inputDate1" value="{!! date('Y-m-d') !!}">
                  </div>
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate2">s/d</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="date" class="form-control" id="inputDate2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div> --}}

                {{-- <div class="row text-center mt-4 align-items-center">
                  <div class="col-2">
                    <label for="inputDate1">Periode</label>
                  </div>

                  <div class="col-4 d-flex align-items-center justify-content-center">
                    <input type="date" class="form-control" id="inputDate1" value="{!! date('Y-m-d') !!}" style="width: 150px;">
                    <label for="inputDate2" class="mx-2 mb-0">s/d</label>
                    <input type="date" class="form-control" id="inputDate2" value="{!! date('Y-m-d') !!}" style="width: 150px;">
                  </div>
                </div>
                <div class="row text-center mt-4">
                  <div class="col-2" style="margin-top:-15px;">
                    <label for="inputOtorisasi">Otorisasi</label>
                  </div>
                  <div class="col-4" style="margin-top:-15px;">
                    <select id="inputOtorisasi" class="form-control" aria-label="Default select example">
                      <option value=0>Otorisasi</option>
                      <option value=1>Non Otorisasi</option>
                      <option value=2>Semua</option>
                    </select>
                  </div>
                </div>
                <div class="row text-center mt-4">
                  <div class="col-2" style="margin-top:-15px;">
                      <label for="inputOrder">Order By</label>
                    </div>
                    <div class="col-4" style="margin-top:-15px;">
                      <select id="inputOrder" class="form-control" aria-label="Default select example">
                        <option value="N">Nomor Bukti</option>
                          <option value="B">Nomor Barang</option>
                    </select>
                  </div>
                </div>
@endsection --}}

@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; // default: Semua
  let globalOrderBy = "N";   // default: Nomor Bukti
  
  $(document).ready(function() {
    console.log("Script sudah jalan. Coba klik tombol untuk tes fungsi.");
    $("#btnFilterData").on("click", function() {
      if (typeof doShowFormFilterData === "function") doShowFormFilterData();
      else alert(" Fungsi doShowFormFilterData belum tersedia.");
    });

    $("#btnCustomizeTable").on("click", function() {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });

    $("#btnSubmitReport").on("click", function() {
      makeTable('REPORT');
    });

    setOtorisasi(globalOtorisasi);
    setOrderBy(globalOrderBy);
    showPeriode();
    setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  // otorisasi
  function setOtorisasi(val) {
    globalOtorisasi = val;
    let text = (val == '0') ? 'Semua' : (val == '1') ? 'Otorisasi' : 'Non Otorisasi';
    // alertify.success(`Otorisasi: ${text}`);

    // hapus semua centang
    $('#dropdownOtorisasi .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim(); 
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownOtorisasi .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });
  }

  // order by
  function setOrderBy(val) {
    globalOrderBy = val;
    let text = (val == 'N') ? 'Nomor Bukti' : 'Barang';
    // alertify.success(`Order By: ${text}`);

    // hapus semua centang
    $('#dropdownOrder .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });
  }
  
  var modereport_detail = 0, modereport_rekap = 1;
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    console.log("g_modeReport:", g_modeReport);
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0,],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['Qntbatal', 'Qnt Batal', 1, 'float', 0, 2],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        ['KodeBrg', 'Kode', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 0, 2],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 0;
    }
  }
  
  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_detail;
      groupby = 'NoBukti';
    } else if (input_order == "B") {
      g_modeReport = modereport_rekap;
      groupby = 'KodeBrg';
    }

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOto : globalOtorisasi,
      inputOrd : input_order,
    };

    doMakeTable(_mode, groupby, data, "REPORT PENGADAAN CLOSING PR", _date1, _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if (g_modeReport == modereport_detail) {
      data = ['NoBukti', 'Tanggal'];
    } else {
      data = ['KodeBrg', 'NamaBrg'];
    }

    return data;
  }

</script>

@endsection
