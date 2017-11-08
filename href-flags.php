<?php
/**
 * Plugin Name: Per Page Href Flags Manager
 * Description: This plugin adds Href links on per page level including a flag.
 * Version: 1.0.6
 * Author: Better Collective - Hanning Høegh
 * License: GPL2
 */

define( 'HF_PATH', plugin_dir_path( __FILE__ ) );

/*---------------------------*\
    Plugin update checker.
\*---------------------------*/
require 'plugin-update-checker/plugin-update-checker.php';
$className2 = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker2 = new $className2(
    'https://github.com/SmileyJoey/per-page-href-flags-manager/',
    __FILE__,
    'master'
);

/*---------------------------*\
    Load plugin style CSS.
\*---------------------------*/
add_action('wp_enqueue_scripts', 'plugin_scripts_stylesheets');
function plugin_scripts_stylesheets() {
	if ( is_single() || is_page()  ) {
		wp_enqueue_style('href-flags-manager', plugin_dir_url( __FILE__ ) . 'css/style.css' );
	}
}

/*----------------------------------------------------------------------*\
    If ACF is not installed, include it, without needing to install it.
\*----------------------------------------------------------------------*/
// if( !class_exists('acf') ) {

// 	// 1. customize ACF path
// 	add_filter('acf/settings/path', 'my_acf_settings_path');
// 	function my_acf_settings_path( $path ) {
	 
// 	    // update path
// 	    $path = HF_PATH . '/acf/';
// 	    return $path;
// 	}
	 

// 	// 2. customize ACF dir
// 	add_filter('acf/settings/dir', 'my_acf_settings_dir');
// 	function my_acf_settings_dir( $dir ) {
	 
// 	    // update path
// 	    $dir = HF_PATH . '/acf/';
// 	    return $dir;
// 	}
	 

// 	// 3. Hide ACF field group menu item
// 	// add_filter('acf/settings/show_admin', '__return_false');


// 	// 4. Include ACF
// 	include_once( HF_PATH . '/acf/acf.php' );
// }

/*-------------------------*\
    NOTICE ABOUT ACF PRO
\*-------------------------*/

function check_if_acf_exists() {
    if( !class_exists('acf') ) : ?>
        <div class="notice notice-error is-dismissible">
            <p>You need to install &amp; activate ACF PRO to make Hreflang plugin work!</p>
        </div>
    <?php endif;
}
add_action('admin_init', 'check_if_acf_exists');



/*----------------------------------------------------------------*\
    Helper function. Check if given field group already exists.
\*----------------------------------------------------------------*/
function is_field_group_exists($value, $type='post_title') {
	$exists = false;
	if ($field_groups = get_posts(array('post_type'=>'acf'))) {
	    foreach ($field_groups as $field_group) {
	        if ($field_group->$type == $value) {
	            $exists = true;
	        }
	    }
	}
	return $exists;
}



/*--------------------------------------------------------------------*\
    If group field "Hreflang" doesn't exists, load JSON group field.
\*--------------------------------------------------------------------*/
$fieldGroup = 'Hreflang';

if ( is_field_group_exists($fieldGroup) == false ) {

	// Load ACF Settings from JSON file.
	add_filter('acf/settings/load_json', 'href_acf_json_load_point');
	function href_acf_json_load_point( $paths ) {

	    // append path
	    $paths[] = HF_PATH . 'acf-json-group-fields';
	    return $paths;
	}

	// Save ACF Settings to JSON file.
	add_filter('acf/settings/save_json', 'href_acf_json_save_point');
	function href_acf_json_save_point( $path ) {
	    if( isset($_POST['acf_field_group']['key']) && $_POST['acf_field_group']['key'] == "group_87e816c9a487a" )
	        $path = HF_PATH . 'acf-json-group-fields';
	    return $path;
	}

}



/*-------------------------------------------*\
    Output Href alternate links to header.
\*-------------------------------------------*/
add_action('wp_head', 'output_href_links');
function output_href_links() {
	if ( is_single() || is_page()  ) {
		$postid = get_the_ID();
		// echo "Post ID: ".$postid;
		// var_dump(have_rows('hreflang'));
  		//Output links.
	  	if( have_rows('hreflang', $postid) ):
			while ( have_rows('hreflang', $postid) ) : the_row(); ?>
				<link rel="alternate" hreflang="<?php the_sub_field('hreflang_tag'); ?>" href="<?php the_sub_field('href_link'); ?>"/>
			<?php
			endwhile;
		else: //No Rows found.
		endif;
	}
}



