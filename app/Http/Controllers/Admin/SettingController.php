<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        
        $groupedSettings = [];
        foreach ($settings as $group => $groupSettings) {
            $groupedSettings[$group] = $groupSettings->mapWithKeys(function ($setting) {
                return [$setting->key => [
                    'value' => $setting->value,
                    'type' => $setting->type,
                ]];
            })->toArray();
        }

        return view('admin.settings.index', [
            'all_settings' => $groupedSettings,
            'groups' => $settings->keys(),
        ]);
    }

    public function update(Request $request)
    {
        $allSettings = Setting::all();

        $rules = [];
        foreach ($allSettings as $setting) {
            if ($setting->type === 'number' || $setting->type === 'integer') {
                $rules[$setting->key] = 'nullable|numeric';
            } elseif ($setting->type === 'boolean') {
                $rules[$setting->key] = 'nullable|in:true,false,1,0';
            } elseif ($setting->type === 'file') {
                $rules[$setting->key] = 'nullable|image|mimes:jpg,jpeg,png,ico,svg|max:2048';
            } else {
                $rules[$setting->key] = 'nullable|string';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.settings.index')
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($allSettings as $setting) {
            if ($request->has($setting->key)) {
                $value = $request->input($setting->key);

                if ($setting->type === 'boolean') {
                    $value = in_array($value, ['true', '1', 1, true]) ? 'true' : 'false';
                }

                if ($setting->type === 'file') {
                    if ($request->hasFile($setting->key)) {
                        $value = upload_file($request->file($setting->key), 'settings');
                    } elseif ($request->input($setting->key) === null) {
                        $value = $setting->value;
                    }
                }

                Setting::set($setting->key, $value, $setting->type, $setting->group);
            } elseif ($setting->type === 'boolean') {
                Setting::set($setting->key, 'false', $setting->type, $setting->group);
            }
        }

        Setting::clearCache();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
