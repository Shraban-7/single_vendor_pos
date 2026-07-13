<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->latest()->paginate(25)->appends($request->all());

        $categories = ExpenseCategory::get();

        $totalExpense = Expense::sum('amount');

        $monthlyExpense = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');


        return view('admin.expenses.index', compact(
            'expenses',
            'categories',
            'totalExpense',
            'monthlyExpense'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        // dd($request->all());

        $categoryId = $this->getOrCreateCategory($request->category_id);

        Expense::create([
            'category_id' => $categoryId,
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        $categoryId = $this->getOrCreateCategory($request->category_id);

        $expense->update([
            'category_id' => $categoryId,
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    private function getOrCreateCategory($categoryInput)
    {
        if (is_numeric($categoryInput)) {
            return (int) $categoryInput;
        }

        $category = ExpenseCategory::firstOrCreate(
            ['name' => $categoryInput],
            ['name' => $categoryInput]
        );

        return $category->id;
    }
}
