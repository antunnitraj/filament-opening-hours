@php
    $businessData = $getBusinessHoursData();
    $displayMode = $getDisplayMode();
    $showTooltips = $getShowTooltips();
@endphp

<div class="fi-ta-opening-hours-column">
    @if ($displayMode === 'circular')
        <!-- Clean Status Display -->
        <div class="flex flex-col items-center space-y-2 p-2">
            <!-- Main Status Badge -->
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full
                    @if ($businessData['is_open']) 
                        bg-green-500
                    @elseif ($businessData['status'] === 'not_configured' || $businessData['status'] === 'disabled')
                        bg-gray-400
                    @else
                        bg-red-500
                    @endif"
                ></div>
                <span class="text-sm font-medium
                    @if ($businessData['is_open'])
                        text-green-700 dark:text-green-400
                    @elseif ($businessData['status'] === 'not_configured' || $businessData['status'] === 'disabled' || $businessData['status'] === 'error')
                        text-gray-600 dark:text-gray-400
                    @else
                        text-red-700 dark:text-red-400
                    @endif">
                    @if ($businessData['is_open'])
                        {{ __('filament-opening-hours::opening-hours.open_status') }}
                    @elseif ($businessData['status'] === 'not_configured')
                        {{ __('filament-opening-hours::opening-hours.not_configured') }}
                    @elseif ($businessData['status'] === 'disabled')
                        {{ __('filament-opening-hours::opening-hours.disabled') }}
                    @elseif ($businessData['status'] === 'error')
                        {{ __('filament-opening-hours::opening-hours.error') }}
                    @else
                        {{ __('filament-opening-hours::opening-hours.closed_status') }}
                    @endif
                </span>
            </div>
            
            <!-- Today's Hours -->
            @php
                $today = strtolower(now()->format('l'));
                $todayHours = $businessData['weekly_hours'][$today] ?? ['formatted' => 'Closed'];
            @endphp
            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                {{ $todayHours['formatted'] }}
            </div>
            
            @if ($showTooltips)
                <div class="sr-only">{{ $businessData['current_status'] }}</div>
            @endif
        </div>
        
    @elseif ($displayMode === 'status')
        <!-- Compact Status Badge -->
        <div class="flex items-center justify-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if ($businessData['is_open'])
                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif ($businessData['status'] === 'not_configured')
                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                @else
                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @endif"
                @if ($showTooltips && isset($businessData['next_open']) && isset($businessData['next_close']))
                    title="{{ __('filament-opening-hours::opening-hours.next') }}: {{ $businessData['is_open'] ? __('filament-opening-hours::opening-hours.closes_at') . ' ' . $businessData['next_close'] : __('filament-opening-hours::opening-hours.opens_at') . ' ' . $businessData['next_open'] }}"
                @endif
            >
                @if ($businessData['is_open'])
                    {{ __('filament-opening-hours::opening-hours.open_status') }}
                @elseif ($businessData['status'] === 'not_configured')
                    {{ __('filament-opening-hours::opening-hours.not_configured') }}
                @elseif ($businessData['status'] === 'disabled')
                    {{ __('filament-opening-hours::opening-hours.disabled') }}
                @else
                    {{ __('filament-opening-hours::opening-hours.closed_status') }}
                @endif
            </span>
        </div>
        
    @elseif ($displayMode === 'weekly')
        <!-- Weekly Overview Display -->
        <div class="flex items-center space-x-1">
            @php
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            @endphp
            @foreach ($days as $day)
                @php
                    $dayData = $businessData['weekly_hours'][$day] ?? ['is_open' => false, 'formatted' => 'Closed'];
                    $dayLabel = ucfirst($day);
                @endphp
                <div class="w-3 h-3 rounded-sm {{ $dayData['is_open'] ? 'bg-green-500' : 'bg-red-300' }}"
                    @if ($showTooltips)
                        title="{{ $dayLabel }}: {{ $dayData['formatted'] }}"
                    @endif
                ></div>
            @endforeach
        </div>
        
        <!-- Current status indicator -->
        <div class="mt-1">
            <div class="w-2 h-2 rounded-full mx-auto
                @if ($businessData['is_open'])
                    bg-green-500 animate-pulse
                @elseif ($businessData['status'] === 'not_configured')
                    bg-gray-400
                @else
                    bg-red-500
                @endif"
                @if ($showTooltips)
                    title="{{ $businessData['current_status'] }}"
                @endif
            ></div>
        </div>
    @endif
</div>

<style>
.fi-ta-opening-hours-column {
    min-width: 120px;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>