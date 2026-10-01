@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Giro')
@section('content')



{{-- <div class="sp-breadcrumb">
  <span>Beranda</span>
  <span class="sp-sep">›</span>
  <span>Master</span>
  <span class="sp-sep">›</span>
  <span class="sp-crumb-active">Giro</span>
</div> --}}

{{-- <div class="sp-page-head">
  <div>
    <h1>Master Giro</h1>
  </div>
  <div class="d-flex gap-2">
    <button id='divAddBuka' class="btn btn-action-primary" onclick="buttonAddBuka()">+ Add Giro Buka</button>
    <button id='divAddTerima' class="btn btn-action-primary" onclick="buttonAddTerima()" hidden>+ Add Giro Terima</button>
  </div>
</div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <style>
  .radioChoiceMaster {
    display: inline-flex;
    list-style: none;
    margin: 0;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }

  .radioChoiceMaster-item {
    display: flex;
  }

  .radioChoiceMaster-btn {
    border: none;
    border-radius: 999px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    background-color: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    outline: none;
    box-shadow: none;
    cursor: pointer;
  }

  .radioChoiceMaster-btn:hover {
    color: #212529;
    background-color: rgba(0,0,0,0.04);
  }

  .radioChoiceMaster-btn:focus,
  .radioChoiceMaster-btn:focus-visible {
    outline: none;
    box-shadow: none;
  }

  .radioChoiceMaster-btn.active {
    color: #fff;
    background-color: #007bff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.35);
  }

  </style>

  {{-- Tab Giro Dibuka / Diterima dan dua tombol Tambah dipasang di toolbar bersama. Kelas "nav"
       ditambahkan pada <ul>: tab Bootstrap 4 membutuhkannya untuk menonaktifkan tab sebelumnya. --}}
  @php
    $slotTabGiro = '<ul class="nav radioChoiceMaster" id="giroTab" role="tablist">'
      . '<li class="radioChoiceMaster-item" role="presentation"><button onclick="hideButtonTerima()" class="radioChoiceMaster-btn active" id="tab-dibuka-btn" data-toggle="tab" data-target="#tab-dibuka" type="button" role="tab">Daftar Giro Dibuka</button></li>'
      . '<li class="radioChoiceMaster-item" role="presentation"><button onclick="hideButtonBuka()" class="radioChoiceMaster-btn" id="tab-diterima-btn" data-toggle="tab" data-target="#tab-diterima" type="button" role="tab">Daftar Giro Diterima</button></li>'
      . '</ul>';
    $slotAksiGiro = '<button id="divAddBuka" class="btn btn-dpp-utama" type="button" onclick="buttonAddBuka()">Tambah Giro Buka</button>'
      . '<button id="divAddTerima" class="btn btn-dpp-utama" type="button" onclick="buttonAddTerima()" hidden>Tambah Giro Terima</button>';
  @endphp

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster', ['slotFilter' => $slotTabGiro, 'slotAksi' => $slotAksiGiro])

      <div class="tab-content">

        <!-- ---------- DAFTAR GIRO DIBUKA ---------- -->
        <div class="tab-pane fade show active" id="tab-dibuka" role="tabpanel">
          <table id="tabel_dibuka" class="data-table po-aksi-hover">
            <thead id="tabel_dibuka_header" class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_dataDibuka" class="text-left"></tbody>
          </table>
        </div>

        <!-- ---------- DAFTAR GIRO DITERIMA ---------- -->
        <div class="tab-pane fade" id="tab-diterima" role="tabpanel">
          <table id="tabel_diterima" class="data-table po-aksi-hover">
            <thead id="tabel_diterima_header" class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_dataDiterima" class="text-left"></tbody>
          </table>
        </div>

      </div>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>

</div>

<div id="printContainer" style="display:none"></div>


<!-- start modal add terima giro-->
<div class="modal fade"  id="formTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Giro Terima</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_add_bank">Bank</label>
          <input type="text" class="form-control" id="input_add_bank" placeholder="Bank">

          <label for="input_add_noGiro">No. Giro</label>
          <input type="text" class="form-control" id="input_add_noGiro" placeholder="No. Giro">

          <label for="input_add_tglGiro">Tanggal Giro</label>
          <input type="date" class="form-control" id="input_add_tglGiro">
        </div>

            <div class="bs-form">
          <label for="input_add_valas">Valas</label>
          <select id="input_add_valas" class="form-control form-control-lg mb-3" aria-label=".form-control-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
          <label for="input_add_kurs">Kurs</label>
          <input type="text" class="form-control" id="input_add_kurs" placeholder="Kurs" disabled>

            
            
          <label for="input_add_nilaiGiro">Nilai Giro</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-right" id="input_add_nilaiGiro" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
            <input type="text" class="form-control text-right" id="input_add_nilaiGiroRp" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)"></div></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_tglTerima">Tanggal Terima</label>
          <input type="date" class="form-control" id="input_add_tglTerima" >

          <label for="input_add_buktiTerima">Bukti Terima</label>
          <input type="text" class="form-control" id="input_add_buktiTerima" disabled>

          <label for="input_add_keterangan">Keterangan</label>
          <input type="text" class="form-control" id="input_add_keterangan">

          <label for="input_add_tglCair">Tanggal Cair</label>
          <input type="date" class="form-control" id="input_add_tglCair" disabled>

          <label for="input_add_buktiCair">Bukti Cair</label>
          <input type="text" class="form-control" id="input_add_buktiCair" disabled>

          <label for="input_add_keteranganCair">Keterangan Cair</label>
          <input type="text" class="form-control" id="input_add_keteranganCair" disabled>
        </div>
            
          <div class="bs-form bs-form-1">
          <label for="input_add_perkiraanKas">Perkiraan Kas</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraanKas" placeholder="Perkiraan Kas" disabled>
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraanKas()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddTerima()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add terima giro-->

<!-- start modal add buka giro-->
<div class="modal fade"  id="formBuka" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Giro Buka</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="bs-form bs-form-1">
          <label for="input_add2_bank">Bank</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_add2_bank" placeholder="Bank" disabled>
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectBank()" title="Cari"><i class="bi bi-search"></i></button>
            </div>
          </div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add2_noGiro">No. Giro</label>
          <input type="text" class="form-control" id="input_add2_noGiro" placeholder="No. Giro">

          <label for="input_add2_tglGiro">Tanggal Giro</label>
          <input type="date" class="form-control" id="input_add2_tglGiro">
        </div>
            
            <div class="bs-form">
          <label for="input_add2_valas">Valas</label>
          <select id="input_add2_valas" class="form-control form-control-lg mb-3" aria-label=".form-control-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
          <label for="input_add2_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_add2_kurs" placeholder="Kurs" disabled>


            
          <label for="input_add2_nilaiGiro">Nilai Giro</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-right" id="input_add2_nilaiGiro" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
            <input type="text" class="form-control text-right" id="input_add2_nilaiGiroRp" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)"></div></div>
        </div>


            <div class="bs-form bs-form-1">
          <label for="input_add2_tglBuka">Tanggal Buka</label>
          <input type="date" class="form-control" id="input_add2_tglBuka" >

          <label for="input_add2_buktiBuka">Bukti Buka</label>
          <input type="text" class="form-control" id="input_add2_buktiBuka" disabled>

          <label for="input_add2_keterangan">Keterangan</label>
          <input type="text" class="form-control" id="input_add2_keterangan">

          <label for="input_add2_tglCair">Tanggal Cair</label>
          <input type="date" class="form-control" id="input_add2_tglCair" disabled>

          <label for="input_add2_buktiCair">Bukti Cair</label>
          <input type="text" class="form-control" id="input_add2_buktiCair" disabled>

          <label for="input_add2_keteranganCair">Keterangan Cair</label>
          <input type="text" class="form-control" id="input_add2_keteranganCair" disabled>
        </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddBuka()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add buka giro-->

