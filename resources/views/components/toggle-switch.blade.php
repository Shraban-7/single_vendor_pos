@props(['name', 'label' => null, 'checked' => false])

<div class="flex items-center justify-between">
    @if($label)
        <span class="flex-grow flex flex-col">
            <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
        </span>
    @endif
    
    <button 
        type="button" 
        role="switch" 
        x-data="{ enabled: {{ $checked ? 'true' : 'false' }} }"
        @click="enabled = !enabled"
        :class="enabled ? 'bg-blue-600' : 'bg-gray-200'"
        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        <input type="hidden" name="{{ $name }}" :value="enabled ? 1 : 0">
        <span 
            aria-hidden="true" 
            :class="enabled ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
        ></span>
    </button>
</div>