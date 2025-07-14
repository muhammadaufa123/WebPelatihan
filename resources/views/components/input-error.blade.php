@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
        <x-input-error :messages="['Your error message here']" class="mt-2" />
        @endforeach
    </ul>
@endif
