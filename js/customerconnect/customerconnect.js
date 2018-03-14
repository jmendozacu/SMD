var overlayOpen = false;

var customerConnect = {
    contactsEdit: function(select) {
        if ($('customer_account_contacts_list')) {

            var action_value = select[select.selectedIndex].text;
            var trValue = select.up('tr');
            var details = trValue.select('input.details')[0].value;

            if (action_value == 'Delete') {
                var url = '/customerconnect/account/deleteContact/';
                hideOverlayForms()
                $('window-overlay').show();
                $('loading-mask').show();
                formSubmitAction(details, url);

            } else if (action_value == 'Sync Contact') {
                detial_values = JSON.parse(details);
                if (detial_values.source == 1) {
                    var url = '/customerconnect/account/syncContact/';
                    hideOverlayForms();
                    $('window-overlay').show();
                    $('loading-mask').show();
                    formSubmitAction(details, url);
                } else {
                    alert("Sync option is not available");
                }
            }
            else if (action_value == 'Edit') {
                editContactAction(trValue);
            }
            select.selectedIndex = false;
        
        }
    },
    shippingEdit: function(select) {
        if ($('customer_account_shippingaddress_list')) {
            var action_value = select[select.selectedIndex].text;
            var trValue = select.up('tr');
            var details = trValue.select('input.details')[0].value;

            if (action_value == 'Delete') {
                var url = '/customerconnect/account/deleteShippingAddress/';
                hideOverlayForms();
                $('window-overlay').show();
                $('loading-mask').show();
                formSubmitAction(details, url);
            } else if (action_value == 'Edit') {
                editShippingAddressAction(trValue);
            }
            select.selectedIndex = false;
        }
    }
};

document.observe('dom:loaded', function () {
    // display hand on hover
    if ($('billing_address_update')) {
        $('billing_address_update').observe('click', function (event) {

            $('update-billing-address').select('input[name=address_code]')[0].disabled = 'disabled';
            $('update-billing-address-title').show();
            $('add-billing-address-title').hide();

            hideOverlayForms();
            openOverlay('update-billing-address', 'shipping');
            event.stop();
        });
    }

    if ($('update-billing-address-close')) {
        $('update-billing-address-close').observe('click', closeOverlay);
    }

    if ($('update-shipping-address-close')) {
        $('update-shipping-address-close').observe('click', closeOverlay);
    }

    if ($('update-contact-close')) {
        $('update-contact-close').observe('click', closeOverlay);
    }

    if ($('add-shipping-address')) {
        $('add-shipping-address').observe('click', function (event) {
            resetShippingAddressForm();
            $('update-shipping-address-title').hide();
            $('add-shipping-address-title').show();
            openOverlay('update-shipping-address', 'shipping');
        });
    }

    if ($('add-contact')) {
        $('add-contact').observe('click', function (event) {
            resetContactForm();

            $('update-contact-title').hide();
            $('add-contact-title').show();
            if ($('manage_permissions')) {
                $('manage_permissions').hide();
            }
            openOverlay('update-contact', 'contacts');
        });
    }

    if ($('contact_web_enabled')) {
        $('contact_web_enabled').observe('click', function () {
            var vForm = new VarienForm('update-contact-form');
            vForm.validator.reset();

            if (this.checked) {
                $('contact_email_address_label').addClassName('required');
                $('contact_email_address_label').innerHTML = '* Email Address';
                $('contact_email_address').addClassName('required-entry');
            } else {
                $('contact_email_address_label').removeClassName('required');
                $('contact_email_address_label').innerHTML = 'Email Address';
                $('contact_email_address').removeClassName('required-entry');
            }
        });
    }

    if ($('update-billing-address-submit')) {
        $('update-billing-address-submit').observe('click', function (event) {
            event.stop();
            formSubmit('update-billing-address-form');
        });
    }

    if ($('update-shipping-address-submit')) {
        $('update-shipping-address-submit').observe('click', function (event) {
            event.stop();
            formSubmit('update-shipping-address-form');

        });
    }

    if ($('update-contact-submit')) {
        $('update-contact-submit').observe('click', function (event) {
            event.stop();
            formSubmit('update-contact-form');
        });
    }

    if ($('window-overlay-close')) {
        $('window-overlay-close').observe('click', function (event) {
            hideOverlayForms();
            closeOverlay();
            event.stop();
        });
    }

    Event.observe(window, "resize", function () {
        if (overlayOpen) {
            setWindowPosition();
        }
    });


    // below displays contract codes if 
    $$('[id ^= "contract_code_heading_"]').each(function (ccshow) {

        $(ccshow).up('td').observe('mouseover', function (a) {

            var invoice = ccshow.id.split('heading_');

            $(ccshow).hide();
            $('contract_codes_' + invoice[1]).show();

        })
    })
    $$('[id ^= "contract_codes_"]').each(function (cchide) {

        $(cchide).up('td').observe('mouseout', function (a) {
            invoice = cchide.id.split('contract_codes_');
            cchide.hide();
            $('contract_code_heading_' + invoice[1]).show();
        })

    })
});

