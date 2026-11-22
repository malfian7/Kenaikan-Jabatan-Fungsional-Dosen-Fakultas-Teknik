<?php
include_once("../koneksi/conn.php");
$nip_dosen = $_GET['nip_dosen'];
$select_dosen = "SELECT * FROM dosen WHERE nip_dosen = '$nip_dosen'";
$rs_dosen = $koneksi->Execute($select_dosen);
if ($rs_dosen->recordCount() > 0) {
	while (!$rs_dosen->EOF) {
		$password_dosen 	= $rs_dosen->fields[1];
		$nama_dosen 		= $rs_dosen->fields[2];
		$unit_kerja_dosen 	= $rs_dosen->fields[3];
		$alamat_dosen 		= $rs_dosen->fields[4];
		$telepon_dosen 		= $rs_dosen->fields[5];
		$rs_dosen->MoveNext();
	}
}
include_once("../include/nx_aturan.php");
$f1 = $koneksi->GetRow("SELECT pendidikan_terakhir, status_kepegawaian, tmt_dosen_tetap, ak_kumulatif, ak_penelitian_kumulatif, sertifikat_pendidik, skp_baik, uji_kompetensi FROM dosen WHERE nip_dosen = ?", array($nip_dosen));
if (!$f1) { $f1 = array('', '', '', 0, 0, 0, 0, 0); }
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
	<link href="../asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="../asset/css/nexus.css" rel="stylesheet">
</head>

<body>
	<div class="d-flex" id="wrapper">
		<div class="bg-light border-right" id="sidebar-wrapper">
			<div class="sidebar-heading"><b>Main Menu</b></div>
			<div class="list-group list-group-flush">
				<a href="dosen_beranda.php?nip_dosen=<?php echo nx_e($nip_dosen) ?>" class="list-group-item list-group-item-action bg-light ">Beranda</a>
				<a href="dosen_pengajuan_riwayat.php?nip_dosen=<?php echo nx_e($nip_dosen) ?>" class="list-group-item list-group-item-action bg-light">Riwayat Pengajuan</a>
			</div>
		</div>
		<div id="page-content-wrapper">
			<?php include "../include/navbar_dosen.php" ?>
			<div class="container-fluid">
				<h1></h1>
				<div class="card">
					<div class="card-header bg-primary text-light"><b>PROFIL</b></div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-borderless" id="tabelberkas" width="100%" cellspacing="0">
								<form action="dosen_profile_ubah_code.php?nip_dosen=<?php echo nx_e($nip_dosen); ?>" method="POST">
									<tr>
										<td width="150">NIDN</td>
										<td>
											<div class="col-sm-3">
												<input type="text" name="nip_dosen" value="<?php echo nx_e($nip_dosen); ?>" readonly="readonly" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Nama</td>
										<td>
											<div class="col-sm-8">
												<input type="text" name="nama_dosen" value="<?php echo nx_e($nama_dosen); ?>" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Unit Kerja</td>
										<td>
											<div class="col-sm-8">
												<input type="text" name="unit_kerja_dosen" value="<?php echo nx_e($unit_kerja_dosen); ?>" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Alamat</td>
										<td>
											<div class="col-sm-8">
												<input type="text" name="alamat_dosen" value="<?php echo nx_e($alamat_dosen); ?>" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Telepon</td>
										<td>
											<div class="col-sm-3">
												<input type="text" name="telepon_dosen" value="<?php echo nx_e($telepon_dosen); ?>" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Pendidikan terakhir</td>
										<td>
											<div class="col-sm-4">
												<select name="pendidikan_terakhir" class="form-control">
													<option value="">Pilih pendidikan</option>
													<?php foreach (nx_pendidikan_opsi() as $k => $v) : ?>
														<option value="<?php echo nx_e($k); ?>" <?php echo (string) $f1[0] === (string) $k ? 'selected' : ''; ?>><?php echo nx_e($v); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>Status kepegawaian</td>
										<td>
											<div class="col-sm-5">
												<select name="status_kepegawaian" class="form-control">
													<option value="">Pilih status</option>
													<?php foreach (nx_status_opsi() as $k => $v) : ?>
														<option value="<?php echo nx_e($k); ?>" <?php echo (string) $f1[1] === (string) $k ? 'selected' : ''; ?>><?php echo nx_e($v); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>Diangkat dosen tetap</td>
										<td>
											<div class="col-sm-3">
												<input type="date" name="tmt_dosen_tetap" value="<?php echo nx_e($f1[2]); ?>" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>AK kumulatif SK terakhir</td>
										<td>
											<div class="col-sm-3">
												<input type="number" name="ak_kumulatif" value="<?php echo nx_e($f1[3]); ?>" min="0" max="5000" step="0.01" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>AK penelitian kumulatif</td>
										<td>
											<div class="col-sm-3">
												<input type="number" name="ak_penelitian_kumulatif" value="<?php echo nx_e($f1[4]); ?>" min="0" max="5000" step="0.01" class="form-control">
											</div>
										</td>
									</tr>
									<tr>
										<td>Pernyataan</td>
										<td>
											<div class="col-sm-9">
												<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="skp_baik" name="skp_baik" value="1" <?php echo $f1[6] ? 'checked' : ''; ?>><label class="custom-control-label" for="skp_baik">Predikat SKP minimal baik, 2 tahun berturut-turut</label></div>
												<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="uji_kompetensi" name="uji_kompetensi" value="1" <?php echo $f1[7] ? 'checked' : ''; ?>><label class="custom-control-label" for="uji_kompetensi">Sudah lulus uji kompetensi</label></div>
												<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="sertifikat_pendidik" name="sertifikat_pendidik" value="1" <?php echo $f1[5] ? 'checked' : ''; ?>><label class="custom-control-label" for="sertifikat_pendidik">Memiliki sertifikat pendidik</label></div>
												<small class="form-text text-muted">Pernyataan ini diverifikasi kepegawaian saat memeriksa pengajuan.</small>
											</div>
										</td>
									</tr>
									<tr>
										<td>Password</td>
										<td>
											<div class="col-sm-3">
												<input type="password" name="password_dosen" value="" class="form-control" minlength="6" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengganti">
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<input style="margin: 0 0 0 130%;" type="submit" name="ubah" value="Simpan" class="btn btn-success">
										</td>
									</tr>
								</form>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script src="../asset/vendor/jquery/jquery.min.js"></script>
	<script src="../asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

	<script nonce="<?php echo nx_nonce(); ?>">
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});
	</script>
	<script src="../asset/js/nexus.js"></script>
</body>

</html>