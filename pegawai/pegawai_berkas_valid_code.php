<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	$id_berkas = isset($_GET['id_berkas']) ? $_GET['id_berkas'] : '';
	if (!in_array($status, array('TERKIRIM', 'TELAH REVISI'), true)) {
		nx_done("pegawai_data_pengajuan_berkas_diterima.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Pengajuan ini tidak sedang menunggu pemeriksaan.');
	}
	$koneksi->Execute("UPDATE berkas_pak SET status_berkas = 'VALID', catatan_berkas = '' WHERE id_berkas = ? AND id_pengajuan = ?", array($id_berkas, $id_pengajuan));
	header("location:pegawai_pengajuan_periksa.php" . $q);
