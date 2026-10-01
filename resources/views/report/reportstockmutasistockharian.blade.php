@extends('report.masterreportGudang')
        @include('report.modalbrowsemaster')
{{-- 
@section('reportname')
      <h3>Report Mutasi Stock Harian</h3>
@endsection --}}

@section('header2')
<div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between"
     style="margin-top:-20px; margin-bottom:150px;">

  <!-- LEFT CONTROLS -->
  <div class="d-flex align-items-center" style="gap:10px;">

    <!-- MODE TOGGLE (HIDDEN, SAME AS BEFORE) -->
    <div class="btn-group" role="group" hidden>
      <button type="button"
              class="btn btn-primary"
              id="buttonMode0"
              onclick="doReportMode(0)">
        No Bukti
      </button>
      <button type="button"
              class="btn btn-outline-primary"
              id="buttonMode1"
              onclick="doReportMode(1)">
        Kode Barang
      </button>
    </div>

    <!-- PERIODE -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Periode">
        <i class="fas fa-calendar-alt"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:350px;">
        <label for="inputDate1" class="mb-1">Periode</label>
        <input type="date"
               class="form-control mb-2"
               id="inputDate1"
               value="{!! date('Y-m-d') !!}">

        <label for="inputDate2" class="mb-1">s/d</label>
        <input type="date"
               class="form-control"
               id="inputDate2"
               value="{!! date('Y-m-d') !!}">
      </div>
    </div>

    <!-- GUDANG -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Gudang">
        <i class="fas fa-warehouse"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputGudang" class="mb-1">Gudang</label>
        <div class="input-group">
          <input type="text"
                 id="inputGudang"
                 class="form-control"
                 placeholder="-"
                 onfocus="doSetInputBrowseMaster('inputGudang', '{!! $gudang !!}')"
                 onblur="doBlurInputBrowseMaster()"
                 value="-">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputGudang', '{!! $gudang !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- NO SATUAN -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="No Satuan">
        <i class="fas fa-sort-numeric-up"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:200px;">
        <label for="inputIsi" class="mb-1">No Satuan</label>
        <input type="number"
               id="inputIsi"
               class="form-control"
               value="1">
      </div>
    </div>

  </div>

  <!-- RIGHT ACTIONS -->
  <div class="d-flex ms-auto" style="gap:8px;">
    <button type="button"
            class="btn btn-outline-primary"
            onclick="makeTable('REPORT')"
            title="Submit">
      <i class="fas fa-check"></i>
    </button>
  </div>

</div>
@endsection

@section('additionalModal')
        <!-- start modal browse master -->
        @include('modalbrowsemaster')
        <!-- End modal browse master -->
@endsection


@section('jsreport')
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script type="text/javascript">
  g_modeReport = 0;

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

    // setReportMode(globalReportMode);
    // setOtorisasi(globalOtorisasi);
    // setOrderBy(globalOrderBy);
    // setAgen(globalAgen);
    // showPeriode();
    // setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  $(document).ready(function(){
    $("#gButtonCustomizeTable").hide();
  });

  function setDefaultHeader() {
    gcart_header = [
      ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
      ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
      ['Sat1', 'Sat', 1, 'varchar', 0, 0],
      ['KodeGdg', 'Gdg', 1, 'varchar', 0, 0],
      ['QntAwal', 'Awal', 1, 'float', 1, 2],
      ['QNTPBL', 'Beli', 1, 'float', 1, 2],
      ['QNTRPJ', 'R. Jual', 1, 'float', 1, 2],
      ['QNTADI', 'Adj (+)', 1, 'float', 1, 2],
      ['QNTTRI', 'Tr (+)', 1, 'float', 1, 2],
      ['QNTUKI', 'Uk (+)', 1, 'float', 1, 2],
      ['QNTPNJ', 'Jual', 1, 'float', 1, 2],
      ['QNTRPB', 'R.Beli', 1, 'float', 1, 2],
      ['QNTADO', 'Adj (-)', 1, 'float', 1, 2],
      ['QNTTRO', 'Tr (-)', 1, 'float', 1, 2],
      ['QNTUKO', 'Uk (-)', 1, 'float', 1, 2],
      ['SALDOQNT', 'Akhir', 1, 'float', 1, 2]
    ];

    gsum_issubtotal = 0; gsum_isgrandtotal = 0;
  }
  
  function getRowFooter1(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap;">' + format_number(_sum, 2) + '</td>';
  }
  
  function getRowFooter2(_col, _colspanRow2) {
    let _sum = gcart_res.filter(item => currencyNormalizer(item[_col]) !== 0).length;
    let _str = '  <td colspan="' + _colspanRow2 + '" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap;">' + _sum + '</td>'

    return { _sum, _str };
  }
  
  function setRowFooter() {
    let rowFooter1 = "", rowFooter2 = "";
    let colspanRow2 = 1;

    rowFooter1 += "<tr style='text-align: center'>";
    rowFooter1 += '  <td colspan="3" class="cellcompact-center" style="border: 1px solid black; white-space:nowrap;">Total Item : ' + gcart_res.length + '</td>';
    rowFooter1 += '  <td class="cellcompact-left" style="border: 1px solid black; white-space:nowrap;">Total</td>';

    rowFooter2 += "<tr style='text-align: center'>";
    rowFooter2 += '  <td colspan="4" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap;"></td>';

    let tempFooter2;
    let tot_masuk = 0, tot_keluar = 0, tos = 0;

    // === MASUK === //

    // AWAL
    rowFooter1 += getRowFooter1("QntAwal");
    tempFooter2 = getRowFooter2("QntAwal", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;
    let awal = (tempFooter2._sum !== 0) ? tempFooter2._sum : 1;

    // BELI
    rowFooter1 += getRowFooter1("QNTPBL");
    tempFooter2 = getRowFooter2("QNTPBL", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // RETUR JUAL
    rowFooter1 += getRowFooter1("QNTRPJ");
    tempFooter2 = getRowFooter2("QNTRPJ", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // ADJ (+)
    rowFooter1 += getRowFooter1("QNTADI");
    tempFooter2 = getRowFooter2("QNTADI", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // TR (+)
    rowFooter1 += getRowFooter1("QNTTRI");
    tempFooter2 = getRowFooter2("QNTTRI", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Ubah Kemasan In
    rowFooter1 += getRowFooter1("QNTUKI");
    tempFooter2 = getRowFooter2("QNTUKI", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // === KELUAR === //

    // JUAL
    rowFooter1 += getRowFooter1("QNTPNJ");
    tempFooter2 = getRowFooter2("QNTPNJ", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // RETUR BELI
    rowFooter1 += getRowFooter1("QNTRPB");
    tempFooter2 = getRowFooter2("QNTRPB", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // ADJ (-)
    rowFooter1 += getRowFooter1("QNTADO");
    tempFooter2 = getRowFooter2("QNTADO", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // TR (-)
    rowFooter1 += getRowFooter1("QNTTRO");
    tempFooter2 = getRowFooter2("QNTTRO", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // Ubah Kemasan Out
    rowFooter1 += getRowFooter1("QNTUKO");
    tempFooter2 = getRowFooter2("QNTUKO", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // AKHIR
    rowFooter1 += getRowFooter1("SALDOQNT");
    tempFooter2 = getRowFooter2("SALDOQNT", colspanRow2);
    rowFooter2 += tempFooter2._str;
    // tot_keluar += tempFooter2._sum;

    rowFooter1 += "</tr>";
    rowFooter2 += "</tr>";

    let rowTOS = "";
    let _style = 'style="border: none !important; outline: none !important;"';
    colspanRow2 = (4 + 1 + 5 + 5 + 1);
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';

    // tot_masuk = (tot_masuk !== 0) ? tot_masuk : 1;
    // tos = format_number((tot_keluar / tot_masuk) * 100, 2);
    tos = ((tot_masuk - tot_keluar) / awal);
    let _td = '<td ' + _style + '></td>';
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Item masuk : ' + tot_masuk + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Item masuk : ' + tot_keluar + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Turn over stock : ' + tos + '</td></tr>'
    
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';

    return rowFooter1 + rowFooter2 + rowTOS;
  }

  function makeTable(_mode, _callback) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "KodeBrg";
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();

    let data = {
      date1            : _date1,
      date2            : _date2,
      inputGudang      : $("#inputGudang").val(),
      inputIsi         : $("#inputIsi").val(),
    };

    // doMakeTable() sudah async (lihat catatan di masterreportGudang.blade.php); halaman
    // ini tidak membaca gcart_res/gcart_filter langsung sesudah manggil, tapi mode FILTER
    // dipanggil doShowFormFilterData() di layout yang butuh _callback ini untuk tahu kapan
    // gcart_filter sudah terisi.
    doMakeTable(_mode, groupby, data, "LAPORAN STOK HARIAN", _date1, _date2, _callback);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    // if (g_modeReport == modereport_nobukti) {
    //   data = ['nobukti', 'Tanggal'];
    // } else {
    //   data = ['KODEBRG', 'NAMABRG'];
    // }
    data = ['KodeBrg', 'NamaBrg'];

    return data;
  }

</script>

@endsection