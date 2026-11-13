{{-- DIBANGKITKAN dari purchaseOrderNonStock.blade.php untuk halaman PO gabungan (/purchaseorder).
     Semua id, fungsi, variabel global, name dan class show/hide yang bentrok dengan
     purchaseOrder.blade.php diberi awalan ns_. Ubah PO Non Stock di file ini, bukan di blade lama. --}}
<div id="ns_page2" class="container-fluid" style="display: none" >
<div id="ns_formBsGrid">
  {{-- Margin negatif -80px/-120px yang dulu di sini disesuaikan untuk layout lama
       (purchasing.newmaster) - di newmasterx (padding atas #content lebih kecil,
       lihat @section('css')) itu membuat judul & tombol naik terlalu jauh. Disamakan
       dengan #page2 milik purchaseOrder.blade.php: tanpa margin negatif. --}}
  <div class="row">
    <div class="col-6 text-left">
      <!-- <h2>Form PO (Non-Stock)</h2> -->
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="ns_buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="ns_modalBodyAddMain" class="">
    <div class="modal-body" style="">
      <div class="row">
        <input type="hidden" class="form-control" id="ns_input_add_nourut">
        <div class="col-md-3">
          <div class="row">

            {{-- Jenis PO (PO Stock / PO Non Stock), pasangannya #input_add_jenispo di form PO Stock.
                 Diisi & dikunci oleh poIsiDropdownJenisPO(), diganti lewat poGantiJenisPO()
                 (purchaseOrder.blade.php). --}}
            <div class="col-md-4">
              <div class="form-group">
                <label for="ns_input_add_jenispo">Jenis PO</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <select id="ns_input_add_jenispo" class="form-control text-left" onchange="poGantiJenisPO(this, 'nonstock')">
                  <option value="nonstock">PO Non Stock</option>
                </select>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Supplier</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-12px;">
              {{-- Supplier bisa diketik: saran muncul di bawah (sama seperti Pelanggan menu SO). --}}
              <div class="input-group mb-3 position-relative">
                <input type="text" class="form-control text-left" placeholder="Cari Supplier..." id="ns_input_add_kodesupplier" autocomplete="off">
                <button class="btn btn-chip-biru btn-sm" id="ns_buttonAddListPelanggan" style="height:32px; border-radius:0;" onclick="ns_performSearchSupplier()">
                  <i class="bi bi-search"></i>
                </button>
                <div id="ns_po_dropdown_supplier" class="dropdown-menu w-100 po-dropdown-supplier"></div>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="ns_input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            {{-- Tanggal dipindah ke kolom Valas/Kurs, tepat di bawah Kurs. --}}

          </div>
        </div>

        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Nama Supplier" id="ns_input_add_namasupplier"  disabled>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Supplier" class="form-control text-left align-items-left" id="ns_input_add_alamatsupplier"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <select class="form-control" id="ns_input_add_valas" onchange="ns_onChangeValas()"></select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" id="ns_input_add_kurs"  disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="date" class="form-control text-left" id="ns_input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                  </div>
                </div>
              </div>
            </div>

            {{-- Perkiraan header sudah tidak diinput manual: perkiraan diambil otomatis dari
                 COA Biaya barang (Master Barang -> Master Headgroup/SubGroup). Header DBPO.PERKIRAAN
                 diisi server (PONonStockController@nilaiHeaderNonStock). Dikomentari, jangan dihapus.
            <div class="col-md-6" style="margin-top:-12px;">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Perkiraan</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6" style="margin-top:-12px;">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_add_perkiraan">
                    <button onclick="buttonAddListPerkiraan()" id="buttonAddListPerkiraan" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            --}}

            {{-- PPH23 & PPH21 hanya informatif (selalu disabled): nilainya dari setting PPH supplier
                 (DBCUSTSUPP.NPPH23 / NPPH22), saat edit dari nilai yang tersimpan di PO. --}}
            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>PPH23</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control text-right" id="input_add_pph23" value='0.00' disabled>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      <div class="col-md-3">
        <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>
              <div class="col-md-6">
                <select onchange="ns_onChangeTipePPN()" id="ns_input_add_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected>None</option>
                  <option value=1 >Exclude</option>
                  <option value=2 >Include</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 rodokNdukurTitik">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <select id="ns_input_add_pembayaran" onchange="ns_onChangeInputAddPembayaran()" class="form-control form-select-lg mb-3 text-left" aria-label=".form-select-lg example">
                    <option value=0 selected >Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-12px;">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="ns_input_add_hari" onblur="ns_onChangeHari()" value=0 min=0 >
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>PPH21</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control text-right" id="input_add_pph21" value='0.00' disabled>
                  </div>
                </div>
              </div>
            </div>

        </div>
      </div>
    </div>

          <hr/>
          <div class="row" style='margin-top:5px'>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-lg btn-show-hide-header btn-chip-biru" style="
                height: 38px;
                width: 38px;
                margin-top: -35px;
                padding: 0;
                border-radius: 8px;
                font-size: 1.15rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="ns_buttonShowHideHeader()" title="Show/Hide Header"><i class="bi bi-truck"></i></button>
            </div>
          </div>
            <div class="ns_showhidemodalbodyaddmain mt-4" id="ns_modalBodyAddMainHeader" style="display: none;">
              <div class="row" style='margin-top:-30px'>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <select class="form-control" id="ns_input_add_kodealamatkirim" onchange="ns_onChangeKodeAlamatKirim()">
                          <option value="GMPL">GMPL</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_add_alamatkirim"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Ekspedisi</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input class="form-control" id="ns_input_add_kodeekspedisi" value="-" readonly>
                        <button onclick="ns_buttonAddListLokasiPenerima()" id="ns_buttonAddListLokasiPenerima" value="-" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_add_ekspedisi"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Keterangan</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_add_keterangan" onblur="ns_onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="date" class="form-control text-left" id="ns_input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="ns_onChangeTgglKirim()">
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="ns_buttonAddListBackOffice()" id="ns_buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="ns_input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="ns_input_add_namabackoffice"  disabled>
                            <button onclick="ns_buttonAddListBackOffice()" id="ns_buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="ns_buttonAddListPIC()" id="ns_buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="ns_input_add_kodepic"  >
                        <input type="text" class="form-control" id="ns_input_add_namapic"  disabled>
                        <button onclick="ns_buttonAddListPIC()" id="ns_buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="ns_input_add_kodesales" >
                    <input type="text" class="form-control" id="ns_input_add_namasales"  disabled>
                    <button onclick="ns_buttonAddListSales()" id="ns_buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>

                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="ns_onChangeDraftPO()" id="ns_input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

        </div>
            <hr/>
      </div>

    </div>

      <div class="ns_showhidemodalbodyaddmain container-fluid" id="ns_modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto; margin-top:-35px;">
          <!-- <input type="hidden" name="ns_noUrut" id="ns_input_add_noUrut" value="" /> -->
          <div class="row">
            {{-- Pakai .data-table supaya tampilannya sama dengan tabel item purchaseOrder.blade.php. --}}
            <table id="ns_tabel_add" class="data-table">
              <thead id='ns_tabel_data_header' class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Alias</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col" id="th_add_actions">Actions</th>
                </tr>
              </thead>
              <tbody id="ns_tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info-circle-fill"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id='ns_buttonTambahItem' class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              transition: background-color 0.3s, box-shadow 0.3s;"
              onclick="ns_buttonAddAddItem()">Tambah Item</button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="ns_addAddItem" class="container-fluid ns_showhide">
          <hr/>

            <div class="row">
              <div class="col-12">
                <h4 id="ns_h4AddAddItem">Add Item</h4>
                <h4 id="ns_h4AddEditItem">Edit Item</h4>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12">

              {{-- PO Non Stock tidak lagi punya pilihan Jasa/FOC di UI - sumber barang
                   cukup lewat dropdown "+ Dari" di bawah. pFOC tetap dikirim ke sp_PO
                   (submitAddAdd/submitAddEdit), jadi field-nya dipertahankan sebagai
                   hidden bernilai tetap 0. pJasa sendiri dipaksa 1 di server
                   (PONonStockController@spAdd), tidak pernah dibaca dari form. --}}
              <input type="hidden" id="ns_input_add_add_foc" value="0">

              <div class="row">

                {{-- Kolom kiri : identitas barang --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">+ Dari</label>
                    <div class="col-md-4">
                      <select id="ns_input_add_add_outstanding" class="form-control" onchange="ns_onChangeOutstanding()">
                        <option value="PR" selected>PR Non Stock</option>
                        <option value="NONPR">Non PR</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Kode Barang</label>
                    <div class="col-md-4">
                      <div class="input-group">
                        <input type="text" class="form-control text-left" id="ns_input_add_add_kodebarang">
                        <button class="btn btn-chip-biru btn-sm" id="ns_buttonBrowseBarangItem" style="height:32px; border-radius:0;" onclick="ns_performSearch()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <input type="text" class="form-control text-left" id="ns_input_add_add_namabarangasli" placeholder="Nama Barang" readonly>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Nama Alias</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="ns_input_add_add_namabarang">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Costing</label>
                    <div class="col-md-3">
                      <div class="input-group">
                        <input type="text" class="form-control" id="input_add_add_costing">
                        <button class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick="buttonAddListCosting()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                    <label class="col-md-3 mb-0">Sub-Costing</label>
                    <div class="col-md-3">
                      <div class="input-group">
                        <input type="text" class="form-control" id="input_add_add_subcosting">
                        <button class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick="buttonAddListSubCosting()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                </div>

                {{-- Kolom kanan : qty, harga, diskon --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">QTY</label>
                    <div class="col-md-3">
                      <input type="text" id="ns_input_add_add_qty" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right ns_input-partial-number" onblur="ns_cekQntStock()" tabindex="5">
                    </div>
                    <label class="col-md-3 mb-0">Satuan</label>
                    <div class="col-md-3">
                      <select id="ns_input_add_add_nosat" class="form-control">
                        <option value=0 selected>Tidak</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Harga</label>
                    <div class="col-md-9">
                      <input type="text" id="ns_input_add_add_harga" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right ns_input-partial-number" onchange="ns_onChangeInputAddAddHarga()" tabindex="6">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc(%)</label>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="ns_input_add_add_discpersen1" value=0 onChange='ns_calculateDiscRp()' tabindex="8">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="ns_input_add_add_discpersen2" value=0 onChange='ns_calculateDiscRp()' tabindex="9">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="ns_input_add_add_discpersen3" value=0 onChange='ns_calculateDiscRp()' tabindex="10">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc RP</label>
                    <div class="col-md-3">
                      <input type="text" id="ns_input_add_add_discrp" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right ns_input-partial-number" onchange="ns_reverseCalculateDiscPercent()" tabindex="7">
                    </div>
                    <label class="col-md-3 mb-0">Satuan Alias</label>
                    <div class="col-md-3">
                      <input type="text" class="form-control text-right" id="input_add_add_satuanAlias" tabindex="7">
                    </div>
                  </div>

                  {{-- No. PNW PO: field newSML, sementara disembunyikan (belum dipakai), jangan dihapus. --}}
                  <div class="form-row align-items-center mb-2" hidden>
                    <label class="col-md-3 mb-0">No. PNW PO</label>
                    <div class="col-md-9">
                      <div class="input-group">
                        <input type="text" class="form-control" value="-" id="ns_input_add_add_nopnwpo" readonly>
                        <button onclick="ns_buttonAddAddListPWO()" id="buttonAddListPWO" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" type="button">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                </div>

              </div>

              <div class="row" hidden>
                <div class="col-md-1">
                  <input type="text" min="1" max="100" class="form-control" id="ns_input_add_add_noPPL" tabindex="1">
                </div>
                <div class="col-md-1">
                  <input type="text" min="1" max="100" class="form-control" id="ns_input_add_add_urutPPL" tabindex="1">
                </div>
              </div>

            </div>

            <div class="col-md-12">
              <div id="ns_divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="ns_tabel_add_harga_terakhir" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Qty</th>
                            <th style="padding: 4px 12px;" scope="col">Satuan</th>
                            <th style="padding: 4px 12px;" scope="col">Valas</th>
                            <th style="padding: 4px 12px;" scope="col">Kurs</th>
                            <th style="padding: 4px 12px;" scope="col">Harga</th>
                            <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                            <th style="padding: 4px 12px;" scope="col">Hrg. Nett</th>
                          </tr>
                        </thead>
                        <tbody id="ns_tabel_data_add_harga_terakhir" class="text-left" >
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
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                </div>
              </div>
            </div>


            <div class="col-md-12">
              <div id="ns_divStockProyeksi" style="display:none;">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Stock Proyeksi</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="ns_tabel_add_stock_proyeksi" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Stock</th>
                            <th style="padding: 4px 12px;" scope="col">Out PO</th>
                            <th style="padding: 4px 12px;" scope="col">Out SO</th>
                            <th style="padding: 4px 12px;" scope="col">S Marketing</th>
                          </tr>
                        </thead>
                        <tbody id="ns_tabel_data_add_stock_proyeksi" class="text-left" >
                          <tr>
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
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">

              <button type="button" class="btn btn-lg btn-batal-add" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="ns_closeShowHideAdd()">Batal</button>

              <button type="button" class="btn btn-lg btn-histori-harga" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="ns_showTableHargaTerakhir()">Histori Harga</button>

              <button type="button" class="btn btn-lg btn-stock-proyeksi" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="ns_showTableStockProyeksi()">Stock Proyeksi</button>

              <button type="button" id="ns_submitAddAdd" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="ns_submitAddAdd()">Simpan</button>

              <button type="button" id="ns_submitAddEdit" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="ns_submitAddEdit()">Simpan</button>
            </div>

          </div>

        </div>

        <!-- END ADD ADD -->

        <!-- ADD EDIT -->

        <div  id="ns_addEditItem" class="container-fluid ns_showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="ns_input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="ns_input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="ns_buttonAddEditListBarang()" id="ns_buttonAddEditListBarang"  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="ns_input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="ns_input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="ns_input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="ns_tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qty</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="ns_tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
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
</div>

            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="ns_input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="ns_input_add_edit_nosat" onchange="ns_onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" tabindex='2'>
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="ns_input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="ns_onChangeInputAddAddHarga()" id="ns_input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="ns_input_add_edit_disc" onChange="ns_onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="ns_input_add_edit_discrp" onChange="ns_onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="ns_input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="ns_input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="ns_input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="ns_closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

  <div class="container-fluid" style="margin-top: -10px;">
  <div class="row">

    <!-- Total = jumlah Subtotal seluruh item -->
    <div class="col">
      <div class="form-group">
        <label>Total</label>
        <input type="text" class="form-control text-right" id="ns_input_add_total" value="0.00" disabled>
      </div>
    </div>

    <!-- Disc % -->
    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="number" class="form-control text-right" id="ns_input_add_disc" onblur="ns_onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="text" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right ns_input-partial-number" id="ns_input_add_discrp" onblur="ns_onChangeInputAddDiscRp()" value="0.00">
      </div>
    </div>

    <!-- DPP -->
    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="ns_input_add_dpp" value="0.00" disabled>
      </div>
    </div>

    <!-- PPN -->
    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="ns_input_add_ppn" value="0.00" disabled>
      </div>
    </div>

    <!-- Grand Total -->
    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="ns_input_add_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div>
</div>

</div>{{-- /#formBsGrid --}}
</div>

<!-- ns_page3 -->

<div id="ns_page3" class="container-fluid" style="display: none" >
<div id="ns_formBsGrid">
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail PO (Non-Stock)</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="ns_buttonCloseForm()">Close</button>
        </div>
      </div>

<div id="" class="">
  <div class="modal-body" >
<!-- <div class="container-fluid"> -->
  <div class="row">

    <input type="hidden" class="form-control" id="ns_input_detail_nourut" >
    <div class="col-md-3">

      <div class="row">

        <div class="col-md-4" style="margin-top:-40px;">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="ns_input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-center" id="ns_input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-center" id="ns_input_detail_kodepelanggan" disabled>
      </div>
    </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="row">
        <!-- <div class="col-md-6">
          <div class="row">


        <div class="col-9">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button class="btn btn-primary btn-sm text-right" id="ns_buttonAddListPelanggan" onclick="ns_buttonAddListPelanggan()"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div>
    </div>
    <div class="col-md-6">
    </div> -->


      <!-- <div class="col-md-6">
        <div class="row"> -->



        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="ns_input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-center" id="ns_input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        <!-- </div>
      </div> -->

      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="ns_buttonAddListValas()" id="ns_buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="ns_input_detail_valas"  disabled>
            <!-- <button onclick="ns_buttonAddListValas()" id="ns_buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-center" id="ns_input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-center" id="ns_input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="ns_input_detail_pembayaran" disabled class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Tunai</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-left" id="ns_input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="ns_input_detail_tipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-primary" onclick="ns_buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="ns_buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-primary btn-lg" style="
  height: 30px;
  margin-top: -40px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="ns_buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
</div>
</div>
  <div class="mt-4" id="ns_modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control" id="ns_input_detail_kodealamatkirim" >
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="ns_buttonAddListLokasiPenerima()" id="ns_buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="ns_input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_detail_alamatlokasipenerima"  value ='-'disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="ns_buttonAddListPIC()" id="ns_buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="ns_input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="ns_input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="ns_input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="ns_input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="ns_buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="ns_buttonAddListPIC()" id="ns_buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="ns_input_detail_kodepic"  >
            <input type="text" class="form-control" id="ns_input_detail_namapic"  disabled>
            <!-- <button onclick="ns_buttonAddListPIC()" id="ns_buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="ns_buttonAddListBackOffice()" id="ns_buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="ns_input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="ns_input_detail_namabackoffice"  disabled>
            <!-- <button onclick="ns_buttonAddListBackOffice()" id="ns_buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="ns_buttonAddListSales()" id="ns_buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="ns_input_detail_kodesales" >
          <input type="text" class="form-control" id="ns_input_detail_namasales"  disabled>
          <!-- <button onclick="ns_buttonAddListSales()" id="ns_buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="ns_input_detail_kodesales" >
      <input type="text" class="form-control" id="ns_input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="ns_input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="ns_noUrut" id="ns_input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="ns_tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">DPP</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="ns_tabel_data_detail" class="text-left" >

        <tr >

          <td></td>
          <td></td>

            <td class="text-center">
              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
              <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
              <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
            </td>
      </tr>
      </tbody>

    </table>
  </div>
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="ns_buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="ns_input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="ns_input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="ns_input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="ns_input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="ns_input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>{{-- /#formBsGrid --}}
</div>

<!-- ns_page3 end input_add -->

