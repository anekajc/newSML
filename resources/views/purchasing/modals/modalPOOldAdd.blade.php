
{{-- ============================================================
     Picker "klik baris" (mengikuti gudang/permintaanpemakaian).
     Kolom Actions + tombol "+" sudah dihapus dari semua tabel di file ini; baris
     yang dibangun di halaman pemakai diberi class .pick-row + onclick.

     CSS-nya sengaja ditaruh DI SINI, bukan di layout atau di report-table.css:
       - report-table.css tidak dimuat di layout purchasing/marketing, dan memuatnya
         di sana merusak layout Bootstrap halaman-halaman itu (lihat catatan di
         public/css/po-table-header.css);
       - dengan menempel di file modal, style ikut ke SEMUA halaman yang meng-include
         modal ini (purchaseOrder + penawaranso) tanpa perlu menyentuh file bersama.

     Desain header disamakan dengan #tabel_data_header di purchaseOrder: abu muda,
     uppercase, garis bawah tipis - bukan lagi bg-primary biru.
     ============================================================ --}}
<style>
  /* Baris bisa diklik: kursor tangan + highlight, supaya user tahu barisnya aktif. */
  #form tbody tr.pick-row {
    cursor: pointer;
    transition: background-color .12s;
  }

  #form tbody tr.pick-row:hover td {
    background-color: #eef2ff;
  }

  /* Header tabel picker - seragam dengan #tabel_data_header di purchaseOrder. */
  #form thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee !important;
    border-top: none !important;
  }

  #form tbody td {
    border-top: none !important;
    border-bottom: 1px solid #f1f3f5 !important;
    font-size: 13px;
    vertical-align: middle;
  }

  /* Tiap pembungkus tabel picker masih membawa inline style margin-top negatif
     (-30px s/d -60px) - dulu untuk mengimbangi judul <h3> di atasnya, padahal judul itu
     sekarang sudah dikomentari. Akibatnya tabel + kotak pencarian tertarik naik sampai
     menempel ke header modal. Dinolkan di sini (butuh !important karena melawan inline
     style) supaya berlaku untuk semua picker sekaligus, tanpa mengedit 15 style inline. */
  #form .showhidemodalbodyadd .row > .col-12 {
    margin-top: 0 !important;
  }

  /* Baris toolbar DataTables (kolom "length" + kotak pencarian) dipaksa selebar tabel.
     Build DataTables di sini adalah versi integrasi Bootstrap (lihat
     public/css/datatables.min.css: "div.dataTables_wrapper div.dataTables_filter{text-align:right}"),
     yang menaruh kotak pencarian di dalam <div class="col-sm-12 col-md-6"> alias SEPARUH
     KANAN. Akibatnya rata-kanan hanya mentok di tepi kolom itu, bukan tepi tabel - itu
     sebabnya kotak pencarian berhenti di tengah. Kolomnya dijadikan 100% supaya tepi
     kanannya sejajar dengan tepi kanan tabel. */
  #form .dataTables_wrapper > .row:first-child > div {
    flex: 0 0 100%;
    max-width: 100%;
  }

  /* Kotak pencarian: buang tulisan "Search:", ganti placeholder jadi "Cari Data" (diisi
     skrip di bawah), pasang ikon kaca pembesar di dalam kolomnya, dan geser rata kanan.
     Dipakai float:none + text-align:right, bukan float:right: posisinya sama-sama mentok
     kanan, tapi tanpa float tidak ada risiko tabel di bawahnya naik menimpa kotak ini. */
  /* display:block WAJIB ada di sini. DataTables memberi div pencarian id "<idTabel>_filter",
     dan halaman pemakai masih punya sisa aturan lama yang menjadikannya flex container:
     #tabel_add_list_pelanggan_filter / _noSo_filter / _sales_filter { display:flex }
     (purchaseOrder.blade.php + penawaranso.blade.php). Di dalam flex container,
     text-align:right TIDAK berpengaruh pada <label>-nya - itu sebabnya khusus modal
     Supplier/No SO/Sales kotak pencarian tetap menempel di kiri, sedangkan modal lain
     (Barang, Ekspedisi) yang tidak punya aturan itu sudah benar. Dikembalikan ke block
     supaya text-align:right berlaku. Selektor ini (#id + .class) menang atas aturan
     lama itu (#id saja). */
  #form .dataTables_filter {
    display: block;
    float: none;
    width: 100%;
    text-align: right;
    margin-bottom: 8px;
  }

  #form .dataTables_filter label {
    font-size: 0; /* menyembunyikan teks "Search:" tanpa mengubah markup DataTables */
    margin: 0;
    display: inline-block;
  }

  #form .dataTables_filter input {
    font-size: 13px;
    margin-left: 0;
    width: 240px;
    max-width: 100%;
    padding: 7px 10px 7px 32px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
  }

  #form .dataTables_filter input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #e8edff;
  }

  /* Kotak search FOC (server-side, Enter untuk cari) - mengikuti gaya kotak search
     di pembelianpermintaanagen: lebar tetap + ikon kaca pembesar, bukan melebar
     penuh seperti form-control biasa. */
  #form #input_search_barang_foc {
    width: 260px;
    max-width: 100%;
    font-size: 13px;
    padding: 7px 10px 7px 32px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
  }

  #form #input_search_barang_foc:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #e8edff;
  }
