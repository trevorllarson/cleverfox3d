<?php

namespace Pulp;

require __DIR__ . '/vendor/autoload.php';

use League\Glide\ServerFactory;
use League\Glide\Urls\UrlBuilderFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureException;

class Glide
{
    private $config = [];
    private $imageConfig = [];
    private $uploads;
    protected $glidePath;

    public function __construct()
    {
        $this->uploads = wp_get_upload_dir();
        $this->glidePath = '/img/';

        $this->config['source'] = $this->uploads['basedir'];
        $this->config['cache'] = $this->uploads['basedir'] . DIRECTORY_SEPARATOR . 'cache';
        $this->config['baseUrl'] = $this->glidePath;
        $this->imageConfig = [
            'fm' => 'webp'
        ];

        $this->addActions();
    }

    /**
     * Catch Wordpress requests
     */
    public function addActions()
    {
        add_action('init', [$this, 'addRewrites']);
        add_action('parse_query', [$this, 'handle']);
    }

    /**
     * Add Glide images rewrite rules
     */
    public function addRewrites()
    {
        add_rewrite_tag('%glide-size%', '(.*?)');
        add_rewrite_tag('%glide-path%', '(.*)');
        add_rewrite_rule(sprintf('%s([^/]*)/(.*)', $this->glidePath), 'index.php?glide-size=$matches[1]&glide-path=$matches[2]', 'top');
    }

    /**
     * Handle image requests
     */
    public function handle()
    {
        global $wp_query;

        if (!is_object($wp_query)) {
            return;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (strpos($_SERVER['REQUEST_URI'], $this->glidePath) === false) {
            return;
        }
        $this->serveImage($_SERVER['REQUEST_URI']);
    }

    /**
     * Serve as image
     *
     * @param string $requestedUrl
     */
    protected function serveImage(string $requestedUrl)
    {
        // inbound URL will have parameters
        $url = parse_url($requestedUrl);

        // bail if we don't have a source image
        $requestedPath = str_replace($this->glidePath, '', $url['path']);
        if (!file_exists($this->uploads['basedir'] . DIRECTORY_SEPARATOR . $requestedPath)) {
            status_header(404);
            echo 'Image not found.';
            die(1);
        }
        /*
            Validation:
            input path and params must match path and params used in imageUrl()
            imageUrl builder prepends glidePath for us, so we have to do that here in manually in validateRequest()
            we don't need it in outputImage() though
        */
        try {
            parse_str($url['query'], $requestedParams);
            $params = $this->imageParams($requestedParams);
            SignatureFactory::create(AUTH_KEY)->validateRequest($this->glidePath . $requestedPath, $params);
            status_header(200);
            $server = ServerFactory::create($this->config);
            $server->outputImage($requestedPath, $params);
            die();
        } catch (SignatureException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Image Glide URL
     *
     * @param string|int $url String URL or integer attachment ID
     * @param string     $slug
     *
     * @return mixed
     */
    public function imageUrl($url, array $params)
    {
        if (is_int($url)) {
            $url = wp_get_attachment_url($url);
        }
        $urlBuilder = UrlBuilderFactory::create($this->glidePath, AUTH_KEY);
        return $urlBuilder->getUrl($this->relativeUploadUrl($url), $this->imageParams($params));
    }

    /**
     * Extract relative media upload URL.
     *
     * @param string $url
     *
     * @return string
     */
    public function relativeUploadUrl(string $url)
    {
        $url = str_replace('http://', 'https://', $url);
        $baseUrl = str_replace('http://', 'https://', $this->uploads['baseurl']);
        return ltrim(str_replace($baseUrl, '', $url), '/');
    }

    /**
     * Combine input params with defaults
     *
     * @param array $params
     *
     * @return array
     */
    private function imageParams(array $params)
    {
        return  array_merge($this->imageConfig, $params);
    }
}
