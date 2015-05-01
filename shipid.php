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


require_once plugin_dir_path(__FILE__) . '../woocommerce/includes/abstracts/abstract-wc-settings-api.php';
require_once plugin_dir_path(__FILE__) . '../woocommerce/includes/abstracts/abstract-wc-shipping-method.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shipid-activator.php
 */
function activate_shipid() {
    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        die('Woocommerce is not active');
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shipid-deactivator.php
 */
function deactivate_shipid() {
    
}

// global $woocommerce;

register_activation_hook( __FILE__, 'activate_shipid' );
register_deactivation_hook( __FILE__, 'deactivate_shipid' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
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
    
    
    
    wp_dequeue_script('wc-country-select');
    wp_enqueue_script('wc-country-select', plugin_dir_url(__FILE__) . 'assets/js/country-select.js');
    
    wp_enqueue_script('shipid-admin-order', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'));
}

add_action( 'woocommerce_shipping_init', 'run_shipid' );

function admin_assets() {
    if ( isset($_GET['section']) && $_GET['section'] == 'shipid') {
        wp_enqueue_script('jquery-ui-autocomplete ');
        wp_enqueue_script('shipid-admin', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'));
        wp_localize_script('shipid-admin', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    }
    
    wp_enqueue_script('shipid-admin-order', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin-order.js', array('jquery'));
    wp_localize_script('shipid-admin-order', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    
    wp_dequeue_script('wc-country-select');
    wp_enqueue_script('wc-country-select', plugin_dir_url(__FILE__) . 'assets/js/country-select.js');
    
}

add_action( 'admin_enqueue_scripts', 'admin_assets' );

function add_shipid_method( $methods ) {
    $methods[] = 'Shipid'; 
    return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'add_shipid_method' );


function myplugin_plugin_path() {
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

add_filter( 'woocommerce_locate_template', 'shipid_locate_template', 10, 3 );

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

function public_assets($hook)
{
    wp_enqueue_style('shipid-public-style', plugin_dir_url(__FILE__) . 'assets/css/shipid-public.css');

    wp_enqueue_script('shipid-public-js', plugin_dir_url(__FILE__) . 'assets/js/shipid-public.js', array('jquery'), '1.0', true);
    // wp_enqueue_script('parallax', plugin_dir_url(__FILE__) . 'assets/js/parallax.js', array('jquery'), '1.3.1', true);

    wp_localize_script('shipid-public-js', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
    
    
    
}

add_action( 'wp_enqueue_scripts', 'public_assets', 100 );


function get_province() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://rajaongkir.com/api/starter/province",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: API_KEY_ANDA"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response_array = json_decode($response);
        $province = array();
        foreach ($response_array->rajaongkir->results as $key => $val) {
            $province[] = array(
                'id'=>$val->province_id . '+' . $val->province,
                'value'=>$val->province
            );
        }

        echo json_encode($province);
    }

    wp_die();
}

add_action('wp_ajax_shipid-get-province', 'get_province');
add_action('wp_ajax_nopriv_shipid-get-province', 'get_province');


function get_city() {
    $province = explode('|', $_GET['province']);
    $province_id = $province[0];
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
        // CURLOPT_URL => "http://rajaongkir.com/api/starter/city?province=" . $_GET['province'],
        CURLOPT_URL => "http://rajaongkir.com/api/starter/city?province=" . $province_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: 91ecfdccb664c29f9a82115fbd674139"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response_array = json_decode($response);
        $city = array();
        foreach ($response_array->rajaongkir->results as $key => $val) {
            $city[] = array(
                'id'=>$val->city_id . '+' . $val->type . ' ' . $val->city_name,
                'value'=>$val->type . ' ' . $val->city_name
            );
        }

        echo json_encode($city);
    }

    

    wp_die();
}

add_action('wp_ajax_shipid-get-city', 'get_city');
add_action('wp_ajax_nopriv_shipid-get-city', 'get_city');


function get_selected_shipping_location() {
    $shipping_state = WC()->customer->get_shipping_state();
    $shipping_city = WC()->customer->get_shipping_city();

    echo json_encode(array('province'=>$shipping_state, 'city'=>$shipping_city));
    
    wp_die();
}

add_action('wp_ajax_nopriv_shipid-get-selected-shipping-location', 'get_selected_shipping_location');
add_action('wp_ajax_shipid-get-selected-shipping-location', 'get_selected_shipping_location');


function enable_city($enable_reverse)
{
    if(!$enable_reverse) {
        return true;
    }
}
    
add_filter( 'woocommerce_shipping_calculator_enable_city', 'enable_city', 1, 1 );



function custom_checkout_fields($fields)
{
    $new_fields = array();

    foreach ($fields['billing'] as $k => $v) {
        if($k == 'billing_city') {
            
            $new_fields['billing']['billing_state'] = array(
                'type'=>'shipid_select',
                'label'=>'Province',
                'class'=>array('billing_state_opt'),
                'required'=>true,
                'clear'=>true 
            );
        } elseif($k == 'billing_state') {
            
            $new_fields['billing']['billing_city'] = array(
                'type'=>'shipid_select',
                'label'=>'City',
                'class'=>array('billing_city_opt'),
                'required'=>true,
                'clear'=>true
            );
        }
            
        $new_fields['billing'][$k] = $v;

    }

    foreach ($fields['shipping'] as $k => $v) {
        if($k == 'shipping_city') {
            
            $new_fields['shipping']['shipping_state'] = array(
                'type'=>'shipid_select',
                'label'=>'Province',
                'id'=>'shipping_state',
                'class'=>array('shipping_state_opt'),
                'required'=>true,
                'clear'=>true
            );
        } elseif($k == 'shipping_state') {
            
            $new_fields['shipping']['shipping_city'] = array(
                'type'=>'shipid_select',
                'label'=>'City',
                'id'=>'shipping_city',
                'class'=>array('shipping_city_opt'),
                'required'=>true,
                'clear'=>true
            );
        }
            
        $new_fields['shipping'][$k] = $v;

    }

    unset($new_fields['billing']['billing_state']['validate']);
    unset($new_fields['shipping']['shipping_state']['validate']);

    $new_fields['account'] = $fields['account'];
    $new_fields['order'] = $fields['order'];
    

    return $new_fields;
}

add_filter('woocommerce_checkout_fields', 'custom_checkout_fields');


function shipid_hidden_fields($a, $key, $args, $value)
{
    echo '<input type="hidden" name="' . $key . '" id="' . $key . '">';
}

add_filter('woocommerce_form_field_shipid_hidden', 'shipid_hidden_fields', 10, 4);

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

function format_billing_address($address, $order) {
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

add_filter('woocommerce_order_formatted_billing_address', 'format_billing_address', 10, 2);

function format_shipping_address($address, $order) {
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

add_filter('woocommerce_order_formatted_shipping_address', 'format_shipping_address', 10, 2);
add_filter('woocommerce_my_account_my_address_formatted_address', 'format_shipping_address', 10, 2);


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