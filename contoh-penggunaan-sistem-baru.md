# Contoh Penggunaan API Sistem Pelanggan Baru

## 1. Membuat Pesanan dengan Pelanggan Baru

```bash
curl -X POST "http://localhost:8000/api/pesanan" \
  -H "Content-Type: application/json" \
  -d '{
    "id_owner": 1,
    "nama_pelanggan": "John Doe",
    "nomor": "081234567890",
    "alamat": "Jl. Sudirman No. 123, Jakarta",
    "layanan": "Cuci Reguler",
    "berat": 2.5,
    "jumlah_harga": 37500,
    "status": "pending",
    "jenis_pembayaran": "cash"
  }'
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
}
```

## 2. Membuat Pesanan dengan Pelanggan yang Sudah Ada

```bash
curl -X POST "http://localhost:8000/api/pesanan" \
  -H "Content-Type: application/json" \
  -d '{
    "id_owner": 1,
    "id_pelanggan": 1,
    "layanan": "Cuci Express",
    "berat": 1.5,
    "jumlah_harga": 37500,
    "status": "pending",
    "jenis_pembayaran": "transfer"
  }'
```

**Response:**
```json
{
    "status": true,
    "message": "Pesanan berhasil dibuat",
    "data": {
        "id": 2,
        "id_owner": 1,
        "id_pelanggan": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Sudirman No. 123, Jakarta",
        "layanan": "Cuci Express",
        "berat": "1.50",
        "jumlah_harga": "37500",
        "status": "pending",
        "jenis_pembayaran": "transfer",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z",
        "pelanggan": {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta"
        }
    }
}
```

## 3. Mengambil Daftar Pelanggan (dari tabel pesanan)

```bash
curl -X GET "http://localhost:8000/api/pelanggan?id_owner=1"
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

## 4. Mencari Pelanggan

```bash
curl -X GET "http://localhost:8000/api/pelanggan/search?q=john&id_owner=1"
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

## 5. Mengambil Daftar Pesanan

```bash
curl -X GET "http://localhost:8000/api/pesanan?id_owner=1"
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
            "id_admin": null,
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
        },
        {
            "id": 2,
            "id_owner": 1,
            "id_admin": null,
            "id_pelanggan": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta",
            "layanan": "Cuci Express",
            "berat": "1.50",
            "jumlah_harga": "37500",
            "status": "pending",
            "jenis_pembayaran": "transfer",
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

## 6. Mengupdate Status Pesanan

```bash
curl -X PUT "http://localhost:8000/api/pesanan/1" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "diproses"
  }'
```

**Response:**
```json
{
    "status": true,
    "message": "Pesanan berhasil diupdate",
    "data": {
        "id": 1,
        "id_owner": 1,
        "id_admin": null,
        "id_pelanggan": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Sudirman No. 123, Jakarta",
        "layanan": "Cuci Reguler",
        "berat": "2.50",
        "jumlah_harga": "37500",
        "status": "diproses",
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
}
```

## 7. Mengupdate Pesanan dengan Pelanggan Lain

```bash
curl -X PUT "http://localhost:8000/api/pesanan/1" \
  -H "Content-Type: application/json" \
  -d '{
    "id_pelanggan": 2
  }'
```

**Response:**
```json
{
    "status": true,
    "message": "Pesanan berhasil diupdate",
    "data": {
        "id": 1,
        "id_owner": 1,
        "id_admin": null,
        "id_pelanggan": 2,
        "nama_pelanggan": "Jane Smith",
        "nomor": "081234567891",
        "alamat": "Jl. Thamrin No. 456, Jakarta",
        "layanan": "Cuci Reguler",
        "berat": "2.50",
        "jumlah_harga": "37500",
        "status": "diproses",
        "jenis_pembayaran": "cash",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z",
        "pelanggan": {
            "id": 2,
            "nama_pelanggan": "Jane Smith",
            "nomor": "081234567891",
            "alamat": "Jl. Thamrin No. 456, Jakarta"
        }
    }
}
```

## 8. Mengambil Detail Pesanan

```bash
curl -X GET "http://localhost:8000/api/pesanan/1"
```

**Response:**
```json
{
    "status": true,
    "message": "Data pesanan berhasil diambil",
    "data": {
        "id": 1,
        "id_owner": 1,
        "id_admin": null,
        "id_pelanggan": 2,
        "nama_pelanggan": "Jane Smith",
        "nomor": "081234567891",
        "alamat": "Jl. Thamrin No. 456, Jakarta",
        "layanan": "Cuci Reguler",
        "berat": "2.50",
        "jumlah_harga": "37500",
        "status": "diproses",
        "jenis_pembayaran": "cash",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z",
        "pelanggan": {
            "id": 2,
            "nama_pelanggan": "Jane Smith",
            "nomor": "081234567891",
            "alamat": "Jl. Thamrin No. 456, Jakarta"
        }
    }
}
```

## Cara Kerja Sistem

### 1. **Membuat Pesanan dengan Pelanggan Baru**
- Data pelanggan disimpan di tabel `pesanan` (nama_pelanggan, nomor, alamat)
- Sistem mengecek apakah pelanggan sudah ada di tabel `pelanggan` berdasarkan nomor
- Jika belum ada, buat record baru di tabel `pelanggan`
- Set `id_pelanggan` di tabel `pesanan` ke ID pelanggan yang baru dibuat

### 2. **Membuat Pesanan dengan Pelanggan yang Sudah Ada**
- Ambil data pelanggan dari tabel `pelanggan` berdasarkan `id_pelanggan`
- Copy data pelanggan ke tabel `pesanan` (nama_pelanggan, nomor, alamat)
- Set `id_pelanggan` di tabel `pesanan`

### 3. **Mengambil Daftar Pelanggan**
- Ambil data dari tabel `pesanan` (bukan dari tabel `pelanggan`)
- Group by nomor untuk menghindari duplikasi
- Return data pelanggan unik

### 4. **Mencari Pelanggan**
- Cari dari tabel `pesanan` berdasarkan nama atau nomor
- Group by nomor untuk menghindari duplikasi
- Return data pelanggan yang cocok

## Keuntungan Sistem Ini

1. **Data Utama di Pesanan**: Data pelanggan langsung tersedia di setiap pesanan
2. **Backup di Tabel Pelanggan**: Tabel `pelanggan` sebagai backup dan referensi
3. **Konsistensi**: Data pelanggan selalu disimpan di kedua tabel
4. **Performa**: Tidak perlu join table untuk mendapatkan data pelanggan
5. **Fleksibilitas**: Bisa membuat pesanan dengan pelanggan baru atau yang sudah ada

Sistem ini memberikan fleksibilitas maksimal sambil tetap menjaga konsistensi data dan performa yang baik. 