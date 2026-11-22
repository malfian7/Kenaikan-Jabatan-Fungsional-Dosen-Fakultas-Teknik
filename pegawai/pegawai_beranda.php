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
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo/FT.png">

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
				<a href="pegawai_beranda.php?nip_pegawai=<?php echo nx_e($nip_pegawai) ?>" class="list-group-item list-group-item-action bg-info text-light">Beranda</a>
				<a href="pegawai_data_penilai.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Data Penilai</a>
				<a href="pegawai_data_pengajuan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Data Pengajuan</a>
			</div>
		</div>
		<div id="page-content-wrapper">
			<?php include "../include/navbar_pegawai.php" ?>
			<div class="container-fluid">
				<h1></h1>
				<?php include "../include/nx_home_pegawai.php"; ?>
			</div>
		</div>
	</div>


	<script src="../asset/vendor/jquery/jquery.min.js"></script>
	<script src="../asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

	<script>
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});
	</script>
	<script src="../asset/js/nexus.js"></script>
</body>

</html>