/*----------------------------------------------------------*\
    Output Href links flags right after the main content.
\*----------------------------------------------------------*/
add_filter( "the_content", "right_after_content" );
function right_after_content($content){
	if ( is_single() || is_page()  ) {
		$postid = get_the_ID();
		if( have_rows('hreflang', $postid) ): ?>
			<?php $after_content=""; ?>
			<?php while( have_rows('hreflang', $postid) ): the_row(); ?>
				<?php
					$flag_size = '32'; // 32, 48 or 20
					$hreflang = get_sub_field('hreflang_tag');
					$matches = null;
					$country = preg_match('/(.*)\\-(.*)/', $hreflang, $matches);
					$country = $matches[2];
					$flag = get_sub_field('hreflang_flag');
					$href_link = get_sub_field('href_link');
					$active = (get_sub_field('hreflang_active') ? 'active' : '');
					if($flag) $language = $flag;
					else $language = $country;
					switch($language) {
						/* A */
						case 'AL':
						 	$language_flag = 'flag-20-Albania';
							break;
						case 'AU':
						 	$language_flag = 'flag-20-Australia';
							break;
						case 'AT':
						 	$language_flag = 'flag-20-Austria';
							break;
						/* B */
						case 'BR':
						 	$language_flag = 'flag-20-Brazil';
							break;
						case 'BG':
						 	$language_flag = 'flag-20-Bulgaria';
							break;
						/* C */
						case 'CA':
						 	$language_flag = 'flag-20-Canada';
							break;
						case 'CN':
						 	$language_flag = 'flag-20-China';
							break;
						case 'HR':
						 	$language_flag = 'flag-20-Croatian';
							break;
						case 'CZ':
						 	$language_flag = 'flag-20-Czech-republic';
							break;
						/* D */
						case 'DK':
						 	$language_flag = 'flag-20-Denmark';
							break;
						/* F */
						case 'FI':
						 	$language_flag = 'flag-20-Finland';
							break;
						case 'FR':
						 	$language_flag = 'flag-20-France';
							break;
						/* G */
						case 'DE':
						 	$language_flag = 'flag-20-Germany';
							break;
						case 'GR':
						 	$language_flag = 'flag-20-Greece';
							break;
						/* H */
						case 'HK':
						 	$language_flag = 'flag-20-Hong-kong';
							break;
						case 'HU':
						 	$language_flag = 'flag-20-Hungary';
							break;
						/* I */
						case 'IN':
						 	$language_flag = 'flag-20-India';
							break;
						case 'ID':
						 	$language_flag = 'flag-20-Indonesia';
							break;
						case 'IE':
						 	$language_flag = 'flag-20-Ireland';
							break;
						case 'IT':
						 	$language_flag = 'flag-20-Italy';
							break;
						/* J */
						case 'JP':
						 	$language_flag = 'flag-20-Japan';
							break;
						/* K */
						case 'KZ':
						 	$language_flag = 'flag-20-Kazakhstan';
							break;
						case 'KE':
						 	$language_flag = 'flag-20-Kenya';
							break;
						/* L */
						case 'LV':
						 	$language_flag = 'flag-20-Latvia';
							break;
						/* M */
						case 'MY':
						 	$language_flag = 'flag-20-Malaysia';
							break;
						case 'MX':
						 	$language_flag = 'flag-20-Mexico';
							break;
						/* N */
						case 'NL':
						 	$language_flag = 'flag-20-Netherlands';
							break;
						case 'NZ':
						 	$language_flag = 'flag-20-New-zealand';
							break;
						case 'NG':
						 	$language_flag = 'flag-20-Nigeria';
							break;
						case 'NO':
						 	$language_flag = 'flag-20-Norway';
							break;
						/* P */
						case 'PH':
						 	$language_flag = 'flag-20-Philippines';
							break;
						case 'PL':
						 	$language_flag = 'flag-20-Poland';
							break;
						case 'PT':
						 	$language_flag = 'flag-20-Portugal';
							break;
						/* R */
						case 'RO':
						 	$language_flag = 'flag-20-Romania';
							break;
						case 'RU':
						 	$language_flag = 'flag-20-Russia';
							break;
						/* S */
						case 'RS':
						 	$language_flag = 'flag-20-Serbia';
							break;
						case 'SI':
						 	$language_flag = 'flag-20-Slovenia';
							break;
						case 'ZA':
						 	$language_flag = 'flag-20-South-africa';
							break;
						case 'ES':
						 	$language_flag = 'flag-20-Spain';
							break;
						case 'SE':
						 	$language_flag = 'flag-20-Sweden';
							break;
						case 'CH':
						 	$language_flag = 'flag-20-Switzerland';
							break;
						/* T */
						case 'TH':
						 	$language_flag = 'flag-20-Thailand';
							break;
						case 'TN':
						 	$language_flag = 'flag-20-Tunisia';
							break;
						case 'TR':
						 	$language_flag = 'flag-20-Turkey';
							break;
						case 'TZ':
						 	$language_flag = 'flag-20-Tanzania';
							break;
						/* U */
						case 'GB':
							$language_flag = 'flag-20-United-kingdom';
							break;
						case 'US':
						 	$language_flag = 'flag-20-United-states';
							break;
						case 'UG':
						 	$language_flag = 'flag-20-Uganda';
							break;
						/* V */		
						case 'VN':
						 	$language_flag = 'flag-20-Vietnam';
							break;
						/* Error */
						default:
							$language_flag = 'error';
					}
				?>
			<?php $after_content .= "<a href='$href_link' class='$language_flag $active language-flag'></a>"; ?>
			<?php endwhile; ?>
		<?php return $content .= "<div id='language-container'> $after_content </div>" ; ?>
		<?php endif;
	}
	return $content;
}