<?php
/* preset FGC setting*/
if ( file_exists( TEMPLATEPATH . "/includes/options-init.php" ) ) {
	require_once TEMPLATEPATH . "/includes/options-init.php";
}

if (!session_id()) {
	session_start();
}

/* Designed by TemplateLite.com */
$tpinfo[ 'themename' ] = 'Beach Holiday';
$tpinfo[ 'prefix' ]    = 'templatelite';        //for options. e.g. all templatelite themes should use "templatelite" for general options (feed url, twitter id, analytics)
$tpinfo[ 'tb_prefix' ] = 'templatelite_beachholiday';//for options. theme base prefix


if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar( array(
		'before_widget' => '<li><div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div></li>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
}

include( TEMPLATEPATH . '/includes/theme-options.php' );
include( TEMPLATEPATH . '/includes/theme-setup.php' );
include( TEMPLATEPATH . '/includes/theme-functions.php' );
include( TEMPLATEPATH . '/includes/ctxphc-functions.php' );
include( TEMPLATEPATH . '/template.php' );

$max_children = 4;

/**
 *  Load custom scripts:
 */
function reg_custom_scripts_and_styles() {

	wp_register_script( 'mp-validation-script', get_template_directory_uri() . '/includes/js/mp-validation-script.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'mp-validation-script' );

	//Register JQuery Input Validation Rules in English
	wp_register_script( 'validation-local', get_template_directory_uri() . '/includes/js/languages/jquery.validationEngine-en.js', '', true );
	wp_enqueue_script( 'validation-local' );

	//Register jQuery Input Validation Engine
	wp_register_script( 'validation-engine', get_template_directory_uri() . '/includes/js/jquery.validationEngine.js', '', true );
	wp_enqueue_script( 'validation-engine' );

	//Register jQuery Input Validation CSS Stylesheet
	wp_register_style( 'validation-style', get_template_directory_uri() . '/includes/css/validationEngine.jquery.css' );

	wp_register_script( 'ctxphc-scripts', get_template_directory_uri() . '/includes/js/ctxphc-scripts.js', array( 'jquery' ), '', true );
	if ( is_page( 'membership' ) ) {
		wp_enqueue_script( 'ctxphc-scripts' );
		wp_enqueue_style( 'validation-style' );
	}

	wp_register_script( 'ctxphc-pb-script', get_template_directory_uri() . '/includes/js/ctxphc-pb-script.js', array( 'jquery' ), '', true );

	if ( is_page( 'pirates-ball-members-only-early-registration' ) || is_page( 'pirates-ball-early-registration' ) || is_page(
			'pirates-ball-registration' ) || is_page( 'pirates-ball-private-registration' )
	) {
		wp_enqueue_script( 'ctxphc-pb-script' );
		wp_enqueue_style( 'validation-style' );
	}

	//Register CTXPHC Custom CSS Stylesheet
	wp_register_style( 'ctxphc-custom-style', get_template_directory_uri() . '/includes/css/ctxphc-style.css' );
	wp_enqueue_style( 'ctxphc-custom-style' );

	//Register CTXPHC CSS Print Stylesheet
	wp_register_style( 'ctxphc-print-style', get_template_directory_uri() . '/includes/css/ctxphc-print-style.css', '', '', "print" );
	wp_enqueue_style( 'ctxphc-print-style' );

	//Register CTXPHC Pirates Ball Registration Custom CSS Stylesheet
	wp_register_style( 'pb_reg_styles', get_stylesheet_directory_uri() . '/includes/css/pb_reg_styles.css', array(), '1.0' );
	if ( is_page( 'pirates-ball-members-only-early-registration' ) || is_page( 'pirates-ball-early-registration' ) ||
	     is_page( 'pirates-ball-registration' ) || is_page( 'pirates-ball-private-registration' )
	) {
		wp_enqueue_style( 'pb_reg_styles' );
	}
}

add_action( 'wp_enqueue_scripts', 'reg_custom_scripts_and_styles' );

/**
 * Redirect non-admins to the current page and Administrators to the dashboard
 * after logging into the site.
 *
 * @since    1.1.1
 */
/*
function memb_login_redirect( $redirect_to, $request, $user ) {
	return ( $user->has_cap( 'delete_users' ) ) ? admin_url() : $request;
}

add_filter( 'login_redirect', 'memb_login_redirect', 10, 3 );
*/

//TODO:  Look into registration_redirect

//TODO:  Look into lostpassword_redirect


function redirect_after_logout() {
	wp_logout_url( home_url() );
}

add_filter( 'allowed_redirect_hosts', 'redirect_after_logout' );


function memb_lower_case_user_name( $name ) {
	// might be turned off
	if ( function_exists( 'sp_strtolower' ) ) {
		return mb_strtolower( $name );
	}

	return strtolower( $name );
}

add_filter( 'sanitize_user', 'memb_lower_case_user_name' );


//TODO:  Create IPN Web Accept handler Class

//Main IPN Web Accept Txn Type processing calls Membership Paypal processing class
/**
 * @param $data
 */
function cm_ipn_web_accept_payment_processing( $data ) {
	$payment_processing = new membershipPayPalPaymentProcessing( $data );
}

add_action( 'paypal-paypal_ipn_for_wordpress_txn_type_web_accept', 'cm_ipn_web_accept_payment_processing' );

function my_ipn_web_failed_payment_processing( $data ) {
	$payment_processing = new membershipPayPalPaymentProcessing( $data );
}

add_action( 'paypal-web_accept_failed', 'my_ipn_web_failed_payment_processing' );

/**
 *
 */
function get_renewal_date( $renewing = false ) {
	$current_date          = is_date_safe( date( "Y-m-d" ) );
	$curr_year             = intval( date( 'Y' ) );
	$renewal_year          = $curr_year + 1;
	$extended_renewal_year = $renewal_year + 1;
	$extend_renewal_date   = $curr_year . '-09-01';

	//todo: update this to use unix timestamps
	if ( $current_date > $extend_renewal_date ) {
		$renewal_date = $extended_renewal_year . '-01-01';
	} else {
		$renewal_date = $renewal_year . '-01-01';
	}

	return $renewal_date;
}

/**
 * @param $reg_id
 *
 * @return mixed
 */
function activate_new_member( $reg_id ) {
	global $wpdb;
	$wpdb->print_error();

}

/**
 * @param $recid
 *
 * @return array
 */
function  activate_renewing_member( $recid ) {
	Global $wpdb;
	//process renewing membership record.
	//return $renewing_memb_ids array

}


/**
 * @param $rel_id
 *
 * @return wpdb
 */
function get_membership_pricing() {
	/** @var wpdb $wpdb */
	global $wpdb;
	$cost       = $wpdb->get_results( "SELECT cost FROM ctxphc_membership_pricing" );
	$type_count = count( $cost );

	for ( $x = 1, $y = 0; $x <= $type_count; $x ++, $y ++ ) {
		$pricing[ $x ] = $cost[ $y ];
	}

	return $pricing;
}

function format_save_phone( $phone_number ) {
	return preg_replace( '/[^0-9]/', '', $phone_number );
}

function formatPhoneNumber( $phoneNumber ) {
	$phoneNumber = preg_replace( '/[^0-9]/', '', $phoneNumber );

	if ( strlen( $phoneNumber ) > 10 ) {
		$countryCode = substr( $phoneNumber, 0, strlen( $phoneNumber ) - 10 );
		$areaCode    = substr( $phoneNumber, - 10, 3 );
		$nextThree   = substr( $phoneNumber, - 7, 3 );
		$lastFour    = substr( $phoneNumber, - 4, 4 );

		$phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
	} else if ( strlen( $phoneNumber ) == 10 ) {
		$areaCode  = substr( $phoneNumber, 0, 3 );
		$nextThree = substr( $phoneNumber, 3, 3 );
		$lastFour  = substr( $phoneNumber, 6, 4 );

		$phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
	} else if ( strlen( $phoneNumber ) == 7 ) {
		$nextThree = substr( $phoneNumber, 0, 3 );
		$lastFour  = substr( $phoneNumber, 3, 4 );

		$phoneNumber = $nextThree . '-' . $lastFour;
	}

	return $phoneNumber;
}

function is_date_safe( $date ) {
	global $memb_error;

	$date        = new DateTime( $date );
	$fixed_date  = $date->format( 'Y-m-d' );
	$date_fields = explode( '-', $fixed_date );

	$safe_date = checkdate( $date_fields[ 1 ], $date_fields[ 2 ], $date_fields[ 0 ] );
	if ( $safe_date ) {
		$fixed_safe_date = sprintf( "%s-%02s-%02s", $date_fields[ 0 ], $date_fields[ 1 ], $date_fields[ 2 ] );
	} else {
		$memb_error->add( 'date', "The date was not valid.  This needs to be checked out.  The data will be stored for further review in the failed_registration table." );
		$fixed_safe_date = $memb_error->get_error_message( 'date' );
	}

	return $fixed_safe_date;
}

function load_reg_types(){
	global $reg_types;
	 $reg_types = array (
		"new" => "new",
		"renew" => "renew"	
	);
}

function reg_type_new() { 
	global $reg_types; 
	
	if (!isset($reg_types)){
		load_reg_types();
	}
	
	return $reg_types['new'];
	
}

function reg_type_renew() {
	global $reg_types;

	if (!isset($reg_types)){
		load_reg_types();
	}

	return $reg_types['renew'];

}

function get_associate_id_keys() {
	
	global $max_children;
	$id_names = array("mb","sp");
	
	for ($i=1;$i<=$max_children;++$i){
		$id_names[]= "c$i";
	}
	
	return ($id_names);
}



/**
 * @return mixed
 */
function process_registration() {
	global $prime_members_id;

	$fam_counter  = 0;
	$current_date = date( "Y-m-d" );
	$renewal_date = get_renewal_date();

	$clean_form_data      = get_clean_form_data( 'registration' );
	$clean_users_data     = $clean_form_data[ 'userdata' ];
	$clean_users_metadata = $clean_form_data[ 'metadata' ];

	foreach ( $clean_users_data as $memb_user_key => $memb_data_array ) {
		$memb_id = add_wordpress_user( $memb_data_array );

		if ( ! is_wp_error( $memb_id ) ) {
			$member_ids[] = $memb_id;
			$key_id       = $memb_user_key . '_id';

			$clean_users_metadata[ 'mb' ][ $key_id ] = $memb_id;
		}


		if ( is_wp_error( $memb_id ) ) {
			error_log_message( $memb_id->get_error_message() );
			$memb_id                                                  = null;
			$clean_users_metadata[ $memb_user_key ][ 'hatch_date' ]   = is_date_safe( $current_date );
			$clean_users_metadata[ $memb_user_key ][ 'renewal_date' ] = is_date_safe( $renewal_date );
			$clean_users_metadata[ $memb_user_key ][ 'status_id' ]    = 0; // new member registration defaults to pending

			foreach ( $memb_data_array as $mb_meta_key => $mb_meta_value ) {
				$clean_users_metadata[ 'mb' ][ $memb_user_key . '_' . $mb_meta_key ] = $mb_meta_value;
			}

			foreach ( $clean_users_metadata[ $memb_user_key ] as $mb_meta_key => $mb_meta_value ) {
				$clean_users_metadata[ 'mb' ][ $memb_user_key . '_' . $mb_meta_key ] = $mb_meta_value;
			}
		} else {
			if ( 'mb' == $memb_user_key ) {
				if ( is_null( $prime_members_id ) ) {
					$prime_members_id = $memb_id;
				}
			} else {
				// Add primary Member's Wordpress user id to each family member with a Wordpress user id.
				$clean_users_metadata[ $memb_user_key ][ 'mb_id' ] = $prime_members_id;
			}

			$hatch_date = get_user_meta( $memb_id, 'hatch_date', true);
			$reg_date = get_user_meta( $memb_id, 'reg_date', true);
			if (($hatch_date == '') ){
				if  ($reg_date == '') {
            		$clean_users_metadata[ $memb_user_key ][ 'hatch_date' ]   = is_date_safe( $current_date );
            		$clean_users_metadata[ $memb_user_key ][ 'status_id' ]    = 0; // new member registration defaults to pending
            	}else{
            		$clean_users_metadata[ $memb_user_key ][ 'hatch_date' ] = $reg_date;
            	}
            }
            
            	
            
			
			$clean_users_metadata[ $memb_user_key ][ 'renewal_date' ] = is_date_safe( $renewal_date );
			

			$clean_form_data[ 'metadata' ] = $clean_users_metadata;
		}
	}

	$i = 0;
	foreach ( $clean_users_metadata as $members_data ) {
		$user_id = $member_ids[ $i ++ ];
		$result  = add_members_metadata( $members_data, $user_id );
		if ( is_wp_error( $result ) ) {
			error_log_message( $result->get_error_message() );
		}
	}

	return $clean_form_data;
}


/**
 * @param $current_memb_id
 *
 * @return int|WP_Error
 */
function process_update_metadata() {
	global $memb_error;
	$clean_form_data      = get_clean_form_data( 'update' );
	$clean_users_data     = $clean_form_data[ 'userdata' ];
	$clean_users_metadata = $clean_form_data[ 'metadata' ];

	//check form data against existing metadata to determine if data needs to be updated
	foreach ( $clean_users_metadata as $member_key => $member_meta_array ) {
		$current_memb_id = $_POST[ $member_key . '_id' ];
		foreach ( $member_meta_array as $member_meta_key => $member_meta_value ) {
			compare_form_data_value( $current_memb_id, $member_meta_key, $member_meta_value );
		}

	}

	foreach ( $clean_users_data as $member_key => $member_user_array ) {
		$current_memb_id = $_POST[ $member_key . '_id' ];

		//Get existing Wordpress user data
		$user_info = get_userdata( $current_memb_id );
		if ( ! $user_info ) {
			$memb_error->add( 'get userdata failed', "There was a failure when getting userdata for: $current_memb_id" );
			error_log_message( $memb_error->get_error_message() );
		} else {
			foreach ( $member_user_array as $user_data_key => $user_data_value ) {
				compare_form_userdata( $user_data_key, $user_data_value, $user_info, $current_memb_id );
			}

			return $clean_form_data;
		}
	}
}

function compare_form_userdata( $user_data_key, $user_data_value, $user_info, $current_memb_id ) {
	global $memb_error;

	$keyval     = null;
	$key_fields = explode( '_', $user_data_key );
	if ( is_array( $key_fields ) ) {
		$i = 0;
		while ( $i < count( $key_fields ) ) {
			$keyval .= $key_fields[ $i ++ ];
		}
	}
	$user_key = 'user_' . $keyval;
	if ( $user_data_value != $user_info->$user_key ) {
		$result = wp_update_user( array( 'ID' => $current_memb_id, $user_key => $user_data_value ) );

		if ( is_wp_error( $result ) ) {
			$memb_error->add( 'user_data_update', "There was a failure when getting userdata for: $current_memb_id" );
			error_log_message( $memb_error->get_error_message() );
		}
	}
}


function get_clean_form_data( $type ) {
	/**
	 * Clean up input data for insertion into the Wordpress User Meetadata table.
	 *
	 * @var ARRAY $member_data
	 *
	 **/
	
	global $max_children;
	
	//register 
	$orig_meta = $_SESSION['orig_user_meta'];
	$orig_user = $_SESSION['orig_user_data'];
	
	$reg_action = ucwords( strtolower( sanitize_text_field( $_POST[ 'reg_action' ] ) ) );
	$mb_userdata = array(
		'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_first_name' ] ) ) ),
		'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_last_name' ] ) ) ),
		'email'      => is_email( strtolower( sanitize_email( wp_unslash( $_POST[ 'mb_email' ] ) ) ) ),
	);
	
	if ($orig_user['mb']->__isset('ID')){
		$mb_userdata['ID'] = $orig_user['mb']->get('ID');
	}
	
	// define $family tags
	$fam_tag_prefix = array ("mb", "sp");
	//for ($i = 1; $i <= 10; $i++)
	for ($i = 1; $i<= $max_children; $i++){
		$fam_tag_prefix[] =  "c{$i}";
	}

	$mb_metadata = array(
		'phone'           => $_POST[ 'mb_phone' ],
		'birthday'        => is_date_safe( $_POST[ 'mb_birthday' ] ),
		'occupation'      => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_occupation' ] ) ) ),
		'relationship_id' => ( isset( $_POST[ 'mb_relationship' ] ) ? intval( $_POST[ 'mb_relationship' ] ) : 1 ),
		'membership_type' => intval( $_POST[ 'memb_type' ] ),
		'addr1'           => sanitize_text_field( $_POST[ 'mb_addr1' ] ),
		'addr2'           => sanitize_text_field( $_POST[ 'mb_addr2' ] ),
		'city'            => sanitize_text_field( $_POST[ 'mb_city' ] ),
		'state'           => sanitize_text_field( $_POST[ 'mb_state' ] ),
		'zip'             => sanitize_text_field( $_POST[ 'mb_zip' ] ),
	);
	
	//transfer non form metadata to member meta
	foreach ($fam_tag_prefix as $prefix){
		
	}

	$cleaned_form_data[ 'reg_action'] = $reg_action;
	$cleaned_form_data[ 'userdata' ][ 'mb' ] = $mb_userdata;
	$cleaned_form_data[ 'metadata' ][ 'mb' ] = $mb_metadata;

	/** @var ARRAY $sp */
	if ( isset( $_POST[ 'sp_first_name' ] ) && ! empty( $_POST[ 'sp_first_name' ] ) ) {
		$sp_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'sp_email' ] ) ) ),
		);
		
		if (isset($orig_meta['sp_id'])){
			$sp_userdata['ID'] = $orig_meta['sp_id'][0];
		}

		$sp_metadata = array(
			'phone'           => $_POST[ 'sp_phone' ],
			'birthday'        => is_date_safe( $_POST[ 'sp_birthday' ] ),
			'relationship_id' => intval( $_POST[ 'sp_relationship' ] ),
		);

		if ( 'update' == $type && ! empty( $_POST[ 'sp_id' ] ) ) {
			$cleaned_form_data[ 'metadata' ][ 'mb' ][ 'sp_id' ] = intval( $_POST[ 'sp_id' ] );
			$sp_metadata[ 'mb_id' ]                             = intval( $_POST[ 'mb_id' ] );
		}

		$cleaned_form_data[ 'userdata' ][ 'sp' ] = $sp_userdata;
		$cleaned_form_data[ 'metadata' ][ 'sp' ] = $sp_metadata;
	}

	//replace explicitly named child_data processing references with loop to reduce code duplication
	//for ($c=1; $c<=$max_children; $c++){
	for ($c=1; $c<=$max_children; $c++){
		$ctag = "c{$c}";
		$udata_name = $ctag."_userdata";  #runtime array name
		$mdata_name = $ctag."_metadata";  #runtime array name
		if ( isset( $_POST[ $ctag.'_first_name' ] ) && ! empty( $_POST[ $ctag.'_first_name' ] ) ) {
			${$udata_name} = array(
				'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag.'_first_name' ] ) ) ),
				'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag.'_last_name' ] ) ) ),
				'email'      => is_email( strtolower( sanitize_email( $_POST[ $ctag.'_email' ] ) ) ),
			);
			
		if (isset($orig_meta["{$ctag}_id"][0])){
			${$udata_name}['ID'] = $orig_meta["{$ctag}_id"][0];
		} 
	
			${$mdata_name} = array(
				'birthday'        => is_date_safe( $_POST[ $ctag.'_birthday' ] ),
				'relationship_id' => intval( $_POST[ $ctag.'_relationship' ] ),
			);
	
			if ( 'update' == $type && ! empty( $_POST[ $ctag.'_id' ] ) ) {
				$cleaned_form_data[ 'metadata' ][ 'mb' ][ $ctag.'_id' ] = intval( $_POST[ $ctag.'_id' ] );
				${$mdata_name}[ 'mb_id' ] = intval( $_POST[ 'mb_id' ] );
			}
	
			$cleaned_form_data[ 'userdata' ][ $ctag ] = ${$udata_name};
			$cleaned_form_data[ 'metadata' ][$ctag]   = ${$mdata_name};
		}
	} #end child_data loop
	

	
    /* repeated code replaced by single loop above
     * *
	if ( isset( $_POST[ 'c1_first_name' ] ) && ! empty( $_POST[ 'c1_first_name' ] ) ) {
		$c1_userdata = array(
				'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'c1_first_name' ] ) ) ),
				'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'c1_last_name' ] ) ) ),
				'email'      => is_email( strtolower( sanitize_email( $_POST[ 'c1_email' ] ) ) ),
		);
	
		$c1_metadata = array(
				'birthday'        => is_date_safe( $_POST[ 'c1_birthday' ] ),
				'relationship_id' => intval( $_POST[ 'c1_relationship' ] ),
		);
	
		if ( 'update' == $type && ! empty( $_POST[ 'c1_id' ] ) ) {
			$cleaned_form_data[ 'metadata' ][ 'mb' ][ 'c1_id' ] = intval( $_POST[ 'c1_id' ] );
			$c1_metadata[ 'mb_id' ]                             = intval( $_POST[ 'mb_id' ] );
		}
		$cleaned_form_data[ 'userdata' ][ 'c1' ] = $c1_userdata;
		$cleaned_form_data[ 'metadata' ][ 'c1' ] = $c1_metadata;
	}
	
	
	
	if ( isset( $_POST[ 'c2_first_name' ] ) && ! empty( $_POST[ 'c2_first_name' ] ) ) {
		$c2_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'c2_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'c2_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'c2_email' ] ) ) ),
		);

		$c2_metadata = array(
			'birthday'        => is_date_safe( $_POST[ 'c2_birthday' ] ),
			'relationship_id' => intval( $_POST[ 'c2_relationship' ] ),
		);

		if ( 'update' == $type && ! empty( $_POST[ 'c2_id' ] ) ) {
			$cleaned_form_data[ 'metadata' ][ 'mb' ][ 'c2_id' ] = intval( $_POST[ 'c2_id' ] );
			$c2_metadata[ 'mb_id' ]                             = intval( $_POST[ 'mb_id' ] );
		}
		$cleaned_form_data[ 'userdata' ][ 'c2' ] = $c2_userdata;
		$cleaned_form_data[ 'metadata' ][ 'c2' ] = $c2_metadata;
	}

	if ( isset( $_POST[ 'c3_first_name' ] ) && ! empty( $_POST[ 'c3_first_name' ] ) ) {
		$c3_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'c3_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'c3_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'c3_email' ] ) ) ),
		);

		$c3_metadata = array(
			'birthday'        => is_date_safe( $_POST[ 'c3_birthday' ] ),
			'relationship_id' => intval( $_POST[ 'c3_relationship' ] ),
		);

		if ( 'update' == $type && ! empty( $_POST[ 'c3_id' ] ) ) {
			$cleaned_form_data[ 'metadata' ][ 'mb' ][ 'c3_id' ] = intval( $_POST[ 'c3_id' ] );
			$c3_metadata[ 'mb_id' ]                             = intval( $_POST[ 'mb_id' ] );
		}
		$cleaned_form_data[ 'userdata' ][ 'c3' ] = $c3_userdata;
		$cleaned_form_data[ 'metadata' ][ 'c3' ] = $c3_metadata;
	}

	if ( isset( $_POST[ 'c4_first_name' ] ) && ! empty( $_POST[ 'c4_first_name' ] ) ) {
		$c4_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'c4_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'c4_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'c4_email' ] ) ) ),
		);

		$c4_metadata = array(
			'birthday'        => is_date_safe( $_POST[ 'c4_birthday' ] ),
			'relationship_id' => intval( $_POST[ 'c4_relationship' ] ),
		);

		if ( 'update' == $type && ! empty( $_POST[ 'c4_id' ] ) ) {
			$cleaned_form_data[ 'metadata' ][ 'mb' ][ 'c4_id' ] = intval( $_POST[ 'c4_id' ] );
			$c4_metadata[ 'mb_id' ]                             = intval( $_POST[ 'mb_id' ] );
		}

		$cleaned_form_data[ 'userdata' ][ 'c4' ] = $c4_userdata;
		$cleaned_form_data[ 'metadata' ][ 'c4' ] = $c4_metadata;
	}
	 *
     * 
     */

	 // end obsoleted code

	return $cleaned_form_data;
}