<!-- start modal edit terima giro-->
<div class="modal fade"  id="formEditTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Giro Terima</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_edit_bank">Bank</label>
          <input type="text" class="form-control" id="input_edit_bank" placeholder="Bank" disabled>

          <label for="input_edit_noGiro">No. Giro</label>
          <input type="text" class="form-control" id="input_edit_noGiro" placeholder="No. Giro" disabled>

          <label for="input_edit_tglGiro">Tanggal Giro</label>
          <input type="date" class="form-control" id="input_edit_tglGiro">
        </div>

            <div class="bs-form">
          <label for="input_edit_valas">Valas</label>
          <select id="input_edit_valas" class="form-control form-control-lg mb-3" aria-label=".form-control-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
          <label for="input_edit_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_edit_kurs" placeholder="Kurs" disabled>


            
            
          <label for="input_edit_nilaiGiro">Nilai Giro</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-right" id="input_edit_nilaiGiro">
            <input type="text" class="form-control text-right" id="input_edit_nilaiGiroRp"></div></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_edit_tglTerima">Tanggal Terima</label>
          <input type="date" class="form-control" id="input_edit_tglTerima" >

          <label for="input_edit_buktiTerima">Bukti Terima</label>
          <input type="text" class="form-control" id="input_edit_buktiTerima" placeholder="Bukti Terima" disabled>

          <label for="input_edit_keterangan">Keterangan</label>
          <input type="text" class="form-control" id="input_edit_keterangan">

          <label for="input_edit_tglCair">Tanggal Cair</label>
          <input type="date" class="form-control" id="input_edit_tglCair" disabled>

          <label for="input_edit_buktiCair">Bukti Cair</label>
          <input type="text" class="form-control" id="input_edit_buktiCair" placeholder="Bukti Cair" disabled>

          <label for="input_edit_keteranganCair">Keterangan Cair</label>
          <input type="text" class="form-control" id="input_edit_keteranganCair" disabled>
        </div>

          <div class="bs-form bs-form-1">
          <label for="input_edit_perkiraanKas">Perkiraan Kas</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraanKas" placeholder="Perkiraan Kas" disabled>
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraanKas()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditTerima()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit terima giro-->

