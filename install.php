<?php
/*
 * Installer SIPAK: sekali jalan. Memeriksa lingkungan, membuat database dan tabel,
 * akun admin pegawai pertama, pengaturan institusi, lalu mengunci diri sendiri.
 * Setelah terpasang halaman ini menolak semua permintaan.
 */
define('NX_INSTALLER', true);
$root = __DIR__;
$kfg = $root . '/koneksi/config.php';
$lock = $root . '/koneksi/terpasang.lock';
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_name('SIPAKINSTALL');
session_start();
header('X-Frame-Options: SAMEORIGIN'); header('X-Content-Type-Options: nosniff'); header('Referrer-Policy: same-origin'); header('Cache-Control: no-store');
$nonce = rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; script-src 'self' 'nonce-$nonce'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'");

function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function halaman($judul, $isi, $kode = 200)
{
	global $nonce;
	http_response_code($kode);
	echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($judul) . ' · Pemasangan SIPAK</title>';
	echo '<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;background:#f3f4f9;color:#15172e;font:14px/1.55 "Segoe UI",system-ui,-apple-system,Arial,sans-serif;display:grid;place-items:start center;padding:32px 16px}';
	echo '.c{width:100%;max-width:640px;background:#fff;border:1px solid rgba(21,23,46,.1);border-radius:18px;padding:28px 32px;box-shadow:0 18px 40px -12px rgba(21,23,46,.18)}h1{font-size:22px;margin:0 0 4px}.m{color:#4a4e69;margin:0 0 18px}h2{font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#3b2db0;margin:24px 0 8px}';
	echo 'label{display:block;font-weight:600;margin:12px 0 4px}input[type=text],input[type=password]{width:100%;padding:10px 12px;border:1px solid rgba(21,23,46,.18);border-radius:10px;font:inherit}input:focus{outline:2px solid #5646e0;outline-offset:1px}small{color:#62667f;display:block;margin-top:3px}';
	echo '.row{display:grid;grid-template-columns:1fr 1fr;gap:0 14px}@media(max-width:560px){.row{grid-template-columns:1fr}.c{padding:22px 18px}}';
	echo 'ul.k{list-style:none;padding:0;margin:0}ul.k li{display:flex;justify-content:space-between;gap:12px;padding:5px 0;border-bottom:1px dotted #cfd2e2}.ok{color:#08724d;font-weight:600}.no{color:#b92a2f;font-weight:600}';
	echo 'button{margin-top:22px;padding:12px 22px;border:0;border-radius:12px;background:#5646e0;color:#fff;font:inherit;font-weight:700;cursor:pointer}button:disabled{opacity:.5;cursor:not-allowed}.err{background:#fdeced;color:#b92a2f;border-radius:10px;padding:10px 14px;margin:0 0 14px}a.t{display:inline-block;margin-top:18px;padding:12px 22px;border-radius:12px;background:#5646e0;color:#fff;text-decoration:none;font-weight:700}.w{background:#fff3d9;color:#855000;border-radius:10px;padding:10px 14px;margin-top:14px}';
	echo '.chk{display:flex;gap:10px;align-items:flex-start;font-weight:500}.chk input{margin-top:4px}</style></head><body><div class="c">' . $isi . '</div></body></html>';
	exit;
}

/* Sudah terpasang: tolak. */
if (is_file($lock) || is_file($kfg)) {
	halaman('Sudah terpasang', '<h1>Aplikasi sudah terpasang</h1><p class="m">Halaman pemasangan dikunci. Untuk memasang ulang, hapus berkas koneksi/config.php dan koneksi/terpasang.lock di server (data lama tidak ikut terhapus).</p><a class="t" href="index.php">Ke halaman depan</a>', 403);
}
/* Pemasangan lama (XAMPP bawaan) tanpa berkas lock: kunci juga. */
$m0 = @new mysqli('localhost', 'root', '', 'sipak');
if ($m0 && !$m0->connect_errno) {
	$r = $m0->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sipak' AND TABLE_NAME = 'pegawai'");
	if ($r && (int) $r->fetch_row()[0] > 0) {
		@file_put_contents($lock, date('c') . "\n");
		halaman('Sudah terpasang', '<h1>Aplikasi sudah terpasang</h1><p class="m">Database "sipak" sudah berisi data SIPAK, jadi pemasangan ulang ditolak demi keamanan.</p><a class="t" href="index.php">Ke halaman depan</a>', 403);
	}
}

