<?php
	include_once("../koneksi/conn.php");

	$nip_penilai      = nx_input('nip_penilai');
	$password_penilai = isset($_POST['password_penilai']) && is_string($_POST['password_penilai']) ? $_POST['password_penilai'] : '';

	nx_ensure_schema();   // migrasi sekali: lebarkan kolom password, utf8mb4, hash password lama

	$tunggu = nx_throttle_wait('penilai', $nip_penilai);
	if ($tunggu > 0) {
		nx_flash('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . ceil($tunggu / 60) . ' menit.');
		header("location:penilai_login_gagal.php");
		exit;
	}

	$baris = $koneksi->GetRow("SELECT nip_penilai, password_penilai FROM penilai WHERE nip_penilai = ?", array($nip_penilai));

	if ($baris && nx_password_ok($password_penilai, $baris[1])) {
		if (!nx_is_hashed($baris[1]) || password_needs_rehash($baris[1], PASSWORD_DEFAULT)) {
			$koneksi->Execute("UPDATE penilai SET password_penilai = ? WHERE nip_penilai = ?", array(nx_hash_password($password_penilai), $baris[0]));
		}
		nx_throttle_clear('penilai', $nip_penilai);
		session_regenerate_id(true);
		$_SESSION['role']     = 'penilai';
		$_SESSION['nip']      = $baris[0];
		$_SESSION['login_at'] = time();
		$_SESSION['last']     = time();
		header("location:penilai_beranda.php?nip_penilai=" . rawurlencode($baris[0]));
	} else {
		nx_throttle_fail('penilai', $nip_penilai);
		header("location:penilai_login_gagal.php");
	}
