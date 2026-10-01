<style>
.posting-sidebar {
  position: fixed;
  top: 77px;
  right: 0;
  left: auto;
  height: calc(100vh - 60px);
  width: 280px;
  background-color: #f8f9fa;
  border-left: 1px solid #dee2e6;
  border-right: none;
  z-index: 1000;
  box-shadow: -2px 0 5px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease; /* enables the collapse slide */
}

.posting-sidebar.collapsed {
  transform: translateX(100%); /* slides fully off-screen to the right */
}

/* Toggle tab — stays fixed to the screen edge regardless of collapsed state,
   so there's always a way to bring the sidebar back. Its own position
   shifts with the sidebar via the .collapsed rule below it. */
.posting-sidebar-toggle {
  position: fixed;
  top: 50%;
  right: 280px;
  transform: translateY(-50%);
  z-index: 1001;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-right: none;
  border-radius: 8px 0 0 8px;
  width: 22px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: -2px 0 5px rgba(0,0,0,0.1);
  transition: right 0.3s ease;
}

.posting-sidebar-toggle:hover {
  background-color: #e9ecef;
}

.posting-sidebar-toggle.collapsed {
  right: 0; /* tab sticks to the very edge once the sidebar is hidden */
}

.posting-sidebar-toggle svg {
  width: 14px;
  height: 14px;
  color: #495057;
  transition: transform 0.3s ease;
}

.posting-sidebar-toggle.collapsed svg {
  transform: rotate(180deg); /* arrow flips to point the other way */
}

/* STICKY TITLE SECTION */
.posting-sidebar-header {
  position: sticky;
  top: 0;
  background-color: #f8f9fa;
  padding: 20px 15px 10px 15px;
  border-bottom: 1px solid #dee2e6;
  z-index: 10;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.posting-sidebar-header h3 {
  color: #495057;
  margin: 0;
  font-weight: 600;
  font-size: 1.25rem;
}

/* SCROLLABLE CONTENT SECTION */
.posting-sidebar-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px 15px;
}

.posting-sidebar .btn {
  width: 100%;
  margin-bottom: 10px;
  text-align: left;
  border-radius: 8px;
  padding: 12px 15px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.posting-sidebar .btn:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.posting-sidebar .btn i {
  margin-right: 8px;
  width: 16px;
}

.posting-main-content {
    margin-left: 0;
    margin-right: 280px;
    margin-top: 60px;
    padding: 20px;
    min-height: calc(100vh - 60px);
}

.posting-content-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  padding: 30px;
  text-align: center;
}

.posting-welcome-text {
  color: #6c757d;
  font-size: 18px;
  margin-bottom: 20px;
}

@media (max-width: 768px) {
  .posting-sidebar {
    width: 250px;
    top: 60px;
    height: calc(100vh - 60px);
    transform: translateX(100%); /* fixed: right-anchored sidebar hides to the RIGHT, not left */
    transition: transform 0.3s ease;
  }

  .posting-sidebar.active {
    transform: translateX(0);
  }

  .posting-sidebar.active.collapsed {
    transform: translateX(100%); /* collapsed always wins over active on mobile */
  }

  .posting-sidebar-toggle {
    right: 250px;
  }

  .posting-sidebar-toggle.collapsed {
    right: 0;
  }

  .posting-main-content {
    margin-right: 0;
    margin-top: 60px;
  }

  .posting-mobile-menu-btn {
    position: fixed;
    top: 70px;
    right: 20px; /* moved to right to match the sidebar side */
    z-index: 1001;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  }
}
</style>

<!-- Collapse Toggle Tab -->
<div class="posting-sidebar-toggle" id="postingSidebarToggle" onclick="togglePostingSidebar()">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <polyline points="9 18 15 12 9 6"></polyline>
  </svg>
</div>

