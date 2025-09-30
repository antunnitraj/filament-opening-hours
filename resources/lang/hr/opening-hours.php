<?php

return [
    // Form Labels and Descriptions
    'business_hours_configuration' => 'Konfiguracija radnog vremena',
    'business_hours_configuration_description' => 'Postavite vremensku zonu i omogućite/onemogućite funkcionalnost radnog vremena',
    'weekly_schedule' => 'Tjedni raspored',
    'weekly_schedule_description' => 'Postavite svoje redovno tjedno radno vrijeme',
    'weekly_schedule_description_disabled' => '⚠️ Radno vrijeme je onemogućeno - Konfigurirajte sate ispod, zatim omogućite iznad za aktivaciju',
    'exceptions_special_hours' => 'Iznimke i posebni sati',
    'exceptions_special_hours_description' => 'Upravljajte praznicima, posebnim datumima i neredovnim satima',
    'exceptions_special_hours_description_disabled' => '⚠️ Radno vrijeme je onemogućeno - Konfigurirajte iznimke ispod, zatim omogućite iznad za aktivaciju',

    // Form Fields
    'enable_business_hours' => 'Omogući radno vrijeme',
    'enable_business_hours_help' => 'Automatski se omogućuje kada su sati konfigurirani. Isključite za privremeno onemogućavanje.',
    'timezone' => 'Vremenska zona',
    'timezone_help' => 'Odaberite vremensku zonu poslovanja',
    'open' => 'Otvoreno',
    'from' => 'Od',
    'to' => 'Do',
    'duration' => 'Trajanje',
    'add_time_slot' => 'Dodaj vremenski period',
    'new_time_slot' => 'Novi vremenski period',

    // Days of the Week
    'monday' => 'Ponedjeljak',
    'tuesday' => 'Utorak',
    'wednesday' => 'Srijeda',
    'thursday' => 'Četvrtak',
    'friday' => 'Petak',
    'saturday' => 'Subota',
    'sunday' => 'Nedjelja',
    'today' => 'Danas',

    // Day Descriptions
    'configure_day_hours' => 'Konfigurirajte radno vrijeme za :day',

    // Exception Management
    'add_exception' => 'Dodaj iznimku',
    'date_mode' => 'Način datuma',
    'single_date' => 'Jedan datum',
    'date_range' => 'Raspon datuma',
    'recurring_annual' => 'Godišnje ponavljanje',
    'exception_type' => 'Vrsta iznimke',
    'date' => 'Datum',
    'start_date' => 'Datum početka',
    'end_date' => 'Datum završetka',
    'annual_date' => 'Godišnji datum',
    'custom_label' => 'Prilagođena oznaka',
    'description' => 'Opis',
    'special_hours' => 'Posebni sati',
    'special_hours_description' => 'Definirajte prilagođene sate za ovaj datum',

    // Exception Types
    'closed' => 'Zatvoreno',
    'holiday' => 'Praznik',
    'special_hours_type' => 'Posebni sati',
    'maintenance' => 'Održavanje',
    'event' => 'Poseban događaj',

    // Exception Placeholders and Help
    'date_help' => 'Odaberite određeni datum za ovu iznimku',
    'range_help' => 'Iznimka će se primijeniti na sve datume u ovom rasponu',
    'annual_help' => 'Ova će se iznimka ponavljati svake godine na ovaj datum',
    'label_placeholder' => 'npr. Božić, Obuka zaposlenika, itd.',
    'description_placeholder' => 'Dodatni detalji o ovoj iznimci',

    // Exception List
    'no_exceptions_configured' => 'Nema konfiguriranih iznimki',

    'no_exceptions_configured_disabled' => 'Radno vrijeme je trenutno onemogućeno.',

    // Status Messages
    'open_until' => 'Otvoreno do :time',
    'closed_until' => 'Zatvoreno do :time',
    'open_status' => 'Otvoreno',
    'closed_status' => 'Zatvoreno',
    'closed_today' => 'Danas zatvoreno',
    'no_hours_configured' => 'Nema konfiguriranih sati',
    'business_hours_disabled' => 'Radno vrijeme onemogućeno',
    'status_unavailable' => 'Status nedostupan',
    'not_configured' => 'Nije konfigurirano',
    'disabled' => 'Onemogućeno',
    'error' => 'Greška',
    'error_status' => 'Greška',

    // Days structure for easier translation access
    'days' => [
        'monday' => 'Ponedjeljak',
        'tuesday' => 'Utorak',
        'wednesday' => 'Srijeda',
        'thursday' => 'Četvrtak',
        'friday' => 'Petak',
        'saturday' => 'Subota',
        'sunday' => 'Nedjelja',
    ],

    // Table Column
    'hours' => 'Sati',
    'status' => 'Status',
    'schedule' => 'Raspored',
    'current_status' => 'Trenutni status',
    'weekly_schedule_short' => 'Tjedni raspored',
    'hours_summary' => 'Sažetak sati',

    // Infolist Entries
    'business_hours' => 'Radno vrijeme',
    'weekly_schedule_grid' => 'Tjedni raspored',
    'exceptions_holidays' => 'Iznimke i posebni sati',
    'timezone_info' => 'Informacije o vremenskoj zoni',
    'operating_days' => 'Ukupno radnih dana',
    'last_updated' => 'Posljednje ažurirano',
    'error_loading_hours' => 'Greška pri učitavanju radnog vremena',

    // Exception Display
    'range_badge' => 'Raspon',
    'annual_badge' => 'Godišnje',
    'single_badge' => 'Jedan',
    'every' => 'Svaki',

    // Time Formats
    'closes_at' => 'Zatvara se u',
    'opens_at' => 'Otvara se u',
    'next' => 'Sljedeće',

    // Validation Messages
    'time_required' => 'Vrijeme je obavezno',
    'end_after_start' => 'Vrijeme završetka mora biti nakon vremena početka',
    'date_required' => 'Datum je obavezan',
    'end_date_after_start' => 'Datum završetka mora biti nakon datuma početka',

    // General
    'loading' => 'Učitavanje...',
    'save' => 'Spremi',
    'cancel' => 'Otkaži',
    'delete' => 'Obriši',
    'edit' => 'Uredi',
    'add' => 'Dodaj',
    'remove' => 'Ukloni',
    'confirm' => 'Potvrdi',
];
