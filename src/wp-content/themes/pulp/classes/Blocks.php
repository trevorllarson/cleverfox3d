<?php

namespace Pulp;

class Blocks
{
    protected $custom_blocks;
    public $blockName;

    public function __construct()
    {
        add_action('init', [$this, 'blocksInit']);
        add_filter('allowed_block_types_all', [$this, 'allowedBlockTypes'], 10, 2);
        add_filter('block_categories_all', [$this, 'blockCategories']);
        add_filter('acf/settings/load_json', [$this, 'loadAcfFieldGroup']);
        add_action('admin_menu', [$this, 'adminMenu']);
        $this->custom_blocks = [];
    }

    /**
     * Load ACF field groups for blocks
     */
    public function loadAcfFieldGroup($paths)
    {
        $blocks = $this->getBlocks();
        foreach ($blocks as $block) {
            $paths[] = get_template_directory() . '/blocks/' . $block;
        }
        return $paths;
    }

    /**
     * Get Blocks
     */
    public function getBlocks()
    {
        $theme   = wp_get_theme();
        $blocks  = get_option('pulp_blocks');
        $version = get_option('pulp_blocks_version');
        if (empty($blocks) || version_compare($theme->get('Version'), $version) || ( function_exists('wp_get_environment_type') && 'production' !== wp_get_environment_type() )) {
            $blocks = scandir(get_template_directory() . '/blocks/');
            $blocks = array_values(array_diff($blocks, array( '..', '.', '.DS_Store', '_base-block' )));

            update_option('pulp_blocks', $blocks);
            update_option('pulp_blocks_version', $theme->get('Version'));
        }
        return $blocks;
    }

    /**
     * Block categories
     *
     * @since 1.0.0
     */
    public function blockCategories($categories)
    {

        // Check to see if we already have a Custom Blocks category
        $include = true;
        foreach ($categories as $category) {
            if ('custom-blocks' === $category['slug']) {
                $include = false;
            }
        }

        if ($include) {
            $categories = array_merge(
                $categories,
                [
                    [
                        'slug'  => 'custom-blocks',
                        'title' => __('Custom Blocks', 'pulp'),
                        'icon'  => ''
                    ]
                ]
            );
        }

        return $categories;
    }


    /**
     * Registers blocks by finding the block.json files in our build directory
    */
    public function blocksInit()
    {
        $blocks = $this->getBlocks();
        foreach ($blocks as $block) {
            if (file_exists(get_template_directory() . '/blocks/' . $block . '/block.json')) {
                register_block_type(get_template_directory() . '/blocks/' . $block . '/block.json');
                $metadata = wp_json_file_decode(get_template_directory() . '/blocks/' . $block . '/block.json', ['associative' => true]);
                if (!$metadata) {
                    continue;
                }
                $this->custom_blocks[] = $metadata['name'];
            }
        }
    }

    /**
     * Creates allow-list of blocks for authors
    */
    public function allowedBlockTypes($allowed_block_types, $block_editor_context)
    {
        $allowedBlocks = [
            'core/address',
            'core/block', // necessary for patterns
            'core/button',
            'core/buttons',
            // 'core/group',
            'core/heading',
            'core/html',
            'core/image',
            'core/list-item',
            'core/list',
            // 'core/columns',
            // 'core/column',
            'core/more',
            'core/paragraph',
            'core/pullquote',
            'core/separator',
            'core/shortcode',
            // 'core/table',
            'core/video',
            'core/embed',
            'gravityforms/form'
        ];
        return array_merge($this->custom_blocks, $allowedBlocks);
    }

    public static function render($block, $content, $is_preview, $post_id, $wp_block, $context): void
    {
        $name = explode('/', $block['name']);
        $slug = end($name);
        if (file_exists(get_theme_file_path("/blocks/{$slug}/{$slug}.php"))) {
            include get_theme_file_path("/blocks/{$slug}/{$slug}.php");
        }
    }

    function adminMenu()
    {
        add_menu_page('Block Previews', 'Block Previews', 'manage_options', 'cms-block-previews', [$this, 'previews'], 'dashicons-sos', 0);
    }

    public function previews()
    {
        $blocks = $this->getBlocks();
        ?>
        <div class="wrap">

            <h1 style="margin-bottom: 25px;">Block Previews</h1>
            <div style="display: grid; gap: 30px; grid-template-columns: repeat(auto-fit, minmax(700px, 1fr));">
            <?php
            foreach ($blocks as $block) :
                $title = '';
                $image = '';

                if (file_exists(get_template_directory() . '/blocks/' . $block . '/block.json')) {
                    $metadata = wp_json_file_decode(get_template_directory() . '/blocks/' . $block . '/block.json', ['associative' => true]);
                    if (!$metadata) {
                        continue;
                    }
                    $title = $metadata['title'] ?? '';
                }

                if (file_exists(get_template_directory() . '/blocks/' . $block . '/preview.jpg')) {
                    $image = get_template_directory_uri() . '/blocks/' . $block . '/preview.jpg';
                }

                if (empty($blockTitle) || empty($blockPreviewImage)) {
                    // continue;
                }
                ?>

                <div style="box-sizing: border-box; padding: 20px; border-radius: 4px; background: white; width: 100%; margin-inline: auto; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: grid; place-content: center;">
                    <h2 style="margin-top: 0; text-align: center;"><?= $title ?></h2>
                    <?php if ($image) : ?>
                        <img src="<?= $image ?>" style="width: 100%; height: auto" alt="">
                    <?php else : ?>
                        <p style="text-align: center;">No preview image available for this block.</p>
                    <?php endif; ?>
                </div>

                <?php
            endforeach;
            ?>
            </div>
        </div>
        <?php
    }
}
