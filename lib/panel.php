<?php
/**
 * Panel shortcode
 * @see https://generatewp.com/take-shortcodes-ultimate-level/
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
	 * __construct 
	 * class constructor will set the needed filter and action hooks
	 */
	function __construct()
    {
		//add shortcode
		add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );
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
		extract( shortcode_atts(
			array(
				'header' => 'no',
				'footer' => 'no',
				'type' => 'default',
			), $atts )
		);
		
		//make sure the panel type is a valid styled type if not revert to default
		$panel_types = array('primary','success','info','warning','danger','default');
		$type = in_array($type, $panel_types)? $type: 'default';

		//start panel markup
		$output = '<div class="panel panel-'.$type.'">';

		//check if panel has a header
		if ('no' != $header)
			$output .= '<div class="panel-heading">'.$header.'</div>';

		//add panel body content and allow shortcode in it
		$output .= '<div class="panel-body">'.trim(do_shortcode( $content )).'</div>';

		//check if panel has a footer
		if ('no' != $footer)
			$output .= '<div class="panel-footer">'.$footer.'</div>';

		//add closing div tag
		$output .= '</div>';

		//return shortcode output
		return $output;
	}
}
new tk_panel_shortcode();

endif;