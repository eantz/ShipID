<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://destiyadian.com
 * @since             1.0.0
 * @package           shipid
 *
 * @wordpress-plugin
 * Plugin Name:       ShipID- Woocommerce Shipping for Indonesian Courier
 * Plugin URI:        http://destiyadian.com/shipid-uri/
 * Description:       This is shipping plugin for Indonesian Courier (currently only support JNE)
 * Version:           0.1.0
 * Author:            @eantz
 * Author URI:        http://destiyadian.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shipid
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// RajaOngkir access class
require_once plugin_dir_path(__FILE__) . 'rajaongkiraccess.class.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shipid-activator.php
 */
function activate_shipid() {
    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        die(__('Woocommerce is not active', 'shipid'));
    }

    if(RajaOngkirAccess::$api_key == '') {
        die(__('Please set your RajaOngkir API KEY', 'shipid'));
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shipid-deactivator.php
 */
function deactivate_shipid() {
    delete_option('woocommerce_shipid_setting');
}

register_activation_hook( __FILE__, 'activate_shipid' );
register_deactivation_hook( __FILE__, 'deactivate_shipid' );


/**
 * The core plugin class that is extending functionality of Woocommerce shipping
 */
require plugin_dir_path( __FILE__ ) . 'shipid.class.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_shipid() {

    $plugin = new Shipid();
    $plugin->api_key = $key;
    
    
    // disable woocommerce country select.
    wp_dequeue_script('wc-country-select');
    wp_enqueue_script('wc-country-select', plugin_dir_url(__FILE__) . 'assets/js/country-select.js');
    
    // wp_enqueue_script('shipid-admin-order', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'));
}

add_action( 'woocommerce_shipping_init', 'run_shipid' );

/*
* Add shipping method in woocommerce shipping page
*/
function add_shipid_method( $methods ) {
    $methods[] = 'ShipID'; 
    return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'add_shipid_method' );


