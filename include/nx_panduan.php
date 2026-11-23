<?php
/* Panduan pengguna per peran: halaman mandiri, bisa dicetak atau disimpan sebagai PDF. */
include_once __DIR__ . '/nx_helpers.php';

function nx_panduan_isi($role)
{
	$umum_status = array(
		array('BELUM DIKIRIM', 'Pengajuan masih disusun dosen dan belum masuk antrean.'),
		array('TERKIRIM', 'Menunggu pemeriksaan keaslian berkas oleh pegawai.'),
		array('BERKAS DITOLAK', 'Ada berkas yang perlu diperbaiki dosen.'),
		array('TELAH REVISI', 'Dosen sudah memperbaiki, menunggu pemeriksaan ulang.'),
		array('BERKAS DITERIMA', 'Semua berkas valid, menunggu penilaian dua penilai.'),
		array('PENILAIAN SELESAI', 'Kedua penilai selesai, menunggu keputusan pegawai.'),
		array('DITERIMA / DITOLAK', 'Keputusan akhir sudah terbit.'),
	);
	$p = array();
	if ($role === 'dosen') {
		$p['judul'] = 'Panduan Dosen';
		$p['intro'] = 'Alur singkat dari menyiapkan data sampai pengajuan selesai dinilai.';
		$p['langkah'] = array(
			array('Lengkapi profil', 'Buka Profil, lalu Ubah. Isi pendidikan terakhir, status kepegawaian, tanggal diangkat dosen tetap, angka kredit kumulatif SK terakhir, dan pernyataan (SKP baik, uji kompetensi, sertifikat pendidik). Data ini dipakai untuk memeriksa syarat kenaikan jenjang.'),
			array('Buat pengajuan', 'Pilih Pengajuan baru, tentukan jabatan tujuan, tanggal SK jabatan terakhir, dan tautan dokumen pendukung (Google Drive atau sejenisnya, pastikan bisa dibuka pegawai).'),
			array('Tambah berkas angka kredit', 'Untuk tiap kegiatan pilih unsur dan butir rubrik, isi volume dan tautan bukti. Untuk karya bersama isi pembagian angka kredit antarpenulis. Poin dihitung otomatis dari rubrik, volume, dan bagian Anda.'),
			array('Isi publikasi syarat khusus', 'Hanya untuk kenaikan jenjang (mis. Lektor ke Lektor Kepala). Tambahkan jurnal atau karya yang dipersyaratkan lengkap dengan peringkat dan peran Anda.'),
			array('Baca panel kelayakan', 'Panel di halaman pengajuan menunjukkan syarat yang sudah dan belum terpenuhi. Syarat bertanda Wajib harus terpenuhi agar pengajuan bisa dikirim; lainnya berupa catatan.'),
			array('Kirim pengajuan', 'Setelah syarat wajib terpenuhi, tekan Kirim Pengajuan. Berkas tidak bisa dihapus setelah terkirim.'),
			array('Pantau dan revisi', 'Lihat status di Riwayat dan lonceng notifikasi. Jika berkas ditolak, buka pengajuan, perbaiki berkas yang ditandai beserta catatan pegawai, lalu kirim ulang.'),
			array('Cetak lembar usulan', 'Di panel Riwayat Status ada tombol Cetak lembar usulan. Pilih Simpan sebagai PDF untuk arsip atau tanda tangan.'),
		);
		$p['faq'] = array(
			array('Tombol kirim ditolak. Mengapa?', 'Ada syarat Wajib yang belum terpenuhi, biasanya total angka kredit atau belum ada berkas. Lihat panel kelayakan untuk kekurangannya.'),
			array('Poin berbeda dari hitungan saya?', 'Poin otomatis = poin rubrik x volume x bagian penulis. Angka kredit resmi tetap ditetapkan penilai dan bisa lebih rendah dari usulan.'),
			array('Berkas saya ditolak, apa langkahnya?', 'Buka pengajuan, lihat catatan di berkas yang ditolak, ubah berkas, lalu kirim ulang. Status berubah menjadi Telah Revisi.'),
		);
	} elseif ($role === 'pegawai') {
		$p['judul'] = 'Panduan Pegawai (Admin)';
		$p['intro'] = 'Tugas kepegawaian: memeriksa berkas, mengelola penilai, memutuskan, serta mengatur aplikasi.';
		$p['langkah'] = array(
			array('Periksa pengajuan masuk', 'Data Pengajuan, tab Belum Diperiksa dan Telah Direvisi. Buka tiap pengajuan: panel kelayakan dan riwayat tampil di atas daftar berkas. Tandai tiap berkas valid atau tolak dengan catatan yang jelas.'),
			array('Terima atau kembalikan pengajuan', 'Bila semua berkas valid, terima pengajuan. Bila ada yang ditolak, kembalikan ke dosen; dosen mendapat notifikasi otomatis.'),
			array('Tugaskan dua penilai', 'Pada pengajuan berstatus Berkas Diterima pilih dua penilai berbeda. Penilai mendapat notifikasi penugasan.'),
			array('Tetapkan keputusan', 'Setelah kedua penilai selesai, buka Persetujuan, tinjau total angka kredit, lalu terima atau tolak. Keputusan masuk ke riwayat dan dosen diberi tahu.'),
			array('Kelola penilai', 'Data Penilai: tambah, ubah, atau hapus akun penilai (penilai yang sedang memegang pengajuan tidak bisa dihapus).'),
			array('Atur aturan jabatan', 'Aturan Jabatan memuat parameter kenaikan jenjang (masa jabatan, pendidikan, jumlah publikasi). Ubahan ditandai Disesuaikan institusi.'),
			array('Pantau lewat Statistik dan ekspor', 'Statistik menampilkan volume per bulan, lama tiap tahap, beban penilai, dan antrean terlama. Ekspor CSV mengunduh seluruh pengajuan untuk Excel.'),
			array('Pengaturan dan backup', 'Menu Pengaturan: nama aplikasi, institusi, penandatangan, dan logo. Unduh backup database secara berkala dan simpan di tempat aman.'),
		);
		$p['faq'] = array(
			array('Bagaimana memulihkan backup?', 'Buka phpMyAdmin, pilih database kosong, lalu Impor berkas .sql hasil backup. Trigger dibuat ulang otomatis saat login berikutnya.'),
			array('Mengapa penilai tidak bisa dipilih?', 'Pastikan sudah ada minimal dua akun penilai di Data Penilai dan keduanya berbeda orang.'),
			array('Di mana jejak siapa mengubah status?', 'Panel Riwayat Status di halaman detail pengajuan, dan kolom riwayat di lembar usulan cetak.'),
		);
	} else {
		$p['judul'] = 'Panduan Penilai';
		$p['intro'] = 'Menilai berkas angka kredit pengajuan yang ditugaskan kepada Anda.';
		$p['langkah'] = array(
			array('Lihat penugasan', 'Daftar Pengajuan menampilkan pengajuan yang menunggu penilaian Anda. Lonceng notifikasi memberi tahu bila ada tugas baru.'),
			array('Nilai tiap berkas', 'Buka berkas, periksa bukti, lalu isi poin. Poin Anda tidak boleh melebihi batas rubrik (poin rubrik x volume x bagian penulis).'),
			array('Gunakan Tabel Angka Kredit', 'Menu Tabel PAK menampilkan rubrik dan nilai maksimum tiap butir kegiatan.'),
			array('Selesaikan penilaian', 'Setelah semua berkas dinilai, kirim penilaian. Penilaian yang sudah dikirim tidak dapat diubah. Bila penilai lain juga selesai, pegawai mengambil keputusan.'),
			array('Lihat yang sudah selesai', 'Daftar Pengajuan Selesai menyimpan riwayat penilaian Anda.'),
		);
		$p['faq'] = array(
			array('Poin saya ditolak sistem?', 'Poin melebihi batas rubrik untuk volume dan bagian penulis berkas itu. Turunkan sampai batas yang ditampilkan.'),
			array('Apakah penilaian bisa diubah setelah dikirim?', 'Tidak. Pastikan semua berkas sudah benar sebelum menekan kirim.'),
		);
	}
	$p['status'] = $umum_status;
	return $p;
}

