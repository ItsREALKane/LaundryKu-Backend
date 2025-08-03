# Cara Menggunakan API yang Sudah Diperbaiki

## Masalah yang Sudah Diperbaiki

### 1. **API Pelanggan Mengembalikan Data Kosong**
**Masalah:** Data `nama_pelanggan`, `nomor`, dan `alamat` di tabel `pesanan` kosong
**Solusi:** 
- Mengisi data pesanan yang kosong dengan data pelanggan
- Memperbaiki query untuk memfilter data yang tidak kosong

### 2. **API Layanan Memerlukan ID Owner**
**Masalah:** API layanan memerlukan parameter `id_owner` yang wajib
**Solusi:** Membuat parameter `id_owner` menjadi opsional

## Cara Menggunakan API

### 1. **API Pelanggan**

#### Mengambil Daftar Pelanggan
```bash
# Tanpa filter owner
GET /api/pelanggan

# Dengan filter owner
GET /api/pelanggan?id_owner=1

# Dengan pencarian
GET /api/pelanggan?search=john

# Dengan filter owner dan pencarian
GET /api/pelanggan?id_owner=1&search=john
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta"
        },
        {
            "nama_pelanggan": "Jane Smith",
            "nomor": "081234567891",
            "alamat": "Jl. Thamrin No. 456, Jakarta"
        }
    ]
}
```

#### Mencari Pelanggan
```bash
# Tanpa filter owner
GET /api/pelanggan/search?q=john

# Dengan filter owner
GET /api/pelanggan/search?q=john&id_owner=1
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta"
        }
    ]
}
```

### 2. **API Layanan**

#### Mengambil Daftar Layanan
```bash
# Tanpa filter owner (mengambil semua layanan)
GET /api/layanan

# Dengan filter owner
GET /api/layanan?id_owner=1
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_layanan": "Cuci Reguler",
            "harga_layanan": "15000",
            "keterangan_layanan": "Cuci biasa dengan waktu pengerjaan 2-3 hari",
            "id_owner": 1,
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "nama_layanan": "Cuci Express",
            "harga_layanan": "25000",
            "keterangan_layanan": "Cuci cepat dengan waktu pengerjaan 1 hari",
            "id_owner": 1,
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        }
    ]
}
```

### 3. **API Pesanan**

#### Mengambil Daftar Pesanan
```bash
# Tanpa filter
GET /api/pesanan

# Dengan filter owner
GET /api/pesanan?id_owner=1

# Dengan filter status
GET /api/pesanan?status=pending

# Dengan filter owner dan status
GET /api/pesanan?id_owner=1&status=pending
```

**Response:**
```json
{
    "status": true,
    "message": "Data pesanan berhasil diambil",
    "data": [
        {
            "id": 1,
            "id_owner": 1,
            "id_admin": 1,
            "id_pelanggan": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta",
            "layanan": "Cuci Reguler",
            "berat": "2.50",
            "jumlah_harga": "37500",
            "status": "pending",
            "jenis_pembayaran": "cash",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z",
            "pelanggan": {
                "id": 1,
                "nama_pelanggan": "John Doe",
                "nomor": "081234567890",
                "alamat": "Jl. Sudirman No. 123, Jakarta"
            }
        }
    ]
}
```

## Contoh Penggunaan dengan cURL

### 1. Mengambil Daftar Pelanggan
```bash
curl -X GET "http://localhost:8000/api/pelanggan"
```

### 2. Mengambil Daftar Layanan
```bash
curl -X GET "http://localhost:8000/api/layanan"
```

### 3. Mengambil Daftar Pesanan
```bash
curl -X GET "http://localhost:8000/api/pesanan"
```

### 4. Mencari Pelanggan
```bash
curl -X GET "http://localhost:8000/api/pelanggan/search?q=john"
```

## Contoh Penggunaan dengan JavaScript

### 1. Mengambil Daftar Pelanggan
```javascript
const response = await fetch('/api/pelanggan');
const data = await response.json();
console.log(data);
```

### 2. Mengambil Daftar Layanan
```javascript
const response = await fetch('/api/layanan');
const data = await response.json();
console.log(data);
```

### 3. Mengambil Daftar Pesanan
```javascript
const response = await fetch('/api/pesanan');
const data = await response.json();
console.log(data);
```

### 4. Mencari Pelanggan
```javascript
const response = await fetch('/api/pelanggan/search?q=john');
const data = await response.json();
console.log(data);
```

## Status API

✅ **API Pelanggan**: Sudah diperbaiki dan berfungsi normal
✅ **API Layanan**: Sudah diperbaiki dan berfungsi normal  
✅ **API Pesanan**: Sudah berfungsi normal

## Catatan Penting

1. **Data Pelanggan**: Sekarang diambil dari tabel `pesanan` dengan filter data yang tidak kosong
2. **Parameter Opsional**: Parameter `id_owner` di API layanan sekarang opsional
3. **Pencarian**: API pelanggan mendukung pencarian berdasarkan nama atau nomor
4. **Filter**: Semua API mendukung filter berdasarkan owner dan parameter lainnya

Sekarang semua API sudah berfungsi dengan baik dan dapat digunakan untuk aplikasi Anda! 