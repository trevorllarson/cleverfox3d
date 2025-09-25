<?php

namespace Pulp;

class Template
{
    /*
    * Gets the URL for an image with Glide support
    */
    public static function getImageURL($imageId, $width, $height, $fit = 'crop')
    {
        // if we're not using glide, send back a regular WP image URL
        if (DISABLE_GLIDE) {
            // registerd and default sizes
            $sizes = get_intermediate_image_sizes();
            $availableImages = [];
            foreach ($sizes as $size) {
                $availableImages[] = wp_get_attachment_image_src($imageId, $size);
            }
            // if we find an exact match, send it
            foreach ($availableImages as $imageData) {
                if ($imageData[1] == $width && $imageData[2] == $height) {
                    return $imageData[0];
                }
            }
            // find an image larger than the requested
            // TODO: find the smallest option larger than the requested size
            foreach ($availableImages as $imageData) {
                if ($imageData[1] > $width && $imageData[2] > $height) {
                    return $imageData[0];
                }
            }
            // fall back to the original image
            return wp_get_attachment_url($imageId);
        }

        return self::getGlideImageUrl($imageId, $width, $height, $fit);
    }

    /**
     * Get the glide url for an image
     */
    public static function getGlideImageUrl($imageId, $width, $height, $fit)
    {
        // Glide can't convert SVGs so just send back the URL
        $filetype = wp_check_filetype(wp_get_original_image_path($imageId));
        if ($filetype['type'] === 'image/svg+xml') {
            return wp_get_attachment_url($imageId);
        }

        // Crop to focal point if set
        if ($fit === 'crop') {
            if ($focal = get_post_meta($imageId, 'focal-point', true)) {
                $fit = $fit .= '-' . str_replace(',', '-', $focal);
            }
        }

        $glide = new Glide();
        return $glide->imageUrl($imageId, [
            'w' => $width,
            'h' => $height,
            'fit' => $fit
        ]);
    }

    /**
     * Output an image tag with the glide URL
     */
    public static function getGlideImageTag($imageId, $params, $loading = 'lazy')
    {
        $src = self::getGlideImageUrl($imageId, $params);
        $alt = get_post_meta($imageId, '_wp_attachment_image_alt', true);
        return '<img src="' . $src . '" alt="' . $alt . '" width="' . $params['w'] . '" height="' . $params['h'] . '" decoding="async" loading="' . $loading . '">';
    }

    /**
     * Get the primary term for the post, if it exists
     */
    public static function getPrimaryPostTerm(string $taxonomy, int $post_id = null): object|bool
    {
        // If no post ID is provided, set it to the current.
        if (! $post_id) {
            $post_id = get_the_ID();
        }

        // Make sure Yoast is active.
        if (class_exists('WPSEO_Primary_Term')) {
            // Get the primary term.
            $wpseo_primary_term = new \WPSEO_Primary_Term($taxonomy, $post_id);
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();

            // If we have one, return it.
            if ($wpseo_primary_term) {
                $term_object = get_term($wpseo_primary_term);

                if ('Uncategorized' === $term_object->name) {
                    return false;
                }

                return $term_object;
            }
        }

        // If no primary is found, get all the terms for the post.
        $terms = get_the_terms($post_id, $taxonomy);

        // If no terms are available for the post, fail.
        if (! $terms || is_wp_error($terms)) {
            return false;
        }

        if ('Uncategorized' === $terms[0]->name) {
            return false;
        }

        // Return the first term if terms are available.
        return $terms[0];
    }


