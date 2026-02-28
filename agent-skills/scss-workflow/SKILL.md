---
name: scss-workflow
description: SCSS structure, variables, mixins, font loading, and SVG sprites. Use when styling admin or frontend, adding custom fonts, or using SVG icons.
---

# SCSS Workflow

SCSS structure and build workflow.

## Directory Structure

```
src/styles/
├── abstracts/
│   ├── _variables.scss    # Colors, sizes, breakpoints
│   ├── _mixins.scss        # Reusable mixins
│   └── _functions.scss     # Custom functions
├── core/
│   ├── _reset.scss         # CSS reset
│   ├── _typography.scss    # Font styles
│   └── _utilities.scss     # Utility classes
├── vendor/
│   └── _normalize.scss     # Third-party CSS
├── admin.scss              # Admin entry point
└── main.scss               # Frontend entry point
```

## Entry Points

### Admin Styles

**File**: `src/styles/admin.scss`

```scss
@import 'abstracts/variables';
@import 'abstracts/mixins';
@import 'core/reset';
@import 'core/typography';

.wppb-admin {
	&__header {
		padding: 20px;
		background: $color-primary;
	}

	&__content {
		margin: 20px 0;
	}
}
```

### Frontend Styles

**File**: `src/styles/main.scss`

```scss
@import 'abstracts/variables';
@import 'abstracts/mixins';
@import 'core/reset';
@import 'core/typography';

.wppb {
	&__container {
		max-width: 1200px;
		margin: 0 auto;
	}

	&__button {
		@include button-primary;
	}
}
```

## Variables

**File**: `src/styles/abstracts/_variables.scss`

```scss
// Colors
$color-primary: #0073aa;
$color-secondary: #23282d;
$color-success: #46b450;
$color-error: #dc3232;
$color-warning: #ffb900;

// Typography
$font-family-base: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
$font-size-base: 16px;
$line-height-base: 1.5;

// Spacing
$spacing-xs: 4px;
$spacing-sm: 8px;
$spacing-md: 16px;
$spacing-lg: 24px;
$spacing-xl: 32px;

// Breakpoints
$breakpoint-sm: 576px;
$breakpoint-md: 768px;
$breakpoint-lg: 992px;
$breakpoint-xl: 1200px;

// Z-index
$z-index-dropdown: 1000;
$z-index-modal: 1050;
$z-index-tooltip: 1060;
```

## Mixins

**File**: `src/styles/abstracts/_mixins.scss`

```scss
// Responsive breakpoints
@mixin respond-to($breakpoint) {
	@media (min-width: $breakpoint) {
		@content;
	}
}

// Button styles
@mixin button-primary {
	padding: $spacing-sm $spacing-md;
	background: $color-primary;
	color: white;
	border: none;
	border-radius: 4px;
	cursor: pointer;

	&:hover {
		background: darken($color-primary, 10%);
	}
}

// Flexbox center
@mixin flex-center {
	display: flex;
	align-items: center;
	justify-content: center;
}

// Clearfix
@mixin clearfix {
	&::after {
		content: '';
		display: table;
		clear: both;
	}
}
```

## Custom Fonts

### Add Font Files

Place fonts in `src/fonts/`:

```
src/fonts/
├── roboto-regular.woff2
├── roboto-bold.woff2
└── roboto-italic.woff2
```

### Define Font Face

**File**: `src/styles/core/_typography.scss`

```scss
@font-face {
	font-family: 'Roboto';
	src: url('../fonts/roboto-regular.woff2') format('woff2');
	font-weight: 400;
	font-style: normal;
	font-display: swap;
}

@font-face {
	font-family: 'Roboto';
	src: url('../fonts/roboto-bold.woff2') format('woff2');
	font-weight: 700;
	font-style: normal;
	font-display: swap;
}

body {
	font-family: 'Roboto', sans-serif;
}
```

## SVG Sprites

### Add SVG Icons

Place SVG files in `src/icons/`:

```
src/icons/
├── check.svg
├── close.svg
└── arrow.svg
```

### Use in SCSS

```scss
.icon {
	width: 24px;
	height: 24px;
	background-image: url('../icons/spritemap.svg#sprite-check');
	background-size: contain;
}
```

### Use in HTML

```html
<svg class="icon">
	<use xlink:href="path/to/spritemap.svg#sprite-check"></use>
</svg>
```

## Build Commands

```bash
# Development build (unminified)
npm run build:dev

# Production build (minified)
npm run build

# Watch mode (auto-rebuild)
npm run dev

# Clean dist folder
npm run clean
```

## Path Resolution

Webpack resolves paths automatically:

```scss
// Relative to src/styles/
@import 'abstracts/variables';

// Relative to src/
background-image: url('../fonts/font.woff2');
background-image: url('../icons/icon.svg');
background-image: url('../images/image.png');
```

## Output

Built files go to `dist/`:

```
dist/
├── scripts/
│   ├── admin-common.js
│   └── main-common.js
└── styles/
    ├── admin-common.css
    └── main-common.css
```

## Nesting

```scss
.wppb {
	&__card {
		padding: $spacing-md;

		&--featured {
			border: 2px solid $color-primary;
		}

		&__title {
			font-size: 24px;
			margin-bottom: $spacing-sm;
		}

		&__content {
			color: $color-secondary;
		}
	}
}
```

Output:

```css
.wppb__card { padding: 16px; }
.wppb__card--featured { border: 2px solid #0073aa; }
.wppb__card__title { font-size: 24px; margin-bottom: 8px; }
.wppb__card__content { color: #23282d; }
```

## Rules

- Use SCSS, not plain CSS
- Import abstracts first (variables, mixins)
- Use variables for colors, spacing, breakpoints
- Create mixins for reusable patterns
- Use relative paths for assets
- Place fonts in `src/fonts/`
- Place icons in `src/icons/`
- Use `npm run dev` for development
- Use `npm run build` for production
- Never edit files in `dist/` directly

