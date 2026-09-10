# Design Language System

This document outlines the design language system derived from the `_theme` directory, providing a comprehensive reference for UI components, styling patterns, and implementation guidelines.

## Overview

The design language is extracted from the `_theme` directory which contains a complete Bootstrap 5-based admin dashboard template with custom components, styling, and JavaScript functionality.

## Color System

Based on the CSS variables defined in `_theme/public/assets/css/style.css`:

### Primary Colors
- `--primary-50`: #E4F1FF
- `--primary-100`: #BFDCFF
- `--primary-200`: #95C7FF
- `--primary-300`: #6BB1FF
- `--primary-400`: #519FFF
- `--primary-500`: #458EFF
- `--primary-600`: #487FFF (Brand primary)
- `--primary-700`: #486CEA
- `--primary-800`: #4759D6
- `--primary-900`: #4536B6

### Neutral Colors
- `--neutral-50`: #F5F6FA
- `--neutral-100`: #F3F4F6
- `--neutral-200`: #EBECEF
- `--neutral-300`: #D1D5DB
- `--neutral-400`: #9CA3AF
- `--neutral-500`: #6B7280
- `--neutral-600`: #4B5563
- `--neutral-700`: #374151
- `--neutral-800`: #1F2937
- `--neutral-900`: #111827

### Semantic Colors
- **Success**: `--success-500`: #22C55E
- **Danger**: `--danger-500`: #EF4444
- **Warning**: `--warning-500`: #EAB308
- **Info**: `--info-500`: #3B82F6
- **Cyan**: `--cyan-500`: #2bc9f9

### Theme Variables
- `--base`: #fff (White)
- `--brand`: var(--primary-600)
- `--black`: var(--dark-2)
- `--white`: var(--base)
- `--bg-color`: var(--neutral-50)
- `--text-primary-light`: var(--neutral-900)
- `--text-secondary-light`: var(--neutral-600)
- `--input-bg`: var(--neutral-50)
- `--input-stroke`: var(--neutral-300)

## Typography

Font sizes defined in the CSS root:

### Heading Sizes
- `--h1`: clamp(2rem, 1.2rem + 4vw, 4.5rem)
- `--h2`: clamp(1.75rem, 1.11rem + 3.2vw, 3.75rem)
- `--h3`: clamp(1.5rem, 1.02rem + 2.4vw, 3rem)
- `--h4`: clamp(1.375rem, 1.095rem + 1.4vw, 2.25rem)
- `--h5`: clamp(1.25rem, 1.05rem + 1vw, 1.875rem)
- `--h6`: clamp(1.125rem, 1.005rem + 0.6vw, 1.5rem)

### Text Sizes
- `--font-2xxl`: 2rem
- `--font-2xl`: 1.75rem
- `--font-xxl`: 1.5rem
- `--font-xl`: 1.25rem
- `--font-lg`: 1.125rem
- `--font-md`: 1rem
- `--font-sm`: 0.875rem
- `--font-xs`: 0.75rem
- `--font-xxs`: 0.625rem

Font family: `Inter` (from Google Fonts)

## Spacing & Sizing

### Size Scale
- `--size-2`: 0.125rem
- `--size-4`: 0.25rem
- `--size-6`: 0.375rem
- `--size-8`: 0.5rem
- `--size-9`: 0.5625rem
- `--size-10`: 0.625rem
- `--size-11`: 0.6875rem
- `--size-12`: 0.75rem
- `--size-13`: 0.8125rem
- `--size-16`: 1rem
- `--size-20`: 1.25rem
- `--size-24`: 1.5rem
- `--size-28`: 1.5rem
- `--size-32`: 2rem
- `--size-36`: 2rem
- `--size-40`: 2.5rem
- `--size-44`: 2.75rem
- `--size-48`: 3rem
- `--size-50`: 3.125rem
- `--size-56`: 3.5rem
- `--size-60`: 3.75rem
- `--size-64`: 4rem
- `--size-72`: 4.5rem
- `--size-76`: 4.75rem
- `--size-80`: 5rem
- `--size-90`: 5.625rem
- `--size-110`: 6.875rem
- `--size-120`: 7.5rem

### Border Radius
- `--rounded-2`: 0.125rem
- `--rounded-4`: 0.25rem
- `--rounded-6`: 0.375rem
- `--rounded-8`: 0.5rem
- `--rounded-9`: 0.5625rem
- `--rounded-10`: 0.625rem
- `--rounded-11`: 0.6875rem
- `--rounded-12`: 0.75rem
- `--rounded-13`: 0.8125rem
- `--rounded-16`: 1rem
- `--rounded-20`: 1.25rem
- `--rounded-24`: 1.5rem
- `--rounded-28`: 1.5rem
- `--rounded-32`: 2rem
- `--rounded-36`: 2rem
- `--rounded-40`: 2.5rem
- `--rounded-44`: 2.75rem
- `--rounded-48`: 3rem
- `--rounded-50`: 3.125rem
- `--rounded-56`: 3.5rem
- `--rounded-60`: 3.75rem
- `--rounded-64`: 4rem
- `--rounded-72`: 4.5rem
- `--rounded-76`: 4.75rem
- `--rounded-80`: 5rem
- `--rounded-90`: 5.625rem
- `--rounded-110`: 6.875rem
- `--rounded-120`: 7.5rem

