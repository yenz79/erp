<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use Inertia\Inertia;
use App\Models\ActivityLog;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;

class POSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Sale::class, 'sale');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Policy: viewAny
        $this->authorize('viewAny', Sale::class);
        $sales = Sale::with(['user', 'store', 'saleItems.product'])->latest()->paginate(20);
        if (request()->wantsJson()) {
            return response()->json($sales);
        }
        return Inertia::render('POS', ['sales' => $sales]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Policy: create
        $this->authorize('create', Sale::class);
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);
        try {
            $sale = Sale::create([
                'store_id' => $validated['store_id'],
                'user_id' => $validated['user_id'],
                'total' => $validated['total'],
            ]);
            foreach ($validated['items'] as $item) {
                $sale->saleItems()->create($item);
            }
            $this->logActivity('create', 'Sale', $sale->toArray());
            return response()->json($sale->load('saleItems.product'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale = Sale::with(['user', 'store', 'saleItems.product'])->findOrFail($id);
        $this->authorize('view', $sale);
        return response()->json($sale);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);
        $this->authorize('update', $sale);
        $validated = $request->validate([
            'total' => 'sometimes|numeric',
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.qty' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric',
        ]);
        try {
            $sale->update($validated);
            if (isset($validated['items'])) {
                $sale->saleItems()->delete();
                foreach ($validated['items'] as $item) {
                    $sale->saleItems()->create($item);
                }
            }
            $this->logActivity('update', 'Sale', $sale->toArray());
            return response()->json($sale->load('saleItems.product'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $this->authorize('delete', $sale);
        $sale->saleItems()->delete();
        $sale->delete();
        $this->logActivity('delete', 'Sale', ['id' => $id]);
        return response()->json(['message' => 'Transaksi penjualan dihapus']);
    }

    private function logActivity($action, $model, $data = [])
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model,
            'data' => json_encode($data),
            'ip_address' => request()->ip(),
        ]);
    }

    public function printReceipt($id)
    {
        $sale = Sale::with(['user', 'store', 'saleItems.product'])->findOrFail($id);
        $this->authorize('view', $sale);
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($sale->id, $generator::TYPE_CODE_128));
        // Simulasi cetak struk thermal printer
        return Inertia::render('POSPrint', [
            'sale' => $sale,
            'barcode' => $barcode
        ]);
    }

    public function exportSales()
    {
        $filename = 'sales_export_' . now()->format('Ymd_His') . '.csv';
        $sales = Sale::with(['user', 'store', 'saleItems.product'])->get();
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Tanggal', 'User', 'Store', 'Total', 'Items']);
        foreach ($sales as $sale) {
            $items = collect($sale->saleItems)->map(function($item) {
                return $item->product->name . ' x' . $item->qty;
            })->implode('; ');
            fputcsv($csv, [
                $sale->id,
                $sale->created_at,
                $sale->user->name ?? '',
                $sale->store->name ?? '',
                $sale->total,
                $items
            ]);
        }
        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);
        Storage::disk('local')->put($filename, $csvContent);
        return response()->download(storage_path('app/' . $filename));
    }

    public function importSales(Request $request)
    {
        $this->authorize('create', Sale::class);
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Sesuaikan mapping kolom sesuai header CSV
                $sale = Sale::create([
                    'store_id' => $row[3] ?? null,
                    'user_id' => $row[2] ?? null,
                    'total' => $row[4] ?? 0,
                ]);
                // Items parsing (kolom 5)
                if (!empty($row[5])) {
                    $items = explode(';', $row[5]);
                    foreach ($items as $itemStr) {
                        [$productName, $qty] = explode(' x', $itemStr);
                        // Cari product_id dari nama
                        $product = \App\Models\Product::where('name', trim($productName))->first();
                        if ($product) {
                            $sale->saleItems()->create([
                                'product_id' => $product->id,
                                'qty' => (int) $qty,
                                'price' => 0 // Harga default, bisa diupdate
                            ]);
                        }
                    }
                }
                $imported++;
            } catch (\Exception $e) {
                continue;
            }
        }
        fclose($handle);
        return response()->json(['message' => "Import selesai: $imported transaksi."]);
    }

    public function backupAuto()
    {
        $this->authorize('create', Sale::class);
        // Backup database otomatis
        $filename = 'backup_auto_' . now()->format('Ymd_His') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        $command = sprintf('mysqldump -u%s -p%s %s > %s', env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), $path);
        exec($command);
        return response()->json(['message' => 'Backup otomatis berhasil', 'file' => $filename]);
    }

    public function storeFaceAttendance(Request $request)
    {
        $this->authorize('create', \App\Models\Attendance::class);
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'shift_id' => 'required|exists:shifts,id',
            'type' => 'required|string',
            'face_data' => 'required|string',
            'timestamp' => 'required|date',
        ]);
        // Simulasi verifikasi wajah (hash/compare)
        $faceHash = Str::substr(md5($validated['face_data']), 0, 16);
        $attendance = \App\Models\Attendance::create(array_merge($validated, ['face_hash' => $faceHash]));
        return response()->json($attendance, 201);
    }

    public function notifySale($id)
    {
        $sale = Sale::with(['user', 'store', 'saleItems.product'])->findOrFail($id);
        $this->authorize('view', $sale);
        // Simulasi notifikasi real-time
        Event::dispatch('sale.created', $sale);
        return response()->json(['message' => 'Notifikasi real-time dikirim', 'sale_id' => $sale->id]);
    }
}
