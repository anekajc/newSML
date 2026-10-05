<div class="modal fade" id="formBrowseMaster" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header text-right">
        <h5 class="modal-title" id="formBrowseMasterLabel">Browse Master</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="doCloseFormBrowseMaster(true)">
          <span aria-hidden="true">&times;</span>
        </button> -->
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto" data-dismiss="modal" aria-label="Close" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="doCloseFormBrowseMaster(true)">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row mt-3 mb-2" id="tabelbrowsefilter" style="display: none;">
            <div class="col-12 d-flex justify-content-end align-items-center">
              <label for="browsefilter" class="mr-2 mb-0">SEARCH:</label>
              <input type="text" id="browsefilter" class="form-control form-control-sm" style="width: 200px;" onfocus="doSetFilterBrowse()" onblur="doBlurFilterBrowse()">
              <button type="button" class="btn btn-primary col-1" style="font-size: 14px; margin: 0 5px;" onclick="doBrowseMasterFilter()" hidden><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="customTable">
                    <table id="tabelbrowsemaster" class="table table-bordered table-striped"  >
                      <thead id="tabelbrowsemaster_header" class="text-center bg-primary text-white">
                      </thead>
                      <tbody id="tabelbrowsemaster_data" class="text-left" >
                      </tbody>
                    </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="doCloseFormBrowseMaster(true)" title="Batal (Esc)">Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="doShowSelectedBrowseMaster()" title="Submit">Submit</button> -->
      </div>
    </div>
  </div>
</div>