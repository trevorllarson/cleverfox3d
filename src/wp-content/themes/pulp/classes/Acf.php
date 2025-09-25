<?php

    namespace Pulp;

    class Acf
    {
        public function __construct()
        {
            add_filter('acf/load_field/name=button_style', [$this, 'buttonStyleOptions']);
        }

        public function buttonStyleOptions( $field ) {

            // reset choices
            $field['choices'] = [
                'blue' => 'Blue',
                'white' => 'White',
            ];

            // return the field
            return $field;

        }
    }
