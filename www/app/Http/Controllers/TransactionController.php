<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Client;
use App\Support\DataTables\TransactionDataTable;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(TransactionDataTable $dataTable)
    {
        $clients = Client::all();
        // Os totais poderiam ser processados via Service para analytics.
        $totalIncome = Transaction::where('type', 'income')->where('status', 'paid')->sum('amount');
        $totalPending = Transaction::where('type', 'income')->where('status', 'pending')->sum('amount');

        return $dataTable->render('transactions.index', compact('clients', 'totalIncome', 'totalPending'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('transactions.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'status' => 'required|in:pending,paid',
            'payment_method' => 'required|string',
            'due_date' => 'nullable|date',
            'gateway' => 'required|string'
        ]);

        if ($data['status'] === 'paid') {
            $data['paid_at'] = now();
        }

        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', 'Lançamento financeiro contabilizado no livro-caixa!');
    }

    public function edit(Transaction $transaction)
    {
        $clients = Client::orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'clients'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'status' => 'required|in:pending,paid',
            'payment_method' => 'required|string',
            'due_date' => 'nullable|date',
            'gateway' => 'required|string'
        ]);

        if ($data['status'] === 'paid' && $transaction->status !== 'paid') {
            $data['paid_at'] = now();
        } elseif ($data['status'] === 'pending') {
            $data['paid_at'] = null;
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Lançamento modificado e reprocessado.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete(); // SoftDeletes preserva a integridade original permitindo recuperar direto no banco.
        return redirect()->route('transactions.index')->with('success', 'Estorno Administrativo (SoftDelete) processado no Lançamento.');
    }

    /**
     * Endpoint API JSON interno para renderizar o Chart.js na interface.
     */
    public function metrics(Request $request)
    {
        $currentYear = now()->year;

        // Faturamento Mês a Mês do ano atual
        $monthlyIncome = \DB::table('transactions')
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->where('type', 'income')
            ->where('status', 'paid')
            ->whereYear('paid_at', $currentYear)
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
            
        // Preenche com zero os meses faltantes
        $incomeData = [];
        for($i=1; $i<=12; $i++) {
            $incomeData[] = isset($monthlyIncome[$i]) ? (float)$monthlyIncome[$i]->total : 0.0;
        }

        // Faturamento por Gateway
        $gateways = \DB::table('transactions')
            ->selectRaw('gateway, SUM(amount) as total')
            ->where('type', 'income')
            ->where('status', 'paid')
            ->whereNull('deleted_at')
            ->groupBy('gateway')
            ->get();

        return response()->json([
            'monthly' => [
                'labels' => ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                'data' => $incomeData
            ],
            'gateways' => [
                'labels' => $gateways->pluck('gateway')->map(function($val) { return ucfirst($val); }),
                'data' => $gateways->pluck('total')
            ]
        ]);
    }
}
