<?php
    use Pulp\Actions;
    $actions = new Actions();

//    $filePath = $actions->getAsset('/assets/css/theme.css');
?>
<link rel='stylesheet' href='<?= get_theme_file_uri("/blocks/{$blockName}/style.css"); ?>'>