<!-- Posting Sidebar Component -->
<div class="posting-sidebar" id="postingSidebar">
  <!-- STICKY HEADER SECTION -->
  <div class="posting-sidebar-header">
    <h3 id='judulPosting'>Master Set Posting</h3>
  </div>

  <!-- SCROLLABLE CONTENT SECTION -->
  <div class="posting-sidebar-content" id="postingSidebarContent">
    <!-- Posting Section -->
    <div class="mb-4">
      <h6 class="text-muted mb-3">Posting Utama</h6>
      <button type="button" class="btn btn-success" onclick="pagePostingKas()">
        <i class="bi bi-cash"></i> Posting Kas
      </button>
      <button type="button" class="btn btn-success" onclick="pagePostingBank()">
        <i class="bi bi-bank"></i> Posting Bank
      </button>
      <button type="button" class="btn btn-success" onclick="pagePostingAkumulasi()">
        <i class="bi bi-arrow-up-circle"></i> Posting Akumulasi
      </button>
      <button type="button" class="btn btn-success" onclick="pagePostingAktiva()">
        <i class="bi bi-building"></i> Posting Aktiva
      </button>
      <button type="button" class="btn btn-success" onclick="pagePostingHargaPokok()">
        <i class="bi bi-calculator"></i> Posting Harga Pokok
      </button>
    </div>

    <!-- Financial Section -->
    <div class="mb-4">
      <h6 class="text-muted mb-3">Keuangan</h6>
      <button type="button" class="btn btn-primary" onclick="pagePostingHutang()">
        <i class="bi bi-arrow-down-circle"></i> Posting Hutang
      </button>
      <button type="button" class="btn btn-primary" onclick="pagePostingPiutang()">
        <i class="bi bi-arrow-up-circle"></i> Posting Piutang
      </button>
      <button type="button" class="btn btn-primary" onclick="pagePostingDeposito()">
        <i class="bi bi-piggy-bank"></i> Posting Deposito
      </button>
    </div>

    <!-- Uang Muka Section -->
    <div class="mb-4">
      <h6 class="text-muted mb-3">Uang Muka Hutang & Piutang</h6>
      <button type="button" class="btn btn-warning" onclick="pagePostingUMHutang()">
        <i class="bi bi-arrow-down"></i> Posting UM Hutang
      </button>
      <button type="button" class="btn btn-warning" onclick="pagePostingUMPiutang()">
        <i class="bi bi-arrow-up"></i> Posting UM Piutang
      </button>
    </div>

    <!-- Temporary Section -->
    <div class="mb-4">
      <h6 class="text-muted mb-3">Hutang dan Piutang Sementara</h6>
      <button type="button" class="btn btn-secondary" onclick="pagePostingHutangSementara()">
        <i class="bi bi-clock"></i> Posting Hutang Sementara
      </button>
      <button type="button" class="btn btn-secondary" onclick="pagePostingPiutangSementara()">
        <i class="bi bi-clock-history"></i> Posting Piutang Sementara
      </button>
    </div>

    <!-- Giro Section -->
    <div class="mb-4">
      <h6 class="text-muted mb-3">Giro</h6>
      <button type="button" class="btn btn-info" onclick="pagePostingGiroTerima()">
        <i class="bi bi-receipt"></i> Posting Giro Terima
      </button>
      <button type="button" class="btn btn-info" onclick="pagePostingGiroBuka()">
        <i class="bi bi-file-earmark-text"></i> Posting Giro Buka
      </button>
    </div>

    <div class="mb-4">
      <h6 class="text-muted mb-3">Rugi dan Laba</h6>
      <button type="button" class="btn btn-dark" onclick="pagePostingRLTahunLalu()">
        <i class="bi bi-file-text"></i> RL Tahun Lalu
      </button>
      <button type="button" class="btn btn-dark" onclick="pagePostingRLTahunIni()">
        <i class="bi bi-file-text"></i> RL Tahun Ini
      </button>
      <button type="button" class="btn btn-dark" onclick="pagePostingRLBulanLalu()">
        <i class="bi bi-file-text"></i> RL Bulan Lalu
      </button>
    </div>

    <div class="mb-4">
      <h6 class="text-muted mb-3">Lain-Lain</h6>
      <button type="button" class="btn btn-danger" onclick="pagePostingSelisih()">
        <i class="bi bi-plus-slash-minus"></i> Selisih
      </button>
    </div>

    <div class="mb-4">
      <h6 class="text-muted mb-3">PPN & PPH</h6>
      <button type="button" class="btn btn-outline-success" onclick="pagePostingPPNMasukan()">
        <i class="bi bi-node-plus"></i> PPN Masukan
      </button>
      <button type="button" class="btn btn-outline-success" onclick="pagePostingPPNKeluaran()">
        <i class="bi bi-node-plus-fill"></i> PPN Keluaran
      </button>
      <button type="button" class="btn btn-outline-success" onclick="pagePostingPPHMasukan()">
        <i class="bi bi-piggy-bank"></i> PPH Masukan
      </button>
      <button type="button" class="btn btn-outline-success" onclick="pagePostingPPHKeluaran()">
        <i class="bi bi-piggy-bank-fill"></i> PPH Keluaran
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const postingSidebarContent = document.getElementById('postingSidebarContent');
  const SCROLL_STORAGE_KEY = 'posting_sidebar_scroll_position';

  // Restore scroll position when page loads
  function restoreScrollPosition() {
    const savedScrollPosition = sessionStorage.getItem(SCROLL_STORAGE_KEY);
    if (savedScrollPosition && postingSidebarContent) {
      postingSidebarContent.scrollTop = parseInt(savedScrollPosition, 10);
    }
  }

  // Save scroll position
  function saveScrollPosition() {
    if (postingSidebarContent) {
      sessionStorage.setItem(SCROLL_STORAGE_KEY, postingSidebarContent.scrollTop.toString());
    }
  }

  // Listen for scroll events and save position
  if (postingSidebarContent) {
    postingSidebarContent.addEventListener('scroll', function() {
      clearTimeout(postingSidebarContent.scrollTimer);
      postingSidebarContent.scrollTimer = setTimeout(saveScrollPosition, 100);
    });
  }

  // Save scroll position before page unloads
  window.addEventListener('beforeunload', saveScrollPosition);

  restoreScrollPosition();
  setTimeout(restoreScrollPosition, 100);

  // Restore collapsed/expanded state
  restorePostingSidebarCollapseState();
});

