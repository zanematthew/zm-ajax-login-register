<?php

Class ALRHtml {

    public function buildFormFieldsHtml( $fields=null, $prefix=null, $order=null ){

        // Use this filter to add/remove fields
        $fields = apply_filters( $prefix . '_form_fields', $fields );

        // Use this filter to add additional classes for the parent/container of each
        // form field
        $default_classes = apply_filters( $prefix . '_field_container_classes', array(
            ALR_NAMESPACE . '_form_field_container'
            ) );
        $html = null;

        // Used to change the order
        $order = apply_filters( $prefix . '_order_fields', array_keys( $fields ) );

        foreach( $order as $key ){

            // Key specific filter?
            // $field = apply_filters( $prefix . '_filter_field_' . $key, $fields[ $key ] );

            if ( empty( $fields[ $key ] ) ){

                $html .= "invalid key {$key} added for order<br />";
                $html .= PHP_EOL;

            } else {


                do_action( $key . '_above_field' );


                // filter
                $args = wp_parse_args( $fields[ $key ], apply_filters( $prefix . '_fields_args', array(
                    'extra' => null,
                    'required' => null,
                    'size' => null,
                    'name' => $key,
                    'id' => sanitize_title( $key ),
                    'classes' => array(
                        ALR_NAMESPACE . '_' . esc_attr( $fields[ $key ]['type'] ) . '_field',
                        ALR_NAMESPACE . '_form_field'
                        ),
                    'placeholder' => esc_attr( $fields[ $key ]['title'] ),
                    'type' => esc_attr( $fields[ $key ]['type'] ),
                    'html' => null,
                    'value' => esc_attr( $fields[ $key ]['title'] )
                    ) ) );

                $container_classes = array_merge( $default_classes, array(
                    ALR_NAMESPACE . '_' . $args['type'] . '_container',
                    $prefix . '_' . $args['type'] . '_container'
                    ) );

                $field_classes = implode( " ", $args['classes'] );

                $html .= '<div class="' . implode( " ", $container_classes ) . '">';

                switch ( $fields[ $key ]['type'] ) {

                    case 'text':
                        $html .= '<label for="' . $args['id'] . '" class="' . ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input type="text" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'password':
                        $html .= '<label for="' . $args['id'] . '" class="' . ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input type="password" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'email':
                        $html .= '<label for="' . $args['id'] . '" class="' . ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= '<input autocorrect="none" autocapitalize="none" type="email" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'checkbox':
                        $html .= '<input type="checkbox" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" ' . $args['extra'] . ' />';
                        $html .= '<label for="' . $args['id'] . '" class="' . ALR_NAMESPACE . '_label">' . $args['title'] . '</label>';
                        $html .= PHP_EOL;
                        break;

                    case 'html':
                        $html .= $args['html'];
                        $html .= PHP_EOL;
                        break;

                    case 'submit':
                        $html .= '<input type="submit" value="' . $args['value'] . '" id="' . $args['id'] . '" class="' . $field_classes . '" name="' . $args['name'] . '" />';
                        $html .= PHP_EOL;
                        break;

                    default:
                        $html .= 'no default';
                        $html .= PHP_EOL;
                        break;
                }

                $html .= '</div>';

                do_action( $key . '_below_field' );

            }

        }
        return $html;
    }


    public function buildFormHtmlLinks( $links=null, $prefix=null ){

        $links = apply_filters( $prefix . '_form_links', $links );

        if ( $links ){

            $html = null;
            foreach( $links as $key => $value ){
                 $args = wp_parse_args( $value, apply_filters( $prefix . '_link_args', array(
                    'href' => '#',
                    'class' => 'foo',
                    'title' => esc_attr( $value['text'] ),
                    'text' => esc_attr( $value['text'] ),
                    'id' => $prefix . '_' . sanitize_title( $value['text'] ),
                    'name' => $key
                    ) ) );

                $classes = array(
                    ALR_NAMESPACE . '_link',
                    $prefix . '_link',
                    $args['class']
                    );

                $html .= '<li><a href="'.$args['href'].'" class="' . implode( " ", $classes ) . '" id="'.$args['id'].'" title="'.$args['title'].'">'.$args['text'].'</a></li>';
                $html .= PHP_EOL;
            }

        } else {

            $html = null;

        }

        return '<ul class="' . ALR_NAMESPACE . '_ul_container">' . $html . '</ul>';
    }

}