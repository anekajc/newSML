
  const bmmodal_browsemaster = "browsemaster";
  var bm_onfocusfield = "";
  var bm_inputDestination, bm_inputDestinationValue, bm_url = "";

  var bm_filterMode = false, bm_filterOnShow = false, bm_filterOnFocus = "", bm_filterCart = {};

  document.addEventListener('keydown', function(event) {
    if(g_modeModal == bmmodal_browsemaster) {
      if(event.keyCode == 27){ return doCloseFormBrowseMaster(); }
    }

    if(bm_onfocusfield != "" && !event.ctrlKey && event.keyCode == 13) { return doBrowseMaster(); }
    if(bm_filterOnFocus && event.keyCode == 13) { return doBrowseMasterFilter(); }
  });

  function doShowFormBrowseMaster() {
    if (bm_filterOnShow) { return; }
    g_modeModal = bmmodal_browsemaster;
    $("#formBrowseMaster").modal('toggle');
  }

  function doCloseFormBrowseMaster() {
console.log('doCloseFormBrowseMaster')
    g_modeModal = g_modalNone;
    bm_filterMode = false;
    bm_filterOnShow = false;
    $("#browsefilter").val("");
    $('#tabelbrowsemaster').DataTable().destroy();
    $("#formBrowseMaster").modal('toggle');
  }

  function doBlurInputBrowseMaster() {
    doSetInputBrowseMaster("", "", false, false);
  }

  function doSetInputBrowseMaster(_inputDestination, _url, _modeFilter = false, _onFocus = true) {
    if (_onFocus) {
        bm_onfocusfield = _inputDestination;
        bm_inputDestination = _inputDestination;
        bm_url = _url;
    } else {
        bm_onfocusfield = "";
    }
    bm_filterMode = _modeFilter;
  }

  function doBlurFilterBrowse() { bm_filterOnFocus = false; }
  function doSetFilterBrowse() { bm_filterOnFocus = true; }

  function doBrowseMaster(_inputDestination = "", _url = "", _modeFilter = false) {
    // jika ada custom shortcut, buatlah function doBrowseMasterCustomShortcut di Blade
    if ($.isFunction(window.doBrowseMasterCustomShortcut)) { return doBrowseMasterCustomShortcut(); }

    if (_url != "") {
      bm_inputDestination = _inputDestination;
      bm_url = _url;
      bm_filterMode = _modeFilter;
    }
    doBrowseMasterControl(bm_inputDestination, bm_url);
  }

  function doBrowseMasterFilter() {
    if ($("#browsefilter").val() == "") { return; }
    bm_filterCart['filter'] = $("#browsefilter").val();
    doBrowseMasterControl(bm_inputDestination, bm_url);
  }

  function doBrowseMasterControl(_inputDestination, _url) {
    let title = "";
    doShowFormBrowseMaster();

    // konstruksi tabel browse master
    let ajaxConfig = {
        url    : _url,
        type   : "get",
        async  : false,
        success: function(res) {
            if ((res.table && res.table.length > 0) || (bm_filterMode && res.table)) {
                doShowBrowseMaster(res);
                title = res.title;
            }
        }
    };
    if (bm_filterOnShow) { ajaxConfig.data = bm_filterCart; }
    $.ajax(ajaxConfig);

    bm_inputDestination = _inputDestination;
    $("#formBrowseMasterLabel").html(title);
  }

  function doShowBrowseMaster(_res) {
    if (bm_filterOnShow) { $('#tabelbrowsemaster').DataTable().destroy(); }

    // PERSIAPKAN KOLOM TABLE BROWSE MASTER
    let cart_browseHeader = _res.kolom;

    // HEADER TABLE BROWSE MASTER
    let _str = '<tr>';
    if (cart_browseHeader.length > 0) {
      cart_browseHeader.forEach((item, i) => {
        _str += '<th style="text-align: center;">' + item[1] + '</th>';
      });
    }
    _str += '</tr>';
    $("#tabelbrowsemaster_header").html(_str);

    // DATA TABLE BROWSE MASTER
    _str = "";
    if (_res.table && _res.table.length > 0) {
        _res.table.forEach((item, i) => {
            _str += '<tr id="' + i + '-trrowbrowsemaster" draggable="true" onclick="doSelectbrowsemaster(' + i + ',\'' + item[cart_browseHeader[0][0]] + '\')">';
            cart_browseHeader.forEach((itemcart, j) => {
                if (itemcart[2] == "date") {
                  _str += '  <td>' + format_date(item[itemcart[0]]) + '</td>';
                } else if (itemcart[2] == "float") {
                  let _value = currencyNormalizer(item[itemcart[0]]);
                  let _decimal = itemcart[3];
                  _str += '  <td>' + format_number(_value,_decimal) + '</td>';
                } else {
                  _str += '  <td>' + nullToEmpty(item[itemcart[0]]) + '</td>';
                }
            });
            _str += '</tr>';
        });
    } else {
        _str += '<tr>';
        if (cart_browseHeader.length > 0) {
          let _strEmpty = (bm_filterMode) ? "Gunakan kolom filter untuk mencari " + _res.title : "Tidak ada transaksi ditemukan"
          cart_browseHeader.forEach((item, i) => {
            if (i === 0) {
              _str += '  <td colspan="' + cart_browseHeader.length + '" class="text-center">' + _strEmpty + '</td>';
            } else {
              _str += '  <td style="display: none;"></td>';
            }
          });
        }
        _str += '</tr>';
    }

    $("#tabelbrowsemaster_data").html(_str);

    let _searching = (!bm_filterMode);
    bm_filterOnShow = (bm_filterMode);
    $("#tabelbrowsemaster").DataTable({
      "lengthChange": false,
      "paging": false,
      "searching": _searching,
    });

    if (bm_filterMode) { $('#tabelbrowsefilter').show(); } else { $('#tabelbrowsefilter').hide(); }
  }

  function doSelectbrowsemaster(_row, _kode) {
    $('#tabelbrowsemaster_data > tr').each(function() {
      $(this).css('background-color', '');
      $(this).css('color', '');
    });

    $("#"+_row+"-trrowbrowsemaster").css('background-color', '#0069d9');
    $("#"+_row+"-trrowbrowsemaster").css('color', 'white');

    bm_inputDestinationValue = _kode;

    $("#"+_row+"-trrowbrowsemaster").on('dblclick', function() {
        doShowSelectedBrowseMaster();
      });
  }

  function doShowSelectedBrowseMaster() {
    $("#" + bm_inputDestination).val(bm_inputDestinationValue);
    doCloseFormBrowseMaster();
  }