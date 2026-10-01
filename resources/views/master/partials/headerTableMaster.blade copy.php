
      <style>
      .sp-length-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
      }

      .sp-length-wrap label {
        margin: 0; /* stops default label margin from pushing the select down/over */
      }

      .sp-length-wrap select {
        width: auto; /* stops form-select from stretching full-width and forcing a wrap */
      }
    </style>
  
  
  <div class="sp-toolbar">
    <div class="sp-search-wrap">
      <i class="bi bi-search sp-search-icon"></i>
      <input type="text" id="tabel_filter_visual" placeholder="Cari user...">
    </div>

    <div class="sp-length-wrap">
      <label for="tabel_length_visual">Tampilkan</label>
      <select id="tabel_length_visual" class="form-select form-select-sm">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="-1">Semua</option>
      </select>
    </div>
  </div>