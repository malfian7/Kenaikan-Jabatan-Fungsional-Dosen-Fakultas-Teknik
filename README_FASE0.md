# SIPAK — Pembaruan Fase 0 (Keamanan) + UI/Animasi Nexus

## Cara memasang
1. Cadangkan folder lama `C:\xampp\htdocs\sipak`, lalu ganti isinya dengan folder ini. Database `sipak` TIDAK perlu diimpor ulang.
2. Pastikan PHP 7.3+ dan MySQL/MariaDB berjalan di XAMPP, lalu buka `http://localhost/sipak`.
3. Login pertama setiap peran akan otomatis memperlebar kolom password dan meng-hash semua password lama (aman, sekali jalan). Akun contoh: password `12345`.

## Yang berubah (keamanan)
- Login memakai session (ID diganti saat login), logout sungguhan (`*_logout.php`), batas 5 percobaan gagal / 10 menit.
- Password disimpan sebagai hash (`password_hash`); halaman profil tidak lagi menampilkan password.
- Semua query tulis memakai prepared statement; parameter URL divalidasi; setiap halaman memeriksa kepemilikan data (dosen hanya data sendiri, penilai hanya pengajuan yang ditugaskan).
- Aksi ubah/hapus/kirim wajib POST + token CSRF (tombol hapus kini berupa form dengan dialog konfirmasi).
- Semua keluaran HTML di-escape; header keamanan dasar dikirim; kesalahan server tidak membocorkan detail.
- Logika penilaian dikunci: nilai penilai dibatasi poin rubrik × volume, hanya penilai yang ditugaskan, hanya saat status BERKAS DITERIMA.

## Yang berubah (tampilan)
- Tema Nexus tetap; ditambah toast notifikasi, dialog konfirmasi yang tumbuh dari tombol, bilah progres navigasi, validasi form dengan getar halus dan pesan di bawah kolom, kartu muncul saat di-scroll. Semua menghormati `prefers-reduced-motion`.

## Perlu Anda periksa
- Beberapa berkas aksi di repo asli berukuran 0 byte (kosong). Saya menulis ulang: `dosen_berkas_edit_code`, `dosen_pengajuan_input_code`, `dosen_pengajuan_revisi_berkas_ubah_code`, `pegawai_berkas_tolak_code`, `pegawai_pengajuan_periksa_accept_control`, `pegawai_pengajuan_periksa_return_control`. Bandingkan dengan salinan lokal Anda bila ada.
- Perbaikan bug: halaman "tambah berkas" error 500 untuk pengajuan yang belum punya berkas (total poin kosong).
- Belum dikerjakan: header CSP, konversi charset ke utf8mb4, dan sebagian halaman masih memperbarui total poin saat dibuka (GET).

## Fase 1: Cek kelayakan otomatis + syarat kenaikan jenjang (Kepmendiktisaintek 39/M/KEP/2026)
- Panel "Kelayakan Pengajuan" di halaman dosen (tambah berkas, kirim gagal) dan pegawai (periksa pengajuan): total angka kredit, jumlah berkas, minimal AK tiap unsur, berkas ditolak yang belum diperbaiki, dan lama sejak SK jabatan terakhir, lengkap dengan bar dan saran kekurangan.
- Syarat WAJIB (total AK dan minimal satu berkas) dikunci di server: tombol Kirim memakai aturan yang sama dengan panel.
- Minimal AK per unsur ditampilkan sebagai catatan, bukan penghalang; kepegawaian dan penilai yang memutuskan. Lama sejak SK hanya informasi karena batas minimalnya ditetapkan institusi.
- Aturan ada di satu berkas: `include/nx_kelayakan.php`.

### Tambahan: syarat kenaikan jenjang (dari juknis 39/2026)
- Berlaku bila jenjang berubah (mis. Lektor ke Lektor Kepala). Dicek: masa di jabatan sebelumnya (min. 2 tahun), masa dosen tetap (Profesor 10 tahun), pendidikan (Profesor S3), syarat khusus publikasi (Tabel 4-6: jenis jurnal, peringkat Scopus/Sinta, peran penulis), serta pernyataan SKP baik, uji kompetensi, dan sertifikat pendidik.
- Dosen mengisi publikasi di kartu "Publikasi Syarat Khusus" pada halaman pengajuan; profil dosen kini punya pendidikan terakhir, status, tanggal diangkat dosen tetap, AK kumulatif SK terakhir (dihitung bersama berkas), dan pernyataan.
- Kepegawaian mengatur parameter di menu "Aturan Jabatan" (tabel `aturan_jabatan`). Perubahan ditandai "Disesuaikan institusi".
- Tabel dan kolom baru dibuat otomatis saat login/sesi pertama setelah pembaruan.

