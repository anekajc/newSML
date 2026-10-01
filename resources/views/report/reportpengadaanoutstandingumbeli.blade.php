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
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>
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
  let globalDate2 = "{!! date('Y-m-d') !!}";

  $(document).ready(function() {
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

    showPeriode();

    setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });
  

  // periode
  function showPeriode() {
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  var modereport_detailnobukti = 0, modereport_detailbarang = 1;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NOBUKTI', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['DPP', 'UM', 1, 'float', 1, 2],
        ['SR', 'LPB', 1, 'float', 1, 2],
        ['akhir', 'Sisa', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
      
    } else {
      gcart_header = [
        ['NOBUKTI', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['DPP', 'UM', 1, 'float', 1, 2],
        ['SR', 'LPB', 1, 'float', 1, 2],
        ['akhir', 'Sisa', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    console.log("makeTable jalankan mode:", _mode);

    let groupby = 'NOBUKTI';
    let _date2 = $("#inputDate2").val();

    g_modeReport = modereport_detailnobukti;

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date2: _date2,
    };

    console.log("Data terkirim ke server:", data);

    doMakeTable(_mode, groupby, data, "REPORT OUTSTANDING UANG MUKA BELI", _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    return ['NOBUKTI', 'NAMACUSTSUPP'];
  }

  function reportMode(_mode) {
    if (jenisreport != _mode) {
      let prev_mode = jenisreport;
      jenisreport = _mode;

      $("#tombolMode" + prev_mode).removeClass("btn-primary");
      $("#tombolMode" + prev_mode).addClass("btn-outline-primary");

      $("#tombolMode" + jenisreport).removeClass("btn-outline-primary");
      $("#tombolMode" + jenisreport).addClass("btn-primary");

      setModeReport();

    }
  }

  function setModeReport() {
    if (jenisreport === 0) {
      g_modeReport = modereport_detailnobukti;
    } else {
      g_modeReport = modereport_rekapnobukti;
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }


</script>

@endsection
