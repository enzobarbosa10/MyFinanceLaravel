<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'balance' => 'required|numeric',
            'type' => 'required|in:corrente,poupanca,dinheiro,outro',
        ]);

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'balance' => $request->balance,
            'type' => $request->type,
        ]);

        return redirect()->route('accounts.index')->with('success', 'Conta criada com sucesso!');
    }
}
