<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Expense::with('category')->where('user_id', $userId);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('expense_date', [$request->from_date, $request->to_date]);
        }

        $totalAmount = (float) $query->sum('amount');
        $monthlyExpense = (float) Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.expenses.index', compact('expenses', 'totalAmount', 'monthlyExpense', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (empty($validated['title'])) {
            $validated['title'] = $request->input('description')
                ? substr($request->input('description'), 0, 255)
                : 'Expense';
        }

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = upload_file($request->file('receipt_image'), 'expenses');
        }

        Expense::create($validated);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Expense created successfully!']);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense created successfully!');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        $category = ExpenseCategory::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => ['id' => $category->id, 'name' => $category->name],
            ]);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Category created successfully!');
    }

    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);

        if ($expense->receipt_image) {
            delete_file($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
