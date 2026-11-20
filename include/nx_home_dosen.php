<?php
/* Beranda dosen: sambutan, statistik, tangga jabatan, distribusi status. Hanya SELECT. */
require_once __DIR__ . '/nx_helpers.php';

$nx_nip    = $koneksi->qstr($nip_dosen);
$nx_counts = nx_status_counts($koneksi, "nip_dosen = $nx_nip");
$nx_q      = 'nip_dosen=' . rawurlencode($nip_dosen);

// Pengajuan terbaru -> posisi pada tangga jabatan
$nx_asal = $nx_tujuan = '';
$rs_last = $koneksi->Execute("SELECT jabatan_asal, jabatan_tujuan FROM pengajuan WHERE nip_dosen = $nx_nip ORDER BY id_pengajuan DESC LIMIT 1");
if ($rs_last && !$rs_last->EOF) {
	$nx_asal   = $rs_last->fields[0];
	$nx_tujuan = $rs_last->fields[1];
}
$nx_i_asal   = nx_stage_index($nx_asal);
$nx_i_tujuan = nx_stage_index($nx_tujuan);
$nx_rungs = array(
	array('Asisten Ahli',  'III/a – III/b'),
	array('Lektor',        'III/c – III/d'),
	array('Lektor Kepala', 'IV/a – IV/c'),
	array('Guru Besar',    'IV/d – IV/e'),
);
?>
<section class="nx-welcome" aria-labelledby="nx-hello">
	<div>
		<span class="nx-eyebrow"><i class="fas fa-graduation-cap" aria-hidden="true"></i><span data-greeting>Selamat datang</span></span>
		<h2 id="nx-hello"><?php echo nx_e($nama_dosen); ?></h2>
		<p>Simulasikan angka kredit, unggah berkas, dan pantau setiap tahap pengajuan kenaikan jabatan fungsional Anda di satu tempat.</p>
	</div>
	<div class="nx-actions">
		<a class="btn btn-light-solid" href="dosen_pengajuan_input.php?<?php echo $nx_q; ?>"><i class="fas fa-plus" aria-hidden="true"></i>Ajukan kenaikan jabatan</a>
		<a class="btn btn-ghost" href="dosen_pengajuan_riwayat.php?<?php echo $nx_q; ?>">Lihat riwayat</a>
	</div>
</section>

<div class="nx-stats">
	<?php
	nx_stat_card('dosen_pengajuan_riwayat.php?' . $nx_q, 'fa-pen', '', 'Belum dikirim', $nx_counts['BELUM DIKIRIM'], 'Draf yang menunggu Anda');
	nx_stat_card('dosen_pengajuan_riwayat_terkirim.php?' . $nx_q, 'fa-paper-plane', 'kirim', 'Dalam proses', nx_sum($nx_counts, array('TERKIRIM', 'TELAH REVISI', 'BERKAS DITERIMA', 'PENILAIAN SELESAI')), 'Sedang diperiksa & dinilai');
	nx_stat_card('dosen_pengajuan_riwayat_berkas_ditolak.php?' . $nx_q, 'fa-redo', 'revisi', 'Perlu revisi', $nx_counts['BERKAS DITOLAK'], 'Berkas yang harus diperbaiki');
	nx_stat_card('dosen_pengajuan_riwayat_selesai.php?' . $nx_q, 'fa-check', 'lulus', 'Selesai', nx_sum($nx_counts, array('DITERIMA', 'DITOLAK')), 'Keputusan akhir diterbitkan');
	?>
</div>

<div class="nx-grid">
	<section class="nx-panel" aria-labelledby="nx-ladder-t">
		<div class="nx-panel-head">
			<h3 class="nx-panel-title" id="nx-ladder-t">Tangga jabatan fungsional</h3>
			<span class="nx-panel-sub">Berdasarkan pengajuan terbaru</span>
		</div>
		<div class="nx-ladder" role="list">
			<?php foreach ($nx_rungs as $i => $r) :
				$cls = '';
				$tag = '&nbsp;';
				if ($nx_i_asal === $i)                        { $cls = 'is-now';  $tag = 'Saat ini'; }
				elseif ($nx_i_tujuan === $i)                  { $cls = 'is-next'; $tag = 'Tujuan'; }
				elseif ($nx_i_asal >= 0 && $i < $nx_i_asal)   { $cls = 'is-done'; }
				?>
				<div class="nx-rung <?php echo $cls; ?>" role="listitem">
					<span class="nx-rung-dot" aria-hidden="true"><?php echo $i + 1; ?></span>
					<span class="nx-rung-name"><?php echo nx_e($r[0]); ?><br><small><?php echo nx_e($r[1]); ?></small></span>
					<span class="nx-rung-tag"><?php echo $tag; ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="nx-ladder-note">
			<?php if ($nx_asal !== '') : ?>
				Pengajuan terakhir: <b><?php echo nx_e($nx_asal); ?></b> menuju <b><?php echo nx_e($nx_tujuan); ?></b>.
			<?php else : ?>
				Anda belum memiliki pengajuan. Mulai dengan <b>Ajukan kenaikan jabatan</b> untuk melihat posisi Anda di sini.
			<?php endif; ?>
		</p>
	</section>

	<section class="nx-panel" aria-labelledby="nx-status-t">
		<div class="nx-panel-head">
			<h3 class="nx-panel-title" id="nx-status-t">Status pengajuan Anda</h3>
			<span class="nx-panel-sub"><?php echo array_sum($nx_counts); ?> total</span>
		</div>
		<?php nx_status_bars($nx_counts); ?>
	</section>
</div>
