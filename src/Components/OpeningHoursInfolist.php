<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Closure;
use Filament\Infolists\Components\Entry;

class OpeningHoursInfolist extends Entry
{
    protected string $view = 'filament-opening-hours::components.opening-hours-infolist';

    protected bool|Closure $showExceptions = true;

    protected bool|Closure $showStatus = true;

    protected string|Closure|null $timezone = null;

    public function showExceptions(bool|Closure $condition = true): static
    {
        $this->showExceptions = $condition;

        return $this;
    }

    public function showStatus(bool|Closure $condition = true): static
    {
        $this->showStatus = $condition;

        return $this;
    }

    public function timezone(string|Closure|null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getShowExceptions(): bool
    {
        return $this->evaluate($this->showExceptions);
    }

    public function getShowStatus(): bool
    {
        return $this->evaluate($this->showStatus);
    }

    public function getTimezone(): ?string
    {
        return $this->evaluate($this->timezone);
    }

    public function getFormattedOpeningHours(): array
    {
        $record = $this->getRecord();

        if (! method_exists($record, 'openingHours')) {
            return [];
        }

        $days = config('filament-opening-hours.days', [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ]);

        $formattedHours = [];

        foreach ($days as $key => $label) {
            try {
                $dayHours = $record->getOpeningHoursForDay($key);
                $formattedHours[$label] = empty($dayHours) ? 'Closed' : implode(', ', $dayHours);
            } catch (\Exception $e) {
                $formattedHours[$label] = 'Error';
            }
        }

        return $formattedHours;
    }

    public function getCurrentStatus(): string
    {
        $record = $this->getRecord();

        if (! method_exists($record, 'getCurrentStatus')) {
            return 'Unknown';
        }

        try {
            return $record->getCurrentStatus();
        } catch (\Exception $e) {
            return 'Error loading status';
        }
    }

    public function getExceptions(): array
    {
        $record = $this->getRecord();

        if (! isset($record->opening_hours_exceptions) || ! is_array($record->opening_hours_exceptions)) {
            return [];
        }

        $exceptions = [];
        foreach ($record->opening_hours_exceptions as $date => $hours) {
            $exceptions[$date] = empty($hours) ? 'Closed' : implode(', ', $hours);
        }

        return $exceptions;
    }
}
