<?php
/**
 * Plugin Name: CTXPHC PB Reg
 * Plugin URI: http://www.kaptkaos.com/
 * Description: Adds Pirate's ball registration fields to Woocommerce check page.
 * Version: 1.0.0
 * Author: Kapt Kaos
 * Author URI: http://kaptkaos.com
 * Requires at least: 4.1
 * Tested up to: 4.3
 * Date: 5/27/2016
 * Time: 11:56 AM
 */

global $pb_attendee_field_lbls;

	$pb_attendee_field_lbls = array(
		'first_name_2'       => 'First Name',
		'last_name_2'        => 'Last Name',
		'club_affiliation_2' => 'Club Affiliation',
		'cc_cruise_2'        => 'Castaway Cruise',
		'first_name_3'       => 'First Name',
		'last_name_3'        => 'Last Name',
		'club_affiliation_3' => 'Club Affiliation',
		'cc_cruise_3'        => 'Castaway Cruise',
		'first_name_4'       => 'First Name',
		'last_name_4'        => 'Last Name',
		'club_affiliation_4' => 'Club Affiliation',
		'cc_cruise_4'        => 'Castaway Cruise',
	);

/**
* Define Pirate's Ball Attendee's checkout fields
 */
 add_filter( 'woocommerce_checkout_fields', 'add_pb_attendee_checkout_fields' );

function add_pb_attendee_checkout_fields( $fields ) {
	$fields[ 'attendees' ] = array(
		'attendee_2_fields' => array(
			'first_name_2'       => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'First Name', 'woocommerce' ),
			),
			'last_name_2'        => array(
				'type'  => 'text',
				'class' => array( 'form-row-last' ),
				'label' => __( 'Last Name', 'woocommerce' ),
			),
			'club_affiliation_2' => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'Club Affiliation', 'woocommerce' ),
			),
			'cc_cruise_2'        => array(
				'type'    => 'select',
				'class' => array( 'form-row-last' ),
				'options' => array(
					'' => __( 'Please Select Yes or No.'),
					'y' => __( 'Yes' ),
					'n' => __( 'No' ),
				),
				'label'   => __( 'Castaway Cruise', 'woocommerce' ),
			),
		),

		'attendee_3_fields' => array(
			'first_name_3'       => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'First Name', 'woocommerce' ),
			),
			'last_name_3'        => array(
				'type'  => 'text',
				'class' => array( 'form-row-last' ),
				'label' => __( 'Last Name', 'woocommerce' ),
			),
			'club_affiliation_3' => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'Club Affiliation', 'woocommerce' ),
			),
			'cc_cruise_3'        => array(
				'type'    => 'select',
				'class' => array( 'form-row-last' ),
				'options' => array(
					'' => __( 'Please Select Yes or No.'),
					'y' => __( 'Yes' ),
					'n' => __( 'No' ),
				),
				'label'   => __( 'Castaway Cruise', 'woocommerce' ),
			),
		),

		'attendee_4_fields' => array(
			'first_name_4'       => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'First Name', 'woocommerce' ),
			),
			'last_name_4'        => array(
				'type'  => 'text',
				'class'     => array('form-row-last'),
				'label' => __( 'Last Name', 'woocommerce' ),
			),
			'club_affiliation_4' => array(
				'type'  => 'text',
				'class'     => array('form-row-first'),
				'label' => __( 'Club Affiliation', 'woocommerce' ),
			),
			'cc_cruise_4'        => array(
				'type'    => 'select',
				'class' => array( 'form-row-last' ),
				'options' => array(
					'' => __( 'Please Select Yes or No.'),
					'y' => __( 'Yes' ),
					'n' => __( 'No' ),
				),
				'label'   => __( 'Castaway Cruise', 'woocommerce' ),
			),
		),
	);

	return $fields;
}


/**
 * Add the fields to the checkout form
 **/
add_action( 'woocommerce_after_order_notes', 'display_pb_attendee_checkout_fields' );

function display_pb_attendee_checkout_fields() {  //This is working
	$checkout = WC()->checkout(); ?>
	<div class="pb-reg-wc-fields">
	<div class="spacer"></div>
	<h3><?php _e( 'Pirate\'s Ball Attendees' ); ?></h3>

	<div class="attendee_fields">
		<?php
		// because of this foreach, everything added to the array in the previous function will display automagically
		$attendees = $checkout->checkout_fields[ 'attendees' ];
		foreach ( $attendees as $attendee ) :
			foreach ( $attendee as $key => $field ) :
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			endforeach; ?>
			<div class="spacer"></div>
		<?php endforeach; ?>
	</div>
<?php }

