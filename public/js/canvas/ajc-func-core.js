/*
    Function Javascript utama yang dipakai program Web AJC
    Selalu ubah tanggal Last Update jika melakukan perubahan
    Format: dd Month yyyy hh:mm (nama_user)

    Last Update:
    12 Februari 2025 12:44 (Tio)
*/

      function onEnterSetup(_id_name) {
        var input = document.getElementById(_id_name);
        input.addEventListener("keypress", function(event) {
          if (event.key === "Enter") {
            /* Buatlah function bernama onEnterFunction */
            /* Isinya berupa Switch Case per nama ID */
            onEnterFunction(_id_name);
          }
        });
      }


      function padZero(num)     { return num.toString().padStart(2, '0'); }
      function getDateHours()   { let dateNow = new Date(); return padZero(dateNow.getHours()); }
      function getDateMinutes() { let dateNow = new Date(); return padZero(dateNow.getMinutes()); }
      function getDateSeconds() { let dateNow = new Date(); return padZero(dateNow.getSeconds()); }
      function getDateIndo()    { let dateNow = new Date(); return padZero(dateNow.toLocaleDateString('id-ID')); }

      function getDateNow(_separator = "") {
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear() + _separator + month + _separator + day;

        return today;
      }

      function getTimeNow(_separator = ":") {
        return getDateHours() + _separator + getDateMinutes() + _separator + getDateSeconds();
      }
      

      function numberWithCommas(n) { var parts=n.toString().split("."); return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : ""); }
      function numberNoCommas(n) { return n.replace(/,/g, ""); }

      // function numberNoDecimals(n) { var _num = (n==".00") ? "0" : n.replace(".00", ""); return _num; }
      function numberNoDecimals(n) {
        var _isDesimal = false;
        for (var i = 0; i < n.length; i++) {
          if (n[i] == ".") { _isDesimal = true; break; }
        }
        if (!_isDesimal) { return (n == "") ? "0" :n; }

        var _num = n;

        while (_num != "") {
          var lastChar = _num[_num.length -1];
          if (lastChar == "0") {
            // menghilangkan semua nol di belakang koma
            _num = _num.substring(0, _num.length-1);
          } else {
            // hilangkan jika koma, ada kemungkinan nilainya jadi empty
            if (lastChar == ".") {
              _num = _num.substring(0, _num.length-1);
            }

            // jika ketemu yang bukan nol, loop berhenti
            break;
          }
        }

        return (_num == "") ? "0" : _num;
      }

      function currencyNormalizer(n) {
        if (n == null) { return 0.0; }
        var _isDesimal = false;
        for (var i = 0; i < n.length; i++) {
          if (n[i] == ".") { _isDesimal = true; break; }
        }
        if (!_isDesimal) { return (n == "") ? 0.0 : parseFloat(n); }

        var _num = n;

        while (_num != "") {
          var lastChar = _num[_num.length -1];
          if (lastChar == "0") {
            // menghilangkan semua nol di belakang koma
            _num = _num.substring(0, _num.length-1);
          } else {
            // hilangkan jika koma, ada kemungkinan nilainya jadi empty
            if (lastChar == ".") {
              _num = _num.substring(0, _num.length-1);
            }

            // jika ketemu yang bukan nol, loop berhenti
            break;
          }
        }

        return (_num == "") ? 0.0 : parseFloat(_num);
      }

      function removeSpasi(n) { return n.replace(/ /g, ""); }

      function numbersWithDividers(n) {
        if (isNaN(n)) return ""; // Handle non-numeric input gracefully

        // Use only the integer part of the number
        let integerPart = Math.floor(n).toString();

        // Format the integer part with dot separators
        return integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      function toInteger(n) { return parseInt(n.replace(/,/g, "")); }
      function toFloat(n) { n= (n.substring(0,1)=="." ? "0" : "")+n; return parseFloat(n.replace(/,/g, "")); }

      function round(value, precision) { var multiplier = Math.pow(10, precision || 0); return Math.round(value * multiplier) / multiplier; } function middleTD() {}
      function format_timestamp(date) { if (date == "" || date == null) return ""; tgl = date.split(" ")[0]; waktu = date.split(" ")[1]; return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu; }

      function format_date(date, isMonthName = false, separatorOld = "-", separatorNew = "/", withDay = true) { 
        if (date == "" || date == null) { return ""; }

        let monthNames = [
              "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
              "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
            ];

        date = date.substring(0, 10);
        let _day   = date.split(separatorOld)[2];
        let _month = (isMonthName) ? monthNames[date.split(separatorOld)[1] - 1] : date.split(separatorOld)[1];
        let _year  = date.split(separatorOld)[0];
        let _separator = (isMonthName) ? " " : separatorNew;

        return (withDay) ? _day + _separator + _month + _separator + _year : _month + _separator + _year
      }

      function format_number(n, _decimal = 0, _comma = true) {
        return (_comma) ? numberWithCommas(n.toFixed(_decimal)) : n.toFixed(_decimal); 
      }

      function messageRequired(_input_name) {
        return "Kolom "+ _input_name +" harus terisi.";
      }
      function messageHiddenRequired(_input_name) {
        return "Terjadi kesalahan. "+ _input_name +" tidak ditemukan. Silahkan refresh halaman.";
      }
      function messageNotZero(_input_name) {
        return "Kolom "+ _input_name +" tidak boleh nol.";
      }
      function messageMustNumber(_input_name) {
        return "Kolom "+ _input_name +" harus angka.";
      }
      function messageNotEmptyCart(_menu_name) {
        return "Detail "+ _menu_name +" tidak boleh kosong";
      }

      function nullToEmpty (_val) { return (_val == null) ? ""  : _val; }
      function nullToZero  (_val) { return (_val == null) ?  0  : _val; }
      function nullToStrip (_val) { return (_val == null) ? "-" : _val; }
      function emptyToZero (_val) { return (_val == "")   ? "0" : _val; }

      function cekRequiredNotEmpty(_id_name) {
        if ($("#" + _id_name).val() != "") {
          return true;
        } else {
          $("#" + _id_name).focus();
          return false;
        }
      }
      function cekRequiredNotZero(_id_name) {
        var _nominal = $("#" + _id_name).val();
        if (_nominal != "" && _nominal != "0.00" && _nominal != "0") {
          return true;
        } else {
          $("#" + _id_name).focus();
          return false;
        }
      }

      function setEmptyNumberToZero(_id_name) {
        return ($("#" + _id_name).val() != "") ? $("#" + _id_name).val().replace(/,/g, '') : "0";
      }

      function setFocus(_modal, _item) {
        $('#' + _modal).on('shown.bs.modal', function () {
          $('#' + _item).focus();
        })
      }

      function doSelectRow(_id_data, _row, _oldrow, _tr = "row", _bgcolor = "blue", _color = "white") {
        $('#'+_id_data+' > tr').each(function() {
          $(this).css('background-color', '');
          $(this).css('color', '');
        });

        if (_row != _oldrow) {
          $("#"+_row+"-tr"+_tr).css('background-color', _bgcolor);
          $("#"+_row+"-tr"+_tr).css('color', _color);

          return _row;
        } else { 
          return "";
        }
      }

      function trWithSelectRow(_id_kode_urut, _name = "row", _addition_str = "") {
        let id_kode = ($.isNumeric(_id_kode_urut)) ? _id_kode_urut : removeSpasi(_id_kode_urut);
        return '<tr id="' + id_kode + '-tr'+_name+'" onclick="select'+_name+'(\'' + id_kode + '\', '+_addition_str+')">';
      }