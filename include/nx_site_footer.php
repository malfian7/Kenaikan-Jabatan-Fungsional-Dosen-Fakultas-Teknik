<footer class="nx-footer">
	<div class="nx-wrap">
		<div>
			<div class="nx-brandline"><img src="assets/img/logo/FT.png" alt="">Fakultas Teknik UHAMKA</div>
			<p class="nx-copy">Sistem Informasi Kenaikan Jabatan Dosen</p>
			<p class="nx-copy">&copy; <?php echo date('Y'); ?> Hak cipta dilindungi.</p>
		</div>
		<div>
			<h4>Kontak</h4>
			<p>Jl. Tanah Merdeka No.6, Kp. Rambutan, Ciracas, Jakarta Timur.</p>
			<p>Telp: (021) 8400941 &middot; Fax: (021) 87782739</p>
			<p>ft@uhamka.ac.id</p>
		</div>
		<div>
			<h4>Tautan</h4>
			<p><a href="index.php">Beranda</a></p>
			<p><a href="about.php">Tentang Kami</a></p>
			<p><a href="register.php">Daftar Disini</a></p>
		</div>
	</div>
</footer>
<script>
	(function () {
		var nav = document.getElementById('nx-site-nav');
		if (!nav) return;
		function s() { nav.classList.toggle('is-scrolled', (window.scrollY || document.documentElement.scrollTop) > 6); }
		window.addEventListener('scroll', s, { passive: true });
		s();
	})();
</script>
