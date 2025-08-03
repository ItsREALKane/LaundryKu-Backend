# Sistem Pelanggan Baru - Data Utama di Tabel Pesanan

## Overview
Sistem ini menggunakan pendekatan hybrid dimana:
- **Data pelanggan utama disimpan di tabel `pesanan`** (nama_pelanggan, nomor, alamat)
- **Tabel `pelanggan` sebagai backup/referensi** yang diisi otomatis dari data pesanan
- **Foreign key `id_pelanggan`** tetap ada untuk relasi ke tabel pelanggan

## Struktur Database

### Tabel `pesanan` (Data Utama)
```sql
CREATE TABLE pesanan (
    id BIGINT PRIMARY KEY,
    id_owner BIGINT,
    id_admin BIGINT NULL,
    id_pelanggan BIGINT NULL,  -- Foreign key ke tabel pelanggan
    nama_pelanggan VARCHAR(255), -- Data utama pelanggan
    nomor VARCHAR(255),         -- Data utama pelanggan
    alamat TEXT,               -- Data utama pelanggan
    layanan VARCHAR(255),
    berat DECIMAL(8,2),
    jumlah_harga DECIMAL(10,2),
    status ENUM('pending','diproses','selesai','lunas'),
    jenis_pembayaran ENUM('cash','transfer'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel `pelanggan` (Backup/Referensi)
```sql
CREATE TABLE pelanggan (
    id BIGINT PRIMARY KEY,
    nama_pelanggan VARCHAR(255),
    nomor VARCHAR(255),
    alamat TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Cara Kerja Sistem

### 1. Membuat Pesanan dengan Pelanggan Baru
```json
POST /api/pesanan
{
    "id_owner": 1,
    "nama_pelanggan": "John Doe",
    "nomor": "081234567890",
    "alamat": "Jl. Contoh No. 123",
    "layanan": "Cuci Reguler",
    "berat": 2.5,
    "jumlah_harga": 37500
}
```

**Proses:**
1. Data pelanggan disimpan di tabel `pesanan` (nama_pelanggan, nomor, alamat)
2. Sistem mengecek apakah pelanggan sudah ada di tabel `pelanggan` berdasarkan nomor
3. Jika belum ada, buat record baru di tabel `pelanggan`
4. Set `id_pelanggan` di tabel `pesanan` ke ID pelanggan yang baru dibuat

### 2. Membuat Pesanan dengan Pelanggan yang Sudah Ada
```json
POST /api/pesanan
{
    "id_owner": 1,
    "id_pelanggan": 1,
    "layanan": "Cuci Express",
    "berat": 1.5,
    "jumlah_harga": 37500
}
```

**Proses:**
1. Ambil data pelanggan dari tabel `pelanggan` berdasarkan `id_pelanggan`
2. Copy data pelanggan ke tabel `pesanan` (nama_pelanggan, nomor, alamat)
3. Set `id_pelanggan` di tabel `pesanan`

### 3. Mengambil Daftar Pelanggan
```bash
GET /api/pelanggan?id_owner=1
```

**Proses:**
1. Ambil data dari tabel `pesanan` (bukan dari tabel `pelanggan`)
2. Group by nomor untuk menghindari duplikasi
3. Return data pelanggan unik

## API Endpoints

### Pesanan API

#### POST /api/pesanan
Membuat pesanan baru.

**Request Body (Pelanggan Baru):**
```json
{
    "id_owner": 1,
    "nama_pelanggan": "John Doe",
    "nomor": "081234567890",
    "alamat": "Jl. Contoh No. 123",
    "layanan": "Cuci Reguler",
    "berat": 2.5,
    "jumlah_harga": 37500
}
```

**Request Body (Pelanggan yang Sudah Ada):**
```json
{
    "id_owner": 1,
    "id_pelanggan": 1,
    "layanan": "Cuci Express",
    "berat": 1.5,
    "jumlah_harga": 37500
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
        "id_pelanggan": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "layanan": "Cuci Reguler",
        "berat": "2.50",
        "jumlah_harga": "37500",
        "status": "pending",
        "pelanggan": {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123"
        }
    }
}
```

### Pelanggan API

#### GET /api/pelanggan
Mendapatkan daftar pelanggan dari tabel pesanan.

**Query Parameters:**
- `id_owner` (optional): Filter berdasarkan owner
- `search` (optional): Pencarian berdasarkan nama atau nomor

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123"
        },
        {
            "nama_pelanggan": "Jane Smith",
            "nomor": "081234567891",
            "alamat": "Jl. Lain No. 456"
        }
    ]
}
```

#### GET /api/pelanggan/search?q=john
Mencari pelanggan berdasarkan nama atau nomor.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Contoh No. 123"
        }
    ]
}
```

## Keuntungan Sistem Ini

### 1. **Data Utama di Pesanan**
- Data pelanggan langsung tersedia di setiap pesanan
- Tidak perlu join table untuk mendapatkan data pelanggan
- Performa query lebih cepat

### 2. **Backup di Tabel Pelanggan**
- Tabel `pelanggan` sebagai backup/referensi
- Memudahkan pencarian pelanggan yang sudah ada
- Mencegah duplikasi data pelanggan

### 3. **Fleksibilitas**
- Bisa membuat pesanan dengan pelanggan baru atau yang sudah ada
- Data pelanggan selalu konsisten antara kedua tabel
- Mudah untuk maintenance dan backup

### 4. **Kompatibilitas**
- Tetap bisa menggunakan foreign key untuk relasi
- API tetap konsisten dengan sistem sebelumnya
- Mudah untuk migration dari sistem lama

## Contoh Penggunaan

### 1. Membuat Pesanan dengan Pelanggan Baru
```javascript
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
const response = await fetch('/api/pelanggan/search?q=john&id_owner=1');
const data = await response.json();
```

## Catatan Penting

1. **Data Utama**: Data pelanggan utama ada di tabel `pesanan`
2. **Backup**: Tabel `pelanggan` sebagai backup dan referensi
3. **Konsistensi**: Data pelanggan selalu disimpan di kedua tabel
4. **Pencarian**: Pencarian pelanggan dilakukan dari tabel `pesanan`
5. **Relasi**: Foreign key `id_pelanggan` tetap ada untuk relasi

Sistem ini memberikan fleksibilitas maksimal sambil tetap menjaga konsistensi data dan performa yang baik. 