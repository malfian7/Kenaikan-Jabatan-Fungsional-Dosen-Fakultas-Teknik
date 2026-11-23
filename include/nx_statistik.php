<?php
/* Statistik pengajuan untuk pegawai. Hanya SELECT. Tahap proses dihitung dari riwayat_status (Fase 2). */
include_once __DIR__ . '/nx_helpers.php';

function nx_durasi($detik)
{
	if ($detik === null) { return '-'; }
	if ($detik < 3600) { return max(1, (int) round($detik / 60)) . ' menit'; }
	if ($detik < 172800) { return str_replace('.', ',', (string) round($detik / 3600, 1)) . ' jam'; }
	return str_replace('.', ',', (string) round($detik / 86400, 1)) . ' hari';
}

function nx_statistik_data($db)
{
	$d = array();
	$d['status'] = nx_status_counts($db, '1=1');
	$d['total'] = array_sum($d['status']);

	/* 12 bulan terakhir berdasarkan data (berakhir di bulan pengajuan terbaru). */
	$per = array();
	foreach ((array) $db->GetAll("SELECT DATE_FORMAT(tanggal_pengajuan, '%Y-%m'), COUNT(*) FROM pengajuan GROUP BY 1") as $r) { $per[$r[0]] = (int) $r[1]; }
	$akhir = $per ? max(array_keys($per)) : date('Y-m');
	$t = strtotime($akhir . '-01');
	$bulan = array();
	for ($i = 11; $i >= 0; $i--) {
		$k = date('Y-m', strtotime('-' . $i . ' month', $t));
		$bulan[] = array($k, isset($per[$k]) ? $per[$k] : 0);
	}
	$d['bulanan'] = $bulan;

	$dit = (int) $d['status']['DITERIMA']; $tol = (int) $d['status']['DITOLAK'];
	$d['diterima'] = $dit; $d['ditolak'] = $tol;
	$d['rasio_terima'] = ($dit + $tol) > 0 ? $dit / ($dit + $tol) : null;

	/* Lama tiap tahap: kejadian pertama tiap status per pengajuan. */
	$awal = array();
	foreach ((array) $db->GetAll("SELECT id_pengajuan, status_baru, UNIX_TIMESTAMP(waktu) FROM riwayat_status WHERE peristiwa IN ('status', 'dibuat') ORDER BY waktu, id_riwayat") as $r) {
		if (!isset($awal[$r[0]][$r[1]])) { $awal[$r[0]][$r[1]] = (int) $r[2]; }
	}
	$tahap = array(
		'pemeriksaan' => array('Pemeriksaan berkas', array('TERKIRIM'), array('BERKAS DITERIMA')),
		'penilaian' => array('Penilaian', array('BERKAS DITERIMA'), array('PENILAIAN SELESAI')),
		'keputusan' => array('Keputusan akhir', array('PENILAIAN SELESAI'), array('DITERIMA', 'DITOLAK')),
		'total' => array('Total, kirim sampai keputusan', array('TERKIRIM'), array('DITERIMA', 'DITOLAK')),
	);
	$d['tahap'] = array();
	foreach ($tahap as $kunci => $def) {
		$sel = array();
		foreach ($awal as $s) {
			$a = null; $b = null;
			foreach ($def[1] as $x) { if (isset($s[$x])) { $a = $a === null ? $s[$x] : min($a, $s[$x]); } }
			foreach ($def[2] as $x) { if (isset($s[$x])) { $b = $b === null ? $s[$x] : min($b, $s[$x]); } }
			if ($a !== null && $b !== null && $b >= $a) { $sel[] = $b - $a; }
		}
		$d['tahap'][$kunci] = array($def[0], $sel ? array_sum($sel) / count($sel) : null, count($sel));
	}

	$d['jenjang'] = (array) $db->GetAll("SELECT jabatan_tujuan, COUNT(*), SUM(status_pengajuan = 'DITERIMA'), SUM(status_pengajuan = 'DITOLAK') FROM pengajuan GROUP BY jabatan_tujuan ORDER BY COUNT(*) DESC, jabatan_tujuan");

	$d['penilai'] = (array) $db->GetAll("SELECT n.nip_penilai, n.nama_penilai,
		(SELECT COUNT(*) FROM pengajuan p WHERE p.status_pengajuan = 'BERKAS DITERIMA' AND (p.nip_penilai_1 = n.nip_penilai OR p.nip_penilai_2 = n.nip_penilai)) AS menunggu,
		(SELECT COUNT(*) FROM pengajuan p WHERE p.nip_penilai_1 = CONCAT(n.nip_penilai, 'V') OR p.nip_penilai_2 = CONCAT(n.nip_penilai, 'V')) AS selesai
		FROM penilai n ORDER BY menunggu DESC, selesai DESC, n.nama_penilai");

	$d['antrean'] = (array) $db->GetAll("SELECT p.id_pengajuan, d.nama_dosen, p.status_pengajuan,
		TIMESTAMPDIFF(SECOND, COALESCE((SELECT MAX(r.waktu) FROM riwayat_status r WHERE r.id_pengajuan = p.id_pengajuan), NOW()), NOW()) AS tunggu
		FROM pengajuan p LEFT JOIN dosen d ON d.nip_dosen = p.nip_dosen
		WHERE p.status_pengajuan IN ('TERKIRIM', 'TELAH REVISI') ORDER BY tunggu DESC, p.id_pengajuan LIMIT 5");
	return $d;
}

function nx_statistik_bulan_label($ym)
{
	$n = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des');
	return $n[substr($ym, 5, 2)] . ' ' . substr($ym, 2, 2);
}

function nx_statistik_grafik($bulan)
{
	$max = 1; foreach ($bulan as $b) { $max = max($max, $b[1]); }
	$w = 600; $h = 190; $kiri = 8; $dasar = 150; $lebar = ($w - $kiri * 2) / count($bulan); $tinggi = 118;
	$ringkas = array(); foreach ($bulan as $b) { $ringkas[] = nx_statistik_bulan_label($b[0]) . ': ' . $b[1]; }
	$s = '<svg class="nx-chart" viewBox="0 0 ' . $w . ' ' . $h . '" role="img" aria-label="Pengajuan per bulan. ' . nx_e(implode(', ', $ringkas)) . '" preserveAspectRatio="xMidYMid meet">';
	$s .= '<line x1="' . $kiri . '" y1="' . $dasar . '" x2="' . ($w - $kiri) . '" y2="' . $dasar . '" class="nx-chart-axis"/>';
	foreach ($bulan as $i => $b) {
		$x = $kiri + $i * $lebar; $bh = $b[1] > 0 ? max(4, $b[1] / $max * $tinggi) : 0; $bw = $lebar * 0.56;
		if ($b[1] > 0) {
			$s .= '<rect class="nx-chart-bar" style="--i:' . $i . '" x="' . round($x + ($lebar - $bw) / 2, 1) . '" y="' . round($dasar - $bh, 1) . '" width="' . round($bw, 1) . '" height="' . round($bh, 1) . '" rx="4"><title>' . nx_e(nx_statistik_bulan_label($b[0]) . ': ' . $b[1] . ' pengajuan') . '</title></rect>';
			$s .= '<text class="nx-chart-val" x="' . round($x + $lebar / 2, 1) . '" y="' . round($dasar - $bh - 6, 1) . '" text-anchor="middle">' . (int) $b[1] . '</text>';
		}
		$s .= '<text class="nx-chart-lab" x="' . round($x + $lebar / 2, 1) . '" y="' . ($dasar + 20) . '" text-anchor="middle">' . nx_e(nx_statistik_bulan_label($b[0])) . '</text>';
	}
	return $s . '</svg>';
}

function nx_statistik_render($db, $nip_pegawai)
{
	$d = nx_statistik_data($db);
	$q = 'nip_pegawai=' . rawurlencode($nip_pegawai);
	$st = $d['status'];
	?>
<section class="nx-welcome" aria-labelledby="nx-hello">
	<div>
		<span class="nx-eyebrow"><i class="fas fa-chart-bar" aria-hidden="true"></i><span>Ringkasan proses</span></span>
		<h2 id="nx-hello">Statistik pengajuan</h2>
		<p>Pantau volume, kecepatan, dan beban kerja proses kenaikan jabatan. Lama tiap tahap dihitung dari riwayat status.</p>
	</div>
	<div class="nx-actions">
		<a class="btn btn-light-solid" href="pegawai_ekspor_pengajuan.php?<?php echo $q; ?>"><i class="fas fa-file-csv" aria-hidden="true"></i>Ekspor CSV</a>
	</div>
</section>

<div class="nx-stats">
	<?php
	nx_stat_card('pegawai_data_pengajuan.php?' . $q, 'fa-layer-group', '', 'Total pengajuan', $d['total'], 'Semua status');
	nx_stat_card('pegawai_data_pengajuan.php?' . $q, 'fa-search', 'kirim', 'Perlu diperiksa', nx_sum($st, array('TERKIRIM', 'TELAH REVISI')), 'Menunggu pegawai');
	nx_stat_card('pegawai_data_pengajuan_penilaian.php?' . $q, 'fa-hourglass-half', 'revisi', 'Dalam penilaian', nx_sum($st, array('BERKAS DITERIMA', 'PENILAIAN SELESAI')), 'Berkas diterima sampai keputusan');
	nx_stat_card('pegawai_data_pengajuan_selesai.php?' . $q, 'fa-check-circle', 'lulus', 'Tingkat diterima', $d['rasio_terima'] === null ? '-' : round($d['rasio_terima'] * 100) . '%', $d['diterima'] . ' diterima, ' . $d['ditolak'] . ' ditolak');
	?>
</div>

<div class="nx-grid">
	<section class="nx-panel" aria-labelledby="nx-bln-t">
		<div class="nx-panel-head"><h3 class="nx-panel-title" id="nx-bln-t">Pengajuan per bulan</h3><span class="nx-panel-sub">12 bulan terakhir</span></div>
		<?php echo nx_statistik_grafik($d['bulanan']); ?>
	</section>
	<section class="nx-panel" aria-labelledby="nx-thp-t">
		<div class="nx-panel-head"><h3 class="nx-panel-title" id="nx-thp-t">Lama proses rata-rata</h3></div>
		<ul class="nx-lst">
			<?php foreach ($d['tahap'] as $k => $t) : ?>
			<li<?php echo $k === 'total' ? ' class="is-total"' : ''; ?>><span><?php echo nx_e($t[0]); ?><small><?php echo $t[2] > 0 ? 'dari ' . (int) $t[2] . ' pengajuan' : 'belum ada data'; ?></small></span><b><?php echo nx_e(nx_durasi($t[1])); ?></b></li>
			<?php endforeach; ?>
		</ul>
		<p class="nx-note">Hanya pengajuan yang tercatat sejak fitur riwayat dipasang.</p>
	</section>
</div>

<div class="nx-grid nx-grid--even">
	<section class="nx-panel" aria-labelledby="nx-jnj-t">
		<div class="nx-panel-head"><h3 class="nx-panel-title" id="nx-jnj-t">Per jabatan tujuan</h3></div>
		<?php if (!$d['jenjang']) : ?><p class="nx-note">Belum ada pengajuan.</p><?php else :
			$mx = 1; foreach ($d['jenjang'] as $j) { $mx = max($mx, (int) $j[1]); } ?>
		<ul class="nx-lst nx-lst--bar">
			<?php foreach ($d['jenjang'] as $j) : ?>
			<li><span><?php echo nx_e($j[0]); ?><small><?php echo (int) $j[2]; ?> diterima, <?php echo (int) $j[3]; ?> ditolak</small></span><b><?php echo (int) $j[1]; ?></b><span class="nx-bar-track" role="presentation"><span class="nx-bar-fill" style="--p:<?php echo round($j[1] / $mx, 3); ?>"></span></span></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</section>
	<section class="nx-panel" aria-labelledby="nx-pen-t">
		<div class="nx-panel-head"><h3 class="nx-panel-title" id="nx-pen-t">Beban penilai</h3><span class="nx-panel-sub">menunggu dinilai / selesai</span></div>
		<?php if (!$d['penilai']) : ?><p class="nx-note">Belum ada penilai terdaftar.</p><?php else : ?>
		<ul class="nx-lst">
			<?php foreach ($d['penilai'] as $p) : ?>
			<li><span><?php echo nx_e($p[1]); ?><small>NIP <?php echo nx_e($p[0]); ?></small></span><b><?php echo (int) $p[2]; ?> / <?php echo (int) $p[3]; ?></b></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</section>
</div>

<section class="nx-panel" style="margin-top:16px" aria-labelledby="nx-ant-t">
	<div class="nx-panel-head"><h3 class="nx-panel-title" id="nx-ant-t">Antrean pemeriksaan terlama</h3><span class="nx-panel-sub">menunggu pegawai</span></div>
	<?php if (!$d['antrean']) : ?><p class="nx-note">Tidak ada pengajuan yang menunggu pemeriksaan.</p><?php else : ?>
	<ul class="nx-lst">
		<?php foreach ($d['antrean'] as $a) : ?>
		<li><span>#<?php echo (int) $a[0]; ?> · <?php echo nx_e($a[1]); ?><small><?php echo nx_e($a[2]); ?> · menunggu <?php echo nx_e(nx_durasi((int) $a[3])); ?></small></span><a class="nx-badge nx-badge--stempel" href="pegawai_pengajuan_periksa.php?<?php echo $q; ?>&amp;id_pengajuan=<?php echo (int) $a[0]; ?>">Periksa</a></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</section>
<?php
}
