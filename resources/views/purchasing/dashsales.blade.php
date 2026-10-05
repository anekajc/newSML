@extends('master')

@section('css')
<style>
  /* @media only screen and (max-width : 1000px){ #dashboard_part1 {height: auto;} #dashboard_part2 {height: auto;} #part1 {height: 170px !important;} } */
  /* @media only screen and (min-width : 1000.1px){ #dashboard_part1 {height: auto;} #dashboard_part2 {height: auto;} } */
</style>
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Dashboard / Sales Forecast"><span class="blue" id="title_page">Sales</span></a>
</li>
@endsection

@section('content')
<div class="container-fluid" style="height: 100%;">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="row" style="margin-top: 5px;">
    <div class="col-2 col-md-1">
      <table style="width: 100%;height: 100%;font-size: 12pt;">
        <tr><td>Semester: </td></tr>
      </table>
    </div>
    <div class="col-2 col-md-1">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="smt" onchange="toggleSmt()">
        <option value="0">Tidak</option>
        <option value="1">Ya</option>
      </select></td></tr></table>
    </div>
    <div class="col-half"></div>
    <div class="col-2 col-md-1">
      <table style="width: 100%;height: 100%;font-size: 12pt;">
        <tr><td>Periode: </td></tr>
      </table>
    </div>
    <div class="col-2 col-md-1">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="tahun_g1">
        @for($i = 0; $i < count($list_tahun); $i++)
          @if ($list_tahun[$i]->tahun == $tahun)
            <option value="{!! $list_tahun[$i]->tahun !!}" selected>{!! $list_tahun[$i]->tahun !!}</option>
          @else
            <option value="{!! $list_tahun[$i]->tahun !!}">{!! $list_tahun[$i]->tahun !!}</option>
          @endif
        @endfor
      </select></td></tr></table>
    </div>
    <div class="col-1 col-md-1 smt" style="display: none;">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="smt_g1">
        <option value="1">1</option>
        <option value="2">2</option>
      </select></td></tr></table>
    </div>
    <div class="col-md-half">
      <table style="width: 100%;height: 100%;font-size: 12pt; text-align: center;">
        <tr><td>-</td></tr>
      </table>
    </div>
    <div class="col-2 col-md-1">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="tahun_g2">
        <option value="">---</option>
        @for($i = 0; $i < count($list_tahun); $i++)
          <option value="{!! $list_tahun[$i]->tahun !!}">{!! $list_tahun[$i]->tahun !!}</option>
        @endfor
      </select></td></tr></table>
    </div>
    <div class="col-1 col-md-1 smt" style="display: none;">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="smt_g2">
        <option value="1">1</option>
        <option value="2">2</option>
      </select></td></tr></table>
    </div>
    <div class="col-md-half">
      <table style="width: 100%;height: 100%;font-size: 12pt; text-align: center;">
        <tr><td>-</td></tr>
      </table>
    </div>
    <div class="col-2 col-md-1">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="tahun_g3">
        <option value="">---</option>
        @for($i = 0; $i < count($list_tahun); $i++)
          <option value="{!! $list_tahun[$i]->tahun !!}">{!! $list_tahun[$i]->tahun !!}</option>
        @endfor
      </select></td></tr></table>
    </div>
    <div class="col-1 col-md-1 smt" style="display: none;">
      <table style="width: 100%; height: 100%;"><tr><td><select class="form-control" id="smt_g3">
        <option value="1">1</option>
        <option value="2">2</option>
      </select></td></tr></table>
    </div>
    <div class="col-1">
      <button type="button" class="btn btn-round btn-primary" name="button" onclick="loadAllGrafik()">Proses</button>
    </div>
  </div>
  <div class="row" style="width: 100%;">
    <div class="col-12 col-lg-12">
      <div class="card">
        <div class="card-body" id="omsettahunan">
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="width: 100%;">
    <div class="col-12 col-lg-12" style="margin-top: 5px;">
      <div class="card">
        <div class="card-body" style="overflow-x: auto;">
          <table class="table" style="font-size: 11pt;">
            <thead class="text-primary text-center">
              <tr>
                <th rowspan='2' style="vertical-align: middle;">SALES</th>
                <th colspan='3' id="thn-0" class="column-0" style="text-align: center;">{!! $tahun !!}</th>
                <th colspan='3' id="thn-1" class="column-1" style="text-align: center; display: none;"></th>
                <th colspan='3' id="thn-2" class="column-2" style="text-align: center; display: none;"></th>
              </tr>
              <tr>
                <th class="column-0">NON-ADP</th>
                <th class="column-0">ADP</th>
                <th class="column-0">TOTAL</th>
                <th class="column-1" style="display: none;">NON-ADP</th>
                <th class="column-1" style="display: none;">ADP</th>
                <th class="column-1" style="display: none;">TOTAL</th>
                <th class="column-2" style="display: none;">NON-ADP</th>
                <th class="column-2" style="display: none;">ADP</th>
                <th class="column-2" style="display: none;">TOTAL</th>
              </tr>
            </thead>
            <tbody id="data-sales">
              @for ($i = 0; $i < count($list_sales); $i++)
                <tr onclick="showMerk({!!$i!!})" id="tr-{!!$i!!}">
                  <td style="vertical-align: middle;">{!! $list_sales[$i] !!}</td>
                  <td style="vertical-align: middle; text-align:right;" id="td-0-{!!$i!!}-0" class="column-0">{!! number_format($omset_sales[0][$i], 2) !!}</td>
                  <td style="vertical-align: middle; text-align:right;" id="td-0-{!!$i!!}-1" class="column-0">{!! number_format($omset_sales[1][$i], 2) !!}</td>
                  <th style="vertical-align: middle; text-align:right;" id="td-0-{!!$i!!}-2" class="column-0">{!! number_format($omset_sales[2][$i], 2) !!}</th>
                  <td style="vertical-align: middle; display: none; text-align:right;" id="td-1-{!!$i!!}-0" class="column-1"></td>
                  <td style="vertical-align: middle; display: none; text-align:right;" id="td-1-{!!$i!!}-1" class="column-1"></td>
                  <th style="vertical-align: middle; display: none; text-align:right;" id="td-1-{!!$i!!}-2" class="column-1"></th>
                  <td style="vertical-align: middle; display: none; text-align:right;" id="td-2-{!!$i!!}-0" class="column-2"></td>
                  <td style="vertical-align: middle; display: none; text-align:right;" id="td-2-{!!$i!!}-1" class="column-2"></td>
                  <th style="vertical-align: middle; display: none; text-align:right;" id="td-2-{!!$i!!}-2" class="column-2"></th>
                </tr>
              @endfor
              <tr>
                <th style="vertical-align: middle;">TOTAL</th>
                <th style="vertical-align: middle; text-align:right;" id="total-0-0" class="column-0"></th>
                <th style="vertical-align: middle; text-align:right;" id="total-0-1" class="column-0"></th>
                <th style="vertical-align: middle; text-align:right;" id="total-0-2" class="column-0"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-1-0" class="column-1"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-1-1" class="column-1"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-1-2" class="column-1"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-2-0" class="column-2"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-2-1" class="column-2"></th>
                <th style="vertical-align: middle; display: none; text-align:right;" id="total-2-2" class="column-2"></th>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="width: 100%">
    <div class="col-12">
      <div class="card" style="width: 100%;">
        <div class="card-body" id="grafik-perbulan" style="display:none;">
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="width: 100%;">
    <div class="col-12 col-lg-12" id="tabel-perbulan" style="margin-top: 5px; display:none;">
      <div class="card">
        <div class="card-body" style="overflow-x: auto;">
          <table class="table " style="font-size: 11pt;">
            <thead class="text-primary text-center">
              <tr>
                <th rowspan='2' style="vertical-align: middle;">BULAN</th>
                <th colspan='3' id="thn-bulan-0" class="column-0" style="text-align: center;">{!! $tahun !!}</th>
                <th colspan='3' id="thn-bulan-1" class="column-1" style="text-align: center; display: none;"></th>
                <th colspan='3' id="thn-bulan-2" class="column-2" style="text-align: center; display: none;"></th>
              </tr>
              <tr>
                <th class="column-0">NON-ADP</th>
                <th class="column-0">ADP</th>
                <th class="column-0">TOTAL</th>
                <th class="column-1" style="display: none;">NON-ADP</th>
                <th class="column-1" style="display: none;">ADP</th>
                <th class="column-1" style="display: none;">TOTAL</th>
                <th class="column-2" style="display: none;">NON-ADP</th>
                <th class="column-2" style="display: none;">ADP</th>
                <th class="column-2" style="display: none;">TOTAL</th>
              </tr>
            </thead>
            <tbody id="data-perbulan">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="width: 100%;">
    <div class="col-12 col-lg-8 offset-lg-2" id="tabel-merk" style="margin-top: 5px; display:none;">
      <div class="card">
        <div class="card-body" style="overflow-x: auto;">
          <table class="table " style="font-size: 11pt;">
            <thead class="text-primary text-center">
              <tr>
                <th>MERK</th>
                <th id="thn-merk-0" class="column-0">{!! $tahun !!}</th>
                <th id="thn-merk-1" class="column-1" style="display: none;"></th>
                <th id="thn-merk-2" class="column-2" style="display: none;"></th>
              </tr>
            </thead>
            <tbody id="data-merk">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{!! URL::asset('public/js/highcharts.js') !!}"></script>
