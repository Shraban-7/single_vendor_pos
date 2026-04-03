<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => $request->email]
        );

        if (!$subscriber->wasRecentlyCreated) {
            return back()->with('success', 'You are already subscribed!');
        }

        return back()->with('success', 'Thank you for subscribing!');
    }
}
