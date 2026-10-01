{{-- Modal tambah/edit + pemilih perkiraan bersama untuk sub-halaman Set Posting. Tampilan form
     mengikuti #formBsGrid .bs-form, pemilih mengikuti picker purchasing (picker-kas.css). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulTipeModal">Title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form bs-form-1">
            <label for="input_kode">Perkiraan</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_kode" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

      </div>
      <div class="modal-footer" id='buttonTipeModal'>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal aktiva select perkiraan -->
<div class="modal fade picker-kas" id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAktivaSelectPerkiraan">
                <thead class="text-center">
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
<!-- End modal aktiva select perkiraan-->

<!-- start modal edit aktiva select perkiraan -->
<div class="modal fade picker-kas" id="formEditSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelEditAktivaSelectPerkiraan">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Perkiraan</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tabel_dataEditAktivaSelectPerkiraan" class="text-left"></tbody>
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
{{-- End modal edit aktiva select perkiraan  --}}


