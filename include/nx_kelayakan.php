<?php
/*
 * Cek kelayakan pengajuan (Fase 1).
 * Satu sumber aturan untuk: panel di halaman dosen/pegawai dan penguncian tombol kirim.
 * Hanya membaca data. Kompatibel PHP 7.x.
 *
 * Aturan yang dicek (semua dari data yang sudah ada di SIPAK):
 *  - Total angka kredit >= syarat jabatan tujuan                (WAJIB, memblokir pengiriman)
 *  - Minimal ada satu berkas                                    (WAJIB, memblokir pengiriman)
 *  - Minimal angka kredit per unsur (pendidikan, penelitian,
 *    pengabdian, penunjang) menurut tabel syarat_kum            (PERINGATAN, kepegawaian memutuskan)
 *  - Berkas berstatus ditolak yang belum direvisi               (PERINGATAN)
 *  - Kenaikan jenjang (Kepmendiktisaintek 39/2026): masa di jabatan, masa dosen tetap (Profesor), pendidikan,
 *    syarat khusus publikasi (WAJIB, dapat diatur di tabel aturan_jabatan); SKP, uji kompetensi, sertifikat
 *    pendidik (pernyataan dosen, PERINGATAN)
 */
if (!function_exists('nx_e')) {
	function nx_e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
}

include_once __DIR__ . '/nx_aturan.php';

/* Rentang kode rubrik per unsur (sama dengan halaman persetujuan). */
function nx_unsur_rentang()
{
	return array(
		'pendidikan' => array('Pelaksanaan pendidikan', 1, 48),
		'penelitian' => array('Penelitian', 49, 103, 152, 200),   // rubrik lama + rubrik juknis 2026
		'pengabdian' => array('Pengabdian kepada masyarakat', 104, 119),
		'penunjang'  => array('Penunjang', 120, 151),
	);
}

function nx_lama_sejak($tanggal)
{
	if (!$tanggal || $tanggal === '0000-00-00') return null;
	try {
		$d = new DateTime($tanggal);
		$diff = $d->diff(new DateTime('today'));
		if ($diff->invert) return null;
		return array($diff->y, $diff->m);
	} catch (Exception $e) {
		return null;
	}
}

