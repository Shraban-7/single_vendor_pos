@props(['name', 'label' => null, 'rows' => 3, 'placeholder' => ''])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif
    
    <textarea 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}" 
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 rounded-lg
            bg-white 
            border border-gray-200 
            text-gray-900 
            placeholder-gray-400
            transition duration-200 ease-in-out
            focus:outline-none
            focus:border-blue-500
            disabled:opacity-50 disabled:cursor-not-allowed']) }}
    >{{ $slot }}</textarea>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>