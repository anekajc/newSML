# Catatan Update DB - Gudang (DBSMLBARU)

Menu Gudang newSML disamakan dengan newProgramAJC (2026-10-14). Controller, view, dan `routes/gudang.php` disalin dari newProgramAJC, termasuk menu baru Closing Perintah Opname. Keputusan user: gudang ikut newProgramAJC (versi terbaru), termasuk SP-nya.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 16 | `16_menu_closing_perintah_opname.sql` | Isi href `closingperintahopname` untuk menu 06022 di DBMENUWEB (sebelumnya NULL). | Sudah dijalankan 2026-10-14 |
| 18 | `18_sp_gudang_ikut_ajc.sql` | `SP_UBAHKEMASAN`, `vwDetailUbahKemasan`, `sp_CetakSerahSample`, `SP_InsertPRTRANSFER`, `vwMasterKoreksi` = isi DBSMLNEW. Kolom lama DBSMLBARU tetap ada (`HargaIn`, `HPP2`, `NOTE`). Refresh `VwReportUbahKemasanBahan`. | Sudah dijalankan 2026-10-14 |

## Catatan

- Hak akses 06022 untuk user (termasuk SA) belum dicentang di DBSMLBARU. Hak akses diatur sendiri lewat Berkas > Pemakai.
- Ubah kemasan: versi DBSMLBARU dulu menyimpan HPP dibagi ISI, dan view-nya mengalikan lagi dengan ISI. Versi DBSMLNEW tidak melakukan keduanya. Ubah kemasan lama yang disimpan dengan cara lama akan tampil HPP per satuan dasar.
- `vwOutPPL` dan `VwPPL` juga berbeda dengan DBSMLNEW, tapi dipakai purchasing/marketing, jadi tidak diubah.
