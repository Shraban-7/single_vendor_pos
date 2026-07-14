<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|size:11',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;

        $credentials = [
            'phone' => $phone,
            'password' => $request->password,
        ];

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or password.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_seen' => now(),
            ]);

            $userType = $user->role === UserRole::CUSTOMER ? 'Customer' : 'Admin';

            activity_log(
                action: 'login',
                model: Auth::user(),
                description: "Logged in as $userType"
            );

            return response()->json([
                'success' => true,
                'message' => 'Login successful! Welcome back.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role->value,
                ],
                'redirect' =>  route('admin.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid phone number or password.',
        ], 401);
    }

    public function logout(Request $request)
    {
        activity_log(
            action: 'logout',
            model: Auth::user(),
            description: "Logged out"
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Logged out successfully');

        return redirect()->route('login');
    }
}
