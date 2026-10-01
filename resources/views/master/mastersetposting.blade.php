@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Set Posting')
@section('content')
<div class="container-fluid">

@include('master/partials/sidebarPosting')

<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

<style>
#contentContainer {
    position: fixed;
    top: 56px;
    right: 0;
    width: 500px;
    height: calc(100vh - 56px);
    overflow-y: auto;
}
</style>

</div>
<div id="contentContainer" class="container-fluid" style="max-width: 500px" hidden>
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
          <div class="row mt-3" style="overflow:auto; display:flex; justify-content:space-between;">
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingKas()">Posting Kasasdasd</button>
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingBank()">Posting Bank</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="buttonAkumulasi()">Posting Akumulasi</button>
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="buttonAktiva()">Posting Aktiva</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-success btn-lg" style="height: 45px; width:200px;" onclick="pagePostingAkumulasi()">Page Posting Akumulasi</button>
                  <button type="button" class="btn btn-success btn-lg" style="height: 45px; width:200px;" onclick="pagePostingAktiva()">Page Posting Aktiva</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingHargaPokok()">Posting Harga Pokok</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingHutang()">Posting Hutang</button>
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingPiutang()">Posting Piutang</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingDeposito()">Posting Deposito</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingUMHutang()">Posting UM Hutang</button>
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingUMPiutang()">Posting UM Piutang</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingHutangSmtr()">Posting Hutang Sementara</button>
                  <button type="button" class="btn btn-danger btn-lg" style="height: 45px; width:200px;" onclick="buttonPostingPiutangSmtr()">Posting Piutang Sementara</button>
              </div>
              <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingGiroTerima()">Posting Giro Terima</button>
                  <button type="button" class="btn btn-primary btn-lg" style="height: 45px; width:200px;" onclick="pagePostingGiroBuka()">Posting Giro Buka</button>
              </div>
          </div>

</div>

<!-- start modal akumulasi -->
<div class="modal fade"  id="formAkumulasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-12" style="overflow:auto; display:flex; justify-content:space-between; margin-bottom: 10px;">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonRefreshPostingAkumulasi()"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
        <table id="tabel" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="tabel_data" class="text-left" >
            <tr >

              <td></td>
              <td></td>


                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPostingAkumulasi()"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete()"><i class="bi bi-trash"></i></button>
                </td>
          </tr>
          </tbody>


        </table>

        <button type="button" class="btn btn-primary btn-lg " onclick="buttonAkumulasiSelect()">Add Perkiraan</button>


    </div>
        <div class="modal-footer">
           
          <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
        </div>
  </div>
</div>
</div>
<!-- End modal akumulasi-->

<!-- start modal akumulasi Select -->
<div class="modal fade"  id="formAkumulasiSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAkumulasiSelectPerkiraan()">Select Perkiraan</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PerkiraanAkumulasi" placeholder="Perkiraan" disabled>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddAkumulasi()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal akumulasi Select-->

<!-- start modal akumulasi select perkiraan -->
<div class="modal fade"  id="formAkumulasiSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelAkumulasiSelect" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAkumulasiSelect" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal akumulasi select perkiraan-->

<!-- AKTIVA ====================================================================================================================================== -->

<!-- start modal aktiva -->
<div class="modal fade"  id="formAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1800px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonRefreshPostingAkumulasi()"><i class="bi bi-arrow-clockwise"></i></button>
        <table id="tabelPostingAktiva" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Persen</th>
              <th scope="col">Metode</th>
              <th scope="col">Perkiraan Akumulasi</th>
              <th scope="col">Perkiraan Biaya 1</th>
              <th scope="col">Persen Biaya 1</th>
              <th scope="col">Perkiraan Biaya 2</th>
              <th scope="col">Persen Biaya 2</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="tabelDataPostingAktiva" class="text-left" >
            <tr >

              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <!-- <td></td> -->

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit()"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete()"><i class="bi bi-trash"></i></button>
                </td>
          </tr>
          </tbody>


        </table>

        <button type="button" class="btn btn-primary btn-lg " onclick="buttonAddAktiva()">Add Aktiva</button>


    </div>
        <div class="modal-footer">
           
          <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva-->

