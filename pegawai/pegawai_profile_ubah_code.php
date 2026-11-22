<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai = $_GET['nip_pegawai'];
	$balik = "pegawai_profile_ubah.php?nip_pegawai=" . rawurlencode($nip_pegawai);

	$password_pegawai   = isset($_POST['password_pegawai']) && is_string($_POST['password_pegawai']) ? $_POST['password_pegawai'] : '';
	$nama_pegawai       = nx_text('nama_pegawai', 50);
	$unit_kerja_pegawai = nx_text('unit_kerja_pegawai', 50);
	$alamat_pegawai     = nx_text('alamat_pegawai', 500);
	$telepon_pegawai    = nx_text('telepon_pegawai', 20);

	if ($nama_pegawai === '' || $unit_kerja_pegawai === '') {
		nx_done($balik, 'error', 'Nama dan unit kerja wajib diisi.');
	}
	if (!nx_valid_phone($telepon_pegawai)) {
		nx_done($balik, 'error', 'Nomor telepon hanya boleh berisi angka, spasi, +, ( ), dan tanda hubung.');
	}
	if ($password_pegawai !== '' && strlen($password_pegawai) < 6) {
		nx_done($balik, 'error', 'Password baru minimal 6 karakter.');
	}
	$koneksi->Execute("UPDATE pegawai SET nama_pegawai = ?, unit_kerja_pegawai = ?, alamat_pegawai = ?, telepon_pegawai = ? WHERE nip_pegawai = ?",
		array($nama_pegawai, $unit_kerja_pegawai, $alamat_pegawai, $telepon_pegawai, $nip_pegawai));
	if ($password_pegawai !== '') {
		$koneksi->Execute("UPDATE pegawai SET password_pegawai = ? WHERE nip_pegawai = ?", array(nx_hash_password($password_pegawai), $nip_pegawai));
	}
	nx_done("pegawai_profile.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'success', 'Profil diperbarui.');