<script type="text/javascript">
  var new_title = '{!! $tahun !!}', pt = '{!! $pt !!}'; var sales = {!! json_encode($list_kodesls) !!}; var _token = $("#_token").val(); var cek = false;
  $(document).ready(function() {
    $("title").html($("#page_title").html()); hideZero();
  });
  var chart = Highcharts.chart('omsettahunan', {
      chart: {
          type: 'column',
          spacingBottom: 2,
          spacingTop: 2,
          height: 300
      },
      title: {
          text: 'Invoice per sales',
          style: { fontSize: '15px' }
      },
      subtitle: {
          text: ''
      },
      xAxis: {
          categories: {!! json_encode($list_sales) !!},
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: { text: 'Invoice (Rp)' },
          endOnTick: false,
          tickAmount: 5
      },
      lang: {
        thousandsSep: ','
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0; text-align: right;"><b>Rp. {point.y:,.2f},-</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: { pointPadding: 0.2, borderWidth: 0 }
      },
      series: [{
          name: '{!! $tahun !!}',
          data: {!! json_encode($omset_sales[2]) !!}
      }, {
          name: '',
          data: [],
          visible : false
      }, {
          name: '',
          data: [],
          visible : false
      }]
  });

  var chart2 = Highcharts.chart('grafik-perbulan', {
      chart: {
          height: 300
      },
      title: {
          text: 'Invoice per bulan'
      },
      yAxis: {
          title: {
              text: 'Invoice'
          }
      },
      xAxis: {
        tickInterval: 1
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
      },
      plotOptions: {
          series: {
              label: {
                  connectorAllowed: false
              },
              pointStart: 1
          }
      },
      series: [{
          name: '{!! $tahun !!}',
          data: [0, 0, 0, 0, 0, 0]
      }, {
          name: '',
          data: [0]
      }, {
          name: '',
          data: [0]
      }],
      responsive: {
          rules: [{
              condition: {
                  maxWidth: 500
              },
              chartOptions: {
                  legend: {
                      layout: 'horizontal',
                      align: 'center',
                      verticalAlign: 'bottom'
                  }
              }
          }]
      }
  });

  function toggleSmt() {
    if ($("#smt").val() == 0) { $(".smt").hide(); } else { $(".smt").show(); }
    // loadGrafik(0); loadGrafik(1); loadGrafik(2);
  }

  function loadAllGrafik() {
    loadGrafik(0); loadGrafik(1); loadGrafik(2); hideZero();
    $.notify({
      icon: "nc-icon nc-check-2",
      message: "Data telah diupdate"
    }, {
      type: 'success',
      timer: 5000,
      placement: {
        from: 'bottom',
        align: 'left'
      }
    });
  }

  function loadGrafik(index) {
    var tipe = $("#smt").val();
    var tahun = $("#tahun_g"+(parseInt(index)+1)).val();
    var smt = $("#smt_g"+(parseInt(index)+1)).val();
    if (tahun != "") {
      var data = []; graf = [];
      $.ajax({
        url     : "{!! url('getInvoiceSales') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          tipe : tipe,
          smt : smt,
          tahun : tahun,
          sales : sales,
          pt : pt
        },
        success : function(result) {
          data = result;
        }
      });
      chart.series[index].update({name:tahun}, false);
      chart.series[index].setData(data[2], false);
      chart.series[index].setVisible(true, false);
      chart.redraw();
      for (var i = 0; i < sales.length; i++) {
        if (data[0][i] == 0) { $("#td-"+index+"-"+i+"-0").html("0.00"); }
        else { $("#td-"+index+"-"+i+"-0").html(numberWithCommas(round(data[0][i], 2))); }
        if (data[1][i] == 0) { $("#td-"+index+"-"+i+"-1").html("0.00"); }
        else { $("#td-"+index+"-"+i+"-1").html(numberWithCommas(round(data[1][i], 2))); }
        if (data[2][i] == 0) { $("#td-"+index+"-"+i+"-2").html("0.00"); }
        else { $("#td-"+index+"-"+i+"-2").html(numberWithCommas(round(data[2][i], 2))); }
      }
      if (tipe != 0) $("#thn-"+index).html(tahun + " / " + smt);
      else $("#thn-"+index).html(tahun);
      $(".column-"+index).show();
    }
    else {
      chart.series[index].update({name:''}, false);
      chart.series[index].setData([], false);
      chart.series[index].setVisible(false, false);
      chart.redraw();
      $(".column-"+index).hide();
    }
    $('#data-sales > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#tabel-merk").hide(); $("#tabel-perbulan").hide(); $("#grafik-perbulan").hide();
  }

  function showMerk(index) {
    $('#data-sales > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#tr-"+index).css('background-color', 'LightCyan');
    getMerk(index); $("#tabel-merk").show(); $("#tabel-perbulan").show(); $("#grafik-perbulan").show();
    $.notify({
      icon: "nc-icon nc-check-2",
      message: "Data telah diupdate"
    }, {
      type: 'success',
      timer: 5000,
      placement: {
        from: 'bottom',
        align: 'left'
      }
    });
  }

  function getMerk(index) {
    var kodesls = sales[index]; var tipe = $("#smt").val(); var data = [];
    var tahun1 = $("#tahun_g1").val(); var smt1 = $("#smt_g1").val();
    var tahun2 = $("#tahun_g2").val(); var smt2 = $("#smt_g2").val();
    var tahun3 = $("#tahun_g3").val(); var smt3 = $("#smt_g3").val();
    $.ajax({
      url     : "{!! url('getDetailInvoiceMerk') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        tipe : tipe,
        smt1 : smt1,
        tahun1 : tahun1,
        smt2 : smt2,
        tahun2 : tahun2,
        smt3 : smt3,
        tahun3 : tahun3,
        sales : kodesls,
        pt : pt
      },
      success : function(result) {
        var str = ""; var tot1 = 0, tot2 = 0, tot3 = 0;
        for (var i = 0; i < result.length; i++) {
          str += "<tr><td>"+result[i][0]+"</td>\
            <td class='column-0' style='text-align: right;'>"+numberWithCommas(round(result[i][1], 2))+"</td>\
            <td class='column-1' style='text-align: right;'>"+numberWithCommas(round(result[i][2], 2))+"</td>\
            <td class='column-2' style='text-align: right;'>"+numberWithCommas(round(result[i][3], 2))+"</td></tr>";
            tot1 += round(result[i][1], 2); tot2 += round(result[i][2], 2); tot3 += round(result[i][3], 2);
        }
        str += "<tr><th>TOTAL</th>\
            <th class='column-0' style='text-align: right;'>"+numberWithCommas(round(tot1, 2))+"</th>\
            <th class='column-1' style='text-align: right;'>"+numberWithCommas(round(tot2, 2))+"</th>\
            <th class='column-2' style='text-align: right;'>"+numberWithCommas(round(tot3, 2))+"</th></tr>";
        if (tipe == 0) { $("#thn-merk-0").html($("#tahun_g1").val()); $("#thn-merk-1").html($("#tahun_g2").val()); $("#thn-merk-2").html($("#tahun_g3").val()); }
        else { $("#thn-merk-0").html($("#tahun_g1").val() + " / " +$("#smt_g1").val()); $("#thn-merk-1").html($("#tahun_g2").val() + " / " +$("#smt_g2").val()); $("#thn-merk-2").html($("#tahun_g3").val() + " / " +$("#smt_g3").val()); }
        $("#data-merk").html(str);
        if (tahun2 == "") $(".column-1").hide();
        else $(".column-1").show();
        if (tahun3 == "") $(".column-2").hide();
        else $(".column-2").show();
      }
    });
    $.ajax({
      url     : "{!! url('getDetailInvoiceSalesBulanan') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        tipe : tipe,
        smt1 : smt1,
        tahun1 : tahun1,
        smt2 : smt2,
        tahun2 : tahun2,
        smt3 : smt3,
        tahun3 : tahun3,
        sales : kodesls,
        pt : pt
      },
      success : function(result) {
        var str = ""; var tot1 = 0, tot2 = 0, tot3 = 0, tot4 = 0, tot5 = 0, tot6 = 0, tot7 = 0, tot8 = 0, tot9 = 0;
        chart2.series[0].update({name:tahun1}, false);
        chart2.series[0].setData(result[2], false);
        chart2.series[0].setVisible(true, false);
        if (tahun2 != "") {
          chart2.series[1].update({name:tahun2}, false);
          chart2.series[1].setData(result[5], false);
          chart2.series[1].setVisible(true, false);
        } else {
          chart2.series[1].update({name:''}, false);
          chart2.series[1].setData([0], false);
          chart2.series[1].setVisible(false, false);
        }
        if (tahun3 != "") {
          chart2.series[2].update({name:tahun3}, false);
          chart2.series[2].setData(result[8], false);
          chart2.series[2].setVisible(true, false);
        } else {
          chart2.series[2].update({name:''}, false);
          chart2.series[2].setData([0], false);
          chart2.series[2].setVisible(false, false);
        }
        chart2.redraw();
        for (var i = 0; i < result[0].length; i++) {
          str += "<tr><td>"+(i+1)+"</td>\
            <td class='column-0' style='text-align: right;'>"+numberWithCommas(round(result[0][i], 2))+"</td>\
            <td class='column-0' style='text-align: right;'>"+numberWithCommas(round(result[1][i], 2))+"</td>\
            <th class='column-0' style='text-align: right;'>"+numberWithCommas(round(result[2][i], 2))+"</th>\
            <td class='column-1' style='text-align: right;'>"+numberWithCommas(round(result[3][i], 2))+"</td>\
            <td class='column-1' style='text-align: right;'>"+numberWithCommas(round(result[4][i], 2))+"</td>\
            <th class='column-1' style='text-align: right;'>"+numberWithCommas(round(result[5][i], 2))+"</th>\
            <td class='column-2' style='text-align: right;'>"+numberWithCommas(round(result[6][i], 2))+"</td>\
            <td class='column-2' style='text-align: right;'>"+numberWithCommas(round(result[7][i], 2))+"</td>\
            <th class='column-2' style='text-align: right;'>"+numberWithCommas(round(result[8][i], 2))+"</th></tr>";
          tot1 += round(result[0][i], 2); tot2 += round(result[1][i], 2); tot3 += round(result[2][i], 2);
          tot4 += round(result[3][i], 2); tot5 += round(result[4][i], 2); tot6 += round(result[5][i], 2);
          tot7 += round(result[6][i], 2); tot8 += round(result[7][i], 2); tot9 += round(result[8][i], 2);
        }
        str += "<tr><th>TOTAL</th>\
            <th class='column-0' style='text-align: right;'>"+numberWithCommas(round(tot1, 2))+"</th>\
            <th class='column-0' style='text-align: right;'>"+numberWithCommas(round(tot2, 2))+"</th>\
            <th class='column-0' style='text-align: right;'>"+numberWithCommas(round(tot3, 2))+"</th>\
            <th class='column-1' style='text-align: right;'>"+numberWithCommas(round(tot4, 2))+"</th>\
            <th class='column-1' style='text-align: right;'>"+numberWithCommas(round(tot5, 2))+"</th>\
            <th class='column-1' style='text-align: right;'>"+numberWithCommas(round(tot6, 2))+"</th>\
            <th class='column-2' style='text-align: right;'>"+numberWithCommas(round(tot7, 2))+"</th>\
            <th class='column-2' style='text-align: right;'>"+numberWithCommas(round(tot8, 2))+"</th>\
            <th class='column-2' style='text-align: right;'>"+numberWithCommas(round(tot9, 2))+"</th></tr>";
        if (tipe == 0) { $("#thn-bulan-0").html($("#tahun_g1").val()); $("#thn-bulan-1").html($("#tahun_g2").val()); $("#thn-bulan-2").html($("#tahun_g3").val()); }
        else { $("#thn-bulan-0").html($("#tahun_g1").val() + " / " +$("#smt_g1").val()); $("#thn-bulan-1").html($("#tahun_g2").val() + " / " +$("#smt_g2").val()); $("#thn-bulan-2").html($("#tahun_g3").val() + " / " +$("#smt_g3").val()); }
        $("#data-perbulan").html(str);
        if (tahun2 == "") $(".column-1").hide();
        else $(".column-1").show();
        if (tahun3 == "") $(".column-2").hide();
        else $(".column-2").show();
      }
    });
  }

  function hideZero() {
    var tot_00 = 0.00, tot_01 = 0.00, tot_02 = 0.00, tot_10 = 0.00, tot_11 = 0.00, tot_12 = 0.00, tot_20 = 0.00, tot_21 = 0.00, tot_22 = 0.00;
    for (var i = 0; i < sales.length; i++) {
      var cek = false;
      tot_00 += toFloat($("#td-0-"+i+"-0").html()); tot_01 += toFloat($("#td-0-"+i+"-1").html()); tot_02 += toFloat($("#td-0-"+i+"-2").html());
      tot_10 += toFloat($("#td-1-"+i+"-0").html()); tot_11 += toFloat($("#td-1-"+i+"-1").html()); tot_12 += toFloat($("#td-1-"+i+"-2").html());
      tot_20 += toFloat($("#td-2-"+i+"-0").html()); tot_21 += toFloat($("#td-2-"+i+"-1").html()); tot_22 += toFloat($("#td-2-"+i+"-2").html());
      var omset1 = toFloat($("#td-0-"+i+"-2").html());
      var omset2 = toFloat($("#td-1-"+i+"-2").html());
      var omset3 = toFloat($("#td-2-"+i+"-2").html());
      if ($("#tahun_g2").val() != "" && $("#tahun_g3").val() != "") {
        if (omset1 == 0 && omset2 == 0 && omset3 == 0) cek = true;
      } else if ($("#tahun_g2").val() != "") {
        if (omset1 == 0 && omset2 == 0) cek = true;
      } else if ($("#tahun_g3").val() != "") {
        if (omset1 == 0 && omset3 == 0) cek = true;
      } else {
        if (omset1 == 0) cek = true;
      }
      if (cek) $("#tr-"+i).hide();
      else $("#tr-"+i).show();
    }
    $("#total-0-0").html(numberWithCommas(round(tot_00, 2))); $("#total-0-1").html(numberWithCommas(round(tot_01, 2))); $("#total-0-2").html(numberWithCommas(round(tot_02, 2)));
    $("#total-1-0").html(numberWithCommas(round(tot_10, 2))); $("#total-1-1").html(numberWithCommas(round(tot_11, 2))); $("#total-1-2").html(numberWithCommas(round(tot_12, 2)));
    $("#total-2-0").html(numberWithCommas(round(tot_20, 2))); $("#total-2-1").html(numberWithCommas(round(tot_21, 2))); $("#total-2-2").html(numberWithCommas(round(tot_22, 2)));
  }
</script>
@endsection
