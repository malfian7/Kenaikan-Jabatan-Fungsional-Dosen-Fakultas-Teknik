<?php
	include_once("../koneksi/conn.php");

	$nip_pegawai      = nx_input('nip_pegawai');
	$password_pegawai = isset($_POST['password_pegawai']) && is_string($_POST['password_pegawai']) ? $_POST['password_pegawai'] : '';

	nx_ensure_schema();   // migrasi sekali: lebarkan kolom password, utf8mb4, hash password lama

	$tunggu = nx_throttle_wait('pegawai', $nip_pegawai);
	if ($tunggu > 0) {
		nx_flash('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . ceil($tunggu / 60) . ' menit.');
		header("location:pegawai_login_gagal.php");
		exit;
	}

	$baris = $koneksi->GetRow("SELECT nip_pegawai, password_pegawai FROM pegawai WHERE nip_pegawai = ?", array($nip_pegawai));

	if ($baris && nx_password_ok($password_pegawai, $baris[1])) {
		if (!nx_is_hashed($baris[1]) || password_needs_rehash($baris[1], PASSWORD_DEFAULT)) {
			$koneksi->Execute("UPDATE pegawai SET password_pegawai = ? WHERE nip_pegawai = ?", array(nx_hash_password($password_pegawai), $baris[0]));
		}
		nx_throttle_clear('pegawai', $nip_pegawai);
		session_regenerate_id(true);
		$_SESSION['role']     = 'pegawai';
		$_SESSION['nip']      = $baris[0];
		$_SESSION['login_at'] = time();
		$_SESSION['last']     = time();
		header("location:pegawai_beranda.php?nip_pegawai=" . rawurlencode($baris[0]));
	} else {
		nx_throttle_fail('pegawai', $nip_pegawai);
		header("location:pegawai_login_gagal.php");
	}