<!-- start modal add posting aktiva-->
<div class="modal fade"  id="formAddPostingAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Posting Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAktivaSelectPerkiraan()">Perkiraan</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PerkiraanPostingAktiva" placeholder="Perkiraan" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Akumulasi</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAktivaSelectAkumulasi()">Akumulasi</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_AkumulasiPostingAktiva" placeholder="Akumulasi" disabled>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Biaya Penyusutan 1</label>
                  <div class="form-group text-center">
                    <input type="checkbox" id="uangMukaPostingAktiva" value="Y">
                    <label for="uangMukaPostingAktiva">Uang Muka?</label><br>
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAktivaSelectBiayaPenyusutan1()">Penyusutan 1</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_BiayaPenyusutan1PostingAktiva" placeholder="Penyusutan 1">
                    <input type="number" class="form-control" id="input_add_PersenBiayaPenyusutan1PostingAktiva" placeholder="%">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Biaya Penyusutan 2</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAktivaSelectBiayaPenyusutan2()">Penyusutan 2</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_BiayaPenyusutan2PostingAktiva" placeholder="Penyusutan 2">
                    <input type="number" class="form-control" id="input_add_PersenBiayaPenyusutan2PostingAktiva" placeholder="%">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Persentase Susut</label>
                  <div class="form-group text-center">
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                    <input type="number" class="form-control" id="input_add_PersenPostingAktiva" placeholder="%">
                  <label class="text-left">Metode Penyusutan</label>
                    <select name="MedPenyu" id="input_add_MetodePostingAktiva">
                      <option value="L">[L]urus</option>
                      <option value="M">[M]enurun</option>
                      <option value="P">[P]ajak</option>
                    </select>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddAktiva()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add posting aktiva-->

<!-- start modal aktiva select perkiraan -->
<div class="modal fade"  id="formAktivaSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelAktivaSelectPerkiraan" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAktivaSelectPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva select perkiraan-->

<!-- start modal aktiva select akumulasi -->
<div class="modal fade"  id="formAktivaSelectAkumulasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelAktivaSelectAkumulasi" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAktivaSelectAkumulasi" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva select akumulasi-->

<!-- start modal aktiva select BP1 -->
<div class="modal fade"  id="formAktivaBiayaPenyusutan1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelBiayaPenyusutanAktiva1" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataBiayaPenyusutanAktiva1" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihBiayaPenyusutan1()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva select BP1-->

<!-- start modal aktiva select BP2 -->
<div class="modal fade"  id="formAktivaBiayaPenyusutan2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelBiayaPenyusutanAktiva2" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataBiayaPenyusutanAktiva2" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihBiayaPenyusutan2()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva select BP 2-->

<!-- start modal edit posting akumulasi -->
<div class="modal fade"  id="formEditAkumulasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="bs-form bs-form-1">
          <label for="input_edit_Akumulasi">Perkiraan</label>
          <input type="text" class="form-control" id="input_edit_Akumulasi" placeholder="Kode Jenis" disabled>
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonAkumulasiSelectAkumulasi()">Select Perkiraan</button>
        </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditPostingAkumulasi()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit posting akumulasi-->

<!-- start edit akumulasi akumulasi -->
<div class="modal fade"  id="formAkumulasiEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelEditAkumulasi" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataEditAkumulasi" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiEdit()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal akumulasi edit akumulasi-->


<!-- =========================================== POSTING KAS -->
<!-- start modal posting kas -->
<div class="modal fade"  id="formPostingKas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Kas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelPostingKas" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataPostingKas" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonSelectPostingKas()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>

                <button type="button" class="btn btn-primary btn-lg " onclick="buttonAddPostingKas()">Add Kas</button>

    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal posting kas -->

<!-- start modal add posting kas Select -->
<div class="modal fade"  id="formAddPostingKas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Kas Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonSelectPostingKas()">Select Perkiraan</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PostingKas" placeholder="Perkiraan" disabled>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddAkumulasi()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add posting kas Select-->

