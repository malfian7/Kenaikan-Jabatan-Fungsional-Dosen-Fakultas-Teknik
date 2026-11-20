<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Sistem Informasi Kenaikan Jabatan Dosen Fakultas Teknik UHAMKA">
	<meta name="author" content="">

	<link rel="manifest" href="site.webmanifest">
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo/FT.png">

	<title>Masuk sebagai Dosen · SIPAK</title>
	<link href="../asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="../asset/css/nexus.css" rel="stylesheet">
</head>

<body class="nx-login" data-role-page="dosen">
	<div class="nx-auth">
		<aside class="nx-auth-aside">
			<a class="nx-auth-brand" href="../index.php"><img src="../assets/img/logo/FT.png" alt="">SIPAK · Fakultas Teknik</a>
			<div class="nx-auth-hero">
				<h1>Ajukan kenaikan jabatan tanpa tumpukan berkas.</h1>
				<p>Simulasikan angka kredit, unggah berkas pendukung, dan pantau setiap tahap pengajuan Anda secara daring.</p>
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
				<a class="nx-back" href="../index.php"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke beranda</a>
				<h2>Masuk sebagai Dosen</h2>
				<p class="nx-sub">Masukkan NIDN dan kata sandi Anda untuk melanjutkan.</p>
				<div class="nx-alert" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true" style="margin-top:2px"></i><span>NIDN atau kata sandi salah. Silakan coba lagi.</span></div>
				<form action="dosen_login_code.php" method="POST">
					<div class="nx-field">
						<label for="nip_dosen">NIDN</label>
						<div class="nx-input-wrap">
							<i class="fas fa-user-graduate" aria-hidden="true"></i>
							<input type="text" name="nip_dosen" id="nip_dosen" class="form-control" placeholder="Masukkan NIDN" required="required" autofocus="autofocus" autocomplete="username" inputmode="numeric">
						</div>
					</div>
					<div class="nx-field">
						<label for="password_dosen">Kata sandi</label>
						<div class="nx-input-wrap">
							<i class="fas fa-lock" aria-hidden="true"></i>
							<input type="password" name="password_dosen" id="password_dosen" class="form-control" placeholder="Masukkan kata sandi" required="required" autocomplete="current-password">
							<button type="button" class="nx-pw-toggle" data-target="password_dosen" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="fas fa-eye" aria-hidden="true"></i></button>
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
