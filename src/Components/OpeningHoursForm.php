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

class OpeningHoursForm
{
    public static function make(): array
    {
        return [
            Section::make('Regular Opening Hours')
                ->description('Set your regular weekly opening hours')
                ->schema([
                    Grid::make(1)->schema(self::getDayComponents()),
                ]),

            Section::make('Exceptions & Holidays')
                ->description('Add special dates with different hours or closures')
                ->schema([
                    Repeater::make('opening_hours_exceptions')
                        ->schema([
                            Grid::make(3)->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('type')
                                    ->options([
                                        'closed' => 'Closed',
                                        'holiday' => 'Holiday', 
                                        'special_hours' => 'Special Hours',
                                        'maintenance' => 'Maintenance',
                                    ])
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
                                        Grid::make(2)->schema([
                                            TimePicker::make('from')
                                                ->required()
                                                ->seconds(false)
                                                ->displayFormat('H:i'),

                                            TimePicker::make('to')
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
                                            : null
                                    ),
                            ])->visible(fn ($get) => $get('type') === 'special_hours'),
                        ])
                        ->addActionLabel('Add Exception')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): ?string => 
                            isset($state['date']) 
                                ? ($state['date'] . ($state['note'] ? " ({$state['note']})" : ''))
                                : 'New Exception'
                        ),
                ]),
        ];
    }

    protected static function getDayComponents(): array
    {
        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $components = [];

        foreach ($days as $key => $label) {
            $components[] = Group::make([
                Grid::make(12)->schema([
                    Toggle::make("opening_hours.{$key}.enabled")
                        ->label($label)
                        ->live()
                        ->columnSpan(3),

                    Group::make([
                        Repeater::make("opening_hours.{$key}.hours")
                            ->schema([
                                Grid::make(2)->schema([
                                    TimePicker::make('from')
                                        ->required()
                                        ->seconds(false)
                                        ->displayFormat('H:i'),

                                    TimePicker::make('to')
                                        ->required()
                                        ->seconds(false)
                                        ->displayFormat('H:i')
                                        ->after('from'),
                                ]),
                            ])
                            ->addActionLabel('Add Time Slot')
                            ->reorderableWithButtons()
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['from'], $state['to']) 
                                    ? "{$state['from']} - {$state['to']}" 
                                    : null
                            ),
                    ])
                        ->columnSpan(9)
                        ->visible(fn ($get) => $get("opening_hours.{$key}.enabled")),
                ]),
            ]);
        }

        return $components;
    }
}