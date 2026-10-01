@extends('gudang.newmaster')
@section('buttons')

@endsection
{{-- tampilan search bar 1 --}}
  @section('css')
  <style>
  #tabel_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }


  #tabel_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }
  </style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
  <style>
  #tabel2_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel2_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel2_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel2_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>
{{-- end tampilan search bar 2 --}}

@endsection
@section('content')



<div id="page1" class="container-fluid mainpage">
<div class="" style="margin-top:-80px;">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h2>Closing Penyerahan Sample</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg" style="
          height: 30px;
          margin-top: -150px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAdd()">
        Add SO
      </button> -->
    </div>
  </div>
{{-- <button onclick="loadAll()">tes</button> --}}
</div>

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
<div class="card-header" style="">
<div class="row">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">
        Penyerahan Sample
      </a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Closing Penyerahan Sample
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
  <div class="row">
    <div class="col-md-12">
      <div class="container-fluid" style="padding:0; margin:0; width:100%;">
        <table id="tabel" class="table table-sm table-bordered table-striped" style="font-size:13px; width:100%;">
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="white-space: nowrap; width: 100px;">Action</th>
              <th style="white-space: nowrap;">No. Bukti</th>
              <th style="white-space: nowrap;">Tanggal</th>
              <th style="white-space: nowrap;">Keterangan</th>
              <th style="white-space: nowrap;">Customer</th>
              <th style="white-space: nowrap;">Kode Brg</th>
              <th style="white-space: nowrap;">Nama Barang</th>
              <th style="white-space: nowrap; max-width: 120px;">Note</th>
              <th style="white-space: nowrap;">Sisa</th>
              <th style="white-space: nowrap;">Close</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-left" >
              @for ($i = 0; $i < count($tempOutstanding); $i++)
            <tr data-nobukti="{{ $tempOutstanding[$i]->nobukti }}" data-urut="{{ $tempOutstanding[$i]->Urut }}">
              <td class="text-center" style="width: 100px;">
                <button class="btn btn-primary btn-sm" title="Kunci Per Barang" onclick="lockItem('{{ $tempOutstanding[$i]->nobukti }}', '{{ $tempOutstanding[$i]->Urut }}')">
                  <i class="bi bi-lock-fill"></i>
                </button>
                <button class="btn btn-success btn-sm" title="Kunci Per No Bukti" onclick="lockAll('{{ $tempOutstanding[$i]->nobukti }}')">
                    <i class="bi bi-lock-fill"></i>
                </button>
              </td>
              <td>{{ $tempOutstanding[$i]->nobukti}}</td>
              <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
              <td>{{ $tempOutstanding[$i]->Keterangan}}</td>
              <td>{{ $tempOutstanding[$i]->NAMACUSTSUPP}}</td>
              <td>{{ $tempOutstanding[$i]->KODEBRG}}</td>
              <td>{{ $tempOutstanding[$i]->NAMABRG}}</td>
              <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $tempOutstanding[$i]->NOTE }}</td>
              <td>{{ number_format($tempOutstanding[$i]->sisa , 2 ,'.' , ',') }}</td>
              <td class="text-right">{{ (float)$tempOutstanding[$i]->QntCLose == 0 ? '0' : $tempOutstanding[$i]->QntCLose }}</td>
            </tr>
              @endfor
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
  <div class="row">
    <div class="col-md-12">
      <div class="container-fluid" style="padding:0; margin:0; width:100%;">
        <table id="tabel2" class="table table-sm table-bordered table-striped" style="font-size:13px; width:100%;">
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="white-space: nowrap; width: 100px;">Action</th>
              <th style="white-space: nowrap;">No. Bukti</th>
              <th style="white-space: nowrap;">Kode Barang</th>
              <th style="white-space: nowrap;">Nama Barang</th>
              <th style="white-space: nowrap;">Satuan</th>
              <th style="white-space: nowrap;">Qnt Close</th>
              <th style="white-space: nowrap;">User Batal</th>
              <th style="white-space: nowrap;">Tgl Batal</th>
              <th style="white-space: nowrap;">Keterangan Batal</th>
            </tr>
          </thead>
          <tbody id="tabel2_data" class="text-left" >
              @for ($i = 0; $i < count($tempPenerimaan); $i++)
            <tr data-nobukti="{{ $tempPenerimaan[$i]->NOBUKTI }}" data-urut="{{ $tempPenerimaan[$i]->URUT }}">
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" title="Buka Per Barang" onclick="submitUnlock('{{ $tempPenerimaan[$i]->NOBUKTI }}', 'item', '{{ $tempPenerimaan[$i]->URUT }}')">
                    <i class="bi bi-unlock"></i>
                </button>
                <button class="btn btn-danger btn-sm" title="Buka Per No Bukti" onclick="submitUnlock('{{ $tempPenerimaan[$i]->NOBUKTI }}', 'all')">
                    <i class="bi bi-unlock-fill"></i>
                </button>
              </td>
              <td>{{ $tempPenerimaan[$i]->NOBUKTI}}</td>
              <td>{{ $tempPenerimaan[$i]->KODEBRG}}</td>
              <td>{{ $tempPenerimaan[$i]->NAMABRG}}</td>
              <td>{{ $tempPenerimaan[$i]->Satuan}}</td>
              <td>{{ number_format($tempPenerimaan[$i]->QntCLose , 2 ,'.' , ',') }}</td>
              <td>{{ $tempPenerimaan[$i]->UserBatal}}</td>
              <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i]->tglbatal)) !!}</td>
              <td>{{ $tempPenerimaan[$i]->ketBatal}}</td>
            </tr>
              @endfor
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
{{-- Start Modal closing sample --}}
  <div class="modal fade" id="modalLockSample" tabindex="-1" role="dialog" aria-labelledby="modalLockSampleLabel">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Penguncian Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="lock_nobukti">
        <input type="hidden" id="lock_mode">
        <input type="hidden" id="lock_urut">
        <div class="form-group">
          <label for="lock_reason">Masukkan Alasan:</label>
          <textarea class="form-control" id="lock_reason" rows="3" placeholder="" autocomplete="off" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="buttonCloseForm()" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitLock()">Kunci</button>
      </div>
    </div>
  </div>
