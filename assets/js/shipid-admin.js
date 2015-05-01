jQuery(function($) {
    origin_state_selected = '';
    origin_city_selected = '';

    shipping_state_selected = '';
    shipping_city_selected = '';

    billing_state_selected = '';
    billing_city_selected = '';

    var prepareCity = function(city_selector, province_id, type) {
        province_id = province_id == '' ? 1 : province_id;

        

        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            city_selected = '';
            if(type == 'origin') {
                city_selected = origin_city_selected;
            } else if(type == 'shipping') {
                city_selected = shipping_city_selected;
            } else if(type == 'billing') {
                city_selected = billing_city_selected;
            }

            $(city_selector).html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo(city_selector);

                if(city_selected == val.id) {
                    $(city_selector).val(val.id);
                }
            });
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete shipping city");
        });
    }

    var prepareProvince = function(state_selector, city_selector, type) {
        $(state_selector).html('');

        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            state_selected = '';
            if(type == 'origin') {
                state_selected = origin_state_selected;
            } else if(type == 'shipping') {
                state_selected = shipping_state_selected;
            } else if(type == 'billing') {
                state_selected = billing_state_selected;
            }

            state_selected = (state_selected == '' || state_selected == undefined) ? 1 : state_selected;
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo(state_selector);

                if(state_selected == val.id) {
                    $(state_selector).val(val.id);
                }
            });

            console.log(type + ' ' + state_selected);

            prepareCity(city_selector, state_selected, type);
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete billing state");
        });
    }

    if($('#woocommerce_shipid_origin_prov').length > 0) {
        origin_state_selected = $('#woocommerce_shipid_origin_prov option:selected').val();
        origin_city_selected = $('#woocommerce_shipid_origin_city option:selected').val();

        prepareProvince('#woocommerce_shipid_origin_prov', '#woocommerce_shipid_origin_city', 'origin');

        $('#woocommerce_shipid_origin_prov').on('change', function(e) {
            prepareCity('#woocommerce_shipid_origin_city', $(this).val(), 'origin');
        });
    }

    

    if($('.admin_billing_select_state').length != 0) {
        billing_state_selected = $('.admin_billing_select_state option:selected').val();
        billing_city_selected = $('.admin_billing_select_city option:selected').val();
        
        prepareProvince('.admin_billing_select_state', '.admin_billing_select_city', 'billing');
        
        $('.admin_billing_select_state').on('change', function(e) {
            prepareCity('.admin_billing_select_city', $(this).val(), 'billing');
        });

    }

    if($('.admin_shipping_select_state').length != 0) {
        shipping_state_selected = $('.admin_shipping_select_state option:selected').val();
        shipping_city_selected = $('.admin_shipping_select_city option:selected').val();
        
        prepareProvince('.admin_shipping_select_state', '.admin_shipping_select_city', 'shipping');
        
        $('.admin_shipping_select_state').on('change', function(e) {
            prepareCity('.admin_shipping_select_city', $(this).val(), 'shipping');
        });

    }

});