<?php
/**
 * The main plugin class
 */
class Smart_Scroll_To_Top {
    /**
     * Plugin options
     */
    protected $options;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        $this->options = get_option('sstt_options', $this->get_default_options());
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Define admin hooks
     */
    private function define_admin_hooks() {
        $admin = new Smart_Scroll_To_Top_Admin();
        add_action('admin_menu', array($admin, 'add_plugin_admin_menu'));
        add_action('admin_init', array($admin, 'register_settings'));
    }

    /**
     * Define public hooks
     */
    private function define_public_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        // Only add to footer if auto_output is enabled
        if (!isset($this->options['output_mode']) || $this->options['output_mode'] === 'auto') {
            add_action('wp_footer', array($this, 'render_scroll_button'));
        }
        add_shortcode('scroll_to_top', array($this, 'scroll_to_top_shortcode'));
    }

    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('smart-scroll-to-top', false, dirname(plugin_basename(__FILE__)) . '/../languages/');
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        if (empty($this->options['enable'])) {
            return;
        }
        // Enqueue plugin styles
        wp_enqueue_style(
            'smooth-scroll-to-top',
            SSTT_PLUGIN_URL . 'assets/css/smooth-scroll-to-top.css',
            array(),
            SSTT_VERSION
        );

        // Enqueue plugin scripts
        wp_enqueue_script(
            'smooth-scroll-to-top',
            SSTT_PLUGIN_URL . 'assets/js/smooth-scroll-to-top.js',
            array('jquery'),
            SSTT_VERSION,
            true
        );

        // Localize script with options
        wp_localize_script('smooth-scroll-to-top', 'ssttOptions', array(
            'offset' => $this->options['scroll_offset'],
            'speed' => $this->options['scroll_speed'],
            'animation' => $this->options['animation_type'],
            'background_color' => $this->options['background_color'],
            'hover_color' => $this->options['hover_color']
        ));
    }

    /**
     * Render scroll button
     */
    public function render_scroll_button() {
        if (empty($this->options['enable'])) {
            return;
        }
        $icon_class = $this->get_icon_class();
        $position_class = 'sstt-' . strtolower(str_replace(' ', '-', $this->options['button_position']));
        $size_class = 'sstt-' . strtolower($this->options['button_size']);
        $shadow_class = $this->options['shadow'] ? 'sstt-shadow' : '';
        $animation_class = 'sstt-' . strtolower($this->options['animation_type']);

        $style = sprintf(
            'background-color: %s; color: %s; border-radius: %spx;',
            esc_attr($this->options['background_color']),
            esc_attr($this->options['icon_color']),
            esc_attr($this->options['border_radius'])
        );

        printf(
            '<div id="sstt-button" class="sstt-button %s %s %s %s" style="%s">
                <i class="%s"></i>
            </div>',
            esc_attr($position_class),
            esc_attr($size_class),
            esc_attr($shadow_class),
            esc_attr($animation_class),
            esc_attr($style),
            esc_attr($icon_class)
        );
    }

    /**
     * Shortcode callback
     */
    public function scroll_to_top_shortcode($atts) {
        if (empty($this->options['enable'])) {
            return '';
        }
        if (isset($this->options['output_mode']) && $this->options['output_mode'] === 'shortcode') {
            ob_start();
            $this->render_scroll_button();
            return ob_get_clean();
        }
        return '';
    }

    /**
     * Get icon class based on selected options
     */
    private function get_icon_class() {
        $icon_style = $this->options['icon_style'];
        $icon_type = $this->options['icon_type'];

        $icons = array(
            'arrow' => array(
                'fontawesome' => 'sstt-icon-arrow',
                'bootstrap' => 'sstt-icon-arrow',
                'dashicons' => 'dashicons dashicons-arrow-up-alt2'
            ),
            'chevron' => array(
                'fontawesome' => 'sstt-icon-chevron',
                'bootstrap' => 'sstt-icon-chevron',
                'dashicons' => 'dashicons dashicons-arrow-up-alt'
            ),
            'circle' => array(
                'fontawesome' => 'sstt-icon-circle',
                'bootstrap' => 'sstt-icon-circle',
                'dashicons' => 'dashicons dashicons-arrow-up-alt'
            )
        );

        return isset($icons[$icon_style][$icon_type]) ? $icons[$icon_style][$icon_type] : $icons['arrow']['fontawesome'];
    }

    /**
     * Get default options
     */
    public function get_default_options() {
        return array(
            'enable' => true,
            'output_mode' => 'auto',
            'button_position' => 'Bottom Right',
            'icon_style' => 'arrow',
            'icon_type' => 'fontawesome',
            'background_color' => '#0073e6',
            'icon_color' => '#ffffff',
            'hover_color' => '#005bb5',
            'button_size' => 'Medium',
            'border_radius' => '5',
            'shadow' => true,
            'animation_type' => 'fade',
            'scroll_offset' => '300',
            'scroll_speed' => '800'
        );
    }

    /**
     * Run the plugin
     */
    public function run() {
        // Plugin is running
    }
} 