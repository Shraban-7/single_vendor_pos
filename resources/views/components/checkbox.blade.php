@props(['name', 'label' => null, 'value' => 1, 'checked' => false])

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input
            id="{{ $name . '_' . $value }}"
            name="{{ $name }}"
            type="checkbox"
            value="{{ $value }}"
            {{ $checked ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition duration-150']) }}>
    </div>
    @if($label)
    <div class="ml-3 text-sm">
        <label for="{{ $name . '_' . $value }}" class="font-medium text-gray-700 cursor-pointer">{{ $label }}</label>
    </div>
    @endif
</div>

<!-- Example -->
{{--<div class="space-y-3">
    <label class="text-sm font-bold text-gray-700">Notification Frequency</label>
    <x-radio name="notify_level" value="all" label="All Notifications" />
    <x-radio name="notify_level" value="mentions" label="Mentions Only" />
    <x-radio name="notify_level" value="none" label="Mute All" />
</div> --}}