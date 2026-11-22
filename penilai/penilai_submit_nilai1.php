<?php
	include_once("../koneksi/conn.php");
	$nip_penilai  = $_GET['nip_penilai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$balik = "penilai_data_berkas_penilai1.php?nip_penilai=" . rawurlencode($nip_penilai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);

	// Penanda 'V' di belakang NIP berarti penilai tersebut sudah menyelesaikan (mengunci) penilaiannya.
	$p = $koneksi->GetRow("SELECT status_pengajuan, nip_penilai_1 FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if (!$p || $p[1] !== $nip_penilai || $p[0] !== 'BERKAS DITERIMA') {
		nx_done("penilai_daftar_pengajuan_selesai.php?nip_penilai=" . rawurlencode($nip_penilai), 'error', 'Penilaian ini sudah diselesaikan atau bukan bagian Anda.');
	}
	$koneksi->Execute("UPDATE pengajuan SET nip_penilai_1 = ? WHERE id_pengajuan = ? AND nip_penilai_1 = ?", array($nip_penilai . 'V', $id_pengajuan, $nip_penilai));

	// Jika kedua penilai sudah selesai, buka kunci NIP dan tutup penilaian.
	$baris = $koneksi->GetRow("SELECT nip_penilai_1, nip_penilai_2 FROM pengajuan WHERE id_pengajuan = ? AND nip_penilai_1 LIKE '%V' AND nip_penilai_2 LIKE '%V'", array($id_pengajuan));
	if ($baris) {
		$total = (float) $koneksi->GetOne("SELECT COALESCE(SUM(poin_akhir), 0) FROM berkas_pak WHERE id_pengajuan = ?", array($id_pengajuan));
		$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'PENILAIAN SELESAI', nip_penilai_1 = ?, nip_penilai_2 = ?, total_pak_akhir = ? WHERE id_pengajuan = ?",
			array(rtrim($baris[0], 'V'), rtrim($baris[1], 'V'), (int) round($total), $id_pengajuan));
	}
	nx_done("penilai_daftar_pengajuan_selesai.php?nip_penilai=" . rawurlencode($nip_penilai), 'success', 'Penilaian Anda dikirim.');
