<?php
	// Tidak dipakai lagi: keputusan terima/kembalikan ada di
	// pegawai_pengajuan_periksa_accept_control.php dan _return_control.php.
	include_once("../koneksi/conn.php");
	header("location:pegawai_data_pengajuan_berkas_diterima.php?nip_pegawai=" . rawurlencode($_GET['nip_pegawai']));
