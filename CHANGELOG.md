# Changelog

All notable changes to `filament-opening-hours` will be documented in this file.

## v3.0.0 - 2025-01-XX

### 🎉 MAJOR UPDATE: Filament v4 Support!

This is a **breaking change** that adds support for Filament v4 and drops support for Filament v3.

#### ⬆️ Upgrade Requirements
- **BREAKING:** Now requires **Laravel 11.28+** (previously Laravel 10.0+)
- **BREAKING:** Now requires **Filament 4.0+** (previously Filament 3.0+)
- **BREAKING:** Now requires **Livewire v3** (automatically included with Filament v4)
- Still supports PHP 8.1+

#### ✨ What's New
- Full compatibility with Filament v4
- Updated to work with the latest Livewire v3 features
- Modernized codebase with v4 best practices
- All components work seamlessly with Filament v4 panels

#### 🔧 Technical Changes
- Updated Livewire entangle syntax: `$wire.$entangle()` → `$wire.entangle()`
- Asset registration updated for Filament v4 compatibility
- All components verified to work with v4 base classes
- Dev dependencies updated for Laravel 11/Filament v4

#### 🚀 Migration Guide
To upgrade from v2.x to v3.x:
1. Ensure you're running Laravel 11.28+ and Filament 4.0+
2. Update your composer.json: `"karaodin/filament-opening-hours": "^3.0"`
3. Run `composer update`
4. No code changes needed - all APIs remain the same!

#### 📦 For New Installations
```bash
composer require karaodin/filament-opening-hours
```

Requirements:
- PHP 8.1+
- Laravel 11.28+
- Filament 4.0+

## v2.1.0 - 2025-07-20

### ✨ Enhanced Exception Management with Date Ranges

#### New Date Range Features
- **NEW:** Date range support for exceptions (e.g., July 1-15 vacation period)
- **NEW:** Three date modes: Single Date, Date Range, Recurring Annual
- **NEW:** Range validation with end date must be after start date
- **NEW:** Smart range processing for spatie/opening-hours compatibility
- **NEW:** Range badges in exception display (📆 Range, 🔄 Annual, 📅 Single)

#### Enhanced Exception Interface
- **IMPROVED:** Modal form with date mode selector
- **IMPROVED:** Range date picker with clear validation messages
- **IMPROVED:** Exception list formatting with range indicators
- **IMPROVED:** Better sorting: single dates → ranges → recurring
- **IMPROVED:** Enhanced infolist display with range formatting

#### Examples of New Functionality
```php
// Single Date Exception
"December 25, 2024" → Christmas Day (Holiday - Closed)

// Date Range Exception  
"July 1-15, 2024" → Summer Vacation (Closed for 15 days)

// Recurring Annual Exception
"Every December 25" → Annual Christmas Holiday
```

#### Technical Improvements
- **ENHANCED:** Dual storage system for UI display and spatie library compatibility
- **ENHANCED:** Range processing that creates individual dates while maintaining range metadata
- **ENHANCED:** Improved exception filtering and display logic
- **ENHANCED:** Better data handling in HasOpeningHours trait

### Backward Compatibility
- ✅ Existing single date exceptions continue to work unchanged
- ✅ Existing recurring exceptions (MM-DD format) still supported
- ✅ No breaking changes to API or component usage

---

## v2.0.0 - 2025-07-20

### 🎉 MEGA UPDATE: Premium Features That Exceed Paid Plugins!

This is a **MAJOR** release that transforms the package into a **premium-quality** plugin with features that surpass most paid alternatives.

#### ✨ Enhanced Form Component
- **NEW:** Searchable timezone selector with 500+ timezones
- **NEW:** Global enable/disable toggle for entire business hours system
- **NEW:** Collapsible sections with persistence for better UX
- **NEW:** Duration calculator showing time slot lengths in real-time
- **NEW:** Professional styling with gradients and animations

#### 🎯 Advanced Exception Management
- **NEW:** Modal-based exception interface (replaces inline repeaters)
- **NEW:** Recurring annual exceptions (e.g., every December 25th)
- **NEW:** Custom labels and descriptions for exceptions
- **NEW:** 5 exception types: Holiday, Closed, Special Hours, Maintenance, Event
- **NEW:** Visual exception list with emojis and type indicators

#### 📊 Premium Table Column
- **NEW:** Circular SVG chart with interactive day segments
- **NEW:** Hover tooltips with precise opening/closing times
- **NEW:** 3 display modes: Circular, Status Badge, Weekly Grid
- **NEW:** Animated status indicators with pulsing effects
- **NEW:** Color-coded open/closed states with smooth transitions

#### 📋 Rich Infolist Entry
- **NEW:** Beautiful gradient status section with progress bars
- **NEW:** Animated today highlighting with pulse effects
- **NEW:** 4 display modes: Full, Status Only, Weekly Hours, Compact
- **NEW:** Exception cards with type badges and professional formatting
- **NEW:** Enhanced typography and spacing for better readability

#### 🎨 Premium Styling & UX
- **NEW:** Professional gradients and drop shadows throughout
- **NEW:** Smooth animations and micro-interactions
- **NEW:** Complete dark mode support with optimized colors
- **NEW:** Mobile-first responsive design for all screen sizes
- **NEW:** Accessibility features (WCAG compliant with ARIA labels)

#### ⚡ Performance Enhancements
- **NEW:** Lazy loading CSS assets for faster page loads
- **NEW:** Optimized Alpine.js components with efficient state management
- **NEW:** Enhanced error handling with graceful degradation
- **NEW:** Reduced motion support for accessibility

#### 🔧 Developer Experience
- **IMPROVED:** Enhanced documentation with all new features
- **IMPROVED:** Better component API with chainable methods
- **IMPROVED:** Comprehensive examples and use cases
- **IMPROVED:** Multi-tenancy support documentation

### 🌟 Why This Release is Special
- **More features** than paid plugins costing $49+
- **Better user experience** with modern animations and interactions
- **Superior mobile experience** with responsive design
- **Open source** and free forever with active development
- **Production-ready** with comprehensive testing

### Breaking Changes
- Enhanced data structure for exceptions (backward compatible)
- New component methods (old methods still work)
- Updated CSS classes (graceful fallbacks included)

### Migration Guide
No breaking changes for existing users. New features are additive and backward compatible.

---

## v1.0.1 - 2025-01-20

### Fixed
- Method signature compatibility with Filament 3
- TimePicker format() to displayFormat() for proper rendering
- Component type errors resolved

## v1.0.0 - 2025-01-20

### Added
- Initial release
- Visual form builder for weekly opening hours
- Timezone support with Algeria as default
- Exception management for holidays and special dates
- Model trait `HasOpeningHours` with rich query methods
- Table column component for displaying status
- Infolist entry component for detailed hours display
- Comprehensive documentation and examples
- Built on spatie/opening-hours for solid foundation