@props(['name', 'label', 'value', 'selected' => null])

<div class="flex items-center">
    <input 
        id="radio_{{ $name . '_' . $value }}" 
        name="{{ $name }}" 
        type="radio" 
        value="{{ $value }}"
        {{ $value == $selected ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 transition duration-150']) }}
    >
    <label for="radio_{{ $name . '_' . $value }}" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
        {{ $label }}
    </label>
</div>