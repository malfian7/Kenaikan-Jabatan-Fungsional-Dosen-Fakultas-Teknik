<?php
/*
 * SIPAK — lapisan keamanan terpusat (Fase 0).
 *
 * Dimuat otomatis oleh koneksi/conn.php, jadi semua halaman di dosen/, pegawai/
 * dan penilai/ terlindungi tanpa mengubah satu per satu. Halaman login memuatnya
 * langsung (include_once "../koneksi/guard.php").
 *
 * Yang dikerjakan:
 *  1. Session aman (HttpOnly, SameSite, strict mode) + batas waktu tidak aktif.
 *  2. Wajib login sesuai peran; NIP diambil dari session, bukan dari alamat (?nip_dosen=).
 *  3. Validasi parameter GET (hanya parameter yang dikenal, format ketat) + cek
 *     kepemilikan pengajuan/berkas (dosen hanya miliknya, penilai hanya yang ditugaskan).
 *  4. File *_code.php dan *_control.php hanya menerima POST + token CSRF.
 *  5. Token CSRF disisipkan otomatis ke semua <form method="post">.
 *  6. Pesan kilat (flash) yang tampil sebagai toast, header keamanan, halaman galat ramah.
 *
 * Kompatibel PHP 7.3+.
 */

if (defined('NX_GUARD_LOADED')) {
	return;
}
define('NX_GUARD_LOADED', true);

/* ------------------------------------------------------------------ util */

if (!function_exists('nx_e')) {
	function nx_e($s)
	{
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	}
}

function nx_roles()
{
	return array(
		'dosen'   => array('table' => 'dosen',   'nip' => 'nip_dosen',   'pass' => 'password_dosen',   'login' => 'dosen_login.php',   'home' => 'dosen_beranda.php'),
		'pegawai' => array('table' => 'pegawai', 'nip' => 'nip_pegawai', 'pass' => 'password_pegawai', 'login' => 'pegawai_login.php', 'home' => 'pegawai_beranda.php'),
		'penilai' => array('table' => 'penilai', 'nip' => 'nip_penilai', 'pass' => 'password_penilai', 'login' => 'penilai_login.php', 'home' => 'penilai_beranda.php'),
	);
}

function nx_db()
{
	if (empty($GLOBALS['koneksi'])) {
		throw new Exception('Koneksi database belum siap.');
	}
	return $GLOBALS['koneksi'];
}

function nx_session_boot()
{
	if (session_status() === PHP_SESSION_ACTIVE) {
		return;
	}
	$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	session_name('SIPAKSESS');
	ini_set('session.use_strict_mode', '1');
	ini_set('session.use_only_cookies', '1');
	if (PHP_VERSION_ID >= 70300) {
		session_set_cookie_params(array('lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax'));
	} else {
		session_set_cookie_params(0, '/; samesite=Lax', '', $https, true);
	}
	session_start();
}

/* Pesan kilat: ditampilkan sekali sebagai toast di halaman berikutnya. */
function nx_flash($type, $msg)
{
	nx_session_boot();
	$_SESSION['nx_flash'] = array('type' => $type, 'msg' => $msg);
}

function nx_redirect($url)
{
	header('Location: ' . $url);
	exit;
}

