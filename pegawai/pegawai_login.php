<?php include_once("../koneksi/guard.php"); ?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Sistem Informasi Kenaikan Jabatan Dosen <?php echo nx_e(nx_pengaturan('institusi')); ?>">
	<meta name="author" content="">

	<link rel="manifest" href="site.webmanifest">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo nx_e(nx_logo('../')); ?>">

	<title>Masuk sebagai Pegawai · SIPAK</title>
	<link href="../asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="../asset/css/nexus.css" rel="stylesheet">
</head>

<body class="nx-login" data-role-page="pegawai">
	<div class="nx-auth">
		<aside class="nx-auth-aside">
			<a class="nx-auth-brand" href="../index.php"><img src="<?php echo nx_e(nx_logo('../')); ?>" alt=""><?php echo nx_e(nx_pengaturan('app')); ?> · <?php echo nx_e(nx_pengaturan('unit')); ?></a>
			<div class="nx-auth-hero">
				<h1>Periksa berkas dengan cepat dan teliti.</h1>
				<p>Validasi keaslian berkas pengajuan, kelola tim penilai, lalu terbitkan SK kenaikan jabatan.</p>
				<ul class="nx-auth-points">
					<li>Validasi berkas</li>
					<li>Kelola penilai</li>
					<li>Terbitkan SK</li>
				</ul>
			</div>
			<div class="nx-auth-foot">Sistem Informasi Kenaikan Jabatan Dosen · Universitas Muhammadiyah Prof. DR. HAMKA</div>
		</aside>

		<main class="nx-auth-main">
			<div class="nx-auth-card">
				<a class="nx-back" href="../index.php"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke beranda</a>
				<h2>Masuk sebagai Pegawai</h2>
				<p class="nx-sub">Masukkan NIP dan kata sandi Anda untuk melanjutkan.</p>

				<form action="pegawai_login_code.php" method="POST">
					<div class="nx-field">
						<label for="nip_pegawai">NIP</label>
						<div class="nx-input-wrap">
							<i class="fas fa-clipboard-check" aria-hidden="true"></i>
							<input type="text" name="nip_pegawai" id="nip_pegawai" class="form-control" placeholder="Masukkan NIP" required="required" autofocus="autofocus" autocomplete="username" inputmode="numeric">
						</div>
					</div>
					<div class="nx-field">
						<label for="password_pegawai">Kata sandi</label>
						<div class="nx-input-wrap">
							<i class="fas fa-lock" aria-hidden="true"></i>
							<input type="password" name="password_pegawai" id="password_pegawai" class="form-control" placeholder="Masukkan kata sandi" required="required" autocomplete="current-password">
							<button type="button" class="nx-pw-toggle" data-target="password_pegawai" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="fas fa-eye" aria-hidden="true"></i></button>
						</div>
					</div>
					<input type="submit" name="login" value="Login" class="btn btn-primary btn-block text-light">
				</form>
			</div>
		</main>
	</div>

	<script src="../asset/vendor/jquery/jquery.min.js"></script>
	<script src="../asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="../asset/js/nexus.js"></script>
</body>

</html>
