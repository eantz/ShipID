jQuery(function($) {
    billing_state_selected = 0;
    billing_city_selected = 0;
    
    var prepareBillingCity = function(province_id) {
        province_id = province_id == '' ? 1 : province_id;
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            $('.admin_billing_select_city').html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((billing_city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.admin_billing_select_city');

                if(billing_city_selected == val.id) {
                    $('.admin_billing_select_city').val(val.id);
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
    
    var prepareBillingProvince = function() {
        $('.admin_billing_select_state').html('');
        
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((billing_state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.admin_billing_select_state');
                
                console.log(billing_state_selected);

                if(billing_state_selected == val.id) {
                    $('.admin_billing_select_state').val(val.id);
                }
            });

            prepareBillingCity(billing_state_selected);
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete billing state");
        });
    }
    
    if($('.admin_billing_select_state').length != 0) {
        billing_state_selected = $('.admin_billing_select_state option:selected').val();
        billing_city_selected = $('.admin_billing_select_city option:selected').val();
        
        prepareBillingProvince();
        

        $('.admin_billing_select_state').on('change', function(e) {
            prepareBillingCity($(this).val());
        });

    }
    
    
    shipping_state_selected = 0;
    shipping_city_selected = 0;
    
    var prepareShippingCity = function(province_id) {
        province_id = province_id == '' ? 1 : province_id;
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            $('.admin_shipping_select_city').html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((shipping_city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.admin_shipping_select_city');

                if(shipping_city_selected == val.id) {
                    $('.admin_shipping_select_city').val(val.id);
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
    
    var prepareShippingProvince = function() {
        $('.admin_shipping_select_state').html('');
        
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((shipping_state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.admin_shipping_select_state');

                if(shipping_state_selected == val.id) {
                    $('.admin_shipping_select_state').val(val.id);
                }
            });

            prepareShippingCity(shipping_state_selected);
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete billing state");
        });
    }
    
    if($('.admin_shipping_select_state').length != 0) {
        shipping_state_selected = $('.admin_shipping_select_state option:selected').val();
        shipping_city_selected = $('.admin_shipping_select_city option:selected').val();
        
        prepareShippingProvince();
        

        $('.admin_shipping_select_state').on('change', function(e) {
            prepareShippingCity($(this).val());
        });

    }
});