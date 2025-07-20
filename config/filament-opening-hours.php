<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone to use for opening hours when none is specified.
    | This should be a valid timezone identifier.
    |
    */
    'default_timezone' => 'Africa/Algiers',

    /*
    |--------------------------------------------------------------------------
    | Time Format
    |--------------------------------------------------------------------------
    |
    | The format to use when displaying times in the form fields.
    | Uses 24-hour format by default.
    |
    */
    'time_format' => 'H:i',

    /*
    |--------------------------------------------------------------------------
    | Days of Week
    |--------------------------------------------------------------------------
    |
    | The days of the week to display in the form. You can customize
    | the order and which days are shown.
    |
    */
    'days' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Opening Hours
    |--------------------------------------------------------------------------
    |
    | Default opening hours structure for new entries.
    |
    */
    'defaults' => [
        'monday' => ['09:00-17:00'],
        'tuesday' => ['09:00-17:00'],
        'wednesday' => ['09:00-17:00'],
        'thursday' => ['09:00-17:00'],
        'friday' => ['09:00-17:00'],
        'saturday' => [],
        'sunday' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Types
    |--------------------------------------------------------------------------
    |
    | Available exception types for special dates or holidays.
    |
    */
    'exception_types' => [
        'closed' => 'Closed',
        'holiday' => 'Holiday',
        'special_hours' => 'Special Hours',
        'maintenance' => 'Maintenance',
    ],
];