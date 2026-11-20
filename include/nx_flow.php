<?php
/* Panel "Alur kerja" untuk pegawai & penilai. $nx_flow_role = 'pegawai' | 'penilai' */
$nx_flow_steps = array(
	array('dosen',   'Dosen mengajukan',        'Mengisi data jabatan dan mengunggah berkas pendukung.'),
	array('pegawai', 'Pegawai memeriksa berkas', 'Memvalidasi keaslian berkas; berkas tidak valid dikembalikan untuk revisi.'),
	array('penilai', 'Penilai menilai',          'Dua penilai menentukan angka kredit berdasarkan rubrik.'),
	array('pegawai', 'Pegawai menyetujui',       'Menyetujui hasil penilaian dan membuat SK kenaikan jabatan.'),
);
?>
<section class="nx-panel" aria-labelledby="nx-flow-t">
	<div class="nx-panel-head">
		<h3 class="nx-panel-title" id="nx-flow-t">Alur kerja</h3>
		<span class="nx-panel-sub">Peran Anda ditandai</span>
	</div>
	<ol class="nx-flow">
		<?php foreach ($nx_flow_steps as $i => $s) : ?>
			<li class="<?php echo $s[0] === $nx_flow_role ? 'is-me' : ''; ?>">
				<span class="nx-flow-n"><?php echo $i + 1; ?></span>
				<span class="nx-flow-t"><strong><?php echo nx_e($s[1]); ?></strong><span><?php echo nx_e($s[2]); ?></span></span>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
