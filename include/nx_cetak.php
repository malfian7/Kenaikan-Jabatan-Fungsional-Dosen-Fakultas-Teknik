<?php
/* Lembar usulan kenaikan jabatan: halaman cetak mandiri (bisa disimpan sebagai PDF lewat dialog cetak peramban). */
include_once __DIR__ . '/nx_helpers.php';
include_once __DIR__ . '/nx_kelayakan.php';
include_once __DIR__ . '/nx_riwayat.php';

function nx_cetak_halaman($koneksi, $id_pengajuan)
{
	$p = $koneksi->GetRow('SELECT id_pengajuan, nip_dosen, tanggal_pengajuan, jabatan_asal, tanggal_sk, jabatan_tujuan, dokumen_pendukung, status_pengajuan, total_pak_akhir, nip_penilai_1, nip_penilai_2 FROM pengajuan WHERE id_pengajuan = ?', array($id_pengajuan));
	if (!$p) { nx_abort(404, 'Pengajuan tidak ditemukan', 'Pengajuan yang diminta tidak ada.'); }
	$d = $koneksi->GetRow('SELECT nama_dosen, unit_kerja_dosen, pendidikan_terakhir, status_kepegawaian, tmt_dosen_tetap, ak_kumulatif FROM dosen WHERE nip_dosen = ?', array($p[1]));
	$kl = nx_kelayakan($koneksi, $id_pengajuan);
	$berkas = (array) $koneksi->GetAll('SELECT rubrik.aspek_rubrik, rubrik.kegiatan_rubrik, berkas_pak.volume, berkas_pak.persen_bagian, berkas_pak.poin_akhir, berkas_pak.status_berkas, berkas_pak.bukti_berkas FROM berkas_pak INNER JOIN rubrik ON berkas_pak.kode_rubrik = rubrik.kode_rubrik WHERE berkas_pak.id_pengajuan = ? ORDER BY berkas_pak.id_berkas', array($id_pengajuan));
	$pub = (array) $koneksi->GetAll('SELECT judul, jenis, peringkat, peran FROM publikasi WHERE id_pengajuan = ? ORDER BY id_publikasi', array($id_pengajuan));
	$jenis = nx_jenis_publikasi(); $peran = nx_peran_opsi();
	$riw = array_reverse(nx_riwayat_baris($koneksi, $id_pengajuan));
	$nama = $d ? $d[0] : $p[1];
	$tgl = function ($v) { $t = strtotime((string) $v); return $t ? date('d/m/Y', $t) : '-'; };
	$tot = 0.0; foreach ($berkas as $b) { $tot += (float) $b[4]; }
	?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lembar Usulan #<?php echo (int) $id_pengajuan; ?> - <?php echo nx_e($nama); ?></title>
<style>
*{box-sizing:border-box}body{margin:0;background:#eef0f6;color:#15172e;font:13px/1.5 "Segoe UI",system-ui,-apple-system,Arial,sans-serif}
.bar{position:sticky;top:0;display:flex;gap:10px;justify-content:space-between;align-items:center;padding:10px 20px;background:#fff;border-bottom:1px solid #dcdfec}
.bar button,.bar a{font:inherit;font-weight:600;padding:8px 16px;border-radius:10px;border:1px solid #5646e0;background:#5646e0;color:#fff;cursor:pointer;text-decoration:none}
.bar a{background:#fff;color:#5646e0}
.page{max-width:820px;margin:20px auto;padding:36px 44px;background:#fff;border:1px solid #dcdfec;border-radius:6px}
h1{font-size:19px;margin:0 0 2px;text-align:center;letter-spacing:.02em}.sub{text-align:center;color:#4a4e69;margin-bottom:18px}
h2{font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:#3b2db0;border-bottom:1.5px solid #3b2db0;padding-bottom:3px;margin:20px 0 8px}
table{width:100%;border-collapse:collapse}th,td{padding:5px 8px;border:1px solid #cfd2e2;text-align:left;vertical-align:top}th{background:#f0eefe;font-size:12px}
table.kv td:first-child{width:34%;background:#f7f8fc;font-weight:600}td.n,th.n{text-align:right;white-space:nowrap}
.ok{color:#08724d;font-weight:600}.no{color:#b92a2f;font-weight:600}.wr{color:#855000;font-weight:600}.mut{color:#62667f}
ul.cek{list-style:none;margin:0;padding:0}ul.cek li{display:flex;justify-content:space-between;gap:16px;padding:4px 0;border-bottom:1px dotted #cfd2e2}
.ttd{display:flex;justify-content:space-between;gap:30px;margin-top:34px;page-break-inside:avoid}.ttd div{flex:1;text-align:center}.ttd .sp{height:64px}
.foot{margin-top:22px;color:#62667f;font-size:11px;text-align:center}
td,th{overflow-wrap:anywhere}.bar{flex-wrap:wrap}
@media (max-width:640px){.page{margin:10px;padding:20px 14px;overflow-x:auto}h1{font-size:16px}.ttd{flex-direction:column}table.kv td:first-child{width:40%}}
@media print{body{background:#fff}.bar{display:none}.page{margin:0;border:0;border-radius:0;max-width:none;padding:0}tr,li{page-break-inside:avoid}h2{page-break-after:avoid}@page{size:A4;margin:16mm 14mm}}
</style></head><body>
<div class="bar"><span><b>Lembar Usulan</b> <span class="mut">pengajuan #<?php echo (int) $id_pengajuan; ?></span></span><span><a href="#" id="kembali">Kembali</a> <button type="button" id="cetak">Cetak / Simpan PDF</button></span></div>
<div class="page">
<div style="text-align:center;margin-bottom:6px"><img src="<?php echo nx_e(nx_logo('../')); ?>" alt="" height="54" style="height:54px;width:auto"></div>
<h1>LEMBAR USULAN KENAIKAN JABATAN FUNGSIONAL DOSEN</h1>
<div class="sub"><?php echo nx_e(nx_pengaturan('institusi')); ?> · Nomor pengajuan #<?php echo (int) $id_pengajuan; ?></div>

<h2>Data pengusul</h2>
<table class="kv">
<tr><td>Nama</td><td><?php echo nx_e($nama); ?></td></tr>
<tr><td>NIP</td><td><?php echo nx_e($p[1]); ?></td></tr>
<tr><td>Unit kerja</td><td><?php echo nx_e($d ? $d[1] : '-'); ?></td></tr>
<tr><td>Pendidikan terakhir</td><td><?php echo nx_e($d && $d[2] ? $d[2] : '-'); ?></td></tr>
<tr><td>Status kepegawaian</td><td><?php echo nx_e($d && $d[3] ? $d[3] : '-'); ?><?php if ($d && $d[4]) { echo ' (TMT dosen tetap ' . nx_e($tgl($d[4])) . ')'; } ?></td></tr>
<tr><td>AK kumulatif SK terakhir</td><td><?php echo nx_e($d ? nx_num($d[5]) : '-'); ?></td></tr>
</table>

<h2>Data usulan</h2>
<table class="kv">
<tr><td>Tanggal pengajuan</td><td><?php echo nx_e($tgl($p[2])); ?></td></tr>
<tr><td>Jabatan asal</td><td><?php echo nx_e($p[3]); ?> (SK <?php echo nx_e($tgl($p[4])); ?>)</td></tr>
<tr><td>Jabatan tujuan</td><td><?php echo nx_e($p[5]); ?></td></tr>
<tr><td>Status</td><td><?php echo nx_e($p[7]); ?></td></tr>
<tr><td>Total angka kredit</td><td><?php echo nx_e($kl ? nx_num($kl['total']) . ' dari syarat ' . nx_num($kl['syarat']) : nx_num($p[8])); ?> AK</td></tr>
<tr><td>Dokumen pendukung</td><td><?php echo nx_e($p[6]); ?></td></tr>
</table>
<?php if ($kl) : ?>
<h2>Pemeriksaan kelayakan</h2>
<ul class="cek">
<?php foreach ($kl['cek'] as $c) :
	$cls = $c['level'] === 'info' ? 'mut' : ($c['ok'] ? 'ok' : ($c['level'] === 'wajib' ? 'no' : 'wr'));
	$tanda = $c['level'] === 'info' ? 'Info' : ($c['ok'] ? 'Terpenuhi' : ($c['level'] === 'wajib' ? 'Belum' : 'Catatan')); ?>
<li><span><?php echo nx_e($c['label']); ?> <span class="mut"><?php echo nx_e($c['nilai']); ?></span></span><span class="<?php echo $cls; ?>"><?php echo $tanda; ?></span></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h2>Daftar berkas angka kredit</h2>
<?php if ($berkas) : ?>
<table><thead><tr><th>Unsur</th><th>Kegiatan</th><th class="n">Volume</th><th class="n">Bagian</th><th class="n">AK</th><th>Status</th></tr></thead><tbody>
<?php foreach ($berkas as $b) : ?>
<tr><td><?php echo nx_e($b[0]); ?></td><td><?php echo nx_e($b[1]); ?></td><td class="n"><?php echo nx_e(nx_num($b[2])); ?></td><td class="n"><?php echo nx_e(nx_num($b[3] === null || $b[3] === '' ? 100 : $b[3])); ?>%</td><td class="n"><?php echo nx_e(nx_num($b[4])); ?></td><td><?php echo nx_e($b[5]); ?></td></tr>
<?php endforeach; ?>
<tr><td colspan="4" class="n"><b>Jumlah angka kredit berkas</b></td><td class="n"><b><?php echo nx_e(nx_num($tot)); ?></b></td><td></td></tr>
</tbody></table>
<?php else : ?><p class="mut">Belum ada berkas.</p><?php endif; ?>
<?php if ($pub) : ?>
<h2>Publikasi syarat khusus</h2>
<table><thead><tr><th>Judul</th><th>Jenis</th><th>Peringkat</th><th>Peran</th></tr></thead><tbody>
<?php foreach ($pub as $b) : ?>
<tr><td><?php echo nx_e($b[0]); ?></td><td><?php echo nx_e(isset($jenis[$b[1]]) ? $jenis[$b[1]] : $b[1]); ?></td><td><?php echo nx_e($b[2]); ?></td><td><?php echo nx_e(isset($peran[$b[3]]) ? $peran[$b[3]] : $b[3]); ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>

<h2>Riwayat proses</h2>
<table><thead><tr><th width="120">Waktu</th><th>Kejadian</th><th>Oleh</th></tr></thead><tbody>
<?php foreach ($riw as $r) : ?>
<tr><td><?php echo nx_e(date('d/m/Y H:i', strtotime($r[0]))); ?></td><td><?php echo nx_e(nx_riwayat_judul($r)); if ($r[5] && $r[4] !== 'data_awal') { echo ' <span class="mut">(' . nx_e($r[5]) . ')</span>'; } ?></td><td><?php echo nx_e(nx_nama_aktor($koneksi, $r[3])); ?></td></tr>
<?php endforeach; ?>
</tbody></table>

<div class="ttd"><div>Pengusul<div class="sp"></div><b><?php echo nx_e($nama); ?></b><br><span class="mut">NIP <?php echo nx_e($p[1]); ?></span></div><div>Petugas pemeriksa<div class="sp"></div><span class="mut">(................................)</span></div><div>Mengetahui, <?php echo nx_e(nx_pengaturan('pimpinan_jabatan')); ?><div class="sp"></div><?php if (nx_pengaturan('pimpinan_nama') !== '') { echo '<b>' . nx_e(nx_pengaturan('pimpinan_nama')) . '</b>'; } else { echo '<span class="mut">(................................)</span>'; } ?></div></div>
<div class="foot">Dicetak dari <?php echo nx_e(nx_pengaturan('app')); ?> pada <?php echo nx_e(date('d/m/Y H:i')); ?>. Angka kredit resmi ditetapkan oleh penilai.</div>
</div><script nonce="<?php echo nx_nonce(); ?>">document.getElementById("cetak").addEventListener("click",function(){window.print()});document.getElementById("kembali").addEventListener("click",function(e){e.preventDefault();history.back()});</script></body></html>
<?php
}
