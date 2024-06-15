<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly. ?>

<p><strong>Let's quick start it.</strong></p>
<p>Open your current theme <code>functions.php</code> file and paste this code.</p>

<div class="soali_options-code-block">
<pre>
<span>// Control core classes for avoid errors</span>
if ( class_exists( 'SOALI_OPTIONS' ) ) {

  <span>//</span>
  <span>// Set a unique slug-like ID</span>
  $prefix = 'my_framework';

  <span>//</span>
  <span>// Create options</span>
  SOALI_OPTIONS::createOptions( $prefix, array(
    'menu_title' => 'My Framework',
    'menu_slug'  => 'my-framework',
  ) );

  <span>//</span>
  <span>// Create a section</span>
  SOALI_OPTIONS::createSection( $prefix, array(
    'title'  => 'Tab Title 1',
    'fields' => array(

      <span>//</span>
      <span>// A text field</span>
      array(
        'id'    => 'opt-text',
        'type'  => 'text',
        'title' => 'Simple Text',
      ),

    )
  ) );

  SOALI_OPTIONS::createSection( $prefix, array(
    'title'  => 'Tab Title 2',
    'fields' => array(

      array(
        'id'    => 'opt-textarea',
        'type'  => 'textarea',
        'title' => 'Simple Textarea',
      ),

    )
  ) );

}
</pre>
</div>

<p><strong>How to get option value ?</strong></p>

<div class="soali_options-code-block">
<pre>
$options = get_option( 'my_framework' ); <span>// // unique id of the framework</span>

echo $options['opt-text']; <span>// id of field</span>
echo $options['opt-textarea']; <span>// id of field</span>
</pre>
</div>

<p><a href="http://soali.me/documentation/" class="button" target="_blank" rel="nofollow"><i class="fas fa-book"></i> Online Documentation</a></p>
