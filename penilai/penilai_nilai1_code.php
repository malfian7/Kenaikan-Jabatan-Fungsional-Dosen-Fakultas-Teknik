<?php
	include_once("../koneksi/conn.php");
	$nip_penilai  = $_GET['nip_penilai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$id_berkas    = isset($_GET['id_berkas']) ? $_GET['id_berkas'] : '';
	$balik  = "penilai_data_berkas_penilai1.php?nip_penilai=" . rawurlencode($nip_penilai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$formul = "penilai_nilai1.php?nip_penilai=" . rawurlencode($nip_penilai) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "&id_berkas=" . rawurlencode($id_berkas);

	$poin_1 = nx_number('poin_1');

	// Hanya penilai 1 yang belum mengunci penilaiannya yang boleh memberi nilai pada pengajuan ini.
	$p = $koneksi->GetRow("SELECT status_pengajuan, nip_penilai_1 FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if (!$p || $p[1] !== $nip_penilai || $p[0] !== 'BERKAS DITERIMA') {
		nx_done($balik, 'error', 'Penilaian untuk pengajuan ini sudah dikunci atau bukan bagian Anda.');
	}
	// Batas wajar: tidak negatif dan tidak melebihi poin rubrik x volume (pembulatan kecil diizinkan).
	$maks = $koneksi->GetOne("SELECT r.poin_rubrik * b.volume FROM berkas_pak b INNER JOIN rubrik r ON r.kode_rubrik = b.kode_rubrik WHERE b.id_berkas = ? AND b.id_pengajuan = ?", array($id_berkas, $id_pengajuan));
	if ($maks === false || $maks === null) {
		nx_done($balik, 'error', 'Berkas tidak ditemukan.');
	}
	if ($poin_1 === null || $poin_1 < 0 || $poin_1 > (float) $maks * 1.0001) {
		nx_done($formul, 'error', 'Poin harus berupa angka antara 0 dan ' . rtrim(rtrim(number_format((float) $maks, 2, '.', ''), '0'), '.') . ' (poin rubrik x volume).');
	}

	$koneksi->Execute("UPDATE berkas_pak SET poin_1 = ?, poin_akhir = (? + poin_2) / 2 WHERE id_berkas = ? AND id_pengajuan = ?",
		array($poin_1, $poin_1, $id_berkas, $id_pengajuan));
	nx_done($balik, 'success', 'Nilai berkas disimpan.');
