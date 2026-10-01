@extends('report.masterreportGudang')
@include('report.modalbrowsemaster')
{{-- @section('reportname')
      @if ($mode_menu == 'QTY')
        <h3>Report Mutasi Stock (Qty)</h3>
      @elseif ($mode_menu == 'RP')
        <h3>Report Mutasi Stock (Rp)</h3>
      @elseif ($mode_menu == 'QTYRP')
        <h3>Report Mutasi Stock (Qty + Rp)</h3>
      @else
        <h3>Report Mutasi Per Periode</h3>
      @endif
@endsection --}}

@section('header2')
  <div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="margin-top:-20px; margin-bottom:150px;">
    <!-- Kiri: ikon dan controls -->
    <div class="d-flex" style="gap: 10px;">
      
      <!-- Toggle Mode Buttons (No Bukti / Kode Barang) -->
      <div class="btn-group" role="group" hidden>
        <button type="button" class="btn btn-primary" id="buttonMode0" onclick="doReportMode(0)">No Bukti</button>
        <button type="button" class="btn btn-outline-primary" id="buttonMode1" onclick="doReportMode(1)">Kode Barang</button>
      </div>

      <!-- Periode Dropdown -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          @if ($mode_menu == 'PERIODE')
            <label for="inputDate1" class="mb-1">Periode</label>
            <input type="date" class="form-control mb-2" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <label for="inputDate2" class="mb-1">s/d</label>
            <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
          @else
            <label for="inputDate1" class="mb-1">Periode</label>
            <input type="month" class="form-control mb-2" id="inputDate1" value="{!! date('Y-m') !!}">
          @endif
        </div>
      </div>

      <!-- Gudang Dropdown -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnGudang" data-bs-toggle="dropdown" aria-expanded="false" title="Gudang">
          <i class="fas fa-warehouse"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 300px;">
          <label for="inputGudang" class="mb-1">Gudang</label>
          <div class="input-group">
            <input type="text" id="inputGudang" class="form-control" placeholder="-" onfocus="doSetInputBrowseMaster('inputGudang', '{!! $gudang !!}')" onblur="doBlurInputBrowseMaster()" value="-">
            <button type="button" class="btn btn-primary" onclick="doBrowseMaster('inputGudang', '{!! $gudang !!}')"><i class="bi bi-search"></i></button>
          </div>
        </div>
      </div>

      <!-- No Satuan Dropdown -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnSatuan" data-bs-toggle="dropdown" aria-expanded="false" title="No Satuan">
          <i class="fas fa-sort-numeric-up"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 200px;">
          <label for="inputIsi" class="mb-1">No Satuan</label>
          <input type="number" id="inputIsi" class="form-control" value="1">
        </div>
      </div>

      <!-- Stock Minus Dropdown -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnStockMinus" data-bs-toggle="dropdown" aria-expanded="false" title="Stock Minus">
          <i class="fas fa-minus-circle"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 200px;">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="inputStockMinus">
            <label class="form-check-label" for="inputStockMinus">Stock Minus?</label>
          </div>
        </div>
      </div>

      <!-- Filter Data (Grup, Kategori, Sub Kategori, Merk) -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnFilterData" data-bs-toggle="dropdown" aria-expanded="false" title="Filter Data">
          <i class="fa-solid fa-filter"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 600px;" onclick="event.stopPropagation();">
          
          <!-- Row 1: Grup & Kategori -->
          <div class="row mb-3">
            <div class="col-2">
              <label for="inputGrup">Grup</label>
            </div>
            <div class="col-4">
              <div class="input-group">
                <input type="text" id="inputGrup" class="form-control" placeholder="-" onfocus="doSetInputBrowseMaster('inputGrup', '{!! $grup !!}')" onblur="doBlurInputBrowseMaster()" value="-">
                <button type="button" class="btn btn-primary btn-sm" onclick="doBrowseMaster('inputGrup', '{!! $grup !!}')"><i class="bi bi-search"></i></button>
              </div>
            </div>
            <div class="col-2">
              <label for="inputKategori">Kategori</label>
            </div>
            <div class="col-4">
              <div class="input-group">
                <input type="text" id="inputKategori" class="form-control" placeholder="-" onfocus="doSetInputBrowseMaster('inputKategori', '{!! $kategori !!}')" onblur="doBlurInputBrowseMaster()" value="-">
                <button type="button" class="btn btn-primary btn-sm" onclick="doBrowseMaster('inputKategori', '{!! $kategori !!}')"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

          <!-- Row 2: Sub Kategori & Merk -->
          <div class="row mb-3">
            <div class="col-2">
              <label for="inputSubKategori">Sub Kategori</label>
            </div>
            <div class="col-4">
              <div class="input-group">
                <input type="text" id="inputSubKategori" class="form-control" placeholder="-" onfocus="doSetInputBrowseMaster('inputSubKategori', '{!! $subkategori !!}')" onblur="doBlurInputBrowseMaster()" value="-">
                <button type="button" class="btn btn-primary btn-sm" onclick="doBrowseMaster('inputSubKategori', '{!! $subkategori !!}')"><i class="bi bi-search"></i></button>
              </div>
            </div>
            <div class="col-2">
              <label for="inputMerk">Merk</label>
            </div>
            <div class="col-4">
              <div class="input-group">
                <input type="text" id="inputMerk" class="form-control" placeholder="-" onfocus="doSetInputBrowseMaster('inputMerk', '{!! $merk !!}')" onblur="doBlurInputBrowseMaster()" value="-">
                <button type="button" class="btn btn-primary btn-sm" onclick="doBrowseMaster('inputMerk', '{!! $merk !!}')"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Tipe Barang Dropdown -->
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputAgen" data-bs-toggle="dropdown" aria-expanded="false" title="Agen" style="cursor: pointer;">
          <i class="fa-solid fa-user"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownAgen" aria-labelledby="inputAgen">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setAgen('0')">Agen</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setAgen('1')">Non-Agen</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setAgen('2')">Semua</a></li>
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
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script type="text/javascript">
  var modereport_qty = 0, modereport_rp = 1, modereport_qtyrp = 2, modereport_periode = 3;
  g_modeReport = modereport_qty;
  
  let globalAgen = "2";   // default: Agen

  var reportTitle = "";

    $(document).ready(function() {

    // $("#btnFilterData").on("click", function() {
    //   if (typeof doShowFormFilterData === "function") doShowFormFilterData();
    //   else alert(" Fungsi doShowFormFilterData belum tersedia.");
    // });

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
    setAgen(globalAgen);
    // showPeriode();
    // setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function setAgen (val) {
    globalAgen = val;
    let text = (val == '2') ? 'Semua' : (val == '0') ? 'Agen' : 'Non-Agen';
    // alertify.success(`Otorisasi: ${text}`);

    // hapus semua centang
    $('#dropdownAgen .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim(); 
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownAgen .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });
  }

  $(document).ready(function(){
    $("#gButtonCustomizeTable").hide();
    if ("{!! $mode_menu !!}" == "QTY") {
      g_modeReport = modereport_qty;
      reportTitle = "LAPORAN STOK BULANAN QUANTITY";
    } else if ("{!! $mode_menu !!}" == "RP") {
      g_modeReport = modereport_rp;
      reportTitle = "LAPORAN STOK BULANAN DALAM RUPIAH";
    } else if ("{!! $mode_menu !!}" == "QTYRP") {
      g_modeReport = modereport_qtyrp;
      reportTitle = "LAPORAN STOK BULANAN QTY+RUPIAH";
    } else {
      g_modeReport = modereport_periode;
      reportTitle = "LAPORAN STOK BULANAN QTY+RUPIAH";
    }
  });

  function setDefaultHeader() {
    if (g_modeReport == modereport_qty) {
      gcart_header = [
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['KODEGDG', 'Gdg', 1, 'varchar', 0, 0],
        ['QntAwal', 'Awal', 1, 'float', 1, 2],
        ['QNTPBL', 'Beli', 1, 'float', 1, 2],
        ['QNTRPJ', 'R. Jual', 1, 'float', 1, 2],
        ['QNTADI', 'Adj (+)', 1, 'float', 1, 2],
        ['QNTTRI', 'Tr (+)', 1, 'float', 1, 2],
        ['QNTRPK', 'RPM (+)', 1, 'float', 1, 2],
        ['QntHPrd', 'PRD (+)', 1, 'float', 1, 2],
        ['QNTPNJ', 'Jual', 1, 'float', 1, 2],
        ['QNTRBP', 'R.Beli', 1, 'float', 1, 2],
        ['QNTADO', 'Adj (-)', 1, 'float', 1, 2],
        ['QNTTRO', 'Tr (-)', 1, 'float', 1, 2],
        ['QNTPMK', 'PMK (-)', 1, 'float', 1, 2],
        ['SALDOQNT', 'Akhir', 1, 'float', 1, 2]
      ];
    } else if (g_modeReport == modereport_rp) {
      gcart_header = [
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['KODEGDG', 'Gdg', 1, 'varchar', 0, 0],
        ['HRGAWAL', 'Awal', 1, 'float', 1, 0],
        ['HRGPBL', 'Beli', 1, 'float', 1, 0],
        ['HRGRPJ', 'R. Jual', 1, 'float', 1, 0],
        ['HRGADI', 'Adj (+)', 1, 'float', 1, 0],
        ['HRGTRI', 'Tr (+)', 1, 'float', 1, 0],
        ['HRGRPK', 'RPM (+)', 1, 'float', 1, 0],
        ['HRGHPrd', 'PRD (+)', 1, 'float', 1, 0],
        ['HRGPNJ', 'Jual', 1, 'float', 1, 0],
        ['HRGRBP', 'R.Beli', 1, 'float', 1, 0],
        ['HRGADO', 'Adj (-)', 1, 'float', 1, 0],
        ['HRGTRO', 'Tr (-)', 1, 'float', 1, 0],
        ['HRGPMK', 'PMK (-)', 1, 'float', 1, 0],
        ['SALDORP', 'Akhir', 1, 'float', 1, 0]
      ];
    } else if (g_modeReport == modereport_qtyrp) {
      gcart_header = [
          ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
          ['partNumber', 'Part Number', 1, 'varchar', 0, 0],
          ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
          ['KODEGDG', 'Gdg', 1, 'varchar', 0, 0],
          ['Satuan', 'Sat', 1, 'varchar', 0, 0],
          ['QntAwal', 'So. Awal', 1, 'float', 1, 2],
          ['HRGAWAL', 'So. Awal', 1, 'float', 1, 0],
          ['QNTPBL', 'Pembelian', 1, 'float', 1, 2],
          ['HRGPBL', 'Pembelian', 1, 'float', 1, 0],
          ['QNTRPJ', 'Retur Jual', 1, 'float', 1, 2],
          ['HRGRPJ', 'Retur Jual', 1, 'float', 1, 0],
          ['QNTADI', 'Kor. Msk', 1, 'float', 1, 2],
          ['HRGADI', 'Kor. Msk', 1, 'float', 1, 0],
          ['QNTTRI', 'Trans. Msk', 1, 'float', 1, 2],
          ['HRGTRI', 'Trans. Msk', 1, 'float', 1, 0],
          ['QNTRPK', 'R. Pemakaian', 1, 'float', 1, 2],
          ['HRGRPK', 'R. Pemakaian', 1, 'float', 1, 0],
          ['QNTUKI', 'Ubah Kemasan In', 1, 'float', 1, 2],
          ['HRGUKI', 'Ubah Kemasan In', 1, 'float', 1, 0],
          ['qntrspb', 'Terima dr R.Sjln', 1, 'float', 1, 2],
          ['hrgrspb', 'Terima dr R.Sjln', 1, 'float', 1, 0],
          ['QntHPrd', 'Gd TC dr SJ', 1, 'float', 1, 2],
          ['HRGHPrd', 'Gd TC dr SJ', 1, 'float', 1, 0],
          ['QNTPNJ', 'S.Jalan', 1, 'float', 1, 2],
          ['HRGPNJ', 'S.Jalan', 1, 'float', 1, 0],
          ['qntrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 2],
          ['hrgrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 0],
          ['QNTPRJ', 'HPP', 1, 'float', 1, 2],
          ['HRGPRJ', 'HPP', 1, 'float', 1, 0],
          ['QNTRBP', 'Retur Beli', 1, 'float', 1, 2],
          ['HRGRBP', 'Retur  Beli', 1, 'float', 1, 0],
          ['QNTADO', 'Kor. Klr', 1, 'float', 1, 2],
          ['HRGADO', 'Kor. Klr', 1, 'float', 1, 0],
          ['QNTTRO', 'Trans. Klr', 1, 'float', 1, 2],
          ['HRGTRO', 'Trans. Klr', 1, 'float', 1, 0],
          ['QNTUKO', 'Ubah Kemasan Out', 1, 'float', 1, 2],
          ['HRGUKO', 'Ubah Kemasan Out', 1, 'float', 1, 0],
          ['QNTPMK', 'Pemakaian', 1, 'float', 1, 2],
          ['HRGPMK', 'Pemakaian', 1, 'float', 1, 0],
          ['SALDOQNT', 'So. Akhir', 1, 'float', 1, 2],
          ['SALDORP', 'So. Akhir', 1, 'float', 1, 0]
        ];
    } else {
      gcart_header = [
          ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
          ['partNumber', 'Part Number', 1, 'varchar', 0, 0],
          ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
          ['KODEGDG', 'GD', 1, 'varchar', 0, 0],
          ['Satuan', 'Sat', 1, 'varchar', 0, 0],
          ['QntAwal', 'So. Awal', 1, 'float', 1, 2],
          ['HRGAWAL', 'So. Awal', 1, 'float', 1, 0],
          ['QNTPBL', 'Pembelian', 1, 'float', 1, 2],
          ['HRGPBL', 'Pembelian', 1, 'float', 1, 0],
          ['QNTRPJ', 'Retur Jual', 1, 'float', 1, 2],
          ['HRGRPJ', 'Retur Jual', 1, 'float', 1, 0],
          ['QNTADI', 'Kor. Msk', 1, 'float', 1, 2],
          ['HRGADI', 'Kor. Msk', 1, 'float', 1, 0],
          ['QNTTRI', 'Trans. Msk', 1, 'float', 1, 2],
          ['HRGTRI', 'Trans. Msk', 1, 'float', 1, 0],
          ['QNTRPK', 'R. Pemakaian', 1, 'float', 1, 2],
          ['HRGRPK', 'R. Pemakaian', 1, 'float', 1, 0],
          ['QNTUKI', 'Ubah Kemasan In', 1, 'float', 1, 2],
          ['HRGUKI', 'Ubah Kemasan In', 1, 'float', 1, 0],
          ['qntrspb', 'Terima dr R.Sjln', 1, 'float', 1, 2],
          ['hrgrspb', 'Terima dr R.Sjln', 1, 'float', 1, 0],
          ['QntHPrd', 'Gd TC dr SJ', 1, 'float', 1, 2],
          ['HRGHPrd', 'Gd TC dr SJ', 1, 'float', 1, 0],
          ['QNTPNJ', 'S.Jalan', 1, 'float', 1, 2],
          ['HRGPNJ', 'S.Jalan', 1, 'float', 1, 0],
          ['qntrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 2],
          ['hrgrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 0],
          ['QNTPRJ', 'HPP', 1, 'float', 1, 2],
          ['HRGPRJ', 'HPP', 1, 'float', 1, 0],
          ['QNTRBP', 'Retur Beli', 1, 'float', 1, 2],
          ['HRGRBP', 'Retur  Beli', 1, 'float', 1, 0],
          ['QNTADO', 'Kor. Klr', 1, 'float', 1, 2],
          ['HRGADO', 'Kor. Klr', 1, 'float', 1, 0],
          ['QNTTRO', 'Trans. Klr', 1, 'float', 1, 2],
          ['HRGTRO', 'Trans. Klr', 1, 'float', 1, 0],
          ['QNTUKO', 'Ubah Kemasan Out', 1, 'float', 1, 2],
          ['HRGUKO', 'Ubah Kemasan Out', 1, 'float', 1, 0],
          ['QNTPMK', 'Pemakaian', 1, 'float', 1, 2],
          ['HRGPMK', 'Pemakaian', 1, 'float', 1, 0],
          ['SALDOQNT', 'So. Akhir', 1, 'float', 1, 2],
          ['SALDORP', 'So. Akhir', 1, 'float', 1, 0]
      ];
    }

    gsum_issubtotal = 0; gsum_isgrandtotal = 0;
  }
  
  function setRowHeader(_rowHeader) {
    if (g_modeReport == modereport_qty || g_modeReport == modereport_rp) {
      return setRowHeaderQtyOrRp(_rowHeader);
    } else {
      return setRowHeaderQtyRp(_rowHeader);
    }
  }
  
  function setRowHeaderQtyOrRp(_rowHeader) {
    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Kode Barang</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Nama Barang</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Sat</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Gdg</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Awal</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Beli</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">R. Jual</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Adj (+)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Tr (+)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">RPM (+)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">PRD (+)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Jual</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">R.Beli</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Adj (-)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Tr (-)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">PMK (-)</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Akhir</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    let _thopen = "", _thclose = "</th>";

    // FIRST ROW
    _rowHeader += '<tr style="height: 45px; padding: 20px;" class="text-center bg-dark text-light">';
    _thopen = '<th rowspan="3" scope="col" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">';
    _rowHeader += _thopen + 'Kode Barang' + _thclose;
    _rowHeader += _thopen + 'Nama Barang' + _thclose;
    _rowHeader += _thopen + 'Part Number' + _thclose;
    _rowHeader += _thopen + 'Merk' + _thclose;
    _rowHeader += _thopen + 'Gdg' + _thclose;
    _rowHeader += _thopen + 'Sat' + _thclose;

    _rowHeader += '<th colspan="2" rowspan="2" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">So. Awal</th>';
    _rowHeader += '<th colspan="16" style="border: 1px solid black; white-space:nowrap;">Masuk</th>';
    _rowHeader += '<th colspan="16" style="border: 1px solid black; white-space:nowrap;">Keluar</th>';
    _rowHeader += '<th colspan="2" rowspan="2" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">So. Akhir</th>';

    _rowHeader += '</tr>';

    // SECOND ROW
    _rowHeader += '<tr class="text-center bg-dark text-light">';

    const masukKeluarLabels = [
        'Pembelian',    'Retur Jual',      'Kor. Msk',         'Trans. Msk', 
        'R. Pemakaian', 'Ubah Kemasan In', 'Terima dr R.Sjln', 'Gd TC dr SJ',

        'S. Jalan', 'Retur Sjln dr GTC', 'HPP',              'Retur Beli', 
        'Kor. Klr', 'Trans. Klr',        'Ubah Kemasan Out', 'Pemakaian'
    ];
    for (let i = 0; i < masukKeluarLabels.length; i++) {
        _rowHeader += `<th colspan="2" scope="col" style="border: 1px solid black; white-space:nowrap;">${masukKeluarLabels[i]}</th>`;
    }

    _rowHeader += '</tr>';

    // THIRD ROW
    _rowHeader += '<tr class="text-center bg-dark text-light">';

    let _qtyrp = '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Qty</th>';
    _qtyrp += '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Rp.</th>';
    _rowHeader += _qtyrp.repeat(1 + 8 + 8 + 1);

    _rowHeader += '</tr>';

    return _rowHeader;
  }
  
  function getRowFooter1(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    let _decimal = (gcart_header.find(row => row[0] === _col) || [])[5];

    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + format_number(_sum, _decimal) + '</td>';
  }
  
  function getRowFooter2(_col, _colspanRow2) {
    let _sum = gcart_res.filter(item => currencyNormalizer(item[_col]) !== 0).length;
    let _str = '  <td colspan="' + _colspanRow2 + '" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + _sum + '</td>'

    return { _sum, _str };
  }
  
  function setRowFooter() {
    let rowFooter1 = "", rowFooter2 = "";
    let colspanRow2 = 1;

    if (g_modeReport == modereport_qty || g_modeReport == modereport_rp) {
      rowFooter1 += "<tr style='text-align: center'>";
      rowFooter1 += '  <td colspan="3" class="cellcompact-center" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">Total Item : ' + gcart_res.length + '</td>';
      rowFooter1 += '  <td class="cellcompact-left" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">Total</td>';

      rowFooter2 += "<tr style='text-align: center'>";
      rowFooter2 += '  <td colspan="4" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">Jumlah Item</td>';
    } else {
      colspanRow2 = 2;
      rowFooter1 += "<tr style='text-align: center'>";
      rowFooter1 += '  <td colspan="6" class="cellcompact-center" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">Total</td>';

      rowFooter2 += "<tr style='text-align: center'>";
      rowFooter2 += '  <td colspan="6" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + gcart_res.length + ' Jumlah Item</td>';
    }

    let _if1 = (g_modeReport != modereport_rp);
    let _if2 = (g_modeReport != modereport_qty);

    let tempFooter2;
    let tot_masuk = 0, tot_keluar = 0, tos = 0;

    // === MASUK === //

    // AWAL
    rowFooter1 += (_if1) ? getRowFooter1("QntAwal") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGAWAL") : '';
    tempFooter2 = getRowFooter2("QntAwal", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // BELI
    rowFooter1 += (_if1) ? getRowFooter1("QNTPBL") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGPBL") : '';
    tempFooter2 = getRowFooter2("QNTPBL", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // RETUR JUAL
    rowFooter1 += (_if1) ? getRowFooter1("QNTRPJ") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGRPJ") : '';
    tempFooter2 = getRowFooter2("QNTRPJ", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // ADJ (+)
    rowFooter1 += (_if1) ? getRowFooter1("QNTADI") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGADI") : '';
    tempFooter2 = getRowFooter2("QNTADI", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // TR (+)
    rowFooter1 += (_if1) ? getRowFooter1("QNTTRI") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGTRI") : '';
    tempFooter2 = getRowFooter2("QNTTRI", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // RPM (+)
    rowFooter1 += (_if1) ? getRowFooter1("QNTRPK") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGRPK") : '';
    tempFooter2 = getRowFooter2("QNTRPK", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Ubah Kemasan In
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      rowFooter1 += getRowFooter1("QNTUKI");
      rowFooter1 += getRowFooter1("HRGUKI");
      tempFooter2 = getRowFooter2("QNTUKI", colspanRow2);
      rowFooter2 += tempFooter2._str;
      tot_masuk  += tempFooter2._sum;
    }

    // Terima dr R.Sjln
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      rowFooter1 += getRowFooter1("qntrspb");
      rowFooter1 += getRowFooter1("hrgrspb");
      tempFooter2 = getRowFooter2("qntrspb", colspanRow2);
      rowFooter2 += tempFooter2._str;
      // tot_masuk  += tempFooter2._sum;
    }

    // PRD (+)
    rowFooter1 += (_if1) ? getRowFooter1("QntHPrd") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGHPrd") : '';
    tempFooter2 = getRowFooter2("QntHPrd", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // === KELUAR === //

    // JUAL
    rowFooter1 += (_if1) ? getRowFooter1("QNTPNJ") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGPNJ") : '';
    tempFooter2 = getRowFooter2("QNTPNJ", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // Retur Sjln dr GTC
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      rowFooter1 += getRowFooter1("qntrgtc");
      rowFooter1 += getRowFooter1("hrgrgtc");
      tempFooter2 = getRowFooter2("qntrgtc", colspanRow2);
      rowFooter2 += tempFooter2._str;
      tot_masuk  += tempFooter2._sum;
    }

    // HPP
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      rowFooter1 += getRowFooter1("QNTPRJ");
      rowFooter1 += getRowFooter1("HRGPRJ");
      tempFooter2 = getRowFooter2("QNTPRJ", colspanRow2);
      rowFooter2 += tempFooter2._str;
      // tot_masuk  += tempFooter2._sum;
    }

    // RETUR BELI
    rowFooter1 += (_if1) ? getRowFooter1("QNTRBP") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGRBP") : '';
    tempFooter2 = getRowFooter2("QNTRBP", colspanRow2);
    rowFooter2 += tempFooter2._str;
    // tot_keluar += tempFooter2._sum;

    // ADJ (-)
    rowFooter1 += (_if1) ? getRowFooter1("QNTADO") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGADO") : '';
    tempFooter2 = getRowFooter2("QNTADO", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // TR (-)
    rowFooter1 += (_if1) ? getRowFooter1("QNTTRO") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGTRO") : '';
    tempFooter2 = getRowFooter2("QNTTRO", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // Ubah Kemasan Out
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      rowFooter1 += getRowFooter1("QNTUKO");
      rowFooter1 += getRowFooter1("HRGUKO");
      tempFooter2 = getRowFooter2("QNTUKO", colspanRow2);
      rowFooter2 += tempFooter2._str;
      tot_masuk  += tempFooter2._sum;
    }

    // PMK (-)
    rowFooter1 += (_if1) ? getRowFooter1("QNTPMK") : '';
    rowFooter1 += (_if2) ? getRowFooter1("HRGPMK") : '';
    tempFooter2 = getRowFooter2("QNTPMK", colspanRow2);
    rowFooter2 += tempFooter2._str;
    tot_keluar += tempFooter2._sum;

    // AKHIR
    rowFooter1 += (_if1) ? getRowFooter1("SALDOQNT") : '';
    rowFooter1 += (_if2) ? getRowFooter1("SALDORP") : '';
    tempFooter2 = getRowFooter2("SALDOQNT", colspanRow2);
    rowFooter2 += tempFooter2._str;
    // tot_keluar += tempFooter2._sum;

    rowFooter1 += "</tr>";
    rowFooter2 += "</tr>";

    let rowTOS = "";
    let _style = 'style="border: none !important; outline: none !important;"';
    colspanRow2 = (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) ? (5 + 2 + 16 + 16 + 2) : (3 + 1 + 6 + 5 + 1);
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';

    tot_masuk = (tot_masuk !== 0) ? tot_masuk : 1;
    tos = format_number((tot_keluar / tot_masuk) * 100, 2);
    tot_masuk = (gcart_res.length != 0) ? tot_masuk : 0;
    let _td = '<td ' + _style + '></td>';
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Item masuk : ' + tot_masuk + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Item keluar : ' + tot_keluar + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow2 + '" class="cellcompact-left" ' + _style + '>Turn over stock : ' + tos + ' %</td></tr>'
    
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow2+1) + '" ' + _style + '></td></tr>';

    return rowFooter1 + rowFooter2 + rowTOS;
  }

  function makeTable (_mode, _callback) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "KODEBRG";
    let _date1  = $("#inputDate1").val();
    let _date2  = (g_modeReport == modereport_periode) ? $("#inputDate2").val() : null;

    let temp_href = g_href;
    g_href = 'laporanstockmutasistock';

    let data = {
      date1            : _date1,
      inputGudang      : $("#inputGudang").val(),
      inputIsi         : $("#inputIsi").val(),
      inputStockMinus  : $('#inputStockMinus').prop('checked') ? 1 : 0,
      inputGrup        : $("#inputGrup").val(),
      inputKategori    : $("#inputKategori").val(),
      inputSubKategori : $("#inputSubKategori").val(),
      inputMerk        : $("#inputMerk").val(),
      inputJenis       : $("#inputJenis").val(),
      date2            : _date2,
      modeMenu         : g_modeReport,
    };

    // doMakeTable() sudah async (lihat catatan di masterreportGudang.blade.php); teruskan
    // _callback supaya mode FILTER (dipanggil doShowFormFilterData() di layout) tetap tahu
    // kapan gcart_filter sudah terisi.
    doMakeTable(_mode, groupby, data, reportTitle, _date1, null, _callback);

    g_href = temp_href;
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
    data = ['KODEBRG', 'NAMABRG'];

    return data;
  }

</script>

@endsection