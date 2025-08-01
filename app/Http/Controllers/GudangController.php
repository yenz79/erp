<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StockMutation;

class GudangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(StockMutation::class, 'stock_mutation');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', StockMutation::class);
        $mutations = StockMutation::with(['product', 'store', 'user'])->latest()->paginate(20);
        return response()->json($mutations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', StockMutation::class);
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'qty' => 'required|integer',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);
        $mutation = StockMutation::create($validated);
        return response()->json($mutation->load(['product', 'store', 'user']), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mutation = StockMutation::with(['product', 'store', 'user'])->findOrFail($id);
        $this->authorize('view', $mutation);
        return response()->json($mutation);
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
        $mutation = StockMutation::findOrFail($id);
        $this->authorize('update', $mutation);
        $validated = $request->validate([
            'qty' => 'sometimes|integer',
            'type' => 'sometimes|string',
            'description' => 'nullable|string',
        ]);
        $mutation->update($validated);
        return response()->json($mutation->load(['product', 'store', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mutation = StockMutation::findOrFail($id);
        $this->authorize('delete', $mutation);
        $mutation->delete();
        return response()->json(['message' => 'Mutasi stok dihapus']);
    }
}
