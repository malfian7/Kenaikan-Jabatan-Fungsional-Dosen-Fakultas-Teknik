<?php
	include_once("../koneksi/conn.php");
	include_once("../include/nx_aturan.php");
	$nip_pegawai = $_GET['nip_pegawai'];
	$balik = "pegawai_aturan.php?nip_pegawai=" . rawurlencode($nip_pegawai);

	$post = function ($k) { return (isset($_POST[$k]) && is_array($_POST[$k])) ? $_POST[$k] : array(); };
	$mj = $post('min_tahun_jabatan'); $md = $post('min_tahun_dosen_tetap'); $pm = $post('pendidikan_min'); $jp = $post('jumlah_publikasi'); $wp = $post('wajib_publikasi');

	$koneksi->BeginTrans();
	foreach (array('Lektor', 'Lektor Kepala', 'Profesor') as $jn) {
		$a = isset($mj[$jn]) ? $mj[$jn] : ''; $b = isset($md[$jn]) ? $md[$jn] : ''; $c = isset($jp[$jn]) ? $jp[$jn] : '';
		$d = isset($pm[$jn]) && is_string($pm[$jn]) ? $pm[$jn] : '';
		foreach (array($a, $b, $c) as $v) {
			if (!is_string($v) || !preg_match('/^[0-9]{1,2}$/', $v)) {
				$koneksi->RollbackTrans();
				nx_done($balik, 'error', 'Semua isian angka harus bilangan bulat 0 sampai 99.');
			}
		}
		if ((int) $c > 5 || (int) $a > 30 || (int) $b > 40) {
			$koneksi->RollbackTrans();
			nx_done($balik, 'error', 'Nilai di luar batas wajar (tahun maksimal 30 atau 40, publikasi maksimal 5).');
		}
		if ($d !== '' && !in_array($d, array('S2', 'S3'), true)) {
			$koneksi->RollbackTrans();
			nx_done($balik, 'error', 'Pilihan pendidikan minimal tidak dikenal.');
		}
		$old = nx_aturan_jenjang($koneksi, $jn);
		$ubah = !$old || (int) $a !== $old['min_tahun_jabatan'] || (int) $b !== $old['min_tahun_dosen_tetap'] || (string) $d !== (string) $old['pendidikan_min'] || (int) $c !== $old['jumlah_publikasi'] || (isset($wp[$jn]) ? 1 : 0) !== $old['wajib_publikasi'];
		$koneksi->Execute("UPDATE aturan_jabatan SET min_tahun_jabatan = ?, min_tahun_dosen_tetap = ?, pendidikan_min = ?, jumlah_publikasi = ?, wajib_publikasi = ?, terverifikasi = IF(?, 0, terverifikasi), sumber = IF(? AND sumber NOT LIKE '%diubah institusi%', CONCAT(sumber, ' (diubah institusi)'), sumber) WHERE jenjang = ?",
			array((int) $a, (int) $b, $d === '' ? null : $d, (int) $c, isset($wp[$jn]) ? 1 : 0, $ubah ? 1 : 0, $ubah ? 1 : 0, $jn));
	}
	$koneksi->CommitTrans();
	nx_done($balik, 'success', 'Aturan jabatan disimpan.');
