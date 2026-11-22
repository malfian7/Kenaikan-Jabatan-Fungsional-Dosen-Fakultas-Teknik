<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);

	$p = $koneksi->GetRow("SELECT p.status_pengajuan, k.total_pak FROM pengajuan p INNER JOIN syarat_kum k ON k.id_kum = p.id_kum WHERE p.id_pengajuan = ? AND p.nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if (!$p || $p[0] !== 'BELUM DIKIRIM') {
		nx_done("dosen_pengajuan_riwayat.php?nip_dosen=" . rawurlencode($nip_dosen), 'error', 'Pengajuan ini sudah dikirim atau tidak dapat dikirim lagi.');
	}

	// Total dan syarat dihitung server; tidak lagi dipercaya dari alamat/formulir.
	$total_pak_akhir = (float) $koneksi->GetOne("SELECT COALESCE(SUM(poin_akhir), 0) FROM berkas_pak WHERE id_pengajuan = ?", array($id_pengajuan));
	$syarat_pak      = (float) $p[1];

	if ($total_pak_akhir >= $syarat_pak) {
		$koneksi->Execute("UPDATE pengajuan SET tanggal_pengajuan = CURRENT_DATE, status_pengajuan = 'TERKIRIM', total_pak_akhir = ? WHERE id_pengajuan = ? AND status_pengajuan = 'BELUM DIKIRIM'", array((int) round($total_pak_akhir), $id_pengajuan));
		nx_done("dosen_pengajuan_kirim_sukses.php" . $q, 'success', 'Pengajuan terkirim ke bagian kepegawaian.');
	}
	header("location:dosen_pengajuan_kirim_gagal.php" . $q);
