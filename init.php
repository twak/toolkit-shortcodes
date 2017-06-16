<?php
/**
 * Plugin Name: Toolkit Shortcodes
 * Plugin URI: http://toolkit.leeds.ac.uk/wordpress
 * Description: Shortcodes for components in the UoL WordPress Toolkit theme.
 * Version: 1.0.1
 * Author: Web Team
 * Author URI: http://toolkit.leeds.ac.uk/wordpress
 * License: GPL2
 */

if ( ! class_exists( 'tk_shortcodes' ) ) {

    class tk_shortcodes
    {
        /* plugin version */
        public static $version = "1.0.1";

        /* register all shortcodes with wordpress API */
        public static function register()
        {
            // panel shortcode
            add_shortcode( 'panel', array( __CLASS__, 'panel_shortcode' ) );

            // button shortcode
            add_shortcode( 'button', array( __CLASS__, 'button_shortcode' ) );

            // PDF download button
            add_shortcode( 'downloadfile', array( __CLASS__, 'download_shortcode' ) );

            // Remove built in gallery shortcode
            remove_shortcode('gallery', 'gallery_shortcode');

            // add gallery shortcode
            add_shortcode( 'gallery', array( __CLASS__, 'gallery_shortcode' ) );

            // enqueue scripts and styles
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'toolkit_shortcodes_script' ) );
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
            return '<div class="panel panel-default">' . $title . '<div class="panel-body">' . do_shortcode( $content ) . '</div></div>';
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
         * DOWNLOAD SHORTCODE
         * Places a link to a file in an island with an icon
         */
        public static function download_shortcode( $atts, $content = null )
        {
            // Set default parameters
            $pdf_atts = shortcode_atts( array (
                'url' => '',
                'type' => ''
            ), $atts );

            // sanitise
            if ( ! $content || trim($content) == "" ) {
                $content = "Download";
            }
            $type = strtolower(trim($pdf_atts["type"]));
            $types = array('word', 'powerpoint', 'zip', 'pdf', 'excel');
            $url = filter_var( $pdf_atts["url"], FILTER_VALIDATE_URL );
            if ( $url && in_array( $type, $types ) ) {
                return sprintf('<h4><a class="island island-sm island-m-b skin-box-module downloadlink type-%s" href="%s">%s</a></h4>', $type, $url, $content );
            }
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
            if ( $cols >= 6 ) {
                $class = "col-md-2 col-sm-4 col-xs-6";
            } elseif ( $cols >= 4 ) {
                $class = "col-md-3 col-xs-6";
            } elseif ( 3 === $cols ) {
                $class = "col-md-4 col-xs-6";
            } elseif ( 2 === $cols ) {
                $class = "col-xs-6";
            } elseif ( 1 === $cols ) {
                $class = "col-xs-12";
            }

            // start output
            $output = '<!-- Gallery --><div class="tk-gallery container-fluid"><div class="row">';

            // start column output
            $count = 0;
            foreach ($attachments as $id => $attachment) {
                $image_src_url = wp_get_attachment_image_src($id, "thumbnail");//$gallery_atts["size"]);
                $image_link_url = wp_get_attachment_image_src($id, "large");
                $image_caption = get_the_excerpt($id);
                // this was to target clicks with the modal window
                //$output .= sprintf( '<div class="%s"><button data-toggle="modal" data-target="#tk_lightbox%d" data-imgsrc="%s" data-alt="%s" data-caption="%s"><img src="%s" alt="%s"></button></div>', $class, $instance, esc_attr($image_link_url[0]), esc_attr($attachment->post_title), esc_attr($attachment->post_excerpt), $image_src_url[0], esc_attr($attachment->post_title) );
                $output .= sprintf( '<div class="gallery-item %s"><a href="%s"><img src="%s" alt="%s"><p class="gallery-item-caption">%s</p></a></div>', $class, esc_attr($image_link_url[0]), $image_src_url[0], esc_attr($attachment->post_title), $image_caption ) ;
            }

            $output .= '</div>';

            // modal window
            /*$output .= '<!-- Modal -->';
            $output .= sprintf('<div id="tk_lightbox%d" class="tk-lightbox modal fade" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">', $instance );
            //$output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>';
            $output .= '<div class="modal-body"><img src="" alt="" /></div>';
            $output .= '<div class="modal-footer"><p class="caption"></p><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>';
            $output .= '</div></div></div><!-- #Modal -->';*/

            $output .= '</div><!-- #Gallery -->';
            return $output;
        }

        /*
         * Enqueue the additional script
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

            // jquery.detch_syipe.js
            wp_enqueue_script(
                'jquery-detect-swipe-js',
                plugins_url( 'js/vendor/jquery.detect_swipe.js', __FILE__ ),
                array( 'jquery' ),
                self::$version,
                true
            );

            // Featherlight.js

            wp_enqueue_script(
                'featherlight-js',
                plugins_url( 'js/vendor/featherlight.min.js', __FILE__ ),
                array( 'jquery' ),
                self::$version,
                true
            );
            wp_enqueue_style(
                'featherlight-css',
                plugins_url( 'css/vendor/featherlight.min.css', __FILE__ )
            );

            wp_enqueue_script(
                'featherlight-gallery-js',
                plugins_url( 'js/vendor/featherlight.gallery.min.js', __FILE__ ),
                array( 'jquery' ),
                self::$version,
                true
            );

            wp_enqueue_style(
                'featherlight-gallery-css',
                plugins_url( 'css/vendor/featherlight.gallery.min.css', __FILE__ )
            );

        }
    }
    tk_shortcodes::register();
}