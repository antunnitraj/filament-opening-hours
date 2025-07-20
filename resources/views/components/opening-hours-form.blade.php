<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }" class="fi-fo-opening-hours">
        {{ $getChildComponentContainer() }}
    </div>
</x-dynamic-component>