<?php
// dependencies
require_once plugin_dir_path(__FILE__) . '../woocommerce/includes/abstracts/abstract-wc-settings-api.php';
require_once plugin_dir_path(__FILE__) . '../woocommerce/includes/abstracts/abstract-wc-shipping-method.php';

/*
* This class extending WC_Shipping_Method to allow register new shipping method
*/
class Shipid extends WC_Shipping_Method {


    public function __construct() {

        $this->id                   = 'shipid';
        $this->title                = __( 'ShipID', 'shipid' );
        $this->method_description   = __( 'ShipID is a shipping plugin for woocommerce. This shipping plugin only work if the origin and destination country is Indonesia. Shipid use JNE', 'shipid' );
        $this->enabled              = $this->get_option('enabled'); 

        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init() {
        $this->init_admin_page();
    }



    function init_admin_page()
    {
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings

        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                            'title'         => __( 'Enable/Disable', 'shipid' ),
                            'type'          => 'checkbox',
                            'label'         => __( 'Enable this shipping method', 'shipid' ),
                            'default'       => 'no',
                            'id'            => 'shippid_enabled'
                        ),
            'title' => array(
                            'title'         => __( 'Method Title', 'shipid' ),
                            'type'          => 'text',
                            'description'   => __( 'This controls the title which the user sees during checkout.', 'shipid' ),
                            'default'       => __( 'Shipid JNE', 'shipid' ),
                            'desc_tip'      => true,
                            'id'            => 'shippid_title'
                        ),
            'origin_prov' => array(
                            'title'         =>  __('Origin Province', 'shipid'),
                            'type'          =>  'select',
                            'description'   =>  __('This is the origin province of the seller.', 'shipid'),
                            'desc_tip'      =>  true,
                            'class'         =>  'origin_prov',
                            'id'            => 'shippid_origin_prov',
                            'options'       => array($this->get_option('origin_prov') => $this->get_option('origin_prov'))
                        ),
            'origin_city' => array(
                            'title'         =>  __('Origin City', 'shipid'),
                            'type'          =>  'select',
                            'description'   =>  __('This is the origin city of the seller.', 'shipid'),
                            'desc_tip'      =>  true,
                            'class'         =>  'origin_city',
                            'id'            => 'shippid_origin_city',
                            'options'       => array($this->get_option('origin_city') => $this->get_option('origin_city'))
                        ),
            );

    }

    public function is_available( $package ) {
        $is_available = true;
        
        if ( $this->enabled == 'no' ) {
            $is_available =  false;
        }

        if ( $package['destination']['country'] != 'ID' ) {
            $is_available =  false;
        }

        return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
    }

    /**
    * calculate_shipping function.
    *
    * @access public
    * @param mixed $package
    * @return void
    */
    public function calculate_shipping( $package ) {
        add_filter( 'woocommerce_shipping_calculator_enable_city', 'enable_city', 1, 1 );

        if($package['destination']['city'] != '') {
            $origin_city = explode("+", $this->get_option('origin_city'));
            $origin_city_id = $origin_city[0];
            
            $city = explode("+", $package['destination']['city']);
            $city_id = $city[0];

            
            $total_weight = 0;

            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                $_product = $values['data'];

                $total_weight += $_product->get_weight() * $values['quantity'];
            }

            
            $total_weight = $total_weight * 1000;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://rajaongkir.com/api/starter/cost",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "origin=" . $origin_city_id . "&destination=" . $city_id . "&weight=" . $total_weight . "&courier=jne",
                CURLOPT_HTTPHEADER => array(
                    "key: " . RajaOngkirAccess::$api_key
                ),
            ));

            $response = curl_exec($curl);


            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                wc_add_notice( __('Cannot contacting Raja Ongkir API, please try again later', 'shipid'), 'error' ); 
                WC()->shipping->reset_shipping();
                return;
            }

            $response_array = json_decode($response);
            $method_option = array();

            if(empty($response_array->rajaongkir->results[0]->costs)) {
                wc_add_notice( __('We cannot send to this location', 'shipid'), 'error' ); 
                WC()->shipping->reset_shipping();
                return;
            }

            foreach ($response_array->rajaongkir->results[0]->costs as $key => $val) {
                $method_option[$val->service] = $val->cost[0];
            }

            $method_sort = array('REG', 'YES', 'OKE');

            foreach ($method_sort as $key => $val) {
                if(isset($method_option[$val])) {
                    $rate = array(
                        'id'    => $this->id,
                        'label' => 'JNE Reguler',
                        'cost'  => $method_option[$val]->value,
                    );

                    $this->add_rate($rate);

                    break;
                }
            }

            return;
            
        }
        
        wc_add_notice( __('Please set your city', 'shipid'), 'error' ); 
    }

}
