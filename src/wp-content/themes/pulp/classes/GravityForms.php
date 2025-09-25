<?php

namespace Pulp;

class GravityForms
{
    public function __construct()
    {
        add_filter('gform_confirmation_anchor', '__return_true');
        add_filter('gform_disable_css', [$this, 'disableCss']);
        add_filter('gform_disable_form_theme_css', [$this, 'disableCss']);
        add_filter('gform_submit_button', [$this, 'gformSubmitButton'], 10, 2);
    }

    public function disableCss()
    {
        if (is_admin()) {
            return false;
        }
        return true;
    }

    /**F
     * Filters class attribute on submit buttons.
     */
    public function gformSubmitButton(string $button, array $form): string
    {
        return str_replace('gform_button', 'gform_button btn', $button);
    }
}
