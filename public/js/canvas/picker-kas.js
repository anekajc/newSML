/* picker-kas.js — inisialisasi DataTables untuk modal pemilih bergaya Kas
   (lihat public/css/picker-kas.css). Opsinya disalin dari kasInitPicker() di
   public/js/kas.js supaya dropdown "Tampilkan", kotak Search, info, dan pagination
   sama persis dengan modal pemilih di menu Kas.

   Pemakaian: isi <tbody> dulu, lalu panggil pickerKasInit('id_tabel').
   Baris yang bisa dipilih diberi class "pick-row" + onclick di <tr> (tanpa kolom
   Actions / tombol "+"). */
function pickerKasInit(idTabel, opsi) {
    var sel = '#' + idTabel;
    if ($.fn.DataTable.isDataTable(sel)) {
        // destroy() mengembalikan baris LAMA dari cache DataTables; simpan baris baru
        // yang barusan diisi pemanggil, pasang lagi setelah destroy.
        var $tbody = $(sel).children('tbody');
        var barisBaru = $tbody.children().detach();
        $(sel).DataTable().destroy();
        $tbody.empty().append(barisBaru);
    }
    // Baris placeholder ber-colspan ("Belum ada data") membuat DataTables error.
    if ($(sel + ' tbody td[colspan]').length) {
        $(sel + ' tbody').empty();
    }
    return $(sel).DataTable($.extend({
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
        paging: true,
        pageLength: 10,
        language: {
            lengthMenu: 'Tampilkan _MENU_',
            emptyTable: 'Tidak ada data',
            zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        }
    }, opsi || {}));
}
