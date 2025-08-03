# Contoh Penggunaan API Pelanggan dan Layanan

## 1. Mengambil Daftar Pelanggan

```bash
curl -X GET "http://localhost:8000/api/pelanggan"
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "nama_pelanggan": "Jane Smith",
            "nomor": "081234567891",
            "alamat": "Jl. Thamrin No. 456, Jakarta",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z"
        }
    ]
}
```

## 2. Mencari Pelanggan

```bash
curl -X GET "http://localhost:8000/api/pelanggan/search?q=john"
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_pelanggan": "John Doe",
            "nomor": "081234567890",
            "alamat": "Jl. Sudirman No. 123, Jakarta"
        }
    ]
}
```

## 3. Membuat Pelanggan Baru

```bash
curl -X POST "http://localhost:8000/api/pelanggan" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_pelanggan": "Sarah Wilson",
    "nomor": "081234567895",
    "alamat": "Jl. Menteng No. 111, Jakarta"
  }'
```

**Response:**
```json
{
    "success": true,
    "message": "Pelanggan berhasil ditambahkan",
    "data": {
        "id": 6,
        "nama_pelanggan": "Sarah Wilson",
        "nomor": "081234567895",
        "alamat": "Jl. Menteng No. 111, Jakarta",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
    }
}
```

## 4. Mengambil Daftar Layanan

```bash
curl -X GET "http://localhost:8000/api/layanan?id_owner=1"
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

## 5. Membuat Layanan Baru

```bash
curl -X POST "http://localhost:8000/api/layanan" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_layanan": "Cuci Premium",
    "harga_layanan": "40000",
    "keterangan_layanan": "Cuci premium dengan pewangi khusus",
    "id_owner": 1
  }'
```

**Response:**
```json
{
    "success": true,
    "message": "Layanan berhasil ditambahkan",
    "data": {
        "id": 6,
        "nama_layanan": "Cuci Premium",
        "harga_layanan": "40000",
        "keterangan_layanan": "Cuci premium dengan pewangi khusus",
        "id_owner": 1,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
    }
}
```

## 6. Membuat Pesanan dengan Pelanggan yang Sudah Ada

```bash
curl -X POST "http://localhost:8000/api/pesanan" \
  -H "Content-Type: application/json" \
  -d '{
    "id_owner": 1,
    "id_admin": 1,
    "id_pelanggan": 1,
    "layanan": "Cuci Express",
    "berat": 2.5,
    "jumlah_harga": 62500,
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
        "id_admin": 1,
        "id_pelanggan": 1,
        "layanan": "Cuci Express",
        "berat": "2.50",
        "jumlah_harga": "62500",
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

## 7. Membuat Pesanan dengan Pelanggan Baru

```bash
curl -X POST "http://localhost:8000/api/pesanan" \
  -H "Content-Type: application/json" \
  -d '{
    "id_owner": 1,
    "id_admin": 1,
    "nama_pelanggan": "Mike Johnson",
    "nomor": "081234567896",
    "alamat": "Jl. Kebayoran No. 222, Jakarta",
    "layanan": "Cuci Reguler",
    "berat": 1.5,
    "jumlah_harga": 22500,
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
        "id_admin": 1,
        "id_pelanggan": 7,
        "layanan": "Cuci Reguler",
        "berat": "1.50",
        "jumlah_harga": "22500",
        "status": "pending",
        "jenis_pembayaran": "transfer",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z",
        "pelanggan": {
            "id": 7,
            "nama_pelanggan": "Mike Johnson",
            "nomor": "081234567896",
            "alamat": "Jl. Kebayoran No. 222, Jakarta"
        }
    }
}
```

## 8. Mengambil Daftar Pesanan

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
            "id_admin": 1,
            "id_pelanggan": 1,
            "layanan": "Cuci Express",
            "berat": "2.50",
            "jumlah_harga": "62500",
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
            "id_admin": 1,
            "id_pelanggan": 7,
            "layanan": "Cuci Reguler",
            "berat": "1.50",
            "jumlah_harga": "22500",
            "status": "pending",
            "jenis_pembayaran": "transfer",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-15T10:00:00.000000Z",
            "pelanggan": {
                "id": 7,
                "nama_pelanggan": "Mike Johnson",
                "nomor": "081234567896",
                "alamat": "Jl. Kebayoran No. 222, Jakarta"
            }
        }
    ]
}
```

## 9. Mengupdate Status Pesanan

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
        "id_admin": 1,
        "id_pelanggan": 1,
        "layanan": "Cuci Express",
        "berat": "2.50",
        "jumlah_harga": "62500",
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

## 10. Mengupdate Data Pelanggan

```bash
curl -X PUT "http://localhost:8000/api/pelanggan/1" \
  -H "Content-Type: application/json" \
  -d '{
    "alamat": "Jl. Sudirman No. 123A, Jakarta Selatan"
  }'
```

**Response:**
```json
{
    "success": true,
    "message": "Pelanggan berhasil diperbarui",
    "data": {
        "id": 1,
        "nama_pelanggan": "John Doe",
        "nomor": "081234567890",
        "alamat": "Jl. Sudirman No. 123A, Jakarta Selatan",
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
    }
}
```

## Catatan Penting

1. **Pelanggan Otomatis**: Saat membuat pesanan dengan data pelanggan baru, sistem akan otomatis membuat pelanggan baru jika nomor telepon belum terdaftar.

2. **Pencarian Pelanggan**: Gunakan endpoint `/api/pelanggan/search?q=keyword` untuk mencari pelanggan berdasarkan nama atau nomor.

3. **Relasi Data**: Pesanan sekarang terhubung dengan pelanggan melalui `id_pelanggan`, sehingga data lebih terstruktur.

4. **Validasi**: Sistem akan memvalidasi nomor telepon unik untuk pelanggan dan mencegah penghapusan pelanggan yang masih memiliki pesanan.

5. **Layanan per Owner**: Setiap layanan terhubung dengan owner tertentu, sehingga owner hanya bisa melihat dan mengelola layanan miliknya sendiri. 