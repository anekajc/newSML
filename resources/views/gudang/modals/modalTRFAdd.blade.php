
<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Add</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="modalBodyAddListGudang" class="showhidemodalbodyadd">
        <div class="modal-body" >

          <div class="container-fluid mt-4" >

            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Gudang Asal</h3>
              </div>
            </div>

            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-60px;">
                <table id="tabel_add_list_gudang" class="table table-bordered table-hover table-striped table-responsive-lg">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">Kode</th>
                      <th style="padding: 4px 12px;" scope="col">Nama Gudang</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudang" class="text-left" >
                    <tr >
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                      </td>
                      <td>-</td>
                      <td>-</td>
                      
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button type="button" class="btn btn-danger btn-lg" 
          style="
          margin-top:-10px;
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>
      
        <!-- <h1>Tes Modal</h1> -->

        <div id="modalBodyAddListPelanggan" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Pelanggan</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-60px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_pelanggan" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Alamat</th>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_pelanggan" class="text-left" >
                  <tr >
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                      </td>
                </tr>
                </tbody>
              </table>
            <!-- </div> -->
              <!-- <button onclick="buttonSubKategori()">tes</button> -->
            </div>
              </div>
              </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button type="button" class="btn btn-danger btn-lg" 
          style="
          margin-top:-10px;
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>

      <div id="modalBodyAddAddListBarangAll" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div id="modalBodyAddAddListBarangAllTitle" class="col-md-9" style="margin-top:-40px;">
              <h3>Barang All</h3>
            </div>
            <div class="col-3 text-right form-group" style="margin-top:-30px;">
              <input id="input_search_barang_all" type="text" name="" value="" class="form-control" onkeypress="searchBarangAll(event)">
              <label for="input_search_barang_all" class="search-label">SEARCH:</label>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-40px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_barangall" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barangall" class="text-left" >
                @for ($i = 0; $i < count($listBarangAll); $i++)
                <tr >
                  <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('{{ $listBarangAll[$i]->Kodebrg }}')" type="button" ><i class="bi bi-plus"></i></button></td>
                  <td>{{ $listBarangAll[$i]->Kodebrg }}</td>
                  <td>{{ $listBarangAll[$i]->NamaBrg }}</td>
                      
              </tr>
              @endfor
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg" 
        style="
        margin-top:-10px;
        height: 30px; 
        padding: 4px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
        font-weight: 600; 
        text-transform: uppercase; 
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

  <div id="modalBodyAddAddListBarangFOC" class="showhidemodalbodyadd">
    <div class="modal-body">
      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-12">
            <h3>Barang</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-12" style="overflow:auto;">
            <table id="tabel_add_list_barang_foc" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th style="white-space:nowrap;" scope="col">Kode Barang</th>
                  <th style="white-space:nowrap;" scope="col">Nama Barang</th>
                  <th style="white-space:nowrap;" scope="col">Part Number</th>
                  <th style="white-space:nowrap;" scope="col">Merk</th>
                  <th style="white-space:nowrap;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barang_foc" class="text-left" >
                <tr>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

<div id="modalBodyAddAddListBarangNonFOC" class="showhidemodalbodyadd">
  <div class="modal-body">
    <div class="container-fluid mt-4" >
      <div class="row">
        <div class="col-12">
          <h3>Barang</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-12" style="overflow:auto;">
          <table id="tabel_add_list_barang_nonfoc" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Part Number</th>
                <th scope="col">Merk</th>
                <th scope="col">Sat</th>
                <th scope="col">Qnt PR</th>
                <th scope="col">Qnt PO</th>
                <th scope="col">Sisa PR</th>
                <th scope="col">No. PR</th>
                <th scope="col">No. SO Cust</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_barang_nonfoc" class="text-left" >
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
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
  </div>
</div>

