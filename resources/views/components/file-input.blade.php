@props(['label' => null, 'name' => ''])

<div class="w-full">
    @if($label)
    <label class="block mb-2 text-sm font-medium text-gray-900" for="{{ $name }}" >
        {{ $label }}
    </label>
    @endif
    <div class="flex items-center justify-center w-full">
        <label for="{{ $name }}"
            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed 
        rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all border-spacing-4">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="mb-1 text-sm text-gray-600 font-medium">Click to upload logo</p>
                <p class="text-xs text-gray-400">PNG, JPG or SVG</p>
            </div>
            <input id="{{ $name }}" name="{{ $name }}" type="file" class="hidden" {{ $attributes }} />
        </label>
    </div>
</div>