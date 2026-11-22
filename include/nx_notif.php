<?php
/* Lonceng notifikasi di topbar. Isi diisi trigger database; tiap peran hanya melihat miliknya. */
include_once __DIR__ . '/nx_helpers.php';

function nx_notif_tujuan($role)
{
	$t = array('dosen' => 'dosen_pengajuan_riwayat.php', 'pegawai' => 'pegawai_data_pengajuan.php', 'penilai' => 'penilai_daftar_pengajuan.php');
	return isset($t[$role]) ? $t[$role] : '';
}

function nx_notif_bell($koneksi, $role)
{
	if (empty($_SESSION['nip']) || !isset($_SESSION['role']) || $_SESSION['role'] !== $role || !$koneksi) { return; }
	$nip = $_SESSION['nip'];
	try {
		$belum = (int) $koneksi->GetOne('SELECT COUNT(*) FROM notifikasi WHERE peran = ? AND nip = ? AND dibaca = 0', array($role, $nip));
		$rows = (array) $koneksi->GetAll('SELECT id_notif, pesan, dibaca, waktu FROM notifikasi WHERE peran = ? AND nip = ? ORDER BY id_notif DESC LIMIT 8', array($role, $nip));
	} catch (Exception $e) {
		return;   // tabel belum ada: lonceng disembunyikan
	}
	$aksi = $role . '_notif_code.php';
	$label = $belum > 0 ? $belum . ' notifikasi belum dibaca' : 'Notifikasi';
	echo '<div class="nx-notif dropdown">';
	echo '<button type="button" class="nx-icon-btn nx-notif-btn" id="nx-notif-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="' . nx_e($label) . '">';
	echo '<i class="fas fa-bell" aria-hidden="true"></i>';
	if ($belum > 0) { echo '<span class="nx-notif-badge" aria-hidden="true">' . ($belum > 99 ? '99+' : $belum) . '</span>'; }
	echo '</button>';
	echo '<div class="dropdown-menu dropdown-menu-right nx-notif-menu" aria-labelledby="nx-notif-btn">';
	echo '<div class="nx-notif-head"><strong>Notifikasi</strong>';
	if ($belum > 0) {
		echo '<form action="' . nx_e($aksi) . '" method="post" class="nx-inline-form"><input type="hidden" name="aksi" value="baca_semua"><button type="submit" class="nx-notif-all">Tandai semua dibaca</button></form>';
	}
	echo '</div>';
	if (!$rows) {
		echo '<div class="nx-notif-empty">Belum ada notifikasi.</div>';
	} else {
		echo '<ul class="nx-notif-list">';
		foreach ($rows as $r) {
			echo '<li class="' . ($r[2] ? '' : 'is-new') . '"><form action="' . nx_e($aksi) . '" method="post" class="nx-inline-form">';
			echo '<input type="hidden" name="aksi" value="buka"><input type="hidden" name="id_notif" value="' . (int) $r[0] . '">';
			echo '<button type="submit" class="nx-notif-item"><span class="nx-notif-dot" aria-hidden="true"></span><span class="nx-notif-text">' . nx_e($r[1]) . '<small>' . nx_e(date('d/m/Y H:i', strtotime($r[3]))) . '</small></span></button>';
			echo '</form></li>';
		}
		echo '</ul>';
	}
	echo '</div></div>';
}

/* Pengendali POST: tandai dibaca. Dipanggil dari {peran}_notif_code.php. */
function nx_notif_aksi($koneksi, $role)
{
	$nip = isset($_SESSION['nip']) ? $_SESSION['nip'] : '';
	$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
	$tujuan = nx_notif_tujuan($role) . '?' . 'nip_' . $role . '=' . rawurlencode($nip);
	if ($aksi === 'baca_semua') {
		$koneksi->Execute('UPDATE notifikasi SET dibaca = 1 WHERE peran = ? AND nip = ? AND dibaca = 0', array($role, $nip));
		$balik = $tujuan;
		$ref = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
		$p = parse_url($ref);
		if ($p && isset($p['path']) && preg_match('/^[a-z_]+\.php$/', basename($p['path']))) {
			$q = isset($p['query']) ? $p['query'] : '';
			if ($q === '' || preg_match('/^[A-Za-z0-9_=&%.\-]{0,200}$/', $q)) { $balik = basename($p['path']) . ($q !== '' ? '?' . $q : ''); }
		}
		$tujuan = $balik;
	} elseif ($aksi === 'buka') {
		$id = isset($_POST['id_notif']) ? $_POST['id_notif'] : '';
		if (is_string($id) && preg_match('/^[0-9]{1,18}$/', $id)) {
			$koneksi->Execute('UPDATE notifikasi SET dibaca = 1 WHERE id_notif = ? AND peran = ? AND nip = ?', array($id, $role, $nip));
		}
	}
	header('Location: ' . $tujuan);
	exit;
}
