<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 100 );

// END ENQUEUE PARENT ACTION


/*--------------------------------------------             

Dequeue Ultimate Member stylesheets 
---------------------------------------------*/

$um_priority = apply_filters( 'um_core_enqueue_priority', 100 );
//add_action( 'wp_enqueue_scripts',  'gbfl_dequeue_um_scripts', $um_priority + 1);
function gbfl_dequeue_um_scripts() {
	/*
	 *	Fonticons
	 *		um_fonticons_ii
	 *		um_fonticons_fa
	 *	
	 *	Select2
	 *		select2
	 *	
	 *	Modal
	 *		um_modal
	 *	
	 *	Plugin CSS
	 *		um_styles
	 *		um_members
	 *		um_profile
	 *		um_account
	 *		um_misc
	 *	
	 *	File Upload
	 *		um_fileupload
	 *	
	 *	Datetime Picker
	 *		um_datetime
	 *		um_datetime_date
	 *		um_datetime_time
	 *	
	 *	Raty
	 *		um_raty
	 *	
	 *	Scrollbar
	 *		um_scrollbar
	 *	
	 *	Image Crop
	 *		um_crop
	 *	
	 *	Tipsy
	 *		um_tipsy
	 *	
	 *	Responsive
	 *		um_responsive
	 *	
	 *	RTL
	 *		um_rtl
	 *	
	 *	Default CSS
	 *		um_default_css
	 *	
	 *	Old CSS
	 *		um_old_css
	 */
	wp_dequeue_style('um_default_css');
	wp_dequeue_style('um_responsive');
	wp_dequeue_style('um_styles');
	wp_dequeue_style('um_profile');
	wp_dequeue_style('um_account');
	/*wp_dequeue_style('um_members');*/
	wp_dequeue_style('um_misc');
	wp_dequeue_style('um_old_default_css');
	wp_dequeue_style('um_old_css');

}

add_action( 'um_before_form',  'provia_add_entrylink_login', $um_priority + 1);

function provia_add_entrylink_login()
{
	
	echo '<div class="um-col-alt">';
	echo '<div class="um-left um-half">';
	echo '<a href="/entry-link-login/" title="Connect with EntryLink" class="um-button um-alt um-button-social">';
	echo '<img src="/wp-content/uploads/2021/05/ProVia-logo.svg" width="50"> ';
	echo '<span>Connect with EntryLink</span>';
	echo '</a>';
	echo '</div>';	
	echo '</div>';
	echo '<div class="um-clear"></div>';
	
}
