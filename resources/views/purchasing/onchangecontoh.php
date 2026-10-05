

<select onchange="onchangeKategori()" id="input_kategori">

  <option value=0 selected disabled>--Pilih Kategori--</option>
  // list awal (pakai loop dari laravel) value kalau bisa id / yang digunakkan untuk where field di table parentnya
  <option value=1 >Kat 1</option>
  <option value=2 >Kat 2</option>
</select>

function onchangeKategori () {
  // ambil value dari select input_kategori
  let kodekategori = $("#input_kategori").val();

  // ajax untuk data subkategori
      // di controller mencari subkategori where parentId nya sesuai value kodekategori

      //
      //
      selectOptionKategori = "<option value=0 selected disabled>--Pilih Sub Kategori--</option>"
      res.forEach((item, i) => {
      //
      //  selectOptionKategori += `
      //            <option value=${item.SubKategoriId}> ${item.NamaSubKategori}</option>
      //
      //   `
      // });

      document.getElementById("input_subkategori").innerHTML = selectOptionKategori


  // reset semua input lain yang berhubungan

  document.getElementById("input_sub_subkategori").innerHTML = '<option value=0 selected disabled>--Pilih Sub-Sub Kategori--</option>'

  document.getElementById("input_xx").value = 0
  document.getElementById("input_yy").value = ''
  ...
