@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Barang')

@section('buttons')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" data-toggle="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;
@endsection

@section('content')
<div class="container-fluid">
{{-- <h1>Barang</h1> - judul pindah ke header (page-title) --}}
</div>
<!-- <button onclick="loadAll()">tes</button> -->

<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card wms-kartu">
    {{-- card-header lama (isi tab-nya sudah dikomentari) tidak ditampilkan:
    <div class="card-header">
      <div class="row">
        <!-- <nav style="width: 100%;">
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Outstanding PO</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Penerimaan Barang</a>
          </div>
        </nav> -->
      </div>
    </div> --}}
    <div class="card-body">
      <div class="row">
        <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          {{-- Ditambahkan 2026-10-20: kotak cari + pilihan jumlah baris (data dimuat per halaman dari server). --}}
          <div class="po-toolbar wms-toolbar">
            <input type="search" class="po-search-inp" id="cari_barangjadi" placeholder="Cari kode / nama barang" autocomplete="off">
            <div class="po-len-wrap">
              <label for="tampil_barangjadi">Tampilkan</label>
              <select class="po-len-inp" id="tampil_barangjadi">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">Semua</option>
              </select>
            </div>
          </div>
          <table id="tabel_barangjadi" class="data-table po-aksi-hover" data-wms-asli>
            <thead>
              <tr>
                <th class="text-center">Actions</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Agen</th>
                <th>Satuan 1</th>
                <th>Isi 1</th>
                <!-- <th>Harga 1</th> -->
                <th>Satuan 2</th>
                <th>Isi 2</th>
                <!-- <th>Harga 2</th> -->
                <th>Satuan 3</th>
                <th>Isi 3</th>
                <!-- <th>Harga 3</th> -->
                <th>Aktif</th>
                <th>Qty Minimum</th>
                <th>Qty Maksimum</th>
              </tr>
            </thead>
            <tbody id="barangjadi_data">
              {{-- Diubah 2026-10-20: baris diisi DataTables per halaman (initTabelBarangJadi) - render lama:
              @if (count($barangjadi) > 0)
                @for ($i = 0; $i < count($barangjadi); $i++)
                  <tr id="{!! $i !!}-tr" >
                    <td>
                      <button type="button" class="btn btn-secondary btn-sm btn-top" onclick="select({!! $i !!}, '{!! $barangjadi[$i]->KODEBRG !!}', '{!! $barangjadi[$i]->NAMABRG !!}', '{!! $barangjadi[$i]->Berat !!}')" data-toggle="tooltip" data-placement="bottom" title="Lokasi" style="width: 30px;"><i class="bi bi-bag-plus-fill"></i></button>
                  
                    </td>
                    <td>{!! $barangjadi[$i]->KODEBRG !!}</td>
                    <td>{!! $barangjadi[$i]->NAMABRG !!}</td>
                    @if ($barangjadi[$i]->pAgen == 1)
                      <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                    @else
                      <td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>
                    @endif
                    <td>{!! $barangjadi[$i]->SAT1 !!}</td>
                    <td class="text-right">{!! number_format($barangjadi[$i]->ISI1) !!}</td>
                    <!-- <td class="text-right">{!! number_format($barangjadi[$i]->Hrg1_1) !!}</td> -->
                    <td>{!! $barangjadi[$i]->SAT2 !!}</td>
                    <td class="text-right">{!! number_format($barangjadi[$i]->ISI2) !!}</td>
                    <!-- <td class="text-right">{!! number_format($barangjadi[$i]->Hrg2_1) !!}</td> -->
                    <td>{!! $barangjadi[$i]->SAT3 !!}</td>
                    <td class="text-right">{!! number_format($barangjadi[$i]->ISI3) !!}</td>
                    <!-- <td class="text-right">{!! number_format($barangjadi[$i]->Hrg3_1) !!}</td> -->
                    @if ($barangjadi[$i]->ISAKTIF == 1)
                      <td class="text-center"><i class="bi bi-check-circle text-success"></i></td>
                    @else
                      <td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>
                    @endif
                    <td class="text-right">{!! number_format($barangjadi[$i]->QntMin) !!}</td>
                    <td class="text-right">{!! number_format($barangjadi[$i]->QntMax) !!}</td>
                  </tr>
                @endfor
              @else
                <tr>
                  <td colspan="16">Tidak ada data barang jadi ditemukan.</td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                </tr>
              @endif
              --}}
            </tbody>
          </table>
        </div>
        {{-- Paginasi Laravel dimatikan 2026-10-20: tampil rusak di Laravel 13 dan halaman ke-2 error di SQL Server 2008
        <div class="float-right">
          {!! $barangjadi->links() !!}
        </div>
        --}}
      </div>
    </div>
  </div>
</div>