function controllerRedirect(url, ajax) {

    if (!ajax) {
        window.location.replace(url);

    } else {
        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');

        jQuery.ajax({
            url: url,
            //      type: 'POST',
            async: true,
            dataType: "json",
            success: function (data) {
                if (data.message) {
                    showMessage(data.message, data.type);
                }
            }
        });
    }
}

function openOverlay(id_to_show, ul_id) {
    hideOverlayForms();

    $('window-overlay').select('div.box-account').each(function (d) {
        d.removeClassName('activeElement');
    });

    $(id_to_show).addClassName('activeElement')
    $(id_to_show).show();
    $('window-overlay').show();
    $('window-overlay-content').show();
    setWindowPosition();

    overlayOpen = true;
}

function setWindowPosition() {

    var wrapper = $('window-overlay-content');
    var element = wrapper.select('div.activeElement')[0];
    var fieldset = element.select('div.formfields')[0];
    var header = element.select('h2')[0];
    var buttons = element.select('div.buttonbar')[0];

    var wrapperlayout = wrapper.getLayout();

    var paddingHeight = wrapperlayout.get('padding-top') + wrapperlayout.get('padding-bottom');
    var paddingWidth = wrapperlayout.get('padding-left') + wrapperlayout.get('padding-right');

    wrapper.writeAttribute('style', '');
    fieldset.writeAttribute('style', '');

    var buttonHeight = buttons.measure('padding-box-height') + buttons.measure('margin-top') + buttons.measure('margin-bottom');
    var headerHeight = header.measure('padding-box-height') + header.measure('margin-top') + header.measure('margin-bottom');
    var fieldsetMarginHeight = fieldset.measure('margin-top') + fieldset.measure('margin-bottom');
    var fieldsetPaddingHeight = fieldset.measure('padding-top') + fieldset.measure('padding-bottom');

    var maxWidth = $(document.viewport).getWidth() - 40;
    var maxHeight = $(document.viewport).getHeight() - 40;

    var elementHeight = element.measure('padding-box-height');
    var elementWidth = element.measure('padding-box-width');

    var wrapperWidth = Math.min(maxWidth - paddingWidth, elementWidth);
    var wrapperHeight = Math.min(maxHeight - paddingHeight, elementHeight);

    var fieldsetHeight = fieldset.measure('padding-box-height') - fieldset.measure('padding-top') - fieldset.measure('padding-bottom') - fieldset.measure('border-top') - fieldset.measure('border-bottom');
    var fieldsetWidth = fieldset.measure('padding-box-width') - fieldset.measure('padding-left') - fieldset.measure('padding-right') - fieldset.measure('border-left') - fieldset.measure('border-right');

    var contentWidth = Math.min(wrapperWidth - element.measure('padding-left') - element.measure('padding-right'), fieldsetWidth);
    var contentHeight = Math.min(wrapperHeight - fieldsetMarginHeight - fieldsetPaddingHeight - buttonHeight - headerHeight, fieldsetHeight);

    wrapper.setStyle({
        'height': wrapperHeight + 'px',
        'width': wrapperWidth + 'px',
        'marginTop': '-' + (wrapperHeight / 2) + 'px',
        'marginLeft': '-' + (wrapperWidth / 2) + 'px',
        'left': '50%',
        'top': '50%',
    });

    fieldset.setStyle({
        'height': contentHeight + 'px',
        'width': contentWidth + 'px',
        'display': 'block',
        'marginLeft': '0'
    });

}

