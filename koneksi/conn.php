<?php
	/*
	 * Koneksi database SIPAK.
	 * Ubah host/user/password/nama database di baris Connect() sesuai server Anda.
	 */
	define('NX_CONN_LOADING', true);
	include_once __DIR__."/guard.php";
	include_once __DIR__."/adodb5/adodb.inc.php";
	$koneksi=NewADOConnection("mysqli");
	$koneksi->Connect("localhost","root","","sipak");
	$koneksi->Execute("SET NAMES utf8mb4");

	global $koneksi;

	// Penjaga keamanan: login, otorisasi, CSRF (lihat koneksi/guard.php)
	nx_guard_run();

?>
