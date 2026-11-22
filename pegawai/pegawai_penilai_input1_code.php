<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	$nip_penilai = isset($_GET['nip_penilai']) ? $_GET['nip_penilai'] : '';
	if ($status !== 'BERKAS DITERIMA') {
		nx_done("pegawai_data_pengajuan_berkas_diterima.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Penilai hanya dapat ditentukan untuk pengajuan yang berkasnya sudah diterima.');
	}
	$ada = $koneksi->GetOne("SELECT 1 FROM penilai WHERE nip_penilai = ?", array($nip_penilai));
	if (!$ada) {
		nx_done("pegawai_penilai_input.php" . $q, 'error', 'Penilai tidak ditemukan.');
	}
	$lain = $koneksi->GetOne("SELECT nip_penilai_2 FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if ($lain !== false && rtrim((string) $lain, 'V') === $nip_penilai) {
		nx_done("pegawai_penilai_input.php" . $q, 'error', 'Penilai 1 dan penilai 2 harus orang yang berbeda.');
	}
	$koneksi->Execute("UPDATE pengajuan SET nip_penilai_1 = ? WHERE id_pengajuan = ?", array($nip_penilai, $id_pengajuan));
	nx_done("pegawai_penilai_input.php" . $q, 'success', 'Penilai 1 ditetapkan.');
