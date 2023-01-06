![Preview of Kirby blurry placeholder plugin](./.github/kirby-blurry-placeholder-plugin.png)

# Kirby Blurry Placeholder

This plugin implements progressive image loading, providing a better user experience. Tiny thumbnails which inherit the aspect ratio of their source image are combined with a blurry effect for a better placeholder than solid colors, without sacrificing payload.

How it works:

1. An inline, URI-encoded SVG fills the `src` attribute of a given image element. The blurred image is wrapped in a SVG to avoid rasterizing the filter.
2. The large images are then only requests when they are within the viewport.

## Key Features

- ✨ Avoids content jumping (keeping aspect ratio)
- 🥨 Respects [custom image formats](https://getkirby.com/docs/guide/templates/resize-images-on-the-fly#image-formats), like WebP and AVIF
- 🏗 Use as:
  - [Image block](#as-kirby-image-block)
  - [KirbyTag](#as-kirbytag)
  - [File method](#as-file-method)
- [🦌 Loadeer.js](https://github.com/johannschopplich/loadeer) for lazy loading in the frontend
- 🔍 SEO-friendly

## Requirements

- Kirby 3.8+

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-blurry-placeholder`.

### Git Submodule

```bash
git submodule add https://github.com/johannschopplich/kirby-blurry-placeholder.git site/plugins/kirby-blurry-placeholder
```

### Composer

```bash
composer require johannschopplich/kirby-blurry-placeholder
```

## Usage

### As Kirby Image Block

Each Kirby website is tailored to its own unique use case. Thus, this plugin won't add a Kirby block by default. Instead, take a look into the provided [image block example](./snippets/blocks/image.php) to get an idea of how to implement blurry placeholders within blocks.

Of course, you can just copy the block into your `site/snippets/blocks` folder of your current Kirby project, use it as is or adapt it to your needs!

### As File Method

`$file->placeholderUri()` creates and returns the URI-encoded SVG placeholder.

```php
<!-- Using the `placeholderUri` file method for the `src` attribute -->
<img
  src="<?= $image->placeholderUri() ?>"
  data-src="<?= $image->url() ?>"
  data-lazyload
  alt="<?= $image->alt() ?>"
/>
```

### As KirbyTag

This plugin provides a `(blurryimage: …)` KirbyTag built upon Kirby's core `(image: …)` tag. All of the Kirby's image tag options are inferred and thus available for the custom tag as well.

The `(blurryimage: …)` tag:

- Encodes a blurry image placeholder as URI in the `src` attribute.
- Sets the original image's URL as `data-src` or a set of responsive images as `data-srcset`.
- Adds a `data-lazyload` attribute for selection by the lazy loading library.

Example use within a [KirbyText](https://getkirby.com/docs/reference/text/kirbytags) field:

```
(blurryimage: myimage.jpg)
(blurryimage: myimage.jpg link: https://example.com)
(blurryimage: myimage.jpg class: is-poster)
```

If you have enabled `srcset`'s in the options, the KirbyTag syntax stays the same. Just the output changes.

## Lazy Loading in the Frontend

To lazily load the images once they get apparent in the viewport, a JavaScript library is necessary. [🦌 Loadeer.js](https://github.com/johannschopplich/loadeer) is written with this Kirby plugin in mind. In a nutshell, it's a tiny, performant, SEO-friendly lazy loading library and can be used with or without a build step if you don't have a frontend asset build chain.

### Without Build Step & Auto Initialization

Simply load it from a CDN:

```html
<script
  src="https://unpkg.com/loadeer@2.1.1/dist/loadeer.umd.js"
  defer
  init
></script>
```

- The `defer` attribute makes the script execute after HTML content is parsed.
- The `init` attribute tells Loadeer.js to automatically initialize and watch all elements that have a `data-lazyload` attribute.

### Import As ES Module

You can use the ES module build by installing the `loadeer` npm package:

```js
import Loadeer from "loadeer";

const instance = new Loadeer();
instance.observe();
```

### Automatically Calculate the `sizes` Attribute

[Loadeer.js](https://github.com/johannschopplich/loadeer) supports setting the `sizes` attribute automatically, corresponding to the current size of your image. For this to work, the `data-sizes` attribute has to be set to `auto`. If you have `srcset`'s enabled in your configuration, this is already done for you when using the `(blurryimage: …)` KirbyTag.

### Use a Lazy Loader of Your Choice

Each parsed KirbyTag adds the `data-lazyload` attribute to the `img` element. Consequently, you can let a lazy loader of choice select these elements by passing `[data-lazyload]` as selector.

### Animating the Blur

<details>
<summary>🎨 Animating with the "Blur Down" Technique</summary>

> ⚠️ **Disclaimer**: Please avoid copying any code until reading this section. This is an experimental technique that comes with caveats (mostly performance issues).

When using Loadeer.js, we can target all lazy-loaded images with `[data-lazyload]` and refine this selection with `[data-src]` only to target the images that haven't been fully loaded yet.

```css
img[data-lazyload][data-src] {
  filter: blur(150px);
  transform: scale(1.2);
}
```

We can then apply a transition to such properties.

```css
/* Respect users choice for reduced motion */
@media (prefers-reduced-motion: no-preference) {
  img[data-lazyload] {
    transition: 1000ms cubic-bezier(0.86, 0.07, 0.07, 0.96);
    transition-property: filter, transform;
    /* Hint browser at change for better performance */
    will-change: filter, transform;
  }
}
```

As we are manually blurring the `img` **element**, which `src` attribute is our generated SVG (so essentially the SVG is a child of our `img` element), we _need_ to have a container that hides overflowing parts. For better performance we also enforce GPU rendering by applying a `transform` declaration.

```css
.img-container {
  overflow: hidden;
  /* Enforce GPU rendering */
  transform: translateZ(0);
}
```

The biggest caveat with this implementation is the transition of `filter` - users on lower-end devices will probably experience frame drops. Even using `transform` to enforce GPU rendering and using `will-change` as last resort of hinting the at the transition does not always fix such issues. In the end, it heavily depends on what devices your end users are on, how big the painted image is on the site and how many images your site features (that might all blur up at the same time).

So should you implement a "blur down" transition? **Probably not.** If you do, strongly consider the caveats. The best animation is the one that engages users, not the one that scares them.

</details>

## Options

### File Methods

Both `$file->placeholder()` and `$file->placeholderUri()` file methods support the following options passed as an associative array:

| Option        | Type   | Default | Description |                                                                                                                                                                                                                                                  |
| ------------- | ------ | ------- | ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `ratio`       | `float | null`   | `null`      | The ratio of the image. If not set the original image's ratio will be used.                                                                                                                                                                      |
| `transparent` | `bool  | null`   | `null`      | Mark the image as transparent. If `true`, the generated blur will be cropped at the edges. If `false`, the generated blur will be extended at the edges. By default, the plugin will try to evaluate if the the image contains an alpha channel. |

Example:

```php
$file->placeholderUri([
  'ratio' => 1.5,
  'transparent' => true
]);
```

#### Transparent Images

If you know that your image (e.g. a logo) is transparent, you can pass the `transparent` option to the `placeholderUri()` to [circumvent artifacts at the edges of the generated blur](https://github.com/johannschopplich/kirby-blurry-placeholder/issues/5):

```php
<img src="<?= $image->placeholderUri(['transparent' => true]) ?>" />
```

When not explictly specified, transparency is detected by evaluating the alpha channel of each pixel in the thumbnail image (by the given pixel target). If the image doesn't contain an alpha channel itself, an additional filter is added to the SVG placeholder to remove the alpha channel of the generated blur at the edges.

#### Image With Ratio

Pass the `ratio` option to the `placeholderUri()` method on the original image to generate a placeholder image with a specific ratio:

```php
<?php $cropped = $original->crop(500, 400) ?>
<img
  src="<?= $original->placeholderUri(['ratio' => 5/4]) ?>"
  data-src="<?= $cropped->url() ?>"
  data-lazyload
  alt="<?= $original->alt() ?>"
/>
```

> ℹ️ Kirby doesn't support file methods on cropped images, because the latter inherits the `Kirby\Cms\FileVersion` class.

### Global

| Option                                                       | Default | Description                                                                                                                                                                                                                    |
| ------------------------------------------------------------ | ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `pixel-target`           | `60`    | Aim for a placeholder image of ~P pixels (`w \* h = ~P`).                                                                                                                                                                      |
| `kirbytag.srcset-preset` | `null`  | A preset passed to [Kirby's `srcset` method](https://getkirby.com/docs/reference/objects/cms/file/srcset#define-presets) when using the KirbyTag. If `null` (default), a `src` attribute will be rendered instead of `srcset`. |
| `kirbytag.sizes`         | `auto`  | Default for the `data-sizes` attribute if the KirbyTag works with `srcset`'s.                                                                                                                                                  |

> All of the `srcset` options have to be wrapped in an array.

Options can be set in your `config.php` file:

```php
return [
    'johannschopplich.blurry-placeholder' => [
        'pixel-target' => 60,
        'kirbytag' => [
            'srcset-preset' => 'article'
        ]
    ]
];
```

## Placeholders in action

> Note: GIF is slowed down so to grasp visually how placeholders look before the image kicks in.

![GIF showing plugin in action](./.github/kirby-blurry-placeholder-preview.gif)

## Credits

- AMP's [blurry image implementation](https://github.com/ampproject/amp-toolbox/blob/0c8755016ae825b11b63b98be83271fd14cc0486/packages/optimizer/lib/transformers/AddBlurryImagePlaceholders.js)
- [Tobias Möritz](https://github.com/tobimori) for his [transparency detection](https://github.com/johannschopplich/kirby-blurry-placeholder/pull/22)

## License

[MIT](./LICENSE) License © 2020-2023 [Johann Schopplich](https://github.com/johannschopplich)
