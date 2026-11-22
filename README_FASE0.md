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
