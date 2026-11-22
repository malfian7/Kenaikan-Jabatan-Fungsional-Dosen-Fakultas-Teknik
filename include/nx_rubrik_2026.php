<?php
/*
 * Rubrik AK Prestasi penelitian menurut Kepmendiktisaintek 39/M/KEP/2026 (Tabel 2 hal. 14-19 dan Tabel 3 hal. 20-21).
 * Nilai diekstrak dari teks juknis; ringkasan kegiatan ditulis ulang. Mohon cocokkan dengan PDF resmi sebelum dipakai produksi.
 * Format: array(kode_rubrik, kegiatan, angka_kredit). Kode mulai 152 agar tidak bentrok dengan rubrik lama (1-151).
 */
function nx_rubrik_2026()
{
	return array(
		array(152, 'Buku referensi atau monograf hasil penelitian yang diterbitkan (ber-ISBN), maksimal 1 buku per tahun', 40),
		array(153, 'Bab dalam buku ilmiah internasional (book chapter), maksimal 1 per tahun', 15),
		array(154, 'Bab dalam buku ilmiah nasional (book chapter), maksimal 1 per tahun', 10),
		array(155, 'Artikel di jurnal internasional bereputasi Scopus Q1', 40),
		array(156, 'Artikel di jurnal internasional bereputasi Scopus Q2', 38),
		array(157, 'Artikel di jurnal internasional bereputasi Scopus Q3', 35),
		array(158, 'Artikel di jurnal internasional bereputasi Scopus Q4', 33),
		array(159, 'Artikel di jurnal internasional terindeks dengan SJR 0,1 atau kurang (atau IF 0,05 atau kurang)', 30),
		array(160, 'Artikel di jurnal nasional terakreditasi peringkat Sinta 1 atau 2', 25),
		array(161, 'Artikel di jurnal nasional terakreditasi peringkat Sinta 3 atau 4', 20),
		array(162, 'Artikel di jurnal nasional terakreditasi peringkat Sinta 5 atau 6', 15),
		array(163, 'Artikel di jurnal nasional ber-ISSN yang belum terakreditasi (maksimal 10% AK penelitian untuk Lektor Kepala dan Profesor)', 10),
		array(164, 'Makalah dalam prosiding internasional terindeks (Scopus/SJR) yang dipresentasikan dan dipublikasikan', 20),
		array(165, 'Makalah dalam prosiding internasional yang tidak terindeks', 10),
		array(166, 'Makalah dalam prosiding nasional ber-ISSN/ISBN', 7.5),
		array(167, 'Pemakalah undangan (invited speaker) secara oral, tingkat internasional', 5),
		array(168, 'Pemakalah undangan (invited speaker) secara oral, tingkat nasional', 3),
		array(169, 'Artikel ilmiah populer di media massa', 2),
		array(170, 'Hasil penelitian institusi atau kerja sama industri yang tidak dipublikasikan', 2),
		array(171, 'Menerjemahkan buku ilmiah yang diterbitkan (ber-ISBN)', 15),
		array(172, 'Menyunting buku ilmiah yang diterbitkan (ber-ISBN)', 10),
		array(173, 'Paten internasional yang telah diterapkan di industri', 60),
		array(174, 'Paten internasional', 50),
		array(175, 'Paten nasional yang telah diterapkan di industri', 40),
		array(176, 'Paten nasional', 30),
		array(177, 'Paten sederhana', 20),
		array(178, 'Hak cipta, desain industri, atau indikasi geografis yang terdaftar (maksimal 1 per semester)', 15),
		array(179, 'Karya inovatif tanpa paten yang berdampak tingkat nasional', 40),
		array(180, 'Naskah kebijakan (policy paper) tingkat internasional', 20),
		array(181, 'Naskah kebijakan (policy paper) tingkat nasional', 15),
		array(182, 'Naskah kebijakan (policy paper) tingkat lokal', 10),
		array(183, 'Rancangan atau teknologi tanpa paten yang disajikan di forum resmi, tingkat internasional', 20),
		array(184, 'Rancangan atau teknologi tanpa paten yang disajikan di forum resmi, tingkat nasional', 15),
		array(185, 'Rancangan atau teknologi tanpa paten yang disajikan di forum resmi, tingkat lokal', 10),
		array(186, 'Karya cipta seni (tidak terdaftar HKI), tingkat internasional (maksimal 2 per tahun)', 20),
		array(187, 'Karya cipta seni (tidak terdaftar HKI), tingkat nasional (maksimal 2 per tahun)', 15),
		array(188, 'Karya cipta seni (tidak terdaftar HKI), tingkat lokal (maksimal 2 per tahun)', 10),
		array(189, 'Karya seni konseptual, tingkat internasional (maksimal 2 per tahun)', 15),
		array(190, 'Karya seni konseptual, tingkat nasional (maksimal 2 per tahun)', 10),
		array(191, 'Karya seni konseptual, tingkat lokal (maksimal 2 per tahun)', 5),
		array(192, 'Aransemen atau produksi karya seni, tingkat internasional (maksimal 2 per tahun)', 10),
		array(193, 'Aransemen atau produksi karya seni, tingkat nasional (maksimal 2 per tahun)', 6),
		array(194, 'Aransemen atau produksi karya seni, tingkat lokal (maksimal 2 per tahun)', 3),
		array(195, 'Pertunjukan karya seni, tingkat internasional (maksimal 2 per tahun)', 6),
		array(196, 'Pertunjukan karya seni, tingkat nasional (maksimal 2 per tahun)', 4),
		array(197, 'Pertunjukan karya seni, tingkat lokal (maksimal 2 per tahun)', 2),
		array(198, 'Naskah drama, novel, kumpulan cerpen atau puisi (penulis tunggal), tingkat internasional (maksimal 2 per tahun)', 20),
		array(199, 'Naskah drama, novel, kumpulan cerpen atau puisi (penulis tunggal), tingkat nasional (maksimal 2 per tahun)', 15),
		array(200, 'Naskah drama, novel, kumpulan cerpen atau puisi (penulis tunggal), tingkat lokal (maksimal 2 per tahun)', 10),
	);
}

