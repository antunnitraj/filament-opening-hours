<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;

class OpeningHoursForm
{
    public static function schema(): array
    {
        return [
            Section::make('Business Hours Configuration')
                ->description('Configure your business operating hours and timezone')
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('opening_hours_enabled')
                            ->label('Enable Business Hours')
                            ->helperText('Turn off to disable all business hours functionality')
                            ->default(true)
                            ->live()
                            ->columnSpan(1),

                        Select::make('timezone')
                            ->label('Timezone')
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                                return [$timezone => str_replace('_', ' ', $timezone)];
                            })->toArray())
                            ->searchable()
                            ->default('Africa/Algiers')
                            ->helperText('Select your business timezone')
                            ->columnSpan(1),
                    ]),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make('Weekly Schedule')
                ->description('Set your regular weekly operating hours')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Grid::make(1)->schema(self::getDayComponents()),
                ])
                ->visible(fn ($get) => $get('opening_hours_enabled'))
                ->collapsible()
                ->persistCollapsed(),

            Section::make('Exceptions & Special Hours')
                ->description('Manage holidays, special dates, and irregular hours')
                ->icon('heroicon-o-exclamation-triangle')
                ->headerActions([
                    Actions\Action::make('add_exception')
                        ->label('Add Exception')
                        ->icon('heroicon-o-plus')
                        ->color('primary')
                        ->form([
                            Grid::make(3)->schema([
                                Select::make('date_mode')
                                    ->label('Date Mode')
                                    ->options([
                                        'single' => 'Single Date',
                                        'range' => 'Date Range',
                                        'recurring' => 'Recurring Annual',
                                    ])
                                    ->default('single')
                                    ->live()
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('exception_type')
                                    ->label('Exception Type')
                                    ->options([
                                        'closed' => 'Closed',
                                        'holiday' => 'Holiday',
                                        'special_hours' => 'Special Hours',
                                        'maintenance' => 'Maintenance',
                                        'event' => 'Special Event',
                                    ])
                                    ->default('closed')
                                    ->live()
                                    ->required()
                                    ->columnSpan(2),
                            ]),

                            // Single Date
                            Grid::make(1)->schema([
                                DatePicker::make('exception_date')
                                    ->label('Date')
                                    ->required()
                                    ->helperText('Select a specific date for this exception'),
                            ])->visible(fn ($get) => $get('date_mode') === 'single'),

                            // Date Range
                            Grid::make(2)->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->required()
                                    ->after('start_date')
                                    ->helperText('Exception will apply to all dates in this range')
                                    ->columnSpan(1),
                            ])->visible(fn ($get) => $get('date_mode') === 'range'),

                            // Recurring Annual
                            Grid::make(2)->schema([
                                DatePicker::make('recurring_date')
                                    ->label('Annual Date')
                                    ->required()
                                    ->helperText('This exception will repeat every year on this date')
                                    ->columnSpan(2),
                            ])->visible(fn ($get) => $get('date_mode') === 'recurring'),

                            Grid::make(1)->schema([
                                TextInput::make('exception_label')
                                    ->label('Custom Label')
                                    ->placeholder('e.g., Christmas Day, Staff Training, etc.')
                                    ->maxLength(100),

                                TextInput::make('exception_note')
                                    ->label('Description')
                                    ->placeholder('Additional details about this exception')
                                    ->maxLength(255),
                            ]),

                            Section::make('Special Hours')
                                ->description('Define custom hours for this date')
                                ->schema([
                                    Repeater::make('exception_hours')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TimePicker::make('from')
                                                    ->label('From')
                                                    ->required()
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                                TimePicker::make('to')
                                                    ->label('To')
                                                    ->required()
                                                    ->seconds(false)
                                                    ->displayFormat('H:i')
                                                    ->after('from'),
                                            ]),
                                        ])
                                        ->addActionLabel('Add Time Slot')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->defaultItems(0)
                                        ->itemLabel(fn (array $state): ?string => 
                                            isset($state['from'], $state['to']) 
                                                ? "{$state['from']} - {$state['to']}" 
                                                : 'New Time Slot'
                                        ),
                                ])
                                ->visible(fn ($get) => $get('exception_type') === 'special_hours'),

                        ])
                        ->action(function (array $data, $get, $set) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];
                            
                            $exceptionData = [
                                'type' => $data['exception_type'],
                                'label' => $data['exception_label'] ?? '',
                                'note' => $data['exception_note'] ?? '',
                                'hours' => $data['exception_type'] === 'special_hours' ? ($data['exception_hours'] ?? []) : [],
                                'date_mode' => $data['date_mode'],
                            ];

                            switch ($data['date_mode']) {
                                case 'single':
                                    $key = $data['exception_date'];
                                    $exceptionData['date'] = $data['exception_date'];
                                    $exceptions[$key] = $exceptionData;
                                    break;
                                    
                                case 'range':
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
                                    break;
                                    
                                case 'recurring':
                                    $recurringDate = \Carbon\Carbon::parse($data['recurring_date']);
                                    $key = $recurringDate->format('m-d'); // MM-DD format for recurring
                                    $exceptionData['date'] = $data['recurring_date'];
                                    $exceptionData['recurring'] = true;
                                    $exceptions[$key] = $exceptionData;
                                    break;
                            }

                            $set('opening_hours_exceptions', $exceptions);
                        }),
                ])
                ->schema([
                    Placeholder::make('exceptions_list')
                        ->label('')
                        ->content(function ($get) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];
                            
                            if (empty($exceptions)) {
                                return '📝 **No exceptions configured yet**

Use the "Add Exception" button above to add:
• 📅 **Single dates** - Specific holidays or closures
• 📆 **Date ranges** - Vacation periods or seasonal changes  
• 🔄 **Recurring dates** - Annual holidays that repeat

*Examples: Christmas Day, Summer vacation (July 1-15), Every New Year*';
                            }

                            $output = [];
                            $processedRanges = [];
                            
                            foreach ($exceptions as $date => $exception) {
                                // Skip individual dates that are part of a range (already displayed)
                                if (isset($exception['parent_range']) && in_array($exception['parent_range'], $processedRanges)) {
                                    continue;
                                }
                                
                                // Skip range headers (we'll process them separately)
                                if (isset($exception['is_range_header'])) {
                                    continue;
                                }

                                $icon = match($exception['type']) {
                                    'holiday' => '🎉',
                                    'closed' => '🔒',
                                    'special_hours' => '⏰',
                                    'maintenance' => '🔧',
                                    'event' => '🎈',
                                    default => '📅'
                                };

                                $dateFormatted = '';
                                $badge = '';

                                if (isset($exception['is_range']) && $exception['is_range']) {
                                    // Date range
                                    $startDate = \Carbon\Carbon::parse($exception['start_date'])->format('M j');
                                    $endDate = \Carbon\Carbon::parse($exception['end_date'])->format('M j, Y');
                                    $dateFormatted = "{$startDate} - {$endDate}";
                                    $badge = '📆 **Range**';
                                    $processedRanges[] = "range_{$exception['start_date']}_to_{$exception['end_date']}";
                                } elseif (isset($exception['recurring']) && $exception['recurring']) {
                                    // Recurring annual
                                    $dateFormatted = "Every " . \Carbon\Carbon::parse($exception['date'])->format('F j');
                                    $badge = '🔄 **Annual**';
                                } elseif (strlen($date) === 5) {
                                    // MM-DD format (recurring)
                                    $dateFormatted = "Every " . \Carbon\Carbon::createFromFormat('m-d', $date)->format('F j');
                                    $badge = '🔄 **Annual**';
                                } else {
                                    // Single date
                                    $dateFormatted = \Carbon\Carbon::parse($date)->format('M j, Y');
                                    $badge = '📅 **Single**';
                                }

                                $label = $exception['label'] ? " - **{$exception['label']}**" : '';
                                $hours = $exception['type'] === 'special_hours' && !empty($exception['hours'])
                                    ? ' ⏰ ' . collect($exception['hours'])->map(fn($h) => "{$h['from']}-{$h['to']}")->join(', ')
                                    : '';
                                $note = $exception['note'] ? "\n   *{$exception['note']}*" : '';

                                $output[] = "{$icon} **{$dateFormatted}** {$badge}\n   📋 " . ucfirst(str_replace('_', ' ', $exception['type'])) . "{$label}{$hours}{$note}";
                            }

                            return implode("\n\n", $output);
                        })
                        ->extraAttributes(['class' => 'text-sm'])
                        ->columnSpanFull(),
                ])
                ->visible(fn ($get) => $get('opening_hours_enabled'))
                ->collapsible()
                ->persistCollapsed(),
        ];
    }

    protected static function getDayComponents(): array
    {
        $days = [
            'monday' => ['label' => 'Monday', 'icon' => '📅'],
            'tuesday' => ['label' => 'Tuesday', 'icon' => '📅'],
            'wednesday' => ['label' => 'Wednesday', 'icon' => '📅'],
            'thursday' => ['label' => 'Thursday', 'icon' => '📅'],
            'friday' => ['label' => 'Friday', 'icon' => '📅'],
            'saturday' => ['label' => 'Saturday', 'icon' => '🎯'],
            'sunday' => ['label' => 'Sunday', 'icon' => '🎯'],
        ];

        $components = [];

        foreach ($days as $key => $config) {
            $components[] = Section::make($config['label'])
                ->description("Configure {$config['label']} operating hours")
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(12)->schema([
                        Toggle::make("opening_hours.{$key}.enabled")
                            ->label('Open')
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
                                            ->columnSpan(1),

                                        TimePicker::make('to')
                                            ->label('To')
                                            ->required()
                                            ->seconds(false)
                                            ->displayFormat('H:i')
                                            ->after('from')
                                            ->columnSpan(1),

                                        Placeholder::make('duration')
                                            ->label('Duration')
                                            ->content(function ($get) {
                                                $from = $get('from');
                                                $to = $get('to');
                                                
                                                if (!$from || !$to) {
                                                    return '-';
                                                }

                                                $fromTime = \Carbon\Carbon::createFromFormat('H:i', $from);
                                                $toTime = \Carbon\Carbon::createFromFormat('H:i', $to);
                                                
                                                if ($toTime->lessThan($fromTime)) {
                                                    $toTime->addDay();
                                                }
                                                
                                                $duration = $fromTime->diffForHumans($toTime, true);
                                                return $duration;
                                            })
                                            ->columnSpan(1),
                                    ]),
                                ])
                                ->addActionLabel('Add Time Slot')
                                ->reorderableWithButtons()
                                ->defaultItems(0)
                                ->itemLabel(fn (array $state): ?string => 
                                    isset($state['from'], $state['to']) 
                                        ? "⏰ {$state['from']} - {$state['to']}" 
                                        : '➕ New Time Slot'
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