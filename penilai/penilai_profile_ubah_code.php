<?php
	include_once("../koneksi/conn.php");
	$nip_penilai = $_GET['nip_penilai'];
	$balik = "penilai_profile_ubah.php?nip_penilai=" . rawurlencode($nip_penilai);

	$password_penilai   = isset($_POST['password_penilai']) && is_string($_POST['password_penilai']) ? $_POST['password_penilai'] : '';
	$nama_penilai       = nx_text('nama_penilai', 50);
	$unit_kerja_penilai = nx_text('unit_kerja_penilai', 50);
	$rumpun_ilmu        = nx_text('rumpun_ilmu', 50);
	$alamat_penilai     = nx_text('alamat_penilai', 500);
	$telepon_penilai    = nx_text('telepon_penilai', 20);

	if ($nama_penilai === '' || $unit_kerja_penilai === '' || $rumpun_ilmu === '') {
		nx_done($balik, 'error', 'Nama, unit kerja, dan rumpun ilmu wajib diisi.');
	}
	if (!nx_valid_phone($telepon_penilai)) {
		nx_done($balik, 'error', 'Nomor telepon hanya boleh berisi angka, spasi, +, ( ), dan tanda hubung.');
	}
	if ($password_penilai !== '' && strlen($password_penilai) < 6) {
		nx_done($balik, 'error', 'Password baru minimal 6 karakter.');
	}
	$koneksi->Execute("UPDATE penilai SET nama_penilai = ?, unit_kerja_penilai = ?, rumpun_ilmu = ?, alamat_penilai = ?, telepon_penilai = ? WHERE nip_penilai = ?",
		array($nama_penilai, $unit_kerja_penilai, $rumpun_ilmu, $alamat_penilai, $telepon_penilai, $nip_penilai));
	if ($password_penilai !== '') {
		$koneksi->Execute("UPDATE penilai SET password_penilai = ? WHERE nip_penilai = ?", array(nx_hash_password($password_penilai), $nip_penilai));
	}
	nx_done("penilai_profile.php?nip_penilai=" . rawurlencode($nip_penilai), 'success', 'Profil diperbarui.');
