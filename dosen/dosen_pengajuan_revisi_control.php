<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if ($status !== 'BERKAS DITOLAK') {
		nx_done("dosen_pengajuan_riwayat.php?nip_dosen=" . rawurlencode($nip_dosen), 'error', 'Pengajuan ini tidak sedang menunggu revisi.');
	}
	$sisa = (int) $koneksi->GetOne("SELECT COUNT(*) FROM berkas_pak WHERE id_pengajuan = ? AND status_berkas = 'TOLAK'", array($id_pengajuan));
	if ($sisa === 0) {
		$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'TELAH REVISI' WHERE id_pengajuan = ? AND status_pengajuan = 'BERKAS DITOLAK'", array($id_pengajuan));
		nx_done("dosen_pengajuan_riwayat_telah_revisi.php" . $q, 'success', 'Revisi dikirim ulang ke bagian kepegawaian.');
	}
	nx_done("dosen_pengajuan_revisi_berkas.php" . $q, 'error', 'Masih ada ' . $sisa . ' berkas yang perlu diperbaiki.');
