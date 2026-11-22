<?php
$nx_role    = 'pegawai';
$nx_name    = isset($nama_pegawai) ? $nama_pegawai : '';
$nx_profile = 'pegawai_profile.php?nip_pegawai=' . rawurlencode(isset($nip_pegawai) ? $nip_pegawai : '');
$nx_logout  = 'pegawai_logout.php';
include __DIR__ . '/nx_navbar.php';
