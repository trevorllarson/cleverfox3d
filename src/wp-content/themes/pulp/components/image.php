<?php
$glide = new \Pulp\Glide();
$imageUrl = '';

// Process image with Glide or standard WordPress
if($isGlide) {
    $params = [
        'w' => $width,
        'fit' => 'crop',
    ];

    if($height) {
        $params['h'] = $height;
    } else {
        // get ratio from original image height / width
        $ratio = $mediaObj['height'] / $mediaObj['width'];
        $height = floor($width * $ratio);
        $params['h'] = $height;
    }

    if(strpos($mediaObj['url'], 'svg') !== false) {
        $imageUrl = $mediaObj['url'];
    } else {
        $imageUrl = $glide->imageUrl($mediaObj['url'], $params);
    }
} else {
    if(!empty($wpSize) && $wpSize !== 'original') {
        $imageUrl = $mediaObj['sizes'][$wpSize];
    } else {
        $imageUrl = $mediaObj['url'];
    }
}

// Set default class if empty
if(empty($class)) $class = 'img-fluid';

// Get alt text safely
$alt = isset($mediaObj['alt']) ? esc_attr($mediaObj['alt']) : '';
?>

<img src="<?= esc_url($imageUrl); ?>"
     alt="<?= esc_attr($alt); ?>"
     class="<?= esc_attr($class); ?>"
     height="<?= esc_attr($height); ?>"
     width="<?= esc_attr($width); ?>"
     decoding="async"
     loading="<?= $lazyLoad ? 'lazy' : 'eager'; ?>"
>