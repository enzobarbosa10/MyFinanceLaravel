<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentAsset;
use App\Models\InvestmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function index()
    {
        $investments = Investment::with('asset.type')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($inv) {
                $inv->total_value = $inv->totalValue();
                return $inv;
            });

        $totalInvestido = $investments->sum('total_value');

        return view('investments.index', compact('investments', 'totalInvestido'));
    }

    public function create()
    {
        $types = InvestmentType::with('assets')->get();
        return view('investments.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:investment_assets,id',
            'quantity' => 'required|numeric|min:0.0001',
            'purchase_price' => 'required|numeric|min:0.0001',
        ]);

        Investment::create([
            'user_id' => Auth::id(),
            'asset_id' => $request->asset_id,
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
        ]);

        return redirect()->route('investments.index')->with('success', 'Investimento registrado!');
    }

    public function destroy(Request $request)
    {
        Investment::where('id', $request->id)->where('user_id', Auth::id())->delete();
        return redirect()->route('investments.index')->with('success', 'Investimento removido.');
    }

    public function assetsByType(Request $request)
    {
        $typeId = $request->get('type_id');
        $assets = InvestmentAsset::where('type_id', $typeId)->get(['id', 'name', 'symbol']);
        return response()->json($assets);
    }
}
