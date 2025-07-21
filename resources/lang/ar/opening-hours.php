<?php

return [
    // Form Labels and Descriptions
    'business_hours_configuration' => 'إعداد ساعات العمل',
    'business_hours_configuration_description' => 'تحديد المنطقة الزمنية وتفعيل/إلغاء تفعيل وظائف ساعات العمل',
    'weekly_schedule' => 'الجدول الأسبوعي',
    'weekly_schedule_description' => 'تحديد ساعات العمل الأسبوعية المنتظمة',
    'weekly_schedule_description_disabled' => '⚠️ ساعات العمل معطلة - قم بتكوين الساعات أدناه، ثم فعّل أعلاه للتنشيط',
    'exceptions_special_hours' => 'الاستثناءات والساعات الخاصة',
    'exceptions_special_hours_description' => 'إدارة العطل والتواريخ الخاصة والساعات غير المنتظمة',
    'exceptions_special_hours_description_disabled' => '⚠️ ساعات العمل معطلة - قم بتكوين الاستثناءات أدناه، ثم فعّل أعلاه للتنشيط',

    // Form Fields
    'enable_business_hours' => 'تفعيل ساعات العمل',
    'enable_business_hours_help' => 'يتم التفعيل تلقائياً عند تكوين الساعات. قم بالإلغاء للتعطيل المؤقت.',
    'timezone' => 'المنطقة الزمنية',
    'timezone_help' => 'اختر المنطقة الزمنية لعملك',
    'open' => 'مفتوح',
    'from' => 'من',
    'to' => 'إلى',
    'duration' => 'المدة',
    'add_time_slot' => 'إضافة فترة زمنية',
    'new_time_slot' => 'فترة زمنية جديدة',

    // Days of the Week
    'monday' => 'الاثنين',
    'tuesday' => 'الثلاثاء',
    'wednesday' => 'الأربعاء',
    'thursday' => 'الخميس',
    'friday' => 'الجمعة',
    'saturday' => 'السبت',
    'sunday' => 'الأحد',
    'today' => 'اليوم',

    // Day Descriptions
    'configure_day_hours' => 'تكوين ساعات العمل ليوم :day',

    // Exception Management
    'add_exception' => 'إضافة استثناء',
    'date_mode' => 'نمط التاريخ',
    'single_date' => 'تاريخ واحد',
    'date_range' => 'نطاق زمني',
    'recurring_annual' => 'سنوي متكرر',
    'exception_type' => 'نوع الاستثناء',
    'date' => 'التاريخ',
    'start_date' => 'تاريخ البداية',
    'end_date' => 'تاريخ النهاية',
    'annual_date' => 'التاريخ السنوي',
    'custom_label' => 'تسمية مخصصة',
    'description' => 'الوصف',
    'special_hours' => 'ساعات خاصة',
    'special_hours_description' => 'تحديد ساعات مخصصة لهذا التاريخ',

    // Exception Types
    'closed' => 'مغلق',
    'holiday' => 'عطلة',
    'special_hours_type' => 'ساعات خاصة',
    'maintenance' => 'صيانة',
    'event' => 'حدث خاص',

    // Exception Placeholders and Help
    'date_help' => 'اختر تاريخاً محدداً لهذا الاستثناء',
    'range_help' => 'سيتم تطبيق الاستثناء على جميع التواريخ في هذا النطاق',
    'annual_help' => 'سيتكرر هذا الاستثناء كل سنة في هذا التاريخ',
    'label_placeholder' => 'مثل: عيد الميلاد، تدريب الموظفين، إلخ.',
    'description_placeholder' => 'تفاصيل إضافية حول هذا الاستثناء',

    // Exception List
    'no_exceptions_configured' => '📝 **لم يتم تكوين أي استثناءات بعد**

استخدم زر "إضافة استثناء" أعلاه لإضافة:
• 📅 **تواريخ مفردة** - عطل محددة أو إغلاقات
• 📆 **نطاقات زمنية** - فترات إجازة أو تغييرات موسمية  
• 🔄 **تواريخ متكررة** - عطل سنوية تتكرر

*أمثلة: عيد الميلاد، إجازة الصيف (1-15 يوليو)، كل رأس سنة*',

    'no_exceptions_configured_disabled' => '

⚠️ **ملاحظة:** ساعات العمل معطلة حالياً. يمكنك تكوين الاستثناءات الآن، ثم تفعيل ساعات العمل أعلاه لتنشيطها.',

    // Status Messages
    'open_until' => 'مفتوح حتى :time',
    'closed_until' => 'مغلق حتى :time',
    'open_status' => 'مفتوح',
    'closed_status' => 'مغلق',
    'closed_today' => 'مغلق اليوم',
    'no_hours_configured' => 'لم يتم تكوين الساعات',
    'business_hours_disabled' => 'ساعات العمل معطلة',
    'status_unavailable' => 'الحالة غير متاحة',
    'not_configured' => 'غير مكون',
    'disabled' => 'معطل',
    'error' => 'خطأ',
    'error_status' => 'خطأ',

    // Days structure for easier translation access
    'days' => [
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
        'sunday' => 'الأحد',
    ],

    // Table Column
    'hours' => 'الساعات',
    'status' => 'الحالة',
    'schedule' => 'الجدول',
    'current_status' => 'الحالة الحالية',
    'weekly_schedule_short' => 'الجدول الأسبوعي',
    'hours_summary' => 'ملخص الساعات',

    // Infolist Entries
    'business_hours' => 'ساعات العمل',
    'weekly_schedule_grid' => 'الجدول الأسبوعي',
    'exceptions_holidays' => 'الاستثناءات والساعات الخاصة',
    'timezone_info' => 'معلومات المنطقة الزمنية',
    'operating_days' => 'إجمالي أيام العمل',
    'last_updated' => 'آخر تحديث',
    'error_loading_hours' => 'خطأ في تحميل ساعات العمل',

    // Exception Display
    'range_badge' => 'نطاق',
    'annual_badge' => 'سنوي',
    'single_badge' => 'مفرد',
    'every' => 'كل',

    // Time Formats
    'closes_at' => 'يغلق في',
    'opens_at' => 'يفتح في',
    'next' => 'التالي',

    // Validation Messages
    'time_required' => 'الوقت مطلوب',
    'end_after_start' => 'وقت الانتهاء يجب أن يكون بعد وقت البداية',
    'date_required' => 'التاريخ مطلوب',
    'end_date_after_start' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',

    // General
    'loading' => 'جارٍ التحميل...',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'delete' => 'حذف',
    'edit' => 'تعديل',
    'add' => 'إضافة',
    'remove' => 'إزالة',
    'confirm' => 'تأكيد',
];