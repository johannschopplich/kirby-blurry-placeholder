<?php

use Kirby\Toolkit\Html;

/** @var \Kirby\Cms\Block $block */
$alt     = $block->alt();
$caption = $block->caption();
$link    = $block->link();
$ratio   = $block->ratio()->or('auto');
$props   = "figure-blurry-image {$ratio}";
$img     = null;

if ($block->location() === 'web') {
  $img = Html::img($block->src(), ['alt' => $alt]);
} elseif ($image = $block->image()->toFile()) {
  if ($alt->isEmpty()) {
    $alt = $image->alt();
  }

  if ($caption->isEmpty()) {
    $caption = $image->caption();
  }

  $img = Html::img(
    $image->placeholderUri(),
    [
      'alt' => $alt,
      'data-srcset' => $image->srcset(),
      'data-sizes' => 'auto',
      'data-lazyload' => 'true',
      'width' => $image->width(),
      'height' => $image->height()
    ]
  );
} else {
  return;
}

?>
<figure<?= attr(['class' => $props], ' ') ?>>
  <?php if ($link->isNotEmpty()): ?>
    <a href="<?= $link->toUrl() ?>">
      <?= $img ?>
    </a>
  <?php else: ?>
    <?= $img ?>
  <?php endif ?>

  <?php if ($caption->isNotEmpty()): ?>
    <figcaption>
      <?= $caption ?>
    </figcaption>
  <?php endif ?>
</figure>