/**
 * @param $member
 * @param $user_id
 */
function add_members_metadata( $member, $user_id ) {

	foreach ( $member as $meta_key => $meta_value ) {
		update_user_meta( $user_id, $meta_key, $meta_value );
		$result = compare_form_data_value( $user_id, $meta_key, $meta_value );
		if ( is_wp_error( $result ) ) {
			error_log_message( $result->get_error_message() );
		}
	}
}

function update_members_metadata( $user_id, $meta_key, $meta_value, $prev_value ) {
	global $memb_error;
	$result = update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );

	if ( false == $result ) {
		$memb_error->add( 'meta_update', "Metadata update failed:  $user_id, $meta_key, $meta_value, $prev_value" );
		error_log_message( $memb_error->get_error_message() );
	} elseif ( true == $result ) {
		$memb_error->add( 'meta_update', 'The metadata was updated! ' );
		error_log_message( $memb_error->get_error_message() );
	} else {
		$memb_error->add( 'meta_update', 'The metadata did not exist and was added.' );
		error_log_message( $memb_error->get_error_message() );
	}

	return $result;
}


function compare_form_data_value( $user_id, $meta_key, $form_value ) {
	global $memb_error;
	// Verify the stored value matches new or updated value
	$verify_value = get_user_meta( $user_id, $meta_key, true );
	if ( $verify_value != $form_value ) {
		$memb_error->add( 'compare_data', "The form data did not match the metadata: $user_id, $meta_key, $form_value, $verify_value" );
		error_log_message( $memb_error->get_error_message() );
		$result = update_members_metadata( $user_id, $meta_key, $form_value, $verify_value );
		if ( $result != false ) {
			$result = true;
		}

	} else {
		$result = true;
	}

	return $result;
}

