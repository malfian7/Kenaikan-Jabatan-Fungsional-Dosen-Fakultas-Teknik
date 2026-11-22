<?php
$nx_role    = 'dosen';
$nx_name    = isset($nama_dosen) ? $nama_dosen : '';
$nx_profile = 'dosen_profile.php?nip_dosen=' . rawurlencode(isset($nip_dosen) ? $nip_dosen : '');
$nx_logout  = 'dosen_logout.php';
include __DIR__ . '/nx_navbar.php';
