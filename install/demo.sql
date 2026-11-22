-- Data contoh SIPAK (opsional). Semua akun contoh memakai kata sandi 12345.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `dosen` (`nip_dosen`, `password_dosen`, `nama_dosen`, `unit_kerja_dosen`, `alamat_dosen`, `telepon_dosen`) VALUES
	('195812311986031025', '12345', 'MUHAMMAD ALFIAN', 'FAKULTAS TEKNIK', 'KOMP DIT BEK ANG NO B/7', '089635614733'),
	('196106011989031002', '12345', 'YAN MUHAMMAD', 'FAKULTAS TEKNIK', 'JALAN BEKISAR 1 SINGARAJA', '081338515114'),
	('197010122005011001', '12345', 'AL', 'FAKULTAS TEKNIK', 'LINGKUNGAN SANGKET, KEL./KEC. SUKASADA, KAB. BULELENG, KODE POST 81161', '081338724721'),
	('197603122003122001', '12345', 'FIAN', 'FAKULTAS TEKNIK', 'JALAN PAHLAWAN SIDOARJO', '087654223112'),
	('198503302003122002', '12345', 'AL-FIAN', 'FAKULTAS TEKNIK', 'JALAN KEMBANG BARAT SURABAYA', '081234621232');

INSERT INTO `pegawai` (`nip_pegawai`, `password_pegawai`, `nama_pegawai`, `unit_kerja_pegawai`, `alamat_pegawai`, `telepon_pegawai`) VALUES
	('199905042019102001', '12345', 'AL-FIAN', 'FAKULTAS TEKNIK', 'KOMP DIT BEK ANG NO B/7', '089635614733');

INSERT INTO `penilai` (`nip_penilai`, `password_penilai`, `nama_penilai`, `unit_kerja_penilai`, `rumpun_ilmu`, `alamat_penilai`, `telepon_penilai`) VALUES
	('195312101987021001', '12345', 'ALFIAN', 'FAKULTAS TEKNIK', 'TEKNIK ELEKTRO', 'KOMP. UNHAS TAMALANREA BLOK. GB/73', '081144422674'),
	('195912081986011003', '12345', 'MUH ALFIAN', 'FAKULTAS TEKNIK', 'TEKNIK INFORMATIKA', 'KOMP DIT BEK ANG NO B/7', '089635614733'),
	('196301161988101001', '12345', 'ALFIAN MUHAMMAD', 'FAKULTAS TEKNIK', 'TEKNIK MESIN', 'KOMP. UNHAS TAMALANREA BLOK. GB/61', '08114108070'),
	('196305151992031003', '12345', 'M. ALFIAN', 'FAKULTAS TEKNIK', 'TEKNIK INFORMATIKA', 'JL. SUNGAI SADDANG BARU LR. MUKMIN NO.14', '0811469002');

