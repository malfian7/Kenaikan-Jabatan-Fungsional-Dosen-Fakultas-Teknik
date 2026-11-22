<?php
/* Header halaman publik (index/about). $nx_here = 'beranda' | 'tentang' */
$nx_here = isset($nx_here) ? $nx_here : 'beranda';
?>
<header class="nx-site-nav" id="nx-site-nav">
	<div class="nx-wrap">
		<a class="nx-site-brand" href="index.php" aria-label="<?php echo nx_e(nx_pengaturan('app')); ?> — beranda">
			<img src="<?php echo nx_e(nx_logo('')); ?>" alt="">
			<span><?php echo nx_e(nx_pengaturan('app')); ?><small><?php echo nx_e(nx_pengaturan('institusi')); ?></small></span>
		</a>
		<ul class="nx-site-menu">
			<li><a href="index.php"<?php echo $nx_here === 'beranda' ? ' class="is-here" aria-current="page"' : ''; ?>>Beranda</a></li>
			<li><a href="about.php"<?php echo $nx_here === 'tentang' ? ' class="is-here" aria-current="page"' : ''; ?>>Tentang Kami</a></li>
			<li><a href="register.php" class="btn btn-primary">Daftar Disini</a></li>
		</ul>
	</div>
</header>
