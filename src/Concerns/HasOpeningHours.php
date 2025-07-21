<?php

namespace KaraOdin\FilamentOpeningHours\Concerns;

use Carbon\Carbon;
use Spatie\OpeningHours\OpeningHours;

trait HasOpeningHours
{
    protected ?OpeningHours $openingHoursInstance = null;

    public function initializeHasOpeningHours(): void
    {
        $this->casts = array_merge($this->casts, [
            'opening_hours' => 'array',
            'opening_hours_exceptions' => 'array',
        ]);
    }

    public function getOpeningHoursAttribute($value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    public function setOpeningHoursAttribute($value): void
    {
        $this->attributes['opening_hours'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getOpeningHoursExceptionsAttribute($value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    public function setOpeningHoursExceptionsAttribute($value): void
    {
        $this->attributes['opening_hours_exceptions'] = is_array($value) ? json_encode($value) : $value;
    }

    public function openingHours(): OpeningHours
    {
        if ($this->openingHoursInstance === null) {
            $openingHours = $this->opening_hours ?? [];
            $exceptions = $this->opening_hours_exceptions ?? [];
            
            // Convert our format to spatie/opening-hours format
            $spatieData = [];
            
            // Process weekly hours
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                if (isset($openingHours[$day]) && is_array($openingHours[$day])) {
                    $dayData = $openingHours[$day];
                    if (isset($dayData['enabled']) && $dayData['enabled'] && isset($dayData['hours']) && is_array($dayData['hours']) && !empty($dayData['hours'])) {
                        // Filter out empty hours and convert to spatie format
                        $validHours = collect($dayData['hours'])
                            ->filter(fn($h) => isset($h['from'], $h['to']) && !empty($h['from']) && !empty($h['to']))
                            ->map(fn($h) => "{$h['from']}-{$h['to']}")
                            ->toArray();
                        $spatieData[$day] = $validHours;
                    } else {
                        $spatieData[$day] = []; // Closed
                    }
                } else {
                    $spatieData[$day] = []; // Closed
                }
            }
            
            // Process exceptions
            $processedExceptions = [];
            foreach ($exceptions as $key => $exception) {
                // Skip range headers and individual dates that are part of ranges
                if (isset($exception['is_range_header']) || isset($exception['parent_range'])) {
                    continue;
                }
                
                // Convert our exception format to spatie format
                if (isset($exception['hours']) && !empty($exception['hours'])) {
                    $hours = collect($exception['hours'])->map(fn($h) => "{$h['from']}-{$h['to']}")->toArray();
                } else {
                    $hours = []; // Closed
                }
                
                $processedExceptions[$key] = $hours;
            }
            
            $spatieData['exceptions'] = $processedExceptions;
            
            try {
                $this->openingHoursInstance = OpeningHours::create($spatieData);
                // Increase day limit to avoid the 8-day error
                $this->openingHoursInstance->setDayLimit(30);
            } catch (\Exception $e) {
                // Fallback: create empty opening hours if data is invalid
                $this->openingHoursInstance = OpeningHours::create([
                    'monday' => [],
                    'tuesday' => [],
                    'wednesday' => [],
                    'thursday' => [],
                    'friday' => [],
                    'saturday' => [],
                    'sunday' => [],
                    'exceptions' => []
                ]);
                $this->openingHoursInstance->setDayLimit(30);
            }
        }

        return $this->openingHoursInstance;
    }

    public function isOpen(?Carbon $dateTime = null): bool
    {
        try {
            $dateTime = $dateTime ?? now($this->getTimezone());
            return $this->openingHours()->isOpenAt($dateTime);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isClosed(?Carbon $dateTime = null): bool
    {
        return !$this->isOpen($dateTime);
    }

    public function isOpenOn(string $day): bool
    {
        try {
            return $this->openingHours()->isOpenOn($day);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isClosedOn(string $day): bool
    {
        return !$this->isOpenOn($day);
    }

    public function nextOpen(?Carbon $dateTime = null): ?Carbon
    {
        try {
            $dateTime = $dateTime ?? now($this->getTimezone());
            $nextOpen = $this->openingHours()->nextOpen($dateTime);
            return $nextOpen ? Carbon::instance($nextOpen) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function nextClose(?Carbon $dateTime = null): ?Carbon
    {
        try {
            $dateTime = $dateTime ?? now($this->getTimezone());
            $nextClose = $this->openingHours()->nextClose($dateTime);
            return $nextClose ? Carbon::instance($nextClose) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function previousOpen(?Carbon $dateTime = null): ?Carbon
    {
        try {
            $dateTime = $dateTime ?? now($this->getTimezone());
            $previousOpen = $this->openingHours()->previousOpen($dateTime);
            return $previousOpen ? Carbon::instance($previousOpen) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function previousClose(?Carbon $dateTime = null): ?Carbon
    {
        try {
            $dateTime = $dateTime ?? now($this->getTimezone());
            $previousClose = $this->openingHours()->previousClose($dateTime);
            return $previousClose ? Carbon::instance($previousClose) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getOpeningHoursForDay(string $day): array
    {
        try {
            $dayHours = $this->openingHours()->forDay($day);
            // Convert OpeningHoursForDay to array of time strings
            $hours = [];
            foreach ($dayHours as $timeRange) {
                $hours[] = (string) $timeRange;
            }
            return $hours;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getOpeningHoursForDate(Carbon $date): array
    {
        try {
            $dateHours = $this->openingHours()->forDate($date);
            // Convert OpeningHoursForDay to array of time strings
            $hours = [];
            foreach ($dateHours as $timeRange) {
                $hours[] = (string) $timeRange;
            }
            return $hours;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCurrentStatus(?Carbon $dateTime = null): string
    {
        try {
            // Check if business hours are enabled
            if (isset($this->opening_hours_enabled) && !$this->opening_hours_enabled) {
                return __('filament-opening-hours::opening-hours.business_hours_disabled');
            }
            
            $dateTime = $dateTime ?? now($this->getTimezone());
            $currentDay = strtolower($dateTime->format('l'));
            
            // Check if we have any opening hours configured
            $openingHours = $this->opening_hours ?? [];
            if (empty($openingHours)) {
                return __('filament-opening-hours::opening-hours.no_hours_configured');
            }
            
            // Check if today has hours configured
            if (!isset($openingHours[$currentDay]) || 
                !isset($openingHours[$currentDay]['enabled']) || 
                !$openingHours[$currentDay]['enabled'] ||
                empty($openingHours[$currentDay]['hours'])) {
                return __('filament-opening-hours::opening-hours.closed_today');
            }
            
            if ($this->isOpen($dateTime)) {
                try {
                    $nextClose = $this->nextClose($dateTime);
                    return $nextClose 
                        ? __('filament-opening-hours::opening-hours.open_until', ['time' => $this->formatDateForLocale($nextClose, 'H:i')])
                        : __('filament-opening-hours::opening-hours.open_status');
                } catch (\Exception $e) {
                    return __('filament-opening-hours::opening-hours.open_status');
                }
            }

            try {
                $nextOpen = $this->nextOpen($dateTime);
                return $nextOpen 
                    ? __('filament-opening-hours::opening-hours.closed_until', ['time' => $this->formatDateForLocale($nextOpen, 'H:i')])
                    : __('filament-opening-hours::opening-hours.closed_status');
            } catch (\Exception $e) {
                return __('filament-opening-hours::opening-hours.closed_status');
            }
        } catch (\Exception $e) {
            return __('filament-opening-hours::opening-hours.status_unavailable') . ': ' . $e->getMessage();
        }
    }

    protected function getTimezone(): string
    {
        return $this->timezone ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
    }
    
    public function debugOpeningHours(): array
    {
        return [
            'raw_opening_hours' => $this->opening_hours,
            'raw_exceptions' => $this->opening_hours_exceptions,
            'enabled' => $this->opening_hours_enabled ?? true,
            'timezone' => $this->getTimezone(),
        ];
    }

    public function addException(string $date, array $hours = []): self
    {
        $exceptions = $this->opening_hours_exceptions ?? [];
        $exceptions[$date] = $hours;
        $this->opening_hours_exceptions = $exceptions;
        
        // Reset the opening hours instance to force regeneration
        $this->openingHoursInstance = null;
        
        return $this;
    }

    public function removeException(string $date): self
    {
        $exceptions = $this->opening_hours_exceptions ?? [];
        unset($exceptions[$date]);
        $this->opening_hours_exceptions = $exceptions;
        
        // Reset the opening hours instance to force regeneration
        $this->openingHoursInstance = null;
        
        return $this;
    }

    public function hasException(string $date): bool
    {
        $exceptions = $this->opening_hours_exceptions ?? [];
        return array_key_exists($date, $exceptions);
    }

    public function getException(string $date): ?array
    {
        $exceptions = $this->opening_hours_exceptions ?? [];
        return $exceptions[$date] ?? null;
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