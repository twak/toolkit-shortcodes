<?php
/**
 * iframe shortcode used in UoL theme
 *
 * @author Peter Edwards <p.l.edwards@leeds.ac.uk>
 * @version 1.2.1
 * @package UoL_theme
 */

/* sanity check */
if ( ! class_exists( 'uol_shortcode_iframe' ) ) {
	/**
	 * static class to handle iframe shortcode
	 * includes de-registration of existing shortcode
	 */
	class uol_shortcode_iframe
	{
		/**
		 * register shortcode with Wordpress API */
		public static function register()
		{
			/* shortcode for iframes */
			add_shortcode( 'iframe', array( __CLASS__, 'process_shortcode' ) );

			if ( method_exists( __CLASS__, 'embed_to_shortcode' ) ) {
				/* filter for translating embed codes directly in the editor to shortcode equivalents */
				add_filter( 'pre_kses', array( __CLASS__, 'embed_to_shortcode') );
			}
		}

		/**
		 * method which provides the shortcode
		 * called by register_shortcode()
		 */
		public static function process_shortcode( $args, $content = '')
		{
			extract(shortcode_atts(array(
				'url'			=> '',
				'src'			=> '',
				'title'			=> '',
				'scrolling'		=> 'no',
				'width'			=> '100%',
				'height'		=> '300',
				'border'		=> 0,
				'bordercolour'	=> '#fff',
				'margin'		=> 0,
				'style'			=> '',
				'id'            => '',
				'attr'			=> ''
			), $args));
			/* make sure there is a valid url */
			if ( empty( $url ) && empty( $src ) ) {
				return '<!-- Iframe: You did not enter a valid URL -->';
			}
			/* normalise URL variable */
			if ( empty( $url ) ) {
				$url = $src;
			}
			/* sanitise attributes and add style attributes for HTML5 */
			$styles = 'position:relative;';
			$attrs = '';
			if (intval($border) > 0) {
				$styles .= 'border:' . intval($border) . 'px solid ' . $bordercolour . ';';
				$attrs .= ' frameborder="' . intval($border) . '"';
			} else {
				$styles .= 'border:0;';
				$attrs .= ' frameborder="0"';
			}
			if (intval($margin) > 0) {
				$styles .= 'margin:' . intval($margin) . 'px;';
				$attrs .= ' marginheight="' . intval($margin) . '"';
			} else {
				$styles .= 'margin:0;';
				$attrs .= ' marginheight="0"';
			}
			$scrolling = (in_array(strtolower($scrolling), array("yes", "no", "x", "y")))? strtolower($scrolling): 'no';
			switch ($scrolling) {
				case "no":
					$styles .= 'overflow:hidden;';
					$attrs .= ' scrolling="no"';
					break;
				case "yes":
					$styles .= 'overflow:auto;';
					$attrs .= ' scrolling="yes"';
					break;
				case "x":
					$styles .= 'overflow:auto;overflow-x:auto;overflow-y:hidden;';
					$attrs .= ' scrolling="no"';
					break;
				case "y":
					$styles .= 'overflow:auto;overflow-y:auto;overflow-x:hidden;';
					$attrs .= ' scrolling="no"';
					break;
			}
			if (trim($style) != '') {
				$styles .= trim($style);
			}
			$titleattr = esc_attr(trim($title));
			if ( ! empty( $titleattr ) ) {
				$attrs .= sprintf( ' title="%s"', $titleattr );
			}
			$idattr = esc_attr(trim($id));
			if ( ! empty( $idattr ) ) {
				$attrs .= sprintf( ' id="%s"', $idattr );
			}
			/* additional attributes passed to shortcode */
			if ( ! empty( $attr ) ) {
				$attrs .= " " . trim($attr);
			}
			/* return iframe HTML */
			return sprintf('<iframe src="%s" width="%s" height="%s" style="%s"%s>%s</iframe>', $url, $width, $height, $styles, $attrs, $content);

		}

		/**
		 * filter placed on page/post content to turn any <iframe> tags into the shortcode equivalent
		 */
		public static function embed_to_shortcode( $content )
		{
			if ( false === strpos( $content, '<iframe' ) ) {
				return $content;
			}
			if ( preg_match_all( '!<iframe([^>]*)>(.*)</iframe>!Uis', $content, $matches, PREG_SET_ORDER ) ) {
				/* go through each match turning iframes to shortcodes */
				foreach ($matches as $set) {
					$output = '';
					/* parse all attributes on iframe tag */
					$attributes = uol_shortcodes::parseAttributes($set[1]);
					/* grab content */
					$iframe_content = trim($set[2]);
					/* these attributes bercome attributes for the shortcode */
					$parsed_attr = array( 'src', 'title', 'scrolling', 'width', 'height', 'style', 'id' );
					/* allowed extra attributes */
					$allowed_extras = array('mozallowfullscreen', 'allowfullscreen', 'webkitallowfullscreen');
					/* sort out attributes for shortcoode */
					$shortcode_attr = '';
					$extra_attr = '';
					foreach ( $attributes as $name => $value ) {
						if ( in_array( $name, $parsed_attr) ) {
							/* iframe attributes with corresponding shortcode attributes */
							$shortcode_attr .= ' ' . $name . '="' . $value . '"';
						} else {
							/* stuff empty attributes in extras (if allowed) */
							if ( $value !== '' && $value !== "0" && in_array( strtolower($name), $allowed_extras ) ) {
								$extra_attr .= ' ' . $name;
							} else {
								/* if there is a border or margin, convert to shortcode attributes */
								if ( strtolower($name) == "frameborder" ) {
									$shortcode_attr .= ' border="' . $value . '"'; 
								}
								if ( strtolower($name) == "marginheight" ) {
									$shortcode_attr .= ' margin="' . $value . '"';
								}
							}
						}
					}
					$output .= '[iframe' . $shortcode_attr . ' attr="' . trim($extra_attr) . '"]';
					if ( ! empty($iframe_content) ) {
						$output .= $iframe_content . '[/iframe]';
					}
					$content = str_replace($set[0], $output, $content);
				}
				return $content;
			} else {
				/* iframe tag not matched */
				return $content;
			}
		}
	}

	/* register when Wordpress has loaded */
	add_action( 'wp_loaded', array( 'uol_shortcode_iframe', 'register' ) );

/* end sanity check*/
}