function verify_userdata_value( $user_id, $meta_key, $meta_value ) {
	global $memb_error;
	// Verify the stored value matches new or updated value
	$user_info = get_userdata( $user_id );
	if ( ! $user_info ) {
		$memb_error->add( 'get userdata failed', "There was a failure when getting userdata for: $user_id" );
		$result = $memb_error;
	}

	return $result;
}

/**
 * @param $membdata
 *
 * @return int|string
 */
function add_wordpress_user( $member ) {
	global $memb_error;

	if ( ! empty( $member[ 'email' ] ) ) {
		foreach ( $member as $mkey => $mval ) {
			error_log_message( $mkey . '->' . $mval );
		}
		$userdata = array(
			'first_name'      => $member[ 'first_name' ],
			'last_name'       => $member[ 'last_name' ],
			'user_email'      => $member[ 'email' ],
			'user_login'      => ( isset( $member[ 'username' ] ) ? $member[ 'username' ] : mb_strtolower( substr( $member[ 'first_name' ], 0, 3 ) .
			                                                                                              substr( $member[ 'last_name' ], 0, 4 ) ) ),
			'nickname'        => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'display_name'    => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'user_nicename'   => $member[ 'first_name' ] . '-' . $member[ 'last_name' ],
			'user_registered' => $member[ 'reg_date' ],
		);

		
		$memb_id = (isset( $member[ 'ID' ]))?$member['ID' ] :  username_exists($userdata['user_login']);
		if ($memb_id){
			$userdata['ID']=$memb_id;
		}else{
			$userdata['user_pass'] =  ( isset( $member->pass ) ? $member->pass : wp_generate_password( $length = 12, $include_standard_special_chars = false ) );				
		}		
		
		$memb_id = wp_insert_user( $userdata );
		
	} else {
		$memb_error->add( 'no_email', 'Without an email address we cannot create a wordpress user account.' );
		$memb_id = $memb_error;
	}

	return $memb_id;
}

