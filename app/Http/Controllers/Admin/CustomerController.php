<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // ================= WEB USERS =================
        $webQuery = User::where('role', UserRole::CUSTOMER->value);

        if ($request->filled('web_search')) {
            $search = $request->web_search;

            $webQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        $webUsers = $webQuery->latest()->paginate(10)->appends($request->all());


        // ================= POS USERS =================
        $posQuery = Customer::query();

        if ($request->filled('pos_search')) {
            $search = $request->pos_search;

            $posQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        $posUsers = $posQuery->latest()->paginate(10)->appends($request->all());


        return view('admin.customers.index', compact('webUsers', 'posUsers'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Customer updated successfully!');
    }
}