### Shadows
- `--shadow-1`: 0 4px 60px 0 rgba(4, 6, 15, 0.8)
- `--shadow-2`: 0 4px 60px 0 rgba(4, 6, 15, 0.5)
- `--shadow-3`: 0 20px 100px 0 rgba(4, 6, 15, 0.8)
- `--shadow-4`: 4px 8px 24px 0 rgba(182, 182, 182, 0.2)
- `--shadow-5`: 4px 12px 32px 0 rgba(0, 169, 158, 0.1)
- `--shadow-6`: 4px 16px 32px 0 rgba(0, 169, 158, 0.1)

## Component Library

The `_theme/resources/views/componentspage` directory contains individual component showcases that can be used as reference implementations:

### Alerts (`alert.blade.php`)
- Various alert styles (success, warning, info, danger)
- Dismissible alerts
- Alerts with icons

### Avatars (`avatar.blade.php`)
- Circular and square avatars
- Avatar groups and stacks
- Avatar with status indicators
- Image and initial-based avatars

### Badges (`badges.blade.php`)
- Various badge styles (primary, secondary, success, danger, warning, info)
- Pill badges
- Outline badges
- Badge with dot indicators

### Buttons (`button.blade.php`)
- Multiple button variants (primary, secondary, success, danger, warning, info, light, dark)
- Button sizes (small, default, large)
- Button with icons
- Outline buttons
- Link buttons
- Loading state buttons
- Button groups

### Cards (`card.blade.php`)
- Basic cards
- Card with header/footer
- Card with image overlays
- Social cards
- Profile cards
- Stats cards
- Card decks and columns

### Carousel (`carousel.blade.php`)
- Basic carousel/slider
- Carousel with captions
- Carousel with thumbnails
- Autoplay carousel
- Vertical carousel

### Dropdowns (`dropdown.blade.php`)
- Basic dropdowns
- Dropdown with divider
- Dropdown with header
- Dropdown with form elements
- Dropup and dropleft/dropright variations

### Forms (`forms` directory in views)
- Form layouts
- Input groups
- Form validation styles
- Custom checkboxes and radios
- File uploads
- Date/time pickers
- Select menus
- Floating labels

### Modals
- Standard modals
- Fullscreen modals
- Scrollable modals
- Modal sizing (small, large, extra-large)
- Modal with form elements

### Navbars (`navbar.blade.php`)
- Responsive navbar
- Navbar with brand/logo
- Navbar with search form
- Navbar with dropdown menus
- Fixed/sticky navbars
- Transparent navbar
- Navbar with offcanvas sidebar

### Sidebar (`sidebar.blade.php`)
- Collapsible sidebar
- Sidebar with branding
- Sidebar navigation menus
- Sidebar with user profile
- Compact sidebar mode
- Sidebar with scrollable content

### Breadcrumb (`breadcrumb.blade.php`)
- Basic breadcrumb navigation
- Breadcrumb with icons
- Breadcrumb separators
- Responsive breadcrumb

### Pagination (`pagination.blade.php`)
- Basic pagination
- Pagination with icons
- Pagination with disabled/active states
- Circle and rounded pagination styles
- Size variations

### Progress Bars (`progress.blade.php`)
- Basic progress bars
- Striped progress bars
- Animated progress bars
- Vertical progress bars
- Progress bars with labels
- Different color variants

### Tables
- Basic tables
- Striped tables
- Bordered tables
- Hoverable tables
- Responsive tables
- Tables with pagination
- DataTables integration

### Tabs (`tabs.blade.php`)
- Horizontal tabs
- Vertical tabs
- Pills-style tabs
- Tab with dropdown menus
- Fade effect tabs
- Scrollable tabs

### Tooltips & Popovers (`tooltip.blade.php`)
- Tooltips on all positions
- Tooltips with HTML content
- Tooltips with animations
- Popovers with titles
- Popovers with forms

### Widgets (`widgets.blade.php` in views)
- Dashboard widgets
- Stats cards with charts
- Mini charts (sparkline, bar, pie)
- Widget with tabs
- Widget with accordion

### Charts
- ApexCharts integration
- Line charts
- Bar charts
- Pie charts
- Donut charts
- Radar charts
- Polar area charts
- Heatmap charts
- Candlestick charts
- Bubble charts
- Scatter plots

### Calendar & Date Pickers
- FullCalendar integration
- Flatpickr date/time picker
- Date range picker
- Inline calendars
- Calendar with events

### File Upload
- Basic file upload
- Drag & drop upload
- Multiple file upload
- File upload with validation
- Image preview upload
- File upload with progress bar

### Icons
- Remix Icon integration
- Icon sizes and colors
- Icon buttons
- Icon lists
- Animated icons

## Layout System

