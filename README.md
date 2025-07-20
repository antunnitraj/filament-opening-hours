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
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursField;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ... other fields
            
            // Option 1: Using schema spread (recommended)
            ...OpeningHoursForm::schema(),
            
            // Option 2: Using field component
            OpeningHoursField::make('opening_hours_data')
                ->label('Opening Hours'),
        ]);
}
```

### 4. Display in Tables

```php
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ... other columns
            
            // Option 1: Show current status (default)
            OpeningHoursColumn::make('status')
                ->label('Status'),
                
            // Option 2: Show today's hours
            OpeningHoursColumn::make('today_hours')
                ->label('Today')
                ->showToday(),
                
            // Option 3: Simple open/closed
            OpeningHoursColumn::make('simple_status')
                ->label('Open')
                ->showSimpleStatus(),
        ]);
}
```

### 5. Display in Infolists

```php
use KaraOdin\FilamentOpeningHours\Components\OpeningHoursEntry;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // ... other entries
            
            // Option 1: Full details (default)
            OpeningHoursEntry::make('opening_hours')
                ->label('Opening Hours'),
                
            // Option 2: Status only
            OpeningHoursEntry::make('status')
                ->label('Current Status')
                ->showStatusOnly(),
                
            // Option 3: Weekly hours only
            OpeningHoursEntry::make('hours')
                ->label('Weekly Hours')
                ->showWeeklyHours(),
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

## Component Options

### Form Component
```php
// Option 1: Schema spread (recommended) - returns array of components
...OpeningHoursForm::schema()

// Option 2: Field component - single component with all functionality
OpeningHoursField::make('opening_hours_data')->label('Opening Hours')
```

### Table Column Options
```php
// Default: shows current status with colored badge
OpeningHoursColumn::make('status')

// Show today's hours
OpeningHoursColumn::make('today')->showToday()

// Simple open/closed status
OpeningHoursColumn::make('open')->showSimpleStatus()
```

### Infolist Entry Options
```php
// Default: full details with status, hours, and exceptions
OpeningHoursEntry::make('hours')

// Status only
OpeningHoursEntry::make('status')->showStatusOnly()

// Weekly hours only
OpeningHoursEntry::make('schedule')->showWeeklyHours()
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