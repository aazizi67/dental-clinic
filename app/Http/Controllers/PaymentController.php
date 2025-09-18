<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return redirect()->route('accounting.transactions');
    }

    public function create()
    {
        return redirect()->route('accounting.transactions.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('accounting.transactions.create');
    }

    public function show($id)
    {
        return redirect()->route('accounting.transactions');
    }

    public function edit($id)
    {
        return redirect()->route('accounting.transactions');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('accounting.transactions');
    }

    public function destroy($id)
    {
        return redirect()->route('accounting.transactions');
    }

    public function reports()
    {
        return redirect()->route('accounting.reports');
    }
}
