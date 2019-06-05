<?php
/**
 * Download link shortcode
 * @see https://generatewp.com/take-shortcodes-ultimate-level/
 * @see https://github.com/dtbaker/wordpress-mce-view-and-shortcode-editor
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
        $downloadfile = shortcode_atts( array (
            'url' => '',
            'type' => ''
        ), $atts );

        $downloadfile = (object) $downloadfile;

        // sanitise
        $downloadfile->content = ( ! $content || trim($content) == "" ) ? "Download": $content;
        $downloadfile->type = strtolower( trim( $downloadfile->type ) );
        $file_types = array('word', 'powerpoint', 'zip', 'pdf', 'excel', 'github');
        $downloadfile->url = filter_var( $downloadfile->url, FILTER_VALIDATE_URL );
        if ( $downloadfile->url && in_array( $downloadfile->type, $file_types ) ) {
            //return shortcode output
            ob_start();
            include plugin_dir_path( __DIR__ ) . 'templates/downloadfile.php';
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
        $plugins[$this->shortcode_tag] = 'tk-downloadfile-plugin.js';
        return $plugins;
    }

}
new tk_downloadfile_shortcode();

endif;