    /**
     *  Checks if child block exists within tree
     */
    public static function hasBlock(iterable $blocks, string $block_name): bool
    {
        foreach ($blocks as $block) {
            if ($block['blockName'] === $block_name) {
                return true;
            }
            if ($block['innerBlocks']) {
                // Capture the result and only return if it's true.
                // Otherwise we don't traverse other branches of the tree.
                $result = self::hasBlock($block['innerBlocks'], $block_name);
                if (false !== $result) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks for h1 tag anywhere in the post/page content
     */
    public static function hasHeading1(): bool
    {
        // Bypass the below check if we know some blocks hard code in the h1.
        if (has_block('pulp/hero') || has_block('pulp/form-hero')) {
            return true;
        }

        $blocks = parse_blocks(get_the_content());
        return self::findHeading1($blocks);
    }

    /**
     * Checks for h1 tag anywhere in the post/page content
     */
    public static function findHeading1(iterable $blocks): bool
    {
        foreach ($blocks as $block) {
            if ('core/heading' === $block['blockName']) {
                if (isset($block['attrs']['level']) && $block['attrs']['level'] === 1) {
                    return true;
                }
            }
            if ($block['innerBlocks']) {
                foreach ($block['innerBlocks'] as $blocks) {
                    $result = self::findHeading1($block['innerBlocks']);
                    if (false !== $result) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function blockIsPopulated($block): bool
    {
        if (!is_admin()) {
            return true;
        }
        $fieldsToCheck = [];
        $fieldsToOmit = [];
        foreach ($block['data'] as $key => $value) {
            if (strpos($key, '_') === 0) {
                $fieldObject = get_field_object($value);
                // TODO:: add other blocks we should ignore
                if ($fieldObject['type'] === 'button_group') {
                    $fieldsToOmit[] = substr($key, 1);
                }
            } else {
                $fieldsToCheck[] = $key;
            }
        }
        $haveValues = false;
        foreach (array_diff($fieldsToCheck, $fieldsToOmit) as $key) {
            if ($block['data'][$key]) {
                $haveValues = true;
                break;
            }
        }
        return $haveValues;
    }

    public function renderResponsiveImage(
        array $image,
        array|null $imageMobile = null,
        int $desktopWidth = 0,
        int $desktopHeight = 0,
        int $mobileWidth = 0,
        int $mobileHeight = 0,
        string $class = '',
        int $breakpoint = 991,
        bool $isGlide = true,
        string $wpSize = 'original',
        bool $lazyLoad = true
    ): void {
        if (!$image) {
            return;
        }
        if (isset($image['url']) && empty($image['url'])) {
            return;
        }
        include get_theme_file_path('/components/responsive-image.php');
    }

    public static function renderImage(
        array|bool $mediaObj,
        int $height,
        int $width,
        bool $isGlide = true,
        string $wpSize = 'original',
        string $class = '',
        bool $lazyLoad = true
    ): void {
        if (!$mediaObj) {
            return;
        }
        include get_theme_file_path('/components/image.php');
    }

    public static function renderHeading(
        string $content,
        string $class = '',
        string $size = 'h2',
        bool $hasWrapper = false,
        string $wrapperClass = ''
    ): void {
        if (empty($content)) {
            return;
        }
        include get_theme_file_path('/components/heading.php');
    }

    public static function renderCopy(
        string $content,
        bool $hasWrapper = false,
        string $wrapperClass = '',
        string $wrapperEl = 'div'
    ): void {
        if (empty($content)) {
            return;
        }
        $content = trim($content);
        include get_theme_file_path('/components/copy.php');
    }

    public static function renderLink(
        array $link,
        string $class = ''
    ): void {
        if (empty($link)) {
            return;
        }
        include get_theme_file_path('/components/link.php');
    }

    public static function renderButtons(
        array|bool $buttons,
        string $wrapper = 'btn-row',
        bool $animate = false,
        int $delayTime = 0
    ): void {
        if (!is_array($buttons) || empty($buttons)) {
            return;
        }
        include get_theme_file_path('/components/buttons.php');
    }

    public static function renderButton(
        array $button,
        string $extraClass = ''
    ): void {
        if (!$button || !isset($button['link']['title'], $button['link']['url'])) {
            return;
        }
        include get_theme_file_path('/components/button.php');
    }


    public static function renderPageBlocks($postId): void
    {
        if (!$postId) {
            return;
        }

        $postItem = get_post($postId);
        if (!$postItem) {
            return;
        }

        $postContent = get_post($postId)->post_content;
        if (empty($postContent)) {
            return;
        }

        $blocks = parse_blocks($postContent);

        $blockContent = '';
        foreach ($blocks as $block) {
            $blockContent .= render_block($block);
        }
        echo $blockContent;

        return;
    }
}