function nx_panduan_halaman($role)
{
	$p = nx_panduan_isi($role);
	$balik = $role . '_beranda.php?nip_' . $role . '=' . rawurlencode(isset($_SESSION['nip']) ? $_SESSION['nip'] : '');
	?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo nx_e($p['judul']); ?> · <?php echo nx_e(nx_pengaturan('app')); ?></title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f4f9;color:#15172e;font:15px/1.6 "Segoe UI",system-ui,-apple-system,Arial,sans-serif}
.bar{position:sticky;top:0;display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;padding:10px 20px;background:#fff;border-bottom:1px solid #dcdfec}
.bar a,.bar button{font:inherit;font-weight:600;padding:8px 16px;border-radius:10px;border:1px solid #5646e0;background:#fff;color:#5646e0;cursor:pointer;text-decoration:none}.bar button{background:#5646e0;color:#fff}
.page{max-width:780px;margin:24px auto;padding:36px 44px;background:#fff;border:1px solid #dcdfec;border-radius:16px}
.brand{display:flex;align-items:center;gap:12px;margin-bottom:14px}.brand img{height:44px;width:auto}.brand span{font-weight:700}
h1{font-size:26px;margin:0 0 4px}.lead{color:#4a4e69;margin:0 0 8px}h2{font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:#3b2db0;margin:30px 0 12px;border-bottom:1.5px solid #3b2db0;padding-bottom:4px}
ol.s{list-style:none;counter-reset:s;margin:0;padding:0}ol.s li{counter-increment:s;position:relative;padding:0 0 18px 48px}
ol.s li::before{content:counter(s);position:absolute;left:0;top:0;width:32px;height:32px;border-radius:50%;background:#f0eefe;color:#3b2db0;font-weight:700;display:grid;place-items:center}
ol.s b{display:block;font-size:15.5px}ol.s p{margin:2px 0 0;color:#4a4e69}
table{width:100%;border-collapse:collapse;font-size:14px}th,td{padding:8px 10px;border:1px solid #cfd2e2;text-align:left;vertical-align:top}th{background:#f0eefe}td:first-child{font-weight:600;white-space:nowrap}
dl{margin:0}dt{font-weight:700;margin-top:12px}dd{margin:2px 0 0;color:#4a4e69}
.foot{margin-top:28px;color:#62667f;font-size:12px;text-align:center}
@media(max-width:640px){.page{margin:10px;padding:22px 16px}td:first-child{white-space:normal}}
@media print{body{background:#fff}.bar{display:none}.page{margin:0;border:0;padding:0;max-width:none}li,tr{page-break-inside:avoid}h2{page-break-after:avoid}@page{size:A4;margin:16mm 14mm}}
</style></head><body>
<div class="bar"><a href="<?php echo nx_e($balik); ?>">Kembali ke aplikasi</a><button type="button" id="cetak">Cetak / Simpan PDF</button></div>
<div class="page">
<div class="brand"><img src="<?php echo nx_e(nx_logo('../')); ?>" alt=""><span><?php echo nx_e(nx_pengaturan('app')); ?> · <?php echo nx_e(nx_pengaturan('institusi')); ?></span></div>
<h1><?php echo nx_e($p['judul']); ?></h1>
<p class="lead"><?php echo nx_e($p['intro']); ?></p>
<h2>Langkah kerja</h2>
<ol class="s"><?php foreach ($p['langkah'] as $l) : ?><li><b><?php echo nx_e($l[0]); ?></b><p><?php echo nx_e($l[1]); ?></p></li><?php endforeach; ?></ol>
<h2>Arti status pengajuan</h2>
<table><thead><tr><th>Status</th><th>Artinya</th></tr></thead><tbody><?php foreach ($p['status'] as $s) : ?><tr><td><?php echo nx_e($s[0]); ?></td><td><?php echo nx_e($s[1]); ?></td></tr><?php endforeach; ?></tbody></table>
<h2>Pertanyaan umum</h2>
<dl><?php foreach ($p['faq'] as $f) : ?><dt><?php echo nx_e($f[0]); ?></dt><dd><?php echo nx_e($f[1]); ?></dd><?php endforeach; ?></dl>
<div class="foot">Butuh bantuan lain? Hubungi pegawai kepegawaian di institusi Anda.</div>
</div>
<script nonce="<?php echo nx_nonce(); ?>">document.getElementById("cetak").addEventListener("click",function(){window.print()});</script>
</body></html>
<?php
}
