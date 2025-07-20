<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Tables\Columns\Column;
use Closure;

class OpeningHoursColumn extends Column
{
    protected string $view = 'filament-opening-hours::components.opening-hours-column';

    protected string | Closure $displayMode = 'status';
    protected bool | Closure $showTooltips = true;
    protected bool | Closure $showCurrentStatus = true;
    protected string | Closure | null $timezone = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->alignCenter();
    }

    public function circular(): static
    {
        $this->displayMode = 'circular';
        return $this;
    }

    public function status(): static
    {
        $this->displayMode = 'status';
        return $this;
    }

    public function weekly(): static
    {
        $this->displayMode = 'weekly';
        return $this;
    }

    public function showTooltips(bool | Closure $condition = true): static
    {
        $this->showTooltips = $condition;
        return $this;
    }

    public function showCurrentStatus(bool | Closure $condition = true): static
    {
        $this->showCurrentStatus = $condition;
        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->evaluate($this->displayMode);
    }

    public function getShowTooltips(): bool
    {
        return $this->evaluate($this->showTooltips);
    }

    public function getShowCurrentStatus(): bool
    {
        return $this->evaluate($this->showCurrentStatus);
    }

    public function getTimezone(): ?string
    {
        return $this->evaluate($this->timezone);
    }

    public function getBusinessHoursData(): array
    {
        $record = $this->getRecord();
        
        if (!method_exists($record, 'openingHours')) {
            return [
                'status' => 'not_configured',
                'current_status' => 'Not configured',
                'is_open' => false,
                'weekly_hours' => [],
                'today_hours' => [],
                'next_open' => null,
                'next_close' => null,
            ];
        }

        try {
            $timezone = $this->getTimezone() ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
            $now = now($timezone);
            
            // Check if business hours are enabled
            if (isset($record->opening_hours_enabled) && !$record->opening_hours_enabled) {
                return [
                    'status' => 'disabled',
                    'current_status' => 'Disabled',
                    'is_open' => false,
                    'weekly_hours' => array_fill_keys(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], ['hours' => [], 'is_open' => false, 'formatted' => 'Disabled']),
                    'today_hours' => [],
                    'next_open' => null,
                    'next_close' => null,
                    'timezone' => $timezone,
                ];
            }
            
            $data = [
                'status' => 'closed',
                'current_status' => 'Closed',
                'is_open' => false,
                'weekly_hours' => [],
                'today_hours' => [],
                'next_open' => null,
                'next_close' => null,
                'timezone' => $timezone,
            ];
            
            // Safely get status
            try {
                $data['is_open'] = $record->isOpen();
                $data['status'] = $data['is_open'] ? 'open' : 'closed';
                $data['current_status'] = $record->getCurrentStatus();
            } catch (\Exception $e) {
                // Keep default closed status if spatie fails
                $data['current_status'] = 'Hours unavailable';
            }

            // Get weekly hours with better error handling
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                try {
                    $dayHours = $record->getOpeningHoursForDay($day);
                    $data['weekly_hours'][$day] = [
                        'hours' => $dayHours,
                        'is_open' => !empty($dayHours),
                        'formatted' => empty($dayHours) ? 'Closed' : implode(', ', $dayHours),
                    ];
                } catch (\Exception $e) {
                    $data['weekly_hours'][$day] = [
                        'hours' => [],
                        'is_open' => false,
                        'formatted' => 'Error',
                    ];
                }
            }

            // Get today's hours
            $today = strtolower($now->format('l'));
            $data['today_hours'] = $data['weekly_hours'][$today] ?? [];

            // Get next open/close times with error handling
            try {
                if ($nextOpen = $record->nextOpen()) {
                    $data['next_open'] = $nextOpen->format('H:i');
                }
            } catch (\Exception $e) {
                // Ignore nextOpen errors
            }
            
            try {
                if ($nextClose = $record->nextClose()) {
                    $data['next_close'] = $nextClose->format('H:i');
                }
            } catch (\Exception $e) {
                // Ignore nextClose errors
            }

            return $data;
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'current_status' => 'Error loading hours',
                'is_open' => false,
                'weekly_hours' => [],
                'today_hours' => [],
                'next_open' => null,
                'next_close' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getStatusColor(): string
    {
        $data = $this->getBusinessHoursData();
        
        return match($data['status']) {
            'open' => 'success',
            'closed' => 'danger',
            'not_configured' => 'gray',
            'error' => 'warning',
            default => 'gray',
        };
    }

    public function getCircularData(): array
    {
        $data = $this->getBusinessHoursData();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        $segments = [];
        $totalSegments = 7;
        
        foreach ($days as $index => $day) {
            $isOpen = $data['weekly_hours'][$day]['is_open'] ?? false;
            $angle = ($index / $totalSegments) * 360;
            
            $segments[] = [
                'day' => ucfirst($day),
                'is_open' => $isOpen,
                'hours' => $data['weekly_hours'][$day]['formatted'] ?? 'Closed',
                'angle' => $angle,
                'color' => $isOpen ? '#10b981' : '#ef4444', // green : red
            ];
        }

        return [
            'segments' => $segments,
            'center_status' => $data['current_status'],
            'is_currently_open' => $data['is_open'],
        ];
    }
}