<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$id_berkas    = isset($_GET['id_berkas']) ? $_GET['id_berkas'] : '';
	$balik  = "dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$formul = "dosen_berkas_edit.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "&id_berkas=" . rawurlencode($id_berkas);

	$bukti_berkas = nx_clean_link(nx_input('bukti_berkas'));
	$volume       = nx_number('volume');

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	$poin   = $koneksi->GetOne("SELECT r.poin_rubrik FROM berkas_pak b INNER JOIN rubrik r ON r.kode_rubrik = b.kode_rubrik WHERE b.id_berkas = ? AND b.id_pengajuan = ?", array($id_berkas, $id_pengajuan));

	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Berkas hanya dapat diubah selama pengajuan belum dikirim.');
	}
	if ($poin === false || $poin === null || $poin === '') {
		nx_done($balik, 'error', 'Berkas tidak ditemukan.');
	}
	if ($bukti_berkas === '') {
		nx_done($formul, 'error', 'Tautan bukti tidak valid. Tempelkan link Google Drive atau URL lengkap.');
	}
	if ($volume === null || $volume < 1 || $volume > 1000 || floor($volume) != $volume) {
		nx_done($formul, 'error', 'Volume harus bilangan bulat antara 1 dan 1000.');
	}

	$total_poin = (float) $poin * (int) $volume;
	$koneksi->Execute("UPDATE berkas_pak SET bukti_berkas = ?, volume = ?, poin_1 = ?, poin_2 = ?, poin_akhir = ? WHERE id_berkas = ? AND id_pengajuan = ?",
		array($bukti_berkas, (int) $volume, $total_poin, $total_poin, $total_poin, $id_berkas, $id_pengajuan));
	nx_done($balik, 'success', 'Perubahan berkas disimpan.');
