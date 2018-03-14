
document.observe('dom:loaded', function () {      
    //only run nonerpproductcheck on page load when in cart
    
    var path = window.location.pathname;
    if (path.indexOf('/checkout/onepage/') > -1 || path.indexOf('/checkout/multishipping/addresses') > -1){        
        nonErpProductCheck();
    }    
    
    Event.live('.confirm_button_no', 'click', function() {
        $('window-overlay').hide();
        if(path.indexOf('/checkout/onepage/') > -1 || path.indexOf('/checkout/multishipping/addresses') > -1){
            window.location.replace($('ecc_cd_cart_url').value); 
        }
    });
    
    Event.live('.register_checkbox input', 'click', function(el) {
        if (el.checked){  
            $('capture-register-customer-password').show();
            $('capturedetails:customer_password').addClassName('required-entry');
            $('capturedetails:confirm_password').addClassName('required-entry');
        } else {
            $('capture-register-customer-password').hide();
            $('capturedetails:customer_password').removeClassName('required-entry');
            $('capturedetails:confirm_password').removeClassName('required-entry');
        }
    });
})

function rfqSubmit(event) {
    var rfq_skus;
    var skus = [];
    $$('#rfq_lines_table .product_code_display').each(function (a) {
        skus.push(a.innerHTML);
    })
    if (skus.length > 0) {
        rfq_skus = JSON.stringify(skus);
    }
    nonErpProductCheck(rfq_skus);

    return true;
}

function nonErpProductCheck(rfq_skus) {
    var skus;
    var asynchronous = false;
    window.nonErpProductCheckRun = true;
    
    var source = 'rfq';
    var path = window.location.pathname;
    if (path.indexOf('/checkout/onepage/') > -1 || path.indexOf('/checkout/multishipping/addresses') > -1){        
        source = 'checkout';
    } 
    
    var url = $('ecc_cd_check_url').value;
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
    
    var ajaxRequest = new Ajax.Request( url , {
        method: 'post',
        asynchronous: asynchronous,
        parameters: {
            'data': rfq_skus,
            'source': source
        },
        requestHeaders: {Accept: 'application/json'},
        onComplete: function (request) {
            ajaxRequest = false;
        }.bind(this),
        onSuccess: function (request) {
            var response = request.responseText.evalJSON();
            if (response.nonErpItems == true) {

                //only show popup if non erp items check is enabled and option is request 
                if (response.nonErpItemsEnabled == true) {
                    window.nonErpProductItemsEnabled = true;
                }
                if (response.nonErpItems == true && response.option == 'request') {
                    window.nonErpProductItems = true;
                    if (response.msgText) {
                        window.msgText = response.msgText;
                    } else {
                        window.msgText = "Checkout is not currently available. Would you like us to contact you about this order?";
                    }
                    if ($('rfq_save')) {
                        $('rfq_save').stopObserving('click');
                        $('rfq_update').removeAttribute("onsubmit");
                        $('rfq_save').on('click', function (event) {
                            //this needs to be confirm_html to pick up the details
                            moreInformationBox(msgText, 200, 150, 'confirm_html', 'RFQ');
                        })
                        window.rfqpages == true;
                    } else {
                        var info_box_id =  'confirm_html';
                        var info_box_width = 325;
                        var info_box_height = 120;
                        if (response.guest == true) {
                            info_box_id = 'capture_customer_info';
                            info_box_width = undefined;
                            info_box_height = undefined;
                        }
                       
                        moreInformationBox(msgText, info_box_width, info_box_height, info_box_id, 'Checkout');
                    }
                }
            }
        }.bind(this),
        onFailure: function (request) {
            alert('Error occured loading products');
        }.bind(this),
        onException: function (request, e) {
            alert('Error occured loading products');
        }.bind(this)
    });
}

function moreInformationBox(msg, width, height, id, type) {

    if (!$('window-overlay')) {
        $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
    }
    
    if (!$('loading-mask')) {
        $(document.body).insert(
            '<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>'
        );
    }
    $('confirm_html').hide();
    $('capture_customer_info').hide();
    $('capture-customer-info-thank-you').hide();
    $('capturedetails-msg').update(msg);
    
    $('window-overlay').appendChild($('capture_details_wrapper').remove())
    $('capture_details_wrapper').show();
    $(id).show();
    $('window-overlay').show();
    
    positionOverlayElement('capture_details_wrapper', width, undefined, true);
}

function captureDetails(detailsRequired) {
    var data;
    
    if (detailsRequired) {
        
        var captureForm = new Validation($('capturedetails-form'));
        var valid = captureForm.validate();
        
        if (valid == false) {
            return false;
        }
        
        data = JSON.stringify($j('#capturedetails-form').serializeArray(true));
    }
    
    var productSkus;
    if (!data) {
        data = '{"action_type":"' + window.checkoutbuttonclicked + '"}';
        window.checkoutbuttonclicked = false;
        if ($('rfq_lines_table')) {
            var skus = [];
            var rfq_skus;
            $$('#rfq_lines_table .product_code_display').each(function (a) {
                var parent = a.up('tr').id;
                tr = $(parent);
                value = tr.down('.lines_line_value').value;
                qty = tr.down('.lines_quantity').value;
                skus.push({
                    name: a.innerHTML, qty: qty, value: value
                });
            })
            if (skus) {
                rfq_skus = JSON.stringify(skus);
            }
            productSkus = rfq_skus;
        }
    }
    
    var registerAccount = $j('[name = "capturedetails[register]"]').val();
    var url = $('ecc_cd_capture_url').value;
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
    
    var source = 'rfq';
    var path = window.location.pathname;
    if (path.indexOf('/checkout/onepage/') > -1 || path.indexOf('/checkout/multishipping/addresses') > -1){        
        source = 'checkout';
    } 
    
    performAjax(
        url, 
        'post', 
        {
            'data': data,
            'productSkus': productSkus,
            'registerAccount': registerAccount,
            'source': source
        }, 
        function (request) {
            var response = request.responseText;
            if (response.evalJSON().success == true) {
                $('confirm_html').hide();
                $('loading-mask').hide();
                $('capture_customer_info').hide();
                $('capture-customer-info-thank-you').show();
                $('capturedetails-msg').update('Thank you. Our representative will contact you shortly.');
                 positionOverlayElement('capture_details_wrapper', 300, undefined, true);
            }
        }
    );
}