# Sass (.scss)

The project uses Sass as its CSS preprocessor. Main entry points for styles are located in `src/styles/`.

-   `src/styles/admin.scss`: Styles for the WordPress admin area.
-   `src/styles/main.scss`: Styles for the public-facing front end.

Project-specific style variables (colors, fonts, breakpoints, etc.) are defined in `src/styles/abstracts/_variables.scss`.

### Path Resolution in Sass

There are two types of path resolution to be aware of when working with Sass files.

**1. Loading Sass Partials (`@use`)**

The Sass loader is configured to recognize the `src/` directory as a root import path. This means you can load any `.scss` file from within `src/` using the `@use` rule without needing to write long relative paths like `../../../`.

For example, to use a mixin from `src/styles/abstracts/mixins.scss`, you would write:
```scss
// This resolves to src/styles/abstracts/mixins.scss
@use 'styles/abstracts/mixins' as *;
```
Note: This mechanism does **not** use the `@` alias.

**2. Referencing Assets (`url()`)**

For referencing assets like images, icons, or fonts, you use the standard CSS `url()` function. These paths are processed by webpack, which **does** understand the `@` alias. The `@` alias is a shortcut for the `/src` directory.

This is the recommended way to include static assets in your CSS.

**Example: Referencing an image from the `src` directory**
```scss
.header {
  // This resolves to src/images/header-background.png
  background-image: url('@/images/header-background.png');
}

.icon {
  // This resolves to src/icons/my-icon.svg
  background-image: url('@/icons/my-icon.svg');
}
```

## Mixins

Mixins are located in `src/styles/abstracts/mixins.scss`.

### 1. font-size
Converts pixel-based font properties into relative CSS units.
- `font-size` and `letter-spacing` are converted to `rem`.
- `line-height` is converted to a unitless value (equivalent to %).
Units are calculated based on the `$base-font-size` (default is 16px) set in `variables.scss`.

**Usage:**
```scss
@include font-size($font-size, $line-height: false, $letter-spacing: false);
```

**Example:**
```scss
p {
  @include font-size(24, 28, 100);
}
```

**Output:**
```css
p {
  font-size: 1.5rem;
  line-height: 1.1666666667;
  letter-spacing: 0.1rem;
}
```

### 2. font-family
Applies a font-family from the predefined map in `variables.scss`. Default `font-weight` is `400` and `font-style` is `normal`.

**Usage:**
```scss
@include font-family($name, $weight: 400, $style: 'normal');
```

**Example:**
```scss
p {
  @include font-family('helvetica', 700, 'italic');
}
```

**Output:**
```css
p {
  font-family: Helvetica, sans-serif;
  font-weight: 700;
  font-style: italic;
}
```

### 3. input-placeholder
Styles the placeholder for `<input>` elements and handles vendor prefixing.

**Usage:**
```scss
input {
  @include input-placeholder {
    font-weight: 700;
    color: #666;
  }
}
```

**Output:**
```css
input::placeholder {
  font-weight: 700;
  color: #666;
}
```

### 4. link-colors
Sets link pseudo-classes.

**Usage:**
```scss
@include link-colors($normal, $hover: false);
```

**Example 1 (one parameter):**
```scss
a {
  @include link-colors(#f00);
}
```

**Output 1:**
```css
a:link,
a:visited,
a:active,
a:hover,
a:focus {
  color: #f00;
}
```

**Example 2 (both parameters):**
```scss
a {
  @include link-colors(#f00, #0f0);
}
```

**Output 2:**
```css
a:link,
a:visited,
a:active,
a:focus {
  color: #f00;
}

a:hover {
  color: #0f0;
}
```

### 5. ie10-11
Targets IE10 & IE11 with a media query.

**Usage:**
```scss
@include ie10-11 {
  // styles go here
}
```

**Example:**
```scss
@include ie10-11 {
  .my-rule {
    display: none;
  }
}
```

**Output:**
```css
@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
  .my-rule {
    display: none;
  }
}
```

## Functions

Functions are located in `src/styles/abstracts/functions.scss`.

### 1. get-color
Retrieves a color value from the `$colors` map defined in `variables.scss`.

**Usage:**
```scss
.element {
  color: get-color($color-name);
}
```

**Example:**
```scss
.element {
  color: get-color('brand');
  background-color: get-color('black');
}
```

**Output:**
```css
.element {
  color: #0c5a9a;
  background-color: #000;
}
```

### 2. span
Calculates an element's width as a percentage based on a grid system. The column count is set by `$grid-columns` (default is 12) in `variables.scss`.

**Usage:**
```scss
.element {
  width: span($cols);
}
```

**Example:**
```scss
.el-1 { width: span(3); }
.el-2 { width: span(6); }
```

**Output:**
```css
.el-1 {
  width: 25%;
}

.el-2 {
  width: 50%;
}
```

### 3. gutter
Calculates a gutter width based on the `$grid-gutter-width` defined in `variables.scss`. Can be used to create spacing in a flex-based grid. An optional `$half` argument returns half the gutter width.

**Usage:**
```scss
.element {
  padding-left: gutter($half: false);
}
```

**Example (Gutter value is 20px):**
```scss
.row {
  display: flex;
  margin-left: -gutter(true); // -10px
  margin-right: -gutter(true); // -10px
}

.col {
  width: span(6); // 50%
  padding-left: gutter(true); // 10px
  padding-right: gutter(true); // 10px
}
```