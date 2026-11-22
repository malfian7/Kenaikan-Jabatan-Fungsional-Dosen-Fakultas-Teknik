<?php
include_once("../koneksi/conn.php");
$nip_pegawai = $_GET['nip_pegawai'];
$select_pegawai = "SELECT * FROM pegawai WHERE nip_pegawai= '$nip_pegawai'";
$rs_pegawai = $koneksi->Execute($select_pegawai);
if ($rs_pegawai->recordCount() > 0) {
	while (!$rs_pegawai->EOF) {
		$nama_pegawai = $rs_pegawai->fields[2];
		$rs_pegawai->MoveNext();
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="manifest" href="site.webmanifest">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo nx_e(nx_logo('../')); ?>">

	<!-- css here -->
	<title>Sistem Informasi Kenaikan Jabatan Dosen</title>
	<link href="../asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../asset/css/simple-sidebar.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../DataTables/assets/css/jquery.dataTables.min.css">
	<link href="../asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="../asset/css/nexus.css" rel="stylesheet">
</head>

<body>
	<div class="d-flex" id="wrapper">
		<!-- Sidebar -->
		<div class="bg-light border-right" id="sidebar-wrapper">
			<div class="sidebar-heading"><b>Main Menu</b></div>
			<div class="list-group list-group-flush">
				<a href="pegawai_beranda.php?nip_pegawai=<?php echo nx_e($nip_pegawai) ?>" class="list-group-item list-group-item-action bg-light">Beranda</a>
				<a href="pegawai_data_penilai.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Data Penilai</a>
				<a href="pegawai_data_pengajuan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Data Pengajuan</a>
				<a href="pegawai_aturan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Aturan Jabatan</a>
				<a href="pegawai_pengaturan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-info text-light">Pengaturan</a>
			</div>
		</div>
		<div id="page-content-wrapper">
			<?php include "../include/navbar_pegawai.php" ?>
			<div class="container-fluid">
				<h1></h1>
				<div class="card">
					<div class="card-header bg-primary text-light"><b>PENGATURAN INSTITUSI</b></div>
					<div class="card-body">
						<p class="text-muted" style="font-size:13px">Nama dan logo ini tampil di halaman depan, halaman masuk, sidebar, dan lembar usulan cetak.</p>
						<form action="pegawai_pengaturan_code.php?nip_pegawai=<?php echo nx_e(rawurlencode($nip_pegawai)); ?>" method="post" enctype="multipart/form-data" class="nx-form-grid">
							<div class="form-group"><label for="app">Nama aplikasi</label><input class="form-control" type="text" id="app" name="app" maxlength="20" required value="<?php echo nx_e(nx_pengaturan('app')); ?>"></div>
							<div class="form-group"><label for="institusi">Nama institusi lengkap</label><input class="form-control" type="text" id="institusi" name="institusi" maxlength="80" required value="<?php echo nx_e(nx_pengaturan('institusi')); ?>"></div>
							<div class="form-group"><label for="unit">Nama unit singkat</label><input class="form-control" type="text" id="unit" name="unit" maxlength="60" required value="<?php echo nx_e(nx_pengaturan('unit')); ?>"></div>
							<div class="form-group"><label for="pimpinan_jabatan">Jabatan penandatangan</label><input class="form-control" type="text" id="pimpinan_jabatan" name="pimpinan_jabatan" maxlength="40" value="<?php echo nx_e(nx_pengaturan('pimpinan_jabatan')); ?>"></div>
							<div class="form-group"><label for="pimpinan_nama">Nama penandatangan (opsional)</label><input class="form-control" type="text" id="pimpinan_nama" name="pimpinan_nama" maxlength="80" value="<?php echo nx_e(nx_pengaturan('pimpinan_nama')); ?>"></div>
							<div class="form-group">
								<label for="logo">Logo (PNG atau JPG, maksimal 1 MB)</label>
								<div class="d-flex align-items-center" style="gap:14px"><img src="<?php echo nx_e(nx_logo('../')); ?>" alt="Logo saat ini" width="56" height="56" style="object-fit:contain;border:1px solid var(--garis);border-radius:12px;padding:6px;background:#fff"><input class="form-control-file" type="file" id="logo" name="logo" accept="image/png,image/jpeg"></div>
								<div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="logo_reset" name="logo_reset" value="1"><label class="form-check-label" for="logo_reset">Kembalikan ke logo bawaan</label></div>
							</div>
							<button type="submit" class="btn btn-primary">Simpan pengaturan</button>
						</form>
					</div>
				</div>
				<h1></h1>
				<div class="card">
					<div class="card-header bg-primary text-light"><b>BACKUP DATABASE</b></div>
					<div class="card-body">
						<p class="text-muted" style="font-size:13px">Mengunduh seluruh data (akun, pengajuan, berkas, riwayat, pengaturan) sebagai berkas SQL. Berkas ini berisi kata sandi yang sudah di-hash, simpan di tempat aman. Untuk memulihkan, impor berkas lewat phpMyAdmin.</p>
						<form action="pegawai_backup_code.php?nip_pegawai=<?php echo nx_e(rawurlencode($nip_pegawai)); ?>" method="post">
							<button type="submit" class="btn btn-primary"><i class="fas fa-download" aria-hidden="true"></i> Unduh backup (.sql)</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="../asset/vendor/jquery/jquery.min.js"></script>
	<script src="../asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/assets/js/jquery.dataTables.min.js"></script>

	<script type="text/javascript" class="init" nonce="<?php echo nx_nonce(); ?>">
		$(document).ready(function() {
			$('#example').DataTable();
		});
	</script>
	<script nonce="<?php echo nx_nonce(); ?>">
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});
	</script>
	<script src="../asset/js/nexus.js"></script>
</body>

</html>