<!-- start modal edit buka giro-->
<div class="modal fade"  id="formEditBuka" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Giro Buka</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_edit2_bank">Bank</label>
          <input type="text" class="form-control" id="input_edit2_bank" placeholder="Bank" disabled>

          <label for="input_edit2_noGiro">No. Giro</label>
          <input type="text" class="form-control" id="input_edit2_noGiro" placeholder="No. Giro" disabled>

          <label for="input_edit2_tglGiro">Tanggal Giro</label>
          <input type="date" class="form-control" id="input_edit2_tglGiro">
        </div>

            <div class="bs-form">
          <label for="input_edit2_valas">Valas</label>
          <select id="input_edit2_valas" class="form-control form-control-lg mb-3" aria-label=".form-control-lg example">
                  @foreach ($listDataValas as $valas)
                      <option value="{{ $valas->KODEVLS }}" 
                              data-kurs="{{ $valas->KURS }}"
                              {{ $valas->KODEVLS == 'IDR' ? 'selected' : '' }}>
                          {{ $valas->KODEVLS }}
                      </option>
                  @endforeach
                </select>
          <label for="input_edit2_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_edit2_kurs" placeholder="Kurs" disabled>


            
            
          <label for="input_edit2_nilaiGiro">Nilai Giro</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-right" id="input_edit2_nilaiGiro" placeholder="Nilai Giro">
            <input type="text" class="form-control text-right" id="input_edit2_nilaiGiroRp"></div></div>
        </div>


            <div class="bs-form bs-form-1">
          <label for="input_edit2_tglBuka">Tanggal Buka</label>
          <input type="date" class="form-control" id="input_edit2_tglBuka" >

          <label for="input_edit2_buktiBuka">Bukti Buka</label>
          <input type="text" class="form-control" id="input_edit2_buktiBuka" disabled>

          <label for="input_edit2_keterangan">Keterangan</label>
          <input type="text" class="form-control" id="input_edit2_keterangan">

          <label for="input_edit2_tglCair">Tanggal Cair</label>
          <input type="date" class="form-control" id="input_edit2_tglCair" disabled>

          <label for="input_edit2_buktiCair">Bukti Cair</label>
          <input type="text" class="form-control" id="input_edit2_buktiCair" disabled>

          <label for="input_edit2_keteranganCair">Keterangan Cair</label>
          <input type="text" class="form-control" id="input_edit2_keteranganCair" disabled>
        </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditBuka()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit buka giro-->

<!-- start modal pilih Kas -->
<div class="modal fade picker-kas"  id="formSelectKas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Kas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAktivaSelectPerkiraan">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
                </thead>
                <tbody id="tabel_dataAktivaSelectPerkiraan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal pilih Kas-->

<!-- start modal pilih Bank -->
<div class="modal fade picker-kas"  id="formSelectBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Bank</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelBukaSelectBank">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataBukaSelectBank" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal pilih Bank-->

@endsection

