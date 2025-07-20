# Filament Opening Hours

[![Latest Version on Packagist](https://img.shields.io/packagist/v/karaodin/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/karaodin/filament-opening-hours)
[![Total Downloads](https://img.shields.io/packagist/dt/karaodin/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/karaodin/filament-opening-hours)

A **premium-quality** Filament plugin for managing business opening hours with advanced timezone support, visual interfaces, and comprehensive exception management. Built on top of [spatie/opening-hours](https://github.com/spatie/opening-hours).

## ✨ Features

- 🕐 **Advanced Visual Form Builder** - Intuitive interface with collapsible sections and live validation
- 🌍 **Comprehensive Timezone Support** - Searchable timezone dropdown with Algeria as default
- 📅 **Smart Exception Management** - Modal-based system with recurring annual exceptions
- 🎯 **Multiple Display Modes** - Circular charts, status badges, and detailed weekly views
- 🔄 **Global Enable/Disable** - Master toggle for entire business hours system
- 📊 **Rich Table Columns** - Interactive circular displays with hover tooltips
- 📋 **Enhanced Infolist Entries** - Beautiful formatted displays with animations
- 🎨 **Premium Styling** - Professional gradients, animations, and dark mode support
- ⚡ **Performance Optimized** - Lazy loading assets and efficient queries
- 📱 **Mobile Responsive** - Optimized for all screen sizes

## 🚀 Installation

You can install the package via composer:

```bash
composer require karaodin/filament-opening-hours
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-opening-hours-config"
```

## 💻 Usage

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
    $table->boolean('opening_hours_enabled')->default(true);
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
            
            ...OpeningHoursForm::schema(),
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
            
            // Option 1: Circular visual display (recommended)
            OpeningHoursColumn::make('opening_hours')
                ->label('Hours')
                ->circular()
                ->showTooltips(),
                
            // Option 2: Status badge
            OpeningHoursColumn::make('status')
                ->label('Status')
                ->status(),
                
            // Option 3: Weekly overview
            OpeningHoursColumn::make('schedule')
                ->label('Schedule')
                ->weekly(),
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
            
            // Option 1: Full details (recommended)
            OpeningHoursEntry::make('opening_hours')
                ->label('Business Hours')
                ->full(),
                
            // Option 2: Status only
            OpeningHoursEntry::make('status')
                ->label('Current Status')
                ->statusOnly(),
                
            // Option 3: Weekly hours only
            OpeningHoursEntry::make('hours')
                ->label('Weekly Schedule')
                ->weeklyHours(),
                
            // Option 4: Compact summary
            OpeningHoursEntry::make('summary')
                ->label('Hours Summary')
                ->compact(),
        ]);
}
```

## 🎯 Model Methods

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

## ⚙️ Configuration

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
        'event' => 'Special Event',
    ],
];
```

## 🎨 Component Options

### Form Component
```php
// Complete form schema with all features
...OpeningHoursForm::schema()

// Features included:
// - Global enable/disable toggle
// - Searchable timezone selector
// - Collapsible day sections with duration display
// - Modal-based exception management
// - Recurring annual exceptions
// - Custom labels and descriptions
```

### Table Column Options
```php
// Circular display (default)
OpeningHoursColumn::make('hours')
    ->circular()                    // Circular chart with day segments
    ->showTooltips()               // Hover tooltips with precise times
    ->showCurrentStatus()          // Center status indicator
    ->timezone('Africa/Algiers')   // Custom timezone

// Status badge
OpeningHoursColumn::make('status')
    ->status()                     // Badge with current status
    ->showTooltips()               // Next open/close times in tooltip

// Weekly overview
OpeningHoursColumn::make('weekly')
    ->weekly()                     // 7-day grid view with status dots
    ->showTooltips()               // Day details on hover
```

### Infolist Entry Options
```php
// Full details (default)
OpeningHoursEntry::make('hours')
    ->full()                       // Complete display with all sections
    ->showStatus()                 // Current status section
    ->showExceptions()             // Exceptions and holidays
    ->showTimezone()               // Timezone information

// Status only
OpeningHoursEntry::make('status')
    ->statusOnly()                 // Just current status with animation

// Weekly hours
OpeningHoursEntry::make('schedule')
    ->weeklyHours()                // Weekly schedule grid

// Compact summary
OpeningHoursEntry::make('summary')
    ->compact()                    // Minimal overview with stats
```

## 📊 Data Structure

The plugin stores data in this enhanced format:

```json
{
    "opening_hours_enabled": true,
    "timezone": "Africa/Algiers",
    "opening_hours": {
        "monday": {
            "enabled": true,
            "hours": [
                {"from": "09:00", "to": "12:00"},
                {"from": "14:00", "to": "17:00"}
            ]
        },
        "tuesday": {
            "enabled": true,
            "hours": [{"from": "09:00", "to": "17:00"}]
        },
        "wednesday": {"enabled": false},
        // ... other days
    },
    "opening_hours_exceptions": {
        "2024-12-25": {
            "type": "holiday",
            "label": "Christmas Day",
            "note": "Merry Christmas!",
            "hours": [],
            "recurring": false
        },
        "12-31": {
            "type": "special_hours",
            "label": "New Year's Eve",
            "hours": [{"from": "09:00", "to": "15:00"}],
            "recurring": true
        }
    }
}
```

## 🎯 Advanced Features

### Exception Management
- **Modal Interface**: Clean, intuitive exception management
- **Recurring Exceptions**: Annual holidays (e.g., every December 25th)
- **Custom Labels**: Personalized exception names
- **Multiple Types**: Holiday, Closed, Special Hours, Maintenance, Events
- **Rich Descriptions**: Additional notes for each exception

### Visual Enhancements
- **Circular Charts**: SVG-based day segments with hover effects
- **Status Animations**: Pulsing indicators and progress bars
- **Gradient Styling**: Professional color schemes
- **Dark Mode**: Full dark theme support
- **Mobile Responsive**: Optimized for all devices

### Performance Features
- **Lazy Loading**: Assets loaded only when needed
- **Efficient Queries**: Optimized database interactions
- **Caching Support**: Built-in cache compatibility
- **Error Handling**: Graceful degradation on errors

## 🔧 Multi-Tenancy Support

Compatible with [stancl/tenancy](https://tenancyforlaravel.com/):

```php
// In TenantPanelProvider (not OwnerPanelProvider)
->plugins([
    \KaraOdin\FilamentOpeningHours\OpeningHoursPlugin::make(),
])

// Tenant-specific migration
php artisan make:migration add_opening_hours_to_businesses --path=database/migrations/tenant

// Run across all tenants
php artisan tenants:migrate --path=database/migrations/tenant
```

## 📱 Browser Support

- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
- ✅ Mobile Safari
- ✅ Chrome Mobile

## 🚀 Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+

## 🧪 Testing

```bash
composer test
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒 Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## 🙏 Credits

- [Nihat Mahdi](https://github.com/karaOdin)
- [All Contributors](../../contributors)
- Built on [spatie/opening-hours](https://github.com/spatie/opening-hours)
- Inspired by various business hours plugins

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 🌟 Why Choose This Plugin?

### vs. Paid Alternatives
- ✅ **Free & Open Source**
- ✅ **More Features** than most paid plugins
- ✅ **Better Performance** with lazy loading
- ✅ **Modern UI/UX** with animations and gradients
- ✅ **Active Development** with regular updates

### Premium Features
- 🎨 **Professional Design** - Matches Filament's aesthetic perfectly
- ⚡ **Performance Optimized** - Minimal impact on load times
- 🔧 **Highly Customizable** - Multiple display modes and options
- 📱 **Mobile First** - Responsive design for all devices
- 🌙 **Dark Mode Ready** - Full dark theme support
- ♿ **Accessibility** - WCAG compliant with proper ARIA labels

**Experience the difference - try it today!** 🚀