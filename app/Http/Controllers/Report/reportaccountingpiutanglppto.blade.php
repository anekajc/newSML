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

<!-- warna header -->
  <style>
    table, th, td {
      border: 1px solid black !important;
      border-collapse: collapse !important;
    }

    /* Override semua TH header dari kedua function */
    tr.text-center.bg-dark.text-light th {
      background-color: #646668 !important; /* warna abu muda */
      color: white !important;
      text-align: center !important;
      font-weight: bold !important;
    }

    /* Untuk memastikan nested header juga ikut ke-style */
    .tabel_header_kolom th,
    .tabel_header_kolom tr.text-center.bg-dark.text-light th {
      background-color: #646668 !important;
      color: white !important;
      font-weight: bold !important;
    }
  </style>
<!-- warna header -->

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
      {{-- <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Valas" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">IDR</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setReportMode('2')">$</a></li>
        </ul>
      </div> --}}

      <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle"
                  type="button"
                  id="inputReportMode"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  title="Valas">
            <i class="fas fa-book"></i>
          </button>

          <ul class="dropdown-menu" id="dropdownReportMode">
        <li>
          <a class="dropdown-item"
            data-value="IDR"
            style="cursor:pointer"
            onclick="setReportMode('IDR')">IDR</a>
        </li>
        <li>
          <a class="dropdown-item"
            data-value="$"
            style="cursor:pointer"
            onclick="setReportMode('$')">$</a>
        </li>
      </ul>
        </div>

      <!-- nilai valas default (varchar) -->
      <input type="hidden" id="valas_value" name="valas_value" value="IDR">

      <!-- input valas -->
      <div id="valas_container" style="display:none;">
        <div class="input-group">
        <input type="text"
              id="valas_display"
              class="form-control"
              style="width:100px"
              readonly
              placeholder="Pilih valas">
        
        <div class="input-group-append">
          <button type="button"
                  class="btn btn-primary btn-select"
                  style="height:31px;"
                  onclick="buttonSelectValas()">+</button>
        </div>
        </div>
      </div>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Perkiraan">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 600px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->
            <div class="row text-center">
              <div class="col-2" style="margin-top:5px;">
                <label for="inputPerkiraan">Perkiraan</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputPerkiraan" placeholder="Perkiraan" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectPerkiraan()">+</button>
                  </div>
              </div>

              <div class="col-2">
                <label for="inputsuppawal">Supplier Awal</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputSuppAwal" placeholder="Supplier" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectSuppAwal()">+</button>
                  </div>
              </div>

              <div class="col-2">
                <label for="inputsuppakhir">Supplier Akhir</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputSuppAkhir" placeholder="Supplier" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectSuppAkhir()">+</button>
                  </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOrderBy('0')">Tanggal</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOrderBy('1')">No.Nota</a></li>
        </ul>
      </div>
      <input type="hidden" id="inputOrd" name="inputOrd" value="0">
    </div>

    <!-- Kanan: tombol aksi menempel ke ujung kanan layar -->
    <div class="d-flex ms-auto" style="gap: 8px;">
      {{-- <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button> --}}
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormCustomizeTable()" title="Customize Table">
        <i class="fas fa-cog"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="makeTable('REPORT')" title="Submit">
        <i class="fas fa-check"></i>
      </button>
    </div>
  </div>

<!-- start modal aktiva select perkiraan -->
  <div class="modal fade"  id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Divisi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectPerkiraan" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Perkiraan</th>
                <th scope="col">Keterangan</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectPerkiraan" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>


          </table>


      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select perkiraan-->

<!-- start modal aktiva select valas -->
  <div class="modal fade"  id="formSelectValas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Valas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectValas" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Valas</th>
                <th scope="col">Keterangan</th>
                <th scope="col">Kurs</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectValas" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>
          </table>

      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select valas-->

<!-- start modal aktiva select supplier awal -->
  <div class="modal fade"  id="formSelectSuppAwal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select SuppAwal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectSuppAwal" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama</th>
                <th scope="col">Alamat</th>
                <th scope="col">Telpon</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectSuppAwal" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>
          </table>

      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select supplier awal-->

