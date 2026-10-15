<?php
$currentUri = trim(uri_string(), '/');

function isNavActive(string $currentUri, string $path): bool {
    $clean = trim($path, '/');
    return $currentUri === $clean || str_starts_with($currentUri, $clean . '/');
}

function navItem(string $path, string $label, string $icon, string $currentUri, ?string $badge = null): string {
    $active = isNavActive($currentUri, $path) ? 'active' : '';
    $b      = $badge ? "<span class='badge'>{$badge}</span>" : '';
    $url    = base_url($path);
    return "<a href='{$url}' class='nav-item {$active}'>{$icon} {$label}{$b}</a>";
}

function navDropdown(string $id, string $label, string $icon, array $items, string $currentUri): string {
    // Check if any child is active
    $isOpen = false;
    foreach ($items as [$path, $subLabel, $subBadge]) {
        if (isNavActive($currentUri, $path)) {
            $isOpen = true;
            break;
        }
    }
    $ariaExpanded = $isOpen ? 'true' : 'false';
    $collapseClass = $isOpen ? 'collapse show' : 'collapse';
    $btnClass = $isOpen ? 'nav-item nav-dropdown-toggle' : 'nav-item nav-dropdown-toggle collapsed';

    $html  = "<div class='nav-dropdown'>";
    $html .= "<button class='{$btnClass}' data-bs-toggle='collapse' data-bs-target='#{$id}' aria-expanded='{$ariaExpanded}'>";
    $html .= "{$icon} <span class='nav-dropdown-label'>{$label}</span>";
    $html .= "<svg class='nav-chevron' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7'/></svg>";
    $html .= "</button>";
    $html .= "<div id='{$id}' class='{$collapseClass}'><div class='nav-submenu'>";
    foreach ($items as [$path, $subLabel, $subBadge]) {
        $active = isNavActive($currentUri, $path) ? 'active' : '';
        $b      = $subBadge ? "<span class='badge'>{$subBadge}</span>" : '';
        $url    = base_url($path);
        $html  .= "<a href='{$url}' class='nav-subitem {$active}'>{$subLabel}{$b}</a>";
    }
    $html .= "</div></div></div>";
    return $html;
}

// SVG icons
$iDashboard = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>';

$iMasterData = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>';

$iUjian = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>';

$iAdministrasi = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>';

$iLog = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>';

$iSetting = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
?>

<nav class="nav-group">
  <div class="nav-group-label">Menu</div>
  <?= navItem('admin/dashboard', 'Dashboard', $iDashboard, $currentUri) ?>
</nav>

<nav class="nav-group">
  <div class="nav-group-label">Master Data</div>
  <?= navDropdown('ddMasterData', 'Master Data', $iMasterData, [
    ['admin/tahun-ajaran', 'Tahun Pelajaran', null],
    ['admin/kelas',        'Kelas',            null],
    ['admin/guru',        'Data Guru',        null],
    ['admin/siswa',       'Data Siswa',       null],
    ['admin/mapel',       'Mata Pelajaran',   null],
    ['admin/ruang',       'Ruang &amp; Sesi',     null],
  ], $currentUri) ?>
</nav>

<nav class="nav-group">
  <div class="nav-group-label">Ujian</div>
  <?= navDropdown('ddUjian', 'Ujian', $iUjian, [
    ['admin/jenis-ujian', 'Jenis Ujian', null],
    ['admin/ujian', 'Jadwal Ujian', null],
    ['admin/bank-soal', 'Bank Soal', null],
    ['admin/monitoring-soal', 'Monitoring Soal', null],
    ['admin/token', 'Token', null],
  ], $currentUri) ?>
</nav>

<nav class="nav-group">
  <div class="nav-group-label">Administrasi</div>
  <?= navDropdown('ddAdministrasi', 'Administrasi', $iAdministrasi, [
    ['admin/cetak/kartu', 'Cetak Kartu', null],
    ['admin/cetak/berita-acara', 'Cetak Berita Acara', null],
    ['admin/cetak/daftar-hadir', 'Cetak Daftar Hadir', null],
    ['admin/cetak/hasil-nilai', 'Cetak Hasil Nilai', null],
  ], $currentUri) ?>
</nav>

<nav class="nav-group">
  <div class="nav-group-label">Sistem</div>
  <?= navItem('admin/log', 'Log Aktivitas', $iLog, $currentUri) ?>
  <?= navItem('admin/pengaturan', 'Pengaturan', $iSetting, $currentUri) ?>
</nav>
