# Filament Opening Hours

[![Latest Version on Packagist](https://img.shields.io/packagist/v/karaodin/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/karaodin/filament-opening-hours)
[![Total Downloads](https://img.shields.io/packagist/dt/karaodin/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/karaodin/filament-opening-hours)

A comprehensive Filament plugin for managing business opening hours with timezone support, exceptions, and holidays. Built on top of [spatie/opening-hours](https://github.com/spatie/opening-hours).

## Features

- 🕐 **Visual Schedule Builder** - Intuitive form interface for setting weekly hours
- 🌍 **Timezone Support** - Full timezone support with Algeria as default
- 📅 **Exception Management** - Handle holidays, special dates, and closures
- 🔍 **Query Methods** - Rich API for checking if business is open/closed
- 📊 **Display Components** - Table columns and infolist entries
- 🎨 **Filament Integration** - Seamless integration with Filament panels

## Installation

You can install the package via composer:

```bash
composer require karaodin/filament-opening-hours
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-opening-hours-config"
```

## Usage

### 1. Add the Plugin to Your Panel

```php
use KaraOdin\FilamentOpeningHours\OpeningHoursPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(new OpeningHoursPlugin());
}
```

### 2. Prepare Your Model

Add the trait to your model and ensure you have the required database columns:

```php
use KaraOdin\FilamentOpeningHours\Concerns\HasOpeningHours;

class Restaurant extends Model
{
    use HasOpeningHours;

    protected $casts = [
        'opening_hours' => 'array',
        'opening_hours_exceptions' => 'array',
    ];
}
```

**Migration example:**

```php
Schema::table('restaurants', function (Blueprint $table) {
    $table->json('opening_hours')->nullable();
    $table->json('opening_hours_exceptions')->nullable();
    $table->string('timezone')->default('Africa/Algiers');
});
```

### 3. Use in Forms

```php
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursForm;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ... other fields
            
            OpeningHoursForm::make('opening_hours')
                ->timezone('Africa/Algiers'), // Optional, uses config default
        ]);
}
```

### 4. Display in Tables

```php
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursTable;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ... other columns
            
            OpeningHoursTable::make('opening_hours')
                ->label('Status')
                ->showStatus() // Shows "Open until 17:00" or "Closed until 09:00"
                ->timezone('Africa/Algiers'),
        ]);
}
```

### 5. Display in Infolists

```php
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursInfolist;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // ... other entries
            
            OpeningHoursInfolist::make('opening_hours')
                ->label('Opening Hours')
                ->showStatus() // Shows current status
                ->showExceptions(), // Shows exceptions/holidays
        ]);
}
```

## Model Methods

The `HasOpeningHours` trait provides powerful query methods:

```php
$restaurant = Restaurant::first();

// Check if currently open
$restaurant->isOpen(); // true/false
$restaurant->isClosed(); // true/false

// Check specific times
$restaurant->isOpen(Carbon::parse('2024-01-15 14:30')); // true/false

// Check specific days
$restaurant->isOpenOn('monday'); // true/false
$restaurant->isClosedOn('sunday'); // true/false

// Get next opening/closing times
$restaurant->nextOpen(); // Carbon instance or null
$restaurant->nextClose(); // Carbon instance or null

// Get current status with human readable format
$restaurant->getCurrentStatus(); // "Open until 17:00" or "Closed until 09:00"

// Get hours for specific day/date
$restaurant->getOpeningHoursForDay('monday'); // ['09:00-17:00']
$restaurant->getOpeningHoursForDate(Carbon::today()); // ['09:00-17:00']

// Exception management
$restaurant->addException('2024-12-25', []); // Closed on Christmas
$restaurant->addException('2024-12-31', ['09:00-15:00']); // Special hours
$restaurant->removeException('2024-12-25');
$restaurant->hasException('2024-12-25'); // true/false
```

## Configuration

The config file allows you to customize:

```php
return [
    // Default timezone
    'default_timezone' => 'Africa/Algiers',
    
    // Time format for display
    'time_format' => 'H:i',
    
    // Days of the week
    'days' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        // ...
    ],
    
    // Default opening hours
    'defaults' => [
        'monday' => ['09:00-17:00'],
        'tuesday' => ['09:00-17:00'],
        // ...
    ],
    
    // Exception types
    'exception_types' => [
        'closed' => 'Closed',
        'holiday' => 'Holiday',
        'special_hours' => 'Special Hours',
        'maintenance' => 'Maintenance',
    ],
];
```

## Form Component Options

```php
OpeningHoursForm::make('opening_hours')
    ->timezone('Africa/Algiers') // Set specific timezone
```

## Table Component Options

```php
OpeningHoursTable::make('opening_hours')
    ->showStatus() // Show current status (default: true)
    ->showToday() // Show today's hours instead of status
    ->timezone('Africa/Algiers') // Set specific timezone
```

## Infolist Component Options

```php
OpeningHoursInfolist::make('opening_hours')
    ->showStatus() // Show current status section (default: true)
    ->showExceptions() // Show exceptions section (default: true)
    ->timezone('Africa/Algiers') // Set specific timezone
```

## Data Structure

The plugin stores data in this format:

```json
{
    "opening_hours": {
        "monday": ["09:00-12:00", "14:00-17:00"],
        "tuesday": ["09:00-17:00"],
        "wednesday": [],
        "thursday": ["09:00-17:00"],
        "friday": ["09:00-17:00"],
        "saturday": ["10:00-16:00"],
        "sunday": []
    },
    "opening_hours_exceptions": {
        "2024-12-25": [],
        "2024-12-31": ["09:00-15:00"],
        "2024-01-01": []
    }
}
```

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Nihat Mahdi](https://github.com/karaOdin)
- [All Contributors](../../contributors)
- Built on [spatie/opening-hours](https://github.com/spatie/opening-hours)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.