<!-- start modal aktiva select supplier akhir -->
  <div class="modal fade"  id="formSelectSuppAkhir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select SuppAkhir</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectSuppAkhir" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama</th>
                <th scope="col">Alamat</th>
                <th scope="col">Telpon</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectSuppAkhir" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>
          </table>

      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select supplier akhir-->
@endsection

@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let g_reportTitle = "";
  let g_inputPerkiraan = "";
  let g_inputKeterangan = ""; 
  let globalOrderBy = "0";   // default: tanggal
  let globalReportMode = "IDR"; // default: Detail
  // let uiReportMode = 'IDR';

  $(document).ready(function () {
    $("#btnCustomizeTable").on("click", function () {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });
    
    $("#btnSubmitReport").on("click", function () {
      makeTable("REPORT");
    });

    setReportMode(globalReportMode);
    setOrderBy(globalOrderBy);
    showPeriode();

    $("#inputPerkiraan").val("");

    setTimeout(() => {
        makeTable("REPORT");
    }, 100);
  });

  function buttonPilihPerkiraan(selectedPerkiraan) {
    $("#inputPerkiraan").val(selectedPerkiraan);
    $("#formSelectPerkiraan").modal("hide");
  }

  function setReportMode(val) {
  globalReportMode = val;

    if (val === 'IDR') {
      jenisreport = 0;
      DetOrRekap  = 0;

      // set IDR
      $('#valas_value').val('IDR');
      $('#valas_container').hide();
      $('#valas_display').val('');

    } else {
      // mode $
      jenisreport = 1;
      DetOrRekap  = 1;

      $('#valas_value').val('');
      $('#valas_container').show();
      $('#valas_display').val('');
    }

    // hapus centang lama
    $('#dropdownReportMode .dropdown-item .checkmark-red').remove();

    // tambah centang
    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`)
      .append(' <span class="checkmark-red">✔</span>');

    setModeReport();
  }

   // order by
  function setOrderBy(val) {
    globalOrderBy = val;

    $("#inputOrd").val(val);

    // hapus semua centang
    $('#dropdownOrder .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item yg dipilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });
  }

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  var modereport_detail = 'IDR', modereport_rekap = '$';
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    console.log('setDefaultHeader');
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['kode', 'Kode', 1, 'varchar', 0, 0],
        ['nama', 'Nama', 1, 'varchar', 0, 0],
        ['AkhirThLalu', 'Saldo Awal', 1, 'float', 1, 2],
        ['Jumlah', 'Penjualan', 1, 'float', 1, 2],

        ['pelunasan', 'Pelunasan', 1, 'float', 1, 2],
        ['retur', 'Retur', 1, 'float', 1, 2],
        ['saldoakhir', 'Saldo Akhir', 1, 'float', 1, 2],

        ['titip', 'Titipan', 1, 'float', 1, 2],
        ['akhir', 'Saldo', 1, 'float', 1, 2],
        ['RTPiutang', 'RT PT', 1, 'float', 1, 2],
        ['RTPenjualan', 'RT Jual', 1, 'float', 1, 2],
        ['RTO', 'TO', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    } else {
      gcart_header = [
        ['kode', 'Kode', 1, 'varchar', 0, 0],
        ['nama', 'Nama', 1, 'varchar', 0, 0],
        ['AkhirThLalu', 'Saldo Awal', 1, 'float', 1, 2],
        ['Jumlah', 'Penjualan', 1, 'float', 1, 2],

        ['pelunasan', 'Pelunasan', 1, 'float', 1, 2],
        ['retur', 'Retur', 1, 'float', 1, 2],
        ['saldoakhir', 'Saldo Akhir', 1, 'float', 1, 2],

        ['titip', 'Titipan', 1, 'float', 1, 2],
        ['akhir', 'Saldo', 1, 'float', 1, 2],
        ['RTPiutang', 'RT PT', 1, 'float', 1, 2],
        ['RTPenjualan', 'RT Jual', 1, 'float', 1, 2],
        ['RTO', 'TO', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
    console.log('coba');
    console.log(gcart_header);
  }

  function setRowHeader() {
    let rowHeader = "";
    rowHeader += '<tr>';
    rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold">' +
      '{!! $akses["program"] !!}<br/>' + g_reportTitle +
    '</th>';
    rowHeader += '</tr>';
    
    if (globalDate1 && globalDate1 !== 'undefined' && globalDate1 !== '') {
      rowHeader += '<tr>';
      rowHeader += '  <th colspan="15" style="text-align: left; font-weight: bold;">PERIODE: ' +
        format_date(globalDate1, true) +
        ((globalDate2 == null || globalDate2 === '' || globalDate2 === 'undefined')
            ? ''
            : ' S.D ' + format_date(globalDate2, true)) +
      '</th>';
      rowHeader += '</tr>';}
    
    rowHeader += '<tr>';
    rowHeader += '  <th colspan="15" style="text-align: left; font-weight: bold;">PERKIRAAN: ' + g_inputPerkiraan + '</th>';
    rowHeader += '</tr>';

    rowHeader += '<tr>';
    rowHeader +=
      '  <th colspan="15" style="text-align: left; font-weight: bold;">Dicetak Oleh : {!! $akses["user"] !!} // Tanggal : '
      + getDateIndo() +
      ' // Jam : ' + getTimeNow() +
      '</th>';
    rowHeader += '</tr>';
    rowHeader += '<tr><th colspan="15"></th></tr>';

    if (g_modeReport == modereport_detail) {
      rowHeader = setRowHeaderQtyOrRp(rowHeader);
    } else {
      rowHeader = setRowHeaderQtyRp(rowHeader);
    }

    return rowHeader;
  }

  function setRowHeaderQtyOrRp(_rowHeader) {
    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Kode</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Nama</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo Awal</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Penjualan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Pelunasan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Retur</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo Akhir</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Titipan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">RT PT</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">RT Jual</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">TO</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Kode</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Nama</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo Awal</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Penjualan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Pelunasan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Retur</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo Akhir</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Titipan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Saldo</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">RT PT</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">RT Jual</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">TO</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function makeTable (_mode) { 
    globalDate1 = $("#inputDate1").val();
    globalDate2 = $("#inputDate2").val();
    g_reportTitle = "REPORT ACCOUNTING PIUTANG LAPORAN LPP TO";
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = 'nama';    
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();
    let _inputPerkiraan = $("#inputPerkiraan").val();
    let _inputSuppAwal  = $("#inputSuppAwal").val();
    let _inputSuppAkhir = $("#inputSuppAkhir").val();
    let _inputOrd       = $("#inputOrd").val();
    let _valasValue     = $("#valas_value").val();
    

    if (!_inputPerkiraan){
      _inputPerkiraan = '-'
    }

    if (!_inputSuppAwal){
      _inputSuppAwal = '-'
    }

    if (!_inputSuppAkhir){
      _inputSuppAkhir = '-'
    }

    let data = {
      date1          : _date1,
      date2          : _date2,
      inputSuppAwal  : _inputSuppAwal,
      inputSuppAkhir : _inputSuppAkhir,
      inputOrd       : _inputOrd,
      inputPerkiraan : _inputPerkiraan,
      valas_value    : _valasValue
    };
    console.log(data);
      doMakeTable(_mode, groupby, data, _date1, _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if (g_modeReport == modereport_detail) {
      data = ['Tanggal', 'nofaktur'];
    } else {
      data = ['Tanggal', 'nofaktur'];
    }
    
    return data;
  }

  function reportMode(_mode) {
    if (jenisreport != _mode) {
      let prev_mode = jenisreport;
      jenisreport != _mode;

      $('#tombolmode' + prev_mode). removeClass ('btn-primary');
      $('#tombolmode' + prev_mode). addClass ('btn-outline-primary');

      $('#tombolmode' + prev_mode). removeClass ('btn-outline-primary');
      $('#tombolmode' + prev_mode). addClass ('btn-primary');

      setModeReport();
    }
  }

  function setModeReport() {
    let valas = $('#valas_value').val();

    if (valas === 'IDR') {
      g_modeReport = modereport_detail;  
    } else {
      g_modeReport = modereport_rekap; 
    }
    console.log('test');
    doSetHeader(g_modeReport, true);
    console.log('test2');
    doShowCustomize();
  }

//   function setReportMode(val) {
//   uiReportMode = val;

//   // GLOBAL FUNCTION (INT)
//   g_modeReport = (val === 'IDR') ? 0 : 1;

//   if (val === 'IDR') {
//     $('#valas_value').val('IDR');
//     $('#valas_container').hide();
//   } else {
//     $('#valas_value').val('USD');
//     $('#valas_container').show();
//   }

//   doSetHeader();
// }


// js modal perkiraan
  function buttonSelectPerkiraan () {
    loadSelectPerkiraan()
    $("#formSelectPerkiraan").modal('toggle')
  }

  function buttonPilihPerkiraan(kode, nama) {
    $("#inputPerkiraan").val(kode);

    // untuk header 
    g_inputPerkiraan = kode + ' - ' + nama;

    $("#formSelectPerkiraan").modal("hide");
  }

  function loadSelectPerkiraan() {
    console.log('asd');
    let _token = $("#_token").val();
    let dataRefresh = [];

    $('#tabelSelectPerkiraan').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingpiutanglppto_loadperkiraan') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectPerkiraan").innerHTML = rowTable;
    $("#tabelSelectPerkiraan").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

// js modal supplier awal
  function buttonSelectSuppAwal () {
    loadSelectSuppAwal()
    $("#formSelectSuppAwal").modal('toggle')
  }

  function buttonPilihSuppAwal(selectedSuppAwal) {
    $("#inputSuppAwal").val(selectedSuppAwal);
    $("#formSelectSuppAwal").modal("hide");

  }

  function loadSelectSuppAwal() {
    console.log('asd');
    let _token = $("#_token").val();
    let dataRefresh = [];

    $('#tabelSelectSuppAwal').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingpiutanglppto_loadsuppawal') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td>${item.Alamat}</td>
        <td>${item.Telpon ?? ''}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihSuppAwal('${item.KodeCustsupp}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectSuppAwal").innerHTML = rowTable;
    $("#tabelSelectSuppAwal").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

// js modal supplier akhir
  function buttonSelectSuppAkhir () {
    loadSelectSuppAkhir()
    $("#formSelectSuppAkhir").modal('toggle')
  }

  function buttonPilihSuppAkhir(selectedSuppAkhir) {
    $("#inputSuppAkhir").val(selectedSuppAkhir);
    $("#formSelectSuppAkhir").modal("hide");

  }

  function loadSelectSuppAkhir() {
    console.log('asd');
    let _token = $("#_token").val();
    let dataRefresh = [];

    $('#tabelSelectSuppAkhir').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingpiutanglppto_loadsuppawal') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td>${item.Alamat}</td>
        <td>${item.Telpon ?? ''}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihSuppAkhir('${item.KodeCustsupp}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectSuppAkhir").innerHTML = rowTable;
    $("#tabelSelectSuppAkhir").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

// js modal valas
  function buttonSelectValas () {
    loadSelectValas()
    $("#formSelectValas").modal('toggle')
  }

    function buttonPilihValas(selectedValas) {
      $('#valas_display').val(selectedValas); 
      $('#valas_value').val(selectedValas);   
      $('#formSelectValas').modal('hide');
  }

  function loadSelectValas() {
    console.log('asd');
    let _token = $("#_token").val();
    let dataRefresh = [];

    $('#tabelSelectValas').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingpiutanglppto_loadvalas') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.Kodevls}</td>
        <td>${item.NamaVls}</td>
        <td>${item.Kurs}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihValas('${item.Kodevls}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectValas").innerHTML = rowTable;
    $("#tabelSelectValas").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

</script>

@endsection