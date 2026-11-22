<?php
/*
 * Aturan kenaikan jabatan akademik dosen (Kepmendiktisaintek 39/M/KEP/2026).
 * Angka yang dapat berubah (masa minimal, jumlah publikasi, pendidikan, wajib/tidaknya publikasi)
 * disimpan di tabel aturan_jabatan dan dapat diubah kepegawaian. Matriks jenis publikasi di bawah
 * ini mengikuti Tabel 4-6 juknis (hal. 30-32). Kompatibel PHP 7.x.
 */
if (!function_exists('nx_e')) {
	function nx_e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('nx_num')) {
	function nx_num($v)
	{
		$s = number_format((float) $v, 2, ',', '.');
		return rtrim(rtrim($s, '0'), ',');
	}
}

define('NX_ATURAN_SUMBER', 'Kepmendiktisaintek 39/M/KEP/2026');

/* Jenjang dari nama jabatan ("Lektor Kepala IV/a" => "Lektor Kepala"). */
function nx_jenjang($nama)
{
	$n = strtolower(trim((string) $nama));
	if (strpos($n, 'guru besar') === 0 || strpos($n, 'profesor') === 0) return 'Profesor';
	if (strpos($n, 'lektor kepala') === 0) return 'Lektor Kepala';
	if (strpos($n, 'lektor') === 0) return 'Lektor';
	if (strpos($n, 'asisten ahli') === 0) return 'Asisten Ahli';
	return '';
}

function nx_aturan_jenjang($koneksi, $jenjang)
{
	$r = $koneksi->GetRow('SELECT jenjang, min_tahun_jabatan, min_tahun_dosen_tetap, pendidikan_min, jumlah_publikasi, wajib_publikasi, sumber, terverifikasi FROM aturan_jabatan WHERE jenjang = ?', array($jenjang));
	if (!$r) return null;
	return array(
		'jenjang' => $r[0], 'min_tahun_jabatan' => (int) $r[1], 'min_tahun_dosen_tetap' => (int) $r[2],
		'pendidikan_min' => $r[3], 'jumlah_publikasi' => (int) $r[4], 'wajib_publikasi' => (int) $r[5],
		'sumber' => $r[6], 'terverifikasi' => (int) $r[7],
	);
}

function nx_pendidikan_opsi() { return array('S2' => 'S2 / Magister', 'S3' => 'S3 / Doktor', 'Sp' => 'Subspesialis', 'S1' => 'S1 / Profesi'); }
function nx_status_opsi() { return array('PNS' => 'PNS', 'PTNBH' => 'Dosen tetap PTN BH', 'PTS' => 'Dosen tetap PTS / PTS Keagamaan'); }
function nx_pendidikan_rank($p) { $m = array('S1' => 1, 'S2' => 2, 'S3' => 3, 'Sp' => 3); return isset($m[$p]) ? $m[$p] : 0; }

function nx_jenis_publikasi()
{
	return array(
		'jurnal_internasional' => 'Jurnal internasional bereputasi',
		'jurnal_nasional'      => 'Jurnal nasional terakreditasi',
		'karya_seni'           => 'Karya seni',
	);
}

/* Pilihan peringkat tiap jenis. nilai => label. */
function nx_peringkat_per_jenis()
{
	return array(
		'jurnal_internasional' => array('Q1' => 'Scopus Q1', 'Q2' => 'Scopus Q2', 'Q3' => 'Scopus Q3', 'Q4' => 'Scopus Q4', 'INTL-LAIN' => 'Terindeks (SJR 0,1 atau kurang)'),
		'jurnal_nasional'      => array('Sinta 1' => 'Sinta 1', 'Sinta 2' => 'Sinta 2', 'Sinta 3' => 'Sinta 3', 'Sinta 4' => 'Sinta 4', 'Sinta 5' => 'Sinta 5', 'Sinta 6' => 'Sinta 6'),
		'karya_seni'           => array('Internasional' => 'Diakui internasional', 'Nasional' => 'Diakui nasional'),
	);
}

function nx_peran_opsi()
{
	return array(
		'pertama_korespondensi' => 'Penulis pertama dan korespondensi',
		'pertama'               => 'Penulis pertama',
		'korespondensi'         => 'Penulis korespondensi',
		'lainnya'               => 'Peran lain',
	);
}

/* Nilai AK Prestasi per peringkat (Tabel 2 juknis, hal. 14-19). Hanya acuan; AK resmi ditetapkan penilai. */
function nx_ak_acuan_publikasi($jenis, $peringkat)
{
	$m = array(
		'Q1' => 40, 'Q2' => 38, 'Q3' => 35, 'Q4' => 33, 'INTL-LAIN' => 30,
		'Sinta 1' => 25, 'Sinta 2' => 25, 'Sinta 3' => 20, 'Sinta 4' => 20, 'Sinta 5' => 15, 'Sinta 6' => 15,
	);
	return $jenis === 'karya_seni' ? null : (isset($m[$peringkat]) ? $m[$peringkat] : null);
}

