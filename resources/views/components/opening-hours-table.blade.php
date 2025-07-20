<div class="fi-ta-opening-hours">
    <div class="flex items-center gap-2">
        @php
            $status = $getFormattedState();
            $color = $getStatusColor();
        @endphp
        
        <div class="flex items-center gap-1">
            <div class="h-2 w-2 rounded-full bg-{{ $color }}-500"></div>
            <span class="text-sm font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400">
                {{ $status }}
            </span>
        </div>
    </div>
</div>