<div id="modalBodyAddAddListBarangNonFOCPlus" class="showhidemodalbodyadd">
    <div class="modal-body">
      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-12">
            <h3>Barang</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-12" style="overflow:auto;">
            <table id="tabel_add_list_barang_nonfocplus" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th style="white-space:nowrap;" scope="col">Kode Barang</th>
                  <th style="white-space:nowrap;" scope="col">Nama Barang</th>
                  <th style="white-space:nowrap;" scope="col">QNT Sat 1</th>
                  <th style="white-space:nowrap;" scope="col">QNT Sat 2</th>
                  <th style="white-space:nowrap;" scope="col">Sat</th>
                  <th style="white-space:nowrap;" scope="col">Sisa</th>
                  <th style="white-space:nowrap;" scope="col">Sisa 2</th>
                  <th style="white-space:nowrap;" scope="col">No. Bukti</th>
                  <th style="white-space:nowrap;" scope="col">Part Number</th>
                  <th style="white-space:nowrap;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barang_nonfocplus" class="text-left" >
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
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

  <div id="modalBodyAddListPIC" class="showhidemodalbodyadd">
    <div class="modal-body">
      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>PIC</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_pic" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_pic" class="text-left" >
              <tr>
                <td>-</td>
                <td>-</td>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg" 
      style="
      margin-top:-10px;
      height: 30px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

  <div id="modalBodyAddListPWO" class="showhidemodalbodyadd">
    <div class="modal-body">

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Nomor Penawaran PO</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_pwo" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Nomor Bukti</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Supplier</th>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_pwo" class="text-left" >
              <tr >
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
            </tbody>
          </table>
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger btn-lg" 
      style="
      margin-top:-10px;
      height: 30px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

    <div id="modalBodyAddListLokasiPenerima" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Lokasi Penerima</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_lokasipenerima" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_lokasipenerima" class="text-left" >
              <tr >
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg" 
      style="
      margin-top:-10px;
      height: 30px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

<div id="modalBodyAddListAlamatKirim" class="showhidemodalbodyadd">
  <div class="modal-body" >
    <div class="container-fluid mt-4" >
      <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Dikirim Ke</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_alamatkirim" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Nomor</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Alamat</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_alamatkirim" class="text-left" >
              <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <button type="button" class="btn btn-danger btn-lg" 
    style="
    margin-top:-10px;
    height: 30px; 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 0.75rem; 
    font-weight: 600; 
    text-transform: uppercase; 
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    onclick="buttonAddListBatal()">Batal</button>
  </div>
</div>

<div id="modalBodyAddListNoSo" class="showhidemodalbodyadd">
  <div class="modal-body" >
    <div class="container-fluid mt-4" >
      <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Nomor SO</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_noSo" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">No SO</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">No. PO Cust</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_noSo" class="text-left" >
              <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger btn-lg" 
    style="
    margin-top:-10px;
    height: 30px; 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 0.75rem; 
    font-weight: 600; 
    text-transform: uppercase; 
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    onclick="buttonAddListBatal()">Batal</button>
  </div>
</div>

    <div id="modalBodyAddListBackOffice" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Back Office</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_backoffice" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_backoffice" class="text-left" >
              <tr >
                <td>-</td>
                <td>-</td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
            </tr>
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-danger btn-lg" 
      style="
      margin-top:-10px;
      height: 30px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddListBatal()">Batal</button>
    </div>
  </div>

      <div id="modalBodyAddListSales" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-md-4" style="margin-top:-40px;">
              <h3>Sales</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_sales" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_sales" class="text-left" >
                <tr >
                  <td>-</td>
                  <td>-</td>
                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-danger btn-lg" 
        style="
        margin-top:-10px;
        height: 30px; 
        padding: 4px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
        font-weight: 600; 
        text-transform: uppercase; 
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button>
      </div>
    </div>

            <div id="modalBodyAddListValas" class="showhidemodalbodyadd">
                <div class="modal-body">
                    <div class="container-fluid mt-4" >
                        <div class="row">
                            <div class="col-md-4" style="margin-top: -40px;">
                                <h3>Valas</h3>
                            </div>
                        </div>
                    <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                    <div class="row">
                        <div class="col-12" style="overflow:auto; margin-top:-30px;">
                        <!-- <div class="container-fluid"> -->
                        <table id="tabel_add_list_valas" class="table table-bordered table-hover table-striped table-responsive-lg">
                            <thead class="text-center bg-primary text-white">
                                <tr>
                                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                                    <th style="padding: 4px 12px;" scope="col">Kurs</th>
                                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tabel_data_add_list_valas" class="text-left" >
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                                        <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- </div> -->
                        <!-- <button onclick="buttonSubKategori()">tes</button> -->
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
                    <button type="button" class="btn btn-danger btn-lg" 
                    style="
                    margin-top:-10px;
                    height: 30px; 
                    padding: 4px 12px; 
                    border-radius: 20px; 
                    font-size: 0.75rem; 
                    font-weight: 600; 
                    text-transform: uppercase; 
                    transition: background-color 0.3s, box-shadow 0.3s;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
                    onclick="buttonAddListBatal()">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End modal add-->
