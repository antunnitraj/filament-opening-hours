<div 
    x-data="openingHoursColumn(@js($getBusinessHoursData()), @js($getDisplayMode()), @js($getShowTooltips()))"
    class="fi-ta-opening-hours-column"
>
    @if ($getDisplayMode() === 'circular')
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
                <template x-for="(segment, index) in circularData.segments" :key="index">
                    <path
                        :d="getArcPath(segment.angle, 51.4)"
                        :stroke="segment.color"
                        stroke-width="8"
                        fill="none"
                        stroke-linecap="round"
                        :class="{'opacity-50': !segment.is_open}"
                        x-tooltip.raw="segment.day + ': ' + segment.hours"
                        x-show="showTooltips"
                    />
                </template>
            </svg>
            
            <!-- Center status -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <div 
                        :class="{
                            'w-3 h-3 rounded-full mx-auto mb-1': true,
                            'bg-green-500 animate-pulse': data.is_open,
                            'bg-red-500': !data.is_open && data.status !== 'not_configured',
                            'bg-gray-400': data.status === 'not_configured'
                        }"
                    ></div>
                    <div 
                        class="text-xs font-medium"
                        :class="{
                            'text-green-600 dark:text-green-400': data.is_open,
                            'text-red-600 dark:text-red-400': !data.is_open && data.status !== 'not_configured',
                            'text-gray-500 dark:text-gray-400': data.status === 'not_configured'
                        }"
                        x-text="data.is_open ? 'OPEN' : (data.status === 'not_configured' ? 'N/A' : 'CLOSED')"
                    ></div>
                </div>
            </div>
        </div>
        
        <!-- Tooltip for overall status -->
        <div 
            x-show="showTooltips" 
            x-tooltip.raw="data.current_status"
            class="absolute inset-0 cursor-help"
        ></div>
        
    @elseif ($getDisplayMode() === 'status')
        <!-- Status Badge Display -->
        <div class="flex items-center justify-center">
            <span 
                :class="{
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': data.is_open,
                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': !data.is_open && data.status !== 'not_configured',
                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': data.status === 'not_configured'
                }"
                x-text="data.current_status"
                x-tooltip.raw="showTooltips ? 'Next: ' + (data.is_open ? 'Closes at ' + data.next_close : 'Opens at ' + data.next_open) : ''"
            ></span>
        </div>
        
    @elseif ($getDisplayMode() === 'weekly')
        <!-- Weekly Overview Display -->
        <div class="flex items-center space-x-1">
            <template x-for="(day, dayName) in data.weekly_hours" :key="dayName">
                <div 
                    :class="{
                        'w-3 h-3 rounded-sm': true,
                        'bg-green-500': day.is_open,
                        'bg-red-300': !day.is_open,
                    }"
                    :title="dayName.charAt(0).toUpperCase() + dayName.slice(1) + ': ' + day.formatted"
                    x-tooltip.raw="showTooltips ? dayName.charAt(0).toUpperCase() + dayName.slice(1) + ': ' + day.formatted : ''"
                ></div>
            </template>
        </div>
        
        <!-- Current status indicator -->
        <div class="mt-1">
            <div 
                :class="{
                    'w-2 h-2 rounded-full mx-auto': true,
                    'bg-green-500 animate-pulse': data.is_open,
                    'bg-red-500': !data.is_open && data.status !== 'not_configured',
                    'bg-gray-400': data.status === 'not_configured'
                }"
                x-tooltip.raw="data.current_status"
            ></div>
        </div>
    @endif
</div>

<script>
function openingHoursColumn(businessData, displayMode, showTooltips) {
    return {
        data: businessData,
        displayMode: displayMode,
        showTooltips: showTooltips,
        
        get circularData() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const segments = [];
            
            days.forEach((day, index) => {
                const dayData = this.data.weekly_hours[day] || {};
                const angle = (index / 7) * 360;
                
                segments.push({
                    day: day.charAt(0).toUpperCase() + day.slice(1),
                    is_open: dayData.is_open || false,
                    hours: dayData.formatted || 'Closed',
                    angle: angle,
                    color: dayData.is_open ? '#10b981' : '#ef4444'
                });
            });
            
            return {
                segments: segments,
                center_status: this.data.current_status,
                is_currently_open: this.data.is_open
            };
        },
        
        getArcPath(startAngle, radius) {
            const segmentAngle = 360 / 7; // 7 days
            const endAngle = startAngle + segmentAngle - 2; // Small gap between segments
            
            const startAngleRad = (startAngle * Math.PI) / 180;
            const endAngleRad = (endAngle * Math.PI) / 180;
            
            const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";
            
            const x1 = 50 + radius * Math.cos(startAngleRad);
            const y1 = 50 + radius * Math.sin(startAngleRad);
            const x2 = 50 + radius * Math.cos(endAngleRad);
            const y2 = 50 + radius * Math.sin(endAngleRad);
            
            return `M ${x1} ${y1} A ${radius} ${radius} 0 ${largeArcFlag} 1 ${x2} ${y2}`;
        }
    };
}
</script>

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