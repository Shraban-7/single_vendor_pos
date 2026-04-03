@props([
    'id', 
    'size' => 'md', 
    'title' => null
])

@php
    $maxWidth = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-5xl',
        'full' => 'max-w-full h-full',
    ][$size] ?? 'max-w-lg';

    $isFull = $size === 'full';
@endphp

<div 
    id="{{ $id }}" 
    class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden-modal"
    role="dialog"
    aria-modal="true"
>
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('{{ $id }}')"></div>
    
    <div class="modal-container bg-white w-full {{ $maxWidth }} {{ $isFull ? '' : 'mx-4 md:mx-auto rounded-xl shadow-2xl' }} z-50 flex flex-col overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between {{ $isFull ? 'sticky top-0 bg-white' : 'bg-gray-50/50' }}">
            <h3 class="text-lg font-bold text-gray-800">
                {{ $title ?? $header ?? 'Notification' }}
            </h3>
            <button onclick="toggleModal('{{ $id }}')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-grow">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>