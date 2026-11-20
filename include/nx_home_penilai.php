<?php
/* Beranda penilai. Hanya SELECT. */
require_once __DIR__ . '/nx_helpers.php';

$nx_nip    = $koneksi->qstr($nip_penilai);
$nx_where  = "(nip_penilai_1 = $nx_nip OR nip_penilai_2 = $nx_nip)";
$nx_counts = nx_status_counts($koneksi, $nx_where);
$nx_q      = 'nip_penilai=' . rawurlencode($nip_penilai);
$nx_rubrik = (int) $koneksi->GetOne('SELECT COUNT(*) FROM rubrik');
$nx_flow_role = 'penilai';
?>
<section class="nx-welcome" aria-labelledby="nx-hello">
	<div>
		<span class="nx-eyebrow"><i class="fas fa-calculator" aria-hidden="true"></i><span data-greeting>Selamat datang</span></span>
		<h2 id="nx-hello"><?php echo nx_e($nama_penilai); ?></h2>
		<p>Nilai berkas pengajuan yang ditugaskan kepada Anda dan tentukan angka kredit berdasarkan rubrik penilaian.</p>
	</div>
	<div class="nx-actions">
		<a class="btn btn-light-solid" href="penilai_daftar_pengajuan.php?<?php echo $nx_q; ?>"><i class="fas fa-tasks" aria-hidden="true"></i>Daftar pengajuan</a>
		<a class="btn btn-ghost" href="penilai_tabel_pak.php?<?php echo $nx_q; ?>">Tabel angka kredit</a>
	</div>
</section>

<div class="nx-stats">
	<?php
	nx_stat_card('penilai_daftar_pengajuan.php?' . $nx_q, 'fa-inbox', '', 'Ditugaskan', array_sum($nx_counts), 'Seluruh pengajuan untuk Anda');
	nx_stat_card('penilai_daftar_pengajuan.php?' . $nx_q, 'fa-hourglass-half', 'revisi', 'Menunggu penilaian', $nx_counts['BERKAS DITERIMA'], 'Perlu Anda nilai');
	nx_stat_card('penilai_daftar_pengajuan_selesai.php?' . $nx_q, 'fa-check', 'lulus', 'Penilaian selesai', nx_sum($nx_counts, array('PENILAIAN SELESAI', 'DITERIMA', 'DITOLAK')), 'Sudah Anda selesaikan');
	nx_stat_card('penilai_tabel_pak.php?' . $nx_q, 'fa-calculator', 'kirim', 'Butir rubrik', $nx_rubrik, 'Acuan angka kredit');
	?>
</div>

<div class="nx-grid">
	<section class="nx-panel" aria-labelledby="nx-status-t">
		<div class="nx-panel-head">
			<h3 class="nx-panel-title" id="nx-status-t">Status pengajuan yang Anda nilai</h3>
			<span class="nx-panel-sub"><?php echo array_sum($nx_counts); ?> total</span>
		</div>
		<?php nx_status_bars($nx_counts); ?>
	</section>
	<?php include __DIR__ . '/nx_flow.php'; ?>
</div>