// Enhanced navigation functions that save scroll position before redirect
function navigateWithScrollSave(url) {
  const postingSidebarContent = document.getElementById('postingSidebarContent');
  if (postingSidebarContent) {
    sessionStorage.setItem('posting_sidebar_scroll_position', postingSidebarContent.scrollTop.toString());
  }
  window.location.href = url;
}

// ── Collapse / expand ────────────────────────────────────────────────
const POSTING_SIDEBAR_COLLAPSE_KEY = 'posting_sidebar_collapsed';

function togglePostingSidebar() {
  const sidebar = document.getElementById('postingSidebar');
  const toggle  = document.getElementById('postingSidebarToggle');
  if (!sidebar || !toggle) return;

  const collapsed = sidebar.classList.toggle('collapsed');
  toggle.classList.toggle('collapsed', collapsed);
  sessionStorage.setItem(POSTING_SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
}

function restorePostingSidebarCollapseState() {
  const sidebar = document.getElementById('postingSidebar');
  const toggle  = document.getElementById('postingSidebarToggle');
  if (!sidebar || !toggle) return;

  const collapsed = sessionStorage.getItem(POSTING_SIDEBAR_COLLAPSE_KEY) === '1';
  sidebar.classList.toggle('collapsed', collapsed);
  toggle.classList.toggle('collapsed', collapsed);
}

function pagePostingKas() {
  navigateWithScrollSave("{{'mastersetpostingkas'}}");
}

function pagePostingBank() {
  navigateWithScrollSave("{{'mastersetpostingbank'}}");
}

function pagePostingGiroBuka() {
  navigateWithScrollSave("{{'mastersetpostinggirobuka'}}");
}

function pagePostingGiroTerima() {
  navigateWithScrollSave("{{'mastersetpostinggiroterima'}}");
}

function pagePostingHutang() {
  navigateWithScrollSave("{{'mastersetpostinghutang'}}");
}

function pagePostingPiutang() {
  navigateWithScrollSave("{{'mastersetpostingpiutang'}}");
}

function pagePostingAkumulasi() {
  navigateWithScrollSave("{{'mastersetpostingakumulasi'}}");
}

function pagePostingAktiva() {
  navigateWithScrollSave("{{'mastersetpostingaktiva'}}");
}

function pagePostingHargaPokok() {
  navigateWithScrollSave("{{'mastersetpostinghargapokok'}}");
}

function pagePostingDeposito() {
  navigateWithScrollSave("{{'mastersetpostingdeposito'}}");
}

function pagePostingUMHutang() {
  navigateWithScrollSave("{{'mastersetpostingumhutang'}}");
}

function pagePostingUMPiutang() {
  navigateWithScrollSave("{{'mastersetpostingumpiutang'}}");
}

function pagePostingHutangSementara() {
  navigateWithScrollSave("{{'mastersetpostinghutangsementara'}}");
}

function pagePostingPiutangSementara() {
  navigateWithScrollSave("{{'mastersetpostingpiutangsementara'}}");
}

function pagePostingRLTahunLalu() {
  navigateWithScrollSave("{{'mastersetpostingrltahunlalu'}}");
}

function pagePostingRLTahunIni() {
  navigateWithScrollSave("{{'mastersetpostingrltahunini'}}");
}

function pagePostingRLBulanLalu() {
  navigateWithScrollSave("{{'mastersetpostingrlbulanlalu'}}");
}

function pagePostingSelisih() {
  navigateWithScrollSave("{{'mastersetpostingselisih'}}");
}

function pagePostingPPNMasukan() {
  navigateWithScrollSave("{{'mastersetpostingppnmasukan'}}");
}

function pagePostingPPNKeluaran() {
  navigateWithScrollSave("{{'mastersetpostingppnkeluaran'}}");
}

function pagePostingPPHMasukan() {
  navigateWithScrollSave("{{'mastersetpostingpphmasukan'}}");
}

function pagePostingPPHKeluaran() {
  navigateWithScrollSave("{{'mastersetpostingpphkeluaran'}}");
}

// Optional: Clear scroll position when user manually scrolls to top
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'Home') {
    sessionStorage.removeItem('posting_sidebar_scroll_position');
  }
});
</script>