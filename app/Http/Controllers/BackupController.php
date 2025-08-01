<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function index()
    {
        // List file backup
        $files = Storage::disk('backups')->files();
        return response()->json($files);
    }
    public function store()
    {
        // Buat backup database
        $filename = 'backup_' . now()->format('Ymd_His') . '.sql';
        $path = Storage::disk('backups')->path($filename);
        $command = sprintf('mysqldump -u%s -p%s %s > %s', env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), $path);
        exec($command);
        return response()->json(['message' => 'Backup berhasil', 'file' => $filename]);
    }
    public function show($filename)
    {
        // Download file backup
        if (!Storage::disk('backups')->exists($filename)) {
            abort(404);
        }
        return Storage::disk('backups')->download($filename);
    }
    public function destroy($filename)
    {
        // Hapus file backup
        Storage::disk('backups')->delete($filename);
        return response()->json(['message' => 'Backup dihapus']);
    }
}