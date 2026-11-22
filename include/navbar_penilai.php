<?php
$nx_role    = 'penilai';
$nx_name    = isset($nama_penilai) ? $nama_penilai : '';
$nx_profile = 'penilai_profile.php?nip_penilai=' . rawurlencode(isset($nip_penilai) ? $nip_penilai : '');
$nx_logout  = 'penilai_logout.php';
include __DIR__ . '/nx_navbar.php';
