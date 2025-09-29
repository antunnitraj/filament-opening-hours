<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;

/**
 * OpeningHoursForm provides a comprehensive schema for managing business opening hours.
 *
 * Compatible with Filament v4. Uses the Schemas namespace components for layout.
 *
 * Features:
 * - Weekly schedule management with time slots
 * - Exception handling for holidays and special dates
 * - Timezone support
 * - Global enable/disable toggle
 * - Range and recurring date exceptions
 *
 * @since 3.0.0 Updated for Filament v4 compatibility
 */
class OpeningHoursForm
{
    /**
     * Generate the complete form schema for opening hours management.
     *
     * Returns a Filament v4 compatible schema with defensive coding practices.
     * Uses Schemas namespace components (Section, Grid, Group) and Forms
     * components (Toggle, TimePicker, etc.).
     *
     * @return array<\Filament\Schemas\Schema> Form schema components
     */
    public static function schema(): array
    {
        return [
            Section::make(__('filament-opening-hours::opening-hours.business_hours_configuration'))
                ->description(__('filament-opening-hours::opening-hours.business_hours_configuration_description'))
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('timezone')
                            ->label(__('filament-opening-hours::opening-hours.timezone'))
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                                return [$timezone => str_replace('_', ' ', $timezone)];
                            })->toArray())
                            ->searchable()
                            ->default('Africa/Algiers')
                            ->helperText(__('filament-opening-hours::opening-hours.timezone_help'))
                            ->columnSpan(1),

                        Toggle::make('opening_hours_enabled')
                            ->label(__('filament-opening-hours::opening-hours.enable_business_hours'))
                            ->helperText(__('filament-opening-hours::opening-hours.enable_business_hours_help'))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Auto-enable if any hours are configured
                                if (! $state) {
                                    $openingHours = $get('opening_hours') ?? [];
                                    $hasHours = false;

                                    if (is_array($openingHours)) {
                                        foreach ($openingHours as $dayData) {
                                            if (is_array($dayData) &&
                                                isset($dayData['enabled']) && $dayData['enabled'] &&
                                                isset($dayData['hours']) && is_array($dayData['hours']) && ! empty($dayData['hours'])) {
                                                $hasHours = true;
                                                break;
                                            }
                                        }
                                    }

                                    if ($hasHours) {
                                        $set('opening_hours_enabled', true);
                                    }
                                }
                            })
                            ->columnSpan(1),
                    ]),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make(__('filament-opening-hours::opening-hours.weekly_schedule'))
                ->description(fn ($get) => $get('opening_hours_enabled')
                    ? __('filament-opening-hours::opening-hours.weekly_schedule_description')
                    : __('filament-opening-hours::opening-hours.weekly_schedule_description_disabled'))
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Grid::make(1)->schema(self::getDayComponents()),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make(__('filament-opening-hours::opening-hours.exceptions_special_hours'))
                ->description(fn ($get) => $get('opening_hours_enabled')
                    ? __('filament-opening-hours::opening-hours.exceptions_special_hours_description')
                    : __('filament-opening-hours::opening-hours.exceptions_special_hours_description_disabled'))
                ->icon('heroicon-o-exclamation-triangle')
                ->headerActions([
                    Action::make('add_exception')
                        ->label(__('filament-opening-hours::opening-hours.add_exception'))
                        ->icon('heroicon-o-plus')
                        ->color('primary')
                        ->form([
                            Grid::make(3)->schema([
                                Select::make('date_mode')
                                    ->label(__('filament-opening-hours::opening-hours.date_mode'))
                                    ->options([
                                        'single' => __('filament-opening-hours::opening-hours.single_date'),
                                        'range' => __('filament-opening-hours::opening-hours.date_range'),
                                        'recurring' => __('filament-opening-hours::opening-hours.recurring_annual'),
                                    ])
                                    ->default('single')
                                    ->live()
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('exception_type')
                                    ->label(__('filament-opening-hours::opening-hours.exception_type'))
                                    ->options([
                                        'closed' => __('filament-opening-hours::opening-hours.closed'),
                                        'holiday' => __('filament-opening-hours::opening-hours.holiday'),
                                        'special_hours' => __('filament-opening-hours::opening-hours.special_hours_type'),
                                        'maintenance' => __('filament-opening-hours::opening-hours.maintenance'),
                                        'event' => __('filament-opening-hours::opening-hours.event'),
                                    ])
                                    ->default('closed')
                                    ->live()
                                    ->required()
                                    ->columnSpan(2),
                            ]),

                            // Single Date
                            Grid::make(1)->schema([
                                DatePicker::make('exception_date')
                                    ->label(__('filament-opening-hours::opening-hours.date'))
                                    ->required()
                                    ->helperText(__('filament-opening-hours::opening-hours.date_help')),
                            ])->visible(fn ($get) => $get('date_mode') === 'single'),

                            // Date Range
                            Grid::make(2)->schema([
                                DatePicker::make('start_date')
                                    ->label(__('filament-opening-hours::opening-hours.start_date'))
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),

                                DatePicker::make('end_date')
                                    ->label(__('filament-opening-hours::opening-hours.end_date'))
                                    ->required()
                                    ->after('start_date')
                                    ->helperText(__('filament-opening-hours::opening-hours.range_help'))
                                    ->columnSpan(1),
                            ])->visible(fn ($get) => $get('date_mode') === 'range'),

                            // Recurring Annual
                            Grid::make(2)->schema([
                                DatePicker::make('recurring_date')
                                    ->label(__('filament-opening-hours::opening-hours.annual_date'))
                                    ->required()
                                    ->helperText(__('filament-opening-hours::opening-hours.annual_help'))
                                    ->columnSpan(2),
                            ])->visible(fn ($get) => $get('date_mode') === 'recurring'),

                            Grid::make(1)->schema([
                                TextInput::make('exception_label')
                                    ->label(__('filament-opening-hours::opening-hours.custom_label'))
                                    ->placeholder(__('filament-opening-hours::opening-hours.label_placeholder'))
                                    ->maxLength(100),

                                TextInput::make('exception_note')
                                    ->label(__('filament-opening-hours::opening-hours.description'))
                                    ->placeholder(__('filament-opening-hours::opening-hours.description_placeholder'))
                                    ->maxLength(255),
                            ]),

                            Section::make(__('filament-opening-hours::opening-hours.special_hours'))
                                ->description(__('filament-opening-hours::opening-hours.special_hours_description'))
                                ->schema([
                                    Repeater::make('exception_hours')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TimePicker::make('from')
                                                    ->label(__('filament-opening-hours::opening-hours.from'))
                                                    ->required()
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                                TimePicker::make('to')
                                                    ->label(__('filament-opening-hours::opening-hours.to'))
                                                    ->required()
                                                    ->seconds(false)
                                                    ->displayFormat('H:i')
                                                    ->after('from'),
                                            ]),
                                        ])
                                        ->addActionLabel(__('filament-opening-hours::opening-hours.add_time_slot'))
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->defaultItems(0)
                                        ->itemLabel(fn (array $state): ?string => isset($state['from'], $state['to'])
                                                ? "{$state['from']} - {$state['to']}"
                                                : __('filament-opening-hours::opening-hours.new_time_slot')
                                        ),
                                ])
                                ->visible(fn ($get) => $get('exception_type') === 'special_hours'),

                        ])
                        ->action(function (array $data, $get, $set) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];

                            if (! is_array($exceptions)) {
                                $exceptions = [];
                            }

                            $exceptionData = [
                                'type' => $data['exception_type'] ?? 'closed',
                                'label' => $data['exception_label'] ?? '',
                                'note' => $data['exception_note'] ?? '',
                                'hours' => ($data['exception_type'] ?? 'closed') === 'special_hours' ? ($data['exception_hours'] ?? []) : [],
                                'date_mode' => $data['date_mode'] ?? 'single',
                            ];

                            switch ($data['date_mode'] ?? 'single') {
                                case 'single':
                                    if (isset($data['exception_date'])) {
                                        $key = $data['exception_date'];
                                        $exceptionData['date'] = $data['exception_date'];
                                        $exceptions[$key] = $exceptionData;
                                    }
                                    break;

                                case 'range':
                                    if (isset($data['start_date']) && isset($data['end_date'])) {
                                        try {
                                            $startDate = \Carbon\Carbon::parse($data['start_date']);
                                            $endDate = \Carbon\Carbon::parse($data['end_date']);

                                            // Create range key for display
                                            $rangeKey = "range_{$data['start_date']}_to_{$data['end_date']}";
                                            $exceptionData['start_date'] = $data['start_date'];
                                            $exceptionData['end_date'] = $data['end_date'];
                                            $exceptionData['is_range'] = true;

                                            // Add individual dates for spatie/opening-hours compatibility
                                            $currentDate = $startDate->copy();
                                            while ($currentDate->lte($endDate)) {
                                                $dateKey = $currentDate->format('Y-m-d');
                                                $exceptions[$dateKey] = array_merge($exceptionData, [
                                                    'date' => $dateKey,
                                                    'parent_range' => $rangeKey,
                                                ]);
                                                $currentDate->addDay();
                                            }

                                            // Also store the range info for display
                                            $exceptions[$rangeKey] = array_merge($exceptionData, [
                                                'is_range_header' => true,
                                            ]);
                                        } catch (\Exception $e) {
                                            // Skip invalid date range
                                        }
                                    }
                                    break;

                                case 'recurring':
                                    if (isset($data['recurring_date'])) {
                                        try {
                                            $recurringDate = \Carbon\Carbon::parse($data['recurring_date']);
                                            $key = $recurringDate->format('m-d'); // MM-DD format for recurring
                                            $exceptionData['date'] = $data['recurring_date'];
                                            $exceptionData['recurring'] = true;
                                            $exceptions[$key] = $exceptionData;
                                        } catch (\Exception $e) {
                                            // Skip invalid recurring date
                                        }
                                    }
                                    break;
                            }

                            $set('opening_hours_exceptions', $exceptions);
                        }),
                ])
                ->schema([
                    Html::make(function ($get) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];

                            if (! is_array($exceptions) || empty($exceptions)) {
                                $enabled = $get('opening_hours_enabled');
                                $statusText = $enabled ? '' : ' '.__('filament-opening-hours::opening-hours.no_exceptions_configured_disabled');

                                return '<div class="text-sm text-gray-600 dark:text-gray-400">'
                                    .e(__('filament-opening-hours::opening-hours.no_exceptions_configured'))
                                    .e($statusText)
                                    .'</div>';
                            }

                            $output = [];
                            $processedRanges = [];

                            foreach ($exceptions as $date => $exception) {
                                if (! is_array($exception)) {
                                    continue;
                                }

                                // Skip individual dates that are part of a range (already displayed)
                                if (isset($exception['parent_range']) && in_array($exception['parent_range'], $processedRanges)) {
                                    continue;
                                }

                                // Skip range headers (we'll process them separately)
                                if (isset($exception['is_range_header'])) {
                                    continue;
                                }

                                $exceptionType = $exception['type'] ?? 'closed';
                                $icon = match ($exceptionType) {
                                    'holiday' => '🎉',
                                    'closed' => '🔒',
                                    'special_hours' => '⏰',
                                    'maintenance' => '🔧',
                                    'event' => '🎈',
                                    default => '📅'
                                };

                                $dateFormatted = '';
                                $badge = '';

                                try {
                                    if (isset($exception['is_range']) && $exception['is_range'] &&
                                        isset($exception['start_date']) && isset($exception['end_date'])) {
                                        // Date range
                                        $startDate = \Carbon\Carbon::parse($exception['start_date'])->format('M j');
                                        $endDate = \Carbon\Carbon::parse($exception['end_date'])->format('M j, Y');
                                        $dateFormatted = e("{$startDate} - {$endDate}");
                                        $badge = '<span class="font-medium">📆 Range</span>';
                                        $processedRanges[] = "range_{$exception['start_date']}_to_{$exception['end_date']}";
                                    } elseif (isset($exception['recurring']) && $exception['recurring'] && isset($exception['date'])) {
                                        // Recurring annual
                                        $dateFormatted = e('Every '.\Carbon\Carbon::parse($exception['date'])->format('F j'));
                                        $badge = '<span class="font-medium">🔄 Annual</span>';
                                    } elseif (strlen($date) === 5) {
                                        // MM-DD format (recurring)
                                        $dateFormatted = e('Every '.\Carbon\Carbon::createFromFormat('m-d', $date)->format('F j'));
                                        $badge = '<span class="font-medium">🔄 Annual</span>';
                                    } elseif (isset($exception['date'])) {
                                        // Single date
                                        $dateFormatted = e(\Carbon\Carbon::parse($exception['date'])->format('M j, Y'));
                                        $badge = '<span class="font-medium">📅 Single</span>';
                                    } else {
                                        // Fallback for date as key
                                        $dateFormatted = e(\Carbon\Carbon::parse($date)->format('M j, Y'));
                                        $badge = '<span class="font-medium">📅 Single</span>';
                                    }
                                } catch (\Exception $e) {
                                    // Skip invalid dates
                                    continue;
                                }

                                $label = (isset($exception['label']) && $exception['label']) ? ' - <strong>'.e($exception['label']).'</strong>' : '';
                                
                                $hours = '';
                                if ($exceptionType === 'special_hours' && isset($exception['hours']) && is_array($exception['hours']) && ! empty($exception['hours'])) {
                                    $hoursList = collect($exception['hours'])->map(function ($h) {
                                        return is_array($h) && isset($h['from'], $h['to']) ? e("{$h['from']}-{$h['to']}") : '';
                                    })->filter()->join(', ');
                                    $hours = $hoursList ? ' ⏰ '.$hoursList : '';
                                }
                                
                                $note = (isset($exception['note']) && $exception['note']) ? '<br><span class="text-xs italic text-gray-500">'.e($exception['note']).'</span>' : '';

                                $typeLabel = ucfirst(str_replace('_', ' ', $exceptionType));
                                $output[] = '<div class="mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">'
                                    .'<div class="flex items-center gap-2">'
                                    .'<span>'.$icon.'</span>'
                                    .'<strong>'.$dateFormatted.'</strong>'
                                    .$badge
                                    .'</div>'
                                    .'<div class="mt-1 text-sm text-gray-600 dark:text-gray-400">'
                                    .'📋 '.e($typeLabel)
                                    .$label
                                    .$hours
                                    .$note
                                    .'</div>'
                                    .'</div>';
                            }

                            // Join array elements into a single string for Html::make()
                            return '<div class="text-sm">'.implode('', $output).'</div>';
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->persistCollapsed(),
        ];
    }

    protected static function getDayComponents(): array
    {
        $days = [
            'monday' => ['label' => __('filament-opening-hours::opening-hours.monday'), 'icon' => '📅'],
            'tuesday' => ['label' => __('filament-opening-hours::opening-hours.tuesday'), 'icon' => '📅'],
            'wednesday' => ['label' => __('filament-opening-hours::opening-hours.wednesday'), 'icon' => '📅'],
            'thursday' => ['label' => __('filament-opening-hours::opening-hours.thursday'), 'icon' => '📅'],
            'friday' => ['label' => __('filament-opening-hours::opening-hours.friday'), 'icon' => '📅'],
            'saturday' => ['label' => __('filament-opening-hours::opening-hours.saturday'), 'icon' => '🎯'],
            'sunday' => ['label' => __('filament-opening-hours::opening-hours.sunday'), 'icon' => '🎯'],
        ];

        $components = [];

        foreach ($days as $key => $config) {
            $components[] = Section::make($config['label'])
                ->description(__('filament-opening-hours::opening-hours.configure_day_hours', ['day' => $config['label']]))
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(12)->schema([
                        Toggle::make("opening_hours.{$key}.enabled")
                            ->label(__('filament-opening-hours::opening-hours.open'))
                            ->live()
                            ->columnSpan(2),

                        Group::make([
                            Repeater::make("opening_hours.{$key}.hours")
                                ->schema([
                                    Grid::make(3)->schema([
                                        TimePicker::make('from')
                                            ->label('From')
                                            ->required()
                                            ->seconds(false)
                                            ->displayFormat('H:i')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                // Auto-enable business hours when time is set
                                                if ($state) {
                                                    $set('opening_hours_enabled', true);
                                                }
                                            })
                                            ->columnSpan(1),

                                        TimePicker::make('to')
                                            ->label(__('filament-opening-hours::opening-hours.to'))
                                            ->required()
                                            ->seconds(false)
                                            ->displayFormat('H:i')
                                            ->after('from')
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                // Auto-enable business hours when time is set
                                                if ($state) {
                                                    $set('opening_hours_enabled', true);
                                                }
                                            })
                                            ->columnSpan(1),

                                        Placeholder::make('duration')
                                            ->label(__('filament-opening-hours::opening-hours.duration'))
                                            ->content(function ($get) {
                                                $from = $get('from');
                                                $to = $get('to');

                                                if (! $from || ! $to || ! is_string($from) || ! is_string($to)) {
                                                    return '-';
                                                }

                                                try {
                                                    $fromTime = \Carbon\Carbon::createFromFormat('H:i', $from);
                                                    $toTime = \Carbon\Carbon::createFromFormat('H:i', $to);

                                                    if (! $fromTime || ! $toTime) {
                                                        return '-';
                                                    }

                                                    if ($toTime->lessThan($fromTime)) {
                                                        $toTime->addDay();
                                                    }

                                                    $duration = $fromTime->diffForHumans($toTime, true);

                                                    return $duration;
                                                } catch (\Exception $e) {
                                                    return '-';
                                                }
                                            })
                                            ->columnSpan(1),
                                    ]),
                                ])
                                ->addActionLabel(__('filament-opening-hours::opening-hours.add_time_slot'))
                                ->reorderableWithButtons()
                                ->defaultItems(0)
                                ->itemLabel(fn (array $state): ?string => isset($state['from'], $state['to'])
                                        ? "⏰ {$state['from']} - {$state['to']}"
                                        : '➕ '.__('filament-opening-hours::opening-hours.new_time_slot')
                                )
                                ->collapsed(false),
                        ])
                            ->columnSpan(10)
                            ->visible(fn ($get) => $get("opening_hours.{$key}.enabled")),
                    ]),
                ])
                ->collapsible()
                ->persistCollapsed()
                ->collapsed(true);
        }

        return $components;
    }
}
