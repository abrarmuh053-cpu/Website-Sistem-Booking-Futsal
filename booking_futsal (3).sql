-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Jun 2026 pada 04.01
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_futsal`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT 'default.png',
  `tgl_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `email`, `password`, `foto`, `tgl_daftar`) VALUES
(1, 'Administrator', 'abrarmuh053@gmail.com', '$2y$10$.wrFHujPRaIkkery4GfmKeAbYHm2aHffUt0.a0Ax3FzvAya0Ig/wC', 'default.png', '2026-06-05 09:35:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `tanggal_main` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_harga` decimal(10,0) NOT NULL,
  `status` enum('Pending','Dikonfirmasi','Dibatalkan','Selesai') NOT NULL DEFAULT 'Pending',
  `tgl_booking` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id_booking`, `id_user`, `id_lapangan`, `id_jadwal`, `tanggal_main`, `jam_mulai`, `jam_selesai`, `total_harga`, `status`, `tgl_booking`) VALUES
(1, 2, 2, 16, '2026-06-05', '08:00:00', '09:00:00', 150000, 'Selesai', '2026-06-05 10:38:15'),
(2, 3, 1, 12, '2026-06-09', '08:00:00', '09:00:00', 200000, 'Selesai', '2026-06-09 22:16:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` enum('Tersedia','Dibooking') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_lapangan`, `hari`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(1, 1, 'Senin', '08:00:00', '09:00:00', 'Tersedia'),
(2, 1, 'Senin', '09:00:00', '10:00:00', 'Tersedia'),
(3, 1, 'Senin', '10:00:00', '11:00:00', 'Tersedia'),
(4, 1, 'Senin', '15:00:00', '16:00:00', 'Tersedia'),
(5, 1, 'Senin', '16:00:00', '17:00:00', 'Tersedia'),
(6, 1, 'Senin', '19:00:00', '20:00:00', 'Tersedia'),
(7, 1, 'Senin', '20:00:00', '21:00:00', 'Tersedia'),
(8, 1, 'Selasa', '08:00:00', '09:00:00', 'Tersedia'),
(9, 1, 'Selasa', '09:00:00', '10:00:00', 'Tersedia'),
(10, 1, 'Selasa', '15:00:00', '16:00:00', 'Tersedia'),
(11, 1, 'Selasa', '19:00:00', '20:00:00', 'Tersedia'),
(12, 1, 'Rabu', '08:00:00', '09:00:00', 'Tersedia'),
(13, 1, 'Rabu', '10:00:00', '11:00:00', 'Tersedia'),
(14, 1, 'Rabu', '15:00:00', '16:00:00', 'Tersedia'),
(15, 1, 'Rabu', '19:00:00', '21:00:00', 'Tersedia'),
(16, 2, 'Senin', '08:00:00', '09:00:00', 'Tersedia'),
(17, 2, 'Senin', '09:00:00', '10:00:00', 'Tersedia'),
(18, 2, 'Senin', '15:00:00', '16:00:00', 'Tersedia'),
(19, 2, 'Senin', '19:00:00', '20:00:00', 'Tersedia'),
(20, 2, 'Selasa', '08:00:00', '10:00:00', 'Tersedia'),
(21, 2, 'Selasa', '15:00:00', '17:00:00', 'Tersedia'),
(22, 2, 'Rabu', '08:00:00', '09:00:00', 'Tersedia'),
(23, 2, 'Rabu', '19:00:00', '21:00:00', 'Tersedia'),
(24, 3, 'Senin', '07:00:00', '08:00:00', 'Tersedia'),
(25, 3, 'Senin', '08:00:00', '09:00:00', 'Tersedia'),
(26, 3, 'Senin', '16:00:00', '17:00:00', 'Tersedia'),
(27, 3, 'Selasa', '07:00:00', '09:00:00', 'Tersedia'),
(28, 3, 'Selasa', '16:00:00', '18:00:00', 'Tersedia'),
(29, 3, 'Rabu', '07:00:00', '08:00:00', 'Tersedia'),
(30, 3, 'Rabu', '16:00:00', '17:00:00', 'Tersedia'),
(31, 4, 'Senin', '10:00:00', '11:00:00', 'Tersedia'),
(32, 4, 'Senin', '15:00:00', '16:00:00', 'Tersedia'),
(33, 4, 'Senin', '19:00:00', '20:00:00', 'Tersedia'),
(34, 4, 'Selasa', '10:00:00', '12:00:00', 'Tersedia'),
(35, 4, 'Selasa', '19:00:00', '21:00:00', 'Tersedia'),
(36, 4, 'Rabu', '10:00:00', '11:00:00', 'Tersedia'),
(37, 4, 'Rabu', '15:00:00', '17:00:00', 'Tersedia'),
(38, 4, 'Rabu', '19:00:00', '20:00:00', 'Tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontak`
--

CREATE TABLE `kontak` (
  `id_kontak` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subjek` varchar(200) DEFAULT NULL,
  `pesan` text NOT NULL,
  `tgl_kirim` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lapangan`
--

CREATE TABLE `lapangan` (
  `id_lapangan` int(11) NOT NULL,
  `nama_lapangan` varchar(100) NOT NULL,
  `tipe` enum('Indoor','Outdoor') NOT NULL DEFAULT 'Indoor',
  `harga_per_jam` decimal(10,0) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT 'default-lapangan.jpg',
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `tgl_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lapangan`
--

INSERT INTO `lapangan` (`id_lapangan`, `nama_lapangan`, `tipe`, `harga_per_jam`, `deskripsi`, `gambar`, `status`, `tgl_dibuat`) VALUES
(1, 'Lapangan A - VIP Indoor', 'Indoor', 200000, 'Lapangan indoor premium dengan rumput sintetis berkualitas tinggi, pencahayaan LED, dan fasilitas lengkap.', 'lap_1780626248.jpg', 'Aktif', '2026-06-05 09:35:22'),
(2, 'Lapangan B - Indoor', 'Indoor', 150000, 'Lapangan indoor standar dengan rumput sintetis dan pencahayaan baik.', 'lap_1780626195.jpg', 'Aktif', '2026-06-05 09:35:22'),
(3, 'Lapangan C - Outdoor', 'Outdoor', 100000, 'Lapangan outdoor dengan rumput sintetis tahan cuaca.', 'lap_1780626304.jpg', 'Aktif', '2026-06-05 09:35:22'),
(4, 'Lapangan D - Indoor Premium', 'Indoor', 250000, 'Lapangan indoor premium dengan AC, score digital, dan fasilitas VIP.', 'lap_1780626136.jpg', 'Aktif', '2026-06-05 09:35:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `dibaca` tinyint(1) DEFAULT 0,
  `tgl_notifikasi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_user`, `judul`, `pesan`, `dibaca`, `tgl_notifikasi`) VALUES
(1, 2, 'Booking Berhasil', 'Booking lapangan Lapangan B - Indoor pada 2026-06-05 08:00-09:00 berhasil. Silakan datang dan bayar di tempat.', 0, '2026-06-05 10:38:15'),
(2, 3, 'Booking Berhasil', 'Booking lapangan Lapangan A - VIP Indoor pada 2026-06-09 08:00-09:00 berhasil. Silakan datang dan bayar di tempat.', 0, '2026-06-09 22:16:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `metode_pembayaran` enum('Transfer BCA','Transfer BNI','Transfer Mandiri','Transfer BRI','E-Wallet') NOT NULL,
  `jumlah_bayar` decimal(10,0) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('Menunggu','Dikonfirmasi','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `tgl_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_booking`, `metode_pembayaran`, `jumlah_bayar`, `bukti_pembayaran`, `status_pembayaran`, `tgl_bayar`) VALUES
(1, 1, '', 150000, NULL, 'Dikonfirmasi', '2026-06-05 10:38:15'),
(2, 2, '', 200000, NULL, 'Dikonfirmasi', '2026-06-09 22:16:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text DEFAULT NULL,
  `tgl_ulasan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.png',
  `tgl_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_hp`, `alamat`, `foto`, `tgl_daftar`) VALUES
(1, 'John Doe', 'user@futsal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', NULL, 'default.png', '2026-06-05 09:35:22'),
(2, 'Aco', 'innanurmutmainnah04@gmail.com', '$2y$10$ZY4s.NPRBC3PDAlP7N6xVe./GATufSrJYW.TgNUl/pvOtYdlME8xe', '082315969987', NULL, 'default.png', '2026-06-05 10:28:55'),
(3, 'Muhammad Riswan', 'rmuh3418@gmail.com', '$2y$10$yojduvPZWFLyqGQKg6Nw1.2voLfF8tnG7d.ed28o1xcULXxoGhYKm', '087744062441', NULL, 'default.png', '2026-06-09 22:16:22');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lapangan` (`id_lapangan`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_lapangan` (`id_lapangan`);

--
-- Indeks untuk tabel `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id_kontak`);

--
-- Indeks untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id_lapangan`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lapangan` (`id_lapangan`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id_kontak` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id_lapangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id_jadwal`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_3` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
