<?php
/**
 * Panel shortcode
 * @see https://generatewp.com/take-shortcodes-ultimate-level/
 */
if ( ! class_exists( 'tk_downloadfile_shortcode' ) ) :

class tk_downloadfile_shortcode{
    /**
     * $shortcode_tag 
     * holds the name of the shortcode tag
     * @var string
     */
    public $shortcode_tag = 'downloadfile';

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
        $downloadfile_atts = shortcode_atts( array (
            'url' => '',
            'type' => ''
        ), $atts );

        // sanitise
        if ( ! $content || trim($content) == "" ) {
            $content = "Download";
        }
        $type = strtolower(trim($downloadfile_atts["type"]));
        $file_types = array('word', 'powerpoint', 'zip', 'pdf', 'excel');
        $url = filter_var( $downloadfile_atts["url"], FILTER_VALIDATE_URL );
        if ( $url && in_array( $type, $file_types ) ) {
            return sprintf('<h4><a class="island island-sm island-m-b skin-box-module downloadlink type-%s" href="%s">%s</a></h4>', $type, $url, $content );
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
        $plugins[$this->shortcode_tag] = 'tk-downloadfile-plugin.js';
        return $plugins;
    }

}
new tk_downloadfile_shortcode();

endif;