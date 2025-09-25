<?php

namespace Pulp;

class Admin extends Actions
{
    public function __construct()
    {
        add_action('login_head', [$this, 'loginLogo']);
        add_filter('login_headerurl', [$this, 'loginLogoUrl']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('admin_head', [$this, 'adminSidebarStyles']);
        add_filter('attachment_fields_to_edit', [$this, 'focusPointField'], 10, 2);
        add_action('admin_head', [$this, 'focusPointScripts']);
        add_action('admin_init', [$this, 'disableBlockSuggestions']);
        add_filter('acf/settings/show_admin', [$this,'acfShowAdmin']);
    }

    function disableBlockSuggestions()
    {
        remove_action(
            'enqueue_block_editor_assets',
            'wp_enqueue_editor_block_directory_assets'
        );
        remove_action(
            'enqueue_block_editor_assets',
            'gutenberg_enqueue_block_editor_assets_block_directory'
        );
    }

    /**
     * Registers scripts for the admin
     */
    public function adminEnqueueScripts($hook)
    {
        wp_enqueue_script('theme-editor', parent::getAsset('/assets/js/editor.js'), ['wp-blocks', 'wp-dom-ready', 'wp-edit-post']);
    }

    /**
     * Registers scripts for our blocks
     * Use is_admin() to isolate admin styles to the editor because these enqueue to the front too
     */
    public function enqueueBlockAssets()
    {
        if (is_admin()) {
            wp_enqueue_style('theme-editor', parent::getAsset('/assets/css/editor.css'), []);
        }
    }

   /**
     * Replaces the WordPress logo on the login screen
     */
    public function loginLogo()
    {
        echo '<style type="text/css">
			#login h1 a {
				background-image: url("' . get_stylesheet_directory_uri() . '/assets/images/logo.svg");
				background-size: 100%;
				width: 146px;
				height: 31px;
			}
		</style>';
    }

    /*
    Link to the site front instead of wordpress.org
    */
    public function loginLogoUrl()
    {
        return home_url();
    }

    /*
    * Add Front Page edit link to admin Pages menu
    */
    public function adminMenu()
    {
        global $submenu;
        if (get_option('page_on_front')) {
            $submenu['edit.php?post_type=page'][501] = array(
                __('Front Page', 'pulp'),
                'manage_options',
                get_edit_post_link(get_option('page_on_front'))
            );
        }
        add_menu_page('Reusable Blocks', 'Reusable Blocks', 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22);

        if (defined('DEVELOPERS_ID')) {
            if (!in_array(get_current_user_id(), DEVELOPERS_IDS) && wp_get_environment_type() === 'production') {
                remove_submenu_page('themes.php', 'themes.php');
                remove_menu_page('plugins.php', 'plugins.php');
            }
        }
    }

    /*
    * Hide ACF for everyone except EP
    */
    function acfShowAdmin($show)
    {
        // always show locally
        if (wp_get_environment_type() === 'local') {
            return true;
        }
        // enforce definition
        if (!defined('DEVELOPERS_ID')) {
            return false;
        }
        return (in_array(get_current_user_id(), DEVELOPERS_IDS));
    }

