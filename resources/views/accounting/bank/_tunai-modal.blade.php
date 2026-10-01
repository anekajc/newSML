  {{-- Kolom Action + dobel-klik mengikuti kartu hutang Memorial Koreksi (#formMkKartuPT):
       baris faktur -> + (tanya nominal) atau dobel-klik (lunasi seluruh sisa saldo); baris
       pelunasan milik bukti ini merah -> tombol hapus atau dobel-klik (konfirmasi lalu batalkan).
       Lihat tunaiRowHtml() di public/js/bank.js; warnanya di @section('css') bank.blade.php. --}}
  <div class="modal fade rt-picker-v2" id="formTunai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
      <div id="" class="modal-content ">

        <div id= "" class="">
        <div class="modal-header">


            <h5 class="modal-title" id="">Pelunasan Hutang</h5>
          <button type="button" class="close" onclick="selesaiTunai()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>


        <div id="" class="">
        <div class="modal-body">

          <div class="container-fluid" >

            <div class="row">
              <div class="col-md-4">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Nobukti</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control " id="input_tunai_nobukti" disabled>
                    </div>
                  </div>

                </div>

              </div>

              <div class="col-md-4">
                <div class="row">


                <div class="col-md-4">
                  <div class="form-group">
                    <label>Custsupp</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control " id="input_tunai_custsupp" disabled>
                  </div>
                </div>

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
              <!-- <div class="container-fluid"> -->


              <table id="tabel_add_list_tunai" class="dph-tb">
                <thead>
                  <tr>
                    <th scope="col">Action</th>
                    <th scope="col">NoFaktur</th>
                    <th scope="col">NoRetur</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">NoPelunasan</th>
                    <th scope="col">Debet</th>
                    <th scope="col">Kredit</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Valas</th>
                    <th scope="col">Kurs</th>

                  </tr>
                </thead>


                <tbody id="tabel_data_add_list_tunai" class="text-left" >

                  <tr >

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
            <!-- </div> -->
              <!-- <button onclick="buttonSubKategori()">tes</button> -->
            </div>
              </div>








            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

              </div>




          </div>





        </div>


        <div class="modal-footer">



          {{-- Hanya menutup: tiap pelunasan sudah tersimpan (sementara) saat barisnya diklik
               dan dibukukan saat item disimpan. Lihat selesaiTunai() di public/js/bank.js. --}}
          <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary" onclick="selesaiTunai()">Selesai</button>
          <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
        </div>
        </div>

        </div>

      </div>

<!-- End modal add-->








