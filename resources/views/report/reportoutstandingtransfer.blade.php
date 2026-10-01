@extends('masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }
  </style>
<!-- Warna centang -->

@include('modalMarketingSO')

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
          {{-- <label for="inputDate2" class="mb-0">s/d</label>
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}"> --}}
          {{-- <button class="btn btn-primary btn-sm mt-2 w-100" onclick="showPeriode()">Terapkan</button> --}}
        </div>
      </div> 

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder" style="max-height: 125px; overflow-y: auto;">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="B" onclick="setOrderBy('B')">Nomor Barang</a></li>
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

@section('jsreport')

<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; // default: Semua
  let globalOrderBy = "N";   // default: Nomor Bukti
  let globalReportMode = "0"; // default: Detail

  var jenisreport = 0; // ini untuk detail dan rekap
  
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
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }
  
    function setOrderBy (val) {

    globalOrderBy = val;
    let text = ''

    if ( val == 'N'){
      text = 'Nomor Bukti'
    } else if ( val == 'B'){
      text = 'Nomor Barang'
    } else if ( val == 'C'){
      text = 'Nomor Customer'
    } else if ( val == 'S'){
      text = 'Sales'
    } else if ( val == 'HG'){
      text = 'Head Group'
    } else if ( val == 'P'){
      text = 'PIC'
    }
    // alertify.success(`Order By: ${text}`);

    // hapus semua centang
    $('#dropdownOrder .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });

    setModeReport();

  }

  var modereport_nobukti = 0, modereport_barang = 1;
  g_modeReport = modereport_detailnobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_nobukti) {
      gcart_header = [
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KODEBRG', 'Kode Brg', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['GDGASAL', 'Gdg Asal', 1, 'varchar', 0, 0],
        ['gdgTujuan', 'Gdg Tujuan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 2],
        ['SISA', 'Qnt Sisa', 1, 'float', 1, 2],
        ['rPsISA', 'Rp Sisa', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    } else if (g_modeReport == modereport_barang) {
      gcart_header = [
        ['KODEBRG', 'Kode Brg', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['GDGASAL', 'Gdg Asal', 1, 'varchar', 0, 0],
        ['gdgTujuan', 'Gdg Tujuan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 2],
        ['SISA', 'Qnt Sisa', 1, 'float', 1, 2],
        ['rPsISA', 'Rp Sisa', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = (g_modeReport == modereport_nobukti) ? "nobukti" : "KODEBRG";
    let _date1  = $("#inputDate1").val();

    console.log(groupby)
    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1    : _date1,
      inputOto : globalOtorisasi,
      inputOrd : (g_modeReport == modereport_nobukti) ? "N" : "B",
    };

    doMakeTable(_mode, groupby, data, "REPORT OUTSTANDING TRANSFER", _date1);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if (g_modeReport == modereport_nobukti) {
      data = ['nobukti', 'Tanggal'];
    } else {
      data = ['KODEBRG', 'NAMABRG'];
    }

    return data;
  }

  function setModeReport () {
    if (globalOrderBy == "N") {
        g_modeReport = modereport_nobukti;
    } else if (globalOrderBy == 'B') {
        g_modeReport = modereport_barang;
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }

</script>

@endsection
