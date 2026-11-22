<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	$file_sk = nx_clean_link(nx_input('file_sk'));
	if ($status !== 'PENILAIAN SELESAI') {
		nx_done("pegawai_data_pengajuan_selesai.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Keputusan hanya dapat diberikan setelah penilaian selesai.');
	}
	if ($file_sk === '') {
		nx_done("pegawai_persetujuan.php" . $q, 'error', 'Tautan berkas SK tidak valid. Tempelkan link Google Drive atau URL lengkap.');
	}
	$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'DITERIMA', file_sk = ?, nip_pegawai = ? WHERE id_pengajuan = ? AND status_pengajuan = 'PENILAIAN SELESAI'", array($file_sk, $nip_pegawai, $id_pengajuan));
	nx_done("pegawai_data_pengajuan_selesai.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'success', 'Pengajuan diterima dan berkas SK tersimpan.');
