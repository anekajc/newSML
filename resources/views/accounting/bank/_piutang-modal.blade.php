  {{-- Pelunasan Piutang — Lawan 113100 (Piutang Usaha) pada BBK/BBM. Sama seperti #formTunai
       (Pelunasan Hutang, _tunai-modal.blade.php) dan memakai fungsi JS yang sama; elemen mana
       yang dipakai dipilih tunaiUI() di public/js/bank.js. Satu baris per faktur; pelunasan bukti
       ini tampil merah tepat di bawah fakturnya (lihat tunaiRowHtml()). --}}
  <div class="modal fade rt-picker-v2" id="formPiutang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
          <div class="modal-content">

              <div class="modal-header">
                  <h5 class="modal-title">Pelunasan Piutang</h5>
                  <button type="button" class="close" onclick="selesaiTunai()" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="container-fluid">
                      <div class="row" style="padding: 1rem 0;">
                          <div class="col-md-3">
                              <div class="row">
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <label>Nobukti</label>
                                      </div>
                                  </div>
                                  <div class="col-md-8">
                                      <div class="form-group">
                                          <input type="text" class="form-control " id="input_piutang_nobukti"
                                              disabled>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          {{-- Nama customer — diisi buttonAddPickCustSuppX() di public/js/bank.js. --}}
                          <div class="col-md-3">
                              <div class="row">
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <label>Nama</label>
                                      </div>
                                  </div>
                                  <div class="col-md-8">
                                      <div class="form-group">
                                          <input type="text" class="form-control " id="input_piutang_namacustsupp"
                                              disabled>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="col-md-3">
                              <div class="row">
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <label>Customer</label>
                                      </div>
                                  </div>
                                  <div class="col-md-8">
                                      <div class="form-group">
                                          <input type="text" class="form-control " id="input_piutang_custsupp"
                                              disabled>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="col-md-3">
                              <div class="form-group">
                                  <input class="po-search-inp" type="search" id="searchTunai" placeholder="Cari data"
                                      autocomplete="off" style="width: 100%" oninput="filterTunai()">
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row" style="margin-top:20px">
                      <div class="col-12">
                          <p class="text-muted small mb-2">Klik <b>+</b> untuk membayar faktur (nominal bisa
                              diubah), atau dobel-klik baris faktur untuk langsung melunasi seluruh sisa
                              saldonya. Baris merah = pelunasan bukti ini; klik tombol hapus atau dobel-klik
                              barisnya untuk <b>membatalkan</b>.</p>
                      </div>
                      <div class="col-12" style="overflow:auto;  max-height: 400px">
                          <table id="tabel_add_list_piutang" class="dph-tb">
                              <thead>
                                  <tr>
                                      <th scope="col">Action</th>
                                      <th scope="col">NoFaktur</th>
                                      <th scope="col">NoRetur</th>
                                      <th scope="col">Tanggal</th>
                                      <th scope="col">NoBukti</th>
                                      <th scope="col">Debet</th>
                                      <th scope="col">Kredit</th>
                                      <th scope="col">Saldo</th>
                                      <th scope="col">Valas</th>
                                      <th scope="col">Kurs</th>
                                  </tr>
                              </thead>

                              <tbody id="tabel_data_add_list_piutang" class="text-left">
                                  <tr>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                      <td>-</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  {{-- Hanya menutup, sama seperti #formTunai. Lihat selesaiTunai() di public/js/bank.js. --}}
                  <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                      onclick="selesaiTunai()">Selesai</button>
              </div>
          </div>
      </div>
  </div>
