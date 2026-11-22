<?php
/* Ekspor seluruh pengajuan ke CSV (hanya baca). Nilai yang diawali = + - @ diberi awalan ' agar aman dibuka di Excel. */
include_once("../koneksi/conn.php");
function nx_csv_sel($v)
{
	$v = (string) $v;
	if ($v !== '' && strpbrk($v[0], "=+-@\t\r") !== false) { $v = "'" . $v; }
	return '"' . str_replace('"', '""', $v) . '"';
}
$rows = (array) $koneksi->GetAll("SELECT p.id_pengajuan, p.tanggal_pengajuan, p.nip_dosen, d.nama_dosen, d.unit_kerja_dosen, p.jabatan_asal, p.jabatan_tujuan, p.status_pengajuan, p.total_pak_akhir, TRIM(TRAILING 'V' FROM p.nip_penilai_1), TRIM(TRAILING 'V' FROM p.nip_penilai_2), (SELECT COUNT(*) FROM berkas_pak b WHERE b.id_pengajuan = p.id_pengajuan) FROM pengajuan p LEFT JOIN dosen d ON d.nip_dosen = p.nip_dosen ORDER BY p.id_pengajuan");
while (ob_get_level()) { ob_end_clean(); }
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="pengajuan-sipak-' . date('Ymd') . '.csv"');
echo "\xEF\xBB\xBF";
echo implode(';', array_map('nx_csv_sel', array('ID', 'Tanggal pengajuan', 'NIP dosen', 'Nama dosen', 'Unit kerja', 'Jabatan asal', 'Jabatan tujuan', 'Status', 'Total AK', 'NIP penilai 1', 'NIP penilai 2', 'Jumlah berkas'))) . "\r\n";
foreach ($rows as $r) {
	echo implode(';', array_map('nx_csv_sel', $r)) . "\r\n";
}
exit;