    public function adminSidebarStyles()
    {
        echo '<style>
            #adminmenu li.wp-menu-separator {
                margin: 8px 0;
                height: 2px;
                background: #4a4a4a;
            }
        </style>';
    }

    function focusPointField($form_fields, $post)
    {
        $nonce = wp_create_nonce("focal_point_nonce");
        $form_fields['focus_point'] = array(
            'label' => 'Focal Point',
            'input' => 'html',
            'html'  => '<input type="hidden" id="focus-point-field" data-nonce="' . $nonce . '" data-post-id="' . $post->ID . '"><button type="button" class="button button-small" id="focal-point-show" style="margin-right: 8px">Show</button><button type="button" class="button button-small" id="focal-point-remove">Remove</button><span id="focal-saved-message">Saved!</span>',
        );

        return $form_fields;
    }

    function focusPointScripts()
    {
        ?>
        <style>
        .edit-attachment-frame .attachment-media-view .details-image{
            position: relative;
        }
        .edit-attachment-frame .attachment-media-view .details-image .details-image{
            margin: 0;
            background: none;
            max-height: 100%;
            width: 100%;
            height: auto;
        }
        #focal-point{
            width: 25px;
            height: 25px;
            display: block;
            position: absolute;
            background: rgba(255, 255, 255, .3);
            border: solid 1px rgba(255, 255, 255, .6);
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        #focal-saved-message{
            display: none;
            color: green;
            min-height: 26px;
            line-height: 2.18181818;
            padding: 0 8px;
            font-size: 11px;
        }
        </style>
        <script>
            let draggableElement;
            let draggableArea;
            const showMessage = function(){
                jQuery('#focal-saved-message').fadeIn();
                setTimeout(function(){
                    jQuery('#focal-saved-message').fadeOut();
                }, 3000);
            };
            const getFocalPointField = function(){
                return document.getElementById('focus-point-field');
            }
            const getFocalPoint = function(){
                const field = getFocalPointField();
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : window.ajaxurl,
                    data : {
                        action: "get_focal_point",
                        nonce: field.dataset.nonce,
                        post_id : field.dataset.postId
                    },
                    success: function(response) {
                        // if we don't have a focal point already, add one to the center
                        if(!response){
                            addFocalPoint(50, 50);
                            return;
                        }

                        // otherwise add where it was last saved
                        let points = response.split(',');
                        addFocalPoint(points[0], points[1]);
                    }
                })
            }
            const addFocalPoint = function(xPercent,yPercent){
                const image = document.querySelector('img.details-image');
                // create an area where we can calculate offsets
                let imageWrap = document.querySelector('div.details-image');
                if(!imageWrap){
                    imageWrap = document.createElement('div');
                    imageWrap.className = 'details-image';

                    image.parentNode.insertBefore(imageWrap, image);
                    // so the wrap behaves like a scaling image
                    imageWrap.style.aspectRatio = `${image.width} / ${image.height}`;
                    imageWrap.append(image);
                    // establish draggable bounds so admin can't set a point outside of the image
                    draggableArea = imageWrap;
                }

                const existingPoint = document.getElementById('focal-point');
                if(existingPoint){
                    existingPoint.remove();
                }

                const point = document.createElement('span');
                point.id = 'focal-point';
                point.style.left = `${xPercent}%`;
                point.style.top = `${yPercent}%`;

                imageWrap.append(point);

                // when we mouse down on this element assign it to the global element
                // then, in document.mousemove we can check to see if this element was clicked
                point.addEventListener('mousedown', e => {
                    draggableElement = point;
                })
            };
            const saveFocalPoint = function(x,y){
                const field = getFocalPointField();
                if(!field){
                    return;
                }
                field.value = `${x},${y}`;
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : window.ajaxurl,
                    data : {
                        action: "save_focal_point",
                        nonce: field.dataset.nonce,
                        post_id : field.dataset.postId,
                        points: field.value
                    },
                    success: function(response) {
                        if(response.success){
                            showMessage();
                        }
                    }
                })
            };
            const removeFocalPoint = function(x,y){
                const field = getFocalPointField();
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : window.ajaxurl,
                    data : {
                        action: "remove_focal_point",
                        nonce: field.dataset.nonce,
                        post_id : field.dataset.postId
                    },
                    success: function(response) {
                        if(response.success){
                            jQuery('#focal-point').remove();
                            showMessage();
                        }
                    }
                })
            };
            document.addEventListener('DOMContentLoaded', () => {
                // all of this has to happen on click because elements get added late
                document.addEventListener('mousemove', event => {
                    if(draggableElement){
                        let bounds = draggableArea.getBoundingClientRect();
                        // event coordinates are relative to the page, offset them by the draggable area's position in the page
                        let left = event.pageX - bounds.left;
                        let top = event.pageY - bounds.top;
                        // constrain the point to the bounds
                        if(event.pageX > bounds.left && event.pageX < bounds.right){
                            draggableElement.style.left = `${left}px`;
                        }
                        if(event.pageY > bounds.top && event.pageY < bounds.bottom){
                            draggableElement.style.top = `${top}px`;
                        }
                    }
                });
                document.addEventListener('mouseup', event => {
                    if(draggableElement){
                        let bounds = draggableArea.getBoundingClientRect();
                        let left = event.pageX - bounds.left;
                        let top = event.pageY - bounds.top;
                        const xPercent = ((left / bounds.width) * 100).toFixed(0);
                        const yPercent = ((top / bounds.height) * 100).toFixed(0);
                        saveFocalPoint(xPercent, yPercent);
                        draggableElement = null;
                    }
                });
                document.addEventListener('click', (event) => {

                    if(event.target.id === 'focal-point-remove'){
                        removeFocalPoint();
                        return;
                    }

                    if(event.target.id === 'focal-point-show'){
                        getFocalPoint();
                        return;
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * Removes the admin bar from the front of the site
     */
    function removeAdminBarHeading()
    {
        remove_action('wp_head', '_admin_bar_bump_cb');
    }
}
