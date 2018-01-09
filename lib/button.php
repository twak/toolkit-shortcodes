<?php
/**
 * Button shortcode
 * @see https://generatewp.com/take-shortcodes-ultimate-level/
 * @see https://github.com/dtbaker/wordpress-mce-view-and-shortcode-editor
 */
if ( ! class_exists( 'tk_button_shortcode' ) ) :

class tk_button_shortcode{
    /**
     * $shortcode_tag 
     * holds the name of the shortcode tag
     * @var string
     */
    public $shortcode_tag = 'tk_button';

    /**
     * __construct 
     * class constructor will set the needed filter and action hooks
     */
    function __construct()
    {
        //add shortcode
        add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );

        // add button to editor
        add_filter( 'tk_shortcodes_mce_buttons', array( $this, 'add_mce_button' ) );

        // add plugin to editor
        add_filter( 'tk_shortcodes_mce_plugins', array( $this, 'add_mce_plugin' ) );
    }

    /**
     * shortcode_handler
     * @param  array  $atts shortcode attributes
     * @param  string $content shortcode content
     * @return string
     */
    public function shortcode_handler($atts , $content = null)
    {
        // Set default parameters
        $button = shortcode_atts( array (
            'link' => '',
            'title' => '',
            'block' => false,
            'type' => 'default'
        ), $atts );

        $button = (object) $button;

        // sanitise
        $button->link = filter_var( $button->link, FILTER_VALIDATE_URL );
        $button->block = filter_var( $button->block, FILTER_VALIDATE_BOOLEAN );

        if ( $button->link && $button->title ) {
            //return shortcode output
            ob_start();
            include plugin_dir_path( __DIR__ ) . 'templates/button.php';
            return ob_get_clean();
        }
    }

    /**
     * add a button to the tinyMCE editor
     */
    public function add_mce_button( $buttons )
    {
        $buttons[] = $this->shortcode_tag;
        return $buttons;
    }

    /**
     * add a plugin to the tinyMCE editor
     */
    public function add_mce_plugin( $plugins )
    {
        $plugins[$this->shortcode_tag] = 'tk-button-plugin.js';
        return $plugins;
    }

}
new tk_button_shortcode();

endif;