{{-- Modal pemilih mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Toolbar (cari & Tampilkan) mengendalikan tabel di tab yang aktif - MasterList.pakai().
function hideButtonBuka(){
  document.getElementById('divAddBuka').hidden = true;
  document.getElementById('divAddTerima').hidden = false;
  MasterList.pakai('#tabel_diterima')
  MasterList.pakaiKolom('#tabel_diterima')
}

function hideButtonTerima(){
  document.getElementById('divAddBuka').hidden = false;
  document.getElementById('divAddTerima').hidden = true;
  MasterList.pakai('#tabel_dibuka')
  MasterList.pakaiKolom('#tabel_dibuka')
}

// Tinggi kotak scroll dihitung ulang setelah animasi tab selesai (pane sebelumnya tersembunyi).
$(document).on('shown.bs.tab', '#giroTab [data-toggle="tab"]', function () { MasterList.aturTinggi() })

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MGB_KOLOM = [
  ['Bank',           'Bank',                      1, 'varchar', 0, 0],
  ['NoGiro',         'No. Giro',                  1, 'varchar', 0, 0],
  ['TglGiro',        'Tanggal Giro Jatuh Tempo',  1, 'varchar', 0, 0],
  ['Kodevls',        'Valas',                     1, 'varchar', 0, 0],
  ['Kurs',           'Kurs',                      1, 'varchar', 0, 0],
  ['DebetRp',        'Debet Rupiah',              1, 'varchar', 0, 0],
  ['KreditRp',       'Kredit Rupiah',             1, 'varchar', 0, 0],
  ['Debet',          'Debet Valas',               1, 'varchar', 0, 0],
  ['Kredit',         'Kredit Valas',              1, 'varchar', 0, 0],
  ['TglBuka',        'Tanggal Buka Giro',         1, 'varchar', 0, 0],
  ['BuktiBuka',      'Bukti Buka Giro',           1, 'varchar', 0, 0],
  ['Keterangan',     'Keterangan Buka Giro',      1, 'varchar', 0, 0],
  ['TglCair',        'Tanggal Pencairan Giro',    1, 'varchar', 0, 0],
  ['BuktiCair',      'Bukti Pencairan Giro',      1, 'varchar', 0, 0],
  ['KeteranganCair', 'Keterangan Pencairan Giro', 1, 'varchar', 0, 0],
]

const MGT_KOLOM = [
  ['Bank',           'Bank',                      1, 'varchar', 0, 0],
  ['NoGiro',         'No. Giro',                  1, 'varchar', 0, 0],
  ['Kas',            'Perkiraan Kas',             1, 'varchar', 0, 0],
  ['TglGiro',        'Tanggal Giro Jatuh Tempo',  1, 'varchar', 0, 0],
  ['Kodevls',        'Valas',                     1, 'varchar', 0, 0],
  ['Kurs',           'Kurs',                      1, 'varchar', 0, 0],
  ['DebetRp',        'Debet Rupiah',              1, 'varchar', 0, 0],
  ['KreditRp',       'Kredit Rupiah',             1, 'varchar', 0, 0],
  ['Debet',          'Debet Valas',               1, 'varchar', 0, 0],
  ['Kredit',         'Kredit Valas',              1, 'varchar', 0, 0],
  ['TglBuka',        'Tanggal Terima Giro',       1, 'varchar', 0, 0],
  ['BuktiBuka',      'Bukti Terima Giro',         1, 'varchar', 0, 0],
  ['Keterangan',     'Keterangan Terima Giro',    1, 'varchar', 0, 0],
  ['TglCair',        'Tanggal Pencairan Giro',    1, 'varchar', 0, 0],
  ['BuktiCair',      'Bukti Pencairan Giro',      1, 'varchar', 0, 0],
  ['KeteranganCair', 'Keterangan Pencairan Giro', 1, 'varchar', 0, 0],
]

// Tampilan sel sama seperti sebelumnya: tanggal yyyy-mm-dd, nilai rupiah/valas id-ID 2 desimal.
function mgTanggal (v) {
  return v ? (new Date(v)).toLocaleDateString('en-CA') : ''
}
function mgAngka (field) {
  return function (item) {
    return "<td class='text-right'>" + parseFloat(item[field]).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>'
  }
}
function mgTeks (field) {
  return function (item) { return '<td>' + item[field] + '</td>' }
}
let mgKhusus = {
  TglGiro: function (item) { return '<td>' + mgTanggal(item.TglGiro) + '</td>' },
  TglBuka: function (item) { return '<td>' + mgTanggal(item.TglBuka) + '</td>' },
  TglCair: function (item) { return '<td>' + mgTanggal(item.TglCair) + '</td>' },
  DebetRp: mgAngka('DebetRp'), KreditRp: mgAngka('KreditRp'), Debet: mgAngka('Debet'), Kredit: mgAngka('Kredit'),
  Bank: mgTeks('Bank'), NoGiro: mgTeks('NoGiro'), Kas: mgTeks('Kas'), Kodevls: mgTeks('Kodevls'),
  BuktiBuka: mgTeks('BuktiBuka'), Keterangan: mgTeks('Keterangan'), BuktiCair: mgTeks('BuktiCair'), KeteranganCair: mgTeks('KeteranganCair')
}

let dataBuka = []
let dataTerima = []

function renderTabelBuka () {
  if ($.fn.DataTable.isDataTable('#tabel_dibuka')) {
    $('#tabel_dibuka').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil('#tabel_dibuka')
  document.getElementById('tabel_dibuka_header').innerHTML = MasterList.headHtml(cols, '#tabel_dibuka')

  // Kurs giro dibuka ditampilkan apa adanya (seperti sebelumnya).
  let khusus = $.extend({}, mgKhusus, { Kurs: function (item) { return "<td class='text-right'>" + MasterList.nolDepan(item.Kurs) + '</td>' } })

  let rowTable = ""
  dataBuka.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditBuka('${item.NoGiro}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteBuka('${item.NoGiro}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, khusus)
  });

  document.getElementById("tabel_dataDibuka").innerHTML = rowTable
  $("#tabel_dibuka").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel_dibuka')
}

function renderTabelTerima () {
  if ($.fn.DataTable.isDataTable('#tabel_diterima')) {
    $('#tabel_diterima').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil('#tabel_diterima')
  document.getElementById('tabel_diterima_header').innerHTML = MasterList.headHtml(cols, '#tabel_diterima')

  let khusus = $.extend({}, mgKhusus, { Kurs: mgAngka('Kurs') })

  let rowTable = ""
  dataTerima.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditTerima('${item.NoGiro}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeleteTerima('${item.NoGiro}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, khusus)
  });

  document.getElementById("tabel_dataDiterima").innerHTML = rowTable
  $("#tabel_diterima").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel_diterima')
}