function hideOverlayForms() {
    $('window-overlay').select('.box-account').each(function (e) {
        e.hide()
    });

}


function closeOverlay() {
    hideOverlayForms();
    $('window-overlay-content').hide();
    $('window-overlay').hide();
    overlayOpen = false;
}

function formSubmit(form_data_id, url, serialized) {
    var editform = new varienForm($(form_data_id));
    var valid = editform.validate();

    if (!valid) {
        //$('messages').update('<ul class="messages"><li class="error-msg">Errors Found in Form</li></messages>');
    } else {
        hideOverlayForms();
        $('loading-mask').show();
        url = $(form_data_id).readAttribute('action');
        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
        if (!serialized) {
            var form_data = Object.toJSON($(form_data_id).serialize(true));
        } else {
            var form_data = form_data_id;
        }
        formSubmitAction(form_data, url);
    }
}

function formSubmitAction(form_data, url) {
    this.ajaxRequest = new Ajax.Request(url, {
        method: 'post',
        parameters: {'json_form_data': form_data},
        onComplete: function (request) {
            this.ajaxRequest = false;
        }.bind(this),
        onSuccess: function (data) {

            var json = data.responseText.evalJSON();
            if (json.type == 'success') {
                if (json.redirect) {
                    controllerRedirect(json.redirect);
                }
            } else {
                closeOverlay();
                $('loading-mask').hide();
                if (json.message) {
                    showMessage(json.message, json.type);
                }
            }
        }.bind(this),
        onFailure: function (request) {
            alert('Error occured in Ajax Call');
        }.bind(this),
        onException: function (request, e) {
            alert(e);
        }.bind(this)
    });
}
function showMessage(txt, type) {
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
}
function editShippingAddress(row, event) {
    event.stop();
    if (event.element().tagName !== 'SELECT' && (event.element().tagName !== 'OPTION')) {
        var trElement = event.findElement('tr');
        var x = trElement.select('.action-select');
        var objectFromJson = trElement.select('input[name=details]')[0].value;
        commonShippingAddressDetails(objectFromJson);
        openOverlay('update-shipping-address', 'shipping');
    }
    return false;
}

function editShippingAddressAction(e) {
    // this runs when the edit action button is clicked
    var objectFromJson = $(e).select('input[name=details]')[0].value;
    commonShippingAddressDetails(objectFromJson);
    openOverlay('update-shipping-address', 'shipping');
}