function nx_csrf_token()
{
	nx_session_boot();
	if (empty($_SESSION['nx_csrf'])) {
		$_SESSION['nx_csrf'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['nx_csrf'];
}

function nx_csrf_field()
{
	return '<input type="hidden" name="_csrf" value="' . nx_e(nx_csrf_token()) . '">';
}

function nx_input($key, $default = '')
{
	return (isset($_POST[$key]) && is_string($_POST[$key])) ? trim($_POST[$key]) : $default;
}

/* Halaman galat bergaya Nexus (mandiri, tanpa berkas luar agar tetap tampil saat DB mati). */
function nx_abort($code, $title, $message, $hint = '')
{
	while (ob_get_level() > 0) {
		@ob_end_clean();
	}
	if (!headers_sent()) {
		http_response_code($code);
		header('Content-Type: text/html; charset=utf-8');
		header('Cache-Control: no-store');
	}
	echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
		. '<title>' . nx_e($title) . ' · SIPAK</title><style>'
		. 'body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f3f4f9;font-family:"Plus Jakarta Sans",system-ui,-apple-system,Segoe UI,sans-serif;color:#1d1b3a}'
		. '.c{max-width:460px;margin:24px;padding:32px;background:#fff;border:1px solid #e4e5f0;border-radius:20px;box-shadow:0 1px 2px rgba(29,27,58,.04),0 12px 32px rgba(29,27,58,.08);animation:i .38s cubic-bezier(.23,1,.32,1) backwards}'
		. '@keyframes i{from{opacity:0;transform:translateY(8px) scale(.985)}}'
		. '.k{display:inline-block;padding:3px 10px;border-radius:99px;background:#eeebff;color:#5646e0;font-size:12px;font-weight:700;letter-spacing:.04em}'
		. 'h1{font-size:22px;margin:14px 0 8px}p{margin:0 0 10px;line-height:1.6;color:#4b4a6a}.h{font-size:13px;color:#6b6a8a}'
		. 'a{display:inline-block;margin-top:14px;padding:10px 18px;border-radius:10px;background:#5646e0;color:#fff;text-decoration:none;font-weight:600;transition:transform .16s cubic-bezier(.23,1,.32,1)}a:active{transform:scale(.97)}'
		. '@media(prefers-reduced-motion:reduce){.c{animation:none}}'
		. '</style></head><body><div class="c"><span class="k">' . (int) $code . '</span><h1>' . nx_e($title) . '</h1><p>' . nx_e($message) . '</p>'
		. ($hint !== '' ? '<p class="h">' . nx_e($hint) . '</p>' : '')
		. '<a href="javascript:history.length>1?history.back():location.assign(\'../index.php\')">Kembali</a></div></body></html>';
	exit;
}

/* Galat tak tertangani → halaman ramah; detail teknis hanya ke log server. */
function nx_exception_handler($e)
{
	error_log('[SIPAK] ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
	$msg = $e->getMessage();
	$hint = '';
	if (stripos($msg, "doesn't exist") !== false || stripos($msg, 'Unknown database') !== false) {
		$hint = 'Tabel atau database belum ada. Impor berkas sipak.sql ke database "sipak" lewat phpMyAdmin.';
	} elseif (stripos($msg, 'Connection refused') !== false || stripos($msg, 'Access denied') !== false || stripos($msg, 'No such file') !== false) {
		$hint = 'Pastikan MySQL di XAMPP sudah dinyalakan, lalu periksa pengaturan di koneksi/conn.php.';
	}
	nx_abort(500, 'Terjadi kesalahan', 'Permintaan tidak dapat diproses saat ini. Coba lagi sebentar lagi.', $hint);
}

/* ------------------------------------------------------ bantu aksi/validasi */

/* Alihkan dengan pesan: sukses atau galat (tampil sebagai toast di halaman tujuan). */
function nx_done($url, $type, $msg)
{
	nx_flash($type, $msg);
	nx_redirect($url);
}

/* Teks bebas: dipangkas dan dibatasi panjangnya. Penyaringan HTML dilakukan saat ditampilkan (nx_e). */
function nx_text($key, $max = 200)
{
	$v = nx_input($key);
	return function_exists('mb_substr') ? mb_substr($v, 0, $max, 'UTF-8') : substr($v, 0, $max);
}

/* Tautan bukti: tambahkan https:// bila tidak ada skema; kosong jika tidak valid. */
function nx_clean_link($s)
{
	$s = trim((string) $s);
	if ($s === '' || strlen($s) > 500 || preg_match('/\s/', $s)) {
		return '';
	}
	if (!preg_match('#^https?://#i', $s)) {
		$s = 'https://' . $s;
	}
	return filter_var($s, FILTER_VALIDATE_URL) ? $s : '';
}

function nx_valid_nip($s)
{
	return (bool) preg_match('/^[0-9]{8,18}$/', (string) $s);
}

function nx_valid_phone($s)
{
	return (bool) preg_match('/^[0-9+()\-\s]{5,20}$/', (string) $s);
}

/* Baca satu angka dari POST; null bila bukan angka. */
function nx_number($key)
{
	$v = isset($_POST[$key]) && is_string($_POST[$key]) ? str_replace(',', '.', trim($_POST[$key])) : '';
	return (is_numeric($v)) ? (float) $v : null;
}

/* ------------------------------------------------------- kata sandi/skema */

function nx_hash_password($plain)
{
	return password_hash($plain, PASSWORD_DEFAULT);
}

function nx_is_hashed($stored)
{
	$i = password_get_info((string) $stored);
	return !empty($i['algo']);
}

function nx_password_ok($plain, $stored)
{
	$stored = (string) $stored;
	if (nx_is_hashed($stored)) {
		return password_verify($plain, $stored);
	}
	return $stored !== '' && hash_equals($stored, (string) $plain);   // sisa data lama (teks biasa)
}

/*
 * Migrasi sekali jalan, aman diulang: lebarkan kolom password lalu hash semua
 * password yang masih teks biasa. Dipanggil saat proses login.
 */
function nx_ensure_schema()
{
	static $done = false;
	if ($done) {
		return;
	}
	$done = true;
	$db = nx_db();
	try {
		$rows = $db->GetAll("SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME IN ('password_dosen','password_pegawai','password_penilai')");
		foreach ((array) $rows as $r) {
			if ((int) $r[2] < 255) {
				$db->Execute('ALTER TABLE `' . $r[0] . '` MODIFY `' . $r[1] . '` VARCHAR(255) NOT NULL');
			}
		}
		foreach (nx_roles() as $cfg) {
			if (!(int) $db->GetOne('SELECT COUNT(*) FROM `' . $cfg['table'] . '` WHERE `' . $cfg['pass'] . "` NOT LIKE '$%'")) {
				continue;   // semua sudah ter-hash
			}
			$list = $db->GetAll('SELECT `' . $cfg['nip'] . '`, `' . $cfg['pass'] . '` FROM `' . $cfg['table'] . '`');
			foreach ((array) $list as $row) {
				if (!nx_is_hashed($row[1])) {
					$db->Execute('UPDATE `' . $cfg['table'] . '` SET `' . $cfg['pass'] . '` = ? WHERE `' . $cfg['nip'] . '` = ?', array(nx_hash_password($row[1]), $row[0]));
				}
			}
		}
	} catch (Exception $e) {
		error_log('[SIPAK] migrasi skema dilewati: ' . $e->getMessage());
	}
}

/* ---------------------------------------------------------- batas percobaan */

function nx_throttle_file($role, $nip)
{
	return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sipak_lg_' . sha1($role . '|' . strtolower($nip) . '|' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''));
}

/* Mengembalikan sisa detik jika diblokir, 0 jika boleh mencoba. */
function nx_throttle_wait($role, $nip)
{
	$f = nx_throttle_file($role, $nip);
	if (!is_file($f)) {
		return 0;
	}
	$d = json_decode((string) @file_get_contents($f), true);
	if (!is_array($d)) {
		return 0;
	}
	$left = 600 - (time() - (int) $d['t']);
	return ((int) $d['n'] >= 5 && $left > 0) ? $left : 0;
}

function nx_throttle_fail($role, $nip)
{
	$f = nx_throttle_file($role, $nip);
	$d = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null;
	$n = (is_array($d) && time() - (int) $d['t'] < 600) ? (int) $d['n'] + 1 : 1;
	@file_put_contents($f, json_encode(array('n' => $n, 't' => time())), LOCK_EX);
}

function nx_throttle_clear($role, $nip)
{
	@unlink(nx_throttle_file($role, $nip));
}

/* ------------------------------------------------------- penyisipan output */

function nx_output_filter($html)
{
	if (stripos($html, '</body>') === false && stripos($html, '<form') === false) {
		return $html;
	}
	$tok = nx_e(nx_csrf_token());
	$html = preg_replace('/(<form\b[^>]*\bmethod\s*=\s*["\']?post["\']?[^>]*>)/i', '$1<input type="hidden" name="_csrf" value="' . $tok . '">', $html);
	$html = preg_replace('/<head\b[^>]*>/i', '$0<meta name="csrf-token" content="' . $tok . '">', $html, 1);
	if (!empty($_SESSION['nx_flash']) && is_array($_SESSION['nx_flash'])) {
		$f = $_SESSION['nx_flash'];
		unset($_SESSION['nx_flash']);
		$el = '<div id="nx-flash" hidden data-type="' . nx_e($f['type']) . '" data-msg="' . nx_e($f['msg']) . '"></div>';
		if (stripos($html, '</body>') !== false) {
			$html = preg_replace('/<\/body>/i', $el . '</body>', $html, 1);
		} else {
			$html .= $el;
		}
	}
	return $html;
}

/* --------------------------------------------------------------- penjaga */

function nx_guard_run()
{
	static $ran = false;
	if ($ran) {
		return;
	}
	$ran = true;

	$script = isset($_SERVER['SCRIPT_FILENAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']) : '';
	$dir  = basename(dirname($script));
	$base = basename($script);
	$roles = nx_roles();

	nx_session_boot();
	if (!headers_sent()) {
		header('X-Frame-Options: SAMEORIGIN');
		header('X-Content-Type-Options: nosniff');
		header('Referrer-Policy: same-origin');
		header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
	}
	ob_start('nx_output_filter');

	if (!isset($roles[$dir])) {
		return;   // halaman publik di luar folder peran (index, about, register)
	}
	$role = $dir;
	$cfg  = $roles[$role];
	$public = (bool) preg_match('/_(login|login_gagal|login_code|logout)\.php$/', $base);

	if (!headers_sent()) {
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
	}

	/* Metode: aksi tulis hanya lewat POST. */
	$readOnly = array('pegawai_penilai_input_control.php');
	$isAction = (bool) preg_match('/(_code|_control|_hapus|_submit_nilai[12])\.php$/', $base) && !in_array($base, $readOnly, true);
	if ($isAction && $_SERVER['REQUEST_METHOD'] !== 'POST') {
		if (preg_match('/_login_code\.php$/', $base)) {
			nx_redirect($cfg['login']);
		}
		nx_abort(405, 'Metode tidak diizinkan', 'Aksi ini hanya dapat dijalankan dari tombol di dalam aplikasi.');
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sent = isset($_POST['_csrf']) && is_string($_POST['_csrf']) ? $_POST['_csrf'] : '';
		if (empty($_SESSION['nx_csrf']) || !hash_equals($_SESSION['nx_csrf'], $sent)) {
			nx_abort(419, 'Formulir kedaluwarsa', 'Sesi formulir sudah tidak berlaku. Muat ulang halaman, lalu coba lagi.');
		}
	}

	if ($public) {
		// Sudah login? Langsung ke beranda saat membuka halaman login.
		if (preg_match('/_login\.php$/', $base) && !empty($_SESSION['role']) && $_SESSION['role'] === $role && !empty($_SESSION['nip'])) {
			nx_redirect($cfg['home'] . '?' . $cfg['nip'] . '=' . rawurlencode($_SESSION['nip']));
		}
		return;
	}

	/* Wajib login sesuai peran. */
	$ok = !empty($_SESSION['role']) && $_SESSION['role'] === $role && !empty($_SESSION['nip']);
	if ($ok) {
		$now = time();
		if ($now - (isset($_SESSION['last']) ? (int) $_SESSION['last'] : $now) > 7200 || $now - (isset($_SESSION['login_at']) ? (int) $_SESSION['login_at'] : $now) > 28800) {
			$ok = false;
			$msg = 'Sesi Anda berakhir karena tidak aktif. Silakan masuk lagi.';
		} else {
			$_SESSION['last'] = $now;
		}
	}
	if (!$ok) {
		if (!isset($msg)) {
			$msg = 'Silakan masuk terlebih dahulu.';
		}
		if (!empty($_SESSION['role']) && $_SESSION['role'] === $role) {
			unset($_SESSION['role'], $_SESSION['nip'], $_SESSION['login_at'], $_SESSION['last']);   // sesi peran ini kedaluwarsa
		}
		nx_flash('info', $msg);
		nx_redirect($cfg['login']);
	}
	$me = $_SESSION['nip'];

	/* Parameter GET: hanya yang dikenal, dengan format ketat. */
	$rules = array(
		'id_pengajuan' => '/^[0-9]{1,10}$/',
		'id_berkas'    => '/^[0-9]{1,10}$/',
		'kode_rubrik'  => '/^[0-9]{1,10}$/',
		'id_kum'       => '/^[0-9]{1,10}$/',
		'nip_dosen'    => '/^[0-9A-Za-z]{1,25}$/',
		'nip_pegawai'  => '/^[0-9A-Za-z]{1,25}$/',
		'nip_penilai'  => '/^[0-9A-Za-z]{1,25}$/',
	);
	foreach (array_keys($_GET) as $k) {
		if (!isset($rules[$k])) {
			unset($_GET[$k], $_REQUEST[$k]);
			continue;
		}
		if (!is_string($_GET[$k]) || !preg_match($rules[$k], $_GET[$k])) {
			nx_abort(400, 'Permintaan tidak valid', 'Parameter alamat tidak sesuai format yang diizinkan.');
		}
	}
	$_GET[$cfg['nip']] = $me;      // identitas selalu dari session
	$_REQUEST[$cfg['nip']] = $me;

	/* Kepemilikan data. */
	$db = nx_db();
	if (isset($_GET['id_pengajuan'])) {
		$idp = $_GET['id_pengajuan'];
		if ($role === 'dosen') {
			$okp = $db->GetOne('SELECT 1 FROM pengajuan WHERE id_pengajuan = ? AND nip_dosen = ?', array($idp, $me));
		} elseif ($role === 'penilai') {
			$okp = $db->GetOne('SELECT 1 FROM pengajuan WHERE id_pengajuan = ? AND (nip_penilai_1 IN (?, ?) OR nip_penilai_2 IN (?, ?))', array($idp, $me, $me . 'V', $me, $me . 'V'));
		} else {
			$okp = $db->GetOne('SELECT 1 FROM pengajuan WHERE id_pengajuan = ?', array($idp));
		}
		if (!$okp) {
			nx_abort(403, 'Akses ditolak', 'Pengajuan ini tidak ditemukan atau bukan bagian dari akun Anda.');
		}
	}
	if (isset($_GET['id_berkas'])) {
		$b = $db->GetRow('SELECT b.id_pengajuan, p.nip_dosen, p.nip_penilai_1, p.nip_penilai_2 FROM berkas_pak b INNER JOIN pengajuan p ON p.id_pengajuan = b.id_pengajuan WHERE b.id_berkas = ?', array($_GET['id_berkas']));
		if ($b) {   // berkas yang belum ada (mis. saat membuat baru) dibiarkan lewat
			$mine = ($role === 'pegawai')
				|| ($role === 'dosen' && (string) $b[1] === (string) $me)
				|| ($role === 'penilai' && in_array((string) $me, array(rtrim((string) $b[2], 'V'), rtrim((string) $b[3], 'V')), true));
			$match = !isset($_GET['id_pengajuan']) || (string) $b[0] === (string) $_GET['id_pengajuan'];
			if (!$mine || !$match) {
				nx_abort(403, 'Akses ditolak', 'Berkas ini tidak ditemukan atau bukan bagian dari akun Anda.');
			}
		}
	}
}

set_exception_handler('nx_exception_handler');

/* Halaman login memuat berkas ini langsung; conn.php menjalankan penjaga setelah koneksi siap. */
if (!defined('NX_CONN_LOADING')) {
	nx_guard_run();
}
