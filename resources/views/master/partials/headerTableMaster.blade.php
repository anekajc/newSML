  <style>
    .sp-length-wrap {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }    
    
    .sp-filter-wrap {
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

    .sp-toolbar {
      display: flex;
      flex-wrap: wrap; /* lets controls drop to a new line on narrow screens instead of overflowing */
      align-items: center;
      row-gap: 10px;
      column-gap: 12px; /* controls the tight spacing between search and the dropdown next to it */
    }

    .sp-filter-wrap select {
      width: auto;
      min-width: 220px; /* keeps "Hutang Usaha (21201)" from getting clipped */
    }

    .sp-length-wrap {
      margin-left: auto; /* pushes Tampilkan to the far right, away from the search+filter group */
    }
  </style>

  <div class="sp-toolbar">
    <div class="sp-search-wrap">
      <i class="bi bi-search sp-search-icon"></i>
      <input type="text" id="tabel_filter_visual" placeholder="Cari user...">
    </div>

    <div class="sp-filter-wrap">
      <label for="tabel_length_visual">Tampilkan</label>
      <select id="tabel_length_visual" class="form-select form-select-sm">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="-1">Semua</option>
      </select>
    </div>

    <div class="sp-length-wrap">
      <button id='AddVisibility' class="btn btn-action-primary" onclick="buttonAdd()">+ Add</button>
    </div>
  </div>