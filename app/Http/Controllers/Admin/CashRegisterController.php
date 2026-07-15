<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    public function open(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0'
        ]);

        CashRegister::whereNull('closed_at')
            ->update([
                'closed_at' => now(),
                'closing_amount' => DB::raw('opening_amount'),
            ]);

        $register = CashRegister::create([
            'opened_at' => now(),
            'opening_amount' => $request->opening_amount
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'Register opened successfully');
    }

    public function close(Request $request, CashRegister $register)
    {
        $request->validate([
            'closing_amount' => 'nullable|numeric|min:0',
        ]);

        [$start, $end] = businessDayRange();

        $salesTotal = Sale::whereNull('user_id')->whereBetween('created_at', [$start, $end])->sum('paid');
        $expense = Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $salesReturns = SaleReturn::whereBetween('created_at', [$start, $end])->sum('refund_amount');
        $expectedCash = $register->opening_amount + $salesTotal - $salesReturns;

        $closed_at = $register->closed_at ? null : now();
        $closing_amount = $register->closing_amount ? null : $request->closing_amount;
        $difference = $request->closing_amount ? ($request->closing_amount - $expectedCash) : 0;

        $register->update([
            'sales' => $salesTotal,
            'sale_returns' => $salesReturns,
            'expenses' => $expense,
            'difference' => $difference,
            'closed_at' => $closed_at,
            'closing_amount' => $closing_amount,
        ]);

        return redirect()->back()->with('success', 'Cash register close successfully');
    }
}
