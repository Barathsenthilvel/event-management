@props([
    'name',
    'required' => false,
    'disabled' => false,
])

<div class="mp-select-field">
    <select
        name="{{ $name }}"
        @if($required) required @endif
        class="mp-searchable-select"
        @disabled($disabled)
        {{ $attributes->whereStartsWith('data-') }}
    >
        {{ $slot }}
    </select>
</div>
