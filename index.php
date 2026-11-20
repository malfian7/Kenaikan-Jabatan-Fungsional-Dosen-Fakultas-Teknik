<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Sistem Informasi Kenaikan Jabatan Dosen · Fakultas Teknik UHAMKA</title>
    <meta name="description" content="Simulasikan angka kredit dan ajukan kenaikan jabatan fungsional dosen Fakultas Teknik UHAMKA secara daring.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/FT.png">

    <link href="asset/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="asset/css/nexus.css" rel="stylesheet">
    <link href="asset/css/nexus-landing.css" rel="stylesheet">
</head>

<body class="nx-site">
    <?php $nx_here = 'beranda'; include "include/nx_site_header.php"; ?>

    <main>
        <!-- Hero -->
        <section class="nx-hero">
            <div class="nx-wrap">
                <div>
                    <span class="nx-pill"><i class="fas fa-graduation-cap" aria-hidden="true"></i>Fakultas Teknik UHAMKA</span>
                    <h1>Kenaikan jabatan dosen, <em>lebih terarah.</em></h1>
                    <p class="nx-lead">Selamat datang di website Kenaikan Jabatan Dosen Fakultas Teknik UHAMKA. Simulasikan angka kredit, unggah berkas, dan pantau setiap tahap pengajuan Anda di satu tempat.</p>
                    <div class="nx-hero-cta">
                        <a class="btn btn-primary" href="#masuk">Masuk sesuai peran<i class="fas fa-arrow-down" aria-hidden="true"></i></a>
                        <a class="btn nx-btn-plain" href="https://drive.google.com/file/d/1kug1r_n1FW4VZ9Sr0mp_bFHxMhK8_3fE/view"><i class="fas fa-book-open" aria-hidden="true"></i>Download User Guide</a>
                    </div>
                    <ul class="nx-hero-meta">
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i>Simulasi angka kredit</li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i>Pelacakan status</li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i>Revisi berkas daring</li>
                    </ul>
                </div>

                <div class="nx-preview" aria-hidden="true">
                    <div class="nx-preview-top"><strong>Tangga jabatan fungsional</strong><span>Contoh tampilan</span></div>
                    <div class="nx-ladder">
                        <div class="nx-rung is-done"><span class="nx-rung-dot">1</span><span class="nx-rung-name">Asisten Ahli</span><span class="nx-rung-tag">&nbsp;</span></div>
                        <div class="nx-rung is-now"><span class="nx-rung-dot">2</span><span class="nx-rung-name">Lektor</span><span class="nx-rung-tag">Saat ini</span></div>
                        <div class="nx-rung is-next"><span class="nx-rung-dot">3</span><span class="nx-rung-name">Lektor Kepala</span><span class="nx-rung-tag">Tujuan</span></div>
                        <div class="nx-rung"><span class="nx-rung-dot">4</span><span class="nx-rung-name">Guru Besar</span><span class="nx-rung-tag">&nbsp;</span></div>
                    </div>
                    <div class="nx-mini-stats">
                        <div><b>1</b><span>Dalam proses</span></div>
                        <div><b>0</b><span>Perlu revisi</span></div>
                        <div><b>2</b><span>Selesai</span></div>
                    </div>
                    <div class="nx-preview-row">Pengajuan #12<span class="nx-badge nx-badge--stempel">Penilaian selesai</span></div>
                </div>
            </div>
        </section>

        <!-- Masuk sesuai peran -->
        <section class="nx-section" id="masuk">
            <div class="nx-wrap">
                <div class="nx-section-head">
                    <span class="nx-kicker">Mulai di sini</span>
                    <h2>Masuk sesuai peran Anda</h2>
                    <p>Setiap peran memiliki ruang kerja sendiri, dari pengajuan hingga penerbitan SK.</p>
                </div>
                <div class="nx-roles">
                    <a class="nx-role-card" href="dosen/dosen_login.php">
                        <span class="nx-role-ico"><i class="fas fa-user-graduate" aria-hidden="true"></i></span>
                        <h3>Pengajuan Jabatan</h3>
                        <p>Masuk sebagai dosen untuk mengajukan kenaikan jabatan dosen dan melihat hasil.</p>
                        <span class="nx-role-go">Masuk sebagai Dosen<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                    <a class="nx-role-card" href="penilai/penilai_login.php">
                        <span class="nx-role-ico"><i class="fas fa-calculator" aria-hidden="true"></i></span>
                        <h3>Penilaian Angka Kredit</h3>
                        <p>Masuk sebagai Tim Penilai Angka Kredit dosen untuk menilai berkas-berkas pengajuan.</p>
                        <span class="nx-role-go">Masuk sebagai Penilai<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                    <a class="nx-role-card" href="pegawai/pegawai_login.php">
                        <span class="nx-role-ico"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
                        <h3>Periksa Berkas</h3>
                        <p>Masuk sebagai Pegawai untuk melihat dan memberikan validasi terkait keaslian berkas pengajuan.</p>
                        <span class="nx-role-go">Masuk sebagai Pegawai<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Alur -->
        <section class="nx-section">
            <div class="nx-wrap">
                <div class="nx-section-head">
                    <span class="nx-kicker">Alur pengajuan</span>
                    <h2>Empat langkah menuju SK</h2>
                </div>
                <div class="nx-steps">
                    <div class="nx-step"><h3>Dosen mengajukan</h3><p>Isi data jabatan dan unggah berkas pendukung.</p></div>
                    <div class="nx-step"><h3>Pegawai memeriksa</h3><p>Keaslian berkas divalidasi; berkas tidak valid dikembalikan untuk direvisi.</p></div>
                    <div class="nx-step"><h3>Penilai menilai</h3><p>Penilai menentukan angka kredit berdasarkan rubrik.</p></div>
                    <div class="nx-step"><h3>Pegawai menyetujui</h3><p>Hasil disetujui dan SK kenaikan jabatan diterbitkan.</p></div>
                </div>
            </div>
        </section>

        <div class="nx-wrap">
            <div class="nx-band">
                <div>
                    <h2>Baru pertama kali memakai aplikasi ini?</h2>
                    <p>Download User Guide untuk panduan penggunaan langkah demi langkah.</p>
                </div>
                <a class="btn" href="https://drive.google.com/file/d/1kug1r_n1FW4VZ9Sr0mp_bFHxMhK8_3fE/view"><i class="fas fa-download" aria-hidden="true"></i>Download</a>
            </div>
        </div>
    </main>

    <?php include "include/nx_site_footer.php"; ?>
</body>

</html>