/**
 * Update the user meta with field value
 **/
 /**
* add_action('woocommerce_checkout_update_user_meta', 'pb_attendee_field_update_user_meta');
 */
function pb_attendee_field_update_user_meta( $user_id ) {
	global $pb_attendee_field_lbls;
	foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_label ){
		if ($user_id && $_POST['my_field_name']) {
		//update user meta with attendee checkout data
			update_user_meta( $user_id, $pb_attendee_field, esc_attr($_POST[$pb_attendee_field]) );
		}
	}
}

/**
 * Update the order meta with field values
 **/
add_action('woocommerce_checkout_update_order_meta', 'pb_attendee_field_update_order_meta');

function pb_attendee_field_update_order_meta( $order_id ) {
	global $pb_attendee_field_lbls;

	foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_label ){
		if ( ! empty( $_POST[ $pb_attendee_field ] ) ){
			//update order meta with attendee data
			update_post_meta( $order_id, $pb_attendee_field, esc_attr($_POST[$pb_attendee_field]));
		}
	}
}

//Save the PB Reg fields
//add_action( 'woocommerce_process_shop_order_meta', 'ctxphc_save_extra_details', 45, 2 );

function ctxphc_save_extra_details( $post_id ) {
	foreach ($_POST as $key => $value ) {
		//process shop order meta
		update_post_meta( $post_id, "_{$key}", wc_clean( $_POST[ $key ] ) );
	}
}

// save the extra field when checkout is processed
//add_action( 'woocommerce_checkout_update_order_meta', 'save_pb_attendee_checkout_fields', 10, 2 );

function save_pb_attendee_checkout_fields( $order_id, $pb_attendee_field_lbls ) {
	global $pb_attendee_field_lbls;

	foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_label) :

		// don't forget to appropriately sanitize your fields if you are using a different field type
		if ( isset( $_POST[ $pb_attendee_field ] ) ) {
			update_post_meta( $order_id, $pb_attendee_field, sanitize_text_field( $_POST[ $pb_attendee_field ] ) );
		}

		if ( isset( $_POST[ $pb_attendee_field ] ) && in_array( $_POST[ $pb_attendee_field ], array( 'y', 'n' ) ) ) {
			update_post_meta( $order_id, $pb_attendee_field, $_POST[ $pb_attendee_field ] );
		}

	endforeach;
}

// display the extra data on order received page and my-account order review
add_action( 'woocommerce_thankyou', 'display_pb_attendee_order_data', 20 );
add_action( 'woocommerce_view_order', 'display_pb_attendee_order_data', 20 );

function display_pb_attendee_order_data( $order_id ) { //This is working(no data is displayed yet)
	global $pb_attendee_field_lbls; ?>

	<h2><?php _e( "Pirate's Ball Attendee's Info" ); ?></h2>
	<table class="shop_table shop_table_responsive additional_info">
		<tbody>
		<?php foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_label ) : ?>
			<tr>
				<th><?php _e( $pb_attendee_label . ':' ); ?></th>
				<td><?php echo get_post_meta( $order_id, $pb_attendee_field, true ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php }


// display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'ctxphc_display_order_data_in_admin' );

function ctxphc_display_order_data_in_admin( $order ) { //Do not know if this is working yet or not
	global $pb_attendee_field_lbls; ?>

	<div class="order_data_column">
		<h4><?php _e( 'Extra Details', 'woocommerce' ); ?><a href="#" class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a>
		</h4>
		<div class="address">
			<?php foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_field_lbl ) :
				echo '<p><strong>' . __( $pb_attendee_field_lbl ) . ':</strong>' . get_post_meta( $order->id, $pb_attendee_field, true ) . '</p>';
			endforeach; ?>
		</div>
		<div class="edit_address">
			<?php foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_field_lbl ) :
				woocommerce_wp_text_input( array(
					'id'            => $pb_attendee_field,
					'label'         => _e( $pb_attendee_field_lbl ),
					'wrapper_class' => '_billing_company_field',
				) );
			endforeach; ?>
		</div>
	</div>
	<?php
}

// WooCommerce 2.3+
//add_filter( 'woocommerce_email_order_meta_fields', 'ctxphc_email_order_meta_fields', 10, 3 );

function ctxphc_email_order_meta_fields( $fields, $order ) {

	global $pb_attendee_field_lbls;

	foreach ( $pb_attendee_field_lbls as $pb_attendee_field => $pb_attendee_label ) {
		$fields[ $pb_attendee_field ] = array(
			'label' => _e( $pb_attendee_label ),
			'value' => get_post_meta( $order->id, $pb_attendee_field, true ),
		);
	}

	return $fields;
}