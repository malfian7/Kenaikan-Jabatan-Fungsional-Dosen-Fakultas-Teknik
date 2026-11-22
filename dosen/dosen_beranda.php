<?php
include_once("../koneksi/conn.php");
$nip_dosen = $_GET['nip_dosen'];
$select_dosen = "SELECT * FROM dosen WHERE nip_dosen = '$nip_dosen'";
$rs_dosen = $koneksi->Execute($select_dosen);
if ($rs_dosen->recordCount() > 0) {
	while (!$rs_dosen->EOF) {
		$nama_dosen = $rs_dosen->fields[2];
		$rs_dosen->MoveNext();
	}
}
?>

<!-- Frontend Start -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="manifest" href="site.webmanifest">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo nx_e(nx_logo('../')); ?>">

	<!-- css Here -->
	<title>Sistem Informasi Kenaikan Jabatan Dosen</title>
	<link href="../asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../asset/css/simple-sidebar.css" rel="stylesheet">
	<link href="../asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="../asset/css/nexus.css" rel="stylesheet">
</head>

<body>

	<!-- Sidebar Menu  Start-->
	<div class="d-flex" id="wrapper">
		<div class="bg-light border-right" id="sidebar-wrapper">
			<div class="sidebar-heading"><b>Main Menu</b></div>
			<div class="list-group list-group-flush">
				<a href="dosen_beranda.php?nip_dosen=<?php echo nx_e($nip_dosen) ?>" class="list-group-item list-group-item-action bg-info text-light">Beranda</a>
				<a href="dosen_pengajuan_riwayat.php?nip_dosen=<?php echo nx_e($nip_dosen) ?>" class="list-group-item list-group-item-action bg-light">Riwayat Pengajuan</a>
			</div>
		</div>
		<!-- Sidebar Menu End -->

		<!-- Halaman Depan Dosen -->
		<div id="page-content-wrapper">
			<?php include "../include/navbar_dosen.php" ?>
			<div class="container-fluid">
				<h1></h1>
				<?php include "../include/nx_home_dosen.php"; ?>
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
<!-- Frontend End -->