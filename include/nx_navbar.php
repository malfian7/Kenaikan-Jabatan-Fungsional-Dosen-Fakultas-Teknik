<?php
/*
 * Topbar bersama (tema Nexus).
 * Variabel yang dibaca dari file pemanggil:
 *   $nx_role    : 'dosen' | 'pegawai' | 'penilai'
 *   $nx_name    : nama pengguna
 *   $nx_profile : URL halaman profil
 *   $nx_logout  : URL halaman login/logout
 * Id #menu-toggle dan #navbarDropdown dipertahankan agar skrip lama tetap bekerja.
 */
$nx_name_safe = htmlspecialchars((string) $nx_name, ENT_QUOTES, 'UTF-8');
$nx_initial   = strtoupper(substr(trim((string) $nx_name), 0, 1));
if ($nx_initial === '') { $nx_initial = '?'; }
?>
<header class="nx-topbar">
	<button type="button" class="nx-icon-btn" id="menu-toggle" aria-label="Buka atau tutup menu" aria-controls="sidebar-wrapper" aria-expanded="true">
		<i class="fas fa-bars" aria-hidden="true"></i>
	</button>

	<nav class="nx-crumbs" aria-label="Lokasi halaman">
		<span class="nx-role"><?php echo ucfirst($nx_role); ?></span>
		<i class="fas fa-chevron-right nx-sep" aria-hidden="true"></i>
		<span class="nx-here" id="nx-here" aria-current="page"></span>
	</nav>

	<div class="nx-search" id="nx-search" role="search">
		<i class="fas fa-search" aria-hidden="true"></i>
		<input type="search" id="nx-search-input" placeholder="Cari di tabel…" aria-label="Cari di tabel" autocomplete="off" spellcheck="false">
		<kbd aria-hidden="true">/</kbd>
	</div>

	<div class="nx-user dropdown">
		<button type="button" class="nx-user-btn" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="nx-avatar" aria-hidden="true"><?php echo $nx_initial; ?></span>
			<span class="nx-user-meta">
				<span class="nx-user-name"><?php echo $nx_name_safe; ?></span>
				<span class="nx-user-role"><?php echo $nx_role; ?></span>
			</span>
			<i class="fas fa-chevron-down nx-chev" aria-hidden="true"></i>
		</button>
		<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
			<div class="nx-menu-head">
				<strong><?php echo $nx_name_safe; ?></strong>
				<span>Masuk sebagai <?php echo $nx_role; ?></span>
			</div>
			<a class="dropdown-item" href="<?php echo $nx_profile; ?>"><i class="fas fa-user-circle" aria-hidden="true"></i>Profil saya</a>
			<div class="dropdown-divider"></div>
			<a class="dropdown-item nx-danger" href="<?php echo $nx_logout; ?>"><i class="fas fa-sign-out-alt" aria-hidden="true"></i>Log out</a>
		</div>
	</div>
</header>
