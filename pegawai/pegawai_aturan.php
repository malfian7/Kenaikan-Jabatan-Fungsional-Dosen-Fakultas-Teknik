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
include_once("../include/nx_aturan.php");
$data_aturan = array();
foreach (array('Lektor', 'Lektor Kepala', 'Profesor') as $nm_jenjang) {
	$a = nx_aturan_jenjang($koneksi, $nm_jenjang);
	if ($a) { $data_aturan[] = $a; }
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
				<a href="pegawai_aturan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-info text-light">Aturan Jabatan</a>
				<a href="pegawai_pengaturan.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" class="list-group-item list-group-item-action bg-light">Pengaturan</a>
			</div>
		</div>
		<div id="page-content-wrapper">
			<?php include "../include/navbar_pegawai.php" ?>
			<div class="container-fluid">
				<h1></h1>
				<div class="card">
					<div class="card-header bg-primary text-light"><b>ATURAN JABATAN</b></div>
					<div class="card-body">
						<p class="text-muted" style="font-size:13px">Kolom: minimal tahun di jabatan sebelumnya, minimal tahun sebagai dosen tetap, pendidikan minimal, jumlah publikasi syarat khusus, dan apakah publikasi memblokir pengiriman. Parameter kenaikan jenjang yang dipakai panel kelayakan dan tombol kirim dosen. Nilai awal mengikuti <?php echo nx_e(NX_ATURAN_SUMBER); ?>. Ubah hanya bila institusi menetapkan aturan yang berbeda; perubahan berlaku seketika untuk semua pengajuan yang belum dikirim.</p>
						<form action="pegawai_aturan_ubah_code.php?nip_pegawai=<?php echo nx_e($nip_pegawai); ?>" method="post">
							<div class="table-responsive">
								<table class="table table-bordered" style="vertical-align:middle">
									<thead>
										<tr>
											<th>Jenjang tujuan</th>
											<th>Th di jabatan</th>
											<th>Th dosen tetap</th>
											<th>Pendidikan min.</th>
											<th>Publikasi</th>
											<th>Wajib</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($data_aturan as $a) : $jn = $a['jenjang']; ?>
											<tr>
												<td style="min-width:170px"><b><?php echo nx_e($jn); ?></b><div style="font-size:12px;margin-top:4px;color:#62667f"><?php echo nx_e($a['sumber']); ?></div><div style="margin-top:6px"><?php echo strpos($a['sumber'], 'diubah institusi') !== false ? '<span class="nx-badge nx-badge--revisi">Disesuaikan institusi</span>' : ($a['terverifikasi'] ? '<span class="nx-badge nx-badge--lulus">Sesuai juknis</span>' : '<span class="nx-badge nx-badge--netral">Belum diverifikasi</span>'); ?></div></td>
												<td><input type="number" class="form-control" name="min_tahun_jabatan[<?php echo nx_e($jn); ?>]" value="<?php echo nx_e($a['min_tahun_jabatan']); ?>" min="0" max="30" step="1" required></td>
												<td><input type="number" class="form-control" name="min_tahun_dosen_tetap[<?php echo nx_e($jn); ?>]" value="<?php echo nx_e($a['min_tahun_dosen_tetap']); ?>" min="0" max="40" step="1" required></td>
												<td>
													<select class="form-control" name="pendidikan_min[<?php echo nx_e($jn); ?>]">
														<option value="">Tidak ada</option>
														<?php foreach (array('S2' => 'S2', 'S3' => 'S3 / Doktor') as $k => $v) : ?>
															<option value="<?php echo nx_e($k); ?>" <?php echo (string) $a['pendidikan_min'] === (string) $k ? 'selected' : ''; ?>><?php echo nx_e($v); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td><input type="number" class="form-control" name="jumlah_publikasi[<?php echo nx_e($jn); ?>]" value="<?php echo nx_e($a['jumlah_publikasi']); ?>" min="0" max="5" step="1" required></td>
												<td>
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="custom-control-input" id="wp_<?php echo nx_e(md5($jn)); ?>" name="wajib_publikasi[<?php echo nx_e($jn); ?>]" value="1" <?php echo $a['wajib_publikasi'] ? 'checked' : ''; ?>>
														<label class="custom-control-label" for="wp_<?php echo nx_e(md5($jn)); ?>">Wajib</label>
													</div>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<p class="text-muted" style="font-size:12px">Jenis dan peringkat publikasi yang diterima per jenjang mengikuti Tabel 4-6 juknis dan tidak diubah dari sini. Syarat pendidikan Lektor dan Lektor Kepala tidak ditemukan di juknis, jadi dibiarkan kosong. Isi bila institusi mensyaratkannya.</p>
							<button type="submit" class="btn btn-success">SIMPAN ATURAN</button>
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