<?php
	/* Backup seluruh database sebagai berkas SQL (tanpa mysqldump). Hanya POST + CSRF (lewat penjaga). Trigger dibuat ulang otomatis oleh aplikasi. */
	include_once("../koneksi/conn.php");
	@set_time_limit(0);
	$tabel = array();
	foreach ((array) $koneksi->GetAll("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'") as $r) { $tabel[] = $r[0]; }
	while (ob_get_level()) { ob_end_clean(); }
	header('Content-Type: application/sql; charset=UTF-8');
	header('Content-Disposition: attachment; filename="sipak-backup-' . date('Ymd-His') . '.sql"');
	header('Cache-Control: no-store');
	echo "-- Backup SIPAK, " . date('Y-m-d H:i:s') . "\n-- Berisi data sensitif (kata sandi ter-hash). Simpan di tempat aman.\n";
	echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";
	foreach ($tabel as $t) {
		$q = '`' . str_replace('`', '``', $t) . '`';
		$c = $koneksi->GetRow('SHOW CREATE TABLE ' . $q);
		echo "DROP TABLE IF EXISTS $q;\n" . $c[1] . ";\n\n";
		$rs = $koneksi->Execute('SELECT * FROM ' . $q);
		if (!$rs || $rs->RecordCount() === 0) { continue; }
		$kolom = array();
		for ($i = 0; $i < $rs->FieldCount(); $i++) { $fld = $rs->FetchField($i); $kolom[] = '`' . str_replace('`', '``', $fld->name) . '`'; }
		$buf = array();
		while (!$rs->EOF) {
			$vals = array();
			foreach ($rs->fields as $i => $v) {
				if (!is_int($i)) { continue; }
				$vals[] = $v === null ? 'NULL' : $koneksi->qstr((string) $v);
			}
			$buf[] = '(' . implode(',', $vals) . ')';
			if (count($buf) >= 100) { echo "INSERT INTO $q (" . implode(',', $kolom) . ") VALUES\n" . implode(",\n", $buf) . ";\n"; $buf = array(); }
			$rs->MoveNext();
		}
		if ($buf) { echo "INSERT INTO $q (" . implode(',', $kolom) . ") VALUES\n" . implode(",\n", $buf) . ";\n"; }
		echo "\n";
	}
	echo "SET FOREIGN_KEY_CHECKS=1;\n";
	exit;