/*
* These functions and filter allow plugin to override woocommerce template,
* just like in themes
*
* Credit : https://www.skyverge.com/blog/override-woocommerce-template-file-within-a-plugin/
*/
function myplugin_plugin_path() {
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

function shipid_locate_template( $template, $template_name, $template_path ) {
 
    global $woocommerce;

    $_template = $template;

    if ( ! $template_path ) $template_path = $woocommerce->template_url;

    $plugin_path  = myplugin_plugin_path() . '/woocommerce/';


    // Look within passed path within the theme - this is priority

    $template = locate_template(
        array(
          $template_path . $template_name,
          $template_name
        )
    );

    // Modification: Get the template from this plugin, if it exists
    if ( ! $template && file_exists( $plugin_path . $template_name ) )
        $template = $plugin_path . $template_name;

    // Use default template

    if ( ! $template )
        $template = $_template;

    // Return what we found
    return $template;
 
}

add_filter( 'woocommerce_locate_template', 'shipid_locate_template', 10, 3 );



/*
* Enqueue script for admin
*/
function admin_assets() {
    // if ( isset($_GET['section']) && $_GET['section'] == 'shipid') {
    //     wp_enqueue_script('jquery-ui-autocomplete ');
    //     wp_enqueue_script('shipid-admin', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'));
    //     wp_localize_script('shipid-admin', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    // }
    
    // wp_enqueue_script('shipid-admin-order', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin-order.js', array('jquery'));
    // wp_localize_script('shipid-admin-order', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    
    wp_dequeue_script('wc-country-select');
    wp_enqueue_script('wc-country-select', plugin_dir_url(__FILE__) . 'assets/js/country-select.js');

    wp_enqueue_script('shipid-admin', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'), '1.0', true);
    wp_localize_script('shipid-admin', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    
}

add_action( 'admin_enqueue_scripts', 'admin_assets' );


/*
* Enqueue style and scripts for used in main page
*/
function public_assets($hook)
{

    wp_enqueue_style('shipid-public-style', plugin_dir_url(__FILE__) . 'assets/css/shipid-public.css');

    wp_enqueue_script('shipid-public-js', plugin_dir_url(__FILE__) . 'assets/js/shipid-public.js', array('jquery'), '1.0', true);

    wp_localize_script('shipid-public-js', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    
}

add_action( 'wp_enqueue_scripts', 'public_assets', 100 );



/*
* Register ajax handler for get_province
*/

add_action('wp_ajax_shipid-get-province', array('RajaOngkirAccess', 'get_province'));
add_action('wp_ajax_nopriv_shipid-get-province', array('RajaOngkirAccess', 'get_province'));

/*
* Register ajax handler for get_city
*/
add_action('wp_ajax_shipid-get-city', array('RajaOngkirAccess', 'get_city'));
add_action('wp_ajax_nopriv_shipid-get-city', array('RajaOngkirAccess', 'get_city'));


/*
* Register ajax handler for customer shipping location
*/
function get_selected_shipping_location() {
    $shipping_state = WC()->customer->get_shipping_state();
    $shipping_city = WC()->customer->get_shipping_city();

    echo json_encode(array('province'=>$shipping_state, 'city'=>$shipping_city));
    
    wp_die();
}

add_action('wp_ajax_nopriv_shipid-get-selected-shipping-location', 'get_selected_shipping_location');
add_action('wp_ajax_shipid-get-selected-shipping-location', 'get_selected_shipping_location');

function get_selected_billing_location() {
    $shipping_state = WC()->customer->get_state();
    $shipping_city = WC()->customer->get_city();

    $shipping_state = $shipping_state == '' ? WC()->customer->get_shipping_state() : $shipping_state;
    $shipping_city = $shipping_city == '' ? WC()->customer->get_shipping_city() : $shipping_city;

    echo json_encode(array('province'=>$shipping_state, 'city'=>$shipping_city));
    
    wp_die();
}

add_action('wp_ajax_nopriv_shippid-get-selected-billing-location', 'get_selected_billing_location');
add_action('wp_ajax_shipid-get-selected-billing-location', 'get_selected_billing_location');

/*
* Allow user to select city in Shipping Calculator
*/
function enable_city($enable_reverse)
{
    if(!$enable_reverse) {
        return true;
    }
}
    
add_filter( 'woocommerce_shipping_calculator_enable_city', 'enable_city', 1, 1 );


/*
* Custom checkout fields
*/
function custom_checkout_fields($fields)
{
    $new_fields = array();

    foreach ($fields as $key => $val) {
        if($key == 'billing' || $key == 'shipping') {
            foreach ($val as $k => $v) {
                if($k == $key . '_city') {
                    $new_fields[$key][$key . '_state'] = array(
                        'type'=>'shipid_select',
                        'label'=>__('Province', 'shipid'),
                        'class'=>array($key . '_state_opt'),
                        'required'=>true,
                        'clear'=>true 
                    );
                } elseif($k == $key . '_state') {
                    $new_fields[$key][$key . '_city'] = array(
                        'type'=>'shipid_select',
                        'label'=>__('City', 'shipid'),
                        'class'=>array($key . '_city_opt'),
                        'required'=>true,
                        'clear'=>true
                    );
                }

                $new_fields[$key][$k] = $v;
            }
        }
    }

    unset($new_fields['billing']['billing_state']['validate']);
    unset($new_fields['shipping']['shipping_state']['validate']);

    $new_fields['account'] = $fields['account'];
    $new_fields['order'] = $fields['order'];
    

    return $new_fields;
}

add_filter('woocommerce_checkout_fields', 'custom_checkout_fields');

/*
* Add new type for woocommerce field
*/
function shipid_select($a, $key, $args, $value)
{
    $options = $field = '';

	$field = '<p class="form-row form-row-wide ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';

	if ( $args['label'] ) {
		$field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
	}

	$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="'.esc_attr( implode( ' ', $args['input_class'] ) ) .'" ' . ' placeholder="' . esc_attr( $args['placeholder'] ) . '"></select>';

	if ( $args['description'] ) {
		$field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
	}

	$field .= '</p>' . $after;
	
	echo $field;
}

add_filter('woocommerce_form_field_shipid_select', 'shipid_select', 10, 4);



/*
* Format address to be displayed
*/
function format_address($address, $order) {
    $city = explode('+', $address['city']);
    $state = explode('+', $address['state']);
    $formatted = array(
        'first_name'    => $address['first_name'],
		'last_name'     => $address['last_name'],
		'company'       => $address['company'],
		'address_1'     => $address['address_1'],
		'address_2'     => $address['address_2'],
		'city'          => $city[1],
		'state'         => $state[1],
		'postcode'      => $address['postcode'],
		'country'       => $address['country']
	);

	return $formatted;
    
}

add_filter('woocommerce_order_formatted_billing_address', 'format_address', 10, 2);
add_filter('woocommerce_order_formatted_shipping_address', 'format_address', 10, 2);
add_filter('woocommerce_my_account_my_address_formatted_address', 'format_address', 10, 2);


/*
* Custom Billing fields in admin area
*/
function custom_admin_billing_fields($address) {
    global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
    
    $new_address = array();
    foreach($address as $k => $v) {
        if(!($k == 'city' || $k == 'country' || $k == 'state')) {
            $new_address[$k] = $v;
        }
    }
    
    $new_address['country'] = array(
        'label'=>'Country',
        'show'=>false,
        'value'=>'Indonesia'
    );
    
    $state_val = explode('+', get_post_meta( $thepostid, '_billing_state', true ));
    
    $new_address['state'] = array(
        'label'=>'State/Province',
        'show'=>false,
        'class'=>'admin_billing_select_state',
        'type'=>'select',
        'options'=>array(implode($state_val, '+')=>$state_val[1])
    );
    $city_val = explode('+', get_post_meta( $thepostid, '_billing_city', true ));
    $new_address['city'] = array(
        'label'=>'City',
        'show'=>false,
        'class'=>'admin_billing_select_city',
        'type'=>'select',
        'options'=>array(implode($city_val, '+')=>$city_val[1])
    );
    
    
    $address = $new_address;
    
    return $address;
}

add_filter('woocommerce_admin_billing_fields', 'custom_admin_billing_fields', 10, 1);


/*
* Custom Shipping fields in admin area
*/
function custom_admin_shipping_fields($address) {
    global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
    
    $new_address = array();
    foreach($address as $k => $v) {
        if(!($k == 'city' || $k == 'country' || $k == 'state')) {
            $new_address[$k] = $v;
        }
    }
    
    
    $new_address['country'] = array(
        'label'=>'Country',
        'show'=>false,
        'value'=>'Indonesia'
    );
    
    $state_val = explode('+', get_post_meta( $thepostid, '_shipping_state', true ));
    
    $new_address['state'] = array(
        'label'=>'State/Province',
        'show'=>false,
        'class'=>'admin_shipping_select_state',
        'type'=>'select',
        'options'=>array(implode($state_val, '+')=>$state_val[1])
    );
    $city_val = explode('+', get_post_meta( $thepostid, '_shipping_city', true ));
    $new_address['city'] = array(
        'label'=>'City',
        'show'=>false,
        'class'=>'admin_shipping_select_city',
        'type'=>'select',
        'options'=>array(implode($city_val, '+')=>$city_val[1])
    );
    
    
    $address = $new_address;
    
    return $address;
}

add_filter('woocommerce_admin_shipping_fields', 'custom_admin_shipping_fields', 10, 1);


/*
* Custom address fields in My Account
*/
function custom_edit_address($address) {
    
    $type = 'billing';
    
    $new_address = array();
    
    $first = true;
    foreach($address as $k => $v) {
        if($first) {
            $type_array = explode('_', $k);
            $type = $type_array[0];
            $first = false;
        }
        
        if($k == $type . '_city') {
            $new_address[$type . '_state'] = array(
                'type'=>'shipid_select',
                'label'=>'Province',
                'id'=>$type . '_state',
                'class'=>array($type . '_state_opt', 'form-row-wide'),
                'required'=>true,
                'clear'=>true
            );
        } elseif($k == $type . '_state') {
            $new_address[$type . '_city'] = array(
                'type'=>'shipid_select',
                'label'=>'City',
                'id'=>$type . '_city',
                'class'=>array($type . '_city_opt', 'form-row-wide'),
                'required'=>true,
                'clear'=>true
            );
        } else {
            if($k == $type . '_postcode') $v['class']=array('form-row-wide');
            $new_address[$k] = $v;
        }
    }
    
    return $new_address;
}

add_filter('woocommerce_address_to_edit', 'custom_edit_address', 10, 1);


/*
* Custom customer profile fields
*/
function custom_customer_profile($address) {
    $current = array();

    $current['billing_city'] = explode('+', get_user_meta( $_GET['user_id'], 'billing_city', true ));
    $current['billing_state'] = explode('+', get_user_meta( $_GET['user_id'], 'billing_state', true ));
    $current['shipping_city'] = explode('+', get_user_meta( $_GET['user_id'], 'shipping_city', true ));
    $current['shipping_state'] = explode('+', get_user_meta( $_GET['user_id'], 'shipping_state', true ));
    

    $new_address = array();
    foreach($address as $k=>$v) {
        $type = $k;
        $new_address[$k]['fields'] = array();
        
        foreach($v['fields'] as  $key => $val) {
            if(!($key == $type . '_city' || $key ==  $type . '_country' || $key == $type . '_state')) {
                $new_address[$k]['fields'][$key] = $val;
            }    
        }
        
        $new_address[$k]['title'] = $v['title'];
        $new_address[$k]['fields'][$type . '_country'] = $v[$type . '_country'];
        $new_address[$k]['fields'][$type . '_state'] = array(
            'description'=>'State/County or state code',
            'label'=>'State/Province',
            'class'=>'admin_' . $type . '_select_state',
            'type'=>'select',
            'options'=>array(implode($current[$type.'_state'], '+')=>$current[$type.'_state'][1])
        );
        $new_address[$k]['fields'][$type . '_city'] = array(
            'description'=>'State/County or state code',
            'label'=>'State/Province',
            'class'=>'admin_' . $type . '_select_city',
            'type'=>'select',
            'options'=>array(implode($current[$type.'_city'], '+')=>$current[$type.'_city'][1])
        );
        
    }

    return $new_address;
}
add_filter('woocommerce_customer_meta_fields', 'custom_customer_profile', 10, 1);