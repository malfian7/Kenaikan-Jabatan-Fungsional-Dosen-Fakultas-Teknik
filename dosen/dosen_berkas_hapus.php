<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$id_berkas    = isset($_GET['id_berkas']) ? $_GET['id_berkas'] : '';
	$balik = "dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Berkas hanya dapat dihapus selama pengajuan belum dikirim.');
	}
	$koneksi->Execute("DELETE FROM berkas_pak WHERE id_berkas = ? AND id_pengajuan = ?", array($id_berkas, $id_pengajuan));
	nx_done($balik, 'success', 'Berkas dihapus dari pengajuan.');
