jQuery(function($) {
    var state_selected = '';
    var city_selected = '';

    var prepareCity = function(province_id) {
        province_id === 0 ? 1 : province_id;
        console.log(province_id);
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            $('.calc_shipping_city_id').html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.calc_shipping_city_id');
            });
            
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete shipping city");
        });
    }

    var prepareProvince = function() {
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            console.log(state_selected);
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('.calc_shipping_state_id');
            });

            prepareCity(state_selected == undefined ? 1 : state_selected);
        })
        .fail(function(xhr, status, e) {
            alert("Can't contacting Raja Ongkir API, please reload");
            console.log(e);
        })
        .always(function() {
            console.log("complete shipping state");
        });
    }


    
    if($('.calc_shipping_state_id').length != 0) {
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-selected-shipping-location',
            type: 'GET',
            dataType: 'json'
        })
        .done(function(data) {
            console.log(data);
            if(data == null) {
                state_selected = 1;
                city_selected = 0;
            } else {
                state_selected = data.province;
                city_selected = data.city;
            }
            
            prepareProvince();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete shipping location");
        });
        

        $('.calc_shipping_state_id').on('change', function(e) {
            $('.calc_shipping_city_id').html('');
            prepareCity($(this).val());
        });
        

    }

    /**************************************************************************/
    var billing_state_selected = '';
    var billing_city_selected = '';

    var prepareBillingCity = function(province_id) {
        province_id = province_id == '' ? 1 : province_id;
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            $('#billing_city').html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((billing_city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('#billing_city');

                if(billing_city_selected == val.id) {
                    $('#billing_city').val(val.id);
                }
            });
            
            $('#billing_city').trigger('change');
            triggerBillingChange();
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete shipping city");
        });
    }

    var prepareBillingProvince = function() {
        $('#billing_state').html('')
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((billing_state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('#billing_state');

                if(billing_state_selected == val.id) {
                    $('#billing_state').val(val.id);
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

    if($('#billing_state').length != 0) {

        $('#billing_state').html('');

        $.ajax({
            url: shipidobj.url + '?action=shipid-get-selected-shipping-location',
            type: 'GET',
            dataType: 'json'
        })
        .done(function(data) {
            console.log(data);
            if(data == null) {
                billing_state_selected = 1;
                billing_city_selected = 0;
            } else {
                billing_state_selected = data.province;
                billing_city_selected = data.city;
            }
            prepareBillingProvince();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete billing location");
        });

        $('#billing_state').on('change', function(e) {
            $('#billing_city').html('');
            prepareBillingCity($(this).val());
        });


    }




    /**************************************************************************/
    var shipping_state_selected = '';
    var shipping_city_selected = '';

    var prepareShippingCity = function(province_id) {
        province_id = province_id == '' ? 1 : province_id;
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);

            $('#shipping_city').html('');

            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((shipping_city_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('#shipping_city');

                if(shipping_city_selected == val.id) {
                    $('#shipping_city').val(val.id);
                }
            });

            $('#shipping_city').trigger('change');
            triggerShippingChange();
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete shipping city");
        });
    }

    var prepareShippingProvince = function() {
        $('#shipping_state').html('');
        $.ajax({
            url: shipidobj.url + '?action=shipid-get-province',
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            
            $.each(data, function(i, val) {
                opt = '<option value="' + val.id + '" ' + ((shipping_state_selected == val.id) ? 'selected="selected"' : '') + '>' + val.value + '</option>'
                $(opt).appendTo('#shipping_state');

                if(shipping_state_selected == val.id) {
                    $('#shipping_state').val(val.id);
                }
            });

            prepareShippingCity(shipping_state_selected);
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete shipping state");
        });
    }

    if($('#shipping_state').length != 0) {

        $('#shipping_state').html('');

        $.ajax({
            url: shipidobj.url + '?action=shipid-get-selected-shipping-location',
            type: 'GET',
            dataType: 'json'
        })
        .done(function(data) {
            console.log(data);
            if(data == null) {
                shipping_state_selected = 1;
                shipping_city_selected = 0;
            } else {
                shipping_state_selected = data.province;
                shipping_city_selected = data.city;
            }
            prepareShippingProvince();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete shipping location");
        });

        $('#shipping_state').on('change', function(e) {
            $('#shipping_city').html('');
            prepareShippingCity($(this).val());
        });
    }

    /****************************************/
    var triggerBillingChange = function  () {
        if(!$('#ship-to-different-address-checkbox').is(':checked')) {
            $( 'body' ).trigger( 'update_checkout' );
        }
    }

    var triggerShippingChange = function  () {
        console.log('triggered');
        if($('#ship-to-different-address-checkbox').is(':checked')) {
            $( 'body' ).trigger( 'update_checkout' );
        }
    }

    if($('#billing_state').length != 0 && $('#shipping_state').length != 0) {
        $( 'form.checkout' ).on('change', '#billing_city', function() {
            triggerBillingChange();
        });

        $( 'form.checkout' ).on('change', '#shipping_city', function() {
            triggerShippingChange();
        });
    }
});