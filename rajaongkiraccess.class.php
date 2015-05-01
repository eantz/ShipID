<?php

/**
* Raja Ongkir access
*/
class RajaOngkirAccess
{
    public static $api_key = 'API_KEY_ANDA';

    function __construct($key)
    {
        
    }

    /*
    * Get all province
    */
    public static function get_province() {
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
                "key: " . self::$api_key
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

    /*
    * Get City by Province ID
    */
    function get_city() {
        $province = explode('+', $_GET['province']);
        $province_id = $province[0];
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://rajaongkir.com/api/starter/city?province=" . $province_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: " . self::$api_key
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

}