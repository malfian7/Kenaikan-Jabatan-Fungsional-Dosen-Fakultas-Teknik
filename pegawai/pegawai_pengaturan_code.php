<?php
	include_once("../koneksi/conn.php");
	$nip_pegawai = $_GET['nip_pegawai'];
	$balik = "pegawai_pengaturan.php?nip_pegawai=" . rawurlencode($nip_pegawai);

	$app = nx_text('app', 20); $institusi = nx_text('institusi', 80); $unit = nx_text('unit', 60);
	$pj = nx_text('pimpinan_jabatan', 40); $pn = nx_text('pimpinan_nama', 80);
	if ($app === '' || $institusi === '' || $unit === '') {
		nx_done($balik, 'error', 'Nama aplikasi, institusi, dan unit wajib diisi.');
	}
	foreach (array($app, $institusi, $unit, $pj, $pn) as $v) {
		if (preg_match('/[\x00-\x1F<>"\\\\]/', $v)) { nx_done($balik, 'error', 'Isian tidak boleh memuat tanda < > " \\ atau karakter kontrol.'); }
	}

	$logo = null;   // null = tidak diubah
	$dir = dirname(__DIR__) . '/assets/img/logo/';
	if (nx_input('logo_reset') === '1') {
		$logo = 'assets/img/logo/FT.png';
	} elseif (isset($_FILES['logo']) && is_array($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
		$f = $_FILES['logo'];
		if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) { nx_done($balik, 'error', 'Logo gagal diunggah. Coba lagi dengan berkas yang lebih kecil.'); }
		if ($f['size'] > 1048576) { nx_done($balik, 'error', 'Ukuran logo maksimal 1 MB.'); }
		$info = @getimagesize($f['tmp_name']);
		if (!$info || !in_array($info[2], array(IMAGETYPE_PNG, IMAGETYPE_JPEG), true)) { nx_done($balik, 'error', 'Logo harus berupa gambar PNG atau JPG.'); }
		if ($info[0] > 3000 || $info[1] > 3000) { nx_done($balik, 'error', 'Dimensi logo maksimal 3000 x 3000 piksel.'); }
		$ext = $info[2] === IMAGETYPE_PNG ? 'png' : 'jpg';
		$nama = 'logo-' . bin2hex(random_bytes(6)) . '.' . $ext;
		if (!is_dir($dir) || !is_writable($dir) || !move_uploaded_file($f['tmp_name'], $dir . $nama)) { nx_done($balik, 'error', 'Folder assets/img/logo tidak bisa ditulis. Periksa izin folder.'); }
		$logo = 'assets/img/logo/' . $nama;
	}

	$simpan = array('app' => $app, 'institusi' => $institusi, 'unit' => $unit, 'pimpinan_jabatan' => $pj, 'pimpinan_nama' => $pn);
	if ($logo !== null) { $simpan['logo'] = $logo; }
	$lama = nx_pengaturan('logo');
	$koneksi->BeginTrans();
	foreach ($simpan as $k => $v) {
		$koneksi->Execute('INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)', array($k, $v));
	}
	$koneksi->CommitTrans();
	if ($logo !== null && $lama !== $logo && preg_match('#^assets/img/logo/logo-[0-9a-f]{12}\.(png|jpg)$#', $lama) && is_file(dirname(__DIR__) . '/' . $lama)) {
		@unlink(dirname(__DIR__) . '/' . $lama);   // hapus logo unggahan sebelumnya
	}
	nx_done($balik, 'success', 'Pengaturan disimpan.');