### Containers
- Fixed width containers
- Fluid containers
- Responsive containers

### Grid System
- Bootstrap 5 grid (12-column)
- Responsive breakpoints:
  - xs: <576px
  - sm: ≥576px
  - md: ≥768px
  - lg: ≥992px
  - xl: ≥1200px
  - xxl: ≥1400px

### Utilities
- Display utilities (d-none, d-block, d-flex, etc.)
- Flex utilities
- Position utilities
- Sizing utilities (width, height)
- Spacing utilities (margin, padding)
- Text utilities (alignment, transform, weight, decoration)
- Visibility utilities
- Z-index utilities

## JavaScript Components

Based on `_theme/resources/js/app.js` and `_theme/resources/js/bootstrap.js`:

### Initialized Components
- Tooltips
- Popovers
- Scrollspy
- Toast notifications
- Dropdown hover effects
- Perfect scrollbar
- Sidetab navigation
- Flatpickr date/time pickers
- Choices.js enhanced selects
- ApexCharts initialization
- FullCalendar integration
- Vector maps
- DataTables
- Swiper sliders
- Lightbox/magnific popup
- Clipboard.js
- CountUp.js
- Jarallax parallax
- Splitting.js text effects
- Gutenberg post editor
- Code editors (Ace, CodeMirror)
- SVG injectors
- Animations (AOS, GSAP)

## Implementation Guidelines

### 1. File Structure
- Place Blade templates in `resources/views/`
- Store CSS in `public/css/` (compile from `_theme/resources/css/` if modifying)
- Store JavaScript in `public/js/` (compile from `_theme/resources/js/` if modifying)
- Images and assets in `public/assets/`

### 2. Component Usage
- Use Blade components or includes for reusable UI elements
- Follow the naming convention: `resources/views/components/[component-name].blade.php`
- For complex components, use view composers or view creators

### 3. Styling Approach
- Utilize CSS variables from the theme for consistent theming
- Extend or override styles in `public/css/custom.css`
- Avoid inline styles; use utility classes when possible
- Maintain responsive design principles

### 4. JavaScript Integration
- Initialize components in `resources/views/components/script.blade.php`
- Use Alpine.js for reactive components where appropriate
- Initialize third-party libraries in dedicated init functions
- Ensure proper cleanup of event listeners

### 5. Accessibility
- Follow WCAG 2.1 guidelines
- Use semantic HTML elements
- Ensure proper ARIA labels and roles
- Maintain sufficient color contrast
- Support keyboard navigation
- Provide focus indicators

### 6. Dark/Light Mode Support
- The theme supports theme switching via `data-theme` attribute
- CSS variables adapt based on theme
- Use `var(--bg-color)`, `var(--text-primary-light)`, etc. for theme-aware colors

## Integration with Laravel Application

### 1. Views
Extend the base layout:
```blade
@extends('layout.layout')

@php
    $title = 'Page Title';
    $subTitle = 'Page Subtitle';
@endphp

@section('content')
    <!-- Page content here -->
@endsection
```

### 2. Reusing Theme Components
Include theme components:
```blade
@include('components.head')
@include('components.footer')
@include('components.sidebar')
@include('components.navbar')
```

### 3. Using Components from Theme
Reference component showcases for implementation patterns:
- Buttons: `_theme/resources/views/componentspage/button.blade.php`
- Cards: `_theme/resources/views/componentspage/card.blade.php`
- Forms: Check `_theme/resources/views/forms/` directory
- Modals: Refer to event-modal, delegation-modal implementations

### 4. Asset Compilation
If modifying source assets:
```bash
# Compile CSS
npm run dev  # or npm run prod for production

# Or directly use compiled assets from _theme/public/
```

## Customization Guidelines

### 1. Extending the Theme
- Create custom CSS variables in `:root` for brand-specific colors
- Add new utility classes as needed
- Extend existing component styles with BEM naming convention

### 2. Overriding Components
- Override views by creating files with same path in `resources/views/`
- Use view composers to modify shared data
- Extend Blade layouts rather than duplicating when possible

### 3. Adding New Components
- Follow existing patterns in `_theme/resources/views/componentspage/`
- Create corresponding Blade component in `resources/views/components/`
- Add initialization script if needed in `resources/views/components/script.blade.php`
- Document usage in this design language guide

## Maintenance

### 1. Updates
- When updating from upstream theme, merge changes carefully
- Keep customizations in separate files to avoid conflicts
- Update documentation when components are modified

### 2. Version Tracking
- Track theme version in documentation
- Note any deviations from upstream theme
- Maintain changelog for design system updates

## Reference Implementation

The views created in `resources/views/command/` demonstrate proper implementation of this design language:
- `dashboard-overview.blade.php` - Uses cards, alerts, and stats
- `daily-agenda.blade.php` - Uses timeline and event components
- `executive-delegations.blade.php` - Uses kanban board and cards
- `protocol-majlis-calendar.blade.php` - Uses FullCalendar integration

These views properly extend the layout, use theme-consistent styling, and incorporate reusable components from the theme system.