<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Calculate Stats
        $suppliersForStats = Supplier::where('user_id', $userId)->select('current_balance', 'is_active')->get();
        $stats = [
            'total' => $suppliersForStats->count(),
            'active' => $suppliersForStats->where('is_active', true)->count(),
            'due' => $suppliersForStats->where('current_balance', '>', 0)->sum('current_balance'),
            'due_receivers' => $suppliersForStats->where('current_balance', '>', 0)->count()
        ];

        $query = Supplier::query()->where('user_id', $userId);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('supplier_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('filter')) {
            $filter = $request->filter;
            if ($filter === 'due') {
                $query->where('current_balance', '>', 0);
            } elseif ($filter === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        }

        $suppliers = $query->latest()->paginate(15);

        return view('admin.suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'product_category' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'supplier_code' => 'nullable|string|max:50|unique:suppliers,supplier_code',
        ]);

        $user = Auth::user();

        $alreadyExists = Supplier::where('name', $request->input('name'))
            ->where('phone', $request->input('phone'))
            ->where('user_id', $user->id)
            ->first();

        if ($alreadyExists) {
            return back()->withInput()->with('error', 'You have already added this supplier!');
        }

        $data = $validated;
        $data['user_id'] = $user->id;

        if (empty($data['supplier_code'])) {
            $userId = $user->id;
            $nextId = Supplier::where('user_id', $userId)->count() + 1;
            $supplierCode = 'SUP-' . $userId . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            while (Supplier::where('supplier_code', $supplierCode)->exists()) {
                $nextId++;
                $supplierCode = 'SUP-' . $userId . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            $data['supplier_code'] = $supplierCode;
        }

        if ($request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'), 'suppliers');
        }

        $data['opening_balance'] = $request->input('opening_balance', 0.00);
        $data['current_balance'] = $data['opening_balance'];
        $data['total_purchases'] = 0.00;
        $data['total_paid'] = 0.00;
        $data['is_active'] = $request->has('is_active');

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully!');
    }

    public function edit($id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'supplier_code' => 'nullable|string|max:50|unique:suppliers,supplier_code,' . $id,
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            if ($supplier->image) {
                delete_file($supplier->image);
            }
            $data['image'] = upload_file($request->file('image'), 'suppliers');
        }

        if ($request->has('opening_balance')) {
            $oldOpening = (float) $supplier->opening_balance;
            $newOpening = (float) $request->input('opening_balance');
            $diff = $newOpening - $oldOpening;
            $data['current_balance'] = (float) $supplier->current_balance + $diff;
        }

        $data['is_active'] = $request->has('is_active');

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    public function destroy($id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        $hasPurchases = DB::table('purchases')->where('supplier_id', $id)->exists();
        if ($hasPurchases) {
            return back()->with('error', 'Cannot delete supplier with associated purchase records.');
        }

        $hasPayments = DB::table('payments')->where('supplier_id', $id)->exists();
        if ($hasPayments) {
            return back()->with('error', 'Cannot delete supplier with associated payment records.');
        }

        if ($supplier->image) {
            delete_file($supplier->image);
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully!');
    }

    public function makePayment(Request $request, $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'purchase_id' => 'nullable|exists:purchases,id',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];
        $purchaseId = $validated['purchase_id'];

        if ($purchaseId) {
            $purchase = Purchase::where('id', $purchaseId)->where('supplier_id', $id)->first();
            if (!$purchase) {
                return back()->with('error', 'Purchase not found for this supplier.');
            }

            if ($amount > $purchase->due_amount) {
                return back()->with('error', 'Payment amount exceeds the due amount for this purchase.');
            }

            $purchase->paid_amount += $amount;
            $purchase->due_amount -= $amount;
            $purchase->save();
        }

        Payment::create([
            'user_id' => Auth::id(),
            'payment_number' => Payment::generatePaymentNumber(),
            'payment_type' => $purchaseId ? PaymentType::PURCHASE : PaymentType::OTHER,
            'reference_type' => $purchaseId ? Purchase::class : null,
            'reference_id' => $purchaseId,
            'supplier_id' => $id,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        $supplier->current_balance -= $amount;
        $supplier->total_paid += $amount;
        $supplier->save();

        return back()->with('success', 'Payment made successfully!');
    }
}
