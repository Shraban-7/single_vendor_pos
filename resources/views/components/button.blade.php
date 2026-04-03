@props([
    'variant' => 'primary',
    'type' => 'button',
    'size' => 'md',
    'pill' => false,
    'outline' => false,
])

<?php
    $baseClasses = "inline-flex items-center justify-center font-medium transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2";

    // Size Definitions
    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];

    // Solid Variants
    $solidColors = [
        'primary'   => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 border-transparent',
        'secondary' => 'bg-slate-600 text-white hover:bg-slate-700 focus:ring-slate-500 border-transparent',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border-transparent',
        'success'   => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 border-transparent',
        'warning'   => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500 border-transparent',
        'info'      => 'bg-sky-500 text-white hover:bg-sky-600 focus:ring-sky-500 border-transparent',
        'dark'      => 'bg-gray-900 text-white hover:bg-black focus:ring-gray-900 border-transparent',
    ];

    // Outline Variants
    $outlineColors = [
        'primary'   => 'border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
        'secondary' => 'border-slate-600 text-slate-600 hover:bg-slate-50 focus:ring-slate-500',
        'danger'    => 'border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500',
        'success'   => 'border-emerald-600 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-500',
        'warning'   => 'border-amber-500 text-amber-600 hover:bg-amber-50 focus:ring-amber-500',
        'info'      => 'border-sky-500 text-sky-600 hover:bg-sky-50 focus:ring-sky-500',
        'dark'      => 'border-gray-900 text-gray-900 hover:bg-gray-100 focus:ring-gray-900',
    ];

    // Select the correct style based on the 'outline' prop
    $colorClass = $outline 
        ? ($outlineColors[$variant] ?? $outlineColors['primary']) . " border"
        : ($solidColors[$variant] ?? $solidColors['primary']) . " border shadow-sm";

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $rounding = $pill ? 'rounded-full' : 'rounded-md';
?>

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $colorClass $sizeClass $rounding"]) }}>
    {{ $slot }}
</button>