function get_clean_usermeta_data( $user_id ) {
	$clean_user_metadata = array_map( function ( $a ) {
		return $a[ 0 ];
	}, get_user_meta( $user_id ) );

	return $clean_user_metadata;
}

/**
 * @param $message
 * @param $debug
 */
function debug_log_message( $message, $debug = false ) {
	if ( $debug ) {
		error_log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0 );
		error_log( "!!!!!!   $message   !!!!!!", 0 );
		error_log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0 );
	}
}

/**
 * @param $message
 * @param $debug
 */
function error_log_message( $message ) {
	error_log( "###############################################################", 0 );
	error_log( "######   $message   ######", 0 );
	error_log( "###############################################################", 0 );
}


//Shortcodes to be used in pages, posts, widgets etc.

// Add Shortcode to hide email addresses from bots.
function email_cloaking_shortcode( $atts ) {

	// Attributes
	extract( shortcode_atts(
			array(
				'email' => '',
			), $atts )
	);

	// Code
	return antispambot( $email );
}

//To be added to a paypal canceled payment return page
function canceled_paypal_payment() {
	//todo: create code to do something with the user data if they cancel the registration or renewal at the paypal payment screen.
}


function new_member_paypal_welcome_processing() {
	//todo: create code needed to activate a new members registration. Including creating the wordpress user account an adding the registration data to the user_metadata
}

