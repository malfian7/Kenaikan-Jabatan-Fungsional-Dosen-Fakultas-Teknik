<?php
/* Riwayat status pengajuan (diisi trigger database) dan notifikasi dalam aplikasi. */
include_once __DIR__ . '/nx_helpers.php';

/* "role:nip" -> "Nama (Peran)". Nilai lain ditampilkan sebagai "Sistem". */
function nx_nama_aktor($koneksi, $aktor)
{
	static $cache = array();
	if (isset($cache[$aktor])) { return $cache[$aktor]; }
	$hasil = 'Sistem';
	if (preg_match('/^(dosen|pegawai|penilai):([0-9A-Za-z]{1,25})$/', (string) $aktor, $m)) {
		$tabel = array('dosen' => array('dosen', 'nip_dosen', 'nama_dosen', 'Dosen'),
			'pegawai' => array('pegawai', 'nip_pegawai', 'nama_pegawai', 'Pegawai'),
			'penilai' => array('penilai', 'nip_penilai', 'nama_penilai', 'Penilai'));
		$t = $tabel[$m[1]];
		$nama = $koneksi->GetOne('SELECT ' . $t[2] . ' FROM ' . $t[0] . ' WHERE ' . $t[1] . ' = ?', array($m[2]));
		$hasil = ($nama ? $nama : $m[2]) . ' (' . $t[3] . ')';
	}
	return $cache[$aktor] = $hasil;
}

function nx_riwayat_baris($koneksi, $id_pengajuan)
{
	return (array) $koneksi->GetAll('SELECT waktu, status_lama, status_baru, aktor, peristiwa, catatan FROM riwayat_status WHERE id_pengajuan = ? ORDER BY waktu DESC, id_riwayat DESC', array($id_pengajuan));
}

function nx_riwayat_nada($status)
{
	if (in_array($status, array('DITERIMA', 'BERKAS DITERIMA', 'PENILAIAN SELESAI'), true)) { return 'ok'; }
	if (in_array($status, array('DITOLAK', 'BERKAS DITOLAK'), true)) { return 'bad'; }
	if ($status === 'TELAH REVISI') { return 'warn'; }
	return 'info';
}

function nx_riwayat_judul($r)
{
	switch ($r[4]) {
		case 'dibuat': return 'Pengajuan dibuat';
		case 'data_awal': return 'Status awal: ' . $r[2];
		case 'penilai_ditugaskan': return 'Penilai ditugaskan';
		case 'penilai_selesai': return 'Penilai selesai menilai';
		default: return ($r[1] ? $r[1] . ' → ' : '') . $r[2];
	}
}

function nx_riwayat_panel($koneksi, $id_pengajuan)
{
	$rows = nx_riwayat_baris($koneksi, $id_pengajuan);
	$peran = isset($_SESSION['role']) ? $_SESSION['role'] : '';
	$cetak = '';
	if ($peran === 'dosen' || $peran === 'pegawai') {
		$cetak = '<a class="btn btn-sm btn-light float-right nx-cetak-link" target="_blank" rel="noopener" href="' . nx_e($peran . '_pengajuan_cetak.php?nip_' . $peran . '=' . rawurlencode(isset($_SESSION['nip']) ? $_SESSION['nip'] : '') . '&id_pengajuan=' . rawurlencode($id_pengajuan)) . '"><i class="fas fa-print" aria-hidden="true"></i> Cetak lembar usulan</a>';
	}
	echo '<h1></h1><div class="card" id="riwayat"><div class="card-header bg-primary text-light"><b>RIWAYAT STATUS</b>' . $cetak . '</div><div class="card-body">';
	if (!$rows) { echo '<p class="text-muted">Belum ada riwayat.</p></div></div>'; return; }
	echo '<ol class="nx-tl">';
	foreach ($rows as $i => $r) {
		$nada = $r[4] === 'status' || $r[4] === 'data_awal' || $r[4] === 'dibuat' ? nx_riwayat_nada($r[2]) : 'info';
		echo '<li class="nx-tl-item is-' . $nada . '" style="--i:' . min($i, 12) . '"><span class="nx-tl-dot" aria-hidden="true"></span><div class="nx-tl-body">';
		echo '<div class="nx-tl-title">' . nx_e(nx_riwayat_judul($r)) . '</div>';
		if ($r[5] && $r[4] !== 'data_awal') { echo '<div class="nx-tl-note">' . nx_e($r[5]) . '</div>'; }
		echo '<div class="nx-tl-meta">' . nx_e(date('d/m/Y H:i', strtotime($r[0]))) . ' · oleh ' . nx_e(nx_nama_aktor($koneksi, $r[3])) . '</div>';
		echo '</div></li>';
	}
	echo '</ol></div></div>';
}
