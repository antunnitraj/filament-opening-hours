<?php

namespace KaraOdin\FilamentOpeningHours\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;

class OpeningHoursForm extends Component
{
    protected string $view = 'filament-opening-hours::components.opening-hours-form';

    protected ?string $timezone = null;

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function timezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone ?? config('filament-opening-hours.default_timezone', 'Africa/Algiers');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            Section::make('Regular Opening Hours')
                ->description('Set your regular weekly opening hours')
                ->schema([
                    Grid::make(1)
                        ->schema($this->getDayComponents()),
                ]),

            Section::make('Exceptions & Holidays')
                ->description('Add special dates with different hours or closures')
                ->schema([
                    Repeater::make('exceptions')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    DatePicker::make('date')
                                        ->required()
                                        ->unique(fn ($get) => $get('../../exceptions'), 'date')
                                        ->columnSpan(1),

                                    Select::make('type')
                                        ->options(config('filament-opening-hours.exception_types', [
                                            'closed' => 'Closed',
                                            'holiday' => 'Holiday',
                                            'special_hours' => 'Special Hours',
                                            'maintenance' => 'Maintenance',
                                        ]))
                                        ->default('closed')
                                        ->live()
                                        ->columnSpan(1),

                                    TextInput::make('note')
                                        ->placeholder('Optional note')
                                        ->columnSpan(1),
                                ]),

                            Group::make([
                                Repeater::make('hours')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TimePicker::make('from')
                                                    ->required()
                                                    ->seconds(false)
                                                    ->format('H:i'),

                                                TimePicker::make('to')
                                                    ->required()
                                                    ->seconds(false)
                                                    ->format('H:i')
                                                    ->after('from'),
                                            ]),
                                    ])
                                    ->addActionLabel('Add Time Slot')
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        isset($state['from'], $state['to']) 
                                            ? "{$state['from']} - {$state['to']}" 
                                            : null
                                    ),
                            ])
                                ->visible(fn ($get) => $get('type') === 'special_hours'),
                        ])
                        ->addActionLabel('Add Exception')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => 
                            isset($state['date']) 
                                ? ($state['date'] . ($state['note'] ? " ({$state['note']})" : ''))
                                : 'New Exception'
                        ),
                ]),
        ]);
    }

    protected function getDayComponents(): array
    {
        $days = config('filament-opening-hours.days', [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ]);

        $components = [];

        foreach ($days as $key => $label) {
            $components[] = Group::make([
                Grid::make(12)
                    ->schema([
                        Toggle::make("{$key}.enabled")
                            ->label($label)
                            ->live()
                            ->columnSpan(3),

                        Group::make([
                            Repeater::make("{$key}.hours")
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TimePicker::make('from')
                                                ->required()
                                                ->seconds(false)
                                                ->format('H:i'),

                                            TimePicker::make('to')
                                                ->required()
                                                ->seconds(false)
                                                ->format('H:i')
                                                ->after('from'),
                                        ]),
                                ])
                                ->addActionLabel('Add Time Slot')
                                ->reorderableWithButtons()
                                ->simple()
                                ->itemLabel(fn (array $state): ?string => 
                                    isset($state['from'], $state['to']) 
                                        ? "{$state['from']} - {$state['to']}" 
                                        : null
                                ),
                        ])
                            ->columnSpan(9)
                            ->visible(fn ($get) => $get("{$key}.enabled")),
                    ]),
            ]);
        }

        return $components;
    }

    public function getState(): mixed
    {
        $state = parent::getState();

        // Transform the state to match spatie/opening-hours format
        if (is_array($state)) {
            $openingHours = [];
            $exceptions = [];

            // Process regular hours
            foreach (config('filament-opening-hours.days', []) as $key => $label) {
                if (isset($state[$key]['enabled']) && $state[$key]['enabled']) {
                    $dayHours = [];
                    if (isset($state[$key]['hours']) && is_array($state[$key]['hours'])) {
                        foreach ($state[$key]['hours'] as $slot) {
                            if (isset($slot['from'], $slot['to'])) {
                                $dayHours[] = $slot['from'] . '-' . $slot['to'];
                            }
                        }
                    }
                    $openingHours[$key] = $dayHours;
                } else {
                    $openingHours[$key] = [];
                }
            }

            // Process exceptions
            if (isset($state['exceptions']) && is_array($state['exceptions'])) {
                foreach ($state['exceptions'] as $exception) {
                    if (isset($exception['date'])) {
                        $exceptionHours = [];
                        if ($exception['type'] === 'special_hours' && isset($exception['hours'])) {
                            foreach ($exception['hours'] as $slot) {
                                if (isset($slot['from'], $slot['to'])) {
                                    $exceptionHours[] = $slot['from'] . '-' . $slot['to'];
                                }
                            }
                        }
                        $exceptions[$exception['date']] = $exceptionHours;
                    }
                }
            }

            return [
                'opening_hours' => $openingHours,
                'opening_hours_exceptions' => $exceptions,
            ];
        }

        return $state;
    }

    public function dehydrateState(array &$state, bool $isDehydrated = true): void
    {
        // This method is called when saving the form
        // We want to split the data into two separate fields
        if (is_array($state) && isset($state[$this->getName()])) {
            $componentState = $state[$this->getName()];
            
            if (is_array($componentState)) {
                if (isset($componentState['opening_hours'])) {
                    $state['opening_hours'] = $componentState['opening_hours'];
                }
                if (isset($componentState['opening_hours_exceptions'])) {
                    $state['opening_hours_exceptions'] = $componentState['opening_hours_exceptions'];
                }
            }
        }
    }
}