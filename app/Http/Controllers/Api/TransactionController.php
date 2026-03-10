<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->user()
            ->transactions()
            ->with('category');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return response()->json($transactions);
    }
    public function summary(Request $request)
    {
        $userId = $request->user()->id;

        $summary = Transaction::where('user_id', $userId)
            ->selectRaw('
                type,
                COUNT(*) as total_transactions,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount
            ')
            ->groupBy('type')
            ->get();

        $balance = Transaction::where('user_id', $userId)
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as balance
            ")
            ->value('balance');

        return response()->json([
            'summary' => $summary,
            'balance' => number_format($balance ?? 0, 2),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        // Verificar que la categoría pertenece al usuario
        $category = $request->user()
            ->categories()
            ->findOrFail($validated['category_id']);

        $transaction = $request->user()
            ->transactions()
            ->create($validated);

        return response()->json(
            $transaction->load('category'),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Transaction $transaction)
    {
        
    if ($transaction->user_id !== $request->user()->id) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    return response()->json(
        $transaction->load('category')
    );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'amount' => 'sometimes|numeric|min:0.01',
            'type' => 'sometimes|in:income,expense',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'sometimes|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction->load('category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transacción eliminada']);
    }
}
