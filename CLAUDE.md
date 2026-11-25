# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Craft CMS plugin for optimizing and rendering responsive images. The plugin provides a Twig API (`craft.images.render()` and `craft.images.url()`) that automatically generates optimized `<picture>` elements with WebP support, responsive transforms, and fallback images.

**Tech Stack:** PHP 8.2+, Craft CMS 4.x/5.x, Twig templates

## Development Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Run Rector (code modernization for Craft 5)
vendor/bin/rector process

# Run the template tests (img.twig rendered with plain Twig and stand-in assets)
composer test
```

Note: This plugin has no build step or linter configured.

## Architecture

### Event-Driven Plugin System

The plugin follows Craft CMS's event-driven architecture:

1. **Bootstrap** (`src/CraftCmsImages.php`): Registers event listeners during `init()`
   - `View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS` - Makes Twig templates available
   - `CraftVariable::EVENT_INIT` - Injects `craft.images` variable into templates

2. **Service Layer** (`src/variables/ImageVariable.php`): Static helper methods
   - `render()` - Main rendering function, prepares parameters and delegates to Twig
   - `url()` - Returns transformed image URL without HTML markup
   - Transform resolution logic handles mobile/desktop variants

3. **Template Layer** (`src/available-templates/`):
   - `img.twig` - Main rendering logic with conditional paths for SVG, WebP, transforms
   - `macros.twig` - Reusable HTML generation macros

### Key Design Patterns

- **Mobile Transform Auto-naming**: If `mobileTransform` is not specified, the plugin automatically looks for a transform handle with "Mobile" appended (e.g., `heroTransform` → `heroTransformMobile`)

- **WebP with Fallback**: When transforms are applied and WebP is supported, the plugin generates both WebP `<source>` tags and PNG fallback sources within a `<picture>` element

- **Lazy Loading by Default**: All images have `loading="lazy"` unless explicitly disabled via `lazyLoading: false`

- **SVG Special Handling**: SVGs bypass transform logic entirely since they cannot be transformed

## Twig API Reference

The plugin exposes two main methods via `craft.images`:

```twig
{# Full rendering with all options #}
{{ craft.images.render(imageField, {
    transform: 'handleName',              # Transform handle or object
    mobileTransform: 'mobileHandleName',  # Optional, defaults to handle+'Mobile'
    fallback: '/path/to/fallback.png',    # Static fallback image
    class: 'css-classes',                 # CSS classes for <img>
    style: 'inline-styles',               # Inline styles
    alt: 'alt text',                      # Alt text (defaults to field's alt)
    attributes: { 'data-attr': 'value' }, # Custom HTML attributes
    lazyLoading: true,                    # Enable/disable lazy loading
    dimensions: true,                     # Include width/height attributes
    'picture-class': 'css-classes'        # CSS classes for <picture>
}) }}

{# URL only (no HTML) #}
{{ craft.images.url(imageField, {
    transform: { width: 500, height: 300, mode: 'crop' }
}) }}
```

## Recent Changes

The plugin was recently updated for Craft 5 compatibility. Key recent changes include:
- Changed JPEG fallback to PNG for better quality
- Added dimension support
- Fixed partial transform handling
- Made mobileTransform optional (falls back to desktop transform)

## Testing Changes

Since there are no automated tests, manually verify changes by:
1. Installing the plugin in a Craft CMS project
2. Testing `craft.images.render()` with various image fields and transforms
3. Checking browser DevTools for correct `<picture>` and `<source>` elements
4. Verifying WebP is served when supported
5. Testing fallback images and missing field scenarios
