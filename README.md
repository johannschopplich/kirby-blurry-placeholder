![Preview of Kirby blurry placeholder plugin](./.github/social-preview.png)

# Kirby Blurry Placeholder

> Blurry image placeholders with lazyloading for Kirby.

**Key Features:**

- 🖼 Available as Kirbytag
- 🗃 Extends as `Kirby\Cms\File` methods
- ⚡️ Performant, vanilla JavaScript lazy loader included
- 🔍 SEO-friendly with included lazy loader
- ☀️ Wraps the blurred image in a SVG to avoid rasterizing the filter

## Requirements

- Kirby 3
- PHP 7.4+

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-blurry-placeholder`.

### Git submodule

```
git submodule add https://github.com/johannschopplich/kirby-blurry-placeholder.git site/plugins/kirby-blurry-placeholder
```

### Composer

```
composer require johannschopplich/kirby-blurry-placeholder
```

## Usage

## As `(blurryimage: …)` Kirbytag

This plugin doesn't extend the core `(image: …)` Kirbytag, but builds upon it. So all of the options present are available in the additional Kirbytag as well.

The `(blurryimage: …)` tag:
- Encodes a blurry image placeholder as URI in the `src` attribute.
- Sets the original image's URL as `data-src`.
- Adds a `data-lazyload` attribute as generic selector.

Example use in a KirbyText field:
```
(blurryimage: myimage.jpg)
(blurryimage: myimage.jpg link: https://example.com)
(blurryimage: myimage.jpg class: is-poster)
```

### Usage as File Method

`$file->placeholderUri()` creates and returns the URI-encoded SVG placeholder.

```php
// Using the `placeholderUri` for an inlined image in the `src` attribute
<img src="<?= $image->placeholderUri() ?>" data-src="<?= $image->url() ?>" data-lazyload alt="<?= $image->alt() ?>">
```

## Frontend

You have two options to replace the `src` attribute's content with the one in either `data-src` or `data-srcset`:

### Use the Included Lazyload Hook

```js
import { useLazyload } from './src/useLazyload'

const observer = useLazyload()
observer.observe()
```

You may inspect the source to gain more information about options. In a nutshell, it's a SEO-friendly and modernized derivate of [https://github.com/ApoorvSaxena/lozad.js](lozad.js).

### Use a Lazy Loader of Your Choice

Each parsed Kirbytag adds the `data-lazyload` attribute to the `img` element.

Thus you can add the `[data-lazyload]` selector to you lazy loader.

> Note: A `.lazyload` class is intentionally not added to avoid naming conflicts. I also prefer data attributes over classes for selectors only used by JavaScript manipulation. 🤷‍♂️

## Options

| Option | Default | Description |
| --- | --- | --- |
| `kirby-extended.blurry-placeholder.pixel-target` | 60 | Aim for a placeholder image of ~P pixels (w * h = ~P).

## Placeholders in Action

> Note: Slowed down so that you can see better how placeholders look.

![GIF showing plugin in action](./.github/kirby-blurry-placeholder-preview.gif)

## TODO

- [ ] Add tests

## Credits

- AMP's [blurry image implementation](https://github.com/ampproject/amp-toolbox/blob/0c8755016ae825b11b63b98be83271fd14cc0486/packages/optimizer/lib/transformers/AddBlurryImagePlaceholders.js)
- [https://github.com/ApoorvSaxena/lozad.js](lozad.js)

## License

[MIT](https://opensource.org/licenses/MIT)
