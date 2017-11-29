<?php
/**
 * Panel shortcode
 * @see https://generatewp.com/take-shortcodes-ultimate-level/
 * @see https://github.com/dtbaker/wordpress-mce-view-and-shortcode-editor
 */
if ( ! class_exists( 'tk_panel_shortcode' ) ) :

class tk_panel_shortcode{
	/**
	 * $shortcode_tag 
	 * holds the name of the shortcode tag
	 * @var string
	 */
	public $shortcode_tag = 'tk_panel';

    /**
     * panel types
     * these are defined in stylesheets and correspond to different colour schemes
     * @var array
     */
    public $panel_types = array('primary','success','info','warning','danger','default');

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
		// Attributes
		$panel = shortcode_atts(
			array(
				'title' => '',
				'footer' => '',
				'type' => 'default',
			), $atts );
		
        $panel = (object) $panel;

		// make sure the panel type is a valid styled type if not revert to default
		$panel->type = in_array($panel->type, $this->panel_types)? $panel->type: 'default';

        // trim panel body content and process shortcodes
        $panel->content = trim( do_shortcode( $content ) );

		//return shortcode output
        ob_start();
        include plugin_dir_path( __DIR__ ) . 'templates/panel.php';
        return ob_get_clean();
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
        $plugins[$this->shortcode_tag] = 'tk-panel-plugin.js';
        return $plugins;
    }

}
new tk_panel_shortcode();

endif;