/* Mengembalikan array: 'cek' (daftar baris), 'bisa_kirim', 'total', 'syarat', 'terpenuhi', 'jumlah', 'unsur_kurang', 'jenjang' */
function nx_kelayakan($koneksi, $id_pengajuan)
{
	include_once __DIR__ . '/nx_aturan.php';
	$p = $koneksi->GetRow(
		"SELECT p.tanggal_sk, k.total_pak, k.pelaksanaan_pendidikan, k.penelitian, k.pengabdian, k.penunjang, k.nama_jabatan, p.jabatan_asal, p.jabatan_tujuan, p.nip_dosen
		 FROM pengajuan p INNER JOIN syarat_kum k ON k.id_kum = p.id_kum WHERE p.id_pengajuan = ?",
		array($id_pengajuan)
	);
	if (!$p) return null;
	$dos = $koneksi->GetRow(
		"SELECT pendidikan_terakhir, status_kepegawaian, tmt_dosen_tetap, ak_kumulatif, ak_penelitian_kumulatif, sertifikat_pendidik, skp_baik, uji_kompetensi FROM dosen WHERE nip_dosen = ?",
		array($p[9])
	);
	if (!$dos) $dos = array(null, null, null, 0, 0, 0, 0, 0);
	$ak_awal = (float) $dos[3];
	$ak_pen_awal = (float) $dos[4];

	$rentang = nx_unsur_rentang();
	$cols = array();
	foreach ($rentang as $key => $r) {
		$cond = "kode_rubrik BETWEEN " . (int) $r[1] . " AND " . (int) $r[2];
		if (isset($r[3])) { $cond = "(" . $cond . " OR kode_rubrik BETWEEN " . (int) $r[3] . " AND " . (int) $r[4] . ")"; }
		$cols[] = "COALESCE(SUM(CASE WHEN " . $cond . " THEN poin_akhir END),0) AS u_" . $key;
	}
	$s = $koneksi->GetRow(
		"SELECT COUNT(*) AS n, COALESCE(SUM(poin_akhir),0) AS total, COALESCE(SUM(status_berkas='TOLAK'),0) AS tolak, " . implode(',', $cols) . "
		 FROM berkas_pak WHERE id_pengajuan = ?",
		array($id_pengajuan)
	);
	$jumlah_berkas = (int) $s[0];
	$ak_berkas = (float) $s[1];
	$total   = $ak_awal + $ak_berkas;
	$tolak   = (int) $s[2];
	$syarat  = (float) $p[1];
	$min_unsur = array('pendidikan' => (float) $p[2], 'penelitian' => (float) $p[3], 'pengabdian' => (float) $p[4], 'penunjang' => (float) $p[5]);

	$cek = array();
	$ok_total = $total >= $syarat;
	$nilai_total = nx_num($total) . ' / ' . nx_num($syarat) . ' AK';
	$cek[] = array(
		'kunci' => 'total', 'level' => 'wajib', 'ok' => $ok_total,
		'label' => 'Total angka kredit ke ' . $p[6],
		'nilai' => $nilai_total,
		'rasio' => $syarat > 0 ? min(1, $total / $syarat) : 1,
		'saran' => $ok_total ? ($ak_awal > 0 ? 'AK kumulatif SK terakhir ' . nx_num($ak_awal) . ' + berkas pengajuan ' . nx_num($ak_berkas) . '.' : '')
			: 'Kurang ' . nx_num($syarat - $total) . ' AK.' . ($ak_awal > 0 ? ' (AK kumulatif SK terakhir ' . nx_num($ak_awal) . ' + berkas ' . nx_num($ak_berkas) . ')' : ' Tambahkan berkas kegiatan yang relevan.'),
	);
	$ok_berkas = $jumlah_berkas > 0;
	$cek[] = array(
		'kunci' => 'berkas', 'level' => 'wajib', 'ok' => $ok_berkas,
		'label' => 'Berkas pendukung',
		'nilai' => $jumlah_berkas . ' berkas',
		'rasio' => $ok_berkas ? 1 : 0,
		'saran' => $ok_berkas ? '' : 'Belum ada berkas. Tambahkan minimal satu berkas kegiatan.',
	);
	$unsur_kurang = array();
	$i = 3;
	foreach ($rentang as $key => $r) {
		$val = (float) $s[$i++];
		if ($key === 'penelitian') $val += $ak_pen_awal;
		$min = $min_unsur[$key];
		$ok = $val >= $min;
		if (!$ok) $unsur_kurang[] = $r[0];
		$cek[] = array(
			'kunci' => $key, 'level' => 'peringatan', 'ok' => $ok,
			'label' => 'Unsur ' . strtolower($r[0]),
			'nilai' => nx_num($val) . ' / ' . nx_num($min) . ' AK',
			'rasio' => $min > 0 ? min(1, $val / $min) : 1,
			'saran' => $ok ? '' : 'Kurang ' . nx_num($min - $val) . ' AK dari batas minimal unsur ini.',
		);
	}
	if ($tolak > 0) {
		$cek[] = array(
			'kunci' => 'tolak', 'level' => 'peringatan', 'ok' => false,
			'label' => 'Berkas ditolak belum diperbaiki',
			'nilai' => $tolak . ' berkas',
			'rasio' => 0,
			'saran' => 'Perbaiki atau ganti berkas yang ditolak kepegawaian.',
		);
	}

	/* --- Syarat kenaikan jenjang (hanya bila jenjang berubah) --- */
	$j_asal = nx_jenjang($p[7]); $j_tuju = nx_jenjang($p[8]);
	$ganti_jenjang = ($j_tuju !== '' && $j_asal !== '' && $j_asal !== $j_tuju);
	$ar = $ganti_jenjang ? nx_aturan_jenjang($koneksi, $j_tuju) : null;
	$lama = nx_lama_sejak($p[0]);
	$pub_wajib_ok = true;
	if ($ar) {
		if ($ar['min_tahun_jabatan'] > 0) {
			$bulan = $lama ? $lama[0] * 12 + $lama[1] : 0;
			$perlu = $ar['min_tahun_jabatan'] * 12;
			$ok = $bulan >= $perlu;
			$cek[] = array(
				'kunci' => 'masa', 'level' => 'wajib', 'ok' => $ok,
				'label' => 'Masa di jabatan ' . $j_asal,
				'nilai' => ($lama ? ($lama[0] > 0 ? $lama[0] . ' th ' : '') . $lama[1] . ' bln' : 'tanggal SK tidak valid') . ' / min. ' . $ar['min_tahun_jabatan'] . ' th',
				'rasio' => $perlu > 0 ? min(1, $bulan / $perlu) : 1,
				'saran' => $ok ? '' : 'Dihitung dari tanggal SK jabatan terakhir. Belum mencapai ' . $ar['min_tahun_jabatan'] . ' tahun.',
			);
		}
		if ($ar['min_tahun_dosen_tetap'] > 0) {
			$tmt = $dos[2];
			$lm = nx_lama_sejak($tmt);
			$bulan = $lm ? $lm[0] * 12 + $lm[1] : 0;
			$perlu = $ar['min_tahun_dosen_tetap'] * 12;
			$ok = $lm && $bulan >= $perlu;
			$cek[] = array(
				'kunci' => 'tetap', 'level' => 'wajib', 'ok' => (bool) $ok,
				'label' => 'Masa kerja sebagai dosen tetap',
				'nilai' => $lm ? (($lm[0] > 0 ? $lm[0] . ' th ' : '') . $lm[1] . ' bln') . ' / min. ' . $ar['min_tahun_dosen_tetap'] . ' th' : 'belum diisi',
				'rasio' => $lm ? min(1, $bulan / $perlu) : 0,
				'saran' => $ok ? '' : ($lm ? 'Belum mencapai ' . $ar['min_tahun_dosen_tetap'] . ' tahun sejak diangkat dosen tetap.' : 'Isi tanggal diangkat sebagai dosen tetap di profil.'),
				'profil' => !$lm,
			);
		}
		if ($ar['pendidikan_min']) {
			$pend = (string) $dos[0];
			$ok = $pend !== '' && nx_pendidikan_rank($pend) >= nx_pendidikan_rank($ar['pendidikan_min']);
			$opsi = nx_pendidikan_opsi();
			$cek[] = array(
				'kunci' => 'pendidikan', 'level' => 'wajib', 'ok' => $ok,
				'label' => 'Pendidikan terakhir',
				'nilai' => ($pend !== '' ? (isset($opsi[$pend]) ? $opsi[$pend] : $pend) : 'belum diisi') . ' / min. ' . $ar['pendidikan_min'],
				'rasio' => null,
				'saran' => $ok ? '' : ($pend === '' ? 'Isi pendidikan terakhir di profil.' : 'Jenjang ' . $j_tuju . ' mensyaratkan pendidikan ' . $ar['pendidikan_min'] . '.'),
				'profil' => ($pend === ''),
			);
		}
		if ($ar['jumlah_publikasi'] > 0) {
			$pubs = array();
			foreach ((array) $koneksi->GetAll('SELECT judul, jenis, peringkat, peran FROM publikasi WHERE id_pengajuan = ?', array($id_pengajuan)) as $r) {
				$pubs[] = array('judul' => $r[0], 'jenis' => $r[1], 'peringkat' => $r[2], 'peran' => $r[3]);
			}
			$ev = nx_evaluasi_publikasi($pubs, $j_tuju, $ar['jumlah_publikasi']);
			$ok = $ev['ok'];
			$level = $ar['wajib_publikasi'] ? 'wajib' : 'peringatan';
			if ($level === 'wajib' && !$ok) $pub_wajib_ok = false;
			$cek[] = array(
				'kunci' => 'publikasi', 'level' => $level, 'ok' => $ok,
				'label' => 'Syarat khusus publikasi ke ' . $j_tuju,
				'nilai' => (int) $ev['memenuhi'] . ' / ' . (int) $ev['dibutuhkan'] . ' memenuhi',
				'rasio' => $ev['dibutuhkan'] > 0 ? min(1, $ev['memenuhi'] / $ev['dibutuhkan']) : 1,
				'saran' => $ok ? '' : nx_syarat_publikasi_teks($j_tuju),
			);
		}
		// Pernyataan yang diverifikasi kepegawaian
		$pern = array(
			array('skp', 'Predikat SKP minimal baik, 2 tahun berturut-turut', (int) $dos[6]),
			array('uji', 'Uji kompetensi', (int) $dos[7]),
		);
		if ($j_tuju === 'Profesor') $pern[] = array('serdos', 'Sertifikat pendidik', (int) $dos[5]);
		foreach ($pern as $q) {
			$cek[] = array(
				'kunci' => $q[0], 'level' => 'peringatan', 'ok' => (bool) $q[2],
				'label' => $q[1],
				'nilai' => $q[2] ? 'dinyatakan terpenuhi' : 'belum dinyatakan',
				'rasio' => null,
				'saran' => $q[2] ? '' : 'Centang pernyataan ini di profil bila sudah terpenuhi. Kepegawaian akan memverifikasi.',
				'profil' => !$q[2],
			);
		}
		if ($j_tuju === 'Profesor') {
			$cek[] = array(
				'kunci' => 'tambahan', 'level' => 'info', 'ok' => true,
				'label' => 'Syarat khusus tambahan Profesor',
				'nilai' => 'diperiksa kepegawaian',
				'rasio' => null,
				'saran' => 'Pilih satu: ketua hibah kompetitif, pembimbing atau penguji mahasiswa doktor, mitra bestari 2 jurnal internasional Q3, atau PIC kerja sama dengan profesor luar negeri.',
			);
		}
	}
	if ($lama && !$ar) {
		$teks = ($lama[0] > 0 ? $lama[0] . ' tahun ' : '') . $lama[1] . ' bulan';
		$cek[] = array(
			'kunci' => 'masa_info', 'level' => 'info', 'ok' => true,
			'label' => 'Lama sejak SK jabatan terakhir',
			'nilai' => $teks,
			'rasio' => null,
			'saran' => '',
		);
	}

	$terpenuhi = 0; $jumlah = 0; $bisa = true;
	foreach ($cek as $c) {
		if ($c['level'] === 'info') continue;
		$jumlah++;
		if ($c['ok']) $terpenuhi++;
		elseif ($c['level'] === 'wajib') $bisa = false;
	}
	return array(
		'cek' => $cek, 'bisa_kirim' => $bisa,
		'total' => $total, 'ak_berkas' => $ak_berkas, 'ak_awal' => $ak_awal, 'syarat' => $syarat, 'terpenuhi' => $terpenuhi, 'jumlah' => $jumlah,
		'unsur_kurang' => $unsur_kurang, 'jenjang' => $j_tuju, 'ganti_jenjang' => $ganti_jenjang,
		'ada_catatan' => (count($unsur_kurang) > 0) || (function () use ($cek) { foreach ($cek as $c) { if ($c['level'] === 'peringatan' && !$c['ok']) return true; } return false; })(),
	);
}

