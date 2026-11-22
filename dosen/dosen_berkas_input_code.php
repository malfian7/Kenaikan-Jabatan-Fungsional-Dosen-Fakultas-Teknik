<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$kode_rubrik  = isset($_GET['kode_rubrik']) ? $_GET['kode_rubrik'] : '';
	$balik  = "dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$formul = "dosen_berkas_input.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "&kode_rubrik=" . rawurlencode($kode_rubrik);

	$bukti_berkas = nx_clean_link(nx_input('bukti_berkas'));
	$volume       = nx_number('volume');

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	$poin   = $koneksi->GetOne("SELECT poin_rubrik FROM rubrik WHERE kode_rubrik = ?", array($kode_rubrik));

	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Berkas hanya dapat ditambah selama pengajuan belum dikirim.');
	}
	if ($poin === false || $poin === null || $poin === '') {
		nx_done($balik, 'error', 'Butir rubrik tidak ditemukan.');
	}
	if ($bukti_berkas === '') {
		nx_done($formul, 'error', 'Tautan bukti tidak valid. Tempelkan link Google Drive atau URL lengkap.');
	}
	if ($volume === null || $volume < 1 || $volume > 1000 || floor($volume) != $volume) {
		nx_done($formul, 'error', 'Volume harus bilangan bulat antara 1 dan 1000.');
	}

	// Poin selalu dihitung server dari rubrik, bukan dari isian formulir.
	$total_poin = (float) $poin * (int) $volume;
	$koneksi->Execute("INSERT INTO berkas_pak (id_pengajuan, kode_rubrik, bukti_berkas, status_berkas, volume, poin_1, poin_2, poin_akhir, catatan_berkas) VALUES (?, ?, ?, '', ?, ?, ?, ?, '')",
		array($id_pengajuan, $kode_rubrik, $bukti_berkas, (int) $volume, $total_poin, $total_poin, $total_poin));
	nx_done($balik, 'success', 'Berkas ditambahkan ke pengajuan.');
