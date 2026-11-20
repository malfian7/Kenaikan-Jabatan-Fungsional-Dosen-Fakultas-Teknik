<?php
/*
 * Fungsi bantu untuk halaman beranda bertema Nexus.
 * Hanya membaca data (SELECT) — tidak mengubah apa pun di database.
 * Kompatibel PHP 7.x.
 */

if (!function_exists('nx_e')) {
	function nx_e($s)
	{
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	}
}

/* Urutan status mengikuti alur pengajuan. [kunci DB] => [label, nada warna] */
function nx_status_flow()
{
	return array(
		'BELUM DIKIRIM'     => array('Belum dikirim',     'netral'),
		'TERKIRIM'          => array('Terkirim',          'kirim'),
		'BERKAS DITOLAK'    => array('Berkas ditolak',    'tolak'),
		'TELAH REVISI'      => array('Telah revisi',      'revisi'),
		'BERKAS DITERIMA'   => array('Berkas diterima',   ''),
		'PENILAIAN SELESAI' => array('Penilaian selesai', ''),
		'DITERIMA'          => array('Diterima',          'lulus'),
		'DITOLAK'           => array('Ditolak',           'tolak'),
	);
}

/* Hitung pengajuan per status. $where harus sudah aman (memakai qstr). */
function nx_status_counts($koneksi, $where = '1=1')
{
	$counts = array();
	foreach (nx_status_flow() as $key => $meta) {
		$counts[$key] = 0;
	}
	$rs = $koneksi->Execute("SELECT status_pengajuan, COUNT(*) FROM pengajuan WHERE $where GROUP BY status_pengajuan");
	if ($rs && !$rs->EOF) {
		while (!$rs->EOF) {
			$k = strtoupper(trim($rs->fields[0]));
			if (isset($counts[$k])) {
				$counts[$k] = (int) $rs->fields[1];
			}
			$rs->MoveNext();
		}
	}
	return $counts;
}

function nx_sum($counts, $keys)
{
	$t = 0;
	foreach ($keys as $k) {
		$t += isset($counts[$k]) ? (int) $counts[$k] : 0;
	}
	return $t;
}

/* Kartu statistik. $href kosong => kartu non-tautan. */
function nx_stat_card($href, $icon, $tone, $label, $value, $note)
{
	$tag   = $href ? 'a' : 'div';
	$attrs = $href ? ' href="' . nx_e($href) . '"' : '';
	echo '<' . $tag . ' class="nx-stat"' . $attrs . '>';
	echo '<span class="nx-stat-top"><span class="nx-stat-ico ' . ($tone ? 'is-' . $tone : '') . '"><i class="fas ' . $icon . '" aria-hidden="true"></i></span>' . nx_e($label);
	if ($href) {
		echo '<i class="fas fa-arrow-right nx-stat-arrow" aria-hidden="true"></i>';
	}
	echo '</span>';
	echo '<span class="nx-stat-value" data-count="' . (int) $value . '">' . (int) $value . '</span>';
	echo '<span class="nx-stat-note">' . nx_e($note) . '</span>';
	echo '</' . $tag . '>';
}

/* Batang distribusi status. */
function nx_status_bars($counts)
{
	$max = max(1, max($counts));
	$total = array_sum($counts);
	if ($total === 0) {
		echo '<div class="nx-empty">Belum ada data pengajuan.</div>';
		return;
	}
	echo '<div class="nx-bars" role="list">';
	$i = 0;
	foreach (nx_status_flow() as $key => $meta) {
		$n = $counts[$key];
		$p = round($n / $max, 4);
		echo '<div class="nx-bar-row" role="listitem" style="--i:' . $i . '">';
		echo '<span class="nx-bar-label">' . nx_e($meta[0]) . '</span>';
		echo '<span class="nx-bar-val">' . $n . '</span>';
		echo '<span class="nx-bar-track" aria-hidden="true"><span class="nx-bar-fill' . ($meta[1] ? ' is-' . $meta[1] : '') . '" style="--p:' . $p . '"></span></span>';
		echo '</div>';
		$i++;
	}
	echo '</div>';
}

/* Tahap jabatan fungsional dosen: 0 Asisten Ahli, 1 Lektor, 2 Lektor Kepala, 3 Guru Besar; -1 jika tidak dikenali */
function nx_stage_index($label)
{
	$l = strtolower((string) $label);
	if (strpos($l, 'guru besar') !== false) { return 3; }
	if (strpos($l, 'lektor kepala') !== false) { return 2; }
	if (strpos($l, 'lektor') !== false) { return 1; }
	if (strpos($l, 'asisten ahli') !== false) { return 0; }
	return -1;
}
