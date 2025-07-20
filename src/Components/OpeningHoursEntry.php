<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Infolists\Components\TextEntry;

class OpeningHoursEntry extends TextEntry
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'openingHours')) {
                return 'Not configured';
            }

            $days = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];

            $output = [];

            // Add current status
            if (method_exists($record, 'getCurrentStatus')) {
                try {
                    $status = $record->getCurrentStatus();
                    $isOpen = str_contains(strtolower($status), 'open');
                    $statusColor = $isOpen ? '🟢' : '🔴';
                    $output[] = "{$statusColor} **{$status}**";
                    $output[] = '';
                } catch (\Exception $e) {
                    $output[] = '⚠️ Error loading status';
                    $output[] = '';
                }
            }

            // Add weekly hours
            $output[] = '**Regular Hours:**';
            foreach ($days as $key => $label) {
                try {
                    $dayHours = $record->getOpeningHoursForDay($key);
                    $hours = empty($dayHours) ? 'Closed' : implode(', ', $dayHours);
                    $output[] = "• **{$label}:** {$hours}";
                } catch (\Exception $e) {
                    $output[] = "• **{$label}:** Error";
                }
            }

            // Add exceptions if any
            if (isset($record->opening_hours_exceptions) && !empty($record->opening_hours_exceptions)) {
                $output[] = '';
                $output[] = '**Exceptions:**';
                foreach ($record->opening_hours_exceptions as $date => $hours) {
                    $formattedDate = \Carbon\Carbon::parse($date)->format('M j, Y');
                    $formattedHours = empty($hours) ? 'Closed' : implode(', ', $hours);
                    $output[] = "• **{$formattedDate}:** {$formattedHours}";
                }
            }

            return implode("\n", $output);
        });

        $this->markdown();
    }

    public function showStatusOnly(): static
    {
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'getCurrentStatus')) {
                return 'Status unknown';
            }

            try {
                $status = $record->getCurrentStatus();
                $isOpen = str_contains(strtolower($status), 'open');
                $statusColor = $isOpen ? '🟢' : '🔴';
                return "{$statusColor} **{$status}**";
            } catch (\Exception $e) {
                return '⚠️ Error loading status';
            }
        });

        return $this;
    }

    public function showWeeklyHours(): static
    {
        $this->getStateUsing(function ($record) {
            if (!method_exists($record, 'getOpeningHoursForDay')) {
                return 'Hours not configured';
            }

            $days = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];

            $output = [];
            foreach ($days as $key => $label) {
                try {
                    $dayHours = $record->getOpeningHoursForDay($key);
                    $hours = empty($dayHours) ? 'Closed' : implode(', ', $dayHours);
                    $output[] = "• **{$label}:** {$hours}";
                } catch (\Exception $e) {
                    $output[] = "• **{$label}:** Error";
                }
            }

            return implode("\n", $output);
        });

        return $this;
    }
}