function commonShippingAddressDetails(objectFromJson) {
    var arrayFromJson = objectFromJson.evalJSON();
    // populate popup fields      
    var vForm = new VarienForm('update-shipping-address-form');
    vForm.validator.reset();
    hideOverlayForms();
    $('shipping_address_code').value = (arrayFromJson.address_code) ? arrayFromJson.address_code : '';
    $('shipping_address_code').disable();
    $('shipping_name').value = (arrayFromJson.name !== null) ? arrayFromJson.name : '';
    $('shipping_address1').value = (arrayFromJson.address1 !== null) ? arrayFromJson.address1 : '';
    if ($('shipping_address2') != null) {
        $('shipping_address2').value = (arrayFromJson.address2 !== null) ? arrayFromJson.address2 : '';
    }
    if ($('shipping_address3') != null) {
        $('shipping_address3').value = (arrayFromJson.address3 !== null) ? arrayFromJson.address3 : '';
    }
    if ($('shipping_address4') != null) {
        $('shipping_address4').value = (arrayFromJson.address4 !== null) ? arrayFromJson.address4 : '';
    }
    $('shipping_city').value = (arrayFromJson.city !== null) ? arrayFromJson.city : '';
    $('shipping_county_id').setAttribute('defaultValue', ((arrayFromJson.county_id !== null) ? arrayFromJson.county_id : ''));
    $('shipping_county_id').value = (arrayFromJson.county_id !== null) ? arrayFromJson.county_id : '';
    ;
    $('shipping_county').value = (arrayFromJson.county !== null) ? arrayFromJson.county : '';
    $('shipping_country').value = (arrayFromJson.country_code !== null) ? arrayFromJson.country_code : '';
    $('shipping_postcode').value = (arrayFromJson.postcode !== null) ? arrayFromJson.postcode : '';
    if ($('shipping_email') != null) {
        $('shipping_email').value = (arrayFromJson.email !== null) ? arrayFromJson.email : '';
    }
    if ($('shipping_telephone') != null) {
        $('shipping_telephone').value = (arrayFromJson.telephone !== null) ? arrayFromJson.telephone : '';
    }
    if ($('shipping_mobile_number') != null) {
        $('shipping_mobile_number').value = (arrayFromJson.mobile_number !== null) ? arrayFromJson.mobile_number : '';
    }
    if ($('shipping_fax') != null) {
        $('shipping_fax').value = (arrayFromJson.fax !== null) ? arrayFromJson.fax : '';
    }
    $('shipping_old_data').value = (objectFromJson !== null) ? objectFromJson : '';
    if (typeof (limitcheck) != 'undefined') {
        limitcheck.setData(limitcheck.name, limitcheck.address, limitcheck.telephone, limitcheck.instructions);
    }
//    $('update-shipping-address').show();
    if ("createEvent" in document) {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        $('shipping_country').dispatchEvent(evt);
    } else {
        $('shipping_country').fireEvent("onchange");
    }

    $('update-shipping-address-title').show();
    $('add-shipping-address-title').hide();

    var vForm = new VarienForm('update-shipping-address');
    vForm.validator.reset();
//    $('window-overlay').show();
}

function resetShippingAddressForm() {
    var vForm = new VarienForm('update-shipping-address');
    vForm.validator.reset();
    $('shipping_address_code').value = '';
    $('shipping_address_code').enable();
    $('shipping_name').value = '';
    $('shipping_address1').value = '';
    if ($('shipping_address2') != null) {
        $('shipping_address2').value = '';
    }
    if ($('shipping_address3') != null) {
        $('shipping_address3').value = '';
    }
    if ($('shipping_address4') != null) {
        $('shipping_address4').value = '';
    }
//    if(typeof($('shipping_address2')) != null){}{    
//        $('shipping_address2').value = '';
//    }    
//    if(typeof($('shipping_address3')) != null){}{    
//        $('shipping_address3').value = '';
//    }    
//    if(typeof($('shipping_address4')) != 'undefined'){}{    
//        $('shipping_address4').value = '';
//    }    
    $('shipping_city').value = '';
    $('shipping_county_id').setAttribute('defaultValue', '');
    $('shipping_county').value = '';
    $('shipping_country').value = ($('shipping_default_country')) ? $('shipping_default_country').value : '';
    if ("createEvent" in document) {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        $('shipping_country').dispatchEvent(evt);
    } else {
        $('shipping_country').fireEvent("onchange");
    }
    $('shipping_postcode').value = '';
    if ($('shipping_email') != null) {
        $('shipping_email').value = '';
    }
    if ($('shipping_telephone') != null) {
        $('shipping_telephone').value = '';
    }
    if ($('shipping_mobile_number') != null) {
        $('shipping_mobile_number').value = '';
    }
    if ($('shipping_fax') != null) {
        $('shipping_fax').value = '';
    }
    $('shipping_old_data').value = '';
}


function editContact(row, event) {
    event.stop();
    var trElement = Event.findElement(event, 'tr');
    if (event.element().tagName !== 'SELECT' && event.element().tagName !== 'OPTION') {
        var objectFromJson = $(trElement).select('input[name=details]')[0].value;
        commonContactDetails(objectFromJson);
        if ($('manage_permissions')) {
            var groups = $(trElement).select('input[name=groups]')[0].value.split(',');
            setContactGroups(groups);
        }
        openOverlay('update-contact', 'contacts');
    }
}

