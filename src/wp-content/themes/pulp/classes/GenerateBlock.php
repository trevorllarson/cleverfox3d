<?php

namespace Pulp;

use Pulp\Blocks;

class GenerateBlock
{
    public $name;
    public $slug;
    public $withBlockObject;

    public function __invoke($args, $assoc_args)
    {
        $this->name = isset($assoc_args['name']) ? $assoc_args['name'] : null;
        $this->withBlockObject = isset($assoc_args['with-block-object']) ? true : false;
        $this->slug = sanitize_title($this->name);
        $this->run();
    }

    /*
    * Generates block boilerplate
    */
    public function run()
    {
        if (!$this->name) {
            \WP_CLI::error('--name required');
        }

        $blocks = new Blocks();
        if (in_array($this->slug, $blocks->getBlocks())) {
            \WP_CLI::error('Block already exists!');
        }

        $dir = get_template_directory() . '/blocks/' . $this->slug;
        mkdir($dir);

        file_put_contents($dir . '/' . $this->slug . '.php', $this->generateTemplate());
        file_put_contents($dir . '/block.json', $this->generateJson());
        file_put_contents($dir . '/' . $this->slug . '.scss', $this->generateScss());
        file_put_contents(get_template_directory() . '/assets/css/' . $this->slug . '.css', $this->generateCss());

        \WP_CLI::success('Block added, Don\'t forget to add a preview.jpg!');
    }

    /*
    * Outputs everything in the render file.
    * For clarity, the strings are generated in parts. Indentation is pretty gross to look at in one shot.
    */
    private function generateTemplate(): string
    {
        return  $this->generatePhp() . $this->generateHtml();
    }

    /*
    * Generates block.json boilerplate
    * TODO: chose a icon that represents "custom" as the default?
    */
    private function generateJson(): string
    {
        return '{
    "name": "pulp/' . $this->slug . '",
    "title": "' . $this->name . '",
    "description": "",
    "category": "custom-blocks",
    "icon": "",
    "apiVersion": 3,
    "keywords": ["' . implode('","', explode('-', $this->slug)) . '"],
    "acf": {
        "mode": "preview",
        "renderCallback": "Pulp\\\Blocks::render"
    },
    "styles": [],
    "supports": {
        "spacing": {
            "padding": true,
            "margin": true
        }
    },
    "example": {
        "attributes": {
            "mode": "preview",
            "data": {
                "preview_image": true
            }
        }
    }
}';
    }

    /*
    * Set up $blockObject boilerplate if that flag was set when the block was generated.
    */
    private function getBlockObjectStr(): string
    {
        return $this->withBlockObject ? '
$blockObject = [
    \'title\' =>  get_field(\'title\')
];' : '';
    }

    /*
    * To support preview images, we need to check for that data existing in the render file.
    * If it's there, then we can show the image and bail.
    */
    private function getPreviewImageStr(): string
    {
        return '
// if this is set, then we\'re rendering in the block inserter
if (isset($block[\'data\'][\'preview_image\'])) {
    get_template_part(\'parts/block-preview\', args: [\'block\' => \'' . $this->slug . '\']);
    return;
}';
    }

    /*
    * Add in our "is this block populated" check to show admin a message when the block fields are empty
    */
    private function getPopulatedCheckStr(): string
    {
        return '   
if (!Template::blockIsPopulated($block)) {
    get_template_part(\'parts/unpopulated-block\');
    return;
}';
    }

    /*
    * Set up the starting point for HTML output
    */
    private function generateHtml(): string
    {
        return '
<section <?php echo $is_preview ? \'\' : get_block_wrapper_attributes() ?>>
    <?php get_template_part(\'parts/block-stylesheet\', args: [\'block\' => \'' . $this->slug . '\']); ?>
    Hello World
</section>
';
    }

    /*
    * At the start of each block, add the use and other preflight
    */
    private function generatePhp(): string
    {
        return '<?php

use Pulp\Template;
' . $this->getPreviewImageStr() . '        
' . $this->getPopulatedCheckStr() . '        
' . $this->getBlockObjectStr() . '        
?>
';
    }

    /*
    * Prepend the CSS ouput with @use for utils since we'll almost certainly need that
    */
    private function generateScss(): string
    {
        return '@use "../../../../../../assets/scss/utils";
' . $this->generateCss();
    }

    /*
    * Wordpress will add our block class automatically, so start our styles with the same slug
    * Also starts the file with the @use for utils since we'll almost certainly need that
    */
    private function generateCss(): string
    {
        return '.wp-block-pulp-' . $this->slug . '{}';
    }
}

add_action('cli_init', function () {
    \WP_CLI::add_command('ep-generate-block', 'Pulp\GenerateBlock');
});