function nx_q_rank($p) { $m = array('Q1' => 1, 'Q2' => 2, 'Q3' => 3, 'Q4' => 4); return isset($m[$p]) ? $m[$p] : 99; }
function nx_sinta_rank($p) { return preg_match('/^Sinta ([1-6])$/', (string) $p, $mm) ? (int) $mm[1] : 99; }
function nx_peran_pertama($r) { return $r === 'pertama' || $r === 'pertama_korespondensi'; }
function nx_peran_pk($r) { return $r === 'pertama_korespondensi'; }

/* Apakah satu publikasi memenuhi pola untuk jenjang tujuan (Tabel 4-6). $slot: 'A' atau 'B' (hanya Profesor). */
function nx_pub_memenuhi($pub, $jenjang, $slot = 'A')
{
	$jenis = $pub['jenis']; $per = $pub['peringkat']; $peran = $pub['peran'];
	if ($jenjang === 'Lektor') {
		if ($jenis === 'jurnal_nasional') return nx_sinta_rank($per) <= 4 && nx_peran_pertama($peran);
		if ($jenis === 'jurnal_internasional') return nx_q_rank($per) <= 4 && nx_peran_pertama($peran);
		if ($jenis === 'karya_seni') return in_array($per, array('Internasional', 'Nasional'), true);
	} elseif ($jenjang === 'Lektor Kepala') {
		if ($jenis === 'jurnal_internasional') return nx_q_rank($per) <= 3 && nx_peran_pk($peran);
		if ($jenis === 'jurnal_nasional') return nx_sinta_rank($per) <= 2 && nx_peran_pk($peran);
		if ($jenis === 'karya_seni') return $per === 'Internasional';
	} elseif ($jenjang === 'Profesor') {
		if ($slot === 'A') {
			if ($jenis === 'jurnal_internasional') return nx_q_rank($per) <= 2 && nx_peran_pk($peran);
			if ($jenis === 'karya_seni') return $per === 'Internasional';
		} else {
			if ($jenis === 'jurnal_internasional') return nx_q_rank($per) <= 3 && ($peran === 'pertama' || $peran === 'korespondensi' || $peran === 'pertama_korespondensi');
			if ($jenis === 'jurnal_nasional') return nx_sinta_rank($per) <= 1 && ($peran === 'pertama' || $peran === 'korespondensi' || $peran === 'pertama_korespondensi');
			if ($jenis === 'karya_seni') return $per === 'Internasional';
		}
	}
	return false;
}

/* Kalimat syarat publikasi untuk ditampilkan. */
function nx_syarat_publikasi_teks($jenjang)
{
	if ($jenjang === 'Lektor') return 'Minimal 1 artikel sebagai penulis pertama di jurnal nasional terakreditasi Sinta 4 atau lebih tinggi, atau jurnal internasional bereputasi Q4 atau lebih tinggi; atau 1 karya seni yang diakui.';
	if ($jenjang === 'Lektor Kepala') return 'Minimal 1 artikel sebagai penulis pertama sekaligus korespondensi di jurnal internasional bereputasi Q3 atau lebih tinggi, atau jurnal nasional terakreditasi Sinta 2 atau lebih tinggi; atau 1 karya seni internasional.';
	if ($jenjang === 'Profesor') return 'Dua publikasi: (1) jurnal internasional bereputasi Q2 atau lebih tinggi sebagai penulis pertama sekaligus korespondensi, dan (2) jurnal internasional Q3 atau lebih tinggi, atau jurnal nasional Sinta 1, sebagai penulis pertama atau korespondensi.';
	return '';
}

/* Hitung pemenuhan syarat publikasi. Mengembalikan array(ok, jumlah_memenuhi, dibutuhkan, rincian). */
function nx_evaluasi_publikasi($pubs, $jenjang, $dibutuhkan)
{
	if ($jenjang === 'Profesor') {
		$A = array(); $Bset = array();
		foreach ($pubs as $i => $p) {
			if (nx_pub_memenuhi($p, $jenjang, 'A')) $A[] = $i;
			if (nx_pub_memenuhi($p, $jenjang, 'B')) $Bset[] = $i;
		}
		$ok = false;
		foreach ($A as $a) foreach ($Bset as $b) if ($a !== $b) { $ok = true; break 2; }
		$hit = count(array_unique(array_merge($A, $Bset)));
		return array('ok' => $ok && $dibutuhkan <= 2, 'memenuhi' => $ok ? 2 : min(1, $hit), 'dibutuhkan' => 2);
	}
	$n = 0;
	foreach ($pubs as $p) if (nx_pub_memenuhi($p, $jenjang)) $n++;
	return array('ok' => $n >= $dibutuhkan, 'memenuhi' => $n, 'dibutuhkan' => $dibutuhkan);
}
