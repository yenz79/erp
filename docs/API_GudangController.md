# GudangController API Documentation

## Endpoints

### GET /api/gudang
- Description: Mendapatkan daftar mutasi stok.
- Response: JSON paginated stock mutation data.
- Auth: Sanctum
- Contoh Response:
```json
{
  "data": [
    {
      "id": 1,
      "product": {"id": 1, "name": "Produk A"},
      "store": {"id": 1, "name": "Toko 1"},
      "user": {"id": 2, "name": "Admin"},
      "qty": 10,
      "type": "in",
      "description": "Stok masuk awal",
      "created_at": "2025-08-01T10:00:00Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### POST /api/gudang
- Description: Membuat mutasi stok baru.
- Body:
  - product_id (integer, required)
  - store_id (integer, required)
  - user_id (integer, required)
  - qty (integer, required)
  - type (string, required: "in" atau "out")
  - description (string, optional)
- Validasi:
  - Semua field di atas wajib kecuali description
  - type hanya "in" atau "out"
- Response: StockMutation detail (201)
- HTTP Status: 201 Created, 401 Unauthorized, 422 Unprocessable Entity
- Contoh Response:
```json
{
  "id": 2,
  "product": {"id": 1, "name": "Produk A"},
  "store": {"id": 1, "name": "Toko 1"},
  "user": {"id": 2, "name": "Admin"},
  "qty": 5,
  "type": "out",
  "description": "Stok keluar untuk penjualan",
  "created_at": "2025-08-01T11:00:00Z"
}
```

### GET /api/gudang/{id}
- Description: Mendapatkan detail mutasi stok.
- Parameter Path: id (integer) - ID Mutasi Stok
- Response: StockMutation detail
- HTTP Status: 200 OK, 401 Unauthorized, 404 Not Found
- Contoh Response:
```json
{
  "id": 1,
  "product": {"id": 1, "name": "Produk A"},
  "store": {"id": 1, "name": "Toko 1"},
  "user": {"id": 2, "name": "Admin"},
  "qty": 10,
  "type": "in",
  "description": "Stok masuk awal",
  "created_at": "2025-08-01T10:00:00Z"
}
```

### PUT /api/gudang/{id}
- Description: Update mutasi stok.
- Parameter Path: id (integer) - ID Mutasi Stok
- Body:
  - qty (integer, optional)
  - type (string, optional: "in" atau "out")
  - description (string, optional)
- Validasi:
  - qty harus integer
  - type hanya "in" atau "out"
- Response: StockMutation detail
- HTTP Status: 200 OK, 401 Unauthorized, 404 Not Found, 422 Unprocessable Entity
- Contoh Response:
```json
{
  "id": 1,
  "product": {"id": 1, "name": "Produk A"},
  "store": {"id": 1, "name": "Toko 1"},
  "user": {"id": 2, "name": "Admin"},
  "qty": 15,
  "type": "in",
  "description": "Update stok masuk",
  "created_at": "2025-08-01T10:00:00Z"
}
```

### DELETE /api/gudang/{id}
- Description: Hapus mutasi stok.
- Parameter Path: id (integer) - ID Mutasi Stok
- Response: Message
- HTTP Status: 200 OK, 401 Unauthorized, 404 Not Found
- Contoh Response:
```json
{
  "message": "Mutasi stok dihapus"
}
```

## Policies
- Semua endpoint menggunakan policy StockMutation: viewAny, view, create, update, delete.

## Error Handling
- Semua endpoint mengembalikan error JSON jika terjadi exception.

## Auth
- Semua endpoint menggunakan middleware 'auth:sanctum'.

## Catatan
- Field type: "in" untuk stok masuk, "out" untuk stok keluar. Field ini memengaruhi penambahan/pengurangan qty pada stok produk.

---
Dokumentasi ini dihasilkan otomatis oleh GitHub Copilot pada 2025-08-01.
