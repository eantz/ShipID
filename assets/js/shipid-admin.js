jQuery(function($) {

    var prepareCitySuggestion = function(province_id) {
        $('.button-primary').attr('disabled', 'disabled');

        $.ajax({
            url: shipidobj.url + '?action=shipid-get-city&province=' + province_id,
            dataType:'json',
            type: 'GET',
        })
        .done(function(data) {
            console.log(data);
            

            $('.origin_city_id').val('');

            $( ".origin_city" )
                .val('')
                .removeAttr('disabled')
                .autocomplete({
                    source: data,
                    select: function(e, ui) {
                        $('.origin_city').val(ui.item.value);
                        $('.origin_city_id').val(ui.item.id);

                        $('.button-primary').removeAttr('disabled');
                        return false;
                    },
                    change: function(e, ui) {
                        found = false;
                        $.each(data, function(index, val) {
                            if(val.value == $('.origin_city').val()) {
                                found = true;
                            }
                        });

                        if(!found) {
                            $('.origin_city_id').val('');

                            $('<span>')
                                .addClass('city_msg')
                                .css({'color': 'red'})
                                .append('Province Not found, please choose from the list')
                                .insertAfter('.origin_city');

                            $('.button-primary').attr('disabled', 'disabled');
                        } else {
                            $('.city_msg').remove();
                        }
                    }
                })
                .autocomplete("instance")._renderItem(function(ul, item) {
                    return $('<li>')
                        .append(item.value)
                        .appendTo(ul);
                });
        })
        .fail(function() {
            alert("Can't contacting Raja Ongkir API, please reload");
        })
        .always(function() {
            console.log("complete");
        });
    }

    $.ajax({
        url: shipidobj.url + '?action=shipid-get-province',
        dataType:'json',
        type: 'GET',
    })
    .done(function(data) {
        console.log(data);
        
        $( ".origin_prov" )
            .removeAttr('disabled')
            .autocomplete({
                source: data,
                select: function(e, ui) {
                    $('.origin_prov').val(ui.item.value);
                    $('.origin_prov_id').val(ui.item.id);

                    prepareCitySuggestion(ui.item.id);

                    return false;
                },
                change: function(e, ui) {
                    found = false;
                    $.each(data, function(index, val) {
                        if(val.value == $('.origin_prov').val()) {
                            found = true;
                        }
                    });

                    if(!found) {
                        $('.origin_prov_id').val('');
                        $('.origin_city')
                            .val('')
                            .attr('disabled', 'disabled');
                        $('.origin_city_id').val('');

                        $('<span>')
                            .addClass('province_msg')
                            .css({'color': 'red'})
                            .append('Province Not found, please choose from the list')
                            .insertAfter('.origin_prov');

                        $('.button-primary').attr('disabled', 'disabled');
                    } else {
                        $('.province_msg').remove();
                    }
                }
            })
            .autocomplete("instance")._renderItem(function(ul, item) {
                return $('<li>')
                    .append(item.value)
                    .appendTo(ul);
            });
    })
    .fail(function() {
        alert("Can't contacting Raja Ongkir API, please reload");
    })
    .always(function() {
        console.log("complete");
    });

});