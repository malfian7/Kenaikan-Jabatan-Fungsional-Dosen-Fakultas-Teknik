<?php
	include_once("../koneksi/conn.php");
	$nip_dosen    = $_GET['nip_dosen'];
	$balik = "dosen_profile_ubah.php?nip_dosen=" . rawurlencode($nip_dosen);

	$password_dosen   = isset($_POST['password_dosen']) && is_string($_POST['password_dosen']) ? $_POST['password_dosen'] : '';
	$nama_dosen       = nx_text('nama_dosen', 50);
	$unit_kerja_dosen = nx_text('unit_kerja_dosen', 50);
	$alamat_dosen     = nx_text('alamat_dosen', 500);
	$telepon_dosen    = nx_text('telepon_dosen', 20);

	if ($nama_dosen === '' || $unit_kerja_dosen === '') {
		nx_done($balik, 'error', 'Nama dan unit kerja wajib diisi.');
	}
	if (!nx_valid_phone($telepon_dosen)) {
		nx_done($balik, 'error', 'Nomor telepon hanya boleh berisi angka, spasi, +, ( ), dan tanda hubung.');
	}
	if ($password_dosen !== '' && strlen($password_dosen) < 6) {
		nx_done($balik, 'error', 'Password baru minimal 6 karakter.');
	}

	include_once("../include/nx_aturan.php");
	$pend   = nx_input('pendidikan_terakhir');
	$status = nx_input('status_kepegawaian');
	$tmt    = nx_input('tmt_dosen_tetap');
	$ak     = nx_input('ak_kumulatif');
	$akp    = nx_input('ak_penelitian_kumulatif');
	if ($pend !== '' && !isset(nx_pendidikan_opsi()[$pend])) { nx_done($balik, 'error', 'Pilihan pendidikan terakhir tidak dikenal.'); }
	if ($status !== '' && !isset(nx_status_opsi()[$status])) { nx_done($balik, 'error', 'Pilihan status kepegawaian tidak dikenal.'); }
	if ($tmt !== '') {
		$d = DateTime::createFromFormat('Y-m-d', $tmt);
		if (!$d || $d->format('Y-m-d') !== $tmt || $tmt > date('Y-m-d') || $tmt < '1950-01-01') { nx_done($balik, 'error', 'Tanggal diangkat dosen tetap tidak valid.'); }
	}
	foreach (array('AK kumulatif' => $ak, 'AK penelitian kumulatif' => $akp) as $lbl => $v) {
		if ($v !== '' && (!is_numeric($v) || (float) $v < 0 || (float) $v > 5000)) { nx_done($balik, 'error', $lbl . ' harus berupa angka 0 sampai 5000.'); }
	}
	if ($ak !== '' && $akp !== '' && (float) $akp > (float) $ak) { nx_done($balik, 'error', 'AK penelitian kumulatif tidak boleh melebihi AK kumulatif.'); }
	$koneksi->Execute("UPDATE dosen SET pendidikan_terakhir = ?, status_kepegawaian = ?, tmt_dosen_tetap = ?, ak_kumulatif = ?, ak_penelitian_kumulatif = ?, sertifikat_pendidik = ?, skp_baik = ?, uji_kompetensi = ? WHERE nip_dosen = ?",
		array($pend === '' ? null : $pend, $status === '' ? null : $status, $tmt === '' ? null : $tmt, $ak === '' ? 0 : (float) $ak, $akp === '' ? 0 : (float) $akp,
			isset($_POST['sertifikat_pendidik']) ? 1 : 0, isset($_POST['skp_baik']) ? 1 : 0, isset($_POST['uji_kompetensi']) ? 1 : 0, $nip_dosen));
	$koneksi->Execute("UPDATE dosen SET nama_dosen = ?, unit_kerja_dosen = ?, alamat_dosen = ?, telepon_dosen = ? WHERE nip_dosen = ?",
		array($nama_dosen, $unit_kerja_dosen, $alamat_dosen, $telepon_dosen, $nip_dosen));
	if ($password_dosen !== '') {
		$koneksi->Execute("UPDATE dosen SET password_dosen = ? WHERE nip_dosen = ?", array(nx_hash_password($password_dosen), $nip_dosen));
	}
	nx_done("dosen_profile.php?nip_dosen=" . rawurlencode($nip_dosen), 'success', 'Profil diperbarui.');
