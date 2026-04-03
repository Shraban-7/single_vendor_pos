<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

if (!function_exists('apiResponse')) {
    function apiResponse(object|array $data, string|null $message = null, int $statusCode = 200,)
    {
        $response['status'] = true;

        if (isset($message)) $response['message'] = $message;
        if (!empty($data)) $response['data'] = $data;

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('successResponse')) {
    function successResponse(string $message, int $statusCode = 200)
    {
        $response['status'] = true;

        if (isset($message)) $response['message'] = $message;

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message, int $statusCode = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message ?? 'Something went wrong!',
        ], $statusCode);
    }
}

if (!function_exists('apiResourceResponse')) {
    function apiResourceResponse(object $collection, string|null $message = null, array $extraData = [], int $statusCode = 200)
    {
        $response['status'] = true;
        if (isset($message)) $response['message'] = $message;
        if (!empty($extraData)) $response['extraData'] = $extraData;

        if (!empty($collection)) {
            $collection = $collection->additional($response)->response()->getData();
        }

        return response()->json($collection, $statusCode);
    }
}

if (!function_exists('str_slug')) {
    function str_slug($title)
    {
        return Str::slug($title);
    }
}

if (!function_exists('sendValidationError')) {
    function sendValidationError($errors)
    {
        return response()->json([
            'status' => false,
            'message' => $errors->first()
        ], 422);
    }
}

if (!function_exists('validateRequest')) {
    function validateRequest(Request $request, array $rules)
    {
        return Validator::make($request->all(), $rules);
    }
}

if (!function_exists('upload_file')) {
    function upload_file($file, $directory, $disk = 'public')
    {
        if (!Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }

        $fileName =  time() . rand(1, 9999) . '.' . $file->extension();
        $path = $directory . '/' . $fileName;

        Storage::disk($disk)->put($path, File::get($file));

        return $path;
    }
}

if (!function_exists('storage_url')) {
    function storage_url($file, $disk = 'public')
    {
        return Storage::disk($disk)->url($file);
    }
}

if (!function_exists('delete_file')) {
    function delete_file($file)
    {
        Storage::disk('public')->delete($file);
    }
}

if (!function_exists('isValidUsername')) {
    function isValidUsername(string $username): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{6,40}$/', $username) === 1;
    }
}

if (!function_exists('currency')) {
    function currency($key)
    {
        $currency = array(
            'name' => 'BDT',
            'symbol' => '৳',
        );

        return $currency[$key];
    }
}
if (!function_exists('money')) {
    function money($amount, $currencyType = 'symbol')
    {
        $money = number_format($amount, 2);
        $decimal = explode('.', $money);
        if ($decimal[1] == '00') {
            $money = str_replace('.00', '', $money);
        }
        return currency($currencyType) . $money;
    }
}
if (!function_exists('convertBengaliToEnglishDigits')) {
    function convertBengaliToEnglishDigits($input)
    {
        $bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($bengaliDigits, $englishDigits, $input);
    }
}
if (!function_exists('maskString')) {
    function maskString($string, $startFrom = 4)
    {
        $lastFour = mb_substr($string, -$startFrom, null, 'UTF-8');
        $totalDigits = mb_strlen($string, 'UTF-8');
        $maskedPart = str_repeat('*', $totalDigits - $startFrom);
        $maskedString = $maskedPart . $lastFour;

        return $maskedString;
    }
}

// Toast Notification Helpers
if (!function_exists('toast_success')) {
    /**
     * Flash a success toast message
     */
    function toast_success($message)
    {
        session()->flash('success', $message);
    }
}

if (!function_exists('toast_error')) {
    /**
     * Flash an error toast message
     */
    function toast_error($message)
    {
        session()->flash('error', $message);
    }
}

if (!function_exists('toast_warning')) {
    /**
     * Flash a warning toast message
     */
    function toast_warning($message)
    {
        session()->flash('warning', $message);
    }
}

if (!function_exists('toast_info')) {
    /**
     * Flash an info toast message
     */
    function toast_info($message)
    {
        session()->flash('info', $message);
    }
}

if (!function_exists('isMobile')) {
    /**
     * Check if the user is on a mobile device
     */
    function isMobile()
    {
        $userAgent = request()->header('User-Agent');
        $mobileAgents = ['iPhone', 'iPad', 'iPod', 'Android', 'webOS', 'BlackBerry', 'Windows Phone'];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        return false;
    }
}

