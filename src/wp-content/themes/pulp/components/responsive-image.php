<?php
// Set default values
if(!$desktopWidth) $desktopWidth = 'auto';
if(!$desktopHeight) $desktopHeight = 'auto';
if(!$mobileWidth) $mobileWidth = 'auto';
if(!$mobileHeight) $mobileHeight = 'auto';

$glide = new \Pulp\Glide();

// Process desktop image
$params = [
    'w' => $desktopWidth,
    'fit' => 'crop',
];

if($desktopHeight !== 'auto') {
    $params['h'] = $desktopHeight;
} else {
    $ratio = $image['height'] / $image['width'];
    $desktopHeight = floor($desktopWidth * $ratio);
}

$imageUrl = $glide->imageUrl($image['url'], $params);

// Process mobile image
$params = [
    'w' => $mobileWidth,
    'fit' => 'crop',
];

if($mobileHeight) {
    $params['h'] = $mobileHeight;
}

// Check if $imageMobile is an array before using it
if(is_array($imageMobile) && !empty($imageMobile['url'])) {
    $mobileImageUrl = $glide->imageUrl($imageMobile['url'], $params);
} else {
    $mobileImageUrl = $imageUrl;
}

// Set default class if empty
if(empty($class)) $class = 'img-fluid';

// Get alt text safely
$alt = isset($image['alt']) ? esc_attr($image['alt']) : '';
?>

<picture>
    <source media="(max-width: <?= esc_attr($breakpoint); ?>px)" srcset="<?= esc_url($mobileImageUrl); ?>">
    <img src="<?= esc_url($imageUrl); ?>"
         alt="<?= esc_attr($alt); ?>"
         class="<?= esc_attr($class); ?>"
         height="<?= esc_attr($desktopHeight); ?>"
         width="<?= esc_attr($desktopWidth); ?>"
         decoding="async"
         loading="<?= $lazyLoad ? 'lazy' : 'eager'; ?>"
    >
</picture>