function register_membership_shortcodes() {
	add_shortcode( 'payment_canceled', 'canceled_paypal_payment' );
	add_shortcode( 'cloak', 'email_cloaking_shortcode' );
	add_shortcode( 'payment_completed', 'new_member_paypal_welcome_processing' );
}

add_action( 'init', 'register_membership_shortcodes' );

function list_active_members() {
	// WP_User_Query arguments
	$args = array(
		'role'       => 'Subscriber',
		'number'     => '25',
		'order'      => 'ASC',
		'orderby'    => 'user_login',
		'meta_query' => array(
			array(
				'key'     => 'hatch_date',
				'compare' => 'EXISTS',
				'type'    => 'DATETIME',
			),
		),
		'fields'     => array( 'first_name', 'last_name', 'email', 'phone', 'addr1', 'addr2', 'city', 'state', 'zip' ),
	);

// The User Query
	$user_query = new WP_User_Query( $args );
}

add_shortcode( 'list_members', 'list_active_members' );

// Register User Contact Methods that are displayed in a users profile.
function member_contact_methods( $member_contact_method ) {

	$member_contact_method[ 'address' ]  = __( 'Address', 'text_domain' );
	$member_contact_method[ 'city' ]     = __( 'City', 'text_domain' );
	$member_contact_method[ 'state' ]    = __( 'State', 'text_domain' );
	$member_contact_method[ 'zip' ]      = __( 'Zip', 'text_domain' );
	$member_contact_method[ 'Phone' ]    = __( 'Phone', 'text_domain' );
	$member_contact_method[ 'twitter' ]  = __( 'Twitter Username' );
	$member_contact_method[ 'facebook' ] = __( 'Facebook Username' );
	$member_contact_method[ 'yahoo' ]    = __( 'YAHOO Groups Username' );

	return $member_contact_method;
}

