<?php
/**
 * The admin-specific functionality of the plugin.
 */
class Smart_Scroll_To_Top_Admin {
    /**
     * Add admin menu
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            __('Scroll to Top Settings', 'smart-scroll-to-top'),
            __('Scroll to Top', 'smart-scroll-to-top'),
            'manage_options',
            'smart-scroll-to-top',
            array($this, 'display_plugin_admin_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('sstt_options', 'sstt_options', array($this, 'validate_options'));
        add_settings_section('sstt_general_section', __('General Settings', 'smart-scroll-to-top'), null, 'smart-scroll-to-top');
        $this->add_settings_field('enable', __('Enable Button', 'smart-scroll-to-top'), 'checkbox');
        $this->add_settings_field('output_mode', __('Button Output', 'smart-scroll-to-top'), 'select', array(
            'auto' => __('Automatic (Footer)', 'smart-scroll-to-top'),
            'shortcode' => __('Shortcode Only', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('button_position', __('Button Position', 'smart-scroll-to-top'), 'select', array(
            'Bottom Right' => __('Bottom Right', 'smart-scroll-to-top'),
            'Bottom Left' => __('Bottom Left', 'smart-scroll-to-top'),
            'Top Right' => __('Top Right', 'smart-scroll-to-top'),
            'Top Left' => __('Top Left', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('icon_style', __('Icon Style', 'smart-scroll-to-top'), 'select', array(
            'arrow' => __('Arrow', 'smart-scroll-to-top'),
            'chevron' => __('Chevron', 'smart-scroll-to-top'),
            'circle' => __('Circle', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('icon_type', __('Icon Type', 'smart-scroll-to-top'), 'select', array(
            'fontawesome' => __('Font Awesome (CSS Only)', 'smart-scroll-to-top'),
            'bootstrap' => __('Bootstrap (CSS Only)', 'smart-scroll-to-top'),
            'dashicons' => __('Dashicons', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('background_color', __('Background Color', 'smart-scroll-to-top'), 'color');
        $this->add_settings_field('icon_color', __('Icon Color', 'smart-scroll-to-top'), 'color');
        $this->add_settings_field('hover_color', __('Hover Color', 'smart-scroll-to-top'), 'color');
        $this->add_settings_field('button_size', __('Button Size', 'smart-scroll-to-top'), 'select', array(
            'Small' => __('Small', 'smart-scroll-to-top'),
            'Medium' => __('Medium', 'smart-scroll-to-top'),
            'Large' => __('Large', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('border_radius', __('Border Radius (px)', 'smart-scroll-to-top'), 'number');
        $this->add_settings_field('shadow', __('Enable Shadow', 'smart-scroll-to-top'), 'checkbox');
        $this->add_settings_field('animation_type', __('Animation Type', 'smart-scroll-to-top'), 'select', array(
            'fade' => __('Fade', 'smart-scroll-to-top'),
            'slide' => __('Slide', 'smart-scroll-to-top'),
            'zoom' => __('Zoom', 'smart-scroll-to-top'),
            'bounce' => __('Bounce', 'smart-scroll-to-top')
        ));
        $this->add_settings_field('scroll_offset', __('Scroll Offset (px)', 'smart-scroll-to-top'), 'number');
        $this->add_settings_field('scroll_speed', __('Scroll Speed (ms)', 'smart-scroll-to-top'), 'number');
    }

    /**
     * Add a settings field
     */
    private function add_settings_field($id, $title, $type, $options = array()) {
        add_settings_field(
            'sstt_' . $id,
            $title,
            array($this, 'render_field'),
            'smart-scroll-to-top',
            'sstt_general_section',
            array(
                'id' => $id,
                'type' => $type,
                'options' => $options
            )
        );
    }

    /**
     * Render a settings field
     */
    public function render_field($args) {
        $options = get_option('sstt_options');
        $defaults = (new Smooth_Scroll_To_Top())->get_default_options();
        $value = isset($options[$args['id']]) && $options[$args['id']] !== '' ? $options[$args['id']] : (isset($defaults[$args['id']]) ? $defaults[$args['id']] : '');
        switch ($args['type']) {
            case 'checkbox':
                printf('<input type="checkbox" id="sstt_%s" name="sstt_options[%s]" value="1" %s />', esc_attr($args['id']), esc_attr($args['id']), checked(1, $value, false));
                break;
            case 'select':
                printf('<select id="sstt_%s" name="sstt_options[%s]">', esc_attr($args['id']), esc_attr($args['id']));
                foreach ($args['options'] as $key => $label) {
                    printf('<option value="%s" %s>%s</option>', esc_attr($key), selected($key, $value, false), esc_html($label));
                }
                echo '</select>';
                break;
            case 'color':
                printf('<input type="color" id="sstt_%s" name="sstt_options[%s]" value="%s" />', esc_attr($args['id']), esc_attr($args['id']), esc_attr($value));
                break;
            case 'number':
                printf('<input type="number" id="sstt_%s" name="sstt_options[%s]" value="%s" min="0" />', esc_attr($args['id']), esc_attr($args['id']), esc_attr($value));
                break;
        }
    }

    /**
     * Display admin page
     */
    public function display_plugin_admin_page() {
        if (!current_user_can('manage_options')) return;
        if (isset($_GET['settings-updated'])) {
            add_settings_error('sstt_messages', 'sstt_message', __('Settings Saved', 'smart-scroll-to-top'), 'updated');
        }
        settings_errors('sstt_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('sstt_options');
                do_settings_sections('smart-scroll-to-top');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Validate options
     */
    public function validate_options($input) {
        $output = array();
        $output['enable'] = isset($input['enable']) ? (bool) $input['enable'] : false;
        $output['output_mode'] = in_array($input['output_mode'], array('auto', 'shortcode')) ? $input['output_mode'] : 'auto';
        $valid_positions = array('Bottom Right', 'Bottom Left', 'Top Right', 'Top Left');
        $output['button_position'] = in_array($input['button_position'], $valid_positions) ? $input['button_position'] : 'Bottom Right';
        $valid_styles = array('arrow', 'chevron', 'circle');
        $output['icon_style'] = in_array($input['icon_style'], $valid_styles) ? $input['icon_style'] : 'arrow';
        $valid_types = array('fontawesome', 'bootstrap', 'dashicons');
        $output['icon_type'] = in_array($input['icon_type'], $valid_types) ? $input['icon_type'] : 'fontawesome';
        $output['background_color'] = sanitize_hex_color($input['background_color']);
        $output['icon_color'] = sanitize_hex_color($input['icon_color']);
        $output['hover_color'] = sanitize_hex_color($input['hover_color']);
        $valid_sizes = array('Small', 'Medium', 'Large');
        $output['button_size'] = in_array($input['button_size'], $valid_sizes) ? $input['button_size'] : 'Medium';
        $output['border_radius'] = absint($input['border_radius']);
        $output['shadow'] = isset($input['shadow']) ? (bool) $input['shadow'] : false;
        $valid_animations = array('fade', 'slide', 'zoom', 'bounce');
        $output['animation_type'] = in_array($input['animation_type'], $valid_animations) ? $input['animation_type'] : 'fade';
        $output['scroll_offset'] = absint($input['scroll_offset']);
        $output['scroll_speed'] = absint($input['scroll_speed']);
        return $output;
    }
} 