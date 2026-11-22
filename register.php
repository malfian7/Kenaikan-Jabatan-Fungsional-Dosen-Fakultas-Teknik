<?php include_once __DIR__ . '/koneksi/conn.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Daftar akun dosen SIPAK <?php echo nx_e(nx_pengaturan('institusi')); ?>">
	<meta name="author" content="">

	<link rel="manifest" href="site.webmanifest">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo nx_e(nx_logo('')); ?>">

	<title>Daftar · SIPAK</title>
	<link href="asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="asset/css/nexus.css" rel="stylesheet">
</head>

<body class="nx-login">
	<div class="nx-auth">
		<aside class="nx-auth-aside">
			<a class="nx-auth-brand" href="index.php"><img src="<?php echo nx_e(nx_logo('')); ?>" alt=""><?php echo nx_e(nx_pengaturan('app')); ?> · <?php echo nx_e(nx_pengaturan('unit')); ?></a>
			<div class="nx-auth-hero">
				<h1>Mulai perjalanan kenaikan jabatan Anda.</h1>
				<p>Buat akun dosen untuk menyimulasikan angka kredit, mengunggah berkas, dan memantau status pengajuan.</p>
				<ul class="nx-auth-points">
					<li>Simulasi angka kredit</li>
					<li>Unggah berkas</li>
					<li>Pantau status</li>
				</ul>
			</div>
			<div class="nx-auth-foot">Sistem Informasi Kenaikan Jabatan Dosen · Universitas Muhammadiyah Prof. DR. HAMKA</div>
		</aside>

		<main class="nx-auth-main">
			<div class="nx-auth-card">
				<a class="nx-back" href="index.php"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke beranda</a>
				<h2>Daftar akun dosen</h2>
				<p class="nx-sub">Lengkapi data berikut untuk membuat akun.</p>

				<form action="" method="post">
					<div class="nx-field">
						<label for="nidn">NIDN</label>
						<div class="nx-input-wrap">
							<i class="fas fa-id-card" aria-hidden="true"></i>
							<input class="form-control" type="text" id="nidn" name="nidn" placeholder="Masukkan NIDN" autocomplete="username" inputmode="numeric">
						</div>
					</div>
					<div class="nx-field">
						<label for="email">Nama lengkap</label>
						<div class="nx-input-wrap">
							<i class="fas fa-user" aria-hidden="true"></i>
							<input class="form-control" type="text" id="email" name="email" placeholder="Nama lengkap dan gelar" autocomplete="name">
						</div>
					</div>
					<div class="nx-field">
						<label for="password">Kata sandi</label>
						<div class="nx-input-wrap">
							<i class="fas fa-lock" aria-hidden="true"></i>
							<input class="form-control" type="password" id="password" name="password" placeholder="Buat kata sandi" autocomplete="new-password">
							<button type="button" class="nx-pw-toggle" data-target="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="fas fa-eye" aria-hidden="true"></i></button>
						</div>
					</div>
					<div class="nx-field">
						<label for="telepon">Telepon</label>
						<div class="nx-input-wrap">
							<i class="fas fa-phone" aria-hidden="true"></i>
							<input class="form-control" type="text" id="telepon" name="telepon" placeholder="Nomor telepon" autocomplete="tel" inputmode="tel">
						</div>
					</div>
					<div class="nx-field">
						<label style="display:flex;align-items:center;gap:10px;font-weight:500;margin:0">
							<input type="checkbox" name="aggree" style="width:16px;height:16px">Setuju dengan syarat dan ketentuan
						</label>
					</div>
					<button class="btn btn-primary btn-block" type="submit">Daftar</button>
				</form>
				<p class="nx-alt">Anda sudah mempunyai akun? <a href="dosen/dosen_login.php">Login</a></p>
			</div>
		</main>
	</div>

	<script src="asset/vendor/jquery/jquery.min.js"></script>
	<script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="asset/js/nexus.js"></script>
</body>

</html>
