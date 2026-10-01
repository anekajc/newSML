{{-- Modal pemilih serbaguna #formModalOpen (1 modal dipakai beberapa fungsi, isinya diinject
     halaman). Dulu disediakan layout newmaster; layout newmasterTest tidak punya dan tidak boleh
     diubah, jadi dipindahkan ke partial ini. Id di dalamnya (#namaModalOpen, #tabelModalOpen,
     #theadOpen, #tabel_dataModalOpen) sengaja sama persis dengan versi layout lama.
     Tampilan mengikuti modal pemilih purchasing (public/css/picker-kas.css). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

<div class="modal fade picker-kas" id="formModalOpen" tabindex="-1" role="dialog" aria-labelledby="namaModalOpen" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="namaModalOpen"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelModalOpen">
                <thead id="theadOpen" class="text-center">
                  <tr></tr>
                </thead>
                <tbody id="tabel_dataModalOpen" class="text-left">
                  <tr></tr>
                </tbody>
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