<!-- start modal select posting kas -->
<div class="modal fade"  id="formSelectPostingKas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectPostingKas" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataSelectPostingKas" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihPostingKas()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal select posting kas-->

<!-- =========================================== END OF MODAL POSTING KAS -->

<!-- ================================================= START OF MODAL POSTING BANK -->
<!-- start modal posting kas -->
<div class="modal fade"  id="formPostingBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Bank</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelPostingBank" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataPostingBank" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonSelectPostingBank()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>

                <button type="button" class="btn btn-primary btn-lg " onclick="buttonAddPostingBank()">Add Bank</button>

    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal posting kas -->

<!-- start modal add posting kas Select -->
<div class="modal fade"  id="formAddPostingBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Bank Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                  <div class="form-group text-center">
                  <button type="button" class="btn btn-primary btn-lg " onclick="buttonSelectPostingBank()">Select Perkiraan</button>
                </div>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_PostingBank" placeholder="Perkiraan" disabled>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddAkumulasi()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add posting kas Select-->

<!-- start modal select posting bank -->
<div class="modal fade"  id="formSelectPostingBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectPostingBank" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataSelectPostingBank" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihPostingBank()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal select posting bank-->


<!-- ========================================= END OF MODAL POSTING BANK -->

@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
        //    "columnDefs": [
        // { "type": "date", "targets": [1] },
        // {  "className": "text-center", "targets": [3] },
      // ]
    });
    // tabelPostingAktiva
    $("#tabelPostingAktiva").DataTable({
      "lengthChange": false,
        "paging": false ,

  });
});
//