// Setelah tabel digambar ulang, ReportTable & toolbar dikembalikan ke tab yang sedang tampil.
function mgKembaliKeTabAktif () {
  let sel = document.getElementById('divAddTerima').hidden ? '#tabel_dibuka' : '#tabel_diterima'
  MasterList.pakai(sel)
  MasterList.pakaiKolom(sel)
}

function loadAllBuka () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastergiroloadallbuka') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataBuka = res
  }})

  renderTabelBuka()
  mgKembaliKeTabAktif()
}

function loadAllTerima () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastergiroloadallterima') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataTerima = res
  }})

  renderTabelTerima()
  mgKembaliKeTabAktif()
}

function buttonAddBuka () {
  document.getElementById('input_add2_bank').value = ''
  document.getElementById('input_add2_noGiro').value = ''
  document.getElementById('input_add2_tglGiro').value = ''
  document.getElementById('input_add2_nilaiGiro').value = ''
  document.getElementById('input_add2_tglBuka').value = ''
  document.getElementById('input_add2_buktiBuka').value = ''
  document.getElementById('input_add2_keterangan').value = ''
  document.getElementById('input_add2_tglCair').value = ''
  document.getElementById('input_add2_buktiCair').value = ''
  document.getElementById('input_add2_keteranganCair').value = ''

  $("#formBuka").modal('toggle')

}

function buttonAddTerima () {
document.getElementById('input_add_bank').value = ''
document.getElementById('input_add_noGiro').value = ''
document.getElementById('input_add_tglGiro').value = ''
document.getElementById('input_add_nilaiGiro').value = ''
document.getElementById('input_add_tglTerima').value = ''
document.getElementById('input_add_buktiTerima').value = ''
document.getElementById('input_add_keterangan').value = ''
document.getElementById('input_add_tglCair').value = ''
document.getElementById('input_add_buktiCair').value = ''
document.getElementById('input_add_keteranganCair').value = ''
document.getElementById('input_add_perkiraanKas').value = ''

  $("#formTerima").modal('toggle')

}

let noGiroTemp = ''
let noGiroTerimaTemp = ''

function buttonEditBuka (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergirospdetailbuka') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit2_bank").value = res[0].Bank
      document.getElementById("input_edit2_noGiro").value = res[0].NoGiro
      document.getElementById("input_edit2_tglGiro").value = new Date(res[0].TglGiro).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_nilaiGiroRp").value = MasterList.nolDepan(res[0].KreditRp)
      
      document.getElementById("input_edit2_nilaiGiro").value = MasterList.nolDepan(res[0].Kredit)
      document.getElementById("input_edit2_valas").value = res[0].Kodevls
      document.getElementById("input_edit2_kurs").value = formatWithCommas(parseFloat(res[0].Kurs) || 0)
      document.getElementById("input_edit2_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit2_tglBuka").value = new Date(res[0].TglBuka).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_buktiBuka").value = res[0].BuktiBuka
      document.getElementById("input_edit2_tglCair").value = new Date(res[0].TglCair).toLocaleDateString('en-CA');
      document.getElementById("input_edit2_buktiCair").value = res[0].BuktiCair
      document.getElementById("input_edit2_keteranganCair").value = res[0].KeteranganCair

      formatNumber(document.getElementById("input_edit2_nilaiGiroRp"))
      formatNumber(document.getElementById("input_edit2_nilaiGiro"))

      noGiroTemp = res[0].NoGiro

    }})
    $("#formEditBuka").modal('toggle')
}

