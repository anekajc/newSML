{{-- Kolom NPWP/NIK (wajib & tidak boleh sama) di mastersupplier / mastercustomer / masterekspedisi.
     Pemakaian di view:
       npwpPasang('#input_add_npwp', url, () => $('#input_add_kode').val())   -- sekali saat halaman siap
       npwpReset('#input_add_npwp')       -- saat modal dibuka (Add)
       npwpCekSekarang('#input_edit_npwp') -- setelah nilai Edit diisi
       if (!npwpValid('#input_add_npwp')) return  -- di awal submit
     Pengecekan akhir tetap dilakukan server (NpwpTrait). --}}
<style>
  #formBsGrid .npwp-warn { display: none; color: #dc3545; font-size: 11px; line-height: 1.2; margin-bottom: 3px; }
  #formBsGrid input.npwp-dup, #formBsGrid input.npwp-dup:focus { border-color: #dc3545 !important; box-shadow: none; }
</style>
<script type="text/javascript">
  // Bersihkan dari titik, strip & spasi - sama dengan normalisasi di server.
  function npwpBersih (v) { return String(v || '').replace(/[.\-\s]/g, '') }

  function npwpTampil (sel, pesan) {
    let $in = $(sel)
    let $warn = $in.siblings('.npwp-warn')
    if (pesan) {
      $warn.text(pesan).show()
      $in.addClass('npwp-dup')
    } else {
      $warn.text('').hide()
      $in.removeClass('npwp-dup')
    }
  }

  function npwpReset (sel) {
    let $in = $(sel)
    $in.data('npwpDup', false)
    $in.data('npwpReq', ($in.data('npwpReq') || 0) + 1) // jawaban cek lama diabaikan
    npwpTampil(sel, '')
  }

  function npwpCekSekarang (sel) {
    let $in = $(sel)
    let cfg = $in.data('npwpCfg')
    let nilai = npwpBersih($in.val())
    let req = ($in.data('npwpReq') || 0) + 1
    $in.data('npwpReq', req)
    $in.data('npwpDup', false)

    if (!nilai) { npwpTampil(sel, ''); return }

    $.ajax({
      url: cfg.url,
      type: 'get',
      data: { npwp: $in.val(), kode: cfg.kode() || '' },
      success: function (res) {
        if ($in.data('npwpReq') !== req) return // sudah diketik lagi
        $in.data('npwpDup', !!res.ada)
        npwpTampil(sel, res.ada ? res.pesan : '')
      }
    })
  }

  function npwpPasang (sel, url, kodeFn) {
    let $in = $(sel)
    let timer = null
    $in.data('npwpCfg', { url: url, kode: kodeFn || function () { return '' } })
    $in.on('input', function () {
      clearTimeout(timer)
      timer = setTimeout(function () { npwpCekSekarang(sel) }, 400)
    })
  }

  // Dipanggil di awal submit: kosong / sudah ada -> pesan + bingkai merah, return false.
  function npwpValid (sel) {
    let $in = $(sel)
    if (!npwpBersih($in.val())) {
      npwpTampil(sel, 'NPWP/NIK wajib diisi')
      alertify.warning('NPWP/NIK wajib diisi')
      $in.trigger('focus')
      return false
    }
    if ($in.data('npwpDup')) {
      alertify.warning($in.siblings('.npwp-warn').text() || 'NPWP/NIK sudah ada')
      $in.trigger('focus')
      return false
    }
    return true
  }
</script>
