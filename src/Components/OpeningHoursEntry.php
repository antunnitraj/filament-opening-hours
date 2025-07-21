<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Infolists\Components\Entry;
use Closure;

class OpeningHoursEntry extends Entry
{
    protected string $view = 'filament-opening-hours::components.opening-hours-entry';

    protected string | Closure $displayMode = 'full';
    protected bool | Closure $showStatus = true;
    protected bool | Closure $showExceptions = true;
    protected bool | Closure $showTimezone = true;
    protected string | Closure | null $timezone = null;

    public function full(): static
    {
        $this->displayMode = 'full';
        return $this;
    }

    public function statusOnly(): static
    {
        $this->displayMode = 'status';
        return $this;
    }

    public function weeklyHours(): static
    {
        $this->displayMode = 'weekly';
        return $this;
    }

    public function compact(): static
    {
        $this->displayMode = 'compact';
        return $this;
    }

    public function showStatus(bool | Closure $condition = true): static
    {
        $this->showStatus = $condition;
        return $this;
    }

    public function showExceptions(bool | Closure $condition = true): static
    {
        $this->showExceptions = $condition;
        return $this;
    }

    public function showTimezone(bool | Closure $condition = true): static
    {
        $this->showTimezone = $condition;
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

    public function getShowStatus(): bool
    {
        return $this->evaluate($this->showStatus);
    }

    public function getShowExceptions(): bool
    {
        return $this->evaluate($this->showExceptions);
    }

    public function getShowTimezone(): bool
    {
        return $this->evaluate($this->showTimezone);
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
                'current_status' => __('filament-opening-hours::opening-hours.no_hours_configured'),
                'is_open' => false,
                'weekly_hours' => [],
                'exceptions' => [],
                'timezone' => $this->getTimezone() ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers'),
            ];
        }

