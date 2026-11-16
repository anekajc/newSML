{{-- DIBANGKITKAN dari purchaseOrderNonStock.blade.php untuk halaman PO gabungan (/purchaseorder).
     Semua id, fungsi, variabel global, name dan class show/hide yang bentrok dengan
     purchaseOrder.blade.php diberi awalan ns_. Ubah PO Non Stock di file ini, bukan di blade lama. --}}
<script type="text/javascript">

let ns_dataTableAdd = []
let ns_dataTableEdit = []

let ns_dataAddAddListItem = []

let ns_dataRefreshOutstanding = []
let ns_dataRefreshOutstanding2 = []

// Kartu ringkasan (Jumlah Supplier / Total DPP / Outstanding PR) di atas tab-content.
// Jumlah Supplier & Total DPP dihitung server (POController::daftarPO) dari seluruh PO
// tab Purchase Order yang kena filter periode + status/otorisasi. Outstanding PR
// diambil dari recordsTotal endpoint ponsdataoutstandingpr - satu baris di sana =
// satu kode barang, jadi 1 nobukti dengan 3 barang terhitung 3. Tidak ada
// Outstanding SO di sini - menu ini tidak punya SO.
let ponsKpiOut = { 1 : null }

function renderKpiPONS () {
  // ponsKpiPO diisi dari respons ponsloadpurchaseorder - lihat ns_renderTabelPO().
  let kpi = ponsKpiPO
  let totalDPP = kpi ? (Number(kpi.totDPP) || 0) : 0
  let jmlSupp = kpi ? (Number(kpi.jmlSupp) || 0) : 0

  let cards = [
    ['Jumlah Supplier', jmlSupp, '#dc2626', '#fee2e2', 'bi bi-people', false],
    ['Total DPP', totalDPP, '#4f46e5', '#ede9fe', 'bi bi-receipt', true],
    ['Outstanding PR', ponsKpiOut[1] === null ? '-' : ponsKpiOut[1], '#0891b2', '#cffafe', 'bi bi-clipboard-data', false]
  ]

  document.getElementById('ns_poKpiStrip').innerHTML = cards.map((c) => `
    <div class="po-kpi-card">
      <div class="po-kpi-ic" style="background:${c[3]};color:${c[2]}">
        <i class="${c[4]}"></i>
      </div>
      <div>
        <div class="po-kpi-label">${c[0]}</div>
        <div class="po-kpi-val">${c[5] ? 'Rp ' + ns_formatAngka(c[1]) : c[1]}</div>
      </div>
    </div>
  `).join('')
}

let ns_dataRefreshPenerimaan = []

let ns_listAlamatKirim = []

let ns_tempAddAdd = {}
let ns_tempAddEdit = {}
let ns_tempIndexEdit = 0
let ns_tempEditAdd = {}
let ns_tempEditEdit = {}

let ns_tipeform = ''
let ns_tipeformitem = ''
ns_buttonShowHideHeader()

// Qty/Harga/Disc RP di Tambah Item pakai pemisah ribuan (autoNumeric), disamakan
// dengan purchaseOrder.blade.php.
jQuery(function($) {
  $('.ns_input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
    }
  );
});

$(document).ready(function(){
  ns_muatDropdownAlamatKirim()
  ns_muatDropdownValas()

  $("#ns_tabel_add_list_barangall").DataTable({
    "lengthChange": false,
      "paging": false ,
      "searching" : false,
  });

  pickerKasInit('ns_tabel_add_list_pelanggan')

  $("#ns_tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

   // $("#tabel3").DataTable({
   //      "lengthChange": false,
   //        "paging": false ,
   //      });

});

// isoto1 = status otorisasi ns_level 1 dokumen (0/1). Wajib dikirim sebagai isoto1oto, sama seperti
// purchaseOrder.blade.php: POController@updateOtorisasi memakainya di jalur Direktur - tanpa itu
// server membalas 3 tanpa mengubah apa pun.
function ns_buttonOtorisasi (nobukti, isoto1 = 0) {
  console.log(nobukti, isoto1)

  let akses = $("#ns_akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

        let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('poupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      isoto1oto: Number(isoto1) ? 1 : 0
    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        ns_loadAll()
        return
      }
      if (res == 2) {
        alertify.warning("Melebihi plafon")
        return
      }
      if (res == 3) {
        alertify.warning("Diperlukan otorisasi 1 terlebih dahulu")
        return
      }
      alertify.success('Berhasil update otorisasi')
      ns_loadAll()

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function ns_buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#ns_akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.prompt('Batal Otorisasi', "Masukkan keterangan batal otorisasi nomor  " + nobukti, "",
      function(evt, value) {
        let xpket = String(value || '').trim()

        if (!xpket) {
          alertify.warning('Keterangan harus diisi.')
          return false
        }
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('poupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
            pket : xpket
          },
          success: function(res) {
            if (res && res.pesan) {
              alertify.warning(res.pesan)
              ns_loadAll()
              return
            }
            alertify.success('Berhasil batal otorisasi')
            ns_loadAll()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });



}

function ns_onChangeCatatan () {

  if (ns_tipeform == 'edit') {
    let value  = $("#ns_input_add_keterangan").val()
    ns_onChangeHeader('Keterangan' , value)

  }

}
function ns_onChangeTgglKirim () {
  if (ns_tipeform == 'edit') {
    let value  = $("#ns_input_add_tanggalkirim").val()
    ns_onChangeHeader('TglKirim' , value)
  }
}

function ns_onChangeTipePPN () {
  console.log('ns_onChangeTipePPN')
  if (ns_tipeform == 'edit') {
    let value = $("#ns_input_add_tipeppn").val()
    console.log(value)
    ns_onChangeHeader('TipePPn' , value)
    ns_onChangeHeader('PPN' , value)
    ns_refreshUpdateHeader()
    let nobukti = $("#ns_input_add_nobukti").val()
    ns_refreshDataTableAdd(nobukti)
  }


}

function ns_onChangeDraftPO () {
  console.log('ns_onChangeDraftPO')
  if (ns_tipeform == 'edit') {
    let value = $("#ns_input_add_draftpo").val()
    console.log(value)
    ns_onChangeHeader('PPO' , value)
    ns_refreshUpdateHeader()
    let nobukti = $("#ns_input_add_nobukti").val()
    ns_refreshDataTableAdd(nobukti)
  }


}

function ns_onChangeHari () {
  console.log('ns_onChangeHari')
  if (ns_tipeform == 'edit')
  {
    let value = $("#ns_input_add_hari").val()
    console.log(value)
    ns_onChangeHeader('HARI' , value)
    ns_refreshUpdateHeader()
    let nobukti = $("#ns_input_add_nobukti").val()
    ns_refreshDataTableAdd(nobukti)
  }
}

function ns_onChangeInputAddDisc () {
    // document.getElementById("ns_input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (ns_tipeform == 'edit') {
      let value = $("#ns_input_add_disc").val()
      console.log(value)
      ns_onChangeHeader('DISC' , value)
      ns_refreshUpdateHeader()
      let nobukti = $("#ns_input_add_nobukti").val()
      ns_refreshDataTableAdd(nobukti)
    }
}

function ns_onChangeInputAddDiscRp () {
    // document.getElementById("ns_input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (ns_tipeform == 'edit') {
        let value = ns_formatAngkaVal($("#ns_input_add_discrp").val())
        console.log(ns_dataHeaderAdd)
        let x = Number(value) / Number(ns_dataHeaderAdd.TOtSubtotalRP) * 100
        console.log(x)
        console.log(value)
        ns_onChangeHeader('DISC' , x)
        ns_refreshUpdateHeader()
        let nobukti = $("#ns_input_add_nobukti").val()
        ns_refreshDataTableAdd(nobukti)
      }
}

