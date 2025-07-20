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
            
            // Process exceptions to handle ranges properly for spatie/opening-hours
            $processedExceptions = [];
            
            foreach ($exceptions as $key => $exception) {
                // Skip range headers and individual dates that are part of ranges for spatie compatibility
                if (isset($exception['is_range_header']) || isset($exception['parent_range'])) {
                    continue;
                }
                
                // Convert our exception format to spatie/opening-hours format
                if (isset($exception['hours']) && !empty($exception['hours'])) {
                    $hours = collect($exception['hours'])->map(fn($h) => "{$h['from']}-{$h['to']}")->toArray();
                } else {
                    $hours = []; // Closed
                }
                
                $processedExceptions[$key] = $hours;
            }
            
            $data = array_merge($openingHours, ['exceptions' => $processedExceptions]);
            $this->openingHoursInstance = OpeningHours::create($data);
        }

        return $this->openingHoursInstance;
    }

    public function isOpen(?Carbon $dateTime = null): bool
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        return $this->openingHours()->isOpenAt($dateTime);
    }

    public function isClosed(?Carbon $dateTime = null): bool
    {
        return !$this->isOpen($dateTime);
    }

    public function isOpenOn(string $day): bool
    {
        return $this->openingHours()->isOpenOn($day);
    }

    public function isClosedOn(string $day): bool
    {
        return !$this->isOpenOn($day);
    }

    public function nextOpen(?Carbon $dateTime = null): ?Carbon
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        $nextOpen = $this->openingHours()->nextOpen($dateTime);
        
        return $nextOpen ? Carbon::instance($nextOpen) : null;
    }

    public function nextClose(?Carbon $dateTime = null): ?Carbon
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        $nextClose = $this->openingHours()->nextClose($dateTime);
        
        return $nextClose ? Carbon::instance($nextClose) : null;
    }

    public function previousOpen(?Carbon $dateTime = null): ?Carbon
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        $previousOpen = $this->openingHours()->previousOpen($dateTime);
        
        return $previousOpen ? Carbon::instance($previousOpen) : null;
    }

    public function previousClose(?Carbon $dateTime = null): ?Carbon
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        $previousClose = $this->openingHours()->previousClose($dateTime);
        
        return $previousClose ? Carbon::instance($previousClose) : null;
    }

    public function getOpeningHoursForDay(string $day): array
    {
        return $this->openingHours()->forDay($day)->toArray();
    }

    public function getOpeningHoursForDate(Carbon $date): array
    {
        return $this->openingHours()->forDate($date)->toArray();
    }

    public function getCurrentStatus(?Carbon $dateTime = null): string
    {
        $dateTime = $dateTime ?? now($this->getTimezone());
        
        if ($this->isOpen($dateTime)) {
            $nextClose = $this->nextClose($dateTime);
            return $nextClose 
                ? "Open until {$nextClose->format('H:i')}"
                : 'Open';
        }

        $nextOpen = $this->nextOpen($dateTime);
        return $nextOpen 
            ? "Closed until {$nextOpen->format('H:i')}"
            : 'Closed';
    }

    protected function getTimezone(): string
    {
        return $this->timezone ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
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
}