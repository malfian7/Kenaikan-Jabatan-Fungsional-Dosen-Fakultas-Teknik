<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if ($status !== 'PENILAIAN SELESAI') {
		nx_done("pegawai_data_pengajuan_selesai.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Keputusan hanya dapat diberikan setelah penilaian selesai.');
	}
	$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'DITOLAK', nip_pegawai = ? WHERE id_pengajuan = ? AND status_pengajuan = 'PENILAIAN SELESAI'", array($nip_pegawai, $id_pengajuan));
	nx_done("pegawai_data_pengajuan_selesai.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'success', 'Pengajuan ditolak.');