</style>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="namaHeaderTable" class="modal-title">Add</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


        <!-- <h1>Tes Modal</h1> -->

        <div id="modalBodyAddListPelanggan" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            {{-- <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Ini Modal Supplier</h3>
              </div>
            </div> --}}
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-60px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_pelanggan" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Alamat</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_pelanggan" class="text-left" >
                </tbody>
              </table>
            <!-- </div> -->
              <!-- <button onclick="buttonSubKategori()">tes</button> -->
            </div>
              </div>
              </div>
        </div>
        <div class="modal-footer">
          <!--   -->
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


	
<!----====================================ttd======================================= -->
<div id="modalBodyAddListTtd" class="showhidemodalbodyadd">
  <div class="modal-body" >
    <div class="container-fluid mt-4" >
      {{-- <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Ttd</h3>
        </div>
      </div> --}}
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_Ttd" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Id Backoffice</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_Ttd" class="text-left" >
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



<!-- =========================end ttd ===================================================== -->





<!----====================================lokasi penerima / kebun======================================= -->
<div id="modalBodyAddListKebun" class="showhidemodalbodyadd">
  <div class="modal-body" >
    <div class="container-fluid mt-4" >
      {{-- <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Ttd</h3>
        </div>
      </div> --}}
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_Kebun" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Kebun</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_Kebun" class="text-left" >
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



