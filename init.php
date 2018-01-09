<?php
/**
 * Plugin Name: Toolkit Shortcodes
 * Plugin URI: https://bitbucket.org/university-of-leeds/toolkit-shortcodes
 * Bitbucket Plugin URI: https://bitbucket.org/university-of-leeds/toolkit-shortcodes
 * Description: Shortcodes for components in the UoL WordPress Toolkit theme.
 * Version: 1.0.5
 * Author: Web Team
 * Author URI: https://bitbucket.org/university-of-leeds/
 * License: GPL2
 */

if ( ! class_exists( 'tk_shortcodes' ) ) {

    class tk_shortcodes
    {
        /* plugin version */
        public static $version = "1.0.5";

        /* register all shortcodes with wordpress API */
        public function __construct()
        {
            // panel shortcode (Tiny MCE style)
            include dirname(__FILE__) . '/lib/panel.php';

            // panel shortcode (shortcode style)
            add_shortcode( 'panel', array( __CLASS__, 'panel_shortcode' ) );

            // button shortcode (TinyMCE style)
            include dirname(__FILE__) . '/lib/button.php';

            // button shortcode (shortcode style)
            add_shortcode( 'button', array( __CLASS__, 'button_shortcode' ) );

            // download file button
            include dirname(__FILE__) . '/lib/downloadfile.php';

            // iframe
            include dirname(__FILE__) . '/lib/iframe.php';

            // include media templates
            add_action( 'print_media_templates', array( $this, 'media_templates' ) );

            // Remove built in gallery shortcode
            remove_shortcode('gallery', 'gallery_shortcode');

            // add gallery shortcode
            add_shortcode( 'gallery', array( $this, 'gallery_shortcode' ) );

            // enqueue scripts and styles
            add_action( 'wp_enqueue_scripts', array( $this, 'toolkit_shortcodes_script' ) );
            add_action( 'admin_head', array( $this, 'admin_head') );
            add_action( 'admin_enqueue_scripts', array($this , 'admin_script' ) );
            add_filter( 'mce_css', array($this , 'editor_styles' ) );

        }

        /*
         * PANEL SHORTCODE [panel title=""]Blah Blah[/panel]
         */
        public static function panel_shortcode( $atts, $content = null ) {

            // Set default parameters
            $panel_atts = shortcode_atts( array (
                'title' => ''
            ), $atts );

            // If title is empty, don't use it in the panel
            if( $panel_atts['title'] == '') {
                $title = '';
                // Otherwise, add the panel!
            } else {
                $title = '<div class="panel-heading"><h3 class="panel-title">' . wp_kses_post( $panel_atts['title'] ) . '</h3></div>';
            }

            // Return the panel markup
            return '<div class="panel panel-default">' . $title . '<div class="panel-body">' . $content . '</div></div>';
        }

        /*
        * BUTTON SHORTCODE [button link="" text="" type=""]
        */
        public static function button_shortcode( $atts )
        {

            // Set default parameters
            $button_atts = shortcode_atts( array (
                'link' => '',
                'text' => 'Button text',
                'type' => ''
            ), $atts );

            // Button types
            if( $button_atts['type'] == '' ) {
                $button_type = 'btn-primary';
            } else if( $button_atts['type'] == 'success' ) {
                $button_type = 'btn-success';
            } else if( $button_atts['type'] == 'info' ) {
                $button_type = 'btn-info';
            } else if( $button_atts['type'] == 'warning' ) {
                $button_type = 'btn-warning';
            } else if( $button_atts['type'] == 'danger' ) {
                $button_type = 'btn-danger';
            } else if( $button_atts['type'] == 'purple' ) {
                $button_type = 'btn-purple';
            }

            // Return the button
            return '<a href="' . wp_kses_post( $button_atts['link'] ) . '" class="btn btn-lg ' . $button_type . '">' . wp_kses_post( $button_atts['text'] ) . '</a>';
        }

        /**
         * GALLERY SHORTCODE
         * replaces default output for wordpress galleries
         */
        public static function gallery_shortcode( $attr )
        {
            $post = get_post();

            static $instance = 0;
            $instance++;

            if ( ! empty($attr['ids'])) {
                if ( empty( $attr['orderby'] ) ) {
                    $attr['orderby'] = 'post__in';
                }
                $attr['include'] = $attr['ids'];
            }

            $output = apply_filters('post_gallery', '', $attr);

            if ($output != '') {
                return $output;
            }

            if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                if ( ! $attr['orderby'] ) {
                    unset( $attr['orderby'] );
                }
            }

            $gallery_atts = shortcode_atts( array(
                'order' => 'ASC',
                'orderby' => 'menu_order ID',
                'id' => $post->ID,
                'itemtag' => '',
                'icontag' => '',
                'captiontag' => '',
                'columns' => 3,
                'size' => 'thumbnail',
                'include' => '',
                'link' => '',
                'exclude' => ''
            ), $attr);

            $id = intval( $gallery_atts["id"] );

            if ( $gallery_atts["order"] === 'RAND') {
                $gallery_atts["orderby"] = 'none';
            }

            // build args to get attachments
            $args = array(
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => $gallery_atts["order"],
                'orderby' => $gallery_atts["orderby"]
            );

            if ( ! empty( $gallery_atts["include"] ) ) {
                $args['include'] = $gallery_atts["include"];
                $_attachments = get_posts($args);
                $attachments = array();
                foreach ($_attachments as $key => $val) {
                    $attachments[$val->ID] = $_attachments[$key];
                }
            } elseif ( ! empty( $gallery_atts["exclude"] ) ) {
                $args['post_parent'] = $gallery_atts["id"];
                $args['exclude'] = $gallery_atts["exclude"];
                $attachments = get_children($args);
            } else {
                $args['post_parent'] = $gallery_atts["id"];
                $attachments = get_children($args);
            }

            if (empty($attachments)) {
                return '';
            }

            if (is_feed()) {
                $output = "\n";
                foreach ($attachments as $att_id => $attachment) {
                    $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
                }
                return $output;
            }


            $cols = intval( $gallery_atts["columns"] );
            if ( ! $cols ) {
                $cols = 3;
            }
            $item_class = '';
            $container_class = '';
            if ( $cols >= 6 ) {
                $container_class = " clear-md-2 clear-sm-4 clear-xs-6";
                $item_class = "col-md-2 col-sm-4 col-xs-6";
            } elseif ( $cols >= 4 ) {
                $container_class = " clear-md-3 clear-sm-4 clear-xs-6";
                $item_class = "col-md-3 col-sm-4 col-xs-6";
            } elseif ( 3 === $cols ) {
                $container_class = " clear-md-4 clear-sm-4 clear-xs-6";
                $item_class = "col-md-4 col-sm-4 col-xs-6";
            } elseif ( 2 === $cols ) {
                $container_class = " clear-xs-6";
                $item_class = "col-xs-6";
            } elseif ( 1 === $cols ) {
                $item_class = "col-xs-12";
            }

            // enqueue scripts and styles
            wp_enqueue_script(
                'toolkit-gallery-js',
                plugins_url( 'js/toolkit-gallery.js', __FILE__ ),
                array( 'jquery' ),
                self::$version,
                true
            );
            wp_enqueue_style(
                'toolkit-gallery-css',
                plugins_url( 'css/toolkit-gallery.css', __FILE__ )
            );

            // start output
            $output = sprintf('<!-- Gallery --><div class="tk-gallery container-fluid%s" data-featherlight-gallery data-featherlight-filter="a">', $container_class );

            // start column output
            $count = 0;
            foreach ($attachments as $id => $attachment) {
                $image_src_url = wp_get_attachment_image_src($id, $gallery_atts["size"]);
                $image_link_url = wp_get_attachment_image_src($id, "large");
                $image_caption = $attachment->post_excerpt;
                $output .= sprintf( '<div class="gallery-item %s"><a href="%s" rel="gallery"><img src="%s" alt="%s"></a><p class="gallery-item-caption">%s</p></div>', $item_class, esc_attr($image_link_url[0]), $image_src_url[0], esc_attr($attachment->post_title), $image_caption ) ;
            }
            $output .= '</div><!-- #Gallery -->';
            return $output;
        }

        /*
         * Enqueue the additional scripts and styles used in the shortcode output
         */
        public static function toolkit_shortcodes_script()
        {
            wp_enqueue_style(
                'toolkit-shortcode-css',
                plugins_url( 'css/toolkit-shortcodes.css', __FILE__ )
            );
            wp_enqueue_script(
                'toolkit-shortcode-js',
                plugins_url( 'js/toolkit-shortcodes.js', __FILE__ ),
                array( 'jquery' ),
                self::$version,
                true
            );
        }

        /**
         * admin_scripts 
         * Used to enqueue custom scripts and styles for admin
         * @return void
         */
        public function admin_script()
        {
             wp_enqueue_style(
                'tk_shortcodes_admin',
                plugins_url( 'css/toolkit-shortcodes-admin.css' , __FILE__ )
            );
        }

        /**
         * admin_head
         * adds MCE filters if rich editing is enabled
         */
        public function admin_head() 
        {
            // check user permissions
            if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
                return;
            }
            
            // check if WYSIWYG is enabled
            if ( 'true' == get_user_option( 'rich_editing' ) ) {
                add_filter( 'mce_external_plugins', array( $this ,'mce_plugins' ), 100 );
                add_filter( 'mce_buttons_2', array($this, 'mce_buttons' ), 100 );
            }
        }

        /**
         * editor styles
         * loads additional style rules into tinymce editor
         */
        public function editor_styles( $mce_css )
        {
            if ( ! empty( $mce_css ) ) {
                $mce_css .= ',';
            }
            $mce_css .= plugins_url( 'css/toolkit-shortcodes-editor-style.css', __FILE__ );
            return $mce_css;
        }

        /**
         * mce_plugins 
         * Adds tinymce plugins which are added by shortcodes using the 
         * tk_shortcodes_mce_plugins filter.
         * @param  array $plugin_array 
         * @return array
         */
        public function mce_plugins( $plugin_array ) 
        {
            $tk_plugins = apply_filters('tk_shortcodes_mce_plugins', array() );
            if ( count( $tk_plugins ) ) {
                foreach ( $tk_plugins as $tk_plugin => $plugin_script ) {
                    $plugin_array[$tk_plugin] = plugins_url( 'js/' . $plugin_script , __FILE__ );
                }
            }
            return $plugin_array;
        }

        /**
         * mce_buttons 
         * Adds any tinymce buttons defined in shortcodes - added by using the
         * tk_shortcodes_mce_buttons filter
         * @param  array $buttons 
         * @return array
         */
        public function mce_buttons( $buttons ) 
        {
            $tk_buttons = apply_filters('tk_shortcodes_mce_buttons', array() );
            if ( count( $tk_buttons ) ) {
                foreach ( $tk_buttons as $tk_button ) {
                    array_push( $buttons, $tk_button );
                }
            }
            return $buttons;
        }

        /**
         * media_templates
         * Prints any media templates defined in shortcodes
         */
        public function media_templates( $templates )
        {
            foreach (glob(dirname(__FILE__) . "/templates/*.html") as $filename)
            {
                include_once $filename;
            }
        }
    }
    new tk_shortcodes();
}