// Hook into the 'user_contactmethods' filter
add_filter( 'user_contactmethods', 'member_contact_methods' );


/* ######################################################################## */
/* a custom action hook example */

/*
 * 1. Create your own custom action hook named 'the_action_hook'
 *    with just a line of code. Yes, it's that simple.
 *
 *    The first argument to add_action() is your action hook name
 *    and the second argument is the name of the function that actually gets
 *    executed (that's 'callback function' in geek).
 *
 *    In this case you create an action hook named 'the_action_hook'
 *    and the callback function is 'the_action_callback'.
 */

add_action( 'the_action_hook', 'the_action_callback' );

/*
 * 2. Declare the callback function. It prints a sentence.
 *    Note that there is no return value.
 */

function the_action_callback() {
	echo '<p>WordPress is nice!</p>';
}

/*
 * 3. When you call do_action() with your action hook name
 *    as the argument, all functions hooked to it with add_action()
 *    (see step 1. above) get are executed - in this case there is
 *    only one, the_action_callback(), but you can attach as many functions
 *    to your hook as you like.
 *
 *    In this step we wrap our do_action() in yet another
 *    function, the_action(). You can actually skip this step and just
 *    call do_action() from your code.
 */

function the_action() {
	do_action( 'the_action_hook' );
}

function load_states_array() {
	$states_arr = array(
		'AL' => "Alabama",
		'AK' => "Alaska",
		'AZ' => "Arizona",
		'AR' => "Arkansas",
		'CA' => "California",
		'CO' => "Colorado",
		'CT' => "Connecticut",
		'DE' => "Delaware",
		'DC' => "District Of Columbia",
		'FL' => "Florida",
		'GA' => "Georgia",
		'HI' => "Hawaii",
		'ID' => "Idaho",
		'IL' => "Illinois",
		'IN' => "Indiana",
		'IA' => "Iowa",
		'KS' => "Kansas",
		'KY' => "Kentucky",
		'LA' => "Louisiana",
		'ME' => "Maine",
		'MD' => "Maryland",
		'MA' => "Massachusetts",
		'MI' => "Michigan",
		'MN' => "Minnesota",
		'MS' => "Mississippi",
		'MO' => "Missouri",
		'MT' => "Montana",
		'NE' => "Nebraska",
		'NV' => "Nevada",
		'NH' => "New Hampshire",
		'NJ' => "New Jersey",
		'NM' => "New Mexico",
		'NY' => "New York",
		'NC' => "North Carolina",
		'ND' => "North Dakota",
		'OH' => "Ohio",
		'OK' => "Oklahoma",
		'OR' => "Oregon",
		'PA' => "Pennsylvania",
		'RI' => "Rhode Island",
		'SC' => "South Carolina",
		'SD' => "South Dakota",
		'TN' => "Tennessee",
		'TX' => "Texas",
		'UT' => "Utah",
		'VT' => "Vermont",
		'VA' => "Virginia",
		'WA' => "Washington",
		'WV' => "West Virginia",
		'WI' => "Wisconsin",
		'WY' => "Wyoming",
	);

	return $states_arr;
}