<!-- =========================end ttd ===================================================== -->



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
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barangall" class="text-left" >
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!--   -->
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

    {{-- ini modal barang  kalo pake FOC--}}
  <div id="modalBodyAddAddListBarangFOC" class="showhidemodalbodyadd">
    <div class="modal-body">
      <div class="container-fluid mt-4" >
        {{-- Pencarian server-side khusus browse FOC di Purchase Order. Wrapper default
             hidden karena section FOC ini dipakai bersama penawaranso (masih memuat
             semua barang di depan) - hanya purchaseOrder yang menampilkan wrapper ini. --}}
        <div class="row mb-2" id="wrapSearchBarangFOC" style="display:none;">
          <div class="col-12 d-flex justify-content-end">
            <input id="input_search_barang_foc" type="text" class="form-control"
              placeholder="Cari Data, lalu tekan Enter" onkeypress="searchBarangFOC(event)">
          </div>
        </div>
        <div class="row">
          <div class="col-12" style="overflow:auto;">
            <table id="tabel_add_list_barang_foc" class="table table-bordered table-striped"  >
              <thead class="text-center" style='white-space:nowrap;'>
                <tr>
                  <th style="white-space:nowrap;" scope="col">Kode Barang</th>
                  <th style="white-space:nowrap;" scope="col">Nama Barang</th>
                  <th style="white-space:nowrap;" scope="col">Part Number</th>
                  <th style="white-space:nowrap;" scope="col">Merk</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barang_foc" class="text-left" >
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
        <div class="col-12" style="overflow:auto;">
          <table id="tabel_add_list_barang_nonfoc" class="table table-bordered table-striped"  >
            <thead class="text-center" style='white-space:nowrap;'>
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Part Number</th>
                <th scope="col">Merk</th>
                <th scope="col">Sat</th>
                <th scope="col">Qty PR</th>
                <th scope="col">Qty PO</th>
                <th scope="col">Qty Batal</th>
                <th scope="col">Sisa PR</th>
                <th scope="col">No. PR</th>
                <th scope="col">No. SO Cust</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_barang_nonfoc" class="text-left" >
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
          <div class="col-12" style="overflow:auto;">
            <table id="tabel_add_list_barang_nonfocplus" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th style="white-space:nowrap;" scope="col">Kode Barang</th>
                  <th style="white-space:nowrap;" scope="col">Nama Barang</th>
                  <th style="white-space:nowrap;" scope="col">QTY Sat 1</th>
                  <th style="white-space:nowrap;" scope="col">QTY Sat 2</th>
                  <th style="white-space:nowrap;" scope="col">Sat</th>
                  <th style="white-space:nowrap;" scope="col">Sisa</th>
                  <th style="white-space:nowrap;" scope="col">Sisa 2</th>
                  <th style="white-space:nowrap;" scope="col">No. Bukti</th>
                  <th style="white-space:nowrap;" scope="col">Part Number</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barang_nonfocplus" class="text-left" >
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
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_pic" class="text-left" >
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <!--   -->
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
        {{-- <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Nomor Penawaran PO</h3>
          </div>
        </div> --}}
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_pwo" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Nomor Bukti</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Supplier</th>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_pwo" class="text-left" >
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
        {{-- <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <h3>Lokasi Penerima</h3>
          </div>
        </div> --}}
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <!-- <div class="container-fluid"> -->
          <table id="tabel_add_list_lokasipenerima" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kota</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_lokasipenerima" class="text-left" >
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!--   -->
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
      {{-- <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Ini Modal Dikirim Ke</h3>
        </div>
      </div> --}}
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_alamatkirim" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Nomor</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
                <th style="padding: 4px 12px;" scope="col">Alamat</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_alamatkirim" class="text-left" >
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <!--   -->
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
      {{-- <div class="row">
        <div class="col-md-4" style="margin-top:-40px;">
          <h3>Nomor SO</h3>
        </div>
      </div> --}}
      <div class="row">
        <div class="col-12" style="overflow:auto; margin-top:-30px;">
          <table id="tabel_add_list_noSo" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">No SO</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">No. PO Cust</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_noSo" class="text-left" >
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
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_backoffice" class="text-left" >
            </tbody>
          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>
    <div class="modal-footer">
      <!--   -->
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
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_sales" class="text-left" >
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!--   -->
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
                        {{-- <div class="row">
                            <div class="col-md-4" style="margin-top: -40px;">
                                <h3>Valas</h3>
                            </div>
                        </div> --}}
                    <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                    <div class="row">
                        <div class="col-12" style="overflow:auto; margin-top:-30px;">
                        <!-- <div class="container-fluid"> -->
                        <table id="tabel_add_list_valas" class="table table-bordered table-hover table-striped table-responsive-lg">
                            <thead class="text-center">
                                <tr>
                                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                                    <th style="padding: 4px 12px;" scope="col">Kurs</th>
                                </tr>
                            </thead>
                            <tbody id="tabel_data_add_list_valas" class="text-left" >
                            </tbody>
                        </table>
                        <!-- </div> -->
                        <!-- <button onclick="buttonSubKategori()">tes</button> -->
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--   -->
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

{{-- Placeholder "Cari Data" untuk kotak pencarian DataTables di dalam modal ini.
     Tulisan "Search:" bawaan DataTables disembunyikan lewat CSS di atas (font-size:0
     pada <label>), jadi di sini tinggal mengisi placeholder input-nya.

     Dipasang lewat event, bukan lewat opsi language.searchPlaceholder di tiap
     DataTable(), supaya berlaku untuk SEMUA picker di modal ini tanpa harus mengubah
     belasan pemanggilan DataTable() yang tersebar di purchaseOrder + penawaranso.

     Dibungkus DOMContentLoaded karena jQuery baru dimuat SETELAH @yield('content')
     di layout (purchasing/newmasterx.blade.php:1562), sedangkan modal ini ikut
     ter-render di dalam content - tanpa penundaan, $ belum ada saat baris ini jalan. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof jQuery === 'undefined') { return }

  jQuery(document).on('shown.bs.modal', '#form', function () {
    jQuery('#form .dataTables_filter input').attr('placeholder', 'Cari Data')
  })
})
</script>
<!-- End modal add-->