### Yang belum bisa diverifikasi dari juknis (silakan cek)
- Pembagian AK per unsur A-D dan syarat pendidikan Lektor/Lektor Kepala tidak ditemukan di teks yang bisa saya baca. Pembagian per unsur tetap memakai tabel `syarat_kum` bawaan SIPAK (proporsi penelitian 35/40/45% sudah sesuai) dan syarat pendidikan Lektor/Lektor Kepala dikosongkan.
- Rubrik penilaian kegiatan (tabel `rubrik`) belum diganti ke Tabel 2 juknis; baru nilai acuan publikasi yang ditampilkan.
- Syarat khusus tambahan Profesor (hibah, bimbingan doktor, dll.) hanya diingatkan, diperiksa kepegawaian.
- AK Integrasi/Penyetaraan/Konversi diwakili satu angka "AK kumulatif SK terakhir" di profil dosen.

### Rubrik penelitian juknis 39/2026 (Tabel 2 dan 3)
- 49 butir baru (kode 152-200): buku, book chapter, jurnal Scopus Q1-Q4 (40/38/35/33), jurnal Sinta (25/20/15), prosiding, paten (60/50/40/30/20), HKI, kebijakan, karya seni, dan lainnya. Rubrik penelitian lama (kode 49-103) dinonaktifkan dari pemilihan, tetapi berkas lama tetap utuh.
- Rubrik pendidikan, pengabdian, dan penunjang tidak berubah: juknis tidak memuat tabel untuk unsur itu (dihitung lewat konversi SKP).
- Nilai diekstrak dari teks juknis lewat pembaca otomatis dan sebagian kolom (kuartil prosiding, batas pengakuan per tahun) tidak terbaca jelas. WAJIB dicocokkan dengan PDF resmi sebelum dipakai produksi; berkas daftar ada di `include/nx_rubrik_2026.php`. Batas "maksimal per tahun" dan "maksimal per jenjang" ditulis di teks kegiatan, belum dikunci di sistem.
- Form berkas penelitian punya "Pembagian angka kredit antarpenulis" (100/60/50/40 atau persen sendiri). Poin = poin rubrik x volume x bagian; batas nilai penilai ikut memakai bagian itu.

## Fase 2: Alur internal dan laporan
- **Riwayat status otomatis**: tabel `riwayat_status` diisi trigger database (`nx_pengajuan_ai_v1`, `nx_pengajuan_au_v1`) sehingga setiap perubahan status, penugasan penilai, dan penilai selesai tercatat dengan waktu dan pelakunya, apa pun halaman yang mengubahnya. Pelaku diambil dari variabel sesi MySQL `@nx_actor` yang diisi penjaga akses di tiap permintaan (`peran:nip`). Pengajuan lama mendapat satu baris "Status awal" saat fitur dipasang. Panel "Riwayat Status" tampil di halaman detail dosen dan pegawai.
- **Notifikasi dalam aplikasi**: tabel `notifikasi` juga diisi trigger. Pegawai diberi tahu saat pengajuan terkirim, direvisi, atau selesai dinilai; dosen saat berkas ditolak/diterima dan saat keputusan terbit; penilai saat ditugaskan. Lonceng di topbar menampilkan jumlah belum dibaca; tiap peran hanya melihat miliknya. Menandai dibaca lewat POST + CSRF (`{peran}_notif_code.php`) dan tidak bisa menyentuh notifikasi peran lain.
- **Ekspor CSV** (pegawai): tombol "Ekspor CSV" di Data Pengajuan dan beranda. Berkas memakai pemisah titik koma dan BOM UTF-8 (langsung rapi di Excel Indonesia). Nilai yang diawali `= + - @` diberi awalan `'` untuk mencegah injeksi rumus.
- **Lembar usulan cetak**: tombol "Cetak lembar usulan" di panel riwayat (dosen dan pegawai). Halaman memuat data pengusul, usulan, pemeriksaan kelayakan, berkas, publikasi, riwayat, dan kolom tanda tangan. Pakai "Cetak / Simpan PDF" lalu pilih "Simpan sebagai PDF". Dosen hanya bisa membuka lembar miliknya.
- **Zona waktu**: PHP dan sesi MySQL dipaksa WIB (Asia/Jakarta) agar jam riwayat dan jam cetak konsisten.
- Trigger dibuat otomatis saat login pertama setelah pembaruan. Akun MySQL aplikasi harus punya hak `TRIGGER` (root XAMPP sudah punya). Bila tidak, aplikasi tetap jalan tanpa riwayat/notifikasi dan mencatat peringatan ke log error PHP.
- Belum ada: notifikasi email/WhatsApp, filter ekspor per status, lembar cetak untuk penilai.

