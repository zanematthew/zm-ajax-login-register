<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class ALRHtml {

    /**
     * Provides a unified way to build HTML form fields for ALR front-end forms.
     *
     * This method also builds dynamic filters AND actions using the following
     * naming convention:
     *
     *      add_filter( "{$prefix}_form_fields" ); Used to add or remove fields
     *      add_filter( "{$prefix}_field_container_classes" ); Used to add or remove the class names
     *      add_filter( "{$prefix}_order_fields" ); Used to change the field order
     *      add_filter( "{$prefix}_fields_args" ); Adjust the HTML attributes for a single field
     *
     *
     *      add_action( "{$key}_above_field" ); $key is the name of the form field
     *      add_action( "{$key}_below_field" ); $key is the name of the form field
     *
     * @since 2.0.0
     *
     * @param   $fields     (array)     Containing the field an HTML attributes for each field.
     * @param   $prefix     (string)    The prefix
     * @param   $order      (array)     The order
     * @return  The HTML of all form fields, less the form tag.
     */
    public function buildFormFieldsHtml( $fields=null, $prefix=null, $order=null ){

        $fields = apply_filters( $prefix . '_form_fields', $fields );

        $default_classes = apply_filters( $prefix . '_default_field_container_classes', array(
            ZM_ALR_NAMESPACE . '_form_field_container'
            ) );


        // Allow for adding additional fields below
        $new_fields = array();
        foreach( $fields as $key => $value ){

            $new_fields[ $key ] = $value;
            $new_fields = apply_filters( 'above_' . $key, $new_fields );
            $new_fields = apply_filters( 'below_' . $key, $new_fields );

        }

        $order = apply_filters( $prefix . '_order_fields', array_keys( $new_fields ) );

        $html = null;

        foreach( $order as $key ){

            if ( empty( $new_fields[ $key ] ) ){

                $html .= "invalid key {$key} added for order<br />";
                $html .= PHP_EOL;

            } else {

                $args = apply_filters( $prefix . '_fields_args', wp_parse_args( $new_fields[ $key ], array(
                    'extra' => null,
                    'required' => null,
                    'size' => null,
                    'name' => $key,
                    'id' => sanitize_title( $key ),
                    'placeholder' => esc_attr( $new_fields[ $key ]['title'] ),
                    'type' => esc_attr( $new_fields[ $key ]['type'] ),
                    'html' => null,
                    'value' => esc_attr( $new_fields[ $key ]['title'] )
                    ) ) );

                $container_classes = apply_filters( sanitize_title( $key ) . '_field_container_classes', array_merge( $default_classes, array(
                    ZM_ALR_NAMESPACE . '_' . $args['type'] . '_container',
                    $prefix . '_' . $args['type'] . '_container'
                    ) ) );


                // Handle merging of default form field classes
                if ( empty( $args['classes'] ) )
                    $args['classes'] = array();

                $args['classes'] = array_merge( $args['classes'], array(
                    ZM_ALR_NAMESPACE . '_' . esc_attr( $new_fields[ $key ]['type'] ) . '_field',
                    ZM_ALR_NAMESPACE . '_form_field'
                    ) );

                $field_classes = implode( " ", $args['classes'] );

                if ( $args['required'] == true ){
                    $args['required'] = 'required';
                }

                $html .= '<div class="' . implode( " ", $container_classes ) . '">';

                switch ( $new_fields[ $key ]['type'] ) {

                    case 'text':
                        $html .= '<label for="' . $args['id'] . '" class="' . ZM_ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input type="text" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' ' . $args['required'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'password':
                        $html .= '<label for="' . $args['id'] . '" class="' . ZM_ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input type="password" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' ' . $args['required'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'email':
                        $html .= '<label for="' . $args['id'] . '" class="' . ZM_ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input autocorrect="none" autocapitalize="none" type="email" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' ' . $args['required'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'checkbox':
                        $html .= '<input type="checkbox" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" ' . $args['extra'] . ' />';
                        $html .= '<label for="' . $args['id'] . '" class="' . ZM_ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= PHP_EOL;
                        break;

                    case 'html':
                        $html .= $args['html'];
                        $html .= PHP_EOL;
                        break;

                    case 'submit':
                        $html .= '<input type="submit" value="' . $args['value'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" name="' . $args['name'] . '" ' . $args['extra'] . '/>';
                        $html .= PHP_EOL;
                        break;

                    default:
                        $html .= 'no default';
                        $html .= PHP_EOL;
                        break;
                }

                $html .= '</div>';

            }

        }

        $above_html = null;
        $above_html = apply_filters( $prefix . '_above_fields', $above_html );

        $below_html = null;
        $below_html = apply_filters( $prefix . '_below_fields', $below_html );


        return $above_html . $html . $below_html;
    }


    /**
     * Provide a unified way to add links to the bottom of the front-end forms.
     *
     * Additionally the following filters, actions are dynamically created:
     *
     *      add_filter( "{$prefix}_form_links" ); An array of HTML links
     *      add_filter( "{$prefix}_link_args" ); An array containing the HTML attributes for a
     *      single link
     *
     * @since 2.0.0
     *
     * @param   $links      (array)     The HTML links
     * @param   $prefix     (string)    The unique ID
     * @return  An HTML UL LI A of links
     */
    public function buildFormHtmlLinks( $links=null, $prefix=null ){

        $links = apply_filters( $prefix . '_form_links', $links );

        if ( $links ){

            $html = null;
            foreach( $links as $key => $value ){

                 $args = wp_parse_args( $value ,
                    array(
                    'href' => '#',
                    'class' => 'foo',
                    'title' => esc_attr( $value['text'] ),
                    'text' => esc_attr( $value['text'] ),
                    'id' => $prefix . '_' . sanitize_title( $value['text'] ),
                    'name' => $key
                    ) );

                $args = apply_filters( $prefix . '_link_args', $args );

                $classes = array(
                    ZM_ALR_NAMESPACE . '_link',
                    $prefix . '_link',
                    $args['class']
                    );

                $html .= '<li><a href="'.$args['href'].'" class="' . implode( " ", $classes ) . '" id="'.$args['id'].'" title="'.$args['title'].'">'.$args['text'].'</a></li>';
                $html .= PHP_EOL;
            }

        } else {

            $html = null;

        }

        return '<ul class="' . ZM_ALR_NAMESPACE . '_ul_container">' . $html . '</ul>';
    }

}