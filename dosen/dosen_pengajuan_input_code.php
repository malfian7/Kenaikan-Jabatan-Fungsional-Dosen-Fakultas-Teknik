<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$balik = "dosen_pengajuan_input.php?nip_dosen=" . rawurlencode($nip_dosen);

	$jabatan_asal      = nx_text('jabatan_asal', 50);
	$jabatan_tujuan    = nx_text('jabatan_tujuan', 50);
	$tanggal_sk        = nx_text('tanggal_sk', 10);
	$dokumen_pendukung = nx_clean_link(nx_input('dokumen_pendukung'));

	$kum_asal   = $koneksi->GetOne("SELECT id_kum FROM syarat_kum WHERE nama_jabatan = ?", array($jabatan_asal));
	$kum_tujuan = $koneksi->GetOne("SELECT id_kum FROM syarat_kum WHERE nama_jabatan = ?", array($jabatan_tujuan));
	$tgl = DateTime::createFromFormat('Y-m-d', $tanggal_sk);

	if (!$kum_asal || !$kum_tujuan) {
		nx_done($balik, 'error', 'Pilih jabatan saat ini dan jabatan tujuan dari daftar.');
	}
	if ((int) $kum_tujuan <= (int) $kum_asal) {
		nx_done($balik, 'error', 'Jabatan tujuan harus lebih tinggi dari jabatan saat ini.');
	}
	if (!$tgl || $tgl->format('Y-m-d') !== $tanggal_sk || $tanggal_sk > date('Y-m-d')) {
		nx_done($balik, 'error', 'Tanggal SK jabatan terakhir tidak valid (tidak boleh di masa depan).');
	}
	if ($dokumen_pendukung === '') {
		nx_done($balik, 'error', 'Tautan dokumen pendukung tidak valid. Tempelkan link Google Drive atau URL lengkap.');
	}

	$koneksi->Execute("INSERT INTO pengajuan (id_kum, nip_dosen, nip_pegawai, nip_penilai_1, nip_penilai_2, tanggal_pengajuan, jabatan_asal, tanggal_sk, jabatan_tujuan, dokumen_pendukung, status_pengajuan, total_pak_akhir, file_sk) VALUES (?, ?, '', '', '', CURRENT_DATE, ?, ?, ?, ?, 'BELUM DIKIRIM', 0, '')",
		array($kum_tujuan, $nip_dosen, $jabatan_asal, $tanggal_sk, $jabatan_tujuan, $dokumen_pendukung));
	$id_baru = $koneksi->Insert_ID();
	nx_done("dosen_pengajuan_tambah_berkas.php?nip_dosen=" . rawurlencode($nip_dosen) . "&id_pengajuan=" . (int) $id_baru, 'success', 'Pengajuan dibuat. Tambahkan berkas kegiatan Anda.');
