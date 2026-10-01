
let tabelFilterTimeout;
$("#tabel_filter_visual").on("keyup", function () {
  let value = this.value;
  clearTimeout(tabelFilterTimeout);
  tabelFilterTimeout = setTimeout(function () {
    $("#tabel").DataTable().search(value).draw();
  }, 400);
});

$(document).on("change", "#tabel_length_visual", function () {
  $("#tabel").DataTable().page.len(Number(this.value)).draw();
});
