<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Custom Form Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'SOALI_OPTIONS_CUSTOM_FORM' ) ) {
  class SOALI_OPTIONS_CUSTOM_FORM extends SOALI_OPTIONS_Abstract{

    // constans
    public $unique         = '';
    public $abstract       = 'custom_form';
    public $pre_fields     = array();
    public $sections       = array();
    public $args           = array(
      'title'              => '',
      'data_type'          => '',
      'context'            => 'advanced',
      'priority'           => 'default',
      'show_reset'         => false,
      'show_restore'       => false,
      'enqueue_webfont'    => true,
      'async_webfont'      => false,
      'output_css'         => true,
      'nav'                => 'normal',
      'theme'              => 'light',
      'class'              => '',
      'defaults'           => array(),
    );

    // run custom form options construct.
    public function __construct( $key, $params = array() ) {

      $this->unique         = $key;
      $this->args           = apply_filters( "soali_options_{$this->unique}_args", wp_parse_args( $params['args'], $this->args ), $this );
      $this->sections       = apply_filters( "soali_options_{$this->unique}_sections", $params['sections'], $this );
      $this->pre_fields     = $this->pre_fields( $this->sections );
      add_shortcode('custom_options_form_' . $this->unique,[$this,'form_content']);

      parent::__construct();
    }

    // instance
    public static function instance( $key, $params = array() ) {
      return new self( $key, $params );
    }

    public function pre_fields( $sections ) {

      $result  = array();

      foreach ( $sections as $key => $section ) {
        if ( ! empty( $section['fields'] ) ) {
          foreach ( $section['fields'] as $field ) {
            $result[] = $field;
          }
        }
      }

      return $result;
    }

    // get default value
    public function get_default( $field ) {

      $default = ( isset( $field['default'] ) ) ? $field['default'] : '';
      $default = ( isset( $this->args['defaults'][$field['id']] ) ) ? $this->args['defaults'][$field['id']] : $default;

      return $default;

    }

    // get meta value
    public function get_meta_value( $field ) {

      $default = ( isset( $field['id'] ) ) ? $this->get_default( $field ) : '';
      $value   = get_option( $this->unique . '_' . $field['id'], $default);

      return $value;

    }

    // add custom form content
    public function form_content() {
      $has_nav   = ( count( $this->sections ) > 1 && $this->args['context'] !== 'side' ) ? true : false;
      $show_all  = ( ! $has_nav ) ? ' soali_options-show-all' : '';
      $errors    = get_option( 'custom_form_soali_options_errors_'. $this->unique, [] );
      $errors    = ( ! empty( $errors ) ) ? $errors : array();
      $theme     = ( $this->args['theme'] ) ? ' soali_options-theme-'. $this->args['theme'] : '';
      $nav_type  = ( $this->args['nav'] === 'inline' ) ? 'inline' : 'normal';

      echo '<form method="post">';

      $this->save_form();

      if (! empty( $errors ) ) {
        delete_option( 'custom_form_soali_options_errors_'. $this->unique );
      }

      wp_nonce_field( 'soali_options_custom_form_nonce', 'soali_options_custom_form_nonce'. $this->unique );

      echo '<div class="soali_options soali_options-custom_form'. esc_attr( $theme ) .'">';

        echo '<div class="soali_options-wrapper'. esc_attr( $show_all ) .'">';

          if ( $has_nav ) {

            echo '<div class="soali_options-nav soali_options-nav-'. esc_attr( $nav_type ) .' soali_options-nav-custom_form">';

              echo '<ul>';

              $tab_key = 0;

              foreach ( $this->sections as $section ) {
                $tab_error = ( ! empty( $errors['sections'][$tab_key] ) ) ? '<i class="soali_options-label-error soali_options-error">!</i>' : '';
                $tab_icon  = ( ! empty( $section['icon'] ) ) ? '<i class="soali_options-tab-icon '. esc_attr( $section['icon'] ) .'"></i>' : '';

                echo '<li><a href="#">'. $tab_icon . $section['title'] . $tab_error .'</a></li>';

                $tab_key++;

              }

              echo '</ul>';

            echo '</div>';

          }

          echo '<div class="soali_options-content">';

            echo '<div class="soali_options-sections">';

            $section_key = 0;

            foreach ( $this->sections as $section ) {

              $section_onload = ( ! $has_nav ) ? ' soali_options-onload' : '';
              $section_class  = ( ! empty( $section['class'] ) ) ? ' '. $section['class'] : '';
              $section_title  = ( ! empty( $section['title'] ) ) ? $section['title'] : '';
              $section_icon   = ( ! empty( $section['icon'] ) ) ? '<i class="soali_options-section-icon '. esc_attr( $section['icon'] ) .'"></i>' : '';

              echo '<div class="soali_options-section hidden'. esc_attr( $section_onload . $section_class ) .'">';

              echo ( $section_title || $section_icon ) ? '<div class="soali_options-section-title"><h3>'. $section_icon . $section_title .'</h3></div>' : '';

              if ( ! empty( $section['fields'] ) ) {

                foreach ( $section['fields'] as $field ) {

                  if ( ! empty( $field['id'] ) && ! empty( $errors['fields'][$field['id']] ) ) {
                    $field['_error'] = $errors['fields'][$field['id']];
                  }

                  if ( ! empty( $field['id'] ) ) {
                    $field['default'] = $this->get_default( $field );
                  }

                  SOALI_OPTIONS::field( $field, $this->get_meta_value( $field ), $this->unique, 'custom_form' );

                }

              } else {

                echo '<div class="soali_options-no-option">'. esc_html__( 'No data available.', 'soali_options' ) .'</div>';

              }

              echo '</div>';

              $section_key++;

            }

            echo '</div>';

            if ( ! empty( $this->args['show_restore'] ) || ! empty( $this->args['show_reset'] ) ) {

              echo '<div class="soali_options-sections-reset">';
              echo '<label>';
              echo '<input type="checkbox" name="'. esc_attr( $this->unique ) .'[_reset]" />';
              echo '<span class="button soali_options-button-reset">'. esc_html__( 'Reset', 'soali_options' ) .'</span>';
              echo '<span class="button soali_options-button-cancel">'. sprintf( '<small>( %s )</small> %s', esc_html__( 'update post', 'soali_options' ), esc_html__( 'Cancel', 'soali_options' ) ) .'</span>';
              echo '</label>';
              echo '</div>';

            }

          echo '</div>';

          echo ( $has_nav && $nav_type === 'normal' ) ? '<div class="soali_options-nav-background"></div>' : '';

          echo '<div class="clear"></div>';

        echo '</div>';

      echo '</div>';

      echo '<button type="submit">Save</button>';

      echo '</form>';
    }

    // save custom_form
    public function save_form() {

      $count    = 1;
      $data     = array();
      $errors   = array();
      $noncekey = 'soali_options_custom_form_nonce'. $this->unique;
      $nonce    = ( ! empty( $_POST[ $noncekey ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $noncekey ] ) ) : '';

      if ( ! wp_verify_nonce( $nonce, 'soali_options_custom_form_nonce' ) ) {
        return;
      }

      // XSS ok.
      // No worries, This "POST" requests is sanitizing in the below foreach.
      $request = ( ! empty( $_POST[ $this->unique ] ) ) ? $_POST[ $this->unique ] : array();

      if ( ! empty( $request ) ) {

        foreach ( $this->sections as $section ) {

          if ( ! empty( $section['fields'] ) ) {

            foreach ( $section['fields'] as $field ) {

              if ( ! empty( $field['id'] ) ) {

                $field_id    = $field['id'];
                $field_value = isset( $request[$field_id] ) ? $request[$field_id] : '';

                // Sanitize "post" request of field.
                if ( ! isset( $field['sanitize'] ) ) {

                  if( is_array( $field_value ) ) {
                    $data[$field_id] = wp_kses_post_deep( $field_value );
                  } else {
                    $data[$field_id] = wp_kses_post( $field_value );
                  }

                } else if( isset( $field['sanitize'] ) && is_callable( $field['sanitize'] ) ) {

                  $data[$field_id] = call_user_func( $field['sanitize'], $field_value );

                } else {

                  $data[$field_id] = $field_value;

                }

                // Validate "post" request of field.
                if ( isset( $field['validate'] ) && is_callable( $field['validate'] ) ) {

                  $has_validated = call_user_func( $field['validate'], $field_value );

                  if ( ! empty( $has_validated ) ) {

                    $errors['sections'][$count] = true;
                    $errors['fields'][$field_id] = $has_validated;
                    $data[$field_id] = $this->get_meta_value( $field );

                  }

                }

              }

            }

          }

          $count++;

        }

      }

      $data = apply_filters( "soali_options_{$this->unique}_save", $data, $this );

      do_action( "soali_options_{$this->unique}_save_before", $data, $this );

      if ( empty( $data ) || ! empty( $request['_reset'] ) ) {

        if ( $this->args['data_type'] !== 'serialize' ) {
          foreach ( $data as $key => $value ) {
            delete_option( $this->unique . '_' . $key );
          }
        } else {
            delete_option( $this->unique );
        }

      } else {

        if ( $this->args['data_type'] !== 'serialize' ) {
          foreach ( $data as $key => $value ) {
              update_option( $this->unique . '_' . $key,$value );
          }
        } else {
            update_option( $this->unique, $data );
        }

        if ( ! empty( $errors ) ) {
            update_option( 'custom_form_soali_options_errors_'. $this->unique, $errors );
        }

      }

      do_action( "soali_options_{$this->unique}_saved", $data, $this );

      do_action( "soali_options_{$this->unique}_save_after", $data, $this );

    }
  }
}
