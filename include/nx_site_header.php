<?php
/* Header halaman publik (index/about). $nx_here = 'beranda' | 'tentang' */
$nx_here = isset($nx_here) ? $nx_here : 'beranda';
?>
<header class="nx-site-nav" id="nx-site-nav">
	<div class="nx-wrap">
		<a class="nx-site-brand" href="index.php" aria-label="SIPAK — beranda">
			<img src="assets/img/logo/FT.png" alt="">
			<span>SIPAK<small>Fakultas Teknik UHAMKA</small></span>
		</a>
		<ul class="nx-site-menu">
			<li><a href="index.php"<?php echo $nx_here === 'beranda' ? ' class="is-here" aria-current="page"' : ''; ?>>Beranda</a></li>
			<li><a href="about.php"<?php echo $nx_here === 'tentang' ? ' class="is-here" aria-current="page"' : ''; ?>>Tentang Kami</a></li>
			<li><a href="register.php" class="btn btn-primary">Daftar Disini</a></li>
		</ul>
	</div>
</header>
