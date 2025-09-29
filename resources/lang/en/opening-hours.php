<?php

return [
    // Form Labels and Descriptions
    'business_hours_configuration' => 'Business Hours Configuration',
    'business_hours_configuration_description' => 'Set your timezone and enable/disable business hours functionality',
    'weekly_schedule' => 'Weekly Schedule',
    'weekly_schedule_description' => 'Set your regular weekly operating hours',
    'weekly_schedule_description_disabled' => '⚠️ Business hours are disabled - Configure hours below, then enable above to activate',
    'exceptions_special_hours' => 'Exceptions & Special Hours',
    'exceptions_special_hours_description' => 'Manage holidays, special dates, and irregular hours',
    'exceptions_special_hours_description_disabled' => '⚠️ Business hours are disabled - Configure exceptions below, then enable above to activate',

    // Form Fields
    'enable_business_hours' => 'Enable Business Hours',
    'enable_business_hours_help' => 'Automatically enabled when hours are configured. Turn off to temporarily disable.',
    'timezone' => 'Timezone',
    'timezone_help' => 'Select your business timezone',
    'open' => 'Open',
    'from' => 'From',
    'to' => 'To',
    'duration' => 'Duration',
    'add_time_slot' => 'Add Time Slot',
    'new_time_slot' => 'New Time Slot',

    // Days of the Week
    'monday' => 'Monday',
    'tuesday' => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday' => 'Thursday',
    'friday' => 'Friday',
    'saturday' => 'Saturday',
    'sunday' => 'Sunday',
    'today' => 'Today',

    // Day Descriptions
    'configure_day_hours' => 'Configure :day operating hours',

    // Exception Management
    'add_exception' => 'Add Exception',
    'date_mode' => 'Date Mode',
    'single_date' => 'Single Date',
    'date_range' => 'Date Range',
    'recurring_annual' => 'Recurring Annual',
    'exception_type' => 'Exception Type',
    'date' => 'Date',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'annual_date' => 'Annual Date',
    'custom_label' => 'Custom Label',
    'description' => 'Description',
    'special_hours' => 'Special Hours',
    'special_hours_description' => 'Define custom hours for this date',

    // Exception Types
    'closed' => 'Closed',
    'holiday' => 'Holiday',
    'special_hours_type' => 'Special Hours',
    'maintenance' => 'Maintenance',
    'event' => 'Special Event',

    // Exception Placeholders and Help
    'date_help' => 'Select a specific date for this exception',
    'range_help' => 'Exception will apply to all dates in this range',
    'annual_help' => 'This exception will repeat every year on this date',
    'label_placeholder' => 'e.g., Christmas Day, Staff Training, etc.',
    'description_placeholder' => 'Additional details about this exception',

    // Exception List
    'no_exceptions_configured' => '📝 **No exceptions configured yet**

Use the "Add Exception" button above to add:
• 📅 **Single dates** - Specific holidays or closures
• 📆 **Date ranges** - Vacation periods or seasonal changes  
• 🔄 **Recurring dates** - Annual holidays that repeat

*Examples: Christmas Day, Summer vacation (July 1-15), Every New Year*',

    'no_exceptions_configured_disabled' => '

⚠️ **Note:** Business hours are currently disabled. You can configure exceptions now, then enable business hours above to activate them.',

    // Status Messages
    'open_until' => 'Open until :time',
    'closed_until' => 'Closed until :time',
    'open_status' => 'Open',
    'closed_status' => 'Closed',
    'closed_today' => 'Closed today',
    'no_hours_configured' => 'No hours configured',
    'business_hours_disabled' => 'Business hours disabled',
    'status_unavailable' => 'Status unavailable',
    'not_configured' => 'Not configured',
    'disabled' => 'Disabled',
    'error' => 'Error',
    'error_status' => 'Error',

    // Days structure for easier translation access
    'days' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],

    // Table Column
    'hours' => 'Hours',
    'status' => 'Status',
    'schedule' => 'Schedule',
    'current_status' => 'Current Status',
    'weekly_schedule_short' => 'Weekly Schedule',
    'hours_summary' => 'Hours Summary',

    // Infolist Entries
    'business_hours' => 'Business Hours',
    'weekly_schedule_grid' => 'Weekly Schedule',
    'exceptions_holidays' => 'Exceptions & Special Hours',
    'timezone_info' => 'Timezone Information',
    'operating_days' => 'Total operating days',
    'last_updated' => 'Last updated',
    'error_loading_hours' => 'Error loading business hours',

    // Exception Display
    'range_badge' => 'Range',
    'annual_badge' => 'Annual',
    'single_badge' => 'Single',
    'every' => 'Every',

    // Time Formats
    'closes_at' => 'Closes at',
    'opens_at' => 'Opens at',
    'next' => 'Next',

    // Validation Messages
    'time_required' => 'Time is required',
    'end_after_start' => 'End time must be after start time',
    'date_required' => 'Date is required',
    'end_date_after_start' => 'End date must be after start date',

    // General
    'loading' => 'Loading...',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'add' => 'Add',
    'remove' => 'Remove',
    'confirm' => 'Confirm',
];