## Fase 3: Pengerasan, pemasangan, dan pengaturan institusi
### Pengerasan keamanan
- **Content-Security-Policy**: skrip inline hanya jalan bila memuat nonce per permintaan (`nx_nonce()`), jadi skrip yang disuntikkan penyerang tidak dieksekusi. Gaya inline masih diizinkan (ada 38 atribut `style` lama). Tanpa CDN: semua aset lokal. Juga `frame-ancestors 'self'`, `object-src 'none'`, `form-action 'self'`. Saat menambah skrip inline baru tulis `<script nonce="<?php echo nx_nonce(); ?>">`.
- **utf8mb4**: semua tabel dikonversi otomatis saat login pertama (kunci asing dilepas lalu dipasang ulang dengan aturan yang sama).
- **Total poin**: kolom `pengajuan.total_pak_akhir` kini dijaga trigger pada `berkas_pak` (insert, update, delete). Tidak ada lagi UPDATE saat halaman dibuka lewat GET.
### Pemasangan
- **install.php**: pemeriksaan lingkungan, pembuatan database dan tabel dasar, akun admin pegawai pertama (kata sandi minimal 8 karakter, di-hash), nama institusi, opsi data contoh (sandi akun contoh `12345`). Menulis `koneksi/config.php` dan `koneksi/terpasang.lock`; setelah itu halaman menolak semua permintaan (403). Database yang sudah berisi tabel SIPAK ditolak. Instalasi lama (database `sipak` sudah ada) otomatis dianggap terpasang.
- Tanpa config dan tanpa database, semua halaman mengarah ke installer. `koneksi/conn.php` membaca `koneksi/config.php` bila ada (bawaan XAMPP bila tidak).
- Untuk produksi: hapus `install.php` dan folder `install/` setelah terpasang. Pastikan server mengizinkan `.htaccess` (folder `install/` dan `koneksi/` ditutup dari akses web).
### Pengaturan institusi dan backup (menu "Pengaturan" untuk pegawai)
- Nama aplikasi, nama institusi, nama unit, jabatan dan nama penandatangan, serta logo (PNG/JPG maksimal 1 MB, diperiksa isinya, nama file diacak, logo lama dihapus). Tampil di halaman depan, masuk, sidebar, dan lembar usulan.
- Backup database sebagai berkas `.sql` lewat tombol (POST + CSRF), tanpa bergantung pada `mysqldump`. Pulihkan lewat phpMyAdmin. Berkas berisi hash kata sandi, simpan di tempat aman.
- Teks sejarah di `about.php` masih khusus UHAMKA; ganti sesuai institusi Anda.

### Statistik dan panduan (Fase 3, lanjutan)
- **Statistik** (menu pegawai): kartu ringkasan, pengajuan per bulan (12 bulan terakhir berdasarkan data, grafik SVG dengan teks alternatif), lama rata-rata tiap tahap (pemeriksaan, penilaian, keputusan, total) dari `riwayat_status`, jumlah per jabatan tujuan, beban tiap penilai, dan lima antrean pemeriksaan terlama. Lama proses hanya mencakup pengajuan yang tercatat sejak fitur riwayat dipasang; pengajuan lama tidak ikut agar rata-rata tidak menyesatkan.
- **Panduan** per peran (menu pengguna di pojok kanan atas, "Panduan"): langkah kerja, arti status, dan pertanyaan umum. Halaman bisa dicetak atau disimpan sebagai PDF. Isi ada di `include/nx_panduan.php`; ubah teksnya bila alur institusi berbeda.
