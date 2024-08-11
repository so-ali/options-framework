<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.

  $demo    = get_option( 'soali_options_demo_mode', false );
  $text    = ( ! empty( $demo ) ) ? 'Deactivate' : 'Activate';
  $status  = ( ! empty( $demo ) ) ? 'deactivate' : 'activate';
  $class   = ( ! empty( $demo ) ) ? ' soali_options-warning-primary' : '';
  $section = ( ! empty( $_GET[ 'section' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'section' ] ) ) : 'about';
  $links   = array(
    'about'           => 'About',
    'quickstart'      => 'Quick Start',
    'documentation'   => 'Documentation',
    'free-vs-premium' => 'Free vs Premium',
    'support'         => 'Support',
    'relnotes'        => 'Release Notes',
  );

?>
<div class="soali_options-welcome soali_options-welcome-wrap">

  <h1>Welcome to SoaliOptions Framework v<?php echo esc_attr( SOALI_OPTIONS::$version ); ?></h1>

  <p class="soali_options-about-text">A Simple and Lightweight WordPress Option Framework for Themes and Plugins</p>

  <p class="soali_options-demo-button"><a href="<?php echo esc_url( add_query_arg( array( 'soali_options-demo' => $status ) ) ); ?>" class="button button-primary<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $text ); ?> Demo</a></p>

  <div class="soali_options-logo">
    <div class="soali_options--effects"><i></i><i></i><i></i><i></i></div>
    <div class="soali_options--wp-logos">
      <div class="soali_options--wp-logo"></div>
      <div class="soali_options--wp-plugin-logo"></div>
    </div>
    <div class="soali_options--text">SoaliOptions Framework</div>
    <div class="soali_options--text soali_options--version">v<?php echo esc_attr( SOALI_OPTIONS::$version ); ?></div>
  </div>

  <h2 class="nav-tab-wrapper wp-clearfix">
    <?php

      foreach ( $links as $key => $link ) {

        if ( SOALI_OPTIONS::$premium && $key === 'free-vs-premium' ) { continue; }

        $activate = ( $section === $key ) ? ' nav-tab-active' : '';

        echo '<a href="'. esc_url( add_query_arg( array( 'page' => 'soali_options-welcome', 'section' => $key ), admin_url( 'tools.php' ) ) ) .'" class="nav-tab'. esc_attr( $activate ) .'">'. esc_attr( $link ) .'</a>';

      }

    ?>
  </h2>