function editContactAction(e) {
    // this runs when the edit action button is clicked            
    var objectFromJson = $(e).select('input[name=details]')[0].value;
    commonContactDetails(objectFromJson);
    if ($('manage_permissions')) {
        var groups = $(e).select('input[name=groups]')[0].value.split(',');
        setContactGroups(groups);
    }

    openOverlay('update-contact', 'contacts');
}

function setContactGroups(groups) {

    $('update-contact').select('select[name=access_groups] option').each(function (e) {
        e.selected = false;
    });

    $(groups).each(function (group) {
        var option = $('update-contact').select('select[name=access_groups] option[value="' + group + '"]');
        if (option && option.length > 0) {
            option[0].selected = true;
        }
    });
}

function commonContactDetails(objectFromJson) {
    var arrayFromJson = objectFromJson.evalJSON();
    hideOverlayForms();

    $('update-contact').show();
    if ($('manage_permissions')) {
        $('manage_permissions').show();
    }

    var vForm = new VarienForm('update-contact-form');
    vForm.validator.reset();

    $('contact_name').value = (arrayFromJson.name !== null) ? arrayFromJson.name : '';
    $('contact_function').value = (arrayFromJson.function !== null) ? arrayFromJson.function : '';
    $('contact_telephone_number').value = (arrayFromJson.telephone_number !== null) ? arrayFromJson.telephone_number : '';
    $('contact_fax_number').value = (arrayFromJson.fax_number !== null) ? arrayFromJson.fax_number : '';
    $('contact_email_address').value = (arrayFromJson.email_address !== null) ? arrayFromJson.email_address : '';
    $('contact_login_id').value = (arrayFromJson.login_id !== null) ? arrayFromJson.login_id : '';
    $('contact_ecc_master_shopper').value = (arrayFromJson.master_shopper !== null) ? arrayFromJson.master_shopper : '';
    if (arrayFromJson.is_ecc_customer == 0) {
        $('master_shopper_li').hide();
    }
    if (arrayFromJson.is_ecc_customer == 1) {
        $('master_shopper_li').show();
    }

    if (arrayFromJson.login_id) {
        $('contact_web_enabled').checked = true;
    } else {
        $('contact_web_enabled').checked = false;
    }

    if (arrayFromJson.master_shopper == 'y') {
        $('contact_master_shopper').checked = true;
    } else {
        $('contact_master_shopper').checked = false;
    }
    if ($('loggedin_customer_master_shopper').value == '1') {
        if ($('contact_master_shopper').hasAttribute('disabled')) {
            $('contact_master_shopper').removeAttribute('disabled');
        }
        $('contact_master_shopper').enabled = true;
    }
    else {
        if ($('contact_master_shopper').hasAttribute('enabled')) {
            $('contact_master_shopper').removeAttribute('enabled');
        }
        $('contact_master_shopper').disabled = true;
    }
    if ($('contact_email_address').value == $('loggedin_customer_email').value) {
        if ($('contact_master_shopper').hasAttribute('enabled')) {
            $('contact_master_shopper').removeAttribute('enabled');
        }
        $('contact_master_shopper').disabled = true;
    }

    if ($('manage_permissions')) {
        if ($('contact_web_enabled').checked) {
            $('manage_permissions').show();
        } else {
            $('manage_permissions').hide();
        }
    }

    $('contact_old_data').value = (objectFromJson !== null) ? objectFromJson : '';

    $('update-contact-title').show();
    $('add-contact-title').hide();

    $('window-overlay').show();
}

function update_value() {
    if ($('contact_master_shopper').checked) {
        $('contact_ecc_master_shopper').value = 'y';
    }
    else {
        $('contact_ecc_master_shopper').value = 0;
    }
}

function resetContactForm() {
    var vForm = new VarienForm('update-contact-form');
    vForm.validator.reset();
    $('contact_name').value = '';
    $('contact_function').value = '';
    $('contact_telephone_number').value = '';
    $('contact_fax_number').value = '';
    $('contact_email_address').value = '';
    $('contact_login_id').value = '';
    $('contact_web_enabled').checked = false;
    $('contact_old_data').value = '';
}
//function customerAccountContactsListTableRowClick() {
//    openOverlay('update-contact');
//}


