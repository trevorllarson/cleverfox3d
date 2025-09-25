<?php

/**
 * Enqueue admin scripts
 */
function electricpulp_admin_scripts($hook)
{
    global $epHelpers;

    wp_enqueue_script('admin-js', $epHelpers->compiledAsset('/js/admin.js'));
}
// add_action('admin_enqueue_scripts', 'electricpulp_admin_scripts');


// TODO: this is already in ACF Blocks, should be removed here
function electricpulp_editor_assets()
{
    wp_enqueue_style(
        'electricpulp-editor-styles',
        get_home_url() . '/assets/css/editor.css',
        array(),
        filemtime(get_home_path() . '/assets/css/editor.css')
    );
    wp_enqueue_script(
        'electricpulp-editor-scripts',
        get_home_url() . '/assets/js/editor.js',
        array('wp-blocks')
    );
}
add_action('enqueue_block_editor_assets', 'electricpulp_editor_assets');

function electricpulp_custom_login()
{
    $highlight = '#a3d5d9';
    $highlightHover = '#7bd1db';
    $textColor = '#282f33';
    $backgroundColor = '#fff';
    $formBackground = '#efefef';
    $logoWidth = "300px";
    $logoHeight = "150px";

    echo '<style type="text/css">
        h1 a {
            background-image: url("' . get_stylesheet_directory_uri() . '/assets/images/login-logo.svg") !important;
            background-size: 100% !important;
            width: ' . $logoWidth . ' !important;
            height: ' . $logoHeight . ' !important;
        }
        /* Background Color */
        body.login {
            background: ' . $backgroundColor . ' !important;
        }
        /* Button Color */
        .wp-core-ui .button-primary {
            background: ' . $highlight . ';
            border-color: ' . $highlight . ';
            text-shadow: none;
            border-radius: 0;
            box-shadow: none;
            color: ' . $textColor . ';
        }
        .wp-core-ui .button-primary.hover, 
        .wp-core-ui .button-primary:hover, 
        .wp-core-ui .button-primary.focus, 
        .wp-core-ui .button-primary:focus {
            background: ' . $highlightHover . ';
            border-color: ' . $highlightHover . ';
            outline: none;
            box-shadow: none;
            color: ' . $textColor . ';
        }
        /* Form  */
        .login form,
        #wfls-prompt-overlay {
            background: ' . $formBackground . ';
            box-shadow: none;
        }
        .login form label {
            color: ' . $textColor . ';
        }
        .login p,
        .login h2,
        .login h3,
        .login button {
            color: ' . $textColor . '
        }
        input[type=text], input[type=search], input[type=radio], input[type=tel], input[type=time], input[type=url], input[type=week], input[type=password], input[type=checkbox], input[type=color], input[type=date], input[type=datetime], input[type=datetime-local], input[type=email], input[type=month], input[type=number], select, textarea{
            box-shadow: none;
            background: #fff;
        }
        
        input[type=text]:focus, input[type=search]:focus, input[type=radio]:focus, input[type=tel]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, input[type=password]:focus, input[type=checkbox]:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime]:focus, input[type=datetime-local]:focus, input[type=email]:focus, input[type=month]:focus, input[type=number]:focus, select:focus, textarea:focus{
            border-color: ' . $highlightHover . ';
            outline: none;
            box-shadow: none;
        }
        
        .login #login_error,
        div.updated, 
        .login .message, 
        .press-this #message {
            border-left-color: ' . $highlight . ';
        }
    </style>';
}
add_action('login_head', 'electricpulp_custom_login');

/*
 * Ajax Function Example
add_action('wp_ajax_nopriv_ajaxfunction', 'ajaxfunction');
add_action('wp_ajax_sort_ajaxfunction', 'ajaxfunction');
function ajaxfunction () {
  // function logic here
  echo $_POST['param_1'];
  die();
}

$.post("/wp/wp-admin/admin-ajax.php", {
   'action': 'ajaxfunction',
   'param_1': 'Testing'
});
*/