function loadPostingKas () {
  console.log('asd')
  let _token = $("#_token").val();

  $('#tabelPostingKas').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadpostingkas') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPostingKas('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeletePostingKas('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });



  document.getElementById("tabel_dataPostingKas").innerHTML = rowTable
  $("#tabelPostingKas").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}

function loadPostingBank () {
  console.log('asd')
  let _token = $("#_token").val();

  $('#tabelPostingBank').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadpostingbank') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPostingBank('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeletePostingBank('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });



  document.getElementById("tabel_dataPostingBank").innerHTML = rowTable
  $("#tabelPostingBank").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}

function loadPostingKasSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelSelectPostingKas').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadpostingkasselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihPostingKas('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelectPostingKas").innerHTML = rowTable;
  $("#tabelSelectPostingKas").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPostingBankSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelSelectPostingBank').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadpostingbankselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihPostingBank('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelectPostingBank").innerHTML = rowTable;
  $("#tabelSelectPostingBank").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPerkiraanAkumulasi () {
  console.log('asd')
  let _token = $("#_token").val();

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanakumulasi') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPostingAkumulasi('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteAkumulasi('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });



  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}

function loadPerkiraanAkumulasiSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAkumulasiSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanakumulasiselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAkumulasiPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAkumulasiSelect").innerHTML = rowTable;
  $("#tabelAkumulasiSelect").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPerkiraanAkumulasiEditSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditAkumulasi').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanakumulasiselectedit') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAkumulasiEditPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAkumulasi").innerHTML = rowTable;
  $("#tabelEditAkumulasi").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPerkiraanAkumulasiSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAkumulasiSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanakumulasiselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAkumulasiPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAkumulasiSelect").innerHTML = rowTable;
  $("#tabelAkumulasiSelect").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPerkiraanAktivaSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanaktivaselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAktivaPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelAktivaSelectPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loaAkumulasiAktivaSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadperkiraanaktivaselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAktivaPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelAktivaSelectPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadBiayaPenyusutanAktivaSelect1() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelBiayaPenyusutanAktiva1').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadbiayapenyusutan1aktivaselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAktivaBiayaPenyusutan1('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutanAktiva1").innerHTML = rowTable;
  $("#tabelBiayaPenyusutanAktiva1").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadBiayaPenyusutanAktivaSelect2() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelBiayaPenyusutanAktiva2').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadbiayapenyusutan2aktivaselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAktivaBiayaPenyusutan2('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutanAktiva2").innerHTML = rowTable;
  $("#tabelBiayaPenyusutanAktiva2").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadAkumulasiAktivaSelect() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectAkumulasi').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadakumulasiaktivaselect') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAktivaAkumulasi('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectAkumulasi").innerHTML = rowTable;
  $("#tabelAktivaSelectAkumulasi").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function loadPostingAktiva () {
  let _token = $("#_token").val();

  // $('#tabelPostingAktiva').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingloadpostingaktiva') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""
    console.log(item)
    rowTable += `<tr>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td>${item.Persen ? item.Persen : 0}</td>
    <td>${item.Metode ? item.Metode : 0}</td>
    <td>${item.Akumulasi ? item.Akumulasi : 0}</td>
    <td>${item.Biaya1 ? item.Biaya1 : 0}</td>
    <td>${item.PersenBiaya1 ? item.PersenBiaya1 : 0}</td>
    <td>${item.Biaya2 ? item.Biaya2 : 0}</td>
    <td>${item.PersenBiaya2 ? item.PersenBiaya2 : 0}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteAkumulasi('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });



  document.getElementById("tabelDataPostingAktiva").innerHTML = rowTable
  // $("#tabelPostingAktiva").DataTable({
  //   "lengthChange": false,
  //     "paging": false ,
  //   });

}

function buttonPilihAkumulasiPerkiraan(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_PerkiraanAkumulasi").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAkumulasiSelectPerkiraan").modal("hide");

  // Show the second modal
  $("#formAkumulasiSelect").modal("show");
}

function buttonPilihAktivaPerkiraan(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_PerkiraanPostingAktiva").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAktivaSelectPerkiraan").modal("hide");

  // Show the second modal
  $("#formAktivaSelectPerkiraan").modal("show");
}

function buttonPilihAktivaAkumulasi(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_AkumulasiPostingAktiva").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAktivaSelectAkumulasi").modal("hide");

  // Show the second modal
  $("#formAktivaSelectAkumulasi").modal("show");
}

function buttonPilihAkumulasiEditPerkiraan(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_edit_Akumulasi").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAkumulasiEdit").modal("hide");

  // Show the second modal
  $("#formEditAkumulasi").modal("show");
}

function buttonPilihAktivaBiayaPenyusutan1(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_BiayaPenyusutan1PostingAktiva").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAktivaBiayaPenyusutan1").modal("hide");

}

function buttonPilihAktivaBiayaPenyusutan2(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_BiayaPenyusutan2PostingAktiva").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAktivaBiayaPenyusutan2").modal("hide");

}

function buttonPilihPostingKas(selectedPerkiraan, selectedKeterangan) {
  $("#input_add_PostingKas").val(selectedPerkiraan);
  $("#formSelectPostingKas").modal("hide");

}

function buttonPilihPostingBank(selectedPerkiraan, selectedKeterangan) {
  $("#input_add_PostingBank").val(selectedPerkiraan);
  $("#formSelectPostingBank").modal("hide");

}
//

// BUTTON-BUTTON
// ========================== BUTTON POSTING KAS
function buttonPostingKas () {
    loadPostingKas()
  $("#formPostingKas").modal('toggle')
}
function buttonAddPostingKas () {
  $("#formAddPostingKas").modal('toggle')
}
function buttonSelectPostingKas () {
    loadPostingKasSelect()
  $("#formSelectPostingKas").modal('toggle')
}

//============================== BUTTON POSTING BANK
function buttonPostingBank () {
    loadPostingBank()
  $("#formPostingBank").modal('toggle')
}
function buttonAddPostingBank () {
  $("#formAddPostingBank").modal('toggle')
}
function buttonSelectPostingBank () {
    loadPostingBankSelect()
  $("#formSelectPostingBank").modal('toggle')
}

// ====================== BUTTON POSTING AKUMULASI
function buttonAkumulasi () {
  loadPerkiraanAkumulasi()
  $("#formAkumulasi").modal('toggle')
}
function buttonAkumulasiSelect () {
  loadPerkiraanAkumulasi()
  $("#formAkumulasiSelect").modal('toggle')
}
function buttonAkumulasiSelectPerkiraan () {
  loadPerkiraanAkumulasiSelect()
  $("#formAkumulasiSelectPerkiraan").modal('toggle')
}

// ========================= BUTTON POSTING AKTIVA
function buttonAktivaSelectPerkiraan () {
  loadPerkiraanAktivaSelect()
  $("#formAktivaSelectPerkiraan").modal('toggle')
}
function buttonAktivaSelectAkumulasi () {
  loadAkumulasiAktivaSelect()
  $("#formAktivaSelectAkumulasi").modal('toggle')
}
function buttonAkumulasiSelectAkumulasi () {
  loadPerkiraanAkumulasiEditSelect()
  $("#formAkumulasiEdit").modal('toggle')
}
function buttonAktivaSelectBiayaPenyusutan1 () {
  loadBiayaPenyusutanAktivaSelect1()
  $("#formAktivaBiayaPenyusutan1").modal('toggle')
}
function buttonAktivaSelectBiayaPenyusutan2 () {
  loadBiayaPenyusutanAktivaSelect2()
  $("#formAktivaBiayaPenyusutan2").modal('toggle')
}
function buttonAddAktiva () {
  $("#formAddPostingAktiva").modal('toggle')
}
function buttonAktiva () {
    loadPostingAktiva()
  $("#formAktiva").modal('toggle')
}

function buttonEditPostingAkumulasi (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostingspdetailpostingakumulasi') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_Akumulasi").value = res[0].Perkiraan

    }})
    $("#formEditAkumulasi").modal('toggle')
}

function buttonDeleteAkumulasi (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Akumulasi', 'Apakah yakin ingin menghapus Akumulasi ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostingspdeleteakumulasi') !!}",
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
              alertify.success("Akumulasi telah dihapus");
              loadPerkiraanAkumulasi();

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

//
function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let nama = $("#input_edit_nama").val();
  let kurs = $("#input_edit_kurs").val();
  let simbol = $("#input_edit_simbol").val();

  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs  harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Simbol  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Departemen telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitEditPostingAkumulasi () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_Akumulasi").val();

  console.log(kode)

  $.ajax({
    url: "{!! url('mastersetpostingspeditpostingakumulasi') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Posting Akumulasi telah diedit");
        loadPerkiraanAkumulasi()
        $("#formEditAkumulasi").modal('toggle')
      }

    }})

}

//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let kurs = $("#input_add_kurs").val();
  let simbol = $("#input_add_simbol").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Valas telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddAkumulasi () {

  let _token = $("#_token").val();
  let kode = $("#input_add_PerkiraanAkumulasi").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostingspaddakumulasi') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Akumulasi telah ditambah");
        loadPerkiraanAkumulasi();
        $("#formAkumulasiSelect").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddAktiva () {

  let _token = $("#_token").val();
  let perkiraanAktiva = $("#input_add_PerkiraanPostingAktiva").val();
  let AkumulasiAktiva = $("#input_add_AkumulasiPostingAktiva ").val();
  let BP1Aktiva = $("#input_add_BiayaPenyusutan1PostingAktiva").val();
  let PersenBP1Aktiva = $("#input_add_PersenBiayaPenyusutan1PostingAktiva").val();
  let BP2Aktiva = $("#input_add_BiayaPenyusutan2PostingAktiva").val();
  let PersenBP2Aktiva = $("#input_add_PersenBiayaPenyusutan2PostingAktiva").val();
  let persenPenyusutanAktiva = $("#input_add_PersenPostingAktiva").val();
  let metodeAktiva = $("#input_add_MetodePostingAktiva").val();


  $.ajax({
    url: "{!! url('mastersetpostingspaddaktiva') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      perkiraanAktiva,
      AkumulasiAktiva,
      BP1Aktiva,
      PersenBP1Aktiva,
      BP2Aktiva,
      PersenBP2Aktiva,
      persenPenyusutanAktiva,
      metodeAktiva
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Akumulasi telah ditambah");
        $("#formAddPostingAktiva").modal('toggle')
      }

    }})
}

function buttonRefreshPostingAkumulasi(){
loadPerkiraanAkumulasi()
}
function buttonRefreshPostingAktiva(){
loadPostingAktiva()
}

</script>
@endsection