<!-- start modal add detail Lokasi -->
<div class="modal fade" id="detaillokasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Lokasi Barang</h5>
        <button type="button" class="close" aria-label="Close" onclick="resetDetailLokasi()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <h7 class="modal-title">Kode Barang : </h7><h7 class="modal-title" id="lkdbrg"></h7>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <h7 class="modal-title">Nama Barang : </h7><h7 class="modal-title" id="lnmbrg"></h7>
            </div>
          </div>
        </div>



        <div class="col-12">
              <div class="form-group">
                <label>Berat/Volume</label>
              </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <input type="number" class="form-control" id="beratvolume" onblur="onChangeb()" placeholder="Berat / Volume Barang" required enabled>
          </div>
        </div>

        <hr style="margin-top: 0.5rem; margin-bottom: 0.2rem;">
        <div id="contentDetailLokasi">
          <div class="form-group">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width='30%'>Urut</th>
                  <th>Barcode</th>
                </tr>
              </thead>
              <tbody id="lokasirak">
              </tbody>
            </table>
          </div>
          <hr style="margin-top: 0.5rem; margin-bottom: 0.2rem;">
          <div class="form-group">
            <button type="button" class="btn btn-success btn-sm" onclick="showDivTambahPostingLokasi()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="bi bi-plus-circle"></i></button>
            <button type="button" class="btn btn-warning btn-sm" onclick="showEditDetailLokasi()" rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-danger btn-sm" onclick="eraseDetailLokasi()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="bi bi-trash"></i></button>
          </div>
          <hr style="margin-top: 0.2rem; margin-bottom: 0.5rem;">
          <div id="divTambahPostingLokasi">
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="urut">Urut</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="urut" maxlength="2" onkeypress="return hanyaAngka(event)" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="qntMax">Qnt Max</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="number" value=0.00 class="form-control text-right" id="qntMax" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="barcode">Barcode</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" id="barcode">
                    <option value="">-- Pilih Lokasi --</option>
                    @for ($i = 0; $i < count($lokasi); $i++)
                      <option value="{!! $lokasi[$i]->BarcodeLoc !!}">{!! $lokasi[$i]->BarcodeLoc !!} - {!! $lokasi[$i]->NamaLokasi !!}</option>
                    @endfor
                  </select>
                  <!-- <input type="text" class="form-control" id="barcode" placeholder="Nama Lokasi" required> -->
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="tombolTambahL" onclick="addDetailL()">Tambah</button>
                  <button type="button" class="btn btn-primary" id="resetTambahL" onclick="resetTambahDetailL()">Batal</button>
                </div>
              </div>
            </div>
          </div>
          <div id="divUbahPostingLokasi">
            <div class="row">
              <input type="hidden" id="eid_detaillokasi">
              <div class="col-2">
                <div class="form-group">
                  <label for="eurut">Urut</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="eurut" maxlength="2" onkeypress="return hanyaAngka(event)" required disabled>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="eqntMax">Qnt Max</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="number" value=0.00 class="form-control text-right" id="eqntMax" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="ebarcode">Barcode</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" id="ebarcode">
                    <option value="">-- Pilih Lokasi --</option>
                    @for ($i = 0; $i < count($lokasi); $i++)
                      <option value="{!! $lokasi[$i]->BarcodeLoc !!}">{!! $lokasi[$i]->BarcodeLoc !!} - {!! $lokasi[$i]->NamaLokasi !!}</option>
                    @endfor
                  </select>
                  <!-- <input type="text" class="form-control" id="ebarcode" placeholder="Nama Lokasi" required> -->
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="etombolUbahL" onclick="editDetailL()">Ubah</button>
                  <button type="button" class="btn btn-primary" id="eresetUbahL" onclick="reseteditDetailL()">Batal</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="resetDetailLokasi()">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add detail Lokasi -->

