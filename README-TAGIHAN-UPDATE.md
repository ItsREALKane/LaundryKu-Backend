# Pembaruan Struktur Tabel Tagihan

Dokumen ini menjelaskan perubahan yang telah dilakukan pada struktur database untuk sistem tagihan di aplikasi LaundryKu.

## Perubahan yang Dilakukan

### 1. Pembuatan Ulang Tabel Tagihan

Tabel `tagihan` telah dibuat ulang dengan struktur baru yang berisi:
- `nama_pelanggan` (dari tabel pesanan)
- `nomor` (dari tabel pesanan)
- `alamat` (dari tabel pesanan)
- `jumlah_pesanan` (total semua pesanan pelanggan)
- `total_tagihan` (Total jumlah harga dari semua pesanan pelanggan yang belum lunas)
- `id_owner` (ID laundry pemilik untuk filter)

### 2. Penghapusan Tabel detail_pesanan dan Pembuatan Tabel detail_tagihan

Tabel `detail_pesanan` telah dihapus dan digantikan dengan tabel `detail_tagihan` yang berisi:
- `id_pesanan` (dari tabel pesanan)
- `layanan` (dari tabel pesanan)
- `berat` (dari tabel pesanan)
- `jumlah_harga` (dari tabel pesanan)
- `status` (dari tabel pesanan)
- `id_owner`
- `nama_pelanggan` (dari tabel pesanan)

### 3. Penambahan Status 'lunas' pada Kolom 'status' di Tabel pesanan

Kolom `status` pada tabel `pesanan` telah diperbarui untuk menyertakan status 'lunas' selain status yang sudah ada ('pending', 'diproses', 'selesai').

## Relasi Antar Tabel

- `Owner` memiliki banyak `Pesanan`, `Tagihan`, dan `DetailTagihan`
- `Pesanan` memiliki satu `DetailTagihan`
- `Tagihan` memiliki banyak `DetailTagihan` (berdasarkan id_owner dan nama_pelanggan)

## Cara Menjalankan Migrasi

Untuk menerapkan perubahan ini ke database, ikuti langkah-langkah berikut:

1. Jalankan migrasi untuk membuat struktur tabel baru:

```bash
php artisan migrate
```

2. Jalankan seeder untuk memigrasikan data dari struktur lama ke struktur baru:

```bash
php artisan db:seed --class=MigrateToDetailTagihanSeeder
```

## Catatan Penting

- Pastikan untuk membuat backup database sebelum menjalankan migrasi ini.
- Migrasi ini akan menghapus tabel `detail_pesanan` yang ada dan membuat tabel baru `detail_tagihan`.
- Data dari tabel `pesanan` akan digunakan untuk mengisi tabel `tagihan` dan `detail_tagihan`.
- Status 'lunas' telah ditambahkan ke enum status pada tabel `pesanan`.