</div>
{{-- End Modal closing sample --}}
</div>
</div>
</div>
</div>
</div>



@endsection

@section('js')
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}


$(document).ready(function() {
  $("#tabel").DataTable({
    "lengthChange": false,
    "paging": false,
    "autoWidth": false,
    "scrollX": false,
    "info": false
  });

  $("#tabel2").DataTable({
    "lengthChange": false,
    "paging": false,
    "autoWidth": false,
    "scrollX": false,
    "info": false
  });

  $('#modalLockSample').on('shown.bs.modal', function () {
    $('#lock_reason').focus();
  });
});


function lockItem (nobukti, urut) {
  $('#lock_nobukti').val(nobukti);
  $('#lock_mode').val('item');
  $('#lock_urut').val(urut);
  $('#lock_reason').val('');
  $('#modalLockSample').modal('show');
}

function lockAll (nobukti) {
  $('#lock_nobukti').val(nobukti);
  $('#lock_mode').val('all');
  $('#lock_urut').val('');
  $('#lock_reason').val('');
  $('#modalLockSample').modal('show');
}

function submitLock () {
  const _token = $('#_token').val();
  const nobukti = $('#lock_nobukti').val();
  const mode = $('#lock_mode').val();
  const urut = $('#lock_urut').val();
  const reason = $('#lock_reason').val().trim();

  if (!reason) {
    alertify.warning('Silakan masukkan alasan penguncian.');
    return;
  }

  if (mode === 'item') {
    $(`#tabel_data tr[data-nobukti="${nobukti}"][data-urut="${urut}"]`).remove();
  } else {
    $(`#tabel_data tr[data-nobukti="${nobukti}"]`).remove();
  }

  $.ajax({
    url: '{{ url("closingpenyerahansamplelock") }}',
    type: 'POST',
    data: {
      _token,
      nobukti,
      mode,
      urut,
      reason
    },
    success: function (res) {
      if (res.success) {
        $('#modalLockSample').modal('hide');
        alertify.success("Data berhasil dikunci");

        $.ajax({
          url: "{!! url('closingpenyerahansampleloadall') !!}",
          type: "get",
          success: function (res2) {
            $('#tabel2').DataTable().destroy();
            let rowTable2 = '';
            res2.tempPenerimaan.forEach((item) => {
              rowTable2 += `
                <tr data-nobukti="${item.NOBUKTI}" data-urut="${item.URUT}">
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" title="Buka Per Barang" onclick="submitUnlock('${item.NOBUKTI}', 'item', '${item.URUT}')">
                      <i class="bi bi-unlock"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" title="Buka Per No Bukti" onclick="submitUnlock('${item.NOBUKTI}', 'all')">
                      <i class="bi bi-unlock-fill"></i>
                    </button>
                  </td>
                  <td>${item.NOBUKTI}</td>
                  <td>${item.KODEBRG}</td>
                  <td>${item.NAMABRG}</td>
                  <td>${item.Satuan}</td>
                  <td class="text-left">${formatAngka(item.QntCLose)}</td>
                  <td>${item.UserBatal}</td>
                  <td>${item.tglbatal ? formatDate(item.tglbatal, '/') : ''}</td>
                  <td>${item.ketBatal || ''}</td>
                </tr>`;
            });
            document.getElementById("tabel2_data").innerHTML = rowTable2;

            $('#tabel2').DataTable({
              lengthChange: false,
              paging: false,
              autoWidth: false,
              scrollX: false,
              info: false
            });
          }
        });
      } else {
        alertify.error(res.message || "Gagal mengunci data");
      }
    },
    error: function (xhr) {
      alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

function submitUnlock (nobukti, mode = 'all', urut = '') {
  alertify.confirm(
    'Buka Kunci',
    `Yakin ingin Membuka Kunci ${mode === 'item' ? 'Data ini' : 'No. Bukti ' + nobukti} ?`,
    function () {
      const _token = $('#_token').val();

      $.ajax({
        url: "{{ url('closingpenyerahansampleunlock') }}",
        type: "POST",
        data: {
          _token,
          nobukti,
          urut: mode === 'item' ? urut : '',
          mode
        },
        success: function (res) {
          if (res.success) {
            alertify.success("Data berhasil di-unlock");
            setTimeout(() => {
              loadAll();
            }, 300);
          } else {
            alertify.error(res.message || "Gagal membuka kunci");
          }
        },
        error: function (xhr) {
          alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'));
        }
      });
    },
    function () {
      console.log('Batal unlock');
    }
  );
}

function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('closingpenyerahansampleloadall') !!}",
    type: "get",
    success: function (res) {
      // === TABEL 1 ===
      let tabel1 = $('#tabel').DataTable();
      tabel1.clear().destroy();
      $("#tabel_data").html("");

      let rowTable = '';
      res.tempOutstanding.forEach((item) => {
        rowTable += `
          <tr data-nobukti="${item.nobukti}" data-urut="${item.Urut}">
            <td class="text-center" style="width:100px;">
              <button class="btn btn-primary btn-sm" title="Kunci Per Barang" onclick="lockItem('${item.nobukti}', '${item.Urut}')">
                <i class="bi bi-lock-fill"></i>
              </button>
              <button class="btn btn-success btn-sm" title="Kunci Per No Bukti" onclick="lockAll('${item.nobukti}')">
                <i class="bi bi-lock-fill"></i>
              </button>
            </td>
            <td>${item.nobukti}</td>
            <td>${item.Tanggal ? formatDate(item.Tanggal, '/') : ''}</td>
            <td>${item.Keterangan || ''}</td>
            <td>${item.NAMACUSTSUPP || ''}</td>
            <td>${item.KODEBRG || ''}</td>
            <td>${item.NAMABRG || ''}</td>
            <td style="max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.NOTE || ''}</td>
            <td class="text-left">${formatAngka(item.sisa)}</td>
            <td class="text-left">${parseFloat(item.QntCLose ?? 0) === 0 ? '0' : item.QntCLose}</td>
          </tr>`;
      });
      $("#tabel_data").html(rowTable);

      $("#tabel").DataTable({
        lengthChange: false,
        paging: false,
        autoWidth: false,
        scrollX: false,
        info: false
      });

      // === TABEL 2 ===
      let tabel2 = $('#tabel2').DataTable();
      tabel2.clear().destroy();
      $("#tabel2_data").html("");

      let rowTable2 = '';
      res.tempPenerimaan.forEach((item) => {
        rowTable2 += `
          <tr data-nobukti="${item.NOBUKTI}" data-urut="${item.URUT}">
            <td class='text-center'>
              <button class="btn btn-warning btn-sm" title="Buka Per Barang" onclick="submitUnlock('${item.NOBUKTI}', 'item', '${item.URUT}')">
                <i class="bi bi-unlock"></i>
              </button>
              <button class="btn btn-danger btn-sm" title="Buka Per No Bukti" onclick="submitUnlock('${item.NOBUKTI}', 'all')">
                <i class="bi bi-unlock-fill"></i>
              </button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.Satuan}</td>
            <td class="text-left">${formatAngka(item.QntCLose)}</td>
            <td>${item.UserBatal}</td>
            <td>${item.tglbatal ? formatDate(item.tglbatal, '/') : ''}</td>
            <td>${item.ketBatal || ''}</td> 
          </tr>`;
      });
      $("#tabel2_data").html(rowTable2);

      $("#tabel2").DataTable({
        lengthChange: false,
        paging: false,
        autoWidth: false,
        scrollX: false,
        info: false
      });
    },
    error: function (xhr) {
      alertify.error("Gagal memuat data: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

function buttonCloseForm () {
  $('#modalLockSample').modal('hide');
  $('#page1').show();
  loadAll();
}

function formatAngka (angkaString) {
  if (!Number(angkaString)) {
    return '0.00';
  }

  angkaString = parseFloat(angkaString).toFixed(2);

  let tempAngka = angkaString.split('.');

  if (tempAngka[0][0] == '-') {
    let temp2 = '';
    let tempAngka1 = tempAngka[0].split('-');
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2;
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2;
    }
    temp2 += '.' + tempAngka[1];
    return '-' + temp2;
  }

  let temp1 = '';
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1;
    }
    temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1;
  }
  temp1 += '.' + tempAngka[1];
  return temp1;
}


//format Date Khusus karena ada data di database format tidak beraturan
function formatDate(date, pemisah = '-') {
    if (!date) return '';

    const parsedDate = new Date(date);
    if (isNaN(parsedDate)) return ''; 

    let day = '' + parsedDate.getDate();
    let month = '' + (parsedDate.getMonth() + 1);
    const year = parsedDate.getFullYear();

    if (day.length < 2) day = '0' + day;
    if (month.length < 2) month = '0' + month;

    return [year, month, day].join(pemisah);
}



</script>

{{-- script buat hover so belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    setActiveTab(true);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(true);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(false);
    });
  </script>



@endsection