if (empty($_SESSION['t'])) { $_SESSION['t'] = bin2hex(random_bytes(16)); }
$cek = array(
	array('PHP 7.0 atau lebih baru (terpasang ' . PHP_VERSION . ')', version_compare(PHP_VERSION, '7.0.0', '>=')),
	array('Ekstensi mysqli', extension_loaded('mysqli')),
	array('Ekstensi mbstring', extension_loaded('mbstring')),
	array('Ekstensi gd atau getimagesize untuk unggah logo', function_exists('getimagesize')),
	array('Folder koneksi/ bisa ditulis', is_writable($root . '/koneksi')),
	array('Folder assets/img/logo/ bisa ditulis', is_writable($root . '/assets/img/logo')),
	array('Berkas install/schema.sql tersedia', is_file($root . '/install/schema.sql')),
);
$siap = true; foreach ($cek as $c) { if (!$c[1]) { $siap = false; } }

$v = array('host' => 'localhost', 'dbuser' => 'root', 'dbname' => 'sipak', 'nip' => '', 'nama' => '', 'app' => 'SIPAK', 'institusi' => '', 'unit' => '');
$galat = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	foreach ($v as $k => $_) { if (isset($_POST[$k]) && is_string($_POST[$k])) { $v[$k] = trim($_POST[$k]); } }
	$dbpass = isset($_POST['dbpass']) && is_string($_POST['dbpass']) ? $_POST['dbpass'] : '';
	$pw = isset($_POST['pw']) && is_string($_POST['pw']) ? $_POST['pw'] : '';
	$pw2 = isset($_POST['pw2']) && is_string($_POST['pw2']) ? $_POST['pw2'] : '';
	$demo = !empty($_POST['demo']);
	do {
		if (!isset($_POST['t']) || !hash_equals($_SESSION['t'], (string) $_POST['t'])) { $galat = 'Formulir kedaluwarsa. Muat ulang halaman.'; break; }
		if (!$siap) { $galat = 'Perbaiki dulu pemeriksaan lingkungan di atas.'; break; }
		if (!preg_match('/^[A-Za-z0-9.\-_:]{1,100}$/', $v['host'])) { $galat = 'Host database tidak valid.'; break; }
		if (!preg_match('/^[A-Za-z0-9_.\-@]{1,60}$/', $v['dbuser'])) { $galat = 'Nama pengguna database tidak valid.'; break; }
		if (!preg_match('/^[A-Za-z0-9_]{1,40}$/', $v['dbname'])) { $galat = 'Nama database hanya boleh huruf, angka, dan garis bawah.'; break; }
		if (!preg_match('/^[0-9A-Za-z]{4,18}$/', $v['nip'])) { $galat = 'NIP admin harus 4 sampai 18 huruf atau angka.'; break; }
		if (mb_strlen($v['nama'], 'UTF-8') < 3 || mb_strlen($v['nama'], 'UTF-8') > 50 || preg_match('/[\x00-\x1F<>"]/', $v['nama'])) { $galat = 'Nama admin 3 sampai 50 karakter tanpa tanda < > ".'; break; }
		if (strlen($pw) < 8) { $galat = 'Kata sandi admin minimal 8 karakter.'; break; }
		if ($pw !== $pw2) { $galat = 'Konfirmasi kata sandi tidak sama.'; break; }
		foreach (array('app' => 20, 'institusi' => 80, 'unit' => 60) as $k => $mx) {
			if ($v[$k] === '' || mb_strlen($v[$k], 'UTF-8') > $mx || preg_match('/[\x00-\x1F<>"\\\\]/', $v[$k])) { $galat = 'Nama aplikasi, institusi, dan unit wajib diisi (tanpa < > " \\).'; break 2; }
		}
		$m = @new mysqli($v['host'], $v['dbuser'], $dbpass);
		if (!$m || $m->connect_errno) { $galat = 'Tidak bisa terhubung ke MySQL: ' . ($m ? $m->connect_error : 'gagal') . '. Periksa host, pengguna, dan kata sandi.'; break; }
		$m->set_charset('utf8mb4');
		$dn = '`' . $v['dbname'] . '`';
		if (!$m->query("CREATE DATABASE IF NOT EXISTS $dn DEFAULT CHARACTER SET utf8mb4") || !$m->select_db($v['dbname'])) { $galat = 'Database tidak bisa dibuat atau dipilih: ' . $m->error; break; }
		$ada = $m->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('pegawai','dosen','pengajuan')");
		if ($ada && (int) $ada->fetch_row()[0] > 0) { $galat = 'Database "' . $v['dbname'] . '" sudah berisi tabel SIPAK. Pilih nama database lain atau kosongkan dulu.'; break; }
		$jalan = function ($file) use ($m, $root) {
			$sql = file_get_contents($root . '/install/' . $file);
			if ($sql === false || !$m->multi_query($sql)) { return $m->error ?: 'berkas ' . $file . ' tidak terbaca'; }
			do { if ($r = $m->store_result()) { $r->free(); } } while ($m->more_results() && $m->next_result());
			return $m->errno ? $m->error : '';
		};
		$er = $jalan('schema.sql'); if ($er !== '') { $galat = 'Gagal membuat tabel: ' . $er; break; }
		if ($demo && is_file($root . '/install/demo.sql')) {
			$er = $jalan('demo.sql'); if ($er !== '') { $galat = 'Gagal memuat data contoh: ' . $er; break; }
			$h = password_hash('12345', PASSWORD_DEFAULT);
			foreach (array('dosen', 'pegawai', 'penilai') as $t) { $m->query("UPDATE `$t` SET `password_$t` = '" . $m->real_escape_string($h) . "'"); }
		}
		$st = $m->prepare('INSERT INTO pegawai (nip_pegawai, password_pegawai, nama_pegawai, unit_kerja_pegawai, alamat_pegawai, telepon_pegawai) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password_pegawai = VALUES(password_pegawai), nama_pegawai = VALUES(nama_pegawai)');
		$hash = password_hash($pw, PASSWORD_DEFAULT); $strip = '-';
		$st->bind_param('ssssss', $v['nip'], $hash, $v['nama'], $v['unit'], $strip, $strip);
		if (!$st->execute()) { $galat = 'Gagal membuat akun admin: ' . $st->error; break; }
		$ps = $m->prepare('INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)');
		foreach (array('app', 'institusi', 'unit') as $k) { $ps->bind_param('ss', $k, $v[$k]); $ps->execute(); }
		$isi = "<?php\n// Dibuat oleh install.php pada " . date('c') . ". Jaga kerahasiaannya.\nreturn " . var_export(array('host' => $v['host'], 'user' => $v['dbuser'], 'pass' => $dbpass, 'name' => $v['dbname']), true) . ";\n";
		if (file_put_contents($kfg, $isi, LOCK_EX) === false) { $galat = 'Database sudah siap, tetapi koneksi/config.php tidak bisa ditulis. Periksa izin folder koneksi/ lalu ulangi dengan nama database baru.'; break; }
		@chmod($kfg, 0640);
		@file_put_contents($lock, date('c') . "\n");
		$_SESSION = array();
		halaman('Pemasangan selesai', '<h1>Pemasangan selesai</h1><p class="m">Database, tabel, dan akun admin sudah dibuat. Halaman pemasangan sekarang terkunci.</p><ul class="k"><li><span>Akun admin (peran Pegawai)</span><b>' . e($v['nip']) . '</b></li><li><span>Database</span><b>' . e($v['dbname']) . '</b></li><li><span>Data contoh</span><b>' . ($demo ? 'dimuat (sandi akun contoh: 12345)' : 'tidak dimuat') . '</b></li></ul><a class="t" href="pegawai/pegawai_login.php">Masuk sebagai admin</a><div class="w">Disarankan menghapus berkas install.php dan folder install/ dari server produksi setelah pemasangan.' . ($demo ? ' Hapus akun contoh sebelum dipakai sungguhan.' : '') . '</div>');
	} while (false);
}