{{-- Modal tambah/ubah barang lama (sudah tidak dipakai) - komentar Blade 2026-10-20 supaya loop di dalamnya tidak dirender.
<!-- start modal add barang jadi -->
<!-- <div class="modal fade" id="addBarangJadi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Barang Jadi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="args1">Grup</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="args1" onchange="setAll2()">
                <option value="">-- Pilih Grup --</option>
                @for ($i = 0; $i < count($design); $i++)
                  <option value="{!! $design[$i]->kode !!}">{!! $design[$i]->kode !!} - {!! $design[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="args2">Subgrup</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="args2" onchange="setAll()">
                <option value="">-- Pilih Subgrup --</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="args3">Merk</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="args3" onchange="setAll()">
                <option value="">-- Pilih Merk --</option>
                @for ($i = 0; $i < count($merk); $i++)
                  <option value="{!! $merk[$i]->kode !!}">{!! $merk[$i]->kode !!} - {!! $merk[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="args4">Warna</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <select class="form-control" id="args4" onchange="setAll()">
                <option value="">-- Pilih Warna --</option>
                @for ($i = 0; $i < count($warna); $i++)
                  <option value="{!! $warna[$i]->kode !!}">{!! $warna[$i]->kode !!} - {!! $warna[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="args5">Dimensi</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <select class="form-control" id="args5" onchange="setAll()">
                <option value="">-- Pilih Dimensi --</option>
                @for ($i = 0; $i < count($dimensi); $i++)
                  <option value="{!! $dimensi[$i]->kode !!}">{!! $dimensi[$i]->kode !!} - {!! $dimensi[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="kode">Kode Barang Jadi</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="kode" placeholder="Kode Barang Jadi" required disabled>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="nama">Nama Barang Jadi</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <input type="text" class="form-control" id="nama" placeholder="Nama Barang Jadi" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="satuan1">Satuan 1</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="satuan1" placeholder="Sat 1" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="isi1">Isi 1</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="isi1" placeholder="Jumlah / Satuan 1" value='1' required disabled>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="harga1">Harga 1</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="harga1" placeholder="Harga / Satuan 1" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="satuan2">Satuan 2</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="satuan2" placeholder="Sat 2" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="isi2">Isi 2</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="isi2" placeholder="Jumlah / Satuan 2" required>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="harga2">Harga 2</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="harga2" placeholder="Harga / Satuan 2" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="satuan3">Satuan 3</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="satuan3" placeholder="Sat 3" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="isi3">Isi 3</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="isi3" placeholder="Jumlah / Satuan 3" required>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="harga3">Harga 3</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="harga3" placeholder="Harga / Satuan 3" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="konversi">Konversi Tidak Tetap</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="konversi">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="aktif">Aktif / Tidak Aktif</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="aktif">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="minimum">Qty Minimum</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="minimum" placeholder="Masukkan Qty Minimum" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="maksimum">Qty Maksimum</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="maksimum" placeholder="Masukkan Qty Maksimum" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="add()">Tambah</button>
      </div>
    </div>
  </div>
</div> -->
<!-- End modal add barang jadi -->

<!-- start modal edit barang jadi -->
<!-- <div class="modal fade" id="editBarangJadi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Barang Jadi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eid">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eargs1">Grup</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="eargs1" onchange="setAll2('e')">
                <option value="">-- Pilih Grup --</option>
                @for ($i = 0; $i < count($design); $i++)
                  <option value="{!! $design[$i]->kode !!}">{!! $design[$i]->kode !!} - {!! $design[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eargs2">Subgrup</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="eargs2" onchange="setAll('e')">
                <option value="">-- Pilih Subgrup --</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eargs3">Merk</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="eargs3" onchange="setAll('e')">
                <option value="">-- Pilih Merk --</option>
                @for ($i = 0; $i < count($merk); $i++)
                  <option value="{!! $merk[$i]->kode !!}">{!! $merk[$i]->kode !!} - {!! $merk[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eargs4">Warna</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <select class="form-control" id="eargs4" onchange="setAll('e')">
                <option value="">-- Pilih Warna --</option>
                @for ($i = 0; $i < count($warna); $i++)
                  <option value="{!! $warna[$i]->kode !!}">{!! $warna[$i]->kode !!} - {!! $warna[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eargs5">Dimensi</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <select class="form-control" id="eargs5" onchange="setAll('e')">
                <option value="">-- Pilih Dimensi --</option>
                @for ($i = 0; $i < count($dimensi); $i++)
                  <option value="{!! $dimensi[$i]->kode !!}">{!! $dimensi[$i]->kode !!} - {!! $dimensi[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="ekode">Kode Barang Jadi</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="ekode" placeholder="Kode Barang Jadi" required disabled>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="enama">Nama Barang Jadi</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <input type="text" class="form-control" id="enama" placeholder="Nama Barang Jadi" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="esatuan1">Satuan 1</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="esatuan1" placeholder="Sat 1" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="eisi1">Isi 1</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eisi1" placeholder="Jumlah / Satuan 1" value='1' required disabled>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="eharga1">Harga 1</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eharga1" placeholder="Harga / Satuan 1" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="esatuan2">Satuan 2</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="esatuan2" placeholder="Sat 2" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="eisi2">Isi 2</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eisi2" placeholder="Jumlah / Satuan 2" required>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="eharga2">Harga 2</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eharga2" placeholder="Harga / Satuan 2" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="esatuan3">Satuan 3</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="esatuan3" placeholder="Sat 3" maxlength="3" required>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group text-center">
              <label for="eisi3">Isi 3</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eisi3" placeholder="Jumlah / Satuan 3" required>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group text-center">
              <label for="eharga3">Harga 3</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eharga3" placeholder="Harga / Satuan 3" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="ekonversi">Konversi Tidak Tetap</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="ekonversi">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eaktif">Aktif / Tidak Aktif</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="eaktif">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eminimum">Qty Minimum</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="eminimum" placeholder="Masukkan Qty Minimum" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="emaksimum">Qty Maksimum</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control format_number" id="emaksimum" placeholder="Masukkan Qty Maksimum" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset('e')">Batal</button>
        <button type="button" class="btn btn-primary" onclick="edit()">Ubah</button>
      </div>
    </div>
  </div>
</div> -->
<!-- End modal edit barang jadi -->
--}}
@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">
  var g_id = "", g_nama = ""; agen = "";
  var g_kodebrg = "", g_namabrg = "", g_urut = "", g_barcode = "" , g_qntMax = "",g_berat="";

  // start document ready
	$(document).ready(function(){
    // Diubah 2026-10-20: tabel diisi per halaman dari server (initTabelBarangJadi) - init lama:
    // var table = $('#tabel_barangjadi').DataTable(
    //   {"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]}
    // );
    // table.on('search' / 'order' / 'page', ...) -> sekarang di initTabelBarangJadi()
    initTabelBarangJadi();
    $(".format_number").val(0);
    $("#isi1").val(1);
    $(".format_number").autoNumeric('init');
	});

  function hanyaAngka(event) {
    var angka = (event.which) ? event.which : event.keyCode
    if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
        return false;
    return true;
  }

  $("#divTambahPostingLokasi").hide();
  $("#divUbahPostingLokasi").hide();
  // end document ready

  // start reset input
  function reset(name = "") {
    agen = "";
    $("#" + name + "args1").val("");
    $("#" + name + "args2").val("");
    $("#" + name + "args3").val("");
    $("#" + name + "args4").val("");
    $("#" + name + "args5").val("");
    // $("#" + name + "args6").val("");
    $("#" + name + "kode").val("");
    $("#" + name + "nama").val("");
    $("#" + name + "satuan1").val("");
    $("#" + name + "satuan2").val("");
    $("#" + name + "satuan3").val("");
    $("#" + name + "isi1").val(1);
    $("#" + name + "isi2").val(0);
    $("#" + name + "isi3").val(0);
    $("#" + name + "harga1").val(0);
    $("#" + name + "harga2").val(0);
    $("#" + name + "harga3").val(0);
    $("#" + name + "konversi").val(0);
    $("#" + name + "aktif").val(1);
    $("#" + name + "minimum").val(0);
    $("#" + name + "maksimum").val(0);
  }

  function resetDetailLokasi() {
    $("#lkdbrg").html("");
    $("#lnmbrg").html("");

    $("#urut").val("");
    $("#barcode").val("");
    $("#qntMax").val(0.00);
    $("#eurut").val("");
    $("#ebarcode").val("");
    $("#eqntMax").val(0.00);

    $("#detaillokasi").modal('toggle');
  }

  function resetTambahDetailL() {
    $("#urut").val("");
    $("#barcode").val("");
    $("#qntMax").val(0.00);
    $("#divTambahPostingLokasi").hide();
  }

  function reseteditDetailL() {
    $("#eurut").val("");
    $("#ebarcode").val("");
    $("#eqntMax").val(0.00);
    $("#divUbahPostingLokasi").hide();
  }

  // end reset input




  function onChangeb () {
  console.log('onChangeHari')
    let value = $("#beratvolume").val()
    console.log(value)
    onChangeHeader('berat' , value)

    let nobukti = $("#lkdbrg").html()
  }


  function onChangeHeader (field, value) {
    let _token  = $("#_token").val()
    let nobukti = $("#lkdbrg").html()
    $.ajax({
      url: "{!! url('brgonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        alertify.success(`update ${field} berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
  }


  // Ditambahkan 2026-10-20: tabel Barang per halaman dari server (barangjadidata). Kotak cari mencari
  // kode/nama barang di seluruh data, "Tampilkan" mengatur jumlah baris per halaman.
  var tabelBarangJadi = null;
  var timerCariBarangJadi = null;

  function teksBarangJadi(v) {
    return $('<div>').text(v == null ? '' : v).html();
  }

  function angkaBarangJadi(v) {
    var n = Number(v);
    return isFinite(n) ? Math.round(n).toLocaleString('en-US') : '0';
  }

  function ikonBarangJadi(v) {
    return v == 1 ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>';
  }

  function resetPilihBarangJadi() {
    $('#barangjadi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_nama = "";
  }

  function initTabelBarangJadi() {
    tabelBarangJadi = $('#tabel_barangjadi').DataTable({
      serverSide: true,
      processing: true,
      ajax: {
        url: "{!! url('barangjadidata') !!}",
        type: "POST",
        data: function (d) {
          d._token = $("#_token").val();
          delete d.columns;
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Gagal memuat data barang, silahkan refresh browser');
        }
      },
      dom: "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      pageLength: Number($("#tampil_barangjadi").val()) || 10,
      order: [[1, 'asc']],
      columns: [
        // Diubah 2026-10-20: tombol Lokasi bergaya newSML - "+" primary soft, ikon lebih besar, rata tengah.
        { data: null, orderable: false, className: 'text-center', render: function () {
          return '<button type="button" class="btn btn-primary btn-sm btn-top" onclick="pilihBarisBarangJadi(this)" data-toggle="tooltip" data-placement="bottom" title="Lokasi" style="width: 30px;"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>';
        } },
        { data: 'KODEBRG', render: teksBarangJadi },
        { data: 'NAMABRG', render: teksBarangJadi },
        { data: 'pAgen', className: 'text-center', render: ikonBarangJadi },
        { data: 'SAT1', render: teksBarangJadi },
        { data: 'ISI1', className: 'text-right', render: angkaBarangJadi },
        { data: 'SAT2', render: teksBarangJadi },
        { data: 'ISI2', className: 'text-right', render: angkaBarangJadi },
        { data: 'SAT3', render: teksBarangJadi },
        { data: 'ISI3', className: 'text-right', render: angkaBarangJadi },
        { data: 'ISAKTIF', className: 'text-center', render: ikonBarangJadi },
        { data: 'QntMin', className: 'text-right', render: angkaBarangJadi },
        { data: 'QntMax', className: 'text-right', render: angkaBarangJadi }
      ],
      createdRow: function (row, data, index) {
        row.id = index + '-tr';
      },
      language: {
        emptyTable: 'Tidak ada data barang jadi ditemukan.',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
        processing: 'Memuat data...'
      },
      drawCallback: function () {
        if (window.wmsAturTinggiTabel) { setTimeout(window.wmsAturTinggiTabel, 0); }
      }
    });
    tabelBarangJadi.on('search', resetPilihBarangJadi);
    tabelBarangJadi.on('order', resetPilihBarangJadi);
    tabelBarangJadi.on('page', resetPilihBarangJadi);
    tabelBarangJadi.on('length', resetPilihBarangJadi);
  }

  $(document).on('input', '#cari_barangjadi', function () {
    var kata = this.value;
    clearTimeout(timerCariBarangJadi);
    timerCariBarangJadi = setTimeout(function () {
      if (tabelBarangJadi && tabelBarangJadi.search() !== kata) { tabelBarangJadi.search(kata).draw(); }
    }, 400);
  });

  $(document).on('change', '#tampil_barangjadi', function () {
    if (tabelBarangJadi) { tabelBarangJadi.page.len(Number(this.value)).draw(); }
  });

  // Tombol Lokasi di baris tabel -> fungsi select() lama dengan data baris yang sama.
  function pilihBarisBarangJadi(btn) {
    var tr = $(btn).closest('tr');
    var d = tabelBarangJadi.row(tr).data();
    if (!d) { return; }
    select(tr.attr('id').replace('-tr', ''), d.KODEBRG, d.NAMABRG, d.Berat);
  }

  // start refresh tabel
  // Diubah 2026-10-20: Refresh memuat ulang halaman tabel yang sedang tampil (cari & Tampilkan tetap).
  function loadAll() {
    resetPilihBarangJadi();
    if (tabelBarangJadi) { tabelBarangJadi.ajax.reload(null, false); }
  }

  {{-- loadAll lama (memuat semua barang sekaligus lewat loadAllBarangJadi) - tidak dipakai sejak 2026-10-20:
  function loadAllLama() {
    console.log('loadall')
    $('#tabel_barangjadi').DataTable().destroy();
    $('#barangjadi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_nama = "";
    var _token = $("#_token").val();
    console.log('loadall 1')
    $.ajax({
      url     : "{!! url('loadAllBarangJadi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        page : {!! $barangjadi->currentPage() !!}
      },
      success : function(result) {
        result = result.data;
        if (result.length > 0) {
          $('#barangjadi_data').html("");
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-tr">\
               <td>\
                <button type="button" class="btn btn-secondary btn-sm btn-top" onclick="select(' + i + ',\' ' + result[i].KODEBRG + '\', \'' + result[i].NAMABRG + '\')" data-toggle="tooltip" data-placement="bottom" title="Lokasi" style="width: 30px;">\
                  <i class="bi bi-pin-map"></i>\
                </button>\
              </td>\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].NAMABRG + '</td>';
            if (result[i].pAgen == 1) {
              str = str + '<td class="text-center"><i class="bi bi-check-circle text-success"></i></td>';
            } else {
              str = str + '<td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>';
            }
            str = str + '<td>' + result[i].SAT1 + '</td>';
            if (result[i].ISI1 == null || result[i].ISI1 == 0) {
              str = str + '<td class="text-right">' + (0) + '</td>';
            } else {
              str = str + '<td class="text-right">' + (parseInt(result[i].ISI1)) + '</td>';
            }
            // if (result[i].Hrg1_1 == null || result[i].Hrg3_1 == 0) {
            //   str = str + '<td class="text-right">' + (0) + '</td>';
            // } else {
            //   str = str + '<td class="text-right">' + (parseInt(result[i].Hrg1_1)) + '</td>';
            // }
            str = str + '<td>' + result[i].SAT2 + '</td>';
            if (result[i].ISI2 == null || result[i].ISI2 == 0) {
              str = str + '<td class="text-right">' + (0) + '</td>';
            } else {
              str = str + '<td class="text-right">' + (parseInt(result[i].ISI2)) + '</td>';
            }
            // if (result[i].Hrg2_1 == null || result[i].Hrg3_1 == 0) {
            //   str = str + '<td class="text-right">' + (0) + '</td>';
            // } else {
            //   str = str + '<td class="text-right">' + (parseInt(result[i].Hrg2_1)) + '</td>';
            // }
            if (result[i].SAT3 == null) {
              str = str + "<td></td>";
            } else {
              str = str + '<td>' + result[i].SAT3 + '</td>';
            }
            if (result[i].ISI3 == null || result[i].ISI3 == 0) {
              str = str + '<td class="text-right">' + (0) + '</td>';
            } else {
              str = str + '<td class="text-right">' + (parseInt(result[i].ISI3)) + '</td>';
            }
            // if (result[i].Hrg3_1 == null || result[i].Hrg3_1 == 0) {
            //   str = str + '<td class="text-right">' + (0) + '</td>';
            // } else {
            //   str = str + '<td class="text-right">' + (parseInt(result[i].Hrg3_1)) + '</td>';
            // }
            if (result[i].ISAKTIF == 1) {
              str = str + '<td class="text-center"><i class="bi bi-check-circle text-success"></i></td>';
            } else {
              str = str + '<td class="text-center"><i class="bi bi-x-circle text-danger"></i></td>';
            }
            if (result[i].QntMin == null || result[i].Hrg3_1 == 0) {
              str = str + '<td class="text-right">' + (0) + '</td>';
            } else {
              str = str + '<td class="text-right">' + (parseInt(result[i].QntMin)) + '</td>';
            }
            if (result[i].QntMax == null || result[i].Hrg3_1 == 0) {
              str = str + '<td class="text-right">' + (0) + '</td></tr>';
            } else {
              str = str + '<td class="text-right">' + (parseInt(result[i].QntMax)) + '</td></tr>';
            }
          }
          $('#barangjadi_data').html(str);
        }
        else {
          $('#barangjadi_data').html('<tr>\
            <td colspan="15">Tidak ada data barang jadi ditemukan.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        // middleTD();
      }
    });
    console.log('loadall2')
    var table = $('#tabel_barangjadi').DataTable(
      {"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]}
      // { "paging": false, "bInfo" : false, "ordering": false }
    );
    console.log('loadall3')
    table.on('search', function () {
      $('#barangjadi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    console.log('loadall4')
    table.on('order', function () {
      $('#barangjadi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    // Ditambahkan 2026-10-19: tabel sekarang berhalaman - pindah halaman juga mereset pilihan baris.
    table.on('page', function () {
      $('#barangjadi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    console.log('loadall success')
  }
  --}}
  // end refresh table

  function select(_row, _id, _nama,_berat) {
    $('#barangjadi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-tr").css('background-color', 'gold');
    console.log('beratttt');
    console.log(_berat);
    g_id = _id; g_nama = _nama;g_berat = _berat
    ShowLokasi();
   
  }

  function selectlokasi(_row, _urut, _kdbrg, _qntMax, _nmbrg, _barcode) {
    console.log(_row, _urut, _kdbrg,_qntMax, _nmbrg, _barcode)
    $('#lokasirak > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#l"+_row+"-tr").css('background-color', 'gold');
    g_urut = _urut; g_kodebrg = _kdbrg; g_barcode = _barcode; g_namabrg = _nmbrg; g_qntMax = _qntMax;
  }

  function showDivTambahPostingLokasi() {
    if ($("#divTambahPostingLokasi").is(":hidden")) {
      $("#divTambahPostingLokasi").show();
      $("#divUbahPostingLokasi").hide();
    } else {
      $("#divTambahPostingLokasi").hide();
    }
  }

  // start add Rak gudang
  function addDetailL() {
    var _token = $("#_token").val();
    var _kode = $("#lkdbrg").html();
    var _nama = $("#lnmbrg").html();
    var _urut = $("#urut").val();
    var _barcode = $("#barcode").val();
    var _qntMax = $("#qntMax").val();
    var _Choice = 'I';
    if (_kode != "" && _urut != "" && _barcode != "") {
      $.ajax({
        url     : "{!! url('addBarangLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          urut : _urut,
          barcode : _barcode,
          qntMax: _qntMax
        },
        success : function(result) {
          if (result == 1) {
            resetTambahDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data Lokasi!', 'Urut sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add Rak gudang Lokasi

  // start edit Rak lokasi
  function editDetailL(name) {
    var _token = $("#_token").val();
    var _kode = $("#lkdbrg").html();
    var _nama = $("#lnmbrg").html();
    var _urut = $("#eurut").val();
    var _barcode = $("#ebarcode").val();
    var _qntMax = $("#eqntMax").val();
    var _urutold = $("#eid_detaillokasi").val();
    var _Choice = 'U';
    if (_kode != "" && _urut != "" && _barcode != "") {
      $.ajax({
        url     : "{!! url('addBarangLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          urut : _urut,
          barcode : _barcode,
          urutold : _urutold,
          qntMax: _qntMax
        },
        success : function(result) {
          if (result == 1) {
            reseteditDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data Lokasi!', 'Urut sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit Rak Lokasi

  // start hapus Rak Lokasi
  function eraseDetailLokasi() {
    if (g_urut === "" || g_kodebrg === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Lokasi', 'Apakah yakin ingin menghapus Lokasi ' + g_barcode + ' ?',
      function(){
        var _token = $("#_token").val();
        var _kode = $("#lkdbrg").html();
        var _nama = $("#lnmbrg").html();
        var _urut = g_urut;
        var _barcode = g_barcode;
        var _qntMax = g_qntMax;
        var _Choice = 'D';
        $.ajax({
          url     : "{!! url('addBarangLokasi') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _Choice,
            kode : _kode,
            nama : _nama,
            urut : _urut,
            barcode : _barcode,
            qntMax: _qntMax
          },
          success : function(result) {
            if (result == 1) {
              showloadRakLokasi();
              alertify.success('Data Lokasi telah dihapus.');
            } else {
              alertify.alert('Gagal menghapus data Lokasi!', 'Lokasi sudah ada transaksi.', function(){ });
            }

          }
        });
    },function(){});
  }
  // end hapus Rak Lokasi

  // start tampilkan Menu Rak gudang
  function ShowLokasi() {
    let _token  = $("#_token").val()
    
    console.log('ShowLokasi')
    if (g_id === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    g_urut = ""; g_kodebrg = ""; g_namabrg = ""; g_barcode = ""; g_qntMax = "";
    $("#detaillokasi").modal('toggle');
    $("#lkdbrg").html(g_id);
    $("#lnmbrg").html(g_nama);

     console.log('kode barang =', g_id);
     
        $.ajax({
          url     : "{!! url('loadkodebrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kode : g_id

            
          },
          success : function(result) {
             if (result.length > 0) { 
                    console.log(result[0].NAMABRG) ; 
                      $("#beratvolume").val(result[0].Berat);   
                      
            } else {
                alertify.warning('kosong');
              }          
                      
          },error: function (err) {
              console.log(err)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
            }
        });



    


    $("#divUbahPostingLokasi").hide();
    $("#divTambahPostingLokasi").hide();
    showloadRakLokasi();
  }
  // end tampilkan Menu Rak gudang

  // start tampilkan Rak Lokasi
  function showloadRakLokasi() {
    var _token = $("#_token").val();
    var _kode = $("#lkdbrg").html();
    var _nama = $("#lnmbrg").html();
    str = "";
    $.ajax({
      url     : "{!! url('showBarangLokasi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        kode : _kode,
        nama : _nama
      },
      success : function(result) {
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="l'+i+'-tr" onclick="selectlokasi(' + i + ', ' + result[i].Urut + ', \'' + result[i].KodeBarang + '\', \'' + result[i].qntMax + '\', \'' + _nama + '\', \'' + result[i].BarcodeLoc + '\')">\
              <td>' + result[i].Urut + "</td>\
              <td>" + result[i].BarcodeLoc + '</td></tr>';
          }
        } else {
          str = str + "<td colspan='2'>Data Lokasi tidak ditemukan</td>";
        }

        

        $("#lokasirak").html(str);
      }
    });
  }
  // end tampilkan Rak Lokasi

  function showEditDetailLokasi() {
    if (g_urut === "" || g_kodebrg === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingLokasi").hide();
    $("#eurut").val(g_urut);
    $("#ebarcode").val(g_barcode);
    $("#eqntMax").val(g_qntMax);
    $("#eid_detaillokasi").val(g_urut);
    $("#divUbahPostingLokasi").show();
  }

  // start add barang jadi
  function add() {
    var _token = $("#_token").val();
    var _vargs1 = $("#args1").val();
    var _vargs2 = $("#args2").val();
    var _vargs3 = $("#args3").val();
    var _vargs4 = $("#args4").val();
    var _vargs5 = $("#args5").val();
    var _agen = agen;
    var _kode = $("#kode").val();
    var _nama = $("#nama").val();
    var _satuan1 = $("#satuan1").val();
    var _satuan2 = $("#satuan2").val();
    var _satuan3 = $("#satuan3").val();
    var _isi1 = $("#isi1").val().replace(/,/g, "");
    var _isi2 = $("#isi2").val().replace(/,/g, "");
    var _isi3 = $("#isi3").val().replace(/,/g, "");
    var _harga1 = $("#harga1").val().replace(/,/g, "");
    var _harga2 = $("#harga2").val().replace(/,/g, "");
    var _harga3 = $("#harga3").val().replace(/,/g, "");
    var _konversi = $("#konversi").val();
    var _aktif = $("#aktif").val();
    var _minimum = $("#minimum").val().replace(/,/g, "");
    var _maksimum = $("#maksimum").val().replace(/,/g, "");
    if (_kode != "" && _nama != "" && _satuan1 != "" && _harga1 != "" && _isi1 != ""
      && _satuan2 != "" && _harga2 != "" && _isi2 != "" && _minimum != "" && _maksimum != "") {
      $.ajax({
        url     : "{!! url('addBarangJadi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          vargs1 : _vargs1,
          vargs2 : _vargs2,
          vargs3 : _vargs3,
          vargs4 : _vargs4,
          vargs5 : _vargs5,
          agen : _agen,
          kode : _kode,
          nama : _nama,
          satuan1 : _satuan1,
          satuan2 : _satuan2,
          satuan3 : _satuan3,
          isi1 : _isi1,
          isi2 : _isi2,
          isi3 : _isi3,
          harga1 : _harga1,
          harga2 : _harga2,
          harga3 : _harga3,
          konversi : _konversi,
          aktif : _aktif,
          minimum : _minimum,
          maksimum : _maksimum
        },
        success : function(result) {
          if (result == 1) {
            reset();
            loadAll();
            alertify.success('Data barang jadi telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data barang jadi!', 'Kode barang jadi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data barang jadi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add barang jadi

  // start tampilkan barang jadi
  function show() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _id = g_id;
    var _token = $("#_token").val();
    $("#eid").val(_id);
    $.ajax({
      url     : "{!! url('showBarangJadi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id
      },
      success : function(result) {
        $("#eargs1").val(result.kode_grup);
        $("#eargs2").val(result.kode_subgrup);
        $("#eargs3").val(result.kode_merk);
        $("#eargs4").val(result.kode_warna);
        $("#eargs5").val(result.kode_dimensi);
        agen = result.agen;
        // if (kode.length == 6) {
        //   $("#eargs6").val(kode[5]);
        // } else {
        //   $("#eargs6").val("");
        // }
        $("#ekode").val(result.kode);
        $("#enama").val(result.nama);
        $("#esatuan1").val(result.satuan1);
        $("#esatuan2").val(result.satuan2);
        $("#esatuan3").val(result.satuan3);
        $("#eisi1").val(numberWithCommas(result.isi1));
        $("#eisi2").val(numberWithCommas(result.isi2));
        $("#eisi3").val(numberWithCommas(result.isi3));
        $("#eharga1").val(numberWithCommas(result.harga1));
        $("#eharga2").val(numberWithCommas(result.harga2));
        $("#eharga3").val(numberWithCommas(result.harga3));
        $("#ekonversi").val(result.konversi);
        $("#eaktif").val(result.aktif);
        $("#eminimum").val(numberWithCommas(result.minimum));
        $("#emaksimum").val(numberWithCommas(result.maksimum));
        $("#editBarangJadi").modal('toggle');
      }
    });
  }
  // end tampilkan barang jadi

  // start edit barang jadi
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _vargs1 = $("#eargs1").val();
    var _vargs2 = $("#eargs2").val();
    var _vargs3 = $("#eargs3").val();
    var _vargs4 = $("#eargs4").val();
    var _vargs5 = $("#eargs5").val();
    var _agen = agen;
    var _kode = $("#ekode").val();
    var _nama = $("#enama").val();
    var _satuan1 = $("#esatuan1").val();
    var _satuan2 = $("#esatuan2").val();
    var _satuan3 = $("#esatuan3").val();
    var _isi1 = $("#eisi1").val().replace(/,/g, "");
    var _isi2 = $("#eisi2").val().replace(/,/g, "");
    var _isi3 = $("#eisi3").val().replace(/,/g, "");
    var _harga1 = $("#eharga1").val().replace(/,/g, "");
    var _harga2 = $("#eharga2").val().replace(/,/g, "");
    var _harga3 = $("#eharga3").val().replace(/,/g, "");
    var _konversi = $("#ekonversi").val();
    var _aktif = $("#eaktif").val();
    var _minimum = $("#eminimum").val().replace(/,/g, "");
    var _maksimum = $("#emaksimum").val().replace(/,/g, "");
    if (_id != "" && _kode != "" && _nama != "" && _satuan1 != "" && _harga1 != "" && _isi1 != ""
      && _satuan2 != "" && _harga2 != "" && _isi2 != "" && _minimum != "" && _maksimum != "") {
      $.ajax({
        url     : "{!! url('editBarangJadi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          vargs1 : _vargs1,
          vargs2 : _vargs2,
          vargs3 : _vargs3,
          vargs4 : _vargs4,
          vargs5 : _vargs5,
          agen : _agen,
          kode : _kode,
          nama : _nama,
          satuan1 : _satuan1,
          satuan2 : _satuan2,
          satuan3 : _satuan3,
          isi1 : _isi1,
          isi2 : _isi2,
          isi3 : _isi3,
          harga1 : _harga1,
          harga2 : _harga2,
          harga3 : _harga3,
          konversi : _konversi,
          aktif : _aktif,
          minimum : _minimum,
          maksimum : _maksimum
        },
        success : function(result) {
          if (result == 1) {
            $("#editBarangJadi").modal('toggle');
            reset("e");
            loadAll();
            alertify.success('Data barang jadi telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data barang jadi!', 'Kode barang jadi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data barang jadi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit barang jadi

  // start hapus barang jadi
  function erase() {
    if (g_id === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Barang Jadi', 'Apakah yakin ingin menghapus barang jadi dengan nama ' + g_nama + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('eraseBarangJadi') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : g_id
          },
          success : function(result) {
            loadAll();
            alertify.success('Data barang jadi telah dihapus.');
          }
        });
    },function(){});
  }
  // end hapus barang jadi

  function setAll(name = "") {
    var vargs1 = $("#" + name + "args1").val();
    var vargs2 = $("#" + name + "args2").val();
    var vargs3 = $("#" + name + "args3").val();
    var vargs4 = $("#" + name + "args4").val();
    var vargs5 = $("#" + name + "args5").val();
    var args1 = $("#" + name + "args1 :selected").text();
    var args2 = $("#" + name + "args2 :selected").text();
    var args3 = $("#" + name + "args3 :selected").text();
    var args4 = $("#" + name + "args4 :selected").text();
    var args5 = $("#" + name + "args5 :selected").text();
    // var args6 = $("#" + name + "args6").val();
    if (vargs1 != "" && vargs2 != "" && vargs3 != "") {
      var _token = $("#_token").val();
      $.ajax({
        url     : "{!! url('getMerkJadi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          kode : vargs3
        },
        success : function(result) {
          agen = result.agen;
        }
      });
      if (vargs4 != ""){
        if (vargs5 != ""){
          kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0] + args4.split(" - ")[0] + args5.split(" - ")[0];
          nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1] + " " + args4.split(" - ")[1] + " " + args5.split(" - ")[1];
        }else{
          kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0] + args4.split(" - ")[0];
          nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1] + " " + args4.split(" - ")[1];
        }
      }else{
        kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0];
        nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1];
      }
      // if (args6 != "") {
      //   kode += "-" + args6;
      //   nama += " " + args6;
      // }
      $("#" + name + "kode").val(kode);
      $("#" + name + "nama").val(nama);
    } else {
      agen = "";
      kode = agen + vargs1 + vargs2 + vargs3 + vargs4 + vargs5;
      nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1];
      $("#" + name + "kode").val(kode);
      $("#" + name + "nama").val(nama);

      // $("#" + name + "kode").val("");
      // $("#" + name + "nama").val("");
    }
  }

  function setAll2(name = "") {
    if ($("#" + name + "args1").val() == "") {
      $("#" + name + "args2").html('<option value="">-- Pilih Subgrup --</option>');
    } else {
      var str = '<option value="">-- Pilih Subgrup --</option>';
      var _token = $("#_token").val();
      $.ajax({
        url     : "{!! url('getSubgrup') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          grup : $("#" + name + "args1").val()
        },
        success : function(result) {
          for (var i = 0; i < result.length; i++) {
            str += '<option value="'+result[i].kode+'">'+result[i].kode+' - '+result[i].nama+'</option>';
          }
        }
      });
      $("#" + name + "args2").html(str);
    }
    var vargs1 = $("#" + name + "args1").val();
    var vargs2 = $("#" + name + "args2").val();
    var vargs3 = $("#" + name + "args3").val();
    var vargs4 = $("#" + name + "args4").val();
    var vargs5 = $("#" + name + "args5").val();
    var args1 = $("#" + name + "args1 :selected").text();
    var args2 = $("#" + name + "args2 :selected").text();
    var args3 = $("#" + name + "args3 :selected").text();
    var args4 = $("#" + name + "args4 :selected").text();
    var args5 = $("#" + name + "args5 :selected").text();
    if (vargs1 != "" && vargs2 != "" && vargs3 != "") {
      var _token = $("#_token").val();
      $.ajax({
        url     : "{!! url('getMerkJadi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          kode : vargs3
        },
        success : function(result) {
          agen = result.agen;
        }
      });
      if (vargs4 != ""){
        if (vargs5 != ""){
          kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0] + args4.split(" - ")[0] + args5.split(" - ")[0];
          nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1] + " " + args4.split(" - ")[1] + " " + args5.split(" - ")[1];
        }else{
          kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0] + args4.split(" - ")[0];
          nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1] + " " + args4.split(" - ")[1];
        }
      }else{
        kode = agen + args1.split(" - ")[0] + args2.split(" - ")[0] + args3.split(" - ")[0];
        nama = args1.split(" - ")[1] + " " + args2.split(" - ")[1] + " " + args3.split(" - ")[1];
      }
      $("#" + name + "kode").val(kode);
      $("#" + name + "nama").val(nama);
    } else {
      agen = "";
      kode = agen + vargs1 + vargs2 + vargs3 + vargs4 + vargs5;
      nama = args1.split(" - ")[1];
      $("#" + name + "kode").val(kode);
      $("#" + name + "nama").val(nama);
      // $("#" + name + "kode").val("");
      // $("#" + name + "nama").val("");
    }
  }
</script>
@endsection
