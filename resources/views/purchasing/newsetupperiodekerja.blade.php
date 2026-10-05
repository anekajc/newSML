@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
<h1>Setup Periode Kerja</h1>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />


  <div class="row mt-6">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <div class="row text-left justify-content-center">
            <div class="card bg-light mb-3" style="max-width: 30rem;">
            <div class="card-header">Ubah Periode Kerja</div>
            <div class="card-body">
              <div class="row">
                <div class="col-4">
                </div>
                <div class="col-3">
                  <h6 class="card-title">Bulan</h6>
                </div>
                <div class="col-1">
                </div>
                <div class="col-4">
                  <h6 class="card-title">Tahun</h6>
                </div>
              </div>
              <div class="row">
                <div class="col-4">
                  <h5 class="card-title">Periode</p>
                </div>
                <div class="col-3">
                  <select id="input_periodekerja_bulan" class="form-select form-control" aria-label="Default select example">
                    <!-- <option selected>Open this select menu</option> -->
                    <option value=1>1</option>
                    <option value=2>2</option>
                    <option value=3>3</option>
                    <option value=4>4</option>
                    <option value=5>5</option>
                    <option value=6>6</option>
                    <option value=7>7</option>
                    <option value=8>8</option>
                    <option value=9>9</option>
                    <option value=10>10</option>
                    <option value=11>11</option>
                    <option value=12>12</option>
                  </select>
                </div>
                <div class="col-1">
                </div>
                <div class="col-4">
                  <input type="number" class="form-control" id="input_periodekerja_tahun" placeholder="Tahun" >
                </div>
              </div>
              <div class="row text-right">
                <div class="col-12">
                  <a href="#" onclick="submitPeriodeKerja()" class="btn text-right btn-primary">Simpan</a>
                </div>
              </div>
            </div>
          </div>

            <!-- <div class="card w-40">
              <div class="card-body">
                <h5 class="card-title text-left">Laporan Pemasukan Barang</h5>
                <div class="container-fluid">
                <div style="background-color: #E8E8E8; padding: 10px; " class="rounded">
                <div class="row " >
                  <h6 class="col-3 text-left">Periode</h6>
                </div>
                <div class="row text-center">
                  <div class="col-5"><input id="inputDate1" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                  <div class="col-2">s/d
                  </div>
                  <div class="col-5"><input id="inputDate2" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>
              </div>

                <div class="row text-center mt-4">
                  <div class="col-6">
                    <select id="inputReport1" style="display: block; width: 100%"  class="form-select" aria-label="Default select example">
                      <option value=0>BC 2.6.2</option>
                      <option value=1>BC 2.3</option>
                      <option value=2>BC 4.0</option>
                      <option value=3>BC 2.7</option>
                    </select>
                  </div>
                  <div class="col-6">
                    <select id="inputReport2" style="display: block; width: 100%" class="form-select form-select-sm" aria-label=".form-select-sm example">
                      <option value=0>Urut Dok. Pabean</option>
                      <option value=1>Urut Bukti LPB</option>
                    </select>
                  </div>
                </div>

                <div class="row text-center mt-4">
                  <div class="col-6">
                    <select id="inputReport3" style="display: block; width: 100%"  class="form-select" aria-label="Default select example">
                      <option value="All">All</option>
                      <option value="PHI001">PEIHAI</option>
                      <option value="PHJ001">JANTI</option>
                    </select>
                  </div>

                  <div class="col-6">

                    </select>
                  </div>
                </div>

                  <a href="#" class="mt-4 btn btn-primary text-right justify-content-right" onclick="makeTable()">Submit</a>

              </div>
              </div>
            </div> -->
          </div>

</div>










@endsection

@section('js')
<script type="text/javascript">

  $(document).ready(function(){
      // console.log('tes ready')
      let tahun = $("#periode_tahun").val();
      let bulan = $("#periode_bulan").val();
      document.getElementById("input_periodekerja_tahun").value = tahun
      document.getElementById("input_periodekerja_bulan").value = bulan

  });

  function submitPeriodeKerja () {
    let tahun = Number($("#input_periodekerja_tahun").val())
    let bulan = Number($("#input_periodekerja_bulan").val())
    let _token = $("#_token").val();
    console.log(tahun,bulan)

    $.ajax({
      url: "{!! url('newsetupperiodekerjaupdate') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        bulan,
        tahun
      },
      success: function(res) {
        console.log(res)
        console.log(res.data)
        document.getElementById("div_periode").innerHTML = `Username : ${res.data[0].user_id} - Periode : ${res.data[0].bulan} / ${res.data[0].tahun}`

      }
    })
  }




</script>




@endsection
