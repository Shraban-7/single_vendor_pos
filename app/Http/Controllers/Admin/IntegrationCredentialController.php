<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\CourierProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IntegrationCredentialController extends Controller
{
    public function edit(string $type, string $slug): View
    {
        $model = $type === 'payment'
            ? PaymentGateway::whereSlug($slug)->firstOrFail()
            : CourierProvider::whereSlug($slug)->firstOrFail();

        return view('admin.integrations.credentials', [
            'integration' => $model,
            'type'        => $type,
        ]);
    }

    public function update(Request $request, string $type, string $slug): RedirectResponse
    {
        $model = $type === 'payment'
            ? PaymentGateway::whereSlug($slug)->firstOrFail()
            : CourierProvider::whereSlug($slug)->firstOrFail();

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'is_active'    => 'sometimes|boolean',
            'credentials'  => 'required|array',
            'credentials.*' => 'nullable|string',
        ]);

        $model->update($validated);

        return redirect()->back()->with('success', "{$model->name} credentials updated.");
    }
}
