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

	$koneksi->Execute("UPDATE dosen SET nama_dosen = ?, unit_kerja_dosen = ?, alamat_dosen = ?, telepon_dosen = ? WHERE nip_dosen = ?",
		array($nama_dosen, $unit_kerja_dosen, $alamat_dosen, $telepon_dosen, $nip_dosen));
	if ($password_dosen !== '') {
		$koneksi->Execute("UPDATE dosen SET password_dosen = ? WHERE nip_dosen = ?", array(nx_hash_password($password_dosen), $nip_dosen));
	}
	nx_done("dosen_profile.php?nip_dosen=" . rawurlencode($nip_dosen), 'success', 'Profil diperbarui.');
