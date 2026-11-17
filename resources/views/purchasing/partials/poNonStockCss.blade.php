{{-- DIBANGKITKAN dari @section('css') purchaseOrderNonStock.blade.php: hanya aturan yang menunjuk
     elemen form/modal PO Non Stock di halaman PO gabungan, id bentrok sudah diberi awalan ns_. --}}
<style>
#ns_formBsGrid textarea.form-control,
#ns_formBsGrid table .form-control {
    height: auto;
}

#ns_formBsGrid .btn:has(> .bi-search) {
    height: 38px !important;
}

#ns_formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
    padding-top: 0;
    padding-bottom: 0;
}

#ns_tabel_add td:last-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: 13px;
    border: 1px solid transparent;
    box-shadow: none;
    transition: all .12s ease;
}

#ns_tabel_add td:last-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
}

#ns_tabel_add td:last-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#ns_tabel_add td:last-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#ns_tabel_add td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#ns_tabel_add td:last-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#ns_tabel_add td:last-child .btn-info { color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

#ns_tabel_add thead th,
#ns_tabel_detail thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
}

#ns_tabel_add_harga_terakhir thead th {
    background: #e7f7ed !important;
    color: #16a34a !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #cdebd7;
    border-top: none;
}

#ns_tabel_add_harga_terakhir tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
}

#ns_tabel_add_harga_terakhir tbody tr:hover {
    background-color: #f2fbf5;
}

#ns_tabel_add_stock_proyeksi thead th {
    background: #fdeaea !important;
    color: #dc2626 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #f7cfcf;
    border-top: none;
}

#ns_tabel_add_stock_proyeksi tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
}

#ns_tabel_add_stock_proyeksi tbody tr:hover {
    background-color: #fef6f6;
}

#ns_tabel_data_header th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
}

#ns_tabel_add tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
}

#ns_tabel_add tbody tr:hover {
    background-color: #f5f3ff;
}

#ns_input_search_barang_all {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
}
</style>
