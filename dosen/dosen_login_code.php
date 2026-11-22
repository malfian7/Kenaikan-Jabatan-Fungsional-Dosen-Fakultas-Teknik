<?php
	include_once("../koneksi/conn.php");

	$nip_dosen      = nx_input('nip_dosen');
	$password_dosen = isset($_POST['password_dosen']) && is_string($_POST['password_dosen']) ? $_POST['password_dosen'] : '';

	nx_ensure_schema();   // migrasi sekali: lebarkan kolom password, utf8mb4, hash password lama

	$tunggu = nx_throttle_wait('dosen', $nip_dosen);
	if ($tunggu > 0) {
		nx_flash('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . ceil($tunggu / 60) . ' menit.');
		header("location:dosen_login_gagal.php");
		exit;
	}

	$baris = $koneksi->GetRow("SELECT nip_dosen, password_dosen FROM dosen WHERE nip_dosen = ?", array($nip_dosen));

	if ($baris && nx_password_ok($password_dosen, $baris[1])) {
		if (!nx_is_hashed($baris[1]) || password_needs_rehash($baris[1], PASSWORD_DEFAULT)) {
			$koneksi->Execute("UPDATE dosen SET password_dosen = ? WHERE nip_dosen = ?", array(nx_hash_password($password_dosen), $baris[0]));
		}
		nx_throttle_clear('dosen', $nip_dosen);
		session_regenerate_id(true);
		$_SESSION['role']     = 'dosen';
		$_SESSION['nip']      = $baris[0];
		$_SESSION['login_at'] = time();
		$_SESSION['last']     = time();
		header("location:dosen_beranda.php?nip_dosen=" . rawurlencode($baris[0]));
	} else {
		nx_throttle_fail('dosen', $nip_dosen);
		header("location:dosen_login_gagal.php");
	}
