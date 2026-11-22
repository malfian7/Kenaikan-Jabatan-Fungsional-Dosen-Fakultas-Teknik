<?php
	include_once("../koneksi/conn.php");
	include_once("../include/nx_aturan.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$balik = "dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . rawurlencode($id_pengajuan) . "#publikasi";

	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?", array($id_pengajuan, $nip_dosen));
	if ($status !== 'BELUM DIKIRIM') {
		nx_done($balik, 'error', 'Publikasi hanya dapat ditambah selama pengajuan belum dikirim.');
	}
	$judul     = nx_text('judul', 255);
	$jenis     = nx_input('jenis');
	$peringkat = nx_input('peringkat');
	$peran     = nx_input('peran');
	$tautan    = nx_clean_link(nx_input('tautan'));

	$opsi = nx_peringkat_per_jenis();
	if (strlen($judul) < 5) { nx_done($balik, 'error', 'Judul publikasi minimal 5 karakter.'); }
	if (!isset($opsi[$jenis])) { nx_done($balik, 'error', 'Jenis publikasi tidak dikenal.'); }
	if (!isset($opsi[$jenis][$peringkat])) { nx_done($balik, 'error', 'Peringkat tidak sesuai dengan jenis publikasi.'); }
	if ($jenis === 'karya_seni') { $peran = 'pertama'; }
	if (!isset(nx_peran_opsi()[$peran])) { nx_done($balik, 'error', 'Peran penulis tidak dikenal.'); }
	if ($tautan === '') { nx_done($balik, 'error', 'Tautan bukti publikasi tidak valid. Tempelkan URL lengkap artikel atau karya.'); }
	if ((int) $koneksi->GetOne("SELECT COUNT(*) FROM publikasi WHERE id_pengajuan = ?", array($id_pengajuan)) >= 20) {
		nx_done($balik, 'error', 'Maksimal 20 publikasi per pengajuan.');
	}
	$koneksi->Execute("INSERT INTO publikasi (id_pengajuan, judul, jenis, peringkat, peran, tautan) VALUES (?, ?, ?, ?, ?, ?)",
		array($id_pengajuan, $judul, $jenis, $peringkat, $peran, $tautan));
	nx_done($balik, 'success', 'Publikasi ditambahkan.');