ob_start();
?>
<h1>Pemasangan SIPAK</h1>
<p class="m">Sistem Informasi Kenaikan Jabatan Dosen. Isi data di bawah, aplikasi siap dipakai dalam satu langkah.</p>
<h2>Pemeriksaan lingkungan</h2>
<ul class="k"><?php foreach ($cek as $c) : ?><li><span><?php echo e($c[0]); ?></span><span class="<?php echo $c[1] ? 'ok' : 'no'; ?>"><?php echo $c[1] ? 'Siap' : 'Perlu diperbaiki'; ?></span></li><?php endforeach; ?></ul>
<?php if ($galat !== '') : ?><p class="err" role="alert" style="margin-top:18px"><?php echo e($galat); ?></p><?php endif; ?>
<form method="post" autocomplete="off">
<input type="hidden" name="t" value="<?php echo e($_SESSION['t']); ?>">
<h2>Database MySQL</h2>
<div class="row"><div><label for="host">Host</label><input type="text" id="host" name="host" value="<?php echo e($v['host']); ?>" required></div><div><label for="dbname">Nama database</label><input type="text" id="dbname" name="dbname" value="<?php echo e($v['dbname']); ?>" required><small>Dibuat otomatis bila belum ada.</small></div></div>
<div class="row"><div><label for="dbuser">Pengguna</label><input type="text" id="dbuser" name="dbuser" value="<?php echo e($v['dbuser']); ?>" required></div><div><label for="dbpass">Kata sandi</label><input type="password" id="dbpass" name="dbpass"><small>Kosong untuk XAMPP bawaan.</small></div></div>
<h2>Institusi</h2>
<div class="row"><div><label for="app">Nama aplikasi</label><input type="text" id="app" name="app" maxlength="20" value="<?php echo e($v['app']); ?>" required></div><div><label for="unit">Nama unit singkat</label><input type="text" id="unit" name="unit" maxlength="60" value="<?php echo e($v['unit']); ?>" placeholder="Fakultas Teknik" required></div></div>
<label for="institusi">Nama institusi lengkap</label><input type="text" id="institusi" name="institusi" maxlength="80" value="<?php echo e($v['institusi']); ?>" placeholder="Fakultas Teknik Universitas Contoh" required>
<h2>Akun admin pertama (peran Pegawai)</h2>
<div class="row"><div><label for="nip">NIP</label><input type="text" id="nip" name="nip" maxlength="18" value="<?php echo e($v['nip']); ?>" required></div><div><label for="nama">Nama lengkap</label><input type="text" id="nama" name="nama" maxlength="50" value="<?php echo e($v['nama']); ?>" required></div></div>
<div class="row"><div><label for="pw">Kata sandi</label><input type="password" id="pw" name="pw" minlength="8" required><small>Minimal 8 karakter.</small></div><div><label for="pw2">Ulangi kata sandi</label><input type="password" id="pw2" name="pw2" minlength="8" required></div></div>
<label class="chk" style="margin-top:18px"><input type="checkbox" name="demo" value="1"> <span>Muat data contoh (dosen, penilai, dan pengajuan) untuk mencoba atau demo. Jangan dicentang untuk pemakaian sungguhan.</span></label>
<button type="submit"<?php echo $siap ? '' : ' disabled'; ?>>Pasang sekarang</button>
</form>
<?php halaman('Pasang', ob_get_clean(), $galat !== '' ? 422 : 200);
