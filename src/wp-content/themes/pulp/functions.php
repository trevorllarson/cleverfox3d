<?php

/**
 * Autoload our theme classes
 */

spl_autoload_register(function ($class_name) {
    if (str_contains($class_name, 'Pulp')) {
        $class_name =  str_replace("Pulp\\", '', $class_name);
        require get_template_directory() . '/classes/' . $class_name . '.php';
    }
});

// required
new Pulp\CustomPostTypes();
new Pulp\Actions();
new Pulp\Admin();
new Pulp\Glide();
new Pulp\Blocks();
new Pulp\Filters();
new Pulp\Security();
new Pulp\GenerateBlock();

// opt-in
// new Pulp\Acf();
// new Pulp\Search();
// new Pulp\Shortcodes();
// new Pulp\GravityForms();
