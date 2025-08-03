# Dokumentasi API Pelanggan dan Layanan

## Overview
Sistem ini telah diperbarui untuk menggunakan tabel `pelanggan` dan `layanan` yang terpisah. Sekarang data pelanggan disimpan di tabel `pelanggan` dan pesanan menggunakan foreign key `id_pelanggan`.

## Struktur Database Baru

### Tabel `pelanggan`
- `id` (Primary Key)
- `nama_pelanggan` (String)
- `nomor` (String) - Nomor telepon
- `alamat` (Text)
- `created_at`, `updated_at` (Timestamps)

### Tabel `layanan`
- `id` (Primary Key)
- `nama_layanan` (String)
- `harga_layanan` (String)
- `keterangan_layanan` (String)
- `id_owner` (Foreign Key ke owners)
- `created_at`, `updated_at` (Timestamps)

### Tabel `pesanan` (Updated)
- `id` (Primary Key)
- `id_owner` (Foreign Key ke owners)
- `id_admin` (Foreign Key ke admins, nullable)
- `id_pelanggan` (Foreign Key ke pelanggan, nullable)
- `layanan` (String)
- `berat` (Decimal, nullable)
- `jumlah_harga` (Decimal, nullable)
- `status` (Enum: pending, diproses, selesai, lunas)
- `jenis_pembayaran` (Enum: cash, transfer, nullable)
- `created_at`, `updated_at` (Timestamps)

## API Endpoints

### Pelanggan API

#### 1. GET /api/pelanggan
Mendapatkan daftar semua pelanggan.

**Query Parameters:**
- `search` (optional): Mencari pelanggan berdasarkan nama atau nomor

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        }
    ]
}
```

#### 2. POST /api/pelanggan
Membuat pelanggan baru.

**Request Body:**
```json
{
    "nama_pelanggan": "John Doe",
    "nomor": "081234567890",
    "alamat": "Jl. Contoh No. 123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Pelanggan berhasil ditambahkan",
    "data": {
        "id": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
    }
}
```

#### 3. GET /api/pelanggan/search?q=john
Mencari pelanggan berdasarkan nama atau nomor.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123"
        }
    ]
}
```

#### 4. GET /api/pelanggan/{id}
Mendapatkan detail pelanggan berdasarkan ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "pesanan": [
            {
                "id": 1,
                "layanan": "Cuci Reguler",
                "status": "selesai"
            }
        ]
    }
}
```

#### 5. PUT /api/pelanggan/{id}
Memperbarui data pelanggan.

**Request Body:**
```json
{
    "nama_pelanggan": "John Doe Updated",
    "alamat": "Jl. Baru No. 456"
}
```

#### 6. DELETE /api/pelanggan/{id}
Menghapus pelanggan (hanya jika tidak memiliki pesanan).

### Layanan API

#### 1. GET /api/layanan?id_owner=1
Mendapatkan daftar layanan berdasarkan owner.

**Query Parameters:**
- `id_owner` (required): ID owner

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_layanan": "Cuci Reguler",
            "harga_layanan": "15000",
            "keterangan_layanan": "Cuci biasa 2-3 hari",
            "id_owner": 1,
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        }
    ]
}
```

#### 2. POST /api/layanan
Membuat layanan baru.

**Request Body:**
```json
{
    "nama_layanan": "Cuci Express",
    "harga_layanan": "25000",
    "keterangan_layanan": "Cuci cepat 1 hari",
    "id_owner": 1
}
```

#### 3. GET /api/layanan/{id}
Mendapatkan detail layanan.

#### 4. PUT /api/layanan/{id}
Memperbarui layanan.

#### 5. DELETE /api/layanan/{id}
Menghapus layanan.

### Pesanan API (Updated)

#### 1. POST /api/pesanan
Membuat pesanan baru dengan sistem pelanggan.

**Cara 1: Menggunakan pelanggan yang sudah ada**
```json
{
    "id_owner": 1,
    "id_admin": 1,
    "id_pelanggan": 1,
    "layanan": "Cuci Reguler",
    "berat": 2.5,
    "jumlah_harga": 37500,
    "status": "pending",
    "jenis_pembayaran": "cash"
}
```

**Cara 2: Membuat pelanggan baru**
```json
{
    "id_owner": 1,
    "id_admin": 1,
    "nama_pelanggan": "Jane Doe",
    "nomor": "081234567891",
    "alamat": "Jl. Baru No. 789",
    "layanan": "Cuci Express",
    "berat": 1.5,
    "jumlah_harga": 37500,
    "status": "pending",
    "jenis_pembayaran": "transfer"
}
```

**Response:**
```json
{
    "status": true,
    "message": "Pesanan berhasil dibuat",
    "data": {
        "id": 1,
        "id_owner": 1,
        "id_admin": 1,
        "id_pelanggan": 1,
        "layanan": "Cuci Reguler",
        "berat": "2.50",
        "jumlah_harga": "37500",
        "status": "pending",
        "jenis_pembayaran": "cash",
        "pelanggan": {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123"
        }
    }
}
```

## Fitur Utama

### 1. Sistem Pelanggan Terpusat
- Data pelanggan disimpan di tabel terpisah
- Mencegah duplikasi data pelanggan
- Memudahkan pencarian dan pengelolaan pelanggan

### 2. Pencarian Pelanggan Otomatis
- Saat membuat pesanan, sistem akan mengecek apakah pelanggan sudah ada berdasarkan nomor
- Jika sudah ada, akan menggunakan data pelanggan yang ada
- Jika belum ada, akan membuat pelanggan baru

### 3. Relasi Data
- Pesanan terhubung dengan pelanggan melalui `id_pelanggan`
- Layanan terhubung dengan owner melalui `id_owner`
- Data lebih terstruktur dan mudah dikelola

### 4. Validasi
- Validasi nomor telepon unik untuk pelanggan
- Validasi relasi antar tabel
- Pencegahan penghapusan pelanggan yang masih memiliki pesanan

## Cara Penggunaan

### 1. Membuat Pesanan dengan Pelanggan Baru
```javascript
// Frontend akan mengirim data pelanggan baru
const response = await fetch('/api/pesanan', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        id_owner: 1,
        nama_pelanggan: "Pelanggan Baru",
        nomor: "081234567890",
        alamat: "Alamat Pelanggan",
        layanan: "Cuci Reguler",
        berat: 2.5,
        jumlah_harga: 37500
    })
});
```

### 2. Membuat Pesanan dengan Pelanggan yang Sudah Ada
```javascript
// Frontend akan mengirim id_pelanggan
const response = await fetch('/api/pesanan', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        id_owner: 1,
        id_pelanggan: 1, // ID pelanggan yang sudah ada
        layanan: "Cuci Express",
        berat: 1.5,
        jumlah_harga: 37500
    })
});
```

### 3. Mencari Pelanggan
```javascript
// Mencari pelanggan berdasarkan nama atau nomor
const response = await fetch('/api/pelanggan/search?q=john');
const data = await response.json();
```

## Migration yang Diperlukan

Pastikan menjalankan migration berikut:
```bash
php artisan migrate
```

Migration yang akan dijalankan:
1. `2025_07_30_021156_layanan_table.php` - Membuat tabel layanan dan pelanggan
2. `2025_08_15_000000_update_pesanan_use_pelanggan_table.php` - Mengubah tabel pesanan untuk menggunakan foreign key ke pelanggan 