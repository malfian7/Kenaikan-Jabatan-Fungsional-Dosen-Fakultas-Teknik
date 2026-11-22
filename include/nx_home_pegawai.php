<?php
/* Beranda pegawai. Hanya SELECT. */
require_once __DIR__ . '/nx_helpers.php';

$nx_counts = nx_status_counts($koneksi, '1=1');
$nx_q      = 'nip_pegawai=' . rawurlencode($nip_pegawai);
$nx_penilai_total = (int) $koneksi->GetOne('SELECT COUNT(*) FROM penilai');
$nx_flow_role = 'pegawai';
?>
<section class="nx-welcome" aria-labelledby="nx-hello">
	<div>
		<span class="nx-eyebrow"><i class="fas fa-clipboard-check" aria-hidden="true"></i><span data-greeting>Selamat datang</span></span>
		<h2 id="nx-hello"><?php echo nx_e($nama_pegawai); ?></h2>
		<p>Periksa keaslian berkas pengajuan, kelola tim penilai, lalu setujui hasil penilaian dan terbitkan SK.</p>
	</div>
	<div class="nx-actions">
		<a class="btn btn-light-solid" href="pegawai_data_pengajuan.php?<?php echo $nx_q; ?>"><i class="fas fa-file-alt" aria-hidden="true"></i>Periksa pengajuan</a>
		<a class="btn btn-ghost" href="pegawai_data_penilai.php?<?php echo $nx_q; ?>">Data penilai</a>
		<a class="btn btn-ghost" href="pegawai_ekspor_pengajuan.php?<?php echo $nx_q; ?>">Ekspor CSV</a>
	</div>
</section>

<div class="nx-stats">
	<?php
	nx_stat_card('pegawai_data_pengajuan.php?' . $nx_q, 'fa-search', 'kirim', 'Perlu diperiksa', nx_sum($nx_counts, array('TERKIRIM', 'TELAH REVISI')), 'Terkirim & telah direvisi');
	nx_stat_card('pegawai_data_pengajuan_berkas_diterima.php?' . $nx_q, 'fa-hourglass-half', 'revisi', 'Menunggu penilaian', $nx_counts['BERKAS DITERIMA'], 'Berkas valid, belum dinilai');
	nx_stat_card('pegawai_persetujuan.php?' . $nx_q, 'fa-check-circle', '', 'Menunggu persetujuan', $nx_counts['PENILAIAN SELESAI'], 'Penilaian selesai');
	nx_stat_card('pegawai_data_penilai.php?' . $nx_q, 'fa-users', 'lulus', 'Penilai terdaftar', $nx_penilai_total, 'Tim penilai angka kredit');
	?>
</div>

<div class="nx-grid">
	<section class="nx-panel" aria-labelledby="nx-status-t">
		<div class="nx-panel-head">
			<h3 class="nx-panel-title" id="nx-status-t">Distribusi status pengajuan</h3>
			<span class="nx-panel-sub"><?php echo array_sum($nx_counts); ?> total</span>
		</div>
		<?php nx_status_bars($nx_counts); ?>
	</section>
	<?php include __DIR__ . '/nx_flow.php'; ?>
</div>
