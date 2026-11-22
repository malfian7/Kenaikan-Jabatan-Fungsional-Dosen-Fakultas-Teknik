<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);

	$p = $koneksi->GetRow("SELECT p.status_pengajuan, k.total_pak FROM pengajuan p INNER JOIN syarat_kum k ON k.id_kum = p.id_kum WHERE p.id_pengajuan = ? AND p.nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if (!$p || $p[0] !== 'BELUM DIKIRIM') {
		nx_done("dosen_pengajuan_riwayat.php?nip_dosen=" . rawurlencode($nip_dosen), 'error', 'Pengajuan ini sudah dikirim atau tidak dapat dikirim lagi.');
	}

	// Aturan kelayakan dihitung server (sumber yang sama dengan panel kelayakan); tidak dipercaya dari alamat/formulir.
	include_once("../include/nx_kelayakan.php");
	$kl = nx_kelayakan($koneksi, $id_pengajuan);
	if ($kl && $kl['bisa_kirim']) {
		$koneksi->Execute("UPDATE pengajuan SET tanggal_pengajuan = CURRENT_DATE, status_pengajuan = 'TERKIRIM', total_pak_akhir = ? WHERE id_pengajuan = ? AND status_pengajuan = 'BELUM DIKIRIM'", array((int) round($kl['total']), $id_pengajuan));
		nx_done("dosen_pengajuan_kirim_sukses.php" . $q, 'success', 'Pengajuan terkirim ke bagian kepegawaian.');
	}
	header("location:dosen_pengajuan_kirim_gagal.php" . $q);
