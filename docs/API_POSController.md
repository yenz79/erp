# POSController API Documentation

## Endpoints

### GET /api/pos
- Description: Mendapatkan daftar transaksi penjualan.
- Response: JSON paginated sales data.
- Auth: Sanctum

### POST /api/pos
- Description: Membuat transaksi penjualan baru.
- Body: store_id, user_id, total, items[]
- Response: Sale detail (201)
- Auth: Sanctum

### GET /api/pos/{id}
- Description: Mendapatkan detail transaksi penjualan.
- Response: Sale detail
- Auth: Sanctum

### PUT /api/pos/{id}
- Description: Update transaksi penjualan.
- Body: total, items[]
- Response: Sale detail
- Auth: Sanctum

### DELETE /api/pos/{id}
- Description: Hapus transaksi penjualan.
- Response: Message
- Auth: Sanctum

### GET /api/pos/print/{id}
- Description: Cetak struk penjualan (barcode).
- Response: Inertia page POSPrint
- Auth: Sanctum

### GET /api/pos/export
- Description: Export data penjualan ke CSV.
- Response: Download file CSV
- Auth: Sanctum

### POST /api/pos/import
- Description: Import data penjualan dari CSV.
- Body: file (csv)
- Response: Import result
- Auth: Sanctum

### POST /api/pos/backup
- Description: Backup database otomatis.
- Response: Backup status
- Auth: Sanctum

## Policies
- Semua endpoint menggunakan policy Sale: viewAny, view, create, update, delete.

## Activity Log
- Setiap aksi utama (create, update, delete) akan mencatat log aktivitas ke tabel ActivityLog.

## Error Handling
- Semua endpoint mengembalikan error JSON jika terjadi exception.

## Auth
- Semua endpoint menggunakan middleware 'auth:sanctum'.

---
Dokumentasi ini dihasilkan otomatis oleh GitHub Copilot pada 2025-08-01.
