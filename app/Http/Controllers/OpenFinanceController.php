<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\OpenFinanceService;
use Illuminate\Http\Request;

class OpenFinanceController extends Controller
{
    public function __construct(
        private OpenFinanceService $openFinance,
    ) {}

    public function index(Request $request)
    {
        $linkedAccounts = Account::where('user_id', $request->user()->id)
            ->whereNotNull('open_finance_item_id')
            ->get();

        return view('open-finance.index', compact('linkedAccounts'));
    }

    public function connectToken(Request $request)
    {
        $token = $this->openFinance->createConnectToken(
            $request->user(),
            $request->input('item_id'),
        );

        return response()->json(['accessToken' => $token]);
    }
}
