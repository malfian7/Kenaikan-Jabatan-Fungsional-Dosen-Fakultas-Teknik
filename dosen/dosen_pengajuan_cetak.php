<?php
include_once("../koneksi/conn.php");
include_once("../include/nx_cetak.php");
nx_cetak_halaman($koneksi, $_GET['id_pengajuan']);
