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
						<a class="btn btn-success" href="dosen_profile_ubah.php?nip_dosen=<?php echo nx_e($nip_dosen); ?>">UBAH PROFIL</a>
						<h1></h1>
						<div class="table-responsive">
							<table class="table table-borderless" id="tabelberkas" width="100%" cellspacing="0">
								<tr>
									<td width="150">NIDN</td>
									<td><b><?php echo nx_e($nip_dosen); ?></b></td>
								</tr>
								<tr>
									<td>Nama</td>
									<td><?php echo nx_e($nama_dosen); ?></td>
								</tr>
								<tr>
									<td>Unit Kerja</td>
									<td><?php echo nx_e($unit_kerja_dosen); ?></td>
								</tr>
								<tr>
									<td>Alamat</td>
									<td><?php echo nx_e($alamat_dosen); ?></td>
								</tr>
								<tr>
									<td>Telepon</td>
									<td><?php echo nx_e($telepon_dosen); ?></td>
								</tr>
								<tr>
									<td>Pendidikan terakhir</td>
									<td><?php $po = nx_pendidikan_opsi(); echo $f1[0] !== '' && $f1[0] !== null ? nx_e(isset($po[$f1[0]]) ? $po[$f1[0]] : $f1[0]) : '<span class="text-muted">Belum diisi</span>'; ?></td>
								</tr>
								<tr>
									<td>Status kepegawaian</td>
									<td><?php $so = nx_status_opsi(); echo $f1[1] !== '' && $f1[1] !== null ? nx_e(isset($so[$f1[1]]) ? $so[$f1[1]] : $f1[1]) : '<span class="text-muted">Belum diisi</span>'; ?></td>
								</tr>
								<tr>
									<td>Diangkat dosen tetap</td>
									<td><?php echo $f1[2] ? nx_e($f1[2]) : '<span class="text-muted">Belum diisi</span>'; ?></td>
								</tr>
								<tr>
									<td>AK kumulatif SK terakhir</td>
									<td><?php echo nx_e(nx_num($f1[3])); ?> <span class="text-muted">(penelitian: <?php echo nx_e(nx_num($f1[4])); ?>)</span></td>
								</tr>
								<tr>
									<td>Pernyataan</td>
									<td>
										<?php
										$pn = array();
										if ($f1[6]) $pn[] = 'SKP baik 2 tahun';
										if ($f1[7]) $pn[] = 'Uji kompetensi';
										if ($f1[5]) $pn[] = 'Sertifikat pendidik';
										echo $pn ? nx_e(implode(', ', $pn)) : '<span class="text-muted">Belum ada yang dinyatakan</span>';
										?>
									</td>
								</tr>
								<tr>
									<td>Password</td>
									<td>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>
								</tr>
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