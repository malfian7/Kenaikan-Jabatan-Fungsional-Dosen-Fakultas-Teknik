<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai = $_GET['nip_pegawai'];
	$balik = "pegawai_data_penilai_input.php?nip_pegawai=" . rawurlencode($nip_pegawai);
	$nip_penilai = nx_text('nip_penilai', 18);
	$nama_penilai       = nx_text('nama_penilai', 50);
	$unit_kerja_penilai = nx_text('unit_kerja_penilai', 50);
	$rumpun_ilmu        = nx_text('rumpun_ilmu', 50);
	$alamat_penilai     = nx_text('alamat_penilai', 500);
	$telepon_penilai    = nx_text('telepon_penilai', 20);
	$password_penilai   = isset($_POST['password_penilai']) && is_string($_POST['password_penilai']) ? $_POST['password_penilai'] : '';

	if (!nx_valid_nip($nip_penilai)) {
		nx_done($balik, 'error', 'NIP penilai harus berupa 8 sampai 18 digit angka.');
	}
	if ($nama_penilai === '' || $unit_kerja_penilai === '' || $rumpun_ilmu === '') {
		nx_done($balik, 'error', 'Nama, unit kerja, dan rumpun ilmu wajib diisi.');
	}
	if (!nx_valid_phone($telepon_penilai)) {
		nx_done($balik, 'error', 'Nomor telepon hanya boleh berisi angka, spasi, +, ( ), dan tanda hubung.');
	}
	if (strlen($password_penilai) < 6) {
		nx_done($balik, 'error', 'Password awal minimal 6 karakter.');
	}
	if ($koneksi->GetOne("SELECT 1 FROM penilai WHERE nip_penilai = ?", array($nip_penilai))) {
		nx_done($balik, 'error', 'NIP tersebut sudah terdaftar sebagai penilai.');
	}
	$koneksi->Execute("INSERT INTO penilai (nip_penilai, password_penilai, nama_penilai, unit_kerja_penilai, rumpun_ilmu, alamat_penilai, telepon_penilai) VALUES (?, ?, ?, ?, ?, ?, ?)",
		array($nip_penilai, nx_hash_password($password_penilai), $nama_penilai, $unit_kerja_penilai, $rumpun_ilmu, $alamat_penilai, $telepon_penilai));
	nx_done("pegawai_data_penilai.php?nip_pegawai=" . rawurlencode($nip_pegawai), 'success', 'Penilai baru ditambahkan.');