/* Pilihan pembagian angka kredit antarpenulis (hal. 19-20). nilai persen => label */
function nx_bagian_opsi()
{
	return array(
		'100' => '100% (penulis/pencipta tunggal)',
		'60'  => '60% (penulis pertama sekaligus korespondensi)',
		'50'  => '50% (dua penulis, pertama bukan korespondensi)',
		'40'  => '40% (penulis pertama atau korespondensi, tiga penulis atau lebih)',
	);
}

/* Field pembagian: select preset + isian persen bila "lainnya". $cur = persen saat ini. */
function nx_bagian_field($cur = 100)
{
	$opsi = nx_bagian_opsi();
	$cur = (float) $cur;
	$preset = isset($opsi[(string) (int) $cur]) && (float) (int) $cur === $cur;
	echo '<div class="form-group" id="nx-bagian-wrap"><label class="control-label col-sm-12" for="bagian">Pembagian angka kredit antarpenulis:</label><div class="col-sm-8">';
	echo '<select class="form-control" id="bagian" name="bagian">';
	foreach ($opsi as $k => $v) echo '<option value="' . nx_e($k) . '"' . ($preset && (string) (int) $cur === (string) $k ? ' selected' : '') . '>' . nx_e($v) . '</option>';
	echo '<option value="lain"' . ($preset ? '' : ' selected') . '>Anggota lain (isi persen sendiri)</option></select>';
	echo '<input type="number" class="form-control mt-2" id="bagian_lain" name="bagian_lain" min="1" max="100" step="0.01" placeholder="Persen bagian Anda, 1 sampai 100" value="' . ($preset ? '' : nx_e($cur)) . '"' . ($preset ? ' hidden' : '') . '>';
	echo '<small class="form-text text-muted">Poin berkas = poin rubrik x volume x bagian. Aturan 2 penulis: 60/40 bila penulis pertama juga korespondensi, 50/50 bila tidak. Tiga penulis atau lebih: 60% untuk pertama dan korespondensi sekaligus; bila berbeda orang, masing-masing 40%; sisanya dibagi rata anggota lain.</small>';
	echo '</div></div>';
	echo '<script nonce="' . nx_nonce() . '">(function(){var s=document.getElementById("bagian"),o=document.getElementById("bagian_lain");function f(){var l=s.value==="lain";o.hidden=!l;if(l){o.setAttribute("required","");}else{o.removeAttribute("required");}}s.addEventListener("change",f);f();})();</script>';
}

/* Membaca pembagian dari POST. Mengembalikan persen (float 1..100) atau null bila tidak valid. */
function nx_bagian_dari_post()
{
	$v = isset($_POST['bagian']) && is_string($_POST['bagian']) ? trim($_POST['bagian']) : '100';
	if ($v === 'lain') {
		$l = isset($_POST['bagian_lain']) && is_string($_POST['bagian_lain']) ? trim($_POST['bagian_lain']) : '';
		if (!is_numeric($l)) return null;
		$p = (float) $l;
	} elseif (isset(nx_bagian_opsi()[$v])) {
		$p = (float) $v;
	} else {
		return null;
	}
	return ($p >= 1 && $p <= 100) ? round($p, 2) : null;
}

/* Apakah kode rubrik termasuk unsur penelitian (rubrik lama 49-103 atau juknis 2026 152-200)? */
function nx_rubrik_penelitian($kode)
{
	$k = (int) $kode;
	return ($k >= 49 && $k <= 103) || ($k >= 152 && $k <= 200);
}