function buttonEditTerima (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergirospdetailterima') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_edit_bank").value = res[0].Bank
      document.getElementById("input_edit_noGiro").value = res[0].NoGiro
      document.getElementById("input_edit_tglGiro").value = new Date(res[0].TglGiro).toLocaleDateString('en-CA');
      document.getElementById("input_edit_nilaiGiro").value = MasterList.nolDepan(res[0].Debet)
      document.getElementById("input_edit_nilaiGiroRp").value = MasterList.nolDepan(res[0].DebetRp)
      document.getElementById("input_edit_valas").value = res[0].Kodevls
      document.getElementById("input_edit_kurs").value = formatWithCommas(parseFloat(res[0].Kurs) || 0)
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_tglTerima").value = new Date(res[0].TglBuka).toLocaleDateString('en-CA');
      document.getElementById("input_edit_buktiTerima").value = res[0].BuktiBuka
      document.getElementById("input_edit_tglCair").value = new Date(res[0].TglCair).toLocaleDateString('en-CA');
      document.getElementById("input_edit_buktiCair").value = res[0].BuktiCair
      document.getElementById("input_edit_keteranganCair").value = res[0].KeteranganCair
      document.getElementById("input_edit_perkiraanKas").value = res[0].Kas
      
      formatNumber(document.getElementById("input_edit_nilaiGiroRp"))
      formatNumber(document.getElementById("input_edit_nilaiGiro"))

      noGiroTerimaTemp = res[0].NoGiro

    }})
    $("#formEditTerima").modal('toggle')

}

function buttonDeleteTerima (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Giro Terima', 'Apakah yakin ingin menghapus No. Giro ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergirospdeleteterima') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAllBuka()
              loadAllTerima()
              alertify.success("Data Giro Terima " + kode + " telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function buttonDeleteBuka (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Giro Buka', 'Apakah yakin ingin menghapus No. Giro  ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergirospdeletebuka') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAllBuka()
              loadAllTerima()
              alertify.success("Data Giro Buka telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEditBuka () {

  let _token = $("#_token").val();

  let noGiro = $("#input_edit2_noGiro").val();
  let bank = $("#input_edit2_bank").val();
  let tanggalGiro = $("#input_edit2_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_edit2_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_edit2_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_edit2_valas").val();
  let kurs = $("#input_edit2_kurs").val().replace(/,/g, '');
  let tanggalBuka = $("#input_edit2_tglBuka").val();
  let buktiBuka = $("#input_edit2_buktiBuka").val();
  let keterangan = $("#input_edit2_keterangan").val();
  let tanggalCair = $("#input_edit2_tglCair").val();
  let buktiCair = $("#input_edit2_buktiCair").val();
  let keteranganCair = $("#input_edit2_keteranganCair").val();

  // Dulu tanpa validasi - No. Giro/Bank kosong tetap terkirim.
  if (!bank) {
    alertify.warning("Bank harus diisi");
    return
  }
  if (!noGiro) {
    alertify.warning("No. Giro harus diisi");
    return
  }
  if (!tanggalGiro) {
    alertify.warning("Tanggal Giro harus diisi");
    return
  }
  if (!nilaiGiro) {
    alertify.warning("Nilai Giro harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergirospeditbuka') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noGiro,
      bank,
      tanggalGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tanggalBuka,
      buktiBuka,
      keterangan,
      tanggalCair,
      buktiCair,
      keteranganCair,
      noGiroOld : noGiroTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Giro Buka ' "  + noGiro + " ' telah diedit");
        loadAllBuka()
        loadAllTerima()
        $("#formEditBuka").modal('toggle')
      }

    }})

}

