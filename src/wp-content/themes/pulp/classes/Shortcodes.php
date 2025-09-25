<?php

namespace Pulp;

class Shortcodes
{
    public function __construct()
    {
        add_shortcode('button', [$this, 'button']);
        add_shortcode('lead', [$this, 'leadText']);
        add_shortcode('video_embed', [$this, 'videoEmbed']);
    }

    public function button($atts, $content = ''): string
    {
        $a = shortcode_atts([
            'link'   => null,
            'url'    => null,
            'color'  => 'orange',
            'newtab' => 'false',
        ], $atts);

        $target = ($a['newtab'] === 'true') ? '_blank' : '';
        $href   = !empty($a['url']) ? $a['url'] : $a['link'];

        if (empty($href)) {
            return '';
        }

        if ($a['color'] === 'blue') {
            $buttonClass = '';
        } else {
            $buttonClass = 'btn-primary btn-' . $a['color'];
        }

        ob_start();
        include get_theme_file_path('/components/button-shortcode.php');
        return ob_get_clean();
    }



    public function leadText($atts, $content = ''): string
    {
        if (empty($content)) {
            return '';
        }
        ob_start();
        $content = wp_kses_post($content);
        include get_theme_file_path('/components/lead-text.php');
        return ob_get_clean();
    }

    public function videoEmbed($atts, $content = ''): string
    {
        if (empty($content)) {
            return '';
        }
        ob_start();
        include get_theme_file_path('/components/video-embed.php');
        return ob_get_clean();
    }
}
