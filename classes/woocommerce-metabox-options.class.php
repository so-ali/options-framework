<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.
/**
 *
 * Woocommerce_Metabox Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (!class_exists('SOALI_OPTIONS_Woocommerce_Metabox')) {
    class SOALI_OPTIONS_Woocommerce_Metabox extends SOALI_OPTIONS_Abstract
    {

        // constans
        public $unique = '';
        public $abstract = 'woocommerce_metabox';
        public $pre_fields = array();
        public $sections = array();
        public $post_type = array();
        public $args = array(
            'title' => '',
            'post_type' => 'product',
            'data_type' => '',
            'context' => 'advanced',
            'priority' => 200,
            'page_templates' => '',
            'post_formats' => '',
            'show_reset' => false,
            'show_restore' => false,
            'enqueue_webfont' => true,
            'async_webfont' => false,
            'output_css' => true,
            'nav' => 'normal',
            'theme' => 'light',
            'class' => '',
            'defaults' => array(),
            'product_type' => array()
        );

        // run metabox construct
        public function __construct($key, $params = array())
        {

            $this->unique = $key;
            $this->args = apply_filters("soali_options_{$this->unique}_args", wp_parse_args($params['args'], $this->args), $this);
            $this->sections = apply_filters("soali_options_{$this->unique}_sections", $params['sections'], $this);
            $this->post_type = (is_array($this->args['post_type'])) ? $this->args['post_type'] : array_filter((array)$this->args['post_type']);
            $this->post_formats = (is_array($this->args['post_formats'])) ? $this->args['post_formats'] : array_filter((array)$this->args['post_formats']);
            $this->page_templates = (is_array($this->args['page_templates'])) ? $this->args['page_templates'] : array_filter((array)$this->args['page_templates']);
            $this->pre_fields = $this->pre_fields($this->sections);

            add_filter('woocommerce_product_data_tabs', array(&$this, 'add_meta_box'));
            add_action('woocommerce_product_options_' . $key, array(&$this, 'add_meta_box_content'));
            add_action('woocommerce_process_product_meta', array(&$this, 'save_meta_box'));

            if (!empty($this->page_templates) || !empty($this->post_formats) || !empty($this->args['class'])) {
                foreach ($this->post_type as $post_type) {
                    add_filter('postbox_classes_' . $post_type . '_' . $this->unique, array(&$this, 'add_metabox_classes'));
                }
            }

            // wp enqeueu for typography and output css
            parent::__construct();

        }

        // instance
        public static function instance($key, $params = array())
        {
            return new self($key, $params);
        }

        public function pre_fields($sections)
        {

            $result = array();

            foreach ($sections as $key => $section) {
                if (!empty($section['fields'])) {
                    foreach ($section['fields'] as $field) {
                        $result[] = $field;
                    }
                }
            }

            return $result;

        }

        public function add_metabox_classes($classes)
        {

            global $post;

            if (!empty($this->post_formats)) {

                $saved_post_format = (is_object($post)) ? get_post_format($post) : false;
                $saved_post_format = (!empty($saved_post_format)) ? $saved_post_format : 'default';

                $classes[] = 'soali_options-post-formats';

                // Sanitize post format for standard to default
                if (($key = array_search('standard', $this->post_formats)) !== false) {
                    $this->post_formats[$key] = 'default';
                }

                foreach ($this->post_formats as $format) {
                    $classes[] = 'soali_options-post-format-' . $format;
                }

                if (!in_array($saved_post_format, $this->post_formats)) {
                    $classes[] = 'soali_options-metabox-hide';
                } else {
                    $classes[] = 'soali_options-metabox-show';
                }

            }

            if (!empty($this->page_templates)) {

                $saved_template = (is_object($post) && !empty($post->page_template)) ? $post->page_template : 'default';

                $classes[] = 'soali_options-page-templates';

                foreach ($this->page_templates as $template) {
                    $classes[] = 'soali_options-page-' . preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($template));
                }

                if (!in_array($saved_template, $this->page_templates)) {
                    $classes[] = 'soali_options-metabox-hide';
                } else {
                    $classes[] = 'soali_options-metabox-show';
                }

            }

            if (!empty($this->args['class'])) {
                $classes[] = $this->args['class'];
            }

            return $classes;

        }

        // add metabox
        public function add_meta_box($tabs): array
        {
            if (isset($tabs[$this->unique])) {
                return $tabs;
            }

            foreach ($tabs as $tab) {
                if ($tab['target'] === $this->unique) {
                    return $tabs;
                }
            }

            if (!empty($this->args['product_type']) && is_string($this->args['product_type'])) {
                $this->args['product_type'] = [$this->args['product_type']];
            }
            if (!empty($this->args['product_type']) && is_array($this->args['product_type'])) {
                foreach ($this->args['product_type'] as $type){
                    $this->args['class'] .= sprintf(' show_if_%s', $type);
                }
            }

            $tabs[$this->unique] = array(
                'label' => $this->args['title'],
                'target' => $this->unique,
                'class' => array($this->args['class']),
                'priority' => $this->args['priority'],
            );

            add_action('woocommerce_product_data_panels', function () {
                $this->add_meta_box_content($this->unique);
            });

            return $tabs;
        }

        // get default value
        public function get_default($field)
        {

            $default = (isset($field['default'])) ? $field['default'] : '';
            $default = (isset($this->args['defaults'][$field['id']])) ? $this->args['defaults'][$field['id']] : $default;

            return $default;

        }

        // get meta value
        public function get_meta_value($field)
        {

            global $post;

            $value = null;

            if (is_object($post) && !empty($field['id'])) {

                if ($this->args['data_type'] !== 'serialize') {
                    $meta = get_post_meta($post->ID, $field['id']);
                    $value = (isset($meta[0])) ? $meta[0] : null;
                } else {
                    $meta = get_post_meta($post->ID, $this->unique, true);
                    $value = (isset($meta[$field['id']])) ? $meta[$field['id']] : null;
                }

            }

            $default = (isset($field['id'])) ? $this->get_default($field) : '';
            $value = (isset($value)) ? $value : $default;

            return $value;

        }

        // add metabox content
        public function add_meta_box_content($tab = '')
        {
            if (!empty($tab)) {
                printf('<div class="panel woocommerce_options_panel hidden" id="%s">', esc_attr($tab));
            }
            global $post;

            $has_nav = (count($this->sections) > 1) ? true : false;
            $show_all = (!$has_nav) ? ' soali_options-show-all' : '';
            $post_type = (is_object($post)) ? $post->post_type : '';
            $errors = (is_object($post)) ? get_post_meta($post->ID, '_soali_options_errors_' . $this->unique, true) : array();
            $errors = (!empty($errors)) ? $errors : array();
            $theme = ($this->args['theme']) ? ' soali_options-theme-' . $this->args['theme'] : '';
            $nav_type = ($this->args['nav'] === 'inline') ? 'inline' : 'normal';

            if (is_object($post) && !empty($errors)) {
                delete_post_meta($post->ID, '_soali_options_errors_' . $this->unique);
            }

            wp_nonce_field('soali_options_metabox_nonce', 'soali_options_metabox_nonce' . $this->unique);

            echo '<div class="soali_options soali_options-metabox soali_options-woocommerce-metabox' . esc_attr($theme) . '">';

            echo '<div class="soali_options-wrapper' . esc_attr($show_all) . '">';

            if ($has_nav) {

                echo '<div class="soali_options-nav soali_options-nav-' . esc_attr($nav_type) . ' soali_options-nav-metabox">';

                echo '<ul>';

                $tab_key = 0;

                foreach ($this->sections as $section) {

                    if (!empty($section['post_type']) && !in_array($post_type, array_filter((array)$section['post_type']))) {
                        continue;
                    }

                    $tab_error = (!empty($errors['sections'][$tab_key])) ? '<i class="soali_options-label-error soali_options-error">!</i>' : '';
                    $tab_icon = (!empty($section['icon'])) ? '<i class="soali_options-tab-icon ' . esc_attr($section['icon']) . '"></i>' : '';

                    echo '<li><a href="#">' . $tab_icon . $section['title'] . $tab_error . '</a></li>';

                    $tab_key++;

                }

                echo '</ul>';

                echo '</div>';

            }

            echo '<div class="soali_options-content">';

            echo '<div class="soali_options-sections">';

            $section_key = 0;

            foreach ($this->sections as $section) {
                $section_onload = (!$has_nav) ? ' soali_options-onload' : '';
                $section_class = (!empty($section['class'])) ? ' ' . $section['class'] : '';
                $section_title = (!empty($section['title'])) ? $section['title'] : '';
                $section_icon = (!empty($section['icon'])) ? '<i class="soali_options-section-icon ' . esc_attr($section['icon']) . '"></i>' : '';

                echo '<div class="soali_options-section hidden' . esc_attr($section_onload . $section_class) . '">';

                echo ($section_title || $section_icon) ? '<div class="soali_options-section-title"><h3>' . $section_icon . $section_title . '</h3></div>' : '';

                if (!empty($section['fields'])) {

                    foreach ($section['fields'] as $field) {

                        if (!empty($field['id']) && !empty($errors['fields'][$field['id']])) {
                            $field['_error'] = $errors['fields'][$field['id']];
                        }

                        if (!empty($field['id'])) {
                            $field['default'] = $this->get_default($field);
                        }

                        SOALI_OPTIONS::field($field, $this->get_meta_value($field), $this->unique, 'metabox');

                    }

                } else {

                    echo '<div class="soali_options-no-option">' . esc_html__('No data available.', 'soali_options') . '</div>';

                }

                echo '</div>';

                $section_key++;

            }

            echo '</div>';

            if (!empty($this->args['show_restore']) || !empty($this->args['show_reset'])) {

                echo '<div class="soali_options-sections-reset">';
                echo '<label>';
                echo '<input type="checkbox" name="' . esc_attr($this->unique) . '[_reset]" />';
                echo '<span class="button soali_options-button-reset">' . esc_html__('Reset', 'soali_options') . '</span>';
                echo '<span class="button soali_options-button-cancel">' . sprintf('<small>( %s )</small> %s', esc_html__('update post', 'soali_options'), esc_html__('Cancel', 'soali_options')) . '</span>';
                echo '</label>';
                echo '</div>';

            }

            echo '</div>';

            echo ($has_nav && $nav_type === 'normal') ? '<div class="soali_options-nav-background"></div>' : '';

            echo '<div class="clear"></div>';

            echo '</div>';

            echo '</div>';


            if (!empty($tab)) {
                echo '</div>';
            }

        }

        // save metabox
        public function save_meta_box($post_id)
        {

            $count = 1;
            $data = array();
            $errors = array();
            $noncekey = 'soali_options_metabox_nonce' . $this->unique;
            $nonce = (!empty($_POST[$noncekey])) ? sanitize_text_field(wp_unslash($_POST[$noncekey])) : '';

            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !wp_verify_nonce($nonce, 'soali_options_metabox_nonce')) {
                return $post_id;
            }

            // XSS ok.
            // No worries, This "POST" requests is sanitizing in the below foreach.
            $request = (!empty($_POST[$this->unique])) ? $_POST[$this->unique] : array();

            if (!empty($request)) {

                foreach ($this->sections as $section) {

                    if (!empty($section['fields'])) {

                        foreach ($section['fields'] as $field) {

                            if (!empty($field['id'])) {

                                $field_id = $field['id'];
                                $field_value = isset($request[$field_id]) ? $request[$field_id] : '';

                                // Sanitize "post" request of field.
                                if (!isset($field['sanitize'])) {

                                    if (is_array($field_value)) {
                                        $data[$field_id] = wp_kses_post_deep($field_value);
                                    } else {
                                        $data[$field_id] = wp_kses_post($field_value);
                                    }

                                } else if (isset($field['sanitize']) && is_callable($field['sanitize'])) {

                                    $data[$field_id] = call_user_func($field['sanitize'], $field_value);

                                } else {

                                    $data[$field_id] = $field_value;

                                }

                                // Validate "post" request of field.
                                if (isset($field['validate']) && is_callable($field['validate'])) {

                                    $has_validated = call_user_func($field['validate'], $field_value);

                                    if (!empty($has_validated)) {

                                        $errors['sections'][$count] = true;
                                        $errors['fields'][$field_id] = $has_validated;
                                        $data[$field_id] = $this->get_meta_value($field);

                                    }

                                }

                            }

                        }

                    }

                    $count++;

                }

            }

            $data = apply_filters("soali_options_{$this->unique}_save", $data, $post_id, $this);

            do_action("soali_options_{$this->unique}_save_before", $data, $post_id, $this);

            if (empty($data) || !empty($request['_reset'])) {

                if ($this->args['data_type'] !== 'serialize') {
                    foreach ($data as $key => $value) {
                        delete_post_meta($post_id, $key);
                    }
                } else {
                    delete_post_meta($post_id, $this->unique);
                }

            } else {

                if ($this->args['data_type'] !== 'serialize') {
                    foreach ($data as $key => $value) {
                        update_post_meta($post_id, $key, $value);
                    }
                } else {
                    update_post_meta($post_id, $this->unique, $data);
                }

                if (!empty($errors)) {
                    update_post_meta($post_id, '_soali_options_errors_' . $this->unique, $errors);
                }

            }

            do_action("soali_options_{$this->unique}_saved", $data, $post_id, $this);

            do_action("soali_options_{$this->unique}_save_after", $data, $post_id, $this);

        }
    }
}