        try {
            $timezone = $this->getTimezone() ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
            $now = now($timezone);
            
            // Check if business hours are enabled
            if (isset($record->opening_hours_enabled) && !$record->opening_hours_enabled) {
                return [
                    'status' => 'disabled',
                    'current_status' => __('filament-opening-hours::opening-hours.business_hours_disabled'),
                    'is_open' => false,
                    'weekly_hours' => [],
                    'exceptions' => [],
                    'timezone' => $timezone,
                ];
            }
            
            $data = [
                'status' => 'closed',
                'current_status' => __('filament-opening-hours::opening-hours.closed_status'),
                'is_open' => false,
                'weekly_hours' => [],
                'exceptions' => [],
                'timezone' => $timezone,
                'next_open' => null,
                'next_close' => null,
                'last_updated' => $this->formatDateForLocale($now, 'M j, Y \a\t H:i'),
            ];
            
            // Safely get status
            try {
                $data['is_open'] = $record->isOpen();
                $data['status'] = $data['is_open'] ? 'open' : 'closed';
                $data['current_status'] = $record->getCurrentStatus();
            } catch (\Exception $e) {
                // Keep default closed status if spatie fails
                $data['current_status'] = __('filament-opening-hours::opening-hours.status_unavailable');
            }

            // Get weekly hours
            $days = [
                'monday' => __('filament-opening-hours::opening-hours.days.monday'),
                'tuesday' => __('filament-opening-hours::opening-hours.days.tuesday'),
                'wednesday' => __('filament-opening-hours::opening-hours.days.wednesday'),
                'thursday' => __('filament-opening-hours::opening-hours.days.thursday'),
                'friday' => __('filament-opening-hours::opening-hours.days.friday'),
                'saturday' => __('filament-opening-hours::opening-hours.days.saturday'),
                'sunday' => __('filament-opening-hours::opening-hours.days.sunday'),
            ];

            foreach ($days as $key => $label) {
                $dayHours = $record->getOpeningHoursForDay($key);
                $data['weekly_hours'][$key] = [
                    'label' => $label,
                    'hours' => $dayHours,
                    'is_open' => !empty($dayHours),
                    'formatted' => empty($dayHours) ? __('filament-opening-hours::opening-hours.closed_status') : implode(', ', $dayHours),
                    'is_today' => strtolower($now->format('l')) === $key,
                ];
            }

            // Get exceptions
            if (isset($record->opening_hours_exceptions) && is_array($record->opening_hours_exceptions)) {
                $processedRanges = [];
                $displayedExceptions = [];
                
                foreach ($record->opening_hours_exceptions as $date => $exception) {
                    // First pass: collect all range headers for display
                    if (isset($exception['is_range_header']) && $exception['is_range_header']) {
                        $displayedExceptions[$date] = $exception;
                        $processedRanges[] = $date;
                        continue;
                    }
                    
                    // Skip individual dates that are part of a range (already displayed via range header)
                    if (isset($exception['parent_range']) && in_array($exception['parent_range'], $processedRanges)) {
                        continue;
                    }
                    
                    // Show individual dates and recurring exceptions
                    if (!isset($exception['parent_range'])) {
                        $displayedExceptions[$date] = $exception;
                    }
                }
                
                foreach ($displayedExceptions as $date => $exception) {
                    
                    $exceptionData = is_array($exception) ? $exception : ['type' => 'closed', 'hours' => []];
                    
                    $formattedDate = '';
                    $dateMode = '';
                    $isRecurring = false;
                    
                    if (isset($exceptionData['is_range_header']) && $exceptionData['is_range_header']) {
                        // This is a range header - display the range
                        $startDate = $this->formatDateForLocale(\Carbon\Carbon::parse($exceptionData['start_date']), 'M j');
                        $endDate = $this->formatDateForLocale(\Carbon\Carbon::parse($exceptionData['end_date']), 'M j, Y');
                        $formattedDate = "{$startDate} - {$endDate}";
                        $dateMode = 'range';
                    } elseif (isset($exceptionData['is_range']) && $exceptionData['is_range']) {
                        // This is a range item (shouldn't display if we have header)
                        $startDate = $this->formatDateForLocale(\Carbon\Carbon::parse($exceptionData['start_date']), 'M j');
                        $endDate = $this->formatDateForLocale(\Carbon\Carbon::parse($exceptionData['end_date']), 'M j, Y');
                        $formattedDate = "{$startDate} - {$endDate}";
                        $dateMode = 'range';
                    } elseif (isset($exceptionData['recurring']) && $exceptionData['recurring']) {
                        // Recurring annual
                        $formattedDate = __('filament-opening-hours::opening-hours.every') . ' ' . $this->formatDateForLocale(\Carbon\Carbon::parse($exceptionData['date']), 'F j');
                        $dateMode = 'recurring';
                        $isRecurring = true;
                    } elseif (strlen($date) === 5) {
                        // MM-DD format (legacy recurring)
                        $formattedDate = __('filament-opening-hours::opening-hours.every') . ' ' . $this->formatDateForLocale(\Carbon\Carbon::createFromFormat('m-d', $date), 'F j');
                        $dateMode = 'recurring';
                        $isRecurring = true;
                    } else {
                        // Single date
                        $formattedDate = $this->formatDateForLocale(\Carbon\Carbon::parse($date), 'M j, Y');
                        $dateMode = 'single';
                    }
                    
                    $data['exceptions'][] = [
                        'date' => $date,
                        'formatted_date' => $formattedDate,
                        'type' => $exceptionData['type'] ?? 'closed',
                        'label' => $exceptionData['label'] ?? '',
                        'note' => $exceptionData['note'] ?? '',
                        'hours' => $exceptionData['hours'] ?? [],
                        'is_recurring' => $isRecurring,
                        'date_mode' => $dateMode,
                        'formatted_hours' => empty($exceptionData['hours']) ? __('filament-opening-hours::opening-hours.closed_status') : 
                            collect($exceptionData['hours'])->map(fn($h) => "{$h['from']}-{$h['to']}")->join(', '),
                    ];
                }
                
                // Sort exceptions by type and date
                usort($data['exceptions'], function($a, $b) {
                    // Sort by date mode first: single, range, then recurring
                    $modeOrder = ['single' => 1, 'range' => 2, 'recurring' => 3];
                    $aModeOrder = $modeOrder[$a['date_mode']] ?? 4;
                    $bModeOrder = $modeOrder[$b['date_mode']] ?? 4;
                    
                    if ($aModeOrder !== $bModeOrder) {
                        return $aModeOrder <=> $bModeOrder;
                    }
                    
                    return strcmp($a['date'], $b['date']);
                });
            }

            // Get next open/close times with error handling
            try {
                if ($nextOpen = $record->nextOpen()) {
                    $data['next_open'] = $this->formatDateForLocale($nextOpen, 'M j, Y \a\t H:i');
                }
            } catch (\Exception $e) {
                // Ignore nextOpen errors
            }
            
            try {
                if ($nextClose = $record->nextClose()) {
                    $data['next_close'] = $this->formatDateForLocale($nextClose, 'M j, Y \a\t H:i');
                }
            } catch (\Exception $e) {
                // Ignore nextClose errors
            }

            return $data;
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'current_status' => __('filament-opening-hours::opening-hours.error_loading_hours'),
                'is_open' => false,
                'weekly_hours' => [],
                'exceptions' => [],
                'timezone' => $this->getTimezone() ?? 'UTC',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getStatusIcon(): string
    {
        $data = $this->getBusinessHoursData();
        
        return match($data['status']) {
            'open' => '🟢',
            'closed' => '🔴',
            'not_configured' => '⚪',
            'error' => '⚠️',
            default => '⚪',
        };
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

    protected function formatDateForLocale(\Carbon\Carbon $date, string $format): string
    {
        // Get current application locale
        $locale = app()->getLocale();
        
        // Set Carbon locale based on application locale
        $carbonLocale = match($locale) {
            'ar' => 'ar',
            'fr' => 'fr',
            default => 'en',
        };
        
        return $date->locale($carbonLocale)->translatedFormat($format);
    }
}