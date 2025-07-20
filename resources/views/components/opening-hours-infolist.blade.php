<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="fi-in-opening-hours">
        @if ($getShowStatus())
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex items-center gap-2">
                    @php
                        $status = $getCurrentStatus();
                        $isOpen = str_contains(strtolower($status), 'open');
                        $color = $isOpen ? 'success' : 'danger';
                    @endphp
                    
                    <div class="h-3 w-3 rounded-full bg-{{ $color }}-500"></div>
                    <span class="text-sm font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                        {{ $status }}
                    </span>
                </div>
            </div>
        @endif

        <div class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Regular Hours</h4>
            
            @foreach ($getFormattedOpeningHours() as $day => $hours)
                <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $day }}</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $hours }}</span>
                </div>
            @endforeach
        </div>

        @if ($getShowExceptions() && !empty($getExceptions()))
            <div class="mt-6 space-y-2">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Exceptions</h4>
                
                @foreach ($getExceptions() as $date => $hours)
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}
                        </span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $hours }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dynamic-component>