function ns_refreshUpdateHeader ()
{
  let _token  = $("#_token").val()
  let nobukti = $("#ns_input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function ns_onChangeHeaderSP (field, value , field1 = null , value2 = null)
{
  let _token  = $("#_token").val()
  let nobukti = $("#ns_input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function ns_onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#ns_input_add_nobukti").val()
  $.ajax({
    url: "{!! url('poonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        return
      }
      if (res && res.abaikan) {
        return
      }
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function ns_submitAddAdd () {
  ponsProses('Menyimpan item...', function () { ns_submitAddAddKerja() })
}

function ns_submitAddAddKerja () {


  let checkDate = new Date($("#ns_input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let TglJatuhTempo = new Date($("#ns_input_add_tanggal").val())

  let hari = $("#ns_input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (ns_dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "I"
  let NoBukti = $("#ns_input_add_nobukti").val()
  let NoUrut = $("#ns_input_add_nourut").val()
  let Tanggal = $("#ns_input_add_tanggal").val()
  let KodeSupp = $("#ns_input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#ns_input_add_kodeekspedisi").val()
  let Keterangan = $("#ns_input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#ns_input_add_valas").val()
  let Kurs = $("#ns_input_add_kurs").val()
  let PPn = $("#ns_input_add_tipeppn").val()
  let TipeBayar = $("#ns_input_add_pembayaran").val()
  let Hari = $("#ns_input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //discrp
  let Urut = 0
  let KodeBrg =  $("#ns_input_add_add_kodebarang").val()
  let Qnt =  ns_formatAngkaVal($("#ns_input_add_add_qty").val())
  let NoSat =  $("#ns_input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = ns_formatAngkaVal($("#ns_input_add_add_harga").val())
  let DiscP = $("#ns_input_add_add_discpersen1").val()
  let DiscTot = ns_formatAngkaVal($("#ns_input_add_add_discrp").val())
  let NoPPL = $("#ns_input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  // UrutPPL WAJIB diambil dari field tersembunyi yang diisi ns_buttonAddAddPickBarangNonFOC()
  // (baris PR yang dipilih). Dulu dibaca dari variabel global urutPPLTemp yang HANYA diisi
  // buttonAddAddPickBarangJasaPPL() - fungsi itu sudah tidak dipakai lagi sejak daftar
  // "Barang dari PR" pindah ke ns_buttonAddAddPickBarangNonFOC(), jadi nilainya selalu 0.
  // Akibatnya dbPODet.UrutPPL=0, sp_RefreshOutPPL tidak menemukan pasangan
  // (NoPPL,UrutPPL) di dbPPLDet, QntPO PR tidak pernah bertambah, dan barangnya tetap
  // muncul di tab Outstanding PR maupun di daftar browse barang dari PR.
  let UrutPPL = $("#ns_input_add_add_urutPPL").val()
  let Kodegdg = $("#ns_input_add_kodealamatkirim").val()
  let Discpdet2 = $("#ns_input_add_add_discpersen2").val()
  let Discpdet3 = $("#ns_input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#ns_input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#ns_input_add_add_foc").val()
  // No SO & No. PO Cust dibuang dari ns_form (PO Non Stock tidak memakainya) - dikirim
  // konstanta '-' apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let Noso = '-'
  let NOPOCUST = '-'
  //iduser = $user->name
  //pJasa = 0
  // NPPH23, PERKIRAAN & PPH21 header ditentukan server (PONonStockController@nilaiHeaderNonStock):
  // PPH dari supplier, perkiraan dari COA Biaya barang. Nilai di bawah hanya pengisi parameter.
  let NPPH23 = 0
  let PERKIRAAN = ''
  let SatX = $("#input_add_add_satuanAlias").val()
  let COST = $("#input_add_add_costing").val()
  let SUBCOST = $("#input_add_add_subcosting").val()
  let TglKirim = $("#ns_input_add_tanggalkirim").val()
  let PPH21 = 0
  let NOPNw = $("#ns_input_add_add_nopnwpo").val()
  let UrutPNW = 0

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  console.log(ns_tempAddAdd)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0

  // Master barang jasa (DBBARANG) di sini umumnya TIDAK mengisi SAT1/SAT2/SAT3, jadi tiga
  // cabang di bawah tidak pernah kena dan item tersimpan dengan Satuan '-' dan Isi 0.
  // Barang dari PR memakai satuan & isi baris PR-nya sendiri - pola yang sama dipakai
  // purchaseOrder.blade.php (cabang ns_poSumberBarang() === 'PR').
  let masterSat = ns_tempSatuanBarang.length ? ns_tempSatuanBarang[0] : null

  if (masterSat && NoSat == 1) {
    qnt1 = Qnt * masterSat.ISI1
    Satuan = masterSat.SAT1
    Isi = masterSat.ISI1
  }
  if (masterSat && NoSat == 2) {
    qnt1 = Qnt * masterSat.ISI2
    Satuan = masterSat.SAT2
    Isi = masterSat.ISI2
  }
  if (masterSat && NoSat == 3) {
    qnt1 = Qnt * masterSat.ISI3
    Satuan = masterSat.SAT3
    Isi = masterSat.ISI3
  }

  if (!Satuan && ns_tempAddAdd) {
    Satuan = ns_tempAddAdd.Sat || ns_tempAddAdd.Satuan || ''
    if (!Number(Isi)) { Isi = Number(ns_tempAddAdd.Isi) || 0 }
  }

  if (Satuan == '') {
    Satuan = '-'
  }
  // Isi 0 tidak punya arti (dipakai konversi satuan di vwOutPPL/laporan) - baris PR non
  // stock selalu berisi 1.
  if (!Number(Isi)) {
    Isi = 1
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,

    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,

    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,

    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,

    // TipeDisc,
    // Disc,
    //discrp
    Urut,
    KodeBrg,

    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,

    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,

    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,

    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,

    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,

    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    NPPH23,

    PERKIRAAN,
    SatX,
    COST,
    SUBCOST,
    TglKirim,

    PPH21,
    NOPNw,
    UrutPNW
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    url: "{!! url('ponsspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      NoBukti,
      NoUrut,
      Tanggal,
      TglJatuhTempo,
      KodeSupp,
      // Handling,
      KodeExp,
      Keterangan,
      // FakturSupp,
      KodeVls,
      Kurs,
      PPn,
      TipeBayar,
      Hari,
      // TipeDisc,
      // Disc,
      // discrp
      Urut,
      KodeBrg,
      Qnt,
      NoSat,
      Satuan,
      Isi,
      Harga,
      DiscP,
      DiscTot,
      NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      UrutPPL,
      Kodegdg,
      Discpdet2,
      Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      pFOC,
      Noso,
      Jmlrecord,
      NOPOCUST,
      // IdUser,
      // pJasa,
      NPPH23,
      PERKIRAAN,
      SatX,
      COST,
      SUBCOST,
      TglKirim,
      PPH21,
      NOPNw,
      UrutPNW
    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        return
      }

      if (res == 1) {

        ponsTandaiDaftarBerubah()
        ns_tipeform = 'edit'
        document.getElementById("ns_buttonAddListPelanggan").disabled = true
        document.getElementById("ns_input_add_kodesupplier").disabled = true
        // PO sudah tersimpan (punya item) - Jenis PO tidak bisa diganti lagi.
        poIsiDropdownJenisPO('ns_input_add_jenispo', 'nonstock', false)
        $('#ns_divhargaterakhir').hide();
        ns_cleanFormAddAdd()

        ns_refreshDataTableAdd(NoBukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        ns_setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }
      // 3 & 4 = item DITOLAK database. Dulu kegagalan ini tidak kelihatan sama sekali:
      // sp_PO sudah terlanjur menulis header (dbPO) lalu INSERT dbPODET-nya gagal diam-diam,
      // sehingga dokumen tersimpan tanpa satu pun barang - di ns_form barangnya terbaca null
      // dan di tab Purchase Order dokumennya hilang (TotTotalRp NULL).
      if (res == 3) {
        alertify.warning('Kode barang tidak ada di master barang - pilih barang lewat tombol browse')
      }
      if (res == 4) {
        alertify.warning('Item gagal disimpan (ditolak database) - cek kode barang & satuan')
        ns_refreshDataTableAdd(NoBukti)
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function ns_submitAddEdit () {
  ponsProses('Menyimpan item...', function () { ns_submitAddEditKerja() })
}

function ns_submitAddEditKerja () {

  console.log('submitAddEdits')

  let checkDate = new Date($("#ns_input_add_tanggal").val())

  // let periode_bulan = document.getElementById("periode_bulan").value
  // let periode_tahun = document.getElementById("periode_tahun").value

  // if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
  //     alertify.warning("Tanggal tidak sesuai periode");
  //     return
  // }

  let TglJatuhTempo = new Date($("#ns_input_add_tanggal").val())

  let hari = $("#ns_input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (ns_dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let NoBukti = $("#ns_input_add_nobukti").val()
  let NoUrut = $("#ns_input_add_nourut").val()
  let Tanggal = $("#ns_input_add_tanggal").val()
  let KodeSupp = $("#ns_input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#ns_input_add_kodeekspedisi").val()
  let Keterangan = $("#ns_input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#ns_input_add_valas").val()
  let Kurs = $("#ns_input_add_kurs").val()
  let PPn = $("#ns_input_add_tipeppn").val()
  let TipeBayar = $("#ns_input_add_pembayaran").val()
  let Hari = $("#ns_input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //DiscRp
  let Urut = ns_tempAddEdit.Urut
  let KodeBrg =  $("#ns_input_add_add_kodebarang").val()
  let Qnt =  ns_formatAngkaVal($("#ns_input_add_add_qty").val())
  let NoSat =  $("#ns_input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = ns_formatAngkaVal($("#ns_input_add_add_harga").val())
  let DiscP = $("#ns_input_add_add_discpersen1").val()
  let DiscTot = ns_formatAngkaVal($("#ns_input_add_add_discrp").val())
  let NoPPL = $("#ns_input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#ns_input_add_add_urutPPL").val()
  let Kodegdg = $("#ns_input_add_kodealamatkirim").val()
  let Discpdet2 = $("#ns_input_add_add_discpersen2").val()
  let Discpdet3 = $("#ns_input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#ns_input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#ns_input_add_add_foc").val()
  // No SO & No. PO Cust dibuang dari ns_form (PO Non Stock tidak memakainya) - dikirim
  // konstanta '-' apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let Noso = '-'
  let NOPOCUST = '-'
  //iduser = $user->name
  //pJasa = 0
  // NPPH23, PERKIRAAN & PPH21 header ditentukan server (PONonStockController@nilaiHeaderNonStock):
  // PPH dari supplier, perkiraan dari COA Biaya barang. Nilai di bawah hanya pengisi parameter.
  let NPPH23 = 0
  let PERKIRAAN = ''
  let SatX = $("#input_add_add_satuanAlias").val()
  let COST = $("#input_add_add_costing").val()
  let SUBCOST = $("#input_add_add_subcosting").val()
  let TglKirim = $("#ns_input_add_tanggalkirim").val()
  let PPH21 = 0
  let NOPNw = $("#ns_input_add_add_nopnwpo").val()
  let UrutPNW = 0

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  // let tipediskon = 0
  // if (disc) {
  //   tipediskon = 1
  // }
  // if (discrp) {
  //   tipediskon = 1
  // }

  console.log(ns_tempAddEdit)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0
  if (NoSat == 1) {
    qnt1 = Qnt * ns_tempAddEdit.Isi
    Satuan = ns_tempAddEdit.Satuan
    Isi = ns_tempAddEdit.Isi
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,
    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,
    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,
    // TipeDisc,
    // Disc,
    // DiscRp,
    Urut,
    KodeBrg,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,
    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,
    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,
    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    NPPH23,
    PERKIRAAN,
    SatX,
    COST,
    SUBCOST,
    TglKirim,
    PPH21,
    NOPNw,
    UrutPNW
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) <= 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    // Diperbaiki: dulu menembak pospadd (POController@spAdd, milik PO stock), yang
    // menulis pJasa=0 dan mengabaikan NPPH23/PERKIRAAN/SatX/COST/SUBCOST/PPH21 - setiap
    // edit item PO Non Stock diam-diam membalik dokumen jadi PO stock (hilang dari
    // halaman ini) sekaligus mengosongkan field-field itu. ponsspadd
    // (PONonStockController@spAdd) menulis pJasa=1 dan meneruskan semuanya, sama seperti
    // jalur Add (ns_submitAddAdd) yang sudah benar.
    url: "{!! url('ponsspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      NoBukti,
      NoUrut,
      Tanggal,
      TglJatuhTempo,
      KodeSupp,
      // Handling,
      KodeExp,
      Keterangan,
      // FakturSupp,
      KodeVls,
      Kurs,
      PPn,
      TipeBayar,
      Hari,
      // TipeDisc,
      // Disc,
      // DiscRp,
      Urut,
      KodeBrg,
      Qnt,
      NoSat,
      Satuan,
      Isi,
      Harga,
      DiscP,
      DiscTot,
      NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      UrutPPL,
      Kodegdg,
      Discpdet2,
      Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      pFOC,
      Noso,
      Jmlrecord,
      NOPOCUST,
      // IdUser,
      // pJasa,
      NPPH23,
      PERKIRAAN,
      SatX,
      COST,
      SUBCOST,
      TglKirim,
      PPH21,
      NOPNw,
      UrutPNW

    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        return
      }
      console.log('resspsoaddedit', res)
      if (res == 3) {
        alertify.warning('Kode barang tidak ada di master barang - pilih barang lewat tombol browse')
        return
      }
      ponsTandaiDaftarBerubah()

      // ns_lockFormAdd()
      $('.ns_showhide').hide();
      ns_refreshDataTableAdd(NoBukti)

      alertify.success('Berhasil edit item')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function ns_onChangeInputAddPembayaran () {
  console.log("ns_onChangeInputAddPembayaran")
  let check = Number($("#ns_input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (ns_dataTableAdd.length) {

    ns_onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#ns_input_add_nobukti").val()
  console.log('len',ns_dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#ns_input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].hari) {
          document.getElementById("ns_input_add_hari").value = res[0].hari

          if (ns_dataTableAdd.length) {
            console.log('masokk')
            ns_onChangeHeader('HARI' , res[0].hari)
            ns_refreshUpdateHeader()
            // let nobukti = $("#ns_input_add_nobukti").val()
            ns_refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("ns_input_add_hari").value = 0
    // console.log('ns_onChangeHari')
    if (ns_tipeform == 'edit') {
      console.log('len', ns_dataTableAdd.length)
      console.log(value)
      // ns_onChangeHeader('TIPEBAYAR' , check)
      if (ns_dataTableAdd.length) {
        console.log('masokk 2')
        ns_onChangeHeader('HARI' , 0)
        ns_refreshUpdateHeader()
        // let nobukti = $("#ns_input_add_nobukti").val()
        ns_refreshDataTableAdd(nobukti)

      }
    }
  }
}

function ns_onChangeInputAddAddDisc () {
  console.log("ns_onChangeInputAddAddDisc")
  let harga = $("#ns_input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("ns_input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("ns_input_add_add_discrp").value = parseFloat(discRp).toFixed(2)

}

function ns_onChangeInputAddAddHarga () {
  // input_add_add_disc tidak pernah ada di halaman ini (hanya discpersen1/2/3 dan
  // discrp) - dulu baris ini memakai id yang salah sehingga onchange Harga selalu
  // melempar TypeError dan berhenti di tengah jalan.
  document.getElementById("ns_input_add_add_discrp").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen1").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen2").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen3").value = '0.00'
}

function ns_onChangeInputAddEditHarga () {
  document.getElementById("ns_input_add_edit_discrp").value = '0.00'
  document.getElementById("ns_input_add_edit_disc").value = '0.00'
}

function ns_onChangeInputAddAddDiscRp () {
  console.log("ns_onChangeInputAddAddDiscRp")
  let harga = $("#ns_input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = $("#ns_input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function ns_buttonAddAddItem () {
  ns_tipeformitem = 'add'
  $('.ns_showhide').hide();

  $('#ns_divhargaterakhir').hide();

  document.getElementById("ns_input_add_add_outstanding").value = 'PR'
  ns_cleanFormAddAdd()
  ns_poKunciIdentitasBarang(false)
  $('#ns_h4AddAddItem').show();
  $('#ns_h4AddEditItem').hide();
  $('#ns_submitAddAdd').show();
  $('#ns_submitAddEdit').hide();
  $('#ns_addAddItem').show();
  document.getElementById("ns_input_add_add_namabarang").scrollIntoView();
}

function ns_showTableHargaTerakhir () {
  if ($("#ns_divStockProyeksi").is(':visible')) {
    $('#ns_divStockProyeksi').hide();
  }

  if (!$("#ns_divhargaterakhir").is(':visible'))
  {
    $('#ns_divhargaterakhir').show();
  } else
  {
    $('#ns_divhargaterakhir').hide();
  }
}

// Fungsi newSML yang dipertahankan (FOC, No SO, PWO, Stock Proyeksi, dll). Field No SO / FOC / Jasa
// tidak ditampilkan lagi (ns_form disamakan dengan AJC), fungsinya tetap disimpan.
function ns_LockFreeOfCharge(){
  let focState = document.getElementById('ns_input_add_add_foc').value

  if (focState == 1){
    document.getElementById('ns_input_add_add_harga').disabled = true;
    document.getElementById('ns_input_add_add_discrp').disabled = true;
    document.getElementById('ns_input_add_add_discpersen1').disabled = true;
    document.getElementById('ns_input_add_add_discpersen2').disabled = true;
    document.getElementById('ns_input_add_add_discpersen3').disabled = true;
    
    document.getElementById('ns_input_add_add_harga').value = 0,00 ;
    document.getElementById('ns_input_add_add_discrp').value = 0,00 ;
    document.getElementById('ns_input_add_add_discpersen1').value = 0 ;
    document.getElementById('ns_input_add_add_discpersen2').value = 0 ;
    document.getElementById('ns_input_add_add_discpersen3').value = 0 ;
  } else {
    document.getElementById('ns_input_add_add_harga').disabled = false;
    document.getElementById('ns_input_add_add_discrp').disabled = false;
    document.getElementById('ns_input_add_add_discpersen1').disabled = false;
    document.getElementById('ns_input_add_add_discpersen2').disabled = false;
    document.getElementById('ns_input_add_add_discpersen3').disabled = false;
  }


}

function ns_buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickPWO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.no_bukti}</td>
          <td>${item.tanggal ? ns_formatDate(item.tanggal).split('-').reverse().join('/') : ''}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.qty}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_pwo").innerHTML = rowTable

      document.getElementById("ns_namaHeaderTable").textContent = 'Nomor Penawaran PO'

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListPWO').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function ns_buttonAddAddPickBarangFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(ns_dataAddAddListItem[index])
  ns_tempAddAdd = ns_dataAddAddListItem[index]

  ns_cekSatuanBarang(ns_tempAddAdd.Kodebrg)

  document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddAdd.Kodebrg
  ns_kosongkanCosting()
  document.getElementById("ns_input_add_add_namabarang").value = ''
  document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddAdd.NamaBrg
  document.getElementById("ns_input_add_add_noPPL").value = ''
  document.getElementById("ns_input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("ns_input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (ns_tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${ns_tempSatuanBarang[0].SAT1}</option>`
  }
  if (ns_tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${ns_tempSatuanBarang[0].SAT2}</option>`
  }
  if (ns_tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${ns_tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : ns_tempAddAdd.Kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td>-</td>
          <td>${item.SATUAN}</td>
          <td>-</td>
          <td>-</td>
          <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
          <td>-</td>
          <td>-</td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("ns_input_add_add_harga").value = parseFloat(res[0].Xharga).toFixed(2)
      } else {
        if (Number(ns_tempAddAdd.Hrg1_1)) {
          document.getElementById("ns_input_add_add_harga").value = parseFloat(ns_tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("ns_input_add_add_harga").value = '0.00'
        }
      }

      ns_buttonAddListBatal()
      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function ns_buttonAddListNoSO () {

  let _token = $("#_token").val();

  $('#ns_tabel_add_list_noSo').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistnoso') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="ns_buttonAddPickNoSO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          </tr>`

      ns_listNoSo = res

      ns_listNoSo.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="ns_buttonAddPickNoSO('${item.NOBUKTI}' , '${item.NoPesanan}')" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>${item.NOBUKTI}</td>
          <td>${item.Tanggal ? ns_formatDate(item.Tanggal).split('-').reverse().join('/') : ''}</td>
          <td>${item.NoPesanan}</td>
          </tr>`
      });

      document.getElementById("ns_tabel_data_add_list_noSo").innerHTML = rowTable
      $("#ns_tabel_add_list_noSo").DataTable({
        "lengthChange": false,
        "paging": true,
      });
      document.getElementById("ns_namaHeaderTable").textContent = 'Nomor SO'
      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListNoSo').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function ns_buttonAddPickNoSO (kode, nama) {
  console.log('ns_buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  ns_buttonAddListBatal()
}

function ns_buttonAddPickPWO (kode, nama) {
  console.log('ns_buttonAddPickPWO')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KodePF' , kode)
  }
  document.getElementById("ns_input_add_kodepic").value = kode
  document.getElementById("ns_input_add_namapic").value = nama
  ns_buttonAddListBatal()
}

function ns_onChangeDP () {
  console.log('ns_onChangeDP')
  if (ns_tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    ns_onChangeHeader('DP' , value)
    ns_refreshUpdateHeader()
    let nobukti = $("#ns_input_add_nobukti").val()
    ns_refreshDataTableAdd(nobukti)
  }


}

function ns_onChangeNoPO () {
  if (ns_tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    ns_onChangeHeader('NoPesanan' , value)
  }
}

function ns_showTableStockProyeksi () {
  if ($("#ns_divhargaterakhir").is(':visible')) 
  {
    $('#ns_divhargaterakhir').hide();
  }
  
  if (!$("#ns_divStockProyeksi").is(':visible'))
  {
    $('#ns_divStockProyeksi').show();
  } else 
  {
    $('#ns_divStockProyeksi').hide();
  }
}



function ns_buttonAddEditItem (i) {


  ns_tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.ns_showhide').hide();
  // ns_cleanFormAddAdd()
  console.log(ns_dataTableAdd[i])
  ns_tempAddEdit = ns_dataTableAdd[i]

  if (ns_tempAddEdit.NoPPL != null){
    ns_noBuktiUntukAdd = ns_tempAddEdit.NoPPL
  }

  if (ns_tempAddEdit.NoPPL == null){
    ns_noBuktiUntukAdd = 0
  }

  let selectOption = ''
  if (ns_tempAddEdit.Satuan) {
    selectOption += `<option value=1 selected>${ns_tempAddEdit.Satuan}</option>`
  }

  // Sumber barang saat edit ikut nilai NoPPL yang tersimpan - ada isinya berarti dulu
  // diambil dari PR, kosong berarti dari master barang (Non PR). Dropdown + kode barang
  // dikunci selama edit lewat ns_poKunciIdentitasBarang(), sumber barang tidak bisa ditukar
  // di tengah edit item yang sudah tersimpan.
  document.getElementById("ns_input_add_add_outstanding").value = ns_tempAddEdit.NoPPL ? 'PR' : 'NONPR'
  ns_poKunciIdentitasBarang(true)
  if (ns_tempAddEdit.NoPNW == '' || ns_tempAddEdit.NoPNW == null) {
    ns_tempAddEdit.NoPNW = '-'
  }
  document.getElementById("ns_input_add_add_nopnwpo").value = ns_tempAddEdit.NoPNW
  document.getElementById("input_add_add_costing").value = ns_tempAddEdit.Cost ? ns_tempAddEdit.Cost : ''
  document.getElementById("input_add_add_subcosting").value = ns_tempAddEdit.subcost ? ns_tempAddEdit.subcost : ''
  document.getElementById("input_add_add_satuanAlias").value = ns_tempAddEdit.SatX ? ns_tempAddEdit.SatX : ''
  $('#ns_divStockProyeksi').hide();

  document.getElementById("ns_input_add_add_foc").value = ns_tempAddEdit.PFOC
  document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddEdit.KodeBrg
  document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddEdit.NamaBrgAsli
  document.getElementById("ns_input_add_add_namabarang").value = ns_tempAddEdit.NamaAlias
  document.getElementById("ns_input_add_add_discpersen1").value = Number(ns_tempAddEdit.DiscP1) ?  ns_tempAddEdit.DiscP1 : '0.00'
  document.getElementById("ns_input_add_add_discpersen2").value = Number(ns_tempAddEdit.Discp2) ?  ns_tempAddEdit.Discp2 : '0.00'
  document.getElementById("ns_input_add_add_discpersen3").value = Number(ns_tempAddEdit.Discp3) ?  ns_tempAddEdit.Discp3 : '0.00'
  document.getElementById("ns_input_add_add_qty").value = ns_formatAngka(parseFloat(ns_tempAddEdit.Qnt).toFixed(2))
  document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption
  document.getElementById("ns_input_add_add_harga").value = Number(ns_tempAddEdit.Harga) ? ns_formatAngka(parseFloat(ns_tempAddEdit.Harga).toFixed(2)) : '0.00'
  document.getElementById("ns_input_add_add_discrp").value = Number(ns_tempAddEdit.DISCTOT) ? ns_formatAngka(parseFloat(ns_tempAddEdit.DISCTOT).toFixed(2)) : '0.00'

  document.getElementById("ns_input_add_add_noPPL").value = ns_tempAddEdit.NoPPL
  document.getElementById("ns_input_add_add_urutPPL").value = ns_tempAddEdit.UrutPPL

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : ns_tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${ns_formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $('#ns_divhargaterakhir').hide();
  $('#ns_h4AddAddItem').hide();
  $('#ns_h4AddEditItem').show();
  $('#ns_submitAddAdd').hide();
  $('#ns_submitAddEdit').show();
  $('#ns_addAddItem').show();

  document.getElementById("ns_input_add_add_namabarang").scrollIntoView();
}

function ns_closeShowHideAdd () {
  $('.ns_showhide').hide();

}

function ns_setNewNoBukti (tipePpn) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'POB',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("ns_input_add_nobukti").value = res[0].Nobukti
      document.getElementById("ns_input_add_nourut").value = res[0].Nourut

    }})

}


function ns_buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#ns_input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickPIC('${item.kodepic}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_pic").innerHTML = rowTable

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListPIC').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

// Sumber barang untuk "+ Tambah Item": 'PR' (PR Non Stock, default) atau 'NONPR'
// (langsung dari master barang jasa, tanpa hubungan ke PR).
function ns_poSumberBarang () {
  let el = document.getElementById('ns_input_add_add_outstanding')
  return el ? el.value : 'PR'
}

function ns_onChangeOutstanding () {
  ns_cleanFormAddAdd()
}

// Mengunci/membuka identitas barang (sumber + kode + tombol browse) - dikunci saat
// mengedit item yang sudah tersimpan (ns_tipeformitem == 'edit'), supaya barang yang sudah
// dipilih tidak diam-diam ditukar sumbernya. Sama seperti purchaseOrder.blade.php.
function ns_poKunciIdentitasBarang (kunci) {
  document.getElementById('ns_input_add_add_outstanding').disabled = kunci
  document.getElementById('ns_input_add_add_kodebarang').disabled = kunci
  document.getElementById('ns_buttonBrowseBarangItem').disabled = kunci
}

// Cari indeks baris di ns_dataAddAddListItem yang cocok dengan keyword yang diketik user di
// kolom kode barang. Dua tahap: kode barang PERSIS dulu, baru substring kode/nama -
// supaya kode lengkap yang kebetulan jadi bagian nama barang lain tidak membatalkan
// auto-pick.
function ns_poCariIndeksBarang (keyword, fieldKode, fieldNama) {
  let k = String(keyword).trim().toLowerCase()
  if (!k) { return [] }

  let ns_ambil = (item, f) => String((item[f] === null || item[f] === undefined) ? '' : item[f]).toLowerCase()
  let persis = []
  let mirip = []

  ns_dataAddAddListItem.forEach((item, i) => {
    if (ns_ambil(item, fieldKode) === k) { persis.push(i) }
    if (ns_ambil(item, fieldKode).includes(k) || ns_ambil(item, fieldNama).includes(k)) { mirip.push(i) }
  })

  return persis.length ? persis : mirip
}

// Dipakai setelah auto-pick (hasil pencarian persis satu barang): ns_form item sudah terisi
// otomatis, jadi kursor langsung dilempar ke Qty - mengikuti pola buttonAddAddInsertItem
// di pembelianpermintaanagen/nonagen. Diberi jeda karena fungsi pick diakhiri
// scrollIntoView().
function ns_poFokusQty () {
  setTimeout(() => {
    let el = document.getElementById("ns_input_add_add_qty")
    if (el && !el.disabled) { el.focus(); el.select() }
  }, 300)
}

// opsi.keyword  : kata kunci dari kolom kode barang (kode maupun nama barang)
// opsi.autoPick : kalau hasil pencarian PERSIS satu baris, barang itu langsung dipilih
//                 dan modal tidak usah dibuka sama sekali. Hanya diaktifkan oleh Enter,
//                 bukan oleh tombol browse.
// Default {} membuat pemanggil lama berperilaku persis seperti sebelumnya.
function ns_buttonAddAddListBarang (opsi = {}) {
  let _token = $("#_token").val();
  let sumber = ns_poSumberBarang()
  let keyword = opsi.keyword ? String(opsi.keyword).trim() : ''
  let autoPick = !!opsi.autoPick

  if (sumber === 'NONPR') {

    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_jasa')) { $('#tabel_add_list_barang_jasa').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('polistbarangjasa') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        ns_dataAddAddListItem = res

        // Hasil pencarian persis satu barang -> langsung dipilih, modal tidak dibuka.
        if (autoPick && keyword) {
          let cocok = ns_poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            buttonAddAddPickBarangJasa(cocok[0])
            ns_poFokusQty()
            return
          }
        }

        ns_dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="buttonAddAddPickBarangJasa(${i})">
            <td style="white-space:nowrap;">${item.KodeBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaMerk || ''}</td>
            <td style="white-space:nowrap;">${item.PartNumber || ''}</td>
            <td style="white-space:nowrap;">${item.Stock || ''}</td>
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_jasa").innerHTML = rowTable

        pickerKasInit('tabel_add_list_barang_jasa')

        // Kata kunci yang diketik user ikut terbawa ke kotak cari bawaan DataTables.
        if (keyword) { $("#tabel_add_list_barang_jasa").DataTable().search(keyword).draw() }

        document.getElementById("ns_namaHeaderTable").textContent = 'Barang (Non PR)'
        $('.ns_showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangJasa').show();

        $("#ns_form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  } else {

    // Apa pun nilai ns_noBuktiUntukAdd, selalu tampilkan SELURUH barang PR Non Stock yang
    // masih outstanding lewat ponslistbarangjasaall (PONonStockController@listBarangJasaAll,
    // sumbernya vwOutPPL - sama seperti data tab Outstanding PR), dirender ke panel
    // #ns_modalBodyAddAddListBarangNonFOC yang kolomnya sudah cocok (Sat/Qnt PR/Qnt PO/Sisa
    // PR/No. PR/No. SO Cust), dengan ns_buttonAddAddPickBarangNonFOC() yang mengisi
    // NoPPL/UrutPPL dari baris yang dipilih.
    if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_barang_nonfoc')) { $('#ns_tabel_add_list_barang_nonfoc').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('ponslistbarangjasaall') !!}",
      type: "post",
      async: false,
      data: {
        _token
      },
      success: function(res) {
        let rowTable = ``
        ns_dataAddAddListItem = res

        // Auto-pick hanya kalau hasilnya benar-benar satu baris. Kalau satu kode barang
        // punya beberapa baris PR (beda No. PR / beda satuan), modal tetap dibuka supaya
        // user memilih sendiri No. PR mana yang dipakai.
        if (autoPick && keyword) {
          let cocok = ns_poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            ns_buttonAddAddPickBarangNonFOC(cocok[0])
            ns_poFokusQty()
            return
          }
        }

        ns_dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="ns_buttonAddAddPickBarangNonFOC(${i})">
            <td>${item.KodeBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaBrg || ''}</td>
            <td>${item.PartNumber || ''}</td>
            <td>${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td>${item.Sat || ''}</td>
            <td>${item.Qnt ? ns_formatAngka(item.Qnt) : ''}</td>
            <td>${item.QntPO ? ns_formatAngka(item.QntPO) : ''}</td>
            <td>${item.SisaPPL || ''}</td>
            <td>${item.NoBukti || ''}</td>
            <td>${item.NosoCust ? item.NosoCust : ''}</td>
          </tr>`
        });

        document.getElementById("ns_tabel_data_add_list_barang_nonfoc").innerHTML = rowTable

        pickerKasInit('ns_tabel_add_list_barang_nonfoc')

        if (keyword) { $("#ns_tabel_add_list_barang_nonfoc").DataTable().search(keyword).draw() }

        document.getElementById("ns_namaHeaderTable").textContent = 'Barang dari PR'
        $('.ns_showhidemodalbodyadd').hide();
        $('#ns_modalBodyAddAddListBarangNonFOC').show();

        $("#ns_form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
}

// Valas jadi dropdown (bukan lagi picker + tombol +), disamakan dengan purchaseOrder.blade.php.
// Dimuat sekali saat halaman siap - lihat pemanggilan di $(document).ready().
let ns_listValas = []

function ns_muatDropdownValas () {
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      ns_listValas = res
      let selectEl = document.getElementById("ns_input_add_valas")
      let kodeTerpilih = selectEl.value
      selectEl.innerHTML = ''
      ns_listValas.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.kodevls
        opt.textContent = `${item.kodevls} - ${item.namavls}`
        selectEl.appendChild(opt)
      });
      if (ns_listValas.some(item => item.kodevls === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar valas')
    }
  })
}

function ns_onChangeValas () {
  let kode = document.getElementById("ns_input_add_valas").value
  let itemX = ns_listValas.find(item => item.kodevls === kode)
  let kurs = itemX && itemX.kurs ? parseFloat(itemX.kurs).toFixed(2) : '0.00'
  document.getElementById("ns_input_add_kurs").value = kurs
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KODEVLS', kode)
    ns_onChangeHeader('KURS', kurs)
  }
}

// Dikirim Ke jadi dropdown (bukan lagi picker + tombol +), sumbernya tetap DBGUDANG lewat
// polistgudang - lihat PONonStockController@index catatan yang sama di purchaseOrder.blade.php.
function ns_muatDropdownAlamatKirim () {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      ns_listAlamatKirim = res
      let selectEl = document.getElementById("ns_input_add_kodealamatkirim")
      let kodeTerpilih = selectEl.value
      selectEl.innerHTML = ''
      ns_listAlamatKirim.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.KODEGDG
        opt.textContent = `${item.KODEGDG} - ${item.NAMA}`
        selectEl.appendChild(opt)
      });
      if (ns_listAlamatKirim.some(item => item.KODEGDG === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
      ns_onChangeKodeAlamatKirim()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar gudang')
    }
  })
}

function ns_onChangeKodeAlamatKirim () {
  let kode = document.getElementById("ns_input_add_kodealamatkirim").value
  let itemX = ns_listAlamatKirim.find(item => item.KODEGDG === kode)
  let alamat = itemX ? itemX.Alamat : ''
  document.getElementById("ns_input_add_alamatkirim").value = alamat
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('NoAlamatKirim', kode)
    ns_onChangeHeader('AlamatKirim', alamat)
  }
}

function ns_buttonAddListGudang () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_alamatkirim')) { $('#ns_tabel_add_list_alamatkirim').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``

      ns_listAlamatKirim = res

      ns_listAlamatKirim.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="ns_buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KODEGDG}</td>
        <td>${item.NAMA}</td>
        <td>${item.Alamat}</td>
        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_alamatkirim").innerHTML = rowTable
      $("#ns_tabel_add_list_alamatkirim").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("ns_namaHeaderTable").textContent = 'Dikirim Ke'

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListAlamatKirim').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function ns_buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_lokasipenerima')) { $('#ns_tabel_add_list_lokasipenerima').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `<tr class="pick-row" onclick="ns_buttonAddPickLokasiPenerima('-' , '-' )">
        <td>-</td>
        <td>-</td>
        </tr>`

      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="ns_buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )">
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("ns_tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      pickerKasInit('ns_tabel_add_list_lokasipenerima')

      document.getElementById("ns_namaHeaderTable").textContent = 'Ekspedisi'

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListLokasiPenerima').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function ns_buttonAddListValas () {

  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_valas')) { $('#ns_tabel_add_list_valas').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>${item.kodevls}</td>
          <td>${item.namavls}</td>
          <td>${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_valas").innerHTML = rowTable
      $("#ns_tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("ns_namaHeaderTable").textContent = 'Valas'

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListValas').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function ns_buttonAddListPelanggan ()
{
  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_pelanggan')) { $('#ns_tabel_add_list_pelanggan').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistpelanggan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="ns_buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARI}', '${item.PPN}')">
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        </tr>`
      });

      document.getElementById("ns_tabel_data_add_list_pelanggan").innerHTML = rowTable
      pickerKasInit('ns_tabel_add_list_pelanggan')

      document.getElementById("ns_namaHeaderTable").textContent = 'Supplier'

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListPelanggan').show();
      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

// Kotak Kode Supplier sekarang bisa diketik langsung (sama seperti purchaseOrder.blade.php)
// - tombol + / Enter membuka daftar supplier lalu menyaringnya dengan kata yang sudah diketik.
function ns_performSearchSupplier () {
  const searchValue = document.getElementById('ns_input_add_kodesupplier').value.trim();

  ns_buttonAddListPelanggan();

  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_pelanggan')) { $('#ns_tabel_add_list_pelanggan').DataTable().search(searchValue).draw(); }
}

document.getElementById('ns_input_add_kodesupplier').addEventListener('keypress', function (event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    $('#ns_po_dropdown_supplier').hide();
    ns_performSearchSupplier();
  }
});

// Supplier bisa diketik (saran 10 teratas) - helper poPasangKetikSupplier ada di
// purchaseOrder.blade.php. Argumen sama dengan onclick baris modal Supplier Non Stock (HARI).
$(function () {
  poPasangKetikSupplier('ns_input_add_kodesupplier', 'ns_po_dropdown_supplier', function (s) {
    ns_buttonAddPickPelanggan(String(s.KodeCustSupp), String(s.NamaCustSupp), String(s.Alamat), String(s.HARI), String(s.PPN))
  })
})

function buttonAddListCosting ()
{

  let _token  = $("#_token").val()
  // Costing disaring dari COA Biaya barang yang dipilih (bukan lagi Perkiraan header).
  let kodebrg = document.getElementById("ns_input_add_add_kodebarang").value

  if (!kodebrg) {
    alertify.warning('Pilih barang terlebih dahulu')
    return
  }

  if ($.fn.DataTable.isDataTable('#tabel_add_list_costing')) { $('#tabel_add_list_costing').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistcosting') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCosting('${item.KodeCost}')">
        <td>${item.KodeCost}</td>
        <td>${item.NamaCost}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_costing").innerHTML = rowTable
      pickerKasInit('tabel_add_list_costing')

      document.getElementById("ns_namaHeaderTable").textContent = 'Costing'

      $('.ns_showhidemodalbodyadd').hide();
      $('#modalBodyAddListCosting').show();
      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListSubCosting ()
{

  let _token  = $("#_token").val()
  kodeCost = document.getElementById("input_add_add_costing").value

  if ($.fn.DataTable.isDataTable('#tabel_add_list_subcosting')) { $('#tabel_add_list_subcosting').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistsubcosting') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCost
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickSubCosting('${item.KodeSubCost}')">
        <td>${item.KodeSubCost}</td>
        <td>${item.NamaSubCost}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_subcosting").innerHTML = rowTable
      pickerKasInit('tabel_add_list_subcosting')

      document.getElementById("ns_namaHeaderTable").textContent = 'Sub-Costing'

      $('.ns_showhidemodalbodyadd').hide();
      $('#modalBodyAddListSubCosting').show();
      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListPerkiraan ()
{
  if ($.fn.DataTable.isDataTable('#tabel_add_list_perkiraan')) { $('#tabel_add_list_perkiraan').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistperkiraan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPerkiraan('${item.Perkiraan}')">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable
      pickerKasInit('tabel_add_list_perkiraan')

      document.getElementById("ns_namaHeaderTable").textContent = 'Perkiraan'

      $('.ns_showhidemodalbodyadd').hide();
      $('#modalBodyAddListPerkiraan').show();
      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function ns_buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListBackOffice').show();

      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function ns_buttonAddListSales () {
  if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_sales')) { $('#ns_tabel_add_list_sales').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_list_sales").innerHTML = rowTable
      $("#ns_tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.ns_showhidemodalbodyadd').hide();
      $('#ns_modalBodyAddListSales').show();
      $("#ns_form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function ns_onChangeInputAddAddNosat () {
  console.log('ns_onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#ns_input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#ns_input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL ? ns_formatDate(item.TANGGAL).split('-').reverse().join('/') : ''}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td>${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="ns_buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("ns_tabel_data_add_list_sales").innerHTML = rowTable

      if (ns_tipeformitem == 'add') {
        console.log(ns_tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("ns_input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (ns_tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("ns_input_add_add_harga").value = ns_tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("ns_input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("ns_input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (ns_tempAddAdd.Hrg1_1) {
      //       document.getElementById("ns_input_add_add_harga").value = ns_tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("ns_input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (ns_tempAddAdd.Hrg2_1) {
      //       document.getElementById("ns_input_add_add_harga").value = ns_tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("ns_input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (ns_tempAddAdd.Hrg3_1) {
      //       document.getElementById("ns_input_add_add_harga").value = ns_tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("ns_input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

/* ============ Header ns_tabel interaktif (window.ReportTable) ============
 * Diporting dari purchasing/purchaseOrder.blade.php - dokumentasi lengkap pola ini ada
 * di sana, komentar di sini hanya menandai bagian yang berbeda. Halaman ini punya DUA
 * ns_tabel: urut 1 = Outstanding PR (server-side paging, informasi saja - tanpa kolom
 * Actions, lihat memory tab-outstanding-pr-hanya-informasi), urut 2 = Purchase Order
 * (ditarik sekaligus per rentang periode, difilter Otorisasi di browser - kolom Status
 * sengaja belum dikerjakan). Tidak ada urut 3 (Outstanding SO) seperti di
 * purchaseOrder.blade.php.
 */

const PONS_HREF = 'pononstock'
const PONS_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date' }

const PONS_OUT = {
  1 : {
    tabel  : 'ns_tabel',
    thead  : 'ns_tabel_header',
    tbody  : 'ns_tabel_data',
    search : 'ponsSearch1',
    len    : 'ponsLen1',
    url    : "{!! url('ponsdataoutstandingpr') !!}",
    nama   : 'Outstanding PR'
  }
}

let ponsCacheOut = { 1 : null }
let ponsPakaiCacheOut = { 1 : false }

let ponsCart = { 1 : [], 2 : [] }
let ponsActiveUrut = 0
let ponsPerluGambar = { 1 : false, 2 : false }
let ponsPanjangHalaman = { 1 : 10, 2 : 10 }

function ponsUrutTabAktif () {
  return $('#ns_nav-profile-tab').hasClass('active') ? 2 : 1
}

const PONS_SELEKTOR_TABEL_AKTIF = '#ns_myTabContent .tab-pane.active table.data-table'

// labelQty: judul kolom "Qnt..." ditampilkan sebagai "Qty..." (hanya tab outstanding;
// nama field datanya tetap, yang berubah hanya judul yang terlihat user).
function ponsBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered, labelQty) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    if (labelQty) {
      label = String(label).replace(/qnt/gi, 'Qty')
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      PONS_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function ponsKolomTampil (urut) {
  return (ponsCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function ponsKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function ponsAktifkanTabel (urut) {
  ponsActiveUrut = urut
  window.ns_g_modeReport = urut
  window.ns_gcart_header = ponsCart[urut]
}

function ponsOnChangeAktif () {
  if (ponsActiveUrut === 2) {
    ns_renderTabelPO(true)
  } else {
    initTabelOutstandingPons(ponsActiveUrut, true)
  }
}

function ponsHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let ponsRtSudahInit = false

function ponsInitReportTableSekali () {
  if (ponsRtSudahInit || typeof ReportTable === 'undefined') { return }
  ponsRtSudahInit = true

  let urutAktif = ponsUrutTabAktif()
  let idTabel = { 1 : '#ns_tabel', 2 : '#ns_tabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({
      table    : idTabel[u],
      onChange : ponsOnChangeAktif
    })
  });

  ReportTable.init({
    table    : PONS_SELEKTOR_TABEL_AKTIF,
    bar      : '#ns_rtBar',
    onChange : ponsOnChangeAktif
  })

  // Guard klik roda gigi vs sort DataTables - lihat catatan lengkap di
  // purchaseOrder.blade.php (poInitReportTableSekali).
  let ponsGuardUlangKlik = false
  let idThead = ['ns_tabel_header', 'ns_tabel2_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (ponsGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      ponsGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      ponsGuardUlangKlik = false
    }, true)
  });
}

function ponsPindahBar (urut) {
  let bar = document.getElementById('ns_rtBar')
  let id = urut === 2 ? 'ns_tabel2' : PONS_OUT[urut].tabel
  let ns_tabel = document.getElementById(id)
  if (!bar || !ns_tabel) { return }

  let acuan = ns_tabel
  if ($.fn.DataTable.isDataTable('#' + id)) {
    acuan = document.getElementById(id + '_wrapper') || ns_tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

let ponsTimerSearch = { 1 : null, 2 : null }
function ponsIkatSearch (urut) {
  let input = document.getElementById(urut === 2 ? 'ponsSearch2' : PONS_OUT[urut].search)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let nilai = input.value
    let idTabel = urut === 2 ? 'ns_tabel2' : PONS_OUT[urut].tabel
    // Kedua ns_tabel server-side - jangan tembak server tiap ketukan. Guard isDataTable()
    // - tanpa ini, .DataTable() pada ns_tabel yang baru saja di-destroy() (mis. saat
    // berpindah tab persis ketika event ini menyala) diam-diam membuat instance
    // DataTable baru dengan opsi bawaan, bukan error.
    if (ponsTimerSearch[urut]) { clearTimeout(ponsTimerSearch[urut]) }
    ponsTimerSearch[urut] = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) {
        $('#' + idTabel).DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function ponsIkatPanjangHalaman (urut) {
  let sel = document.getElementById(urut === 2 ? 'ponsLen2' : PONS_OUT[urut].len)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(ponsPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    ponsPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    let idTabel = urut === 2 ? 'ns_tabel2' : PONS_OUT[urut].tabel
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(ponsPanjangHalaman[urut]).draw()
    }
  })
}

function ponsIkatPeriode () {
  let awal  = document.getElementById('ponsTglAwal')
  let akhir = document.getElementById('ponsTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let ns_onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    ns_loadTabelPO(true, true)
  }

  awal.addEventListener('change', ns_onUbah)
  akhir.addEventListener('change', ns_onUbah)
}

function ponsAturTinggiTabel () {
  let area = document.getElementById('content')
  let pane = document.querySelector('#ns_myTabContent .tab-pane.active')
  if (!area || !pane) { return }
  let wrap = pane.querySelector('.po-table-wrap')
  if (!wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

window.ns_g_href = PONS_HREF
window.ns_g_modeReport = 1
window.ns_gcart_header = []

function ponsUrutSah (mode) {
  let urut = Number(mode)
  return urut === 2 ? 2 : 1
}

window.ns_doSimpanHeader = function (href, mode) {
  let urut = ponsUrutSah(mode)
  let cart = ponsCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : PONS_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.ns_doSetHeader = function (mode, reset) {
  if (!reset) { return }
  let urut = ponsUrutSah(mode)

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : PONS_HREF,
      urut   : urut,
      reset  : 1
    },
    success : function (res) {
      if (urut === 2) {
        ponsCart[2] = ponsBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2)
      } else {
        ponsCart[1] = ponsBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, true)
      }
      window.ns_gcart_header = ponsCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function ponsFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function ponsRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return ponsFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? ns_formatDate(nilai).split('-').reverse().join('/') : ""
  }
  // Tanggal yang datang sebagai teks 'yyyy-mm-dd...' (mis. hasil CONVERT di SQL) juga tampil dd/mm/yyyy.
  if (typeof nilai === 'string' && /^\d{4}-\d{2}-\d{2}/.test(nilai)) {
    return nilai.substr(0, 10).split('-').reverse().join('/')
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Menggambar ns_tabel "Outstanding PR" (urut 1) - server-side paging, tanpa kolom Actions.
function initTabelOutstandingPons (urut, pakaiCache) {
  if (!ponsMetaSudah) { return }
  let cfg = PONS_OUT[urut]
  if (!cfg) { return }
  let selTabel = '#' + cfg.tabel
  ponsAktifkanTabel(urut)

  let posisi = null
  if ($.fn.DataTable.isDataTable(selTabel)) {
    let dtLama = $(selTabel).DataTable()
    posisi = {
      start  : dtLama.page.info().start,
      search : dtLama.search(),
      order  : dtLama.order()
    }
    dtLama.destroy()
  }
  document.getElementById(cfg.tbody).innerHTML = ""

  let cols = ponsKolomTampil(urut)
  let kolomRender = cols.map(ponsKolomRender)

  let thead = document.getElementById(cfg.thead)
  thead.innerHTML = ponsHeadHtml(cols)

  let columns = []
  kolomRender.forEach((c) => {
    columns.push({
      data : null,
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, row) {
        return ponsRenderNilai(c, row)
      }
    })
  });

  ponsPakaiCacheOut[urut] = !!(pakaiCache && ponsCacheOut[urut])

  if (!columns.length) {
    thead.innerHTML = '<tr><th style="padding: 4px 12px;" scope="col">' + cfg.nama + '</th></tr>'
    document.getElementById(cfg.tbody).innerHTML =
      '<tr><td class="text-center" style="padding: 14px;">Belum ada data untuk ditampilkan</td></tr>'
    return
  }

  let orderAman = posisi ? posisi.order.filter((o) => o[0] < columns.length) : []

  $(selTabel).DataTable({
    "processing" : true,
    "language" : {
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>'
    },
    "serverSide" : true,
    "lengthChange" : false,
    "pageLength" : ponsPanjangHalaman[urut],
    "searchDelay" : 400,
    "dom" : "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "order" : orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : posisi ? { "search" : posisi.search } : { "search" : "" },
    "columns" : columns,
    "ajax" : function (data, callback, settings) {
      if (ponsPakaiCacheOut[urut] && ponsCacheOut[urut]) {
        ponsPakaiCacheOut[urut] = false
        callback(Object.assign({}, ponsCacheOut[urut], { draw : data.draw }))
        return
      }

      let kolom = null
      let arah = 'asc'
      if (data.order && data.order.length) {
        let c = kolomRender[data.order[0].column]
        if (c) {
          kolom = c.field
          arah = data.order[0].dir
        }
      }

      $.ajax({
        url : cfg.url,
        type : "get",
        dataType : "json",
        data : {
          draw : data.draw,
          start : data.start,
          length : data.length,
          search : data.search ? data.search.value : '',
          orderCol : kolom,
          orderDir : arah
        },
        success : function (res) {
          // Kalau sesi login habis / server balas HTML (bukan JSON), "dataType: json"
          // di atas membuat jQuery melempar parsererror - masuk ke error: di bawah,
          // bukan diam-diam menutup overlay lewat callback(string). Tanpa itu,
          // callback(res) di sini akan melempar TypeError dan overlay "Memuat data..."
          // tidak pernah ditutup (lihat juga classlist card di bawah init DataTable).
          ponsCacheOut[urut] = res
          callback(res)
        },
        error : function (err) {
          console.log(cfg.url + ' gagal:', err.status, err.responseText)
          alertify.warning('Gagal memuat data ' + cfg.nama)
          callback({ draw : data.draw, data : [], recordsTotal : 0, recordsFiltered : 0 })
        }
      })
    },
    "drawCallback" : function () {
      setTimeout(ponsAturTinggiTabel, 0)
      ponsKpiOut[urut] = this.api().page.info().recordsTotal
      renderKpiPONS()
    }
  });

  // Integrasi Bootstrap-4 DataTables memberi elemen "Memuat data..." kelas tambahan
  // "card" (lihat public/js/datatables.min.js, sProcessing). Halaman ini punya aturan
  // "#page1 .card { display:block !important }" untuk mematikan gaya kartu dashboard -
  // itu MENGALAHKAN inline "display:none" yang dipasang DataTables saat proses selesai,
  // jadi overlay-nya kelihatan menyala terus walau datanya sudah tergambar. Membuang
  // kelas "card" di sini menghilangkan pertentangan itu (pola sama dengan
  // purchaseOrder.blade.php - poInitTabelOutstanding).
  let ponsElMemuat = document.querySelector('#' + cfg.tabel + '_wrapper > .dataTables_processing')
  if (ponsElMemuat) { ponsElMemuat.classList.remove('card') }

  ponsIkatSearch(urut)
  ponsIkatPanjangHalaman(urut)
  let inputSearch = document.getElementById(cfg.search)
  if (inputSearch) { inputSearch.value = posisi ? posisi.search : '' }
  ponsAturTinggiTabel()
}

// Filter Status & Otorisasi tab "Purchase Order" - client-side, mengikuti pola purchaseOrder.blade.php.
function ponsOtorisasiPO (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function ponsAngka (v) {
  let n = Number(v)
  return isNaN(n) ? 0 : n
}

function ponsStatusPO (item) {
  let qnt   = ponsAngka(item.qnt)
  let qbeli = ponsAngka(item.qntbeli)
  if (qnt === 0)    { return 'Batal' }
  if (qbeli === 0)  { return 'Belum' }
  if (qbeli < qnt)  { return 'Sebagian' }
  return 'Sudah'
}

const PONS_BADGE_STATUS = {
  'Sudah'    : 'is-active',
  'Belum'    : 'is-user',
  'Sebagian' : 'is-supervisor',
  'Batal'    : 'is-inactive'
}

function ponsBadgeStatus (item) {
  let status = ponsStatusPO(item)
  let kelas = PONS_BADGE_STATUS[status] || ''
  return `<span class="sp-badge ${kelas}">${status}</span>`
}

let ponsFilterStatus = 'SEMUA'
let ponsFilterOtorisasi = 'SEMUA'

function ponsUpdateFilterBadge () {
  let jml = 0
  if (ponsFilterStatus !== 'SEMUA') { jml++ }
  if (ponsFilterOtorisasi !== 'SEMUA') { jml++ }
  let badge = document.getElementById('ponsFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function ponsTerapkanFilter () {
  ponsFilterStatus = $('#ponsModalStatus').val() || 'SEMUA'
  ponsFilterOtorisasi = $('#ponsModalOtorisasi').val() || 'SEMUA'
  ponsUpdateFilterBadge()
  $('#modalFilterPONS').modal('hide')
  ns_loadTabelPO(true, true)
}

function ponsResetFilter () {
  ponsFilterStatus = 'SEMUA'
  ponsFilterOtorisasi = 'SEMUA'
  $('#ponsModalStatus').val('SEMUA')
  $('#ponsModalOtorisasi').val('SEMUA')
  ponsUpdateFilterBadge()
  $('#modalFilterPONS').modal('hide')
  ns_loadTabelPO(true, true)
}

let ponsTab2Sudahdimuat = false

// ---- Tab "Purchase Order" (urut 2): server-side paging ----
// Dulu seluruh PO dalam periode ditarik sekaligus lalu dipaging di browser - periode yang
// dimundurkan (ribuan PO) lambat sekali. Sekarang tiap halaman diminta ke server
// (POController::daftarPO, pJasa = 1), sama seperti purchaseOrder.blade.php: filter
// Status/Otorisasi, kotak cari, urutan kolom, dan angka KPI ikut dihitung di server.
let ponsCachePO = null
let ponsPakaiCachePO = false
let ponsKpiPO = null
let ponsDrawPO = 0

// keHalamanAwal = true saat periode/filter berubah; selain itu (habis simpan/otorisasi)
// tetap di halaman yang sama.
function ns_loadTabelPO (paksa, keHalamanAwal) {
  // Konfigurasi kolom belum selesai diambil (halaman baru dibuka) - ns_loadAll() sendiri
  // yang akan menggambar tab aktif begitu konfigurasinya datang.
  if (!ponsMetaSudah) { return }
  if (ponsTab2Sudahdimuat && !paksa) { return }
  ponsTab2Sudahdimuat = true
  if ($.fn.DataTable.isDataTable('#ns_tabel2')) {
    ponsCachePO = null
    ponsPakaiCachePO = false
    $('#ns_tabel2').DataTable().ajax.reload(null, !!keHalamanAwal)
    return
  }
  ns_renderTabelPO()
}

// Isi sel kolom otorisasi/batal - HTML-nya sama persis dengan versi sebelum server-side.
function ponsSelCentang (nyala) {
  return nyala
    ? '<i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i>'
    : '<i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i>'
}

function ponsSelTanggal (nilai) {
  return nilai ? ns_formatDate(nilai).split('-').reverse().join('/') : ''
}

// pakaiCache = true dipanggil dari ponsOnChangeAktif() saat kolom digeser/disembunyikan -
// datanya tidak berubah, jadi respons terakhir dipakai lagi tanpa menembak server.
function ns_renderTabelPO (pakaiCache) {
  let ns_level = $("#ns_level").val()

  ponsAktifkanTabel(2)

  let posisi = null
  if ($.fn.DataTable.isDataTable('#ns_tabel2')) {
    let dtLama = $('#ns_tabel2').DataTable()
    posisi = {
      start  : dtLama.page.info().start,
      search : dtLama.search(),
      order  : dtLama.order()
    }
    $('#ns_tabel2_data').find('[data-toggle="tooltip"]').tooltip('dispose')
    dtLama.destroy()
  }
  document.getElementById("ns_tabel2_data").innerHTML = ""

  let cols2 = ponsKolomTampil(2)
  let kolomRender2 = cols2.map(ponsKolomRender)

  let thead2 = document.getElementById('ns_tabel2_header')

  let headerTable2 = ''
  headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized</th>
<th style="padding: 4px 12px;" scope="col">User Oto</th>
<th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
`

  if (ns_level > 1) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized2</th>
    <th style="padding: 4px 12px;" scope="col">User Oto2</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto2</th>
  `
  }
  if (ns_level > 2) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized3</th>
    <th style="padding: 4px 12px;" scope="col">User Oto3</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto3</th>
  `
  }
  if (ns_level > 3) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized4</th>
    <th style="padding: 4px 12px;" scope="col">User Oto4</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto4</th>
  `
  }
  if (ns_level > 4) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized5</th>
    <th style="padding: 4px 12px;" scope="col">User Oto5</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto5</th>
  `
  }

  headerTable2 += `<th style="padding: 4px 12px;" scope="col">Batal</th>
<th style="padding: 4px 12px;" scope="col">User Batal</th>
<th style="padding: 4px 12px;" scope="col">Tanggal Batal</th>
<th style="padding: 4px 12px;" scope="col">Status</th>`

  thead2.innerHTML = ponsHeadHtml(cols2)
  let baris2 = thead2.querySelector('tr')
  if (baris2) {
    baris2.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris2.insertAdjacentHTML('beforeend', headerTable2)
  }

  // Definisi kolom DataTables + nama field untuk ORDER BY di server (null = tidak bisa
  // diurutkan). Urutan sel otorisasi tiap ns_level mengikuti judulnya: Authorized, User Oto, Tanggal Oto.
  let columns = []
  let fieldKolom = []
  let ns_tambahKolom = function (field, def) {
    columns.push(Object.assign({ data : null }, def))
    fieldKolom.push(field)
  }
  let kolomCentang = function (field, nyala) {
    ns_tambahKolom(field, {
      className : 'text-center',
      render : function (data, type, item) { return ponsSelCentang(nyala(item)) },
      createdCell : function (td, cellData, item) {
        td.classList.add(nyala(item) ? 'text-success' : 'text-danger')
      }
    })
  }
  let ns_kolomOto = function (n) {
    kolomCentang('IsOtorisasi' + n, function (item) { return Number(item['IsOtorisasi' + n]) })
  }
  let ns_kolomTgl = function (field) {
    ns_tambahKolom(field, { render : function (data, type, item) { return ponsSelTanggal(item[field]) } })
  }
  let ns_kolomTeks = function (field) {
    ns_tambahKolom(field, { render : function (data, type, item) { return item[field] || '' } })
  }

  // Warna & ikon disamakan dengan pembelianpermintaanagen.blade.php.
  ns_tambahKolom(null, {
    orderable : false,
    className : 'text-center',
    render : function (data, type, item) {
      let tombolAksiPO = `<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="ns_buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
      if (Number(item.IsOtorisasi1)) {
        // Level 1 sudah terisi tapi ns_level otorisasinya belum lengkap (mis. PO > 200 jt yang
        // diotorisasi Dept. Head) - tombol Otorisasi tetap tampil supaya Direktur bisa mengisi
        // ns_level 2. Edit tetap tidak ditampilkan.
        if (Number(item.NeedOtorisasi)) {
          tombolAksiPO += `
          <button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi Level 2" onclick="ns_buttonOtorisasi('${item.NoBukti}', 1)"><i class="bi bi-key"></i></button>`
        }
        tombolAksiPO += `
          <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Batal Otorisasi" onclick="ns_buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
          <button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Print" onclick="submitPrintcopy('${item.NoBukti}')"><i class="bi bi-printer"></i></button>`
      } else {
        tombolAksiPO += `
          <button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi" onclick="ns_buttonOtorisasi('${item.NoBukti}', 0)"><i class="bi bi-key"></i></button>
          <button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="ns_buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>`
      }
      return tombolAksiPO
    }
  })

  kolomRender2.forEach((c) => {
    ns_tambahKolom(c.field, {
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, item) { return ponsRenderNilai(c, item) }
    })
  });

  ns_kolomOto(1)
  ns_kolomTeks('OtoUser1')
  ns_kolomTgl('TglOto1')
  if (ns_level > 1) {
    ns_kolomOto(2)
    ns_kolomTeks('OtoUser2')
    ns_kolomTgl('TglOto2')
    if (ns_level > 2) {
      ns_kolomOto(3)
      ns_kolomTeks('OtoUser3')
      ns_kolomTgl('TglOto3')
      if (ns_level > 3) {
        ns_kolomOto(4)
        ns_kolomTeks('OtoUser4')
        ns_kolomTgl('TglOto4')
        if (ns_level > 4) {
          ns_kolomOto(5)
          ns_kolomTeks('OtoUser5')
          ns_kolomTgl('TglOto5')
        }
      }
    }
  }
  kolomCentang('Isbatal', function (item) { return item.Isbatal == 1 })
  ns_kolomTeks('UserBatal')
  ns_tambahKolom('TglBatal', {
    render : function (data, type, item) {
      let tglBatalTampil = ''
      if (item.TglBatal) {
        let dTb = new Date(item.TglBatal)
        let dd = ("0" + dTb.getDate()).slice(-2)
        let mm = ("0" + (dTb.getMonth() + 1)).slice(-2)
        tglBatalTampil = dd + '/' + mm + '/' + dTb.getFullYear()
      }
      return tglBatalTampil
    }
  })
  ns_tambahKolom('StatusPO', {
    className : 'text-center',
    render : function (data, type, item) { return ponsBadgeStatus(item) }
  })

  ponsPakaiCachePO = !!(pakaiCache && ponsCachePO)

  let inputSearch2 = document.getElementById('ponsSearch2')
  let kataCari = posisi ? posisi.search : (inputSearch2 ? inputSearch2.value : '')
  let orderAman = posisi ? posisi.order.filter((o) => o[0] < columns.length) : []

  $("#ns_tabel2").DataTable({
    // Lapisan "Memuat data..." sama dengan tab Outstanding PR & menu marketing.
    "processing" : true,
    "serverSide" : true,
    "lengthChange": false,
    "pageLength": ponsPanjangHalaman[2],
    "searchDelay" : 400,
    // [] = urutan bawaan server (Tanggal/NoBukti terbaru dulu).
    "order": orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : { "search" : kataCari || '' },
    "dom": "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "language" : {
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>'
    },
    "columns" : columns,
    "ajax" : function (data, callback) {
      if (ponsPakaiCachePO && ponsCachePO) {
        ponsPakaiCachePO = false
        callback(Object.assign({}, ponsCachePO, { draw : data.draw }))
        return
      }

      let kolom = null
      let arah = 'asc'
      if (data.order && data.order.length && fieldKolom[data.order[0].column]) {
        kolom = fieldKolom[data.order[0].column]
        arah = data.order[0].dir
      }

      ponsDrawPO = data.draw
      $.ajax({
        url : "{!! url('ponsloadpurchaseorder') !!}",
        type : "get",
        data : {
          draw : data.draw,
          start : data.start,
          length : data.length,
          search : data.search ? data.search.value : '',
          orderCol : kolom,
          orderDir : arah,
          tglawal : $('#ponsTglAwal').val(),
          tglakhir : $('#ponsTglAkhir').val(),
          status : ponsFilterStatus,
          otorisasi : ponsFilterOtorisasi
        },
        success : function (res) {
          ponsCachePO = res
          // Respons yang datang terlambat tidak boleh menimpa angka KPI.
          if (data.draw === ponsDrawPO) {
            ponsKpiPO = res.kpi || null
            renderKpiPONS()
          }
          callback(res)
        },
        error : function (err) {
          console.log('ponsloadpurchaseorder gagal:', err.status, err.responseText)
          alertify.warning('Gagal memuat data Purchase Order')
          // WAJIB tetap memanggil callback, kalau tidak ns_tabel berhenti merespons.
          callback({ draw : data.draw, data : [], recordsTotal : 0, recordsFiltered : 0 })
        }
      })
    },
    "preDrawCallback" : function () {
      $('#ns_tabel2_data').find('[data-toggle="tooltip"]').tooltip('dispose')
      $('body > .tooltip').remove()
    },
    "drawCallback" : function () {
      setTimeout(ponsAturTinggiTabel, 0)
      $('#ns_tabel2_data [data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    }
  });

  // Kelas "card" bawaan integrasi Bootstrap 4 membuat lapisan memuat tidak pernah hilang -
  // lihat catatan yang sama di initTabelOutstandingPons().
  let ponsElMemuat2 = document.querySelector('#tabel2_wrapper > .dataTables_processing')
  if (ponsElMemuat2) { ponsElMemuat2.classList.remove('card') }

  ponsIkatSearch(2)
  ponsIkatPanjangHalaman(2)
  ponsIkatPeriode()
  if (inputSearch2) { inputSearch2.value = kataCari || '' }
  ponsAturTinggiTabel()
}

// Lapisan "Memuat data..." di atas area ns_tabel selama konfigurasi kolom diambil saat
// halaman dibuka (tabelnya belum dibungkus DataTables, jadi belum punya lapisan sendiri).
function ponsTampilMemuatAwal (tampil) {
  $('#ns_myTabContent').children('.po-memuat-awal').remove()
  if (!tampil) { return }
  $('#ns_myTabContent').append(
    '<div class="po-memuat-awal"><span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span></div>'
  )
}

// Konfigurasi kolom (ponsloadall) cukup diambil SEKALI seumur halaman - perubahan kolom
// sesudahnya sudah tercatat di ponsCart. ns_loadAll() berikutnya (habis simpan/otorisasi)
// cukup memuat ulang datanya; dulu tiap kali ikut mengambil konfigurasi lagi secara
// sinkron (layar membeku).
let ponsMetaSudah = false
let ponsMetaSedangDiambil = false

// Halaman gabungan: daftar PO & Outstanding PR milik halaman PO (purchaseOrder.blade.php).
// Form Non Stock yang memanggil ns_loadAll() (habis simpan/otorisasi/batal) cukup memuat
// ulang daftar gabungan itu. Kode daftar lama di bawah (ns_loadAllLama, pons*) tidak dipakai.
function ns_loadAll () {
  loadAll()
  poSegarkanOutstanding()
}

function ns_loadAllLama () {
  console.log('loadall')

  ponsInitReportTableSekali()

  if (ponsMetaSudah) {
    ponsMuatUlangData()
    return
  }
  if (ponsMetaSedangDiambil) {
    return
  }
  ponsMetaSedangDiambil = true
  ponsTampilMemuatAwal(true)

  $.ajax({
    url: "{!! url('ponsloadall') !!}",
    type: "get",
    data: {
    },
    success: function(meta) {
      ponsCart[1] = ponsBuatCart(meta.headertableheader, meta.headertablevalue, meta.isnumeric, meta.isshown, meta.desimal, meta.aliasordered, true)
      ponsCart[2] = ponsBuatCart(meta.headertableheader2, meta.headertablevalue2, meta.isnumeric2, meta.isshown2, meta.desimal2, meta.aliasordered2)
      ponsMetaSudah = true
      ponsMuatUlangData()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat konfigurasi ns_tabel')
    },
    complete: function () {
      ponsMetaSedangDiambil = false
      ponsTampilMemuatAwal(false)
    }
  })
}

function ponsMuatUlangData () {
  ponsTab2Sudahdimuat = false

  let urutAktif = ponsUrutTabAktif()
  ponsPindahBar(urutAktif)

  ;[1, 2].forEach((u) => {
    ponsPerluGambar[u] = (u !== urutAktif)
  });

  if (urutAktif === 2) {
    ns_loadTabelPO()
  } else {
    initTabelOutstandingPons(urutAktif)
  }
}

// Tambah/edit/hapus item di ns_form TIDAK lagi memuat ulang daftar PO & tab Outstanding PR
// setiap kali - cukup ditandai, lalu dimuat ulang sekali saat ns_form ditutup.
let ponsDaftarBerubah = false

// Halaman gabungan: penanda "daftar berubah" memakai milik daftar PO gabungan.
function ponsTandaiDaftarBerubah () {
  poTandaiDaftarBerubah()
}

function ponsMuatUlangJikaBerubah () {
  poMuatUlangJikaBerubah()
}

// Lapisan "sedang diproses" di atas seluruh halaman - sama dengan menu marketing
// (ipMulaiProses di invoicepenjualan) dan purchaseOrder.blade.php (poProses).
let ponsSibuk = 0

function ponsMulaiProses (teks) {
  if (ponsSibuk) { return false }
  ponsSibuk++
  let lapisan = document.getElementById('ponsProsesOverlay')
  if (!lapisan) {
    lapisan = document.createElement('div')
    lapisan.id = 'ponsProsesOverlay'
    lapisan.className = 'po-proses-overlay'
    lapisan.innerHTML = '<span class="po-loading-chip"><span class="po-loading-spin"></span><span class="po-proses-teks"></span></span>'
    document.body.appendChild(lapisan)
  }
  lapisan.querySelector('.po-proses-teks').textContent = teks || 'Memproses...'
  lapisan.style.display = 'block'
  return true
}

function ponsSelesaiProses () {
  ponsSibuk = Math.max(0, ponsSibuk - 1)
  if (ponsSibuk) { return }
  let lapisan = document.getElementById('ponsProsesOverlay')
  if (lapisan) { lapisan.style.display = 'none' }
}

// Pekerjaan di halaman ini masih memakai AJAX sinkron (async:false): lapisannya
// digambar dulu, baru pekerjaannya dijalankan.
function ponsProses (teks, kerja) {
  if (!ponsMulaiProses(teks)) { return }
  requestAnimationFrame(function () {
    setTimeout(function () {
      try {
        kerja()
      } finally {
        ponsSelesaiProses()
      }
    }, 0)
  })
}

// Kartu KPI Outstanding PR tampil di semua tab, tapi tabelnya sendiri baru digambar
// saat tabnya dibuka (lazy - lihat ponsPerluGambar). Supaya kartunya tidak menunggu
// itu, total barisnya diambil sendiri di sini dengan payload minimal (length=1),
// terpisah dari initTabelOutstandingPons() - pola sama dengan purchaseOrder.blade.php
// (poMuatKpiOutstanding).
function ponsMuatKpiOutstanding () {
  $.ajax({
    url : PONS_OUT[1].url,
    type : "get",
    cache : false,
    data : { draw : 1, start : 0, length : 1, search : '' },
    success : function (res) {
      ponsKpiOut[1] = res.recordsTotal
      renderKpiPONS()
    },
    error : function (err) {
      console.log(PONS_OUT[1].url + ' gagal memuat KPI:', err.status, err.responseText)
    }
  })
}
// ponsMuatKpiOutstanding() tidak dipanggil lagi - KPI di halaman gabungan milik daftar PO.

// Dipanggil tiap kali PO berhasil disimpan (tambah/edit/hapus item) supaya barang
// yang baru saja diambil langsung hilang dari kartu Outstanding PR maupun tabelnya
// tanpa perlu F5. Cache-nya dikosongkan dulu (ponsCacheOut/ponsPakaiCacheOut) supaya
// draw berikutnya benar-benar menembak server - lihat initTabelOutstandingPons().
function ponsSegarkanOutstanding () {
  ponsCacheOut[1] = null
  ponsPakaiCacheOut[1] = false
  let selTabel = '#' + PONS_OUT[1].tabel
  if ($.fn.DataTable.isDataTable(selTabel)) {
    $(selTabel).DataTable().ajax.reload(null, false)
  }
  ponsMuatKpiOutstanding()
}

$(function () {
  $('#modalFilterPONS').on('show.bs.modal', function () {
    $('#ponsModalStatus').val(ponsFilterStatus)
    $('#ponsModalOtorisasi').val(ponsFilterOtorisasi)
    ponsUpdateFilterBadge()
  })

  $('#ns_nav-profile-tab').on('shown.bs.tab', function () {
    ponsAktifkanTabel(2)
    ponsPindahBar(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (!ponsTab2Sudahdimuat) {
      ponsPerluGambar[2] = false
      ns_loadTabelPO()
    } else if (ponsPerluGambar[2]) {
      ponsPerluGambar[2] = false
      ns_loadTabelPO(true)
    } else {
      ponsAturTinggiTabel()
    }
  })

  $('#ns_nav-home-tab').on('shown.bs.tab', function () {
    ponsAktifkanTabel(1)
    ponsPindahBar(1)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (ponsPerluGambar[1]) {
      ponsPerluGambar[1] = false
      initTabelOutstandingPons(1)
    } else {
      ponsAturTinggiTabel()
    }
  })

  let ponsTimerResize = null
  $(window).on('resize', function () {
    if (ponsTimerResize) { clearTimeout(ponsTimerResize) }
    ponsTimerResize = setTimeout(ponsAturTinggiTabel, 150)
  })
})

function ns_buttonAddAddPickBarangAll (kodebrg) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      ns_tempAddAdd = res.barang[0]
      document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddAdd.Kodebrg
      ns_kosongkanCosting()
      document.getElementById("ns_input_add_add_namabarang").value = ''
      document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddAdd.NamaBrg
      document.getElementById("input_add_add_disc").value = '0.00'
      document.getElementById("ns_input_add_add_discrp").value = '0.00'
      let selectOption = ''
      if (ns_tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${ns_tempAddAdd.Sat1}</option>`
      }
      if (ns_tempAddAdd.Sat2) {
        selectOption += `<option value=2>${ns_tempAddAdd.Sat2}</option>`
      }
      if (ns_tempAddAdd.Sat3) {
        selectOption += `<option value=3>${ns_tempAddAdd.Sat3}</option>`
      }
      document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption

      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      if(!res.harga.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("ns_input_add_add_harga").value = parseFloat(res.harga[0].Xharga).toFixed(2)
      } else {
        if (Number(ns_tempAddAdd.Hrg1_1)) {
          document.getElementById("ns_input_add_add_harga").value = parseFloat(ns_tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("ns_input_add_add_harga").value = '0.00'
        }
      }

      ns_buttonAddListBatal()
      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })

}


let ns_cekQntPR = 0
let ns_cekQntPO = 0
let ns_cekQntSisa = 0


function ns_cekQntStock () {
  // Batas Qnt Sisa hanya berlaku untuk barang dari PR - barang dari master (Non PR)
  // tidak punya kuantitas outstanding untuk dibandingkan. Dulu fungsi ini selalu memakai
  // ns_dataAddAddListItem[0] (baris PERTAMA di daftar terakhir yang dimuat), bukan baris
  // yang benar-benar dipilih (ns_tempAddAdd) - salah kalau baris yang dipilih bukan baris
  // pertama.
  if (ns_poSumberBarang() !== 'PR' || !ns_tempAddAdd) { return }

  ns_cekQntPR = ns_tempAddAdd.Qnt
  ns_cekQntPO = ns_tempAddAdd.QntPO
  ns_cekQntSisa = ns_tempAddAdd.SisaPPL

  let currentQntPO = Number((document.getElementById("ns_input_add_add_qty").value || '0').replace(/,/g, '')) || 0

  if (currentQntPO > ns_cekQntSisa) {
    alertify.warning('Qty PO Tidak boleh melebihi Qty Sisa')
    document.getElementById("ns_input_add_add_qty").value = '0.00'
  }
}

function ns_buttonAddAddPickBarangNonFOC (index , pEdit = 0) {

  let _token  = $("#_token").val()

  console.log(ns_dataAddAddListItem[index])
  ns_tempAddAdd = ns_dataAddAddListItem[index]

  ns_cekSatuanBarang(ns_tempAddAdd.KodeBrg)

  document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddAdd.KodeBrg
  ns_kosongkanCosting()
  document.getElementById("ns_input_add_add_namabarang").value = ''
  document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddAdd.NamaBrg
  document.getElementById("ns_input_add_add_qty").value = ns_formatAngka(parseFloat(ns_tempAddAdd.SisaPPL).toFixed(2))
  document.getElementById("ns_input_add_add_noPPL").value = ns_tempAddAdd.NoBukti
  document.getElementById("ns_input_add_add_urutPPL").value = ns_tempAddAdd.Urut
  // document.getElementById("ns_input_add_add_discrp").value = '0.00'
  let selectOption = ''
  let masterSatPR = ns_tempSatuanBarang.length ? ns_tempSatuanBarang[0] : null
  if (masterSatPR && masterSatPR.SAT1) {
    selectOption += `<option value=1>1-${masterSatPR.SAT1}</option>`
  }
  if (masterSatPR && masterSatPR.SAT2) {
    selectOption += `<option value=2>2-${masterSatPR.SAT2}</option>`
  }
  if (masterSatPR && masterSatPR.SAT3) {
    selectOption += `<option value=3>3-${masterSatPR.SAT3}</option>`
  }
  // Barang jasa hampir selalu tidak punya SAT1..SAT3 di master, sehingga dropdown-nya
  // kosong dan $(...).val() mengembalikan null - Satuan terkirim '-' dan Isi 0. Kalau
  // master tidak memberi satuan, pakai satuan baris PR-nya sendiri.
  if (!selectOption) {
    selectOption = `<option value=${Number(ns_tempAddAdd.NoSat) || 1} selected>${ns_tempAddAdd.Sat || '-'}</option>`
  }
  document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : ns_tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${ns_formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(ns_tempAddAdd.Hrg1_1)) {
          document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(ns_tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("ns_input_add_add_harga").value = '0.00'
        }
      }

      ns_buttonAddListBatal()
      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangJasa (index , pEdit = 0) {
  console.log('BARANG JASA PICK')
  let _token  = $("#_token").val()
  console.log(ns_dataAddAddListItem[index])
  ns_tempAddAdd = ns_dataAddAddListItem[index]

  ns_cekSatuanBarang(ns_tempAddAdd.KodeBrg)

  document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddAdd.KodeBrg
  ns_kosongkanCosting()
  document.getElementById("ns_input_add_add_namabarang").value = ''
  document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddAdd.NamaBrg
  // Barang dari master (Non PR) tidak punya kuantitas outstanding seperti barang dari
  // PR - qty dimulai kosong, diisi manual oleh user.
  document.getElementById("ns_input_add_add_qty").value = '0.00'

  document.getElementById("ns_input_add_add_noPPL").value = ''
  document.getElementById("ns_input_add_add_urutPPL").value = 0

  let selectOption = ''
  if (ns_tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${ns_tempSatuanBarang[0].SAT1}</option>`
  }
  if (ns_tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${ns_tempSatuanBarang[0].SAT2}</option>`
  }
  if (ns_tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${ns_tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : ns_tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${ns_formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(ns_tempAddAdd.Hrg1_1)) {
          document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(ns_tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("ns_input_add_add_harga").value = '0.00'
        }
      }

      ns_buttonAddListBatal()
      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


urutPPLTemp = 0

function buttonAddAddPickBarangJasaPPL (index , pEdit = 0) {
  console.log('BARANG JASA PICK')
  let _token  = $("#_token").val()
  console.log(ns_dataAddAddListItem[index])
  ns_tempAddAdd = ns_dataAddAddListItem[index]

  ns_cekSatuanBarang(ns_tempAddAdd.KodeBrg)

  document.getElementById("ns_input_add_add_kodebarang").value = ns_tempAddAdd.KodeBrg
  ns_kosongkanCosting()
  document.getElementById("ns_input_add_add_namabarang").value = ''
  document.getElementById("ns_input_add_add_namabarangasli").value = ns_tempAddAdd.NamaBrg
  document.getElementById("ns_input_add_add_qty").value = ns_formatAngka(parseFloat(ns_tempAddAdd.Qnt).toFixed(2))
  urutPPLTemp = ns_tempAddAdd.Urut

  document.getElementById("ns_input_add_add_urutPPL").value = 0
  // document.getElementById("ns_input_add_add_discrp").value = '0.00'

  let selectOption = ''
  selectOption = `<option value=1 selected>1</option>`

  document.getElementById("ns_input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : ns_tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${ns_formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(ns_tempAddAdd.Hrg1_1)) {
          document.getElementById("ns_input_add_add_harga").value = ns_formatAngka(parseFloat(ns_tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("ns_input_add_add_harga").value = '0.00'
        }
      }

      ns_buttonAddListBatal()
      document.getElementById("ns_input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

let ns_tempSatuanBarang = []

function ns_cekSatuanBarang (KodeBrg){
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      KodeBrg : KodeBrg
    },
    success: function(res) {
      ns_tempSatuanBarang = res
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function ns_buttonAddPickPelanggan (kode, nama , alamat, hari, ppn) {
  console.log('ns_buttonAddPickPelanggan')

  ns_setNewNoBukti(ppn)

  document.getElementById("ns_input_add_kodesupplier").value = kode
  document.getElementById("ns_input_add_namasupplier").value = nama
  document.getElementById("ns_input_add_alamatsupplier").value = alamat
  document.getElementById("ns_input_add_pembayaran").value = 0
  document.getElementById("ns_input_add_hari").value = hari
  document.getElementById("ns_input_add_kodealamatkirim").value = 'GMPL'
  let itemGudangDefault = ns_listAlamatKirim.find(item => item.KODEGDG === 'GMPL')
  document.getElementById("ns_input_add_alamatkirim").value = itemGudangDefault ? itemGudangDefault.Alamat : ''
  document.getElementById("ns_input_add_kodepic").value = ''
  document.getElementById("ns_input_add_namapic").value = ''
  document.getElementById("ns_input_add_kodeekspedisi").value = '-'
  document.getElementById("ns_input_add_ekspedisi").value = '-'

  if (hari == 0 ){
    ns_selectTipeBayar = `<option value=0 selected>Tunai</option>
    <option value=1>Kredit</option>`
  }
  else if (hari != 0){
    ns_selectTipeBayar = `
    <option value=0 >Tunai</option>
    <option value=1 selected>Kredit</option>`
  }

  if (ppn == 1){
    ns_selectTipePPN = `
    <option value=1>Exclude</option>
    <option value=2>Include</option>`
  } else if (ppn == 0) {
    ns_selectTipePPN = `
    <option value=0>None</option>`
  }

  document.getElementById("ns_input_add_pembayaran").innerHTML = ns_selectTipeBayar
  document.getElementById("ns_input_add_tipeppn").innerHTML = ns_selectTipePPN

  ns_isiPPHSupplier(kode)

  ns_buttonAddListBatal()
  // $("#ns_form").modal('toggle')
}

function ns_formatPPH (nilai) {
  let n = parseFloat(nilai)
  return isNaN(n) ? '0.00' : n.toFixed(2)
}

// PPH23 & PPH21 (informatif) dari setting supplier - daftar supplier polistpelanggan sudah
// membawa NPPH23 & NPPH21 (= DBCUSTSUPP.NPPH22). Yang tersimpan ke PO tetap ditentukan server.
function ns_isiPPHSupplier (kode) {
  document.getElementById("input_add_pph23").value = '0.00'
  document.getElementById("input_add_pph21").value = '0.00'
  poAmbilSupplier(function (list) {
    // Supplier sudah diganti lagi sebelum daftar selesai dimuat - abaikan.
    if (document.getElementById("ns_input_add_kodesupplier").value !== kode) { return }
    let s = (list || []).find((item) => String(item.KodeCustSupp) === String(kode))
    document.getElementById("input_add_pph23").value = ns_formatPPH(s ? s.NPPH23 : 0)
    document.getElementById("input_add_pph21").value = ns_formatPPH(s ? s.NPPH21 : 0)
  })
}

// Costing bergantung pada COA barang, jadi dikosongkan setiap kali barang item dipilih ulang.
function ns_kosongkanCosting () {
  document.getElementById("input_add_add_costing").value = ''
  document.getElementById("input_add_add_subcosting").value = ''
}

function buttonAddPickCosting (kodeCost) {
  document.getElementById("input_add_add_costing").value = kodeCost

  ns_buttonAddListBatal()
  // $("#ns_form").modal('toggle')
}

function buttonAddPickSubCosting (kodeSubCost) {
  document.getElementById("input_add_add_subcosting").value = kodeSubCost

  ns_buttonAddListBatal()
  // $("#ns_form").modal('toggle')
}

function buttonAddPickPerkiraan (kodePerkiraan) {
  console.log('buttonAddPickPerkiraan')

  document.getElementById("input_add_perkiraan").value = kodePerkiraan

  ns_buttonAddListBatal()
  // $("#ns_form").modal('toggle')
}

function ns_buttonAddPickAlamatKirim (index) {

  let itemX = ns_listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    ns_onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  document.getElementById("ns_input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("ns_input_add_alamatkirim").value = itemX.Alamat
  ns_buttonAddListBatal()

}

function ns_buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('ns_buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("ns_input_add_kodeekspedisi").value = kode
  document.getElementById("ns_input_add_ekspedisi").value = nama

  ns_buttonAddListBatal()
  document.getElementById("ns_input_add_kodeekspedisi").scrollIntoView();
}

function ns_buttonAddPickPIC (kode, nama ) {
  console.log('ns_buttonAddPickPIC')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KodePF' , kode)
  }
  document.getElementById("ns_input_add_kodepic").value = kode
  document.getElementById("ns_input_add_namapic").value = nama
  ns_buttonAddListBatal()
}

function ns_buttonAddPickValas (kode, kurs) {
  console.log('ns_buttonAddPickValas')
  console.log(kode,kurs)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KODEVLS' , kode)
    ns_onChangeHeader('KURS' , kurs)
  }
  document.getElementById("ns_input_add_valas").value = kode
  document.getElementById("ns_input_add_kurs").value = kurs
  ns_buttonAddListBatal()
}

function ns_buttonAddPickSales (kode, nama ) {
  console.log('ns_buttonAddPickSales')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("ns_input_add_kodesales").value = kode
  document.getElementById("ns_input_add_namasales").value = nama
  ns_buttonAddListBatal()
  // $("#ns_form").modal('toggle')
}

function ns_buttonAddPickBackOffice (kode, nama ) {
  console.log('ns_buttonAddPickBackOffice')
  console.log(kode,nama)
  if (ns_tipeform == 'edit') {
    ns_onChangeHeader('Boffice' , kode)

  }
  document.getElementById("ns_input_add_kodebackoffice").value = kode
  document.getElementById("ns_input_add_namabackoffice").value = nama
  ns_buttonAddListBatal()

  document.getElementById("ns_input_add_kodebackoffice").scrollIntoView();

}

// 'hide', bukan 'toggle'. Fungsi ini selalu berniat MENUTUP modal browse, dan sekarang
// dipanggil juga dari jalur auto-pick (buttonAddAddPickBarang* dijalankan tanpa modal
// pernah dibuka) - 'toggle' pada kondisi itu justru akan membuka modalnya.
function ns_buttonAddListBatal () {
  $('.ns_showhidemodalbodyadd').hide();
  $('#ns_modalBodyAddMain').show();

  $("#ns_form").modal('hide')
}

function ns_cleanFormAddAdd () {
  document.getElementById("ns_input_add_add_nopnwpo").value = '-'

  document.getElementById("ns_input_add_add_kodebarang").value = ''
  document.getElementById("ns_input_add_add_namabarang").value = ''
  document.getElementById("ns_input_add_add_namabarangasli").value = ''
  document.getElementById("ns_input_add_add_qty").value = '0.00'
  document.getElementById("ns_input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  document.getElementById("ns_input_add_add_harga").value = '0.00'
  document.getElementById("ns_input_add_add_discrp").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen1").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen2").value = '0.00'
  document.getElementById("ns_input_add_add_discpersen3").value = '0.00'
  document.getElementById("input_add_add_costing").value = ''
  document.getElementById("input_add_add_subcosting").value = ''
  document.getElementById("input_add_add_satuanAlias").value = ''

  // Field referensi milik item yang tadi dipilih - kosongkan supaya memulai entri item
  // baru tidak menampilkan sisa asal barang dari item sebelumnya sebelum user memilih
  // barang baru lewat modal browse.
  document.getElementById("ns_input_add_add_noPPL").value = ''
  document.getElementById("ns_input_add_add_urutPPL").value = 0

  document.getElementById("ns_tabel_data_add_harga_terakhir").innerHTML =
    '<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>'
}

function ns_lockFormAdd () {
  document.getElementById("ns_input_add_kodesupplier").disabled = true
  document.getElementById("ns_input_add_tipeppn").disabled = true
  document.getElementById("ns_input_add_pembayaran").disabled = true
  document.getElementById("ns_input_add_keterangan").disabled = true
  document.getElementById("ns_input_add_kodealamatkirim").disabled = true
  document.getElementById("ns_input_add_tanggalkirim").disabled = true
  document.getElementById("ns_input_add_tanggalkirim").disabled = true
  document.getElementById("ns_input_add_hari").disabled = true
  document.getElementById("ns_input_add_draftpo").disabled = true
  document.getElementById("ns_input_add_add_discpersen1").disabled = true
  document.getElementById("ns_input_add_add_discpersen2").disabled = true
  document.getElementById("ns_input_add_add_discpersen3").disabled = true
  document.getElementById("ns_input_add_add_foc").disabled = true

  // Valas & Dikirim Ke sekarang dropdown langsung berinteraksi (bukan lagi input
  // disabled + tombol picker), jadi harus ikut dikunci di sini - lihat catatan yang
  // sama di purchaseOrder.blade.php (ns_lockFormAdd/unlockFormAdd).
  document.getElementById("ns_input_add_valas").disabled = true

  document.getElementById("ns_buttonAddListPelanggan").hidden = true
  document.getElementById("ns_buttonAddListSales").hidden = true
  document.getElementById("ns_buttonAddListPIC").hidden = true
  document.getElementById("ns_buttonAddListLokasiPenerima").hidden = true
  document.getElementById("ns_buttonAddListBackOffice").hidden = true
  document.getElementById("ns_buttonAddListBackOffice").hidden = true
  document.getElementById("ns_buttonTambahItem").hidden = true

  document.getElementById("ns_input_add_disc").disabled = true
  document.getElementById("ns_input_add_discrp").disabled = true

  // Perkiraan header dikomentari di form (otomatis dari COA barang); PPH23/PPH21 selalu disabled.
  document.getElementById("input_add_pph23").disabled = true
  document.getElementById("input_add_pph21").disabled = true
}

function ns_buttonShowHideHeader ()
{
  var modal = document.getElementById("ns_modalBodyAddMainHeader");
  console.log($('#ns_modalBodyAddMainHeader').css('display'))
  if($('#ns_modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function ns_buttonShowHideHeaderDetail () {
  var modal = document.getElementById("ns_modalBodyDetailMainHeader");
  console.log($('#ns_modalBodyDetailMainHeader').css('display'))
  if($('#ns_modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function ns_unlockFormAdd () {
  // Supplier hanya boleh dipilih saat bikin PO baru ("add"). Begitu ns_tipeform == 'edit'
  // (baik karena membuka PO lama lewat ns_buttonEdit(), maupun karena item pertama sudah
  // tersimpan - lihat ns_submitAddAdd()), supplier dikunci supaya tidak diam-diam diganti
  // di tengah dokumen yang sudah ada itemnya. Sama seperti purchaseOrder.blade.php.
  let bolehPilihSupplier = (ns_tipeform !== 'edit')
  document.getElementById("ns_input_add_kodesupplier").disabled = !bolehPilihSupplier
  document.getElementById("ns_buttonAddListPelanggan").disabled = !bolehPilihSupplier

  document.getElementById("ns_input_add_tipeppn").disabled = false
  document.getElementById("ns_input_add_pembayaran").disabled = false
  document.getElementById("ns_input_add_keterangan").disabled = false
  document.getElementById("ns_input_add_kodealamatkirim").disabled = false
  document.getElementById("ns_input_add_tanggalkirim").disabled = false
  document.getElementById("ns_input_add_tanggalkirim").disabled = false
  document.getElementById("ns_input_add_hari").disabled = false
  document.getElementById("ns_input_add_draftpo").disabled = false
  document.getElementById("ns_input_add_add_discpersen1").disabled = false
  document.getElementById("ns_input_add_add_discpersen2").disabled = false
  document.getElementById("ns_input_add_add_discpersen3").disabled = false
  document.getElementById("ns_input_add_add_foc").disabled = false

  document.getElementById("ns_input_add_valas").disabled = false

  document.getElementById("ns_buttonAddListPelanggan").hidden = false
  document.getElementById("ns_buttonAddListSales").hidden = false
  document.getElementById("ns_buttonAddListPIC").hidden = false
  document.getElementById("ns_buttonAddListLokasiPenerima").hidden = false
  document.getElementById("ns_buttonAddListBackOffice").hidden = false
  document.getElementById("ns_buttonAddListBackOffice").hidden = false
  document.getElementById("ns_buttonTambahItem").hidden = false

  document.getElementById("ns_input_add_disc").disabled = false
  document.getElementById("ns_input_add_discrp").disabled = false

  // PPH23/PPH21 hanya informatif (dari supplier) - tetap disabled walau form dibuka.
  document.getElementById("input_add_pph23").disabled = true
  document.getElementById("input_add_pph21").disabled = true
}

function ns_cleanFormAdd () {
  document.getElementById("ns_input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("ns_input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("ns_input_add_kodesupplier").value = ''
  document.getElementById("ns_input_add_namasupplier").value = ''
  document.getElementById("ns_input_add_alamatsupplier").value = ''
  document.getElementById("ns_input_add_kodealamatkirim").value = 'GMPL'
  document.getElementById("ns_input_add_alamatkirim").value = 'Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel.Simpang Pasir Mangkupalas, Samarinda Seberang. '
  document.getElementById("ns_input_add_kodepic").value = ''
  document.getElementById("ns_input_add_namapic").value = ''
  document.getElementById("ns_input_add_kodeekspedisi").value = '-'
  document.getElementById("ns_input_add_ekspedisi").value = '-'
  document.getElementById("ns_input_add_keterangan").value = ''
  document.getElementById("ns_input_add_valas").value = ''
  document.getElementById("ns_input_add_kurs").value = ''
  document.getElementById("ns_input_add_kodebackoffice").value = ''
  document.getElementById("ns_input_add_namabackoffice").value = ''
  document.getElementById("ns_input_add_tipeppn").value = 0
  document.getElementById("ns_input_add_pembayaran").value = 0
  document.getElementById("ns_input_add_kodesales").value = ''
  document.getElementById("ns_input_add_namasales").value = ''
  document.getElementById("ns_input_add_hari").value = 0
  document.getElementById("ns_input_add_draftpo").value = 0
  // Dulu tidak dikosongkan - PO baru masih menampilkan PPH PO / supplier sebelumnya.
  document.getElementById("input_add_pph23").value = '0.00'
  document.getElementById("input_add_pph21").value = '0.00'

  document.getElementById("ns_input_add_tipeppn").disabled = false
  document.getElementById("ns_input_add_pembayaran").disabled = false
  document.getElementById("ns_input_add_keterangan").disabled = false
  document.getElementById("ns_input_add_tanggalkirim").disabled = false
  document.getElementById("ns_input_add_tanggalkirim").disabled = false
  document.getElementById("ns_input_add_hari").disabled = false
  document.getElementById("ns_input_add_draftpo").disabled = false

  document.getElementById("ns_buttonAddListPelanggan").disabled = false
  document.getElementById("ns_buttonAddListSales").disabled = false
  document.getElementById("ns_buttonAddListPIC").disabled = false
  document.getElementById("ns_buttonAddListLokasiPenerima").disabled = false
  document.getElementById("ns_buttonAddListBackOffice").disabled = false

  document.getElementById("ns_input_add_disc").disabled = false
  document.getElementById("ns_input_add_discrp").disabled = false

  document.getElementById("ns_input_add_total").value = '0.00'
  document.getElementById("ns_input_add_disc").value = '0.00'
  document.getElementById("ns_input_add_discrp").value = '0.00'
  document.getElementById("ns_input_add_ppn").value = '0.00'
  document.getElementById("ns_input_add_dpp").value = '0.00'
  document.getElementById("ns_input_add_grandtotal").value = '0.00'
}

function ns_buttonEdit (NOBUKTI) {
  ponsProses('Memuat data...', function () { ns_buttonEditKerja(NOBUKTI) })
}

function ns_buttonEditKerja (NOBUKTI) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  ns_tipeform = 'edit'
  console.log('ns_buttonEdit' , NOBUKTI)

  if (NOBUKTI != null){
  ns_noBuktiUntukAdd = NOBUKTI
  }

  if (NOBUKTI == null){
    ns_noBuktiUntukAdd = 0
  }

  $('.ns_showhide').hide();
  // $('.ns_showhidemodalbodyaddmain').hide();
  $('#ns_buttonSubmitSaveHeader').show();
  ns_unlockFormAdd()

  let akses = $("#ns_akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('pocekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.ns_showhidemodalbodyadd').hide();
  // $('#ns_modalBodyAddListPelanggan').show();
  $('#ns_modalBodyAddMain').show();
  ns_refreshDataTableAdd(NOBUKTI)
  // $("#ns_form").modal('toggle')
  poIsiDropdownJenisPO('ns_input_add_jenispo', 'nonstock', false)
  $('#page1').hide();
  $('#page2').hide();
  $('#ns_page2').show();
}

 let ns_noBuktiUntukAdd = 0

function ns_buttonAdd (noBukti) {
  ponsProses('Memuat data...', function () { ns_buttonAddKerja(noBukti) })
}

function ns_buttonAddKerja (noBukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  ns_tipeform = 'add'

  if (noBukti != null){
  ns_noBuktiUntukAdd = noBukti
  }

  if ( noBukti == null){
    ns_noBuktiUntukAdd = 0
  }

  $('.ns_showhide').hide();
  // $('.ns_showhidemodalbodyaddmain').hide();
  $('#ns_buttonSubmitSaveHeader').hide();
  let akses = $("#ns_akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  ns_dataTableAdd = []
  ns_cleanFormAdd()
  ns_unlockFormAdd()

  ns_refreshDataTableAdd()
  document.getElementById("ns_input_add_nobukti").value = ''
  document.getElementById("ns_input_add_valas").value = 'IDR'
  document.getElementById("ns_input_add_kurs").value = '1.00'
  document.getElementById('ns_input_add_add_noPPL').value = ns_noBuktiUntukAdd;

  // Tanggal diambil dari server (getdate) supaya tidak bisa diakali lewat jam komputer user
  // (sama seperti pembelianpermintaanagen). Input tanggal tetap disabled.
  $.ajax({
    url: "{!! url('gettanggalserver') !!}",
    type: "get",
    async: false,
    success: function(res) {
      document.getElementById("ns_input_add_tanggal").value = res[0].tanggal
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengambil tanggal server, silahkan refresh browser')
    }
  })

  // Jenis PO bisa diganti selama PO belum punya item, kecuali form dibuka dari Outstanding PR
  // (Buat PO) - jenisnya mengikuti jenis PR.
  poIsiDropdownJenisPO('ns_input_add_jenispo', 'nonstock', !ns_noBuktiUntukAdd)
  $('#page1').hide();
  // Saat Jenis PO diganti dari form PO Stock, form PO Stock ditutup di sini (bukan sebelum
  // membuka) supaya kalau pembukaan gagal (periode dikunci, dll.) form lama tetap tampil.
  $('#page2').hide();
  $('#ns_page2').show();

}

function ns_buttonCloseForm () {
  $('#ns_page3').hide();
  $('#ns_page2').hide();
  $('#page1').show();
  ponsMuatUlangJikaBerubah()

}

function ns_buttonCloseFormDetail () {
  $('#ns_page3').hide();
  $('#page1').show();
  ponsMuatUlangJikaBerubah()

}

function ns_submitAdd ()
{

  let alamatpelanggan = $("#ns_input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#ns_input_add_keterangan").val();
  console.log(catatan)

}

function ns_buttonAddMainHeader() {
  $('.ns_showhidemodalbodyaddmain').hide();
  $('#ns_modalBodyAddMainHeader').show();
  // $('#ns_buttonAddListPelanggan').hide();
}

function ns_buttonAddMainItems() {
  $('.ns_showhide').hide();
  $('.ns_showhidemodalbodyaddmain').hide();
  $('#ns_modalBodyAddMainItems').show();
}

function ns_buttonDetailMainHeader() {
  $('.ns_showhidemodalbodydetailmain').hide();
  $('#ns_modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function ns_buttonDetailMainItems() {
  $('.ns_showhide').hide();
  $('.ns_showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function ns_cekPoDet () {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('cekPoDet') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res){
      console.log(res)
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function ns_buttonDetail (NOBUKTI) {
  ponsProses('Memuat data...', function () { ns_buttonDetailKerja(NOBUKTI) })
}

function ns_buttonDetailKerja (NOBUKTI) {
  ns_tipeform = 'detail'
  console.log('button Detail' , NOBUKTI)

  $('.ns_showhide').hide();
  // $('.ns_showhidemodalbodyaddmain').hide();
  $('#ns_buttonSubmitSaveHeader').show();
  ns_lockFormAdd()

  let akses = $("#ns_akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.ns_showhidemodalbodyadd').hide();
  // $('#ns_modalBodyAddListPelanggan').show();
  $('#ns_modalBodyAddMain').show();
  ns_refreshDataTableAdd(NOBUKTI)
  // $("#ns_form").modal('toggle')
  poIsiDropdownJenisPO('ns_input_add_jenispo', 'nonstock', false)
  $('#page1').hide();
  $('#page2').hide();
  $('#ns_page2').show();
}

function ns_refreshDataTableAdd (NOBUKTI) {

  console.log('ns_refreshDataTableAdd' , NOBUKTI)
  // Mode detail hanya melihat - kolom Actions disembunyikan (header + sel).
  document.getElementById("th_add_actions").style.display = ns_tipeform == 'detail' ? 'none' : ''
  if (!NOBUKTI) {

    // if(!ns_dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="10">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("ns_tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('pogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#ns_form").modal('toggle')
          $('#ns_page3').hide();
          $('#ns_page2').hide();
          $('#page1').show();
          ponsMuatUlangJikaBerubah()
        } else {
          ns_dataHeaderAdd = res.list[0]
          ns_dataTableAdd = res.list

          let rowTable = ""
          ns_dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.NamaAlias || ''}</td>
              <td class="text-right">${item.Qnt ? ns_formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? ns_formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? ns_formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? ns_formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
              <td class="text-center" style="${ns_tipeform == 'detail' ? 'display:none' : ''}">
                ${ns_tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="ns_buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="ns_buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!ns_dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="10">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("ns_tabel_data_add").innerHTML = rowTable

          if (ns_dataHeaderAdd.NoPPL != null){
            ns_noBuktiUntukAdd = ns_dataHeaderAdd.NoPPL
          }

          if (ns_dataHeaderAdd.NoPPL == null){
            ns_noBuktiUntukAdd = 0
          }

          document.getElementById("ns_input_add_nobukti").value = ns_dataHeaderAdd.NoBukti
          document.getElementById("ns_input_add_namasupplier").value = ns_dataHeaderAdd.NamaCustSupp
          document.getElementById("ns_input_add_kodesupplier").value = ns_dataHeaderAdd.KodeSupp
          document.getElementById("ns_input_add_alamatsupplier").value = ns_dataHeaderAdd.Alamat1
          document.getElementById("ns_input_add_valas").value = ns_dataHeaderAdd.KodeVls
          document.getElementById("ns_input_add_kurs").value = ns_dataHeaderAdd.Kurs
          document.getElementById("ns_input_add_keterangan").value = ns_dataHeaderAdd.Catatan
          document.getElementById("ns_input_add_kodealamatkirim").value = ns_dataHeaderAdd.Kodegdg
          document.getElementById("ns_input_add_alamatkirim").value = ns_dataHeaderAdd.ALamatGdg
          document.getElementById("ns_input_add_kodeekspedisi").value = ns_dataHeaderAdd.KodeExp
          document.getElementById("ns_input_add_ekspedisi").value = ns_dataHeaderAdd.NamaExp
          document.getElementById("ns_input_add_hari").value = ns_dataHeaderAdd.Hari
          document.getElementById("ns_input_add_keterangan").value = ns_dataHeaderAdd.Keterangan
          document.getElementById("ns_input_add_pembayaran").value = ns_dataHeaderAdd.TipeBayar
          document.getElementById("ns_input_add_tipeppn").value = ns_dataHeaderAdd.PPN
          document.getElementById("ns_input_add_tanggal").value = ns_formatDate(ns_dataHeaderAdd.Tanggal)
          document.getElementById("ns_input_add_tanggalkirim").value = ns_formatDate(ns_dataHeaderAdd.TglKirim)
          // PPH yang tersimpan di PO (informatif). Perkiraan header tidak ditampilkan lagi.
          document.getElementById("input_add_pph23").value = ns_formatPPH(ns_dataHeaderAdd.npph23)
          document.getElementById("input_add_pph21").value = ns_formatPPH(ns_dataHeaderAdd.NPPH21)

          // Total = jumlah Subtotal (Total) seluruh item.
          document.getElementById("ns_input_add_total").value = ns_formatAngka(ns_dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          document.getElementById("ns_input_add_disc").value = ns_dataHeaderAdd.Disc ? parseFloat(ns_dataHeaderAdd.Disc).toFixed(2) : '0.00'
          document.getElementById("ns_input_add_discrp").value = ns_formatAngka(ns_dataHeaderAdd.TotDiskon ? parseFloat(ns_dataHeaderAdd.TotDiskon).toFixed(2) : '0.00')
          document.getElementById("ns_input_add_dpp").value = ns_formatAngka(parseFloat(ns_dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("ns_input_add_ppn").value = ns_formatAngka(parseFloat(ns_dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("ns_input_add_grandtotal").value = ns_formatAngka(parseFloat(ns_dataHeaderAdd.TotNet).toFixed(2))

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
  }
}

function ns_buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#ns_akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(ns_dataTableAdd[i])
  let dataDelete = ns_dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() { ponsProses('Menghapus item...', function () {

        let _token = $("#_token").val();
        let Choice = "D"

        let NoBukti = dataDelete.NoBukti
        let Urut = dataDelete.Urut

        $.ajax({
          // Diperbaiki: dulu menembak pospadd (POController@spAdd, milik PO stock), yang
          // menulis pJasa=0 - menghapus item PO Non Stock diam-diam membalik dokumen jadi
          // PO stock. ponsspadd (PONonStockController@spAdd) menulis pJasa=1, sama seperti
          // jalur Add/Edit yang sudah benar - lihat ns_submitAddAdd()/submitAddEdit().
          url: "{!! url('ponsspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            Choice,
            NoBukti,
            NoUrut:0,
            Tanggal: '',
            TglJatuhTempo: '',
            KodeSupp: '',
            // Handling,
            KodeExp: '',
            Keterangan: '',
            // FakturSupp,
            KodeVls: '',
            Kurs: 0,
            PPn: 0,
            TipeBayar: 0,
            Hari: 0,
            // TipeDisc,
            // Disc,
            DiscRp: 0,
            Urut,
            KodeBrg: 0,
            Qnt: 0,
            NoSat: 0,
            Satuan: '',
            Isi: 0,
            Harga: 0,
            DiscP: 0,
            // DiscTot,
            NoPPL: '',
            // IsClose,
            // IsCloseD,
            // Catatan,
            // IsExp,
            // Tolerate,
            UrutPPL: 0,
            Kodegdg: '',
            Discpdet2: 0,
            Discpdet3: 0,
            // Discpdet4,
            // Discpdet5,
            // FlagTipe,
            NamaBrg: '',
            // IsJasa,
            // pFirst,
            pFOC: 0,
            Noso: '',
            Jmlrecord: 0,
            NOPOCUST: '',
            // IdUser,
            // pJasa,
            NPPH23: 0,
            PERKIRAAN: '',
            SatX: '',
            COST: '',
            SUBCOST: '',
            TglKirim: '',
            PPH21: 0,
            NOPNw: '',
            UrutPNW: 0

          },
          success: function(res) {
            if (res && res.pesan) {
              alertify.warning(res.pesan)
              return
            }
            console.log('resspsoadd', res)
            ponsTandaiDaftarBerubah()

            // ns_lockFormAdd()
            $('.ns_showhide').hide();

            ns_refreshDataTableAdd(NoBukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }) }
    ,function(){
      console.log('no')
    });

}

function ns_formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function ns_searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#ns_input_search_barang_all").val();

    if ($.fn.DataTable.isDataTable('#ns_tabel_add_list_barangall')) { $('#ns_tabel_add_list_barangall').DataTable().destroy(); }

  }

}

// Membuang pemisah ribuan dari nilai ns_input-partial-number (autoNumeric) sebelum dipakai
// sebagai angka - tanpa ini parseFloat("1,234") berhenti di koma dan menghasilkan 1.
function ns_formatAngkaVal (angka) {
  return Number(String(angka == null ? '' : angka).split(',').join('')) || 0
}

function ns_formatAngka (angkaString) {
  if (angkaString === null || angkaString === undefined || angkaString === '') {
    return '0.00'
  }
  if (isNaN(Number(angkaString))) {
    return '0.00'
  }
  angkaString = parseFloat(angkaString).toFixed(2).toString()
  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2 = ''
    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
    }
    temp2 += '.' + tempAngka[1]
    temp2 = '-' + temp2
    return temp2
  }
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function formatAngkaRupiah(angka) {
  if (angka == null || angka == '') return '0.00';

  return ns_formatAngka(parseFloat(angka).toFixed(2));
}

function ns_reverseCalculateDiscPercent() {
  let harga = ns_formatAngkaVal($('#ns_input_add_add_harga').val()) || 0;
  let discRp = ns_formatAngkaVal(document.getElementById('ns_input_add_add_discrp').value) || 0;

  // Clear all discount percentage fields first
  document.getElementById('ns_input_add_add_discpersen1').value = 0;
  document.getElementById('ns_input_add_add_discpersen2').value = 0;
  document.getElementById('ns_input_add_add_discpersen3').value = 0;

  // If harga is 0, we can't calculate percentage
  if (harga === 0) {
    return;
  }

  // Calculate the discount percentage
  let discPercent = (discRp / harga) * 100;

  // Validate that discount doesn't exceed 100%
  if (discPercent > 100) {
    alert("Diskon tidak boleh melebihi harga");
    document.getElementById('ns_input_add_add_discrp').value = '0.00';
    return;
  }

  // Set the first discount percentage field
  document.getElementById('ns_input_add_add_discpersen1').value = discPercent.toFixed(2);
}

function ns_calculateDiscRp() {
  let disc1 = document.getElementById('ns_input_add_add_discpersen1').value
  let disc2 = document.getElementById('ns_input_add_add_discpersen2').value
  let disc3 = document.getElementById('ns_input_add_add_discpersen3').value

  let discRp = ns_formatAngkaVal($('#ns_input_add_add_harga').val())

  disc1 = parseFloat(disc1) || 0
  disc2 = parseFloat(disc2) || 0
  disc3 = parseFloat(disc3) || 0
  discRp = parseFloat(discRp) || 0

  if (disc1 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('ns_input_add_add_discpersen1').value = ""
    return
  }
  if (disc2 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('ns_input_add_add_discpersen2').value = ""
    return
  }
  if (disc3 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('ns_input_add_add_discpersen3').value = ""
    return
  }

  let currentAmount = discRp
  let totalDiscount = 0

  if (disc1 > 0) {
    let afterDiskon1 = currentAmount * (disc1/100)
    currentAmount = currentAmount - afterDiskon1
    totalDiscount += afterDiskon1
  }

  if (disc2 > 0) {
    let afterDiskon2 = currentAmount * (disc2/100)
    currentAmount = currentAmount - afterDiskon2
    totalDiscount += afterDiskon2
  }

  if (disc3 > 0) {
    let afterDiskon3 = currentAmount * (disc3/100)
    currentAmount = currentAmount - afterDiskon3
    totalDiscount += afterDiskon3
  }

  document.getElementById('ns_input_add_add_discrp').value = ns_formatAngka(parseFloat(totalDiscount).toFixed(2))
}












function submitPrintcopy (nobukti) {

    let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('purchaseorderprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        ns_dataPrint = res

        console.log(ns_dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < ns_dataPrint.length; i+=7)
    {
      let tempArray = ns_dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = ns_dataPrint[0].tanggal.split(' ')[0];
    let tanggalKirimOnly = ns_dataPrint[0].tglkirim.split(' ')[0];


    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 18px;
        padding: 0px 4px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .border-bottom {
        border: bottom;
      }
      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 14cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">Jl. Ampera Pergudangan Mangkupalas Bisnis Centre Blok D No.18 RT.022, Simpang Pasir Palaran, Samarinda - Kalimantan Timur</div>
                        <div class="pb-1" style="width: 100%; font-size: 10px;">TELP : 0541 - 4104142, | FAX : 0541 - 4104195</div>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">E-Mail : sml@indo.net.id</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+ns_dataPrint[0].NAMA+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+ns_dataPrint[0].ALAMAT1+`</div>
                    </div>
                  </div>

                  <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+ns_dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                    </div>
              <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Batas Tgl Kirim</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+ns_dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Mata Uang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+ns_dataPrint[0].KODEVLS+`</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 95%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td class="text-center" style="width: 15%">KODE BRG</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 8%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                    <td class="text-center" style="width: 15%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NAMABRG}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 10%;">${itemSub.NAMAMERK ?? ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATX}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${ns_formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${ns_formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
const ns_maxRows = 7;
const ns_fillerCount = Math.max(0, ns_maxRows - item.length);
for (let f = 0; f < ns_fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 18px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 13px;">

  <div style="width: 38%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${ns_dataPrint[0].Expedisi ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${ns_dataPrint[0].almkirim ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
    </div>
    <div style="display: flex; flex-direction: column; width: 100%">
      <div class="pb-1">CV. SINAR MAHAKAM LESTARI</div>
      <div class="pb-1">Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel. Simpang Pasir Mangkupalas, Samarinda Seberang.</div>
      <div class="pb-1">Telp: +62 541-4104142 | UP. IBU ALVI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div>User :</div>
      <div>`+ns_dataPrint[0].Iduser+`</div>
    </div>
  </div>`

  if(i == arrayDataPrint.length - 1){

    tempPrintStr += `
  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> JUMLAH </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].tsub).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 4px; position: relative;">
      <div style="width: 5%; text-align:left;"> DISKON </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].Tdisc).toFixed(2))}</div>

      <div style="
      position: absolute;
      right: 0;
      bottom: 0;
      width: 35%;
      border-bottom: 1px solid #000;"></div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> DPP </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].Tndpprp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 6px; position: relative;">
      <div style="width: 5%; text-align:left;"> PPN </div>
      <div style="
        position: absolute;
        right: 0;
        bottom: 3px;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>

      <!-- garis bawah 2 -->
      <div style="
        position: absolute;
        right: 0;
        bottom: 0;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].TnppnRp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 5%; text-align:left;"> TOTAL </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].TnnetRp).toFixed(2))}</div>
    </div>`};

     tempPrintStr += `
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:10px;">
       <div>Disetujui Oleh</div>

       <div style="height:60px;"></div>

       <div style="font-size:10px;">
        `+ns_dataPrint[0].otouser+`
       </div>
     <div style="font-size:10px;">
        ELECTRONICALLY APPROVED
     </div>
      </div>

        <div style="width:50%; float:left; font-size:10px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

        <!-- HEADER -->
        <tr style="height:20px;">
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Konfirmasi Supplier
          </td>
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Estimasi Tgl. Kirim
          </td>
        </tr>

        <!-- ROW NAMA -->
        <tr style="height:50px;">
          <td style="border:1px solid black; width:50%; font-size:10px; vertical-align: bottom;">
            Nama
          </td>

          <!-- KOLOM KANAN -->
          <td rowspan="2" style="border:1px solid black; height:60px; position:relative;">

            <div style="position:absolute; bottom:5px; left:6px; font-style:italic; font-size:10px; vertical-align: bottom;">
              *wajib isi
            </div>

          </td>
        </tr>

        <!-- ROW TANGGAL -->
        <tr style="height:20px;">
          <td style="border:1px solid black; font-size:10px;">
            Tanggal
          </td>
        </tr>

      </table>
    </div>

    <div style="clear: both;"></div>
  </div>

</div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    let w = window.open(' ');

    w.document.open();
    w.document.write(tempPrintStr);
    w.document.close();

    setTimeout(() => {
      w.focus();
      w.print();
      }, 300);
    }






function ns_submitPrint (nobukti) {

    let _token = $('#_token').val()

  let namaTtdCetak = ''

  // const options = ['EVY YUSIA', 'JULIA', 'DESTI']

  // const overlay = document.createElement('div')
  // overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;'

  // overlay.innerHTML = `
  //   <div style="background:#fff;padding:24px;border-radius:8px;min-width:320px;font-family:sans-serif;font-size:14px;">
  //     <h3 style="margin:0 0 16px;">Cetak Purchase Order</h3>
  //     <label style="display:block;margin-bottom:6px;">Ditandatangani oleh :</label>
  //     <select id="selectNamaTtd" style="width:100%;padding:6px;font-size:14px;border:1px solid #ccc;border-radius:4px;">
  //       <option value="">-- Pilih --</option>
  //       ${options.map(n => `<option value="${n}">${n}</option>`).join('')}
  //     </select>
  //     <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;">
  //       <button id="btnBatalTtd" style="padding:6px 16px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;">Batal</button>
  //       <button id="btnLanjutTtd" style="padding:6px 16px;background:#333;color:#fff;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
  //     </div>
  //   </div>
  // `

  // document.body.appendChild(overlay)

  // document.getElementById('btnBatalTtd').onclick = () => document.body.removeChild(overlay)

  // document.getElementById('btnLanjutTtd').onclick = () => {
  //   namaTtdCetak = document.getElementById('selectNamaTtd').value
  //   document.body.removeChild(overlay)
  

    $.ajax({
      url: "{!! url('purchaseorderprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        ns_dataPrint = res

        console.log(ns_dataPrint)
        
      }
    })
  
    let arrayDataPrint = []
    for (let i = 0; i < ns_dataPrint.length; i+=7) 
    {
      let tempArray = ns_dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = ns_dataPrint[0].tanggal.split(' ')[0];

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .border-bottom {
        border: bottom;
      }
      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. PRAMUKA NO. 63 RT. 11 BANJARMASIN 70249</div>
                        <div class="pb-1" style="width: 100%">TELP : 0511 - 3269593 | FAX : 0511 - 3272142</div>
                        <div class="pb-1" style="width: 100%">E-Mail : spl@indo.net</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].NAMA+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].ALAMAT1+`</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+ns_dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+tanggalOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+ns_dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Mata Uang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+ns_dataPrint[0].KODEVLS+`</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">KODE BRG</td>
                    <td class="text-center" style="width: 5%">MERK</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 5%">HARGA</td>
                    <td class="text-center" style="width: 5%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0
    
    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NAMABRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.NAMAMERK ?? ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${ns_formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${ns_formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows � table is 225px, each row ~24px, header ~24px = ~8 total slots
const ns_maxRows = 7;
const ns_fillerCount = Math.max(0, ns_maxRows - item.length);
for (let f = 0; f < ns_fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 38%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SARANA PRIMA LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].Expedisi+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+ns_dataPrint[0].almkirim+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
    </div>
    <div style="display: flex; flex-direction: column; width: 100%">
      <div class="pb-1">CV. SARANA PRIMA LESTARI</div>
      <div class="pb-1">JL. PRAMUKA NO. 63 RT. 11, BANJARMASIN - 70249</div>
      <div class="pb-1">UP. IBU FITRI</div>
      <div class="pb-1">E-Mail: finance@saranaprimalestari.com</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 20%">USER : </div>
      <div class="pb-1" style="width: 80%">`+ns_dataPrint[0].Iduser+`</div>
    </div>
  </div>

  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> Jumlah </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].tsub).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DISKON </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].Tdisc).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> SUBTOTAL </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].ndpprp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> PPN 10% </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].TnppnRp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 60% margin-left:auto"> TOTAL </div>
      <div style="width: 30%; text-align: right">${ns_formatAngka(parseFloat(ns_dataPrint[0].TnnetRp).toFixed(2))}</div>
    </div>

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui Oleh</td>
        <td class="no-border text-center" style="width: 33%; font-size:13px;">Konfirmasi Supplier</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2 text-center">
          <p class="m-0" style='font-size:12px;'>`+ns_dataPrint[0].otouser+`</p>
        </td>







        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tanggal</p>
        </td>
      </tr>
    </table>
  </div>

</div>

         <div class="footer-print-date" style='margin-bottom:-100px;'>
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px ">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
             </tr>
           </table>
         </div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }
  // }

</script>
{{-- Dulu ada skrip manual "hover tab" (ubah inline style backgroundColor tiap tab
     diklik) untuk mewarnai 3 tab. Tabnya sekarang tinggal 2, dan warnanya sudah
     ditangani `.custom-tabs .nav-link.active` (@section('css')) lewat class yang
     dipasang otomatis oleh plugin tab Bootstrap - skrip manual itu dibuang, bukan
     cuma jadi tidak perlu tapi juga akan error (nav-profile1-tab sudah tidak ada). --}}
<script>
  // autoPick hanya true kalau dipanggil dari Enter. Menekan tombol browse berarti user
  // memang ingin melihat daftarnya, jadi modal selalu dibuka - tetap terfilter kalau
  // kolom kode barang sudah terisi.
  //
  // Penerapan filter ke DataTables sekarang dilakukan di dalam cabang masing-masing di
  // ns_buttonAddAddListBarang(), yang tahu persis ns_tabel mana miliknya sesuai pilihan
  // dropdown "+ Dari".
  function ns_performSearch (autoPick = false) {
    const searchValue = document.getElementById('ns_input_add_add_kodebarang').value.trim();

    if (autoPick && !searchValue) {
      alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.")
      return
    }

    ns_buttonAddAddListBarang({ keyword: searchValue, autoPick: autoPick });
  }

  // Keyboard event
  document.getElementById('ns_input_add_add_kodebarang').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        ns_performSearch(true);
    }
  });

  // window.onload milik halaman PO gabungan (loadAll daftar PO) - tidak ditimpa di sini.

</script>
