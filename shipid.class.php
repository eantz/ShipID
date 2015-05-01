<?php


class Shipid extends WC_Shipping_Method {

    protected $plugin_name;

    protected $version;

    public $api_key = 'API_KEY_ANDA';

    public function __construct() {

        $this->id                 = 'shipid';
        $this->title       = __( 'ShipID' );
        $this->method_description = __( 'ShipID is a shipping plugin for woocommerce. This shipping plugin only work if the origin and destination country is Indonesia. Shipid use JNE' ); // 
        $this->enabled            = $this->get_option('enabled'); 
        
        $this->plugin_name = 'ShipID';
        $this->version = '1.0.0';

        if(is_checkout())
            add_action( 'wp_enqueue_scripts', array($this, 'public_assets') );    

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

    function public_assets($hook)
    {
        wp_enqueue_style('shipid-public-style', plugin_dir_url(__FILE__) . 'assets/css/shipid-public.css');

        wp_enqueue_script('shipid-public-js', plugin_dir_url(__FILE__) . 'assets/js/shipid-public.js', array('jquery', 'wc-checkout'), '1.0', true);

        wp_localize_script('shipid-public-js', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
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
                            'title'         => __( 'Enable/Disable', 'woocommerce' ),
                            'type'          => 'checkbox',
                            'label'         => __( 'Enable this shipping method', 'woocommerce' ),
                            'default'       => 'no',
                            'id'            => 'shippid_enabled'
                        ),
            'title' => array(
                            'title'         => __( 'Method Title', 'woocommerce' ),
                            'type'          => 'text',
                            'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                            'default'       => __( 'Shipid JNE', 'shipid' ),
                            'desc_tip'      => true,
                            'id'            => 'shippid_title'
                        ),
            'origin_prov' => array(
                            'title'         =>  __('Origin Province', 'shipid'),
                            'type'          =>  'text',
                            'description'   =>  __('This is the origin province of the seller.'),
                            'desc_tip'      =>  true,
                            'class'         =>  'origin_prov',
                            'disabled'      =>  true,
                            'id'            => 'shippid_origin_prov'
                        ),
            'origin_city' => array(
                            'title'         =>  __('Origin City', 'shipid'),
                            'type'          =>  'text',
                            'description'   =>  __('This is the origin city of the seller.'),
                            'desc_tip'      =>  true,
                            'class'         =>  'origin_city',
                            'disabled'      =>  true,
                            'id'            => 'shippid_origin_city'
                        ),
            'origin_prov_id' => array(
                            'type'          =>  'hidden',
                            'desc_tip'      =>  false,
                            'class'         =>  'origin_prov_id',
                            'id'            => 'shippid_origin_prov_id'
                        ),
            'origin_city_id' => array(
                            'type'          =>  'hidden',
                            'desc_tip'      =>  false,
                            'class'         =>  'origin_city_id',
                            'id'            => 'shippid_origin_city_id'
                        ),
            );

    }

    function admin_assets() {
        if ( !(isset($_GET['section']) && $_GET['section'] == 'shipid')) {
            return;
        }

        wp_enqueue_script('jquery-ui-autocomplete ');
        wp_enqueue_script('shipid-admin', plugin_dir_url( __FILE__ ) . 'assets/js/shipid-admin.js', array('jquery', 'jquery-ui-autocomplete'));
        wp_localize_script('shipid-admin', 'shipidobj', array('url'=>admin_url( 'admin-ajax.php' )));
        
    }

    public function is_available( $package ) {
        $is_available = true;
        
        if ( 'no' == $this->enabled ) {
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
                CURLOPT_POSTFIELDS => "origin=" . $this->get_option('origin_city_id') . "&destination=" . $city_id . "&weight=" . $total_weight . "&courier=jne",
                CURLOPT_HTTPHEADER => array(
                    "key: " . $this->api_key,
                    "origin"=>$this->get_option('origin_city'),
                    "destination"=>$city_id,
                    "weight"=>$total_weight,
                    'courier'=>'jne'
                ),
            ));

            $response = curl_exec($curl);


            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                wc_add_notice( 'Cannot contacting Raja Ongkir API, please try again later', 'error' ); 
                WC()->shipping->reset_shipping();
                return;
            }

            $response_array = json_decode($response);
            $method_option = array();

            if(empty($response_array->rajaongkir->results[0]->costs)) {
                wc_add_notice( 'We cannot send to this location', 'error' ); 
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
        
        wc_add_notice( 'Please set your city', 'error' ); 
    }

}
