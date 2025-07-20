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
                            Grid::make(2)->schema([
                                DatePicker::make('exception_date')
                                    ->label('Date')
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
                                    ->columnSpan(1),
                            ]),

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

                            Toggle::make('is_recurring')
                                ->label('Recurring Annual Exception')
                                ->helperText('This exception will repeat every year on the same date')
                                ->default(false),
                        ])
                        ->action(function (array $data, $get, $set) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];
                            
                            $exceptionKey = $data['exception_date'];
                            if ($data['is_recurring'] ?? false) {
                                $exceptionKey = date('m-d', strtotime($data['exception_date']));
                            }

                            $exception = [
                                'type' => $data['exception_type'],
                                'label' => $data['exception_label'] ?? '',
                                'note' => $data['exception_note'] ?? '',
                                'recurring' => $data['is_recurring'] ?? false,
                                'hours' => $data['exception_type'] === 'special_hours' ? ($data['exception_hours'] ?? []) : [],
                            ];

                            $exceptions[$exceptionKey] = $exception;
                            $set('opening_hours_exceptions', $exceptions);
                        }),
                ])
                ->schema([
                    Placeholder::make('exceptions_list')
                        ->label('')
                        ->content(function ($get) {
                            $exceptions = $get('opening_hours_exceptions') ?? [];
                            
                            if (empty($exceptions)) {
                                return 'No exceptions configured. Use the "Add Exception" button above to add holidays or special hours.';
                            }

                            $output = [];
                            foreach ($exceptions as $date => $exception) {
                                $icon = match($exception['type']) {
                                    'holiday' => '🎉',
                                    'closed' => '🔒',
                                    'special_hours' => '⏰',
                                    'maintenance' => '🔧',
                                    'event' => '🎈',
                                    default => '📅'
                                };

                                $dateFormatted = $exception['recurring'] ?? false 
                                    ? "Every " . date('F j', strtotime('2000-' . $date))
                                    : date('M j, Y', strtotime($date));

                                $label = $exception['label'] ? " ({$exception['label']})" : '';
                                $hours = $exception['type'] === 'special_hours' && !empty($exception['hours'])
                                    ? ' - ' . collect($exception['hours'])->map(fn($h) => "{$h['from']}-{$h['to']}")->join(', ')
                                    : '';

                                $output[] = "{$icon} **{$dateFormatted}**: {$exception['type']}{$label}{$hours}";
                            }

                            return implode("\n", $output);
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