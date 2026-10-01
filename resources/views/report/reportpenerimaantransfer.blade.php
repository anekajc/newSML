@extends('masterreport')

@section('reportname')
      <h3>Report Penerimaan Transfer</h3>
@endsection


@section('input')
                <div class="row" style="display: flex; justify-content: center;">
                  <div class="col-6 btn-primary text-center tombol-toggle" id="buttonMode0" onclick="doReportMode(0)">No Bukti</div>
                  <div class="col-6 btn-outline-primary text-center tombol-toggle" id="buttonMode1" onclick="doReportMode(1)">Kode Barang</div>
                </div>

                <br>
                
                <div class="row rounded" style="background-color: #E8E8E8; padding: 10px; display: flex; justify-content: center;">
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
                </div>

                <div class="row text-center mt-4">
                  <div class="col-3"></div>
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputOtorisasi">Otorisasi</label>
                  </div>
                  <div class="col-5">
                    <select id="inputOtorisasi" class="form-control" aria-label="Default select example">
                      <option value=0>Otorisasi</option>
                      <option value=1>Non Otorisasi</option>
                      <option value=2>Semua</option>
                    </select>
                  </div>
                  <div class="col-2"></div>

                  <div class="col-2" hidden>
                    <label for="inputOrder">Order By</label>
                  </div>
                  <div class="col-4" hidden>
                    <select id="inputOrder" class="form-control" aria-label="Default select example">
                        <option value="N">Nomor Bukti</option>
                        <option value="B">Nomor Barang</option>
                    </select>
                  </div>
                </div>
@endsection

@section('jsreport')
<script type="text/javascript">
  var modereport_nobukti = 0, modereport_barang = 1;
  g_modeReport = modereport_nobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_nobukti) {
      gcart_header = [
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KODEBRG', 'Kode Brg', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NoTransfer', 'No. Transfer', 1, 'varchar', 0, 0],
        ['GdgAsal', 'Gdg Asal', 1, 'varchar', 0, 0],
        ['GdgTujuan', 'Gdg Tujuan', 1, 'varchar', 0, 0],
        ['SAT_1', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    } else {
      gcart_header = [
        ['KODEBRG', 'Kode Brg', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoTransfer', 'No. Transfer', 1, 'varchar', 0, 0],
        ['GdgAsal', 'Gdg Asal', 1, 'varchar', 0, 0],
        ['GdgTujuan', 'Gdg Tujuan', 1, 'varchar', 0, 0],
        ['SAT_1', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = (g_modeReport == modereport_nobukti) ? "nobukti" : "KODEBRG";
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOto : $("#inputOtorisasi").val(),
      inputOrd : (g_modeReport == modereport_nobukti) ? "N" : "B",
    };

    doMakeTable(_mode, groupby, data, "REPORT PENERIMAAN TRANSFER", _date1, _date2);
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

</script>

@endsection