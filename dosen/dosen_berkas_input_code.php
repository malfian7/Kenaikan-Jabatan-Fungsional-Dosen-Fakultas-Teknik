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
	$poin   = $koneksi->GetOne("SELECT poin_rubrik FROM rubrik WHERE kode_rubrik = ? AND aktif = 1", array($kode_rubrik));

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

	// Poin selalu dihitung server dari rubrik, bukan dari isian formulir. Pembagian penulis hanya untuk unsur penelitian.
	include_once("../include/nx_rubrik_2026.php");
	$persen = 100.0;
	if (nx_rubrik_penelitian($kode_rubrik)) {
		$persen = nx_bagian_dari_post();
		if ($persen === null) {
			nx_done($formul, 'error', 'Pembagian angka kredit harus antara 1% dan 100%.');
		}
	}
	$total_poin = round((float) $poin * (int) $volume * $persen / 100, 2);
	$koneksi->Execute("INSERT INTO berkas_pak (id_pengajuan, kode_rubrik, bukti_berkas, status_berkas, volume, poin_1, poin_2, poin_akhir, catatan_berkas, persen_bagian) VALUES (?, ?, ?, '', ?, ?, ?, ?, '', ?)",
		array($id_pengajuan, $kode_rubrik, $bukti_berkas, (int) $volume, $total_poin, $total_poin, $total_poin, $persen));
	nx_done($balik, 'success', 'Berkas ditambahkan ke pengajuan.');