/* Panel HTML. $mode: 'dosen' (arahan untuk dosen) atau 'pegawai' (ringkasan untuk pemeriksa). */
function nx_kelayakan_panel($data, $mode = 'dosen', $nip = '')
{
	if (!$data) return;
	if (!$data['bisa_kirim']) {
		$ringkas = $mode === 'dosen' ? 'Belum bisa dikirim' : 'Belum memenuhi syarat wajib';
		$nada = 'tolak';
	} elseif ($data['ada_catatan']) {
		$ringkas = $mode === 'dosen' ? 'Bisa dikirim, ada catatan' : 'Memenuhi syarat wajib, ada catatan';
		$nada = 'revisi';
	} else {
		$ringkas = 'Semua syarat terpenuhi';
		$nada = 'lulus';
	}
	echo '<section class="nx-panel nx-kelayakan" aria-labelledby="nx-kl-t">';
	echo '<div class="nx-panel-head"><div><h2 class="nx-panel-title" id="nx-kl-t">Kelayakan Pengajuan</h2>';
	echo '<div class="nx-panel-sub">' . (int) $data['terpenuhi'] . ' dari ' . (int) $data['jumlah'] . ' syarat terpenuhi</div></div>';
	echo '<span class="nx-badge nx-badge--' . ($nada === 'tolak' ? 'tolak' : ($nada === 'revisi' ? 'revisi' : 'lulus')) . '">' . nx_e($ringkas) . '</span></div>';
	echo '<ul class="nx-kl-list">';
	$n = 0;
	foreach ($data['cek'] as $c) {
		$n++;
		if ($c['level'] === 'info') { $state = 'info'; $ico = 'fa-info'; }
		elseif ($c['ok']) { $state = 'ok'; $ico = 'fa-check'; }
		elseif ($c['level'] === 'wajib') { $state = 'gagal'; $ico = 'fa-times'; }
		else { $state = 'warn'; $ico = 'fa-exclamation'; }
		echo '<li class="nx-kl-item is-' . $state . '" style="--i:' . $n . '">';
		echo '<span class="nx-kl-ico" aria-hidden="true"><i class="fas ' . $ico . '"></i></span>';
		echo '<div class="nx-kl-body"><div class="nx-kl-row"><span class="nx-kl-label">' . nx_e($c['label']);
		if ($c['level'] === 'wajib') echo ' <span class="nx-kl-tag">Wajib</span>';
		echo '</span><span class="nx-kl-val">' . nx_e($c['nilai']) . '</span></div>';
		if ($c['rasio'] !== null) {
			$cls = $c['ok'] ? 'is-lulus' : ($c['level'] === 'wajib' ? 'is-tolak' : 'is-revisi');
			echo '<div class="nx-bar-track" role="presentation"><span class="nx-bar-fill ' . $cls . '" style="--p:' . round($c['rasio'], 3) . ';--i:' . $n . '"></span></div>';
		}
		if ($c['saran'] !== '') {
			echo '<div class="nx-kl-hint">' . nx_e($c['saran']);
			if (!empty($c['profil']) && $mode === 'dosen' && $nip !== '') echo ' <a href="dosen_profile_ubah.php?nip_dosen=' . nx_e(rawurlencode($nip)) . '">Lengkapi profil</a>';
			echo '</div>';
		}
		echo '</div></li>';
	}
	echo '</ul>';
	if ($mode === 'dosen') {
		echo '<p class="nx-kl-foot">Syarat bertanda Wajib harus terpenuhi agar pengajuan bisa dikirim. Catatan lain diperiksa kepegawaian dan penilai.</p>';
	}
	echo '</section>';
}
