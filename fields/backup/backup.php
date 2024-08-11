<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'SOALI_OPTIONS_Field_backup' ) ) {
  class SOALI_OPTIONS_Field_backup extends SOALI_OPTIONS_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'soali_options_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'soali_options-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      echo '<textarea name="soali_options_import_data" class="soali_options-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary soali_options-confirm soali_options-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'soali_options' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="soali_options-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary soali_options-export" target="_blank">'. esc_html__( 'Export & Download', 'soali_options' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="soali_options_transient[reset]" value="reset" class="button soali_options-warning-primary soali_options-confirm soali_options-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'soali_options' ) .'</button>';

      echo $this->field_after();

    }

  }
}
