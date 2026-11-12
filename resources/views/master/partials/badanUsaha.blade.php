{{-- Dropdown Bentuk Usaha dari Master Badan Usaha (DBBADANUSAHA) untuk Master Customer,
     Supplier, dan Ekspedisi. Opsi <select> dibuat di view dengan:
       @include('master.partials.badanUsaha', ['opsiSaja' => true])
     dan script ini (BadanUsaha.*) dimuat sekali di section js dengan:
       @include('master.partials.badanUsaha')
     $listBadanUsaha dikirim controller (BadanUsahaTrait::listBadanUsaha).

     PPh21/PPh23 di form dikunci (readonly) dan selalu mengikuti master - di controller juga
     ditimpa lagi lewat BadanUsahaTrait::terapkanBadanUsaha. --}}
@if (!empty($opsiSaja))
  <option value="">Pilih Bentuk Usaha</option>
  @foreach ($listBadanUsaha ?? [] as $bu)
    <option value="{{ $bu->KodeUsaha }}" data-pph21="{{ $bu->PPH21 }}" data-pph23="{{ $bu->PPH23 }}">{{ $bu->KodeUsaha }}</option>
  @endforeach
@else
<script type="text/javascript">
window.BadanUsaha = {
  // Samakan nilai lama: 'PT. ' / 'pt.' -> 'PT' (data lama sering pakai titik/spasi).
  normal: function (v) {
    return String(v == null ? '' : v).trim().replace(/\.+$/, '').trim().toUpperCase()
  },

  // Isi PPh21/PPh23 dari opsi yang dipilih; kosong bila belum memilih.
  terapkan: function (sel, pph21, pph23) {
    let o = $(sel).find('option:selected')
    let ada = !!$(sel).val()
    $(pph21).val(ada ? (parseFloat(o.data('pph21')) || 0).toFixed(2) : '')
    $(pph23).val(ada ? (parseFloat(o.data('pph23')) || 0).toFixed(2) : '')
  },

  // Pilih nilai (mis. dari data lama) lalu terapkan PPh. Tidak ada di master -> kosong (wajib pilih).
  pilih: function (sel, nilai, pph21, pph23) {
    let cari = this.normal(nilai)
    let kode = ''
    $(sel).find('option').each(function () {
      if (this.value && BadanUsaha.normal(this.value) === cari) { kode = this.value; return false }
    })
    $(sel).val(kode)
    this.terapkan(sel, pph21, pph23)
  },

  // Kunci isian PPh dan ikat perubahan dropdown. Dipanggil sekali per form.
  pasang: function (sel, pph21, pph23) {
    $(pph21 + ',' + pph23).prop('readonly', true).attr('title', 'Mengikuti Master Badan Usaha')
    $(sel).off('change.badanUsaha').on('change.badanUsaha', function () { BadanUsaha.terapkan(sel, pph21, pph23) })
  },

  // Validasi sebelum simpan.
  cek: function (sel) {
    if (!$(sel).val()) {
      alertify.warning('Bentuk Usaha harus dipilih (data dari Master Badan Usaha)')
      return false
    }
    return true
  }
}
</script>
@endif
