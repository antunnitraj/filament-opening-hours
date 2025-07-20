@php
    $businessData = $getBusinessHoursData();
    $displayMode = $getDisplayMode();
    $showTooltips = $getShowTooltips();
@endphp

<div class="fi-ta-opening-hours-column">
    @if ($displayMode === 'circular')
        <!-- Circular Display -->
        <div class="relative flex items-center justify-center" style="width: 80px; height: 80px;">
            <!-- SVG Circle -->
            <svg class="absolute inset-0 w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <!-- Background circle -->
                <circle 
                    cx="50" 
                    cy="50" 
                    r="35"
                    fill="none"
                    stroke="#e5e7eb"
                    stroke-width="8"
                    class="dark:stroke-gray-600"
                />
                
                <!-- Day segments -->
                @php
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $segments = [];
                    foreach ($days as $index => $day) {
                        $dayData = $businessData['weekly_hours'][$day] ?? ['is_open' => false, 'formatted' => 'Closed'];
                        $angle = ($index / 7) * 360;
                        $segments[] = [
                            'day' => ucfirst($day),
                            'is_open' => $dayData['is_open'],
                            'hours' => $dayData['formatted'],
                            'angle' => $angle,
                            'color' => $dayData['is_open'] ? '#10b981' : '#ef4444'
                        ];
                    }
                @endphp
                
                @foreach ($segments as $segment)
                    @php
                        $segmentAngle = 360 / 7;
                        $startAngle = $segment['angle'];
                        $endAngle = $startAngle + $segmentAngle - 2;
                        $radius = 35;
                        
                        $startAngleRad = ($startAngle * M_PI) / 180;
                        $endAngleRad = ($endAngle * M_PI) / 180;
                        
                        $largeArcFlag = ($endAngle - $startAngle) <= 180 ? "0" : "1";
                        
                        $x1 = 50 + $radius * cos($startAngleRad);
                        $y1 = 50 + $radius * sin($startAngleRad);
                        $x2 = 50 + $radius * cos($endAngleRad);
                        $y2 = 50 + $radius * sin($endAngleRad);
                        
                        $pathData = "M {$x1} {$y1} A {$radius} {$radius} 0 {$largeArcFlag} 1 {$x2} {$y2}";
                    @endphp
                    
                    <path
                        d="{{ $pathData }}"
                        stroke="{{ $segment['color'] }}"
                        stroke-width="8"
                        fill="none"
                        stroke-linecap="round"
                        class="{{ !$segment['is_open'] ? 'opacity-50' : '' }}"
                        @if ($showTooltips)
                            title="{{ $segment['day'] }}: {{ $segment['hours'] }}"
                        @endif
                    />
                @endforeach
            </svg>
            
            <!-- Center status -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-3 h-3 rounded-full mx-auto mb-1 
                        @if ($businessData['is_open']) 
                            bg-green-500 animate-pulse
                        @elseif ($businessData['status'] === 'not_configured' || $businessData['status'] === 'disabled')
                            bg-gray-400
                        @else
                            bg-red-500
                        @endif"
                    ></div>
                    <div class="text-xs font-medium
                        @if ($businessData['is_open'])
                            text-green-600 dark:text-green-400
                        @elseif ($businessData['status'] === 'not_configured' || $businessData['status'] === 'disabled' || $businessData['status'] === 'error')
                            text-gray-500 dark:text-gray-400
                        @else
                            text-red-600 dark:text-red-400
                        @endif">
                        @if ($businessData['is_open'])
                            OPEN
                        @elseif ($businessData['status'] === 'not_configured' || $businessData['status'] === 'disabled')
                            N/A
                        @elseif ($businessData['status'] === 'error')
                            ERR
                        @else
                            CLOSED
                        @endif
                    </div>
                </div>
            </div>
            
            @if ($showTooltips)
                <div title="{{ $businessData['current_status'] }}" class="absolute inset-0 cursor-help"></div>
            @endif
        </div>
        
    @elseif ($displayMode === 'status')
        <!-- Status Badge Display -->
        <div class="flex items-center justify-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if ($businessData['is_open'])
                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif ($businessData['status'] === 'not_configured')
                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                @else
                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @endif"
                @if ($showTooltips && isset($businessData['next_open']) && isset($businessData['next_close']))
                    title="Next: {{ $businessData['is_open'] ? 'Closes at ' . $businessData['next_close'] : 'Opens at ' . $businessData['next_open'] }}"
                @endif
            >
                {{ $businessData['current_status'] }}
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
    min-width: 80px;
}

.fi-ta-opening-hours-column svg {
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.dark .fi-ta-opening-hours-column svg {
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.3));
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