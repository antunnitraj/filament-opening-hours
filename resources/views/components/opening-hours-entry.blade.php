<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $data = $getBusinessHoursData();
        $displayMode = $getDisplayMode();
        $showStatus = $getShowStatus();
        $showExceptions = $getShowExceptions();
        $showTimezone = $getShowTimezone();
    @endphp
    
    <div class="fi-in-opening-hours space-y-6">
        @if ($displayMode === 'full' || $displayMode === 'status')
            @if ($showStatus)
                <!-- Current Status Section -->
                <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-gradient-to-r from-gray-50 to-white p-4 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">{{ $getStatusIcon() }}</span>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $data['current_status'] }}
                                </p>
                                @if ($showTimezone && isset($data['timezone']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        🌍 {{ str_replace('_', ' ', $data['timezone']) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        @if ($data['status'] !== 'not_configured' && $data['status'] !== 'error')
                            <div class="text-right">
                                @if ($data['is_open'] && isset($data['next_close']))
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('filament-opening-hours::opening-hours.closes_at') }}:</span><br>
                                        {{ $data['next_close'] }}
                                    </p>
                                @elseif (!$data['is_open'] && isset($data['next_open']))
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('filament-opening-hours::opening-hours.opens_at') }}:</span><br>
                                        {{ $data['next_open'] }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    @if ($data['status'] !== 'not_configured' && $data['status'] !== 'error')
                        <!-- Status indicator line -->
                        <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div 
                                class="h-full rounded-full transition-all duration-500 {{ $data['is_open'] ? 'bg-green-500' : 'bg-red-500' }}"
                                style="width: {{ $data['is_open'] ? '100%' : '0%' }}"
                            ></div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        @if ($displayMode === 'full' || $displayMode === 'weekly')
            @if (!empty($data['weekly_hours']))
                <!-- Weekly Schedule Section -->
                <div class="space-y-3">
                    <h4 class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        📅 {{ __('filament-opening-hours::opening-hours.weekly_schedule') }}
                    </h4>
                    
                    <div class="grid gap-2">
                        @foreach ($data['weekly_hours'] as $day => $dayData)
                            <div class="flex items-center justify-between rounded-lg border px-3 py-2 {{ $dayData['is_today'] ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                                <div class="flex items-center space-x-3">
                                    @if ($dayData['is_today'])
                                        <span class="flex h-2 w-2">
                                            <span class="absolute inline-flex h-2 w-2 animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                        </span>
                                    @else
                                        <div class="h-2 w-2 rounded-full {{ $dayData['is_open'] ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                    @endif
                                    <span class="text-sm font-medium {{ $dayData['is_today'] ? 'text-blue-900 dark:text-blue-100' : 'text-gray-900 dark:text-white' }}">
                                        {{ $dayData['label'] }}
                                        @if ($dayData['is_today'])
                                            <span class="ml-1 text-xs text-blue-600 dark:text-blue-400">({{ __('filament-opening-hours::opening-hours.today') }})</span>
                                        @endif
                                    </span>
                                </div>
                                <span class="text-sm {{ $dayData['is_open'] ? ($dayData['is_today'] ? 'text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-white') : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $dayData['formatted'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if ($displayMode === 'full' || $displayMode === 'compact')
            @if ($showExceptions && !empty($data['exceptions']))
                <!-- Exceptions Section -->
                <div class="space-y-3">
                    <h4 class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        🎯 {{ __('filament-opening-hours::opening-hours.exceptions_special_hours') }}
                        <span class="ml-2 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            {{ count($data['exceptions']) }}
                        </span>
                    </h4>
                    
                    <div class="space-y-2">
                        @foreach ($data['exceptions'] as $exception)
                            <div class="flex items-start justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm">
                                            @switch($exception['type'])
                                                @case('holiday')
                                                    🎉
                                                    @break
                                                @case('closed')
                                                    🔒
                                                    @break
                                                @case('special_hours')
                                                    ⏰
                                                    @break
                                                @case('maintenance')
                                                    🔧
                                                    @break
                                                @case('event')
                                                    🎈
                                                    @break
                                                @default
                                                    📅
                                            @endswitch
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $exception['formatted_date'] }}
                                        </span>
                                        @if ($exception['date_mode'] === 'range')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                                📆 {{ __('filament-opening-hours::opening-hours.range_badge') }}
                                            </span>
                                        @elseif ($exception['date_mode'] === 'recurring')
                                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                                🔄 {{ __('filament-opening-hours::opening-hours.annual_badge') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                📅 {{ __('filament-opening-hours::opening-hours.single_badge') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if ($exception['label'])
                                        <p class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $exception['label'] }}
                                        </p>
                                    @endif
                                    
                                    @if ($exception['note'])
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $exception['note'] }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="ml-4 text-right">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        {{ $exception['type'] === 'special_hours' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ __('filament-opening-hours::opening-hours.' . $exception['type']) }}
                                    </span>
                                    @if ($exception['formatted_hours'] !== 'Closed')
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            {{ $exception['formatted_hours'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if ($displayMode === 'compact')
            <!-- Compact Summary -->
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ __('filament-opening-hours::opening-hours.operating_days') }}:
                    </span>
                    <span class="font-medium text-gray-900 dark:text-white">
                        {{ collect($data['weekly_hours'])->where('is_open', true)->count() }}/7
                    </span>
                </div>
                @if (isset($data['last_updated']))
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('filament-opening-hours::opening-hours.last_updated') }}: {{ $data['last_updated'] }}
                    </div>
                @endif
            </div>
        @endif

        @if (isset($data['error']))
            <!-- Error State -->
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-center space-x-2">
                    <span class="text-red-500">⚠️</span>
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ __('filament-opening-hours::opening-hours.error_loading_hours') }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                    {{ $data['error'] }}
                </p>
            </div>
        @endif
    </div>
</x-dynamic-component>

<style>
.fi-in-opening-hours h4 {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping {
    animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>