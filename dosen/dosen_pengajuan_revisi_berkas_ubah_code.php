<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$id_berkas    = isset($_GET['id_berkas']) ? $_GET['id_berkas'] : '';
	$balik  = "dosen_pengajuan_revisi_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$formul = "dosen_pengajuan_revisi_berkas_ubah.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "&id_berkas=" . rawurlencode($id_berkas);

	$bukti_berkas = nx_clean_link(nx_input('bukti_berkas'));
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));

	if ($status !== 'BERKAS DITOLAK') {
		nx_done($balik, 'error', 'Revisi hanya dapat dilakukan saat pengajuan dikembalikan.');
	}
	if ($bukti_berkas === '') {
		nx_done($formul, 'error', 'Tautan bukti tidak valid. Tempelkan link Google Drive atau URL lengkap.');
	}
	$koneksi->Execute("UPDATE berkas_pak SET bukti_berkas = ?, status_berkas = 'TELAH REVISI' WHERE id_berkas = ? AND id_pengajuan = ? AND status_berkas = 'TOLAK'", array($bukti_berkas, $id_berkas, $id_pengajuan));
	nx_done($balik, 'success', 'Revisi berkas disimpan.');