function load_relationships_array() {
	$relationship_arr = array( '2' => "Spouse", '3' => "Partner", '4' => "Child", '5' => "Other" );

	return $relationship_arr;
}

function load_form_data_map () {
	$form_data_map = array(
	/*
	 "memb_type_1" => "memb_type_1",
	"memb_type_2" => "memb_type_2",
	"memb_type_3" => "memb_type_3",
	"memb_type_4" => "memb_type_4",
	*/

	
			// user_meta
			"first_name" => "first_name" ,
			"last_name" => "last_name" ,
			"email" => "email",
			"phone" => "phone",
			"birthday" => "birthday",
			"bday" => "birthday",
			//"reg_date" => "2014-10-09 07:48:32
			"addr1" => "mb_addr1",
			"addr2" => "mb_addr2",
			"city" => "mb_city",
			"state" => "mb_state",
			"zip" =>  "mb_zip",
			"occupation" => "mb_occupation",
			"mb_first_name" => "mb_first_name" ,
			"mb_last_name" => "mb_last_name" ,
			"mb_birthday" => "mb_birthday" ,
			"mb_email" => "mb_email" ,
			"mb_phone" => "mb_phone" ,
			"mb_occupation" => "mb_occupation" ,
			"mb_addr1" => "mb_addr1" ,
			"mb_addr2" => "mb_addr2" ,
			"mb_city" => "mb_city" ,
			"mb_state" => "mb_state",
			"mb_state" => "mb_state",
			"sp_first_name" => "sp_first_name" ,
			"sp_last_name" => "sp_last_name" ,
			"sp_birthday" => "sp_birthday",
			"sp_email" => "sp_email" ,
			"sp_phone" => "sp_phone" ,
			"sp_relationship" => "sp_relationship" ,

	
			"c1_first_name" => "c1_first_name" ,
			"c1_last_name" => "c1_last_name" ,
			"c1_bday" => "c1_birthday" ,
			"c1_relationship" => "c1_relationship",
			"c1_email" => "c1_email" ,
	
			"c2_first_name" => "c2_first_name" ,
			"c2_last_name" => "c2_last_name" ,
			"c2_bday" => "c2_birthday" ,
			"c2_relationship" => "c2_relationship",
			"c2_email" => "c2_email" ,
	
			"c3_first_name" => "c3_first_name" ,
			"c3_last_name" => "c3_last_name" ,
			"c3_bday" => "c3_birthday" ,
			"c3_relationship" => "c3_relationship",
			"c3_email" => "c3_email" ,
	
			"c4_first_name" => "c4_first_name" ,
			"c4_last_name" => "c4_last_name" ,
			"c4_bday" => "c4_birthday" ,
			"c4_relationship" => "c4_relationship",
			"c4_email" => "c4_email"
	);
	
	return ($form_data_map);
}


add_filter( 'body_class', 'browser_body_class' );
function browser_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if ( $is_lynx ) {
		$classes[] = 'lynx';
	} elseif ( $is_gecko ) {
		$classes[] = 'gecko';
	} elseif ( $is_opera ) {
		$classes[] = 'opera';
	} elseif ( $is_NS4 ) {
		$classes[] = 'ns4';
	} elseif ( $is_safari ) {
		$classes[] = 'safari';
	} elseif ( $is_chrome ) {
		$classes[] = 'chrome';
	} elseif ( $is_IE ) {
		$classes[] = 'ie';
	} else {
		$classes[] = 'unknown';
	}

	if ( $is_iphone ) {
		$classes[] = 'iphone';
	}

	return $classes;
}