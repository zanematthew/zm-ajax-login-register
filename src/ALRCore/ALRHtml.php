<?php

Class ALRHtml {

    public function buildFormFieldsHtml( $fields=null, $prefix=null, $order=null ){

        // Use this filter to add additional classes
        $default_classes = apply_filters( $prefix . '_form_classes', array() );
        $html = null;

        foreach( $order as $key ){

            // Key specific filter?
            // $field = apply_filters( $prefix . '_filter_field_' . $key, $fields[ $key ] );

            if ( empty( $fields[ $key ] ) ){

                $html .= "invalid key {$key} added for order<br />";
                $html .= PHP_EOL;

            } else {

                do_action( $key . '_above_field' );

                $args = wp_parse_args( $fields[ $key ], array(
                    'extra' => null,
                    'required' => null,
                    'size' => null,
                    'name' => $key,
                    'id' => $prefix . '_' . sanitize_title( $key ),
                    'class' => ALR_NAMESPACE . '_' . esc_attr( $fields[ $key ]['type'] ) . '_field',
                    'placeholder' => esc_attr( $fields[ $key ]['title'] ),
                    'type' => esc_attr( $fields[ $key ]['type'] ),
                    'html' => null
                    ) );

                $classes = array_merge( $default_classes, array(
                    'noon',
                    ALR_NAMESPACE . '_' . $args['type'] . '_container',
                    $prefix . '_' . $args['type'] . '_container',
                    $args['class'] . '_container'
                    ) );

                $html .= '<div class="' . implode( " ", $classes ) . '">';

                switch ( $fields[ $key ]['type'] ) {

                    case 'text':
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= '<input type="text" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'password':
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= '<input type="password" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'email':
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= '<input autocorrect="none" autocapitalize="none" type="email" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'checkbox':
                        $html .= '<input type="checkbox" name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" ' . $args['extra'] . ' />';
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= PHP_EOL;
                        break;

                    case 'html':
                        $html .= $args['html'];
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

        if ( $links ){

            $html = null;
            foreach( $links as $key => $value ){
                 $args = wp_parse_args( $value, array(
                    'href' => '#',
                    'class' => 'foo',
                    'title' => esc_attr( $value['text'] ),
                    'text' => esc_attr( $value['text'] ),
                    'id' => $prefix . '_' . sanitize_title( $value['text'] )
                    ) );

                $classes = array(
                    'noon',
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