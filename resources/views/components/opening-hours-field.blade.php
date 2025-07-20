<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="fi-fo-opening-hours">
        {{ $getChildComponentContainer() }}
    </div>
</x-dynamic-component>