<?php

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

if (!function_exists('apiResponse')) {
    function apiResponse(object|array $data, string|null $message = null, int $statusCode = 200, )
    {
        $response['status'] = true;

        if (isset($message))
            $response['message'] = $message;
        if (!empty($data))
            $response['data'] = $data;

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('successResponse')) {
    function successResponse(string $message, int $statusCode = 200)
    {
        $response['status'] = true;

        if (isset($message))
            $response['message'] = $message;

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
        if (isset($message))
            $response['message'] = $message;
        if (!empty($extraData))
            $response['extraData'] = $extraData;

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

        $fileName = time() . rand(1, 9999) . '.' . $file->extension();
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
    function money($amount, $showCurrency = true, $currencyType = 'symbol')
    {
        $money = number_format($amount, 2);
        $decimal = explode('.', $money);
        if ($decimal[1] == '00') {
            $money = str_replace('.00', '', $money);
        }

        if (!$showCurrency) {
            return $money;
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

if (!function_exists('convert_number_to_words_bdt')) {
    function convert_number_to_words_bdt($number)
    {
        $number = (int) $number;

        $words = [
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
        ];

        $units = [
            '',
            'Thousand',
            'Lakh',
            'Crore',
        ];

        if ($number == 0) {
            return 'Zero';
        }

        $result = '';

        $numStr = str_pad($number, 9, '0', STR_PAD_LEFT);

        $crore = (int) substr($numStr, 0, 2);
        $lakh = (int) substr($numStr, 2, 2);
        $thousand = (int) substr($numStr, 4, 2);
        $hundred = (int) substr($numStr, 6, 1);
        $rest = (int) substr($numStr, 7, 2);

        if ($crore) {
            $result .= number_to_words_bdt($crore, $words) . ' Crore ';
        }
        if ($lakh) {
            $result .= number_to_words_bdt($lakh, $words) . ' Lakh ';
        }
        if ($thousand) {
            $result .= number_to_words_bdt($thousand, $words) . ' Thousand ';
        }
        if ($hundred) {
            $result .= $words[$hundred] . ' Hundred ';
        }
        if ($rest) {
            $result .= ($result != '' ? 'and ' : '') . number_to_words_bdt($rest, $words);
        }

        return trim($result);
    }

    function number_to_words_bdt($num, $words)
    {
        if ($num < 21) {
            return $words[$num];
        } else {
            $tens = ((int) ($num / 10)) * 10;
            $units = $num % 10;
            return $words[$tens] . ($units ? ' ' . $words[$units] : '');
        }
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
    }
}


if (!function_exists('businessDayRange')) {

    function businessDayRange()
    {
        $now = Carbon::now();

        $startTime = setting('business_day_start','00:00'); 
        $endTime = setting('business_day_end','23:59');   

        $start = Carbon::today()->setTimeFromTimeString($startTime);
        $end = Carbon::today()->setTimeFromTimeString($endTime);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        if ($now->lessThan($start)) {
            $start->subDay();
            $end->subDay();
        }

        return [$start, $end];
    }
}

if (!function_exists('set_image')) {
    function set_image($image = null)
    {
        if ($image) {
            return storage_url($image);
        }

        return asset('assets/images/default.png');
    }
}

if (! function_exists('activity_log')) {
    function activity_log(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void {
        \App\Services\ActivityLogger::log($action, $model, $oldValues, $newValues, $description);
    }
}


if (!function_exists('model_changes')) {
function model_changes($model, array $newData): array {
        $changes = [
            'old' => [],
            'new' => [],
        ];

        foreach ($newData as $field => $value) {
            if ($model->$field != $value) {
                $changes['old'][$field] = $model->$field;
                $changes['new'][$field] = $value;
            }
        }

        return $changes;
    }
}

if (!function_exists('settings')) {
    function settings(string $key, $default = null)
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $value = Setting::where('key', $key)->value('value');

        return $cache[$key] = $value ?? $default;
    }
}

