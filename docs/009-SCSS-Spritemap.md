# SVG Sprites

The boilerplate uses the `svg-spritemap-webpack-plugin` to automatically combine all SVG icons from the `src/icons/` directory into a single SVG spritemap file.

This process runs automatically during any build (`npm run dev` or `npm run build`) if SVG files are present in the `src/icons/` directory.

The final spritemap is generated at `dist/images/spritemap.[contenthash].svg`. The `[contenthash]` part is a unique hash that changes only when the icons themselves change, allowing for long-term browser caching.

## Styling Icons

To allow CSS to control the icon's color, you must **remove any `fill` attributes** from the `<path>` or shape elements within your source `.svg` files.

The boilerplate includes a base style in `src/styles/core/_icons.scss` that allows the icons to inherit their color from the parent text color:
```scss
.svg-icon svg {
    fill: currentColor;
}
```

The size of the icon can be controlled by setting the `font-size` on the `.svg-icon` wrapper, or by setting `width` and `height` on the `<svg>` element itself.

## Usage

To display an icon, you use the `<svg>` and `<use>` elements, referencing the icon's ID from the generated spritemap.

The ID is constructed based on two configuration settings:
- **Sprite Prefix**: The `prefix` is set to `icon-`.
- **Icon Filename**: The name of your `.svg` file (e.g., `twitter.svg`).

This means an icon saved as `twitter.svg` will have an ID of `icon-twitter`.

### PHP Example

To correctly reference the spritemap file with its unique hash, you should use the `asset-manifest.json` file that webpack generates in the `dist/` directory during production builds.

Here is an example of a PHP helper function you could use to display an icon:

```php
<?php
/**
 * Displays an SVG icon from the spritemap.
 *
 * @param string $icon_name The name of the icon (e.g., 'twitter').
 */
function display_svg_icon( string $icon_name ): void {
    // Get the path to the manifest file.
    $manifest_path = plugin_dir_path( __FILE__ ) . '../dist/asset-manifest.json';

    if ( ! file_exists( $manifest_path ) ) {
        return;
    }

    // Decode the manifest file.
    $manifest = json_decode( file_get_contents( $manifest_path ), true );
    $spritemap_file = $manifest['images/spritemap.svg'] ?? '';

    if ( ! $spritemap_file ) {
        return;
    }

    // Construct the full URL to the icon in the spritemap.
    $icon_url = plugins_url( '../dist/' . $spritemap_file, __FILE__ ) . '#icon-' . $icon_name;

    printf(
        '<span class="svg-icon svg-icon--%1$s"><svg><use xlink:href="%2$s"></use></svg></span>',
        esc_attr( $icon_name ),
        esc_url( $icon_url )
    );
}
```

You would then call this function in your templates:
```php
<?php display_svg_icon('twitter'); ?>
<?php display_svg_icon('facebook'); ?>
```