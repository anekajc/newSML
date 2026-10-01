@extends('masterreport')

@section('reportname')
      @if ($mode_menu == 'QTY')
        <h3>Kartu Stock (Qty)</h3>
      @else
        <h3>Kartu Stock (Qty + Rp)</h3>
      @endif
@endsection


@section('input')
                <div class="row" style="display: flex; justify-content: center;" hidden>
                  <div class="col-6 btn-primary text-center tombol-toggle" id="buttonMode0" onclick="doReportMode(0)">No Bukti</div>
                  <div class="col-6 btn-outline-primary text-center tombol-toggle" id="buttonMode1" onclick="doReportMode(1)">Kode Barang</div>
                </div>

                <br>
                
                <div class="row rounded" style="background-color: #E8E8E8; padding: 10px; display: flex; justify-content: center;">
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate1" style="">Periode</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="month" class="form-control" id="inputDate1" value="{!! date('Y-m') !!}">
                  </div>
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate2">s/d</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="month" class="form-control" id="inputDate2" value="{!! date('Y-m') !!}">
                  </div>
                </div>

                <div class="row text-center mt-4" style="justify-content:center;">
                  <div class="col-2">
                    <label for="inputGudang">Gudang</label>
                  </div>
                  <div class="col-2">
                    <input type="text" id="inputGudang" class="form-control" aria-label="Default input example" placeholder="-" onfocus="doSetInputBrowseMaster('inputGudang', '{!! $gudang !!}')" onblur="doBlurInputBrowseMaster()" value ="-">
                  </div>
                  <button type="button" class="btn btn-primary col-1" style="font-size: 14px; margin: 0 5px;" onclick="doBrowseMaster('inputGudang', '{!! $gudang !!}')"><i class="bi bi-search"></i></button>

                  <div class="col-2">
                    <label for="inputBarang">Barang</label>
                  </div>
                  <div class="col-2">
                    <input type="text" id="inputBarang" class="form-control" aria-label="Default input example" placeholder="-" onfocus="doSetInputBrowseMaster('inputBarang', '{!! $barang !!}', true)" onblur="doBlurInputBrowseMaster()" value ="-">
                  </div>
                  <button type="button" class="btn btn-primary col-1" style="font-size: 14px; margin: 0 5px;" onclick="doBrowseMaster('inputBarang', '{!! $barang !!}', true)"><i class="bi bi-search"></i></button>
                </div>

                <div class="row text-center mt-4" style="justify-content:center;">
                  <div class="col-2">
                    <label for="inputIsi">No Satuan</label>
                  </div>
                  <div class="col-1">
                    <input type="number" id="inputIsi" class="form-control" aria-label="Default input example" value ="1">
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
  var modereport_qty = 0, modereport_qtyrp = 1;
  g_modeReport = modereport_qty;
  
  var reportTitle = "";

  $(document).ready(function(){
    $("#gButtonCustomizeTable").hide();
    if ("{!! $mode_menu !!}" == "QTY") {
      g_modeReport = modereport_qty;
      reportTitle = "LAPORAN KARTU STOK";
    } else {
      g_modeReport = modereport_qtyrp;
      reportTitle = "LAPORAN KARTU STOK";
    }  });

  function setDefaultHeader() {
    if (g_modeReport == modereport_qty) {
      gcart_header = [
        ['Tanggal', 'TANGGAL', 1, 'date', 0, 0],
        ['NoBukti', 'NO. BUKTI', 1, 'varchar', 0, 0],
        ['Tipe', 'TIPE', 1, 'varchar', 0, 0],
        ['Keterangan', 'KETERANGAN', 1, 'varchar', 0, 0],
        ['SATUAN', 'SAT', 1, 'int', 0, 0],
        ['QntDB', 'MASUK', 1, 'float', 1, 2],
        ['QntCR', 'KELUAR', 1, 'float', 1, 2],
        ['QntSaldo', 'SALDO', 1, 'sumfloat', 0, 2]
      ];
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tipe', 'Tipe', 1, 'varchar', 0, 0],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Sat', 1, 'int', 0, 0],
        ['HPP', 'HPP', 1, 'float', 0, 2],
        ['QntDB', 'MASUK', 1, 'float', 1, 2],
        ['HrgDebet', 'MASUK', 1, 'float', 1, 2],
        ['QntCR', 'KELUAR', 1, 'float', 1, 2],
        ['HrgKredit', 'KELUAR', 1, 'float', 1, 2],
        ['QntSaldo', 'SALDO', 1, 'sumfloat', 0, 2],
        ['HrgSaldo', 'SALDO', 1, 'sumfloat', 0, 2]
      ];
    }

    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }
  
  function setRowHeader(_rowHeader) {
    if (g_modeReport == modereport_qty) {
      return setRowHeaderQty(_rowHeader);
    } else {
      return setRowHeaderQtyRp(_rowHeader);
    }
  }
  
  function setRowHeaderQty(_rowHeader) {
    _rowHeader += getRowGudangBarangLokasi();
    _rowHeader += doSetRowHeaderDefault(gcart_header);

    return _rowHeader;
  }
  
  function setRowHeaderQtyRp(_rowHeader) {
    _rowHeader += getRowGudangBarangLokasi();
    _rowHeader += doSetRowHeaderDefault(gcart_header);

    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    _rowHeader += getRowGudangBarangLokasi();
    let _thopen = "", _thclose = "</th>";

    // FIRST ROW
    _rowHeader += '<tr style="height: 45px; padding: 20px;" class="text-center bg-dark text-light">';
    _thopen = '<th rowspan="2" scope="col" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">';
    _rowHeader += _thopen + 'Tanggal' + _thclose;
    _rowHeader += _thopen + 'No. Bukti' + _thclose;
    _rowHeader += _thopen + 'Tipe' + _thclose;
    _rowHeader += _thopen + 'Keterangan' + _thclose;
    _rowHeader += _thopen + 'Sat' + _thclose;
    _rowHeader += _thopen + 'HPP' + _thclose;

    _rowHeader += '<th colspan="2" style="border: 1px solid black; white-space:nowrap;">Masuk</th>';
    _rowHeader += '<th colspan="2" style="border: 1px solid black; white-space:nowrap;">Keluar</th>';
    _rowHeader += '<th colspan="2" style="border: 1px solid black; white-space:nowrap;">Saldo</th>';

    _rowHeader += '</tr>';

    // SECOND ROW
    _rowHeader += '<tr class="text-center bg-dark text-light">';

    let _qtyrp = ""
    _qtyrp += '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Quantity</th>';
    _qtyrp += '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Rupiah</th>';
    _rowHeader += _qtyrp.repeat(3);

    _rowHeader += '</tr>';

    return _rowHeader;
  }
  
  function getRowGudangBarangLokasi() {
    let _url, _kode, _barang;
    let _colspan = (g_modeReport == modereport_qty) ? 8 : 12;
    let _row1 = "", _row2 = "";
    const _jarak = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    // GUDANG
    _url = "{!! url('functionbrowse_doLoadGudang') !!}";
    _kode = $("#inputGudang").val();

    $.ajax({
      url     : _url,
      type    : "get",
      async   : false,
      data    : {
        kode : _kode
      },
      success: function(res) {
        _row1 = "Gudang : "
        if (res && res.length > 0) {
            _row1 += res[0].KODEGDG;
        }
      }
    })

    // BARANG
    _url = "{!! url('functionbrowse_doLoadBarang') !!}";
    _kode = $("#inputBarang").val();

    $.ajax({
      url     : _url,
      type    : "get",
      async   : false,
      data    : {
        kode : _kode
      },
      success: function(res) {
        _row2 = "Barang : "
        if (res && res.length > 0) {
            _barang = res;
            _row2 += res[0].KODEBRG + _jarak + res[0].NAMABRG;
        }
      }
    })

    _row1 += _jarak + "Lokasi : "
    if (_barang && _barang.length > 0) {
      $.ajax({
        url     : _url,
        type    : "get",
        async   : false,
        data    : {
          kode : _barang[0].KODEBRG
        },
        success: function(res) {
          if (res && res.length > 0) {
              _row1 += nullToEmpty(res[0].KETERANGAN);
          }
        }
      })
    }

    let _rowHeader = "";
    _rowHeader += '<tr>'
    _rowHeader += '  <th colspan="' + _colspan + '" style="text-align: left;">' + _row1 + '</th>';
    _rowHeader += '</tr>';
    _rowHeader += '<tr>'
    _rowHeader += '  <th colspan="' + _colspan + '" style="text-align: left;">' + _row2 + '</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "NoBukti";
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();

    let temp_href = g_href;
    g_href = 'laporanstockkartustock';

    let data = {
      date1            : _date1,
      date2            : _date2,
      inputGudang      : $("#inputGudang").val(),
      inputBarang      : $("#inputBarang").val(),
      inputIsi         : $("#inputIsi").val(),
    };

    doMakeTable(_mode, groupby, data, "LAPORAN STOK FISIK GUDANG", _date1, _date2);

    doRenameGrandTotal("Total ", galign_right);

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
    data = ['NoBukti', 'Tanggal'];

    return data;
  }

</script>

@endsection