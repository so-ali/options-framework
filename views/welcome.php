<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Setup Framework Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'SOALI_OPTIONS_Welcome' ) ) {
  class SOALI_OPTIONS_Welcome{

    private static $instance = null;

    public function __construct() {

      if ( SOALI_OPTIONS::$premium && ( ! SOALI_OPTIONS::is_active_plugin( 'soali-framework/soali-framework.php' ) || apply_filters( 'soali_options_welcome_page', true ) === false ) ) { return; }

      add_action( 'admin_menu', array( $this, 'add_about_menu' ), 0 );
      add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 5 );
      add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );

      $this->set_demo_mode();

    }

    // instance
    public static function instance() {
      if ( is_null( self::$instance ) ) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    public function add_about_menu() {
      add_management_page( 'SoaliOptions Framework', 'SoaliOptions Framework', 'manage_options', 'soali_options-welcome', array( $this, 'add_page_welcome' ) );
    }

    public function add_page_welcome() {

      $section = ( ! empty( $_GET['section'] ) ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';

      SOALI_OPTIONS::include_plugin_file( 'views/header.php' );

      // safely include pages
      switch ( $section ) {

        case 'quickstart':
          SOALI_OPTIONS::include_plugin_file( 'views/quickstart.php' );
        break;

        case 'documentation':
          SOALI_OPTIONS::include_plugin_file( 'views/documentation.php' );
        break;

        case 'relnotes':
          SOALI_OPTIONS::include_plugin_file( 'views/relnotes.php' );
        break;

        case 'support':
          SOALI_OPTIONS::include_plugin_file( 'views/support.php' );
        break;

        case 'free-vs-premium':
          SOALI_OPTIONS::include_plugin_file( 'views/free-vs-premium.php' );
        break;

        default:
          SOALI_OPTIONS::include_plugin_file( 'views/about.php' );
        break;

      }

      SOALI_OPTIONS::include_plugin_file( 'views/footer.php' );

    }

    public static function add_plugin_action_links( $links, $plugin_file ) {

      if ( $plugin_file === 'soali-framework/soali-framework.php' && ! empty( $links ) ) {
        $links['soali_options--welcome'] = '<a href="'. esc_url( admin_url( 'tools.php?page=soali_options-welcome' ) ) .'">Settings</a>';
        if ( ! SOALI_OPTIONS::$premium ) {
          $links['soali_options--upgrade'] = '<a href="http://soali.me/">Upgrade</a>';
        }
      }

      return $links;

    }

    public static function add_plugin_row_meta( $links, $plugin_file ) {

      if ( $plugin_file === 'soali-framework/soali-framework.php' && ! empty( $links ) ) {
        $links['soali_options--docs'] = '<a href="http://soali.me/documentation/" target="_blank">Documentation</a>';
      }

      return $links;

    }

    public function set_demo_mode() {

      $demo_mode = get_option( 'soali_options_demo_mode', false );

      $demo_activate = ( ! empty( $_GET[ 'soali_options-demo' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'soali_options-demo' ] ) ) : '';

      if ( ! empty( $demo_activate ) ) {

        $demo_mode = ( $demo_activate === 'activate' ) ? true : false;

        update_option( 'soali_options_demo_mode', $demo_mode );

      }

      if ( ! empty( $demo_mode ) ) {

        SOALI_OPTIONS::include_plugin_file( 'samples/admin-options.php' );

        if ( SOALI_OPTIONS::$premium ) {

          SOALI_OPTIONS::include_plugin_file( 'samples/customize-options.php' );
          SOALI_OPTIONS::include_plugin_file( 'samples/metabox-options.php'   );
          SOALI_OPTIONS::include_plugin_file( 'samples/nav-menu-options.php'  );
          SOALI_OPTIONS::include_plugin_file( 'samples/profile-options.php'   );
          SOALI_OPTIONS::include_plugin_file( 'samples/shortcode-options.php' );
          SOALI_OPTIONS::include_plugin_file( 'samples/taxonomy-options.php'  );
          SOALI_OPTIONS::include_plugin_file( 'samples/widget-options.php'    );
          SOALI_OPTIONS::include_plugin_file( 'samples/comment-options.php'   );

        }

      }

    }

  }

  SOALI_OPTIONS_Welcome::instance();
}
