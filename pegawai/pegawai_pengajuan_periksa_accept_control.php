<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai  = $_GET['nip_pegawai'];
	$id_pengajuan = isset($_GET['id_pengajuan']) ? $_GET['id_pengajuan'] : '';
	$q = "?nip_pegawai=" . rawurlencode($nip_pegawai) . "&id_pengajuan=" . rawurlencode($id_pengajuan);
	$status = $koneksi->GetOne("SELECT status_pengajuan FROM pengajuan WHERE id_pengajuan = ?", array($id_pengajuan));
	if (!in_array($status, array('TERKIRIM', 'TELAH REVISI'), true)) {
		nx_done("pegawai_data_pengajuan_berkas_diterima.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'error', 'Pengajuan ini tidak sedang menunggu pemeriksaan.');
	}
	$total = (int) $koneksi->GetOne("SELECT COUNT(*) FROM berkas_pak WHERE id_pengajuan = ?", array($id_pengajuan));
	$belum = (int) $koneksi->GetOne("SELECT COUNT(*) FROM berkas_pak WHERE id_pengajuan = ? AND status_berkas NOT IN ('VALID')", array($id_pengajuan));
	if ($total === 0 || $belum > 0) {
		nx_done("pegawai_pengajuan_periksa.php" . $q, 'error', $total === 0 ? 'Pengajuan belum memiliki berkas.' : 'Masih ada ' . $belum . ' berkas yang belum dinyatakan valid.');
	}
	$koneksi->Execute("UPDATE pengajuan SET status_pengajuan = 'BERKAS DITERIMA', nip_pegawai = ? WHERE id_pengajuan = ?", array($nip_pegawai, $id_pengajuan));
	nx_done("pegawai_penilai_input.php" . $q, 'success', 'Berkas diterima. Tentukan dua penilai berikutnya.');
