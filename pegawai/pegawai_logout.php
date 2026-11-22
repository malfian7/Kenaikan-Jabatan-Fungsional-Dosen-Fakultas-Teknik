<?php
	include_once("../koneksi/guard.php");
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$p = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
	}
	session_destroy();
	session_start();
	session_regenerate_id(true);
	nx_flash('success', 'Anda sudah keluar. Sampai jumpa lagi.');
	header("location:pegawai_login.php");
	exit;