function submitEditTerima () {

  let _token = $("#_token").val();
  let noGiro = $("#input_edit_noGiro").val();
  let bank = $("#input_edit_bank").val();
  let tanggalGiro = $("#input_edit_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_edit_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_edit_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_edit_valas").val();
  let kurs = $("#input_edit_kurs").val().replace(/,/g, '');
  let tanggalTerima = $("#input_edit_tglTerima").val();
  let buktiTerima = $("#input_edit_buktiTerima").val();
  let keterangan = $("#input_edit_keterangan").val();
  let tanggalCair = $("#input_edit_tglCair").val();
  let buktiCair = $("#input_edit_buktiCair").val();
  let keteranganCair = $("#input_edit_keteranganCair").val();
  let perkiraanKas = $("#input_edit_perkiraanKas").val();

  console.log(noGiro);
  console.log(bank);

  if (!noGiro) {
    alertify.warning("No. Giro  harus diisi");
    return
  }

  if (!bank) {
    alertify.warning("Bank  harus diisi");
    return
  }

  if (!tanggalGiro) {
    alertify.warning("Tanggal Giro  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergirospeditterima') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noGiro,
      bank,
      tanggalGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tanggalTerima,
      buktiTerima,
      keterangan,
      tanggalCair,
      buktiCair,
      keteranganCair,
      perkiraanKas,
      noGiroOld : noGiroTerimaTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Giro Terima '" + noGiro + "' telah diedit");
        loadAllBuka()
        loadAllTerima()
        $("#formEditTerima").modal("hide");
      }

    }})

}
//
function submitAddTerima () {

  let _token = $("#_token").val();
  let bank = $("#input_add_bank").val();
  let noGiro = $("#input_add_noGiro").val();
  let tglGiro = $("#input_add_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_add_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_add_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val().replace(/,/g, '');
  let tglTerima = $("#input_add_tglTerima").val();
  let buktiTerima = $("#input_add_buktiTerima").val();
  let keterangan = $("#input_add_keterangan").val();
  let tglCair = $("#input_add_tglCair").val();
  let buktiCair = $("#input_add_buktiCair").val();
  let keteranganCair = $("#input_add_keteranganCair").val();
  let perkiraanKas = $("#input_add_perkiraanKas").val();

  // Dulu tanpa validasi - No. Giro/Bank kosong tetap terkirim.
  if (!bank) {
    alertify.warning("Bank harus diisi");
    return
  }
  if (!noGiro) {
    alertify.warning("No. Giro harus diisi");
    return
  }
  if (!tglGiro) {
    alertify.warning("Tanggal Giro harus diisi");
    return
  }
  if (!nilaiGiro) {
    alertify.warning("Nilai Giro harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergirospaddterima') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      bank,
      noGiro,
      tglGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tglTerima,
      buktiTerima,
      keterangan,
      tglCair,
      buktiCair,
      keteranganCair,
      perkiraanKas
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Giro Terima telah ditambah");
        loadAllTerima()
        $("#formTerima").modal('hide');
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddBuka () {

  let _token = $("#_token").val();
  let bank = $("#input_add2_bank").val();
  let noGiro = $("#input_add2_noGiro").val();
  let tglGiro = $("#input_add2_tglGiro").val();
  let nilaiGiro = parseFloat(($("#input_add2_nilaiGiro").val() || 0).toString().replace(/,/g, '')) || 0;
  let nilaiGiroRp = parseFloat(($("#input_add2_nilaiGiroRp").val() || 0).toString().replace(/,/g, '')) || 0;
  let valas = $("#input_add2_valas").val();
  let kurs = $("#input_add2_kurs").val().replace(/,/g, '');
  let tglBuka = $("#input_add2_tglBuka").val();
  let buktiBuka = $("#input_add2_buktiBuka").val();
  let keterangan = $("#input_add2_keterangan").val();
  let tglCair = $("#input_add2_tglCair").val();
  let buktiCair = $("#input_add2_buktiCair").val();
  let keteranganCair = $("#input_add2_keteranganCair").val();

  // Dulu tanpa validasi - No. Giro/Bank kosong tetap terkirim.
  if (!bank) {
    alertify.warning("Bank harus diisi");
    return
  }
  if (!noGiro) {
    alertify.warning("No. Giro harus diisi");
    return
  }
  if (!tglGiro) {
    alertify.warning("Tanggal Giro harus diisi");
    return
  }
  if (!nilaiGiro) {
    alertify.warning("Nilai Giro harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergirospaddbuka') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      bank,
      noGiro,
      tglGiro,
      nilaiGiro,
      nilaiGiroRp,
      valas,
      kurs,
      tglBuka,
      buktiBuka,
      keterangan,
      tglCair,
      buktiCair,
      keteranganCair
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Giro Buka telah ditambah");
        loadAllBuka()
        loadAllTerima()
        $("#formBuka").modal("hide")
      }

    }})
}

function updateKurs2() {
    var selectElement = document.getElementById('input_add_valas');
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_add_kurs').value = formatWithCommas(parseFloat(kurs) || 0);
}

// Set up the change event listener
document.getElementById('input_add_valas').addEventListener('change', updateKurs2);

// Trigger immediately on page load to handle pre-selected option
document.addEventListener('DOMContentLoaded', updateKurs2);

function updateKurs() {
    var selectElement = document.getElementById('input_add2_valas');
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_add2_kurs').value = formatWithCommas(parseFloat(kurs) || 0);
}

// Set up the change event listener
document.getElementById('input_add2_valas').addEventListener('change', updateKurs);

// Trigger immediately on page load to handle pre-selected option
document.addEventListener('DOMContentLoaded', updateKurs);

document.getElementById('input_edit_valas').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_edit_kurs').value = formatWithCommas(parseFloat(kurs) || 0);
});

document.getElementById('input_edit2_valas').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var kurs = selectedOption.getAttribute('data-kurs');
    document.getElementById('input_edit2_kurs').value = formatWithCommas(parseFloat(kurs) || 0);
});

function buttonSelectPerkiraanKas () {
  loadSelectKas()
  $("#formSelectKas").modal('toggle')
}

function buttonSelectBank () {
  loadSelectBank()
  $("#formSelectBank").modal('toggle')
}

function loadSelectKas() {
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadkas') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihPerkiraanKas('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  pickerKasInit('tabelAktivaSelectPerkiraan')
}

function loadSelectBank() {
  let _token = $("#_token").val();

  $('#tabelBukaSelectBank').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergiroloadbank') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihPerkiraanBank('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBukaSelectBank").innerHTML = rowTable;
  pickerKasInit('tabelBukaSelectBank')
}

