<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai = $_GET['nip_pegawai'];
	$nip_penilai = isset($_GET['nip_penilai']) ? $_GET['nip_penilai'] : '';
	$balik = "pegawai_data_penilai.php?nip_pegawai=" . rawurlencode($nip_pegawai);
	$dipakai = (int) $koneksi->GetOne("SELECT COUNT(*) FROM pengajuan WHERE nip_penilai_1 IN (?, ?) OR nip_penilai_2 IN (?, ?)", array($nip_penilai, $nip_penilai . 'V', $nip_penilai, $nip_penilai . 'V'));
	if ($dipakai > 0) {
		nx_done($balik, 'error', 'Penilai ini masih tercatat pada ' . $dipakai . ' pengajuan sehingga tidak dapat dihapus.');
	}
	$koneksi->Execute("DELETE FROM penilai WHERE nip_penilai = ?", array($nip_penilai));
	nx_done($balik, 'success', 'Penilai dihapus.');
