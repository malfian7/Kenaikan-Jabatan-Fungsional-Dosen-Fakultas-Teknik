<?php include_once __DIR__ . '/koneksi/conn.php'; ?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Tentang Kami · Sistem Informasi Kenaikan Jabatan Dosen</title>
    <meta name="description" content="Sejarah singkat Universitas Muhammadiyah Prof. Dr. Hamka.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo nx_e(nx_logo('')); ?>">

    <link href="asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="asset/css/nexus.css" rel="stylesheet">
    <link href="asset/css/nexus-landing.css" rel="stylesheet">
</head>

<body class="nx-site">
    <?php $nx_here = 'tentang'; include "include/nx_site_header.php"; ?>

    <main>
        <div class="nx-wrap">
            <article class="nx-article" style="padding-top:40px">
                <div class="nx-article-card">
                    <h1>Tentang Kami</h1>
                    <h2>Sejarah Singkat Universitas Muhammadiyah Prof. Dr. Hamka</h2>
					<p>UHAMKA adalah pengembangan dari IKIP Muhammadiyah Jakarta, awalnya adalah Perguruan Tinggi Pendidikan Guru (PTPG), yang diresmikan pada 18 November 1957. Awalnya PTPG memiliki satu jenis Jurusan, yakni Jurusan Ilmu Mendidik, dengan mahasiswa 17 orang. Panitia Pendirian Perguruan Tinggi ini di ketuai oleh Asro Sastroatmodjo, sekretaris HS Prodjokusumo dan Anggotanya terdiri atas Semangun, Asnawi, H.Gozali Dunia, Doeraini dan Soeroto Karto Soedarmo.</p>
					<p>Perubahan bentuk ini diumumkan secara terbuka oleh Ketua PP Muhammadiyah, Dr. H.M Amien Rais, M.A dalam acara Wisuda Sarjana dan Lulusan Program Diploma IKIP Muhammadiyah Jakarta dan peresmian UHAMKA pada tanggal 11 Juni 1997, di Jakarta Convention Centre sekaligus mengukuhkan Rektor IKIP Muhammadiyah Jakarta, Drs. H. Qomari Anwar, M.A menjadi Rektor UHAMKA yang pertama.</p>
					<p>Penggunaan nama Prof. Dr. HAMKA sebagai nama Perguruan Tinggi Muhammadiyah ini berdasarkan persetujuan keluarga besar Buya HAMKA, karena tokoh ini memiliki spirit yang luar biasa dalam belajar mandiri (otodidak), tuntas dan berlangsung sepanjang hayat. Beliau adalah sosok multidimensi dalam beragam kepakaran, yaitu ulama yang intelektual, intelektual yang utama, seorang sastrawan yang piawai dan unik, sekaligus wartawan dan mubaligh Muhammadiyah yang ulung.</p>
                </div>
            </article>
        </div>
    </main>

    <?php include "include/nx_site_footer.php"; ?>
</body>

</html>
