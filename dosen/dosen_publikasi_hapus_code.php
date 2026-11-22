<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$id_publikasi = isset($_GET['id_publikasi']) ? $_GET['id_publikasi'] : '';
	$balik = "dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "#publikasi";

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Publikasi hanya dapat dihapus selama pengajuan belum dikirim.');
	}
	$koneksi->Execute("DELETE FROM publikasi WHERE id_publikasi = ? AND id_pengajuan = ?", array($id_publikasi, $id_pengajuan));
	nx_done($balik, 'success', 'Publikasi dihapus.');