INSERT INTO `pengajuan` (`id_pengajuan`, `id_kum`, `nip_dosen`, `nip_pegawai`, `nip_penilai_1`, `nip_penilai_2`, `tanggal_pengajuan`, `jabatan_asal`, `tanggal_sk`, `jabatan_tujuan`, `dokumen_pendukung`, `status_pengajuan`, `total_pak_akhir`, `file_sk`) VALUES
	(11, 8, '198503302003122002', '', '', '', '2019-10-26', 'Asisten Ahli III/b', '2019-10-01', 'Guru Besar IV/d', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BELUM DIKIRIM', 8, ''),
	(12, 5, '198503302003122002', '', '199706122018032001', '195312101987021001', '2019-12-01', 'Asisten Ahli III/b', '2019-10-03', 'Lektor Kepala IV/a', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'PENILAIAN SELESAI', 516, ''),
	(16, 8, '197603122003122001', '', '', '', '2019-10-27', 'Lektor Kepala IV/c', '2019-10-10', 'Guru Besar IV/d', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XThkn', 'TELAH REVISI', 1018, ''),
	(17, 8, '197603122003122001', '', '195312101987021001', '199706122018032001', '2019-10-27', 'Lektor Kepala IV/c', '2019-10-10', 'Guru Besar IV/d', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XThknMBM', 'BERKAS DITERIMA', 867, ''),
	(18, 3, '197010122005011001', '', '195312101987021001', '195912081986011003', '2019-11-07', 'Asisten Ahli III/a', '2017-08-24', 'Lektor III/c', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0P', 'DITOLAK', 215, ''),
	(19, 5, '196106011989031002', '', '196305151992031003', '196301161988101001', '2019-11-08', 'Lektor III/c', '2016-07-12', 'Lektor Kepala IV/a', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XThknAD', 'BERKAS DITERIMA', 405, ''),
	(20, 2, '197603122003122001', '', '', '', '2019-11-11', 'Asisten Ahli III/a', '2017-04-01', 'Asisten Ahli III/b', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh0PNSZjhP4BDi8PY', 'TELAH REVISI', 1008, ''),
	(21, 2, '195812311986031025', '', '195312101987021001', '196301161988101001', '2019-11-15', 'Asisten Ahli III/a', '2019-11-02', 'Asisten Ahli III/b', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'DITERIMA', 215, 'https://drive.google.com/drive/my-drive'),
	(22, 2, '195812311986031025', '', '', '', '2019-11-15', 'Asisten Ahli III/a', '2019-10-31', 'Asisten Ahli III/b', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BERKAS DITERIMA', 200, ''),
	(23, 3, '195812311986031025', '', '195312101987021001', '195912081986011003', '2019-11-28', 'Asisten Ahli III/a', '2019-11-05', 'Lektor III/c', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PYsas', 'PENILAIAN SELESAI', 280, ''),
	(24, 5, '195812311986031025', '', '196305151992031003', '196301161988101001', '2019-11-28', 'Lektor III/c', '2019-11-28', 'Lektor Kepala IV/a', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BERKAS DITERIMA', 400, ''),
	(25, 7, '195812311986031025', '', '195912081986011003', '196509251990022001', '2019-11-29', 'Asisten Ahli III/b', '2019-11-27', 'Lektor Kepala IV/c', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BERKAS DITERIMA', 710, ''),
	(26, 7, '195812311986031025', '', '', '', '2019-11-29', 'Asisten Ahli III/b', '2019-11-27', 'Lektor Kepala IV/c', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BELUM DIKIRIM', 350, ''),
	(27, 5, '195812311986031025', '', '', '', '2019-11-29', 'Asisten Ahli III/b', '2019-11-27', 'Lektor Kepala IV/a', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0PNSZjhP4BDi8PY', 'BERKAS DITOLAK', 400, ''),
	(28, 2, '198503302003122002', '', '195912081986011003', '196301161988101001', '2019-12-06', 'Asisten Ahli III/a', '2017-06-20', 'Asisten Ahli III/b', 'https://drive.google.com/folderview?id=1qsBJ7J6cCO5XTh93S0NSZjhP4BDi8PY', 'DITERIMA', 153, 'drive.com'),
	(29, 7, '195812311986031025', '', '', '', '2020-09-04', 'Lektor III/c', '2020-08-12', 'Lektor Kepala IV/c', 'http://google.drive', 'TERKIRIM', 816, ''),
	(30, 3, '195812311986031025', '', '', '', '2020-09-06', 'Asisten Ahli III/b', '2020-11-22', 'Lektor III/c', 'https://google.drive', 'BELUM DIKIRIM', 0, ''),
	(31, 3, '195812311986031025', '', '', '', '2020-09-07', 'Asisten Ahli III/a', '2020-03-12', 'Lektor III/c', 'http://google.drive', 'TERKIRIM', 203, ''),
	(32, 5, '195812311986031025', '', '', '', '2020-09-07', 'Asisten Ahli III/b', '2020-04-12', 'Lektor Kepala IV/a', 'http://google.drive', 'BELUM DIKIRIM', 3, ''),
	(33, 2, '195812311986031025', '', '', '', '2020-10-07', 'Asisten Ahli III/a', '2019-12-23', 'Asisten Ahli III/b', 'https://google.drive', 'TERKIRIM', 200, '');

INSERT INTO `berkas_pak` (`id_berkas`, `id_pengajuan`, `kode_rubrik`, `bukti_berkas`, `status_berkas`, `volume`, `poin_1`, `poin_2`, `poin_akhir`, `catatan_berkas`) VALUES
	(11, 16, 8, 'drive.google.com/asASADureroerenmnJIQWQAYU', 'VALID', 2, 8, 8, 8, ''),
	(12, 16, 28, 'drive.google.com/asASADureroerenmnJIQWQE/aadadadAS', 'VALID', 2, 10, 10, 10, ''),
	(13, 17, 6, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 2, 3, 2.5, ''),
	(14, 17, 79, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 80, 80, 80, ''),
	(15, 12, 1, 'drive.google.com/asASADureroerenmnJIQWQE/aadadadnjjbadf', 'VALID', 1, 200, 210, 205, ''),
	(16, 12, 3, 'drive.google.com/asASADureroerenmnJIQWQE/aadadadnjjb', 'VALID', 1, 2, 3, 2.5, ''),
	(17, 12, 6, 'https://drive.google.com/adad/erefcdsadae/SADAEmnasas', 'VALID', 4, 12, 4, 8, ''),
	(19, 11, 8, 'https://drive.google.com/adad/erefcdsadae/SADAE', '', 2, 8, 8, 8, ''),
	(23, 18, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 210, 220, 215, ''),
	(24, 17, 77, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 120, 120, 120, ''),
	(25, 17, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 200, 200, 200, ''),
	(26, 17, 78, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 100, 100, 100, ''),
	(27, 17, 9, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 4, 6, 4, ''),
	(28, 17, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 300, 300, 300, ''),
	(29, 17, 80, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 60, 60, 60, ''),
	(30, 19, 53, 'https://drive.google.com/adad/erefcdsadae/SADAEAAAaw', 'VALID', 2, 80, 80, 80, ''),
	(31, 19, 54, 'https://drive.google.com/adad/erefcdsadae/SADAEqwa', 'VALID', 3, 90, 90, 90, ''),
	(32, 19, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 200, 200, 200, ''),
	(33, 19, 27, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 20, 20, 20, ''),
	(34, 19, 75, 'https://drive.google.com/adad/erefcdsadae/SADAEwq', 'VALID', 1, 15, 15, 15, ''),
	(35, 16, 1, 'https://drive.google.com/adad/erefcdsadaKAR', 'TELAH REVISI', 5, 1000, 1000, 1000, ''),
	(36, 20, 3, 'https://drive.google.com/adad/erefcdsadae/SADAEa', 'TELAH REVISI', 1, 3, 3, 3, ''),
	(37, 20, 4, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 1, 1, 1, ''),
	(38, 20, 8, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 4, 4, 4, ''),
	(39, 20, 1, 'https://drive.google.com/adad/erefcdsadae/SADAEsasassdds', 'TELAH REVISI', 5, 1000, 1000, 1000, ''),
	(40, 21, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 220, 210, 215, ''),
	(41, 22, 1, 'https://drive.google.com/adad/erefcdsadae/SAD', 'TELAH REVISI', 1, 200, 200, 200, ''),
	(42, 23, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 260, 300, 280, ''),
	(43, 24, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 200, 200, 200, ''),
	(44, 24, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 150, 150, 150, ''),
	(45, 24, 78, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 50, 50, 50, ''),
	(46, 25, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 200, 200, 200, ''),
	(47, 25, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 150, 150, 150, ''),
	(48, 25, 77, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 3, 180, 180, 180, ''),
	(49, 25, 78, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 100, 100, 100, ''),
	(50, 25, 53, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 80, 80, 80, ''),
	(51, 26, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', '', 1, 200, 200, 200, ''),
	(52, 26, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', '', 1, 150, 150, 150, ''),
	(53, 27, 1, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'TELAH REVISI', 2, 400, 400, 400, 'File tidak asli'),
	(54, 12, 2, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 300, 300, 300, ''),
	(55, 28, 27, 'https://drive.google.com/adad/erefcdsadae/SADAEa', 'VALID', 2, 42, 40, 41, ''),
	(56, 28, 42, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 28, 30, 29, ''),
	(57, 28, 19, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 12, 12, 12, ''),
	(58, 28, 4, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 1, 0.5, 0.5, 0.5, ''),
	(59, 28, 50, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 2, 40, 40, 40, ''),
	(60, 28, 117, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 3, 15, 15, 15, ''),
	(61, 28, 148, 'https://drive.google.com/adad/erefcdsadae/SADAE', 'VALID', 3, 15, 15, 15, ''),
	(62, 29, 1, 'http://google.drive', '', 1, 200, 200, 200, ''),
	(63, 29, 2, 'http://google.drive', '', 2, 300, 300, 300, ''),
	(64, 29, 15, 'http://google.drive', '', 22, 176, 176, 176, ''),
	(65, 29, 27, 'http://google.drive', '', 3, 60, 60, 60, ''),
	(66, 29, 49, 'http://google.drive', '', 2, 80, 80, 80, ''),
	(67, 31, 1, 'http://google.drive', '', 1, 200, 200, 200, ''),
	(68, 31, 3, 'http://google.drive', '', 1, 3, 3, 3, ''),
	(69, 32, 70, 'http://google.drive', '', 1, 3, 3, 3, ''),
	(70, 33, 1, 'https://google.drive', '', 1, 200, 200, 200, '');

SET FOREIGN_KEY_CHECKS=1;
