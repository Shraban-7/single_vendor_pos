<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function ipn(Request $request)
    {
        Log::info('Received IPN:', $request->all());

        $order = Order::where('id', $request->order_id)->first();

        if (!$order) {
            Log::error('Order not found for IPN:', ['order_id' => $request->order_id]);
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $order->payment_id = $request->payment_id;
        $order->payment_status = $request->status;
        $order->payment_method = PaymentMethod::ONLINE->value;
        $order->payment_method_name = $request->payment_method;

        if ($request->status == PaymentStatus::COMPLETED->value) {
            $order->paid_at = now();
        }

        $order->save();

        return response()->json(['status' => 'success'], 200);
    }

    public function success(Request $request)
    {
        $order = Order::where('payment_id', $request->payment_id)->first();

        if (!$order) {
            Log::error('Order not found for success callback:', ['payment_id' => $request->payment_id]);
            return view('errors.404');
        }

        if ($order->user_id) {
            Auth::loginUsingId($order->user_id);
        }

        return view('payment.success', compact('order'));
    }

    public function cancelled(Request $request)
    {
        $order = Order::where('payment_id', $request->payment_id)->first();

        if (!$order) {
            Log::error('Order not found for cancelled callback:', ['payment_id' => $request->payment_id]);
            return view('errors.404');
        }

        $order->payment_status = PaymentStatus::CANCELLED->value;
        $order->status = OrderStatus::CANCELLED->value;
        $order->save();

        if ($order->user_id) {
            Auth::loginUsingId($order->user_id);
        }

        return view('payment.cancelled');
    }

    public function failed(Request $request)
    {
        $order = Order::where('payment_id', $request->payment_id)->first();

        if (!$order) {
            Log::error('Order not found for failed callback:', ['payment_id' => $request->payment_id]);
            return view('errors.404');
        }

        if ($order->user_id) {
            Auth::loginUsingId($order->user_id);
        }

        $order->payment_status = PaymentStatus::FAILED->value;
        $order->save();
        
        return view('payment.failed');
    }
}
