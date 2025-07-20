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
                'current_status' => 'Business hours not configured',
                'is_open' => false,
                'weekly_hours' => [],
                'exceptions' => [],
                'timezone' => $this->getTimezone() ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers'),
            ];
        }

        try {
            $timezone = $this->getTimezone() ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
            $now = now($timezone);
            
            $data = [
                'status' => $record->isOpen() ? 'open' : 'closed',
                'current_status' => $record->getCurrentStatus(),
                'is_open' => $record->isOpen(),
                'weekly_hours' => [],
                'exceptions' => [],
                'timezone' => $timezone,
                'next_open' => null,
                'next_close' => null,
                'last_updated' => $now->format('M j, Y \a\t H:i'),
            ];

            // Get weekly hours
            $days = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];

            foreach ($days as $key => $label) {
                $dayHours = $record->getOpeningHoursForDay($key);
                $data['weekly_hours'][$key] = [
                    'label' => $label,
                    'hours' => $dayHours,
                    'is_open' => !empty($dayHours),
                    'formatted' => empty($dayHours) ? 'Closed' : implode(', ', $dayHours),
                    'is_today' => strtolower($now->format('l')) === $key,
                ];
            }

            // Get exceptions
            if (isset($record->opening_hours_exceptions) && is_array($record->opening_hours_exceptions)) {
                $processedRanges = [];
                
                foreach ($record->opening_hours_exceptions as $date => $exception) {
                    // Skip individual dates that are part of a range (already displayed)
                    if (isset($exception['parent_range']) && in_array($exception['parent_range'], $processedRanges)) {
                        continue;
                    }
                    
                    // Skip range headers
                    if (isset($exception['is_range_header'])) {
                        continue;
                    }
                    
                    $exceptionData = is_array($exception) ? $exception : ['type' => 'closed', 'hours' => []];
                    
                    $formattedDate = '';
                    $dateMode = '';
                    $isRecurring = false;
                    
                    if (isset($exceptionData['is_range']) && $exceptionData['is_range']) {
                        // Date range
                        $startDate = \Carbon\Carbon::parse($exceptionData['start_date'])->format('M j');
                        $endDate = \Carbon\Carbon::parse($exceptionData['end_date'])->format('M j, Y');
                        $formattedDate = "{$startDate} - {$endDate}";
                        $dateMode = 'range';
                        $processedRanges[] = "range_{$exceptionData['start_date']}_to_{$exceptionData['end_date']}";
                    } elseif (isset($exceptionData['recurring']) && $exceptionData['recurring']) {
                        // Recurring annual
                        $formattedDate = "Every " . \Carbon\Carbon::parse($exceptionData['date'])->format('F j');
                        $dateMode = 'recurring';
                        $isRecurring = true;
                    } elseif (strlen($date) === 5) {
                        // MM-DD format (legacy recurring)
                        $formattedDate = "Every " . \Carbon\Carbon::createFromFormat('m-d', $date)->format('F j');
                        $dateMode = 'recurring';
                        $isRecurring = true;
                    } else {
                        // Single date
                        $formattedDate = \Carbon\Carbon::parse($date)->format('M j, Y');
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
                        'formatted_hours' => empty($exceptionData['hours']) ? 'Closed' : 
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

            // Get next open/close times
            if ($nextOpen = $record->nextOpen()) {
                $data['next_open'] = $nextOpen->format('M j, Y \a\t H:i');
            }
            
            if ($nextClose = $record->nextClose()) {
                $data['next_close'] = $nextClose->format('M j, Y \a\t H:i');
            }

            return $data;
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'current_status' => 'Error loading business hours',
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
}