<?php

function electricpulp_setup_theme_supported_features()
{
    add_theme_support('editor-color-palette', [
        ['name' => __('White', 'electricpulp'), 'slug' => 'white', 'color' => '#ffffff'],
        ['name' => __('Black', 'electricpulp'), 'slug' => 'black', 'color' => '#000000'],
    ]);

    add_theme_support('disable-custom-colors');
    //   add_theme_support('editor-gradient-presets',array());
    //   add_theme_support('disable-custom-gradients');
    add_theme_support('align-wide');
    add_theme_support('disable-custom-font-sizes');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'electricpulp_setup_theme_supported_features');

global $acfBlocks, $acfBlockNamespace;

$acfBlocks = [
    [
        'slug' => 'content-wrap',
        'name' => 'Content Wrap',
        'description' => 'Content Wrap',
        'icon' => '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M8.75 5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5zm0 4a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5zM8 13.75a.75.75 0 0 1 .75-.75h6.5a.75.75 0 0 1 0 1.5h-6.5a.75.75 0 0 1-.75-.75zM8.75 17a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5z" fill="#212121"/></svg>', // svg, dashicon
        'keywords' => ['content', 'width', 'narrow', 'wrap'],
        'preview' => 'skip' // skip, or path to image. Defaults to 'use-slug' which will use the slug name as the image name + .jpg
    ],
];

// Ensure default empty states for some of the less essential fields
foreach($acfBlocks as $index => $acfBlock) {
    if(!isset($acfBlock['description'])) $acfBlocks[$index]['description'] = '';
    // TODO: decide on a default icon for all blocks, allowing for adding blocks without providing a specific icon
    if(!isset($acfBlock['icon'])) $acfBlocks[$index]['icon'] = '';
    if(!isset($acfBlock['keywords'])) $acfBlocks[$index]['keywords'] = [];
    if(!isset($acfBlock['preview'])) $acfBlocks[$index]['preview'] = 'use-slug';
}

$acfBlockNamespace = 'ep';

add_filter('allowed_block_types_all', 'ep_allowed_block_types');
function ep_allowed_block_types($allowed_blocks)
{
    global $acfBlocks, $acfBlockNamespace;

    $allowedBlocks = [
        'gravityforms/form',
        'core/image',
        'core/paragraph',
        'core/heading',
        'core/list',
        'core/list-item',
        // 'core/quote',
        // 'core/separator',
        // 'core/columns',
        // 'core/buttons',
        'core/block', // DO NOT REMOVE THIS, OR REUSABLE BLOCKS WILL BE MISSING FROM YOUR OPTIONS
    ];

    // TODO: pull in full list of blocks and comment out the ones we don't want

    foreach ($acfBlocks as $acfBlock) {
        $allowedBlocks[] = 'acf/' . $acfBlockNamespace . '-' . $acfBlock['slug'];
    }
    return $allowedBlocks;
}

add_action('acf/init', 'electricpulp_acf_init');
function electricpulp_acf_init()
{
    global $acfBlocks, $acfBlockNamespace;

    if (function_exists('acf_register_block')) {

        $themeKeywords     = [$acfBlockNamespace, 'ep'];
        $customBlockGroups = [
            [
                'group'  => 'layout',
                'blocks' => $acfBlocks,
            ],
        ];

        foreach ($customBlockGroups as $index => $group) {
            foreach ($group['blocks'] as $block) {

                $exampleArray = [];

                if(!empty($block['preview']) && $block['preview'] !== 'skip') {

                    if($block['preview'] === 'use-slug') {
                        $block['preview'] = home_url() . '/assets/images/block-previews/' . $block['slug'] . '.jpg';
                    } else {
                        $block['preview'] = home_url() . $block['preview'];
                    }

                    $exampleArray = ['attributes' => ['mode' => 'preview', 'data' => ['preview_image' => $block['preview']]]];
                }

                acf_register_block([
                    'name'            => $acfBlockNamespace . '-' . $block['slug'],
                    'title'           => __($block['name']),
                    'description'     => __($block['description']),
                    'render_callback' => 'electricpulp_acf_block_render_callback',
                    'category'        => $group['group'],
                    'icon'            => $block['icon'],
                    'mode'            => 'preview',
                    'supports'        => ['align' => false, 'mode' => false, 'jsx' => true],
                    'keywords'        => array_merge($themeKeywords, $block['keywords']),
                    'example'         => $exampleArray,
                ]);
            }
        }
    }
}

//load in the appropriate blocks
function electricpulp_acf_block_render_callback($block)
{
    global $acfBlockNamespace;

    // convert name ("acf/ep-floating-block") into path friendly slug ("ep-floating-block")
    $slug = str_replace([$acfBlockNamespace . '-', 'acf/'], '', $block['name']);

    // include a template part from within the "template-parts/block" folder
    if (file_exists(get_theme_file_path("/blocks/{$slug}.php"))) {
        include get_theme_file_path("/blocks/{$slug}.php");
    }
}

// process general block classnames (pt-sm pb-sm etc)
function electricpulp_get_block_class_attr(array $block): string
{
    $className = $block['className'] ?? '';
    return !empty($block['align']) ? "{$className} align {$block['align']}" : $className;
}
