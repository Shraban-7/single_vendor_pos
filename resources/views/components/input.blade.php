@props(['disabled' => false, 'label' => null, 'name' => '', 'type' => 'text', 'id' => null])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 ">
            {{ $label }}
        </label>
    @endif
    <input 
        type="{{ $type }}" 
        id="{{ $id == null ? $name : $id }}" 
        name="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge([
            'class' => 'w-full px-4 py-2.5 rounded-lg
            bg-white
            border border-gray-200
            text-gray-900
            placeholder-gray-400 
            transition duration-200 ease-in-out
            focus:outline-none
            focus:border-blue-500
            disabled:opacity-50 disabled:cursor-not-allowed'
        ]) !!}
    >
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>