<?php

namespace KaraOdin\FilamentOpeningHours;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OpeningHoursServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-opening-hours';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        // Register assets
        FilamentAsset::register([
            Css::make('filament-opening-hours', __DIR__ . '/../resources/css/opening-hours.css')
                ->loadedOnRequest(),
        ], 'karaodin/filament-opening-hours');
    }
}