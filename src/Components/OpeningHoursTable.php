<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Tables\Columns\Column;
use Closure;

class OpeningHoursTable extends Column
{
    protected string $view = 'filament-opening-hours::components.opening-hours-table';

    protected bool | Closure $showStatus = true;
    protected bool | Closure $showToday = true;
    protected string | Closure | null $timezone = null;

    public function showStatus(bool | Closure $condition = true): static
    {
        $this->showStatus = $condition;

        return $this;
    }

    public function showToday(bool | Closure $condition = true): static
    {
        $this->showToday = $condition;

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getShowStatus(): bool
    {
        return $this->evaluate($this->showStatus);
    }

    public function getShowToday(): bool
    {
        return $this->evaluate($this->showToday);
    }

    public function getTimezone(): ?string
    {
        return $this->evaluate($this->timezone);
    }

    public function getFormattedState(): string
    {
        $record = $this->getRecord();
        
        if (!method_exists($record, 'openingHours')) {
            return 'Not configured';
        }

        try {
            if ($this->getShowStatus()) {
                return $record->getCurrentStatus();
            }

            if ($this->getShowToday()) {
                $today = strtolower(now($this->getTimezone())->format('l'));
                $todayHours = $record->getOpeningHoursForDay($today);
                
                if (empty($todayHours)) {
                    return 'Closed today';
                }

                return 'Today: ' . implode(', ', $todayHours);
            }

            // Default: show if currently open/closed
            return $record->isOpen() ? 'Open' : 'Closed';
            
        } catch (\Exception $e) {
            return 'Error loading hours';
        }
    }

    public function getStatusColor(): string
    {
        $record = $this->getRecord();
        
        if (!method_exists($record, 'isOpen')) {
            return 'gray';
        }

        try {
            return $record->isOpen() ? 'success' : 'danger';
        } catch (\Exception $e) {
            return 'gray';
        }
    }
}