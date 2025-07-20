<?php

namespace KaraOdin\FilamentOpeningHours;

use Filament\Contracts\Plugin;
use Filament\Panel;

class OpeningHoursPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'opening-hours';
    }

    public function register(Panel $panel): void
    {
        // Plugin registration logic if needed
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot logic if needed
    }
}