function buttonPilihPerkiraanKas(selectedPerkiraan) {
  $("#input_add_perkiraanKas").val(selectedPerkiraan);
  $("#input_edit_perkiraanKas").val(selectedPerkiraan);
  $("#formSelectKas").modal("hide");
}

function buttonPilihPerkiraanBank(selectedPerkiraan) {
  $("#input_add2_bank").val(selectedPerkiraan);
  $("#input_edit2_bank").val(selectedPerkiraan);
  $("#formSelectBank").modal("hide");
}

window.onload = function(){
  MasterList.kolom({ href: 'mastergiro', mode: 1, table: '#tabel_dibuka', kolom: MGB_KOLOM, onChange: function () { renderTabelBuka(); mgKembaliKeTabAktif() } })
  MasterList.kolom({ href: 'mastergiro', mode: 2, table: '#tabel_diterima', kolom: MGT_KOLOM, onChange: function () { renderTabelTerima(); mgKembaliKeTabAktif() } })
  loadAllBuka();
  loadAllTerima();
  hideButtonTerima();
}

// Get the DOM elements (not their values)
const inputNilaiGiroElement = document.getElementById('input_add2_nilaiGiro');
const inputNilaiGiroRpElement = document.getElementById('input_add2_nilaiGiroRp');
const inputKursElement = document.getElementById('input_add2_kurs');

const inputNilaiGiroElement2 = document.getElementById('input_add_nilaiGiro');
const inputNilaiGiroRpElement2 = document.getElementById('input_add_nilaiGiroRp');
const inputKursElement2 = document.getElementById('input_add_kurs');

// Function to format number with thousands separator
function formatWithCommas(num) {
    return num.toLocaleString('en-US');
}

// Function to parse value and remove commas
function parseValue(element) {
    return parseFloat((element.value || '0').replace(/,/g, '')) || 0;
}

// Add event listener to the input field
inputNilaiGiroElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputNilaiGiroElement);
    const kurs = parseValue(inputKursElement);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputNilaiGiroRpElement.value = formatWithCommas(nilaiGiroRp);
});

// Add event listener to the input field
inputNilaiGiroElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputNilaiGiroElement2);
    const kurs = parseValue(inputKursElement2);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputNilaiGiroRpElement2.value = formatWithCommas(nilaiGiroRp);
});

inputNilaiGiroRpElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputNilaiGiroRpElement);
    const kurs = parseValue(inputKursElement);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputNilaiGiroElement.value = formatWithCommas(nilaiGiro);
});

inputNilaiGiroRpElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputNilaiGiroRpElement2);
    const kurs = parseValue(inputKursElement2);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputNilaiGiroElement2.value = formatWithCommas(nilaiGiro);
});



// Optional: Add formatting to the input itself when user types
inputNilaiGiroElement.addEventListener('blur', function() {
    const value = parseValue(inputNilaiGiroElement);
    if (value > 0) {
        inputNilaiGiroElement.value = formatWithCommas(value);
    }
});

const inputEditNilaiGiroElement = document.getElementById('input_edit2_nilaiGiro');
const inputEditNilaiGiroRpElement = document.getElementById('input_edit2_nilaiGiroRp');
const inputEditKursElement = document.getElementById('input_edit2_kurs');

const inputEditNilaiGiroElement2 = document.getElementById('input_edit_nilaiGiro');
const inputEditNilaiGiroRpElement2 = document.getElementById('input_edit_nilaiGiroRp');
const inputEditKursElement2 = document.getElementById('input_edit_kurs');

// Add event listener to the input field
inputEditNilaiGiroElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputEditNilaiGiroElement);
    const kurs = parseValue(inputEditKursElement);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroRpElement.value = formatWithCommas(nilaiGiroRp);
});

// Add event listener to the input field
inputEditNilaiGiroElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiro = parseValue(inputEditNilaiGiroElement2);
    const kurs = parseValue(inputEditKursElement2);

    // Calculate the result
    const nilaiGiroRp = nilaiGiro * kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroRpElement2.value = formatWithCommas(nilaiGiroRp);
});

inputEditNilaiGiroRpElement.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputEditNilaiGiroRpElement);
    const kurs = parseValue(inputEditKursElement);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroElement.value = formatWithCommas(nilaiGiro);
});

inputEditNilaiGiroRpElement2.addEventListener('input', function() {
    // Get the values from the input fields
    const nilaiGiroRp = parseValue(inputEditNilaiGiroRpElement2);
    const kurs = parseValue(inputEditKursElement2);

    // Calculate the result
    const nilaiGiro = nilaiGiroRp / kurs;

    // Update the value with thousands separator
    inputEditNilaiGiroElement2.value = formatWithCommas(nilaiGiro);
});

</script>




@endsection
