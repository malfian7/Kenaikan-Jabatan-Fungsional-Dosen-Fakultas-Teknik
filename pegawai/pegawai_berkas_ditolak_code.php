<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if (!in_array($status, array('TERKIRIM', 'TELAH REVISI'), true)) {
		nx_done("pegawai_data_pengajuan_berkas_diterima.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Pengajuan ini tidak sedang menunggu pemeriksaan.');
	}
	$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'BERKAS DITOLAK', nip_pegawai = ? WHERE id_pengajuan = ?", array($nip_pegawai, $id_pengajuan));
	header("location:pegawai_data_pengajuan_berkas_ditolak.php?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan));
