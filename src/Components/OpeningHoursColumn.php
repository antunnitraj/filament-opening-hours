<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Tables\Columns\TextColumn;
use Closure;

class OpeningHoursColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'isOpen') || !method_exists($record, 'getCurrentStatus')) {
                return 'Not configured';
            }

            try {
                return $record->getCurrentStatus();
            } catch (\Exception $e) {
                return 'Error loading hours';
            }
        });

        $this->badge()
            ->color(function ($record): string {
                if (!method_exists($record, 'isOpen')) {
                    return 'gray';
                }

                try {
                    return $record->isOpen() ? 'success' : 'danger';
                } catch (\Exception $e) {
                    return 'gray';
                }
            });
    }

    public function showToday(): static
    {
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'getOpeningHoursForDay')) {
                return 'Not configured';
            }

            try {
                $today = strtolower(now()->format('l'));
                $todayHours = $record->getOpeningHoursForDay($today);
                
                if (empty($todayHours)) {
                    return 'Closed today';
                }

                return 'Today: ' . implode(', ', $todayHours);
            } catch (\Exception $e) {
                return 'Error loading hours';
            }
        });

        return $this;
    }

    public function showSimpleStatus(): static
    {
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'isOpen')) {
                return 'Unknown';
            }

            try {
                return $record->isOpen() ? 'Open' : 'Closed';
            } catch (\Exception $e) {
                return 'Error';
            }
        });

        return $this;
    }
}