<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$balik = "dosen_pengajuan_riwayat.php?nip_dosen=" . rawurlencode($nip_dosen);

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Hanya pengajuan yang belum dikirim yang dapat dihapus.');
	}
	$koneksi->Execute("DELETE FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ? AND status_pengajuan = 'BELUM DIKIRIM'", array($id_pengajuan, $nip_dosen));
	nx_done($balik, 'success', 'Pengajuan dihapus.');
