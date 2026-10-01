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

@include('report.modalMarketingSO')

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
          {{-- <button class="btn btn-primary btn-sm mt-2 w-100" onclick="showPeriode()">Terapkan</button> --}}
        </div>
      </div> 

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Report Mode" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rekap</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setReportMode('0')">Detail</a></li>
        </ul>
      </div>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOtorisasi" data-bs-toggle="dropdown" aria-expanded="false" title="Otorisasi" style="cursor: pointer;">
          <i class="fas fa-key"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOtorisasi" aria-labelledby="inputOtorisasi">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOtorisasi('0')">Non Otorisasi</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOtorisasi('1')">Otorisasi</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setOtorisasi('2')">Semua</a></li>
        </ul>
      </div>

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

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder" style="max-height: 125px; overflow-y: auto;">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="B" onclick="setOrderBy('B')">Nomor Barang</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="C" onclick="setOrderBy('C')">Nomor Customer</a></li>
        </ul>
      </div>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 600px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->

            <div class="row text-center">
              <div class="col-2">
                <label for="inputlokasi">Customer</label>
              </div>
              
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputCustomer" placeholder="Customer" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectCustomer')">+</button>
                  </div>
              </div>

              <div class="col-2">
                <label for="inputlokasi">Group</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputGroup" placeholder="Group" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectGroup')">+</button>
                  </div>
              </div>

            </div>

            <div class="row text-center" style='margin-top:5px;'>
              <div class="col-2">
                <label for="inputlokasi">PIC</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputPIC" placeholder="PIC" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectPIC')">+</button>
                  </div>
              </div>
              
              <div class="col-2">
                <label for="inputlokasi">Kategori</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputKategori" placeholder="Kategori" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectKategori')">+</button>
                  </div>
              </div>
            </div>

            <div class="row text-center" style='margin-top:5px;'>
              <div class="col-2">
                <label for="inputlokasi">Sub-Kat</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputSubKategori" placeholder="Sub-Kategori" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectSubKategori')">+</button>
                  </div>
              </div>
              <div class="col-2">
                <label for="inputlokasi">Merk</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputMerk" placeholder="Merk" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectMerk')">+</button>
                  </div>
              </div>
            </div>

          </li>
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
  let globalAgen = "2";   // default: Agen
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

    setReportMode(globalReportMode);
    setOtorisasi(globalOtorisasi);
    setOrderBy(globalOrderBy);
    setAgen(globalAgen);
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
    let text = (val == '2') ? 'Semua' : (val == '1') ? 'Otorisasi' : 'Non Otorisasi';
    // alertify.success(`Otorisasi: ${text}`);

    // hapus semua centang
    $('#dropdownOtorisasi .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim(); 
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownOtorisasi .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });
  }

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

  // order by
  function setOrderBy (val) {

    globalOrderBy = val;
    let text = ''

    if ( val == 'N'){
      text = 'Nomor Bukti'
    } else if ( val == 'B'){
      text = 'Nomor Barang'
    } else if ( val == 'C'){
      text = 'Nomor Customer'
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

  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 0 = Detail, 1 = Rekap
    DetOrRekap = Number(val);    // samakan dengan variabel yang ada di setModeReport

    console.log(val)
    // hapus centang dulu
    $('#dropdownReportMode .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item terpilih
    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });

    // update g_modeReport sesuai pilihan order & detail/rekap
    // setModeReport() sudah mengatur g_modeReport berdasarkan $("#inputOrder").val() dan jenisreport/DetOrRekap
    setModeReport();
  }
  
  var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2 ;
  var modereport_rekapnobukti = 3, modereport_rekapbarang = 4, modereport_rekapcustomer = 5 ;
  g_modeReport = modereport_detailnobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoSPB', 'PO. Cust', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
        ['NoPO', 'PO Customer', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['qnt', 'Qnt', 1, 'varchar', 0, 0],
        ['HARGA', 'harga', 1, 'float', 0, 0],
        ['DiscP', 'Disc', 1, 'float', 0, 0],
        ['Valas', 'DPP VLS', 1, 'float', 1, 0],
        ['NDPP', 'Nilai DPP', 1, 'float', 1, 0],
        ['NPPNRP', 'Nilai PPN', 1, 'float', 1, 0],
        ['DISCTOT', 'Total', 1, 'float', 1, 0],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 0],
        ['NetW', 'Net W', 1, 'float', 1, 2],
        ['GrossW', 'Gross W', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 0],
        ['NetW', 'Net W', 1, 'float', 1, 2],
        ['GrossW', 'Gross W', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapbarang){
      gcart_header = [
        ['KodeBrg', 'No Bukti', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Qnt', 'QNT', 1, 'float', 1, 2],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = '';
    let _date1    = $("#inputDate1").val();
    let _date2    = $("#inputDate2").val();
    let inputOto = globalOtorisasi;
    let _inputAgen = globalAgen;
    let _inputCustomer = $("#inputCustomer").val();
    let _inputGroup = $("#inputGroup").val();
    let _inputPIC = $("#inputPIC").val();
    let _inputKategori = $("#inputKategori").val();
    let _inputSubKategori = $("#inputSubKategori").val();
    let _inputMerk = $("#inputMerk").val();
    let input_order = globalOrderBy;

      if (input_order == "N") {
        groupby = 'NoBukti';
      } else if (input_order == "B") {
        groupby = 'KODEBRG';
      } else {
        groupby = 'KodeCustSupp';
      }

      console.log(inputOto, _inputAgen, input_order)

  setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputAgen : _inputAgen,
      inputCustomer : _inputCustomer,
      inputGroup : _inputGroup,
      inputPIC : _inputPIC,
      inputKategori: _inputKategori,
      inputSubKategori: _inputSubKategori,
      inputMerk : _inputMerk,
      inputOto : inputOto,
      inputOrd : input_order,
    };

    doMakeTable(_mode, groupby, data, "REPORT MARKETING POS", _date1, _date2, _inputAgen, _inputCustomer, _inputGroup, _inputPIC, _inputKategori, _inputSubKategori, _inputMerk);
  }


  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N"){
      data = ['NoBukti', 'Tanggal'];
    } else if ($("#inputOrder").val() == "B"){
      data = ['KODEBRG', 'NAMABRG'];
    } else {
      data = ['KodeCustSupp', 'NAMACUSTSUPP'];
    }

    return data;
  }

  function setModeReport() {
    if (globalOrderBy == "N") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailnobukti;
      } else {
        g_modeReport = modereport_rekapnobukti;
      }
    } else if (globalOrderBy == "B") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailbarang;
      } else {
        g_modeReport = modereport_rekapbarang;
      }
    } else {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailcustomer;
      } else {
        g_modeReport = modereport_rekapcustomer;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }

</script>

@endsection
