<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'SOALI_OPTIONS_Field_icon' ) ) {
  class SOALI_OPTIONS_Field_icon extends SOALI_OPTIONS_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'soali_options' ),
        'remove_title' => esc_html__( 'Remove Icon', 'soali_options' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'soali_options_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="soali_options-icon-select">';
      echo '<span class="soali_options-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary soali_options-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button soali_options-warning-primary soali_options-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="soali_options-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'SOALI_OPTIONS_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'SOALI_OPTIONS_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="soali_options-modal-icon" class="soali_options-modal soali_options-modal-icon hidden">
        <div class="soali_options-modal-table">
          <div class="soali_options-modal-table-cell">
            <div class="soali_options-modal-overlay"></div>
            <div class="soali_options-modal-inner">
              <div class="soali_options-modal-title">
                <?php esc_html_e( 'Add Icon', 'soali_options' ); ?>
                <div class="soali_options-modal-close soali_options-icon-close"></div>
              </div>
              <div class="soali_options-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'soali_options' ); ?>" class="soali_options-icon-search" />
              </div>
              <div class="soali_options-modal-content">
                <div class="soali_options-modal-loading"><div class="soali_options-loading"></div></div>
                <div class="soali_options-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
