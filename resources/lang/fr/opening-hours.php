<?php

return [
    // Form Labels and Descriptions
    'business_hours_configuration' => 'Configuration des Heures d\'Ouverture',
    'business_hours_configuration_description' => 'Définissez votre fuseau horaire et activez/désactivez les fonctionnalités d\'heures d\'ouverture',
    'weekly_schedule' => 'Horaire Hebdomadaire',
    'weekly_schedule_description' => 'Définissez vos heures d\'ouverture hebdomadaires régulières',
    'weekly_schedule_description_disabled' => '⚠️ Les heures d\'ouverture sont désactivées - Configurez les heures ci-dessous, puis activez ci-dessus pour activer',
    'exceptions_special_hours' => 'Exceptions et Heures Spéciales',
    'exceptions_special_hours_description' => 'Gérez les jours fériés, dates spéciales et heures irrégulières',
    'exceptions_special_hours_description_disabled' => '⚠️ Les heures d\'ouverture sont désactivées - Configurez les exceptions ci-dessous, puis activez ci-dessus pour activer',

    // Form Fields
    'enable_business_hours' => 'Activer les Heures d\'Ouverture',
    'enable_business_hours_help' => 'Automatiquement activé lorsque les heures sont configurées. Désactivez pour désactiver temporairement.',
    'timezone' => 'Fuseau Horaire',
    'timezone_help' => 'Sélectionnez le fuseau horaire de votre entreprise',
    'open' => 'Ouvert',
    'from' => 'De',
    'to' => 'À',
    'duration' => 'Durée',
    'add_time_slot' => 'Ajouter un Créneau',
    'new_time_slot' => 'Nouveau Créneau',

    // Days of the Week
    'monday' => 'Lundi',
    'tuesday' => 'Mardi',
    'wednesday' => 'Mercredi',
    'thursday' => 'Jeudi',
    'friday' => 'Vendredi',
    'saturday' => 'Samedi',
    'sunday' => 'Dimanche',
    'today' => 'Aujourd\'hui',

    // Day Descriptions
    'configure_day_hours' => 'Configurez les heures d\'ouverture du :day',

    // Exception Management
    'add_exception' => 'Ajouter une Exception',
    'date_mode' => 'Mode de Date',
    'single_date' => 'Date Unique',
    'date_range' => 'Plage de Dates',
    'recurring_annual' => 'Récurrent Annuel',
    'exception_type' => 'Type d\'Exception',
    'date' => 'Date',
    'start_date' => 'Date de Début',
    'end_date' => 'Date de Fin',
    'annual_date' => 'Date Annuelle',
    'custom_label' => 'Libellé Personnalisé',
    'description' => 'Description',
    'special_hours' => 'Heures Spéciales',
    'special_hours_description' => 'Définir des heures personnalisées pour cette date',

    // Exception Types
    'closed' => 'Fermé',
    'holiday' => 'Jour Férié',
    'special_hours_type' => 'Heures Spéciales',
    'maintenance' => 'Maintenance',
    'event' => 'Événement Spécial',

    // Exception Placeholders and Help
    'date_help' => 'Sélectionnez une date spécifique pour cette exception',
    'range_help' => 'L\'exception s\'appliquera à toutes les dates de cette plage',
    'annual_help' => 'Cette exception se répétera chaque année à cette date',
    'label_placeholder' => 'ex: Noël, Formation du Personnel, etc.',
    'description_placeholder' => 'Détails supplémentaires sur cette exception',

    // Exception List
    'no_exceptions_configured' => '📝 **Aucune exception configurée pour le moment**

Utilisez le bouton "Ajouter une Exception" ci-dessus pour ajouter:
• 📅 **Dates uniques** - Jours fériés spécifiques ou fermetures
• 📆 **Plages de dates** - Périodes de vacances ou changements saisonniers  
• 🔄 **Dates récurrentes** - Jours fériés annuels qui se répètent

*Exemples: Noël, Vacances d\'été (1-15 juillet), Chaque Nouvel An*',

    'no_exceptions_configured_disabled' => '

⚠️ **Note:** Les heures d\'ouverture sont actuellement désactivées. Vous pouvez configurer les exceptions maintenant, puis activer les heures d\'ouverture ci-dessus pour les activer.',

    // Status Messages
    'open_until' => 'Ouvert jusqu\'à :time',
    'closed_until' => 'Fermé jusqu\'à :time',
    'open_status' => 'Ouvert',
    'closed_status' => 'Fermé',
    'closed_today' => 'Fermé aujourd\'hui',
    'no_hours_configured' => 'Aucune heure configurée',
    'business_hours_disabled' => 'Heures d\'ouverture désactivées',
    'status_unavailable' => 'Statut indisponible',
    'not_configured' => 'Non configuré',
    'disabled' => 'Désactivé',
    'error' => 'Erreur',
    'error_status' => 'Erreur',

    // Days structure for easier translation access
    'days' => [
        'monday' => 'Lundi',
        'tuesday' => 'Mardi',
        'wednesday' => 'Mercredi',
        'thursday' => 'Jeudi',
        'friday' => 'Vendredi',
        'saturday' => 'Samedi',
        'sunday' => 'Dimanche',
    ],

    // Table Column
    'hours' => 'Heures',
    'status' => 'Statut',
    'schedule' => 'Horaire',
    'current_status' => 'Statut Actuel',
    'weekly_schedule_short' => 'Horaire Hebdomadaire',
    'hours_summary' => 'Résumé des Heures',

    // Infolist Entries
    'business_hours' => 'Heures d\'Ouverture',
    'weekly_schedule_grid' => 'Horaire Hebdomadaire',
    'exceptions_holidays' => 'Exceptions et Heures Spéciales',
    'timezone_info' => 'Information sur le Fuseau Horaire',
    'operating_days' => 'Total des jours d\'ouverture',
    'last_updated' => 'Dernière mise à jour',
    'error_loading_hours' => 'Erreur lors du chargement des heures d\'ouverture',

    // Exception Display
    'range_badge' => 'Plage',
    'annual_badge' => 'Annuel',
    'single_badge' => 'Unique',
    'every' => 'Chaque',

    // Time Formats
    'closes_at' => 'Ferme à',
    'opens_at' => 'Ouvre à',
    'next' => 'Suivant',

    // Validation Messages
    'time_required' => 'L\'heure est requise',
    'end_after_start' => 'L\'heure de fin doit être après l\'heure de début',
    'date_required' => 'La date est requise',
    'end_date_after_start' => 'La date de fin doit être après la date de début',

    // General
    'loading' => 'Chargement...',
    'save' => 'Enregistrer',
    'cancel' => 'Annuler',
    'delete' => 'Supprimer',
    'edit' => 'Modifier',
    'add' => 'Ajouter',
    'remove' => 'Retirer',
    'confirm' => 'Confirmer',
];