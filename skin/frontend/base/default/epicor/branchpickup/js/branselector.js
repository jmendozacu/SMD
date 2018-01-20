if (typeof Epicor_cartPage == 'undefined') {
    var Epicor_cartPage = {};
}
Epicor_cartPage.cartselect = Class.create();
Epicor_cartPage.cartselect.prototype = {
    target: null,
    wrapperId: "selectbrancPickupWrapperWindow", //selectbrancPickupWrapperWindow
    initialize: function() {
        if (!$('window-overlay')) $(document.body).insert(
            '<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        if ($('loading-mask')) {
            $("loading-mask").remove();
        }
        if (!$('loading-mask')) $(document.body).insert(
            '<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>'
        );
    },
    openpopup: function(newtarget, ahref, removeId, loccode) {
        this.target = newtarget;
        if ($(this.wrapperId)) $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $('loading-mask').show();
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o) { // id = messages1
            if (o.selected == true) {
                website = o.value;
            }
        })
        var cartpopupurl = $('cartpopupurl').value;
        this.ajaxRequest = new Ajax.Request(cartpopupurl, {
            method: 'post',
            parameters: {
                field_id: newtarget,
                website: website,
                removeval: JSON.stringify(removeId),
                branch: loccode
            },
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request) {
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();
                $('window-overlay').show();
                this.updateWrapper();
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured loading products grid');
                this.closepopup();
            }.bind(this),
            onException: function(request, e) {
                alert('Error occured loading products grid');
                this.closepopup();
            }.bind(this)
        });
    },
    //set the branch in session
    // If the action was happened in branch select page, then the page will reloaded
    // If the action was happened in checkout page, then no redirect
    setbranch: function(newtarget, ahref, loccodes,shippingsave=false) {
        this.target = newtarget;
        if ($(this.wrapperId)) $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $('loading-mask').show();
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o) { // id = messages1
            if (o.selected == true) {
                website = o.value;
            }
        })
        var selectbranchurl = $('selectbranch').value;
        this.ajaxRequest = new Ajax.Request(selectbranchurl, {
            method: 'post',
            parameters: {
                field_id: newtarget,
                website: website,
                branch: loccodes
            },
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request) {
                $('loading-mask').hide();
                var ajaxcode = $('ajaxcode');
                var checkoutAjax = $('branchpickup_' + loccodes);
                var isCheckoutAjax = false;
                var branchPickup = $('branch_pickup');
                //this condition is for checkout page 
                //When the user selected a location in search popup
                //this action will triggered
                if (typeof(branchPickup) != "undefined" && branchPickup && typeof(checkoutAjax) !=
                    "undefined" && checkoutAjax) {
                    var isCheckoutAjax = true;
                    LocationSearchSelector.closepopup();
                    selectItemByValue($('branch_pickup'), loccodes);
                    $('selectedbranch').value = loccodes;
                    $('branchpickupshipping').checked = true;
                    $('shipping-new-address-form').hide();
                }
                //this condition is for branch select page
                if (typeof(ajaxcode) != "undefined" && ajaxcode && !(isCheckoutAjax)) {
                    window.location.reload();
                }
                $(this.wrapperId).remove();
                $('window-overlay').hide();
                //saving the shipping information
                if(shippingsave) {
                    var shippingselectForm = $('shipping-address-select');
                    shipping.save();
                    if (typeof(shippingselectForm) != 'undefined' && shippingselectForm != null) {
                      $(shipping.form).elements.namedItem("shipping_address_id").disabled = false;
                    }                     
                }
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured loading branch pickup grid');
                this.closepopup();
            }.bind(this),
            onException: function(request, e) {
                alert('Error occured loading branch pickup grid');
                this.closepopup();
            }.bind(this)
        });
    },
    loaderoptions: function(parentDiv, childDiv) {
        if (childDiv == parentDiv) {
            alert("The parent div cannot be removed.");
        } else if (document.getElementById(childDiv)) {
            var child = document.getElementById(childDiv);
            var parent = document.getElementById(parentDiv);
            parent.removeChild(child);
            if (!$('loading-mask')) $(document.body).insert(
                '<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>'
            );
        } else {
            return false;
        }
    },
    //Assign the values into hidden field 
    //Also changing the shipping form values when the user pressed continue in shipping information page
    checkoptiondata: function() {
        var optionbranchChecked = $('branchpickupshipping').checked;
        var optionshippingChecked = $('normalshipping').checked;
        var action_value = $('branch_pickup').value;
        if ((!optionbranchChecked) && (!optionshippingChecked)) {
            $('shippingbranchselect').show();
            $('branch_pickup').focus();
            $('branch_pickup').scrollIntoView();
            return false;
        } else if ((optionbranchChecked) && (!action_value)) {
            $('brancherrormsg').show();
            $('branch_pickup').focus();
            $('branch_pickup').scrollIntoView();
            return false;
        } else {
            if ((optionbranchChecked) && (action_value)) {
                var shippingselectForm = $('shipping-address-select');
                $$('#shipping-new-address-form ul input, #shipping-new-address-form ul input').each(function(
                    item) {
                    item.clear();
                });
                $('shipping-new-address-form').hide();
                if (!isEmpty($('bfirstname').value)) {
                    $('shipping:firstname').value = $('bfirstname').value;
                }
                if (!isEmpty($('blastname').value)) {
                    $('shipping:lastname').value = $('blastname').value;
                }
                if (!isEmpty($('bmobile_number').value)) {
                    $('shipping:mobile_number').value = $('bmobile_number').value;
                }
                if (!isEmpty($('bpostcode').value)) {
                    $('shipping:postcode').value = $('bpostcode').value;
                }
                if ($('shipping:company')) {
                    $('shipping:company').value = '';
                }
                if (!isEmpty($('bstreet1').value)) {
                    $('shipping:street1').value = $('bstreet1').value;
                }
                if (!isEmpty($('bstreet2').value)) {
                    $('shipping:street2').value = $('bstreet2').value;
                }
                // if (!isEmpty($('bstreet3').value)) {
                //     $('shipping:street3').value = $('bstreet3').value;
                // }
                if (!isEmpty($('btelephone').value)) {
                    $('shipping:telephone').value = $('btelephone').value;
                }
                if (!isEmpty($('bfax').value)) {
                    $('shipping:fax').value = $('bfax').value;
                }
                if (!isEmpty($('bcity').value)) {
                    $('shipping:city').value = $('bcity').value;
                }
                if (!isEmpty($('bcountry_id').value)) {
                    selectItemByValue($('shipping:country_id'), $('bcountry_id').value);
                    var elements = $('shipping:country_id');
                    var event = new Event('change');
                    elements.dispatchEvent(event);
                    //$('shipping:country_id').value = $('bcountry_id').value;
                }
                if (!isEmpty($('bregion_id').value)) {
                    //$('shipping:region_id').setAttribute('defaultValue',  $('bregion_id').value);
                    $('shipping:region_id').value = $('bregion_id').value;
                }
                if (!isEmpty($('bregion').value)) {
                    $('shipping:region').value = $('bregion').value;
                } else {
                    $('shipping:region').value = '';
                }
                $(shipping.form).elements.namedItem("shipping[address_id]").value = '';
                $(shipping.form).elements.namedItem("shipping[save_in_address_book]").value = '';
                if (typeof(shippingselectForm) != 'undefined' && shippingselectForm != null) {
                    $(shipping.form).elements.namedItem("shipping_address_id").disabled = true;
                }
                //this.saveShippingBranch();
                //If some mandatory values are not present in the Location, then show this error
                var mandataorFields = this.checkMandatoryFields();
                if (mandataorFields) {
                    $('branchvalidationrmsg').show();
                    var branchPickupSelected = $('branch_pickup').value;
                    var locationErrorUrl = $('locationerror').value;
                    LocationEditSelector.openpopup('ecc_branchpickup_locationedit', locationErrorUrl + '?q=' +
                        branchPickupSelected);
                    $('brancherrormsg').hide();
                    $('branch_pickup').focus();
                    return false;
                } else {
                    $('branchvalidationrmsg').hide();
                    var branches = checkBranchName();
                    if (!branches) {
                        return false;
                    } else {
                        var branchPickupSelect = $('branch_pickup').value;
                        cartPage.setbranch('ecc_branchpickup_cart', this.href, branchPickupSelect,true);
                    }
                }
            } else {
                shipping.save();
                $('brancherrormsg').hide();
                $('shippingbranchselect').hide();
            }
        }
    },
    //If some mandatory values are not present in the Location, then show this error
    checkMandatoryFields: function() {
        if ((isEmpty($('bcity').value)) || (isEmpty($('bstreet1').value)) || (isEmpty($('bcity').value)) || (
                isEmpty($('bcountry_id').value)) || (isEmpty($('btelephone').value)) || (isEmpty($('bpostcode')
                .value))) {
            return true;
        } else {
            return false;
        }
    },
    closepopup: function() {
        $(this.wrapperId).remove();
        var elementBranch = $('branch_pickup');
        if (typeof(elementBranch) != 'undefined' && elementBranch != null) {
            var defaultValue = $('selectedbranch').value;
            $('branch_pickup').value = defaultValue;
        }
        $('window-overlay').hide();
    },
    getSelectedValues: function() {
        var shippingBranch = document.getElementsByName('selectshipping');
        for (var i = 0; i < shippingBranch.length; i++) {
            if (shippingBranch[i].checked) {
                return selectedShipMValue = shippingBranch[i].value;
            }
        }
    },
    removeClass: function(ele, cls) {
        if (this.hasClass(ele, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            ele.className = ele.className.replace(reg, ' ');
        }
    },
    hasClass: function(element, className) {
        if (element) {
            return element.className && new RegExp("(^|\\s)" + className + "(\\s|$)").test(element.className);
        }
    },
    checkselected: function() {
        var checkPresent = $('s_method_epicor_branchpickup_epicor_branchpickup');
        if (typeof(checkPresent) != "undefined" && checkPresent) {
            var optionChecked = $('s_method_epicor_branchpickup_epicor_branchpickup').checked;
            var action_value = $('branch_pickup').value;
            if ((optionChecked) && (!action_value)) {
                $('brancherrormsg').show();
                $('branchvalidationrmsg').hide();
                return false;
            } else {}
        }
    },
    saveShippingBranch: function() {
        var ajax_url = $('saveshippingbranch').value;
        var action_value = $('branch_pickup').value;
        var url = decodeURIComponent(ajax_url);
        this.ajaxRequest = new Ajax.Request(url, {
            method: 'post',
            parameters: {
                'locationcode': action_value
            },
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(data) {
                var json = data.responseText.evalJSON();
                if (json.type == 'success') {
                    var classnames = $('opc-shipping_method').className;
                    $('loading-mask').hide();
                }
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured in Ajax Call');
            }.bind(this),
            onException: function(request, e) {
                alert(e);
            }.bind(this)
        });
    },
    updateWrapper: function() {
        if ($(this.wrapperId)) {
            var height = 40;
            $$('#' + this.wrapperId + ' > *').each(function(item) {
                height += item.getHeight();
            });
            if (height > ($(document.viewport).getHeight() - 60)) height = $(document.viewport).getHeight() -
                60;
            if (height < 55) height = 55;
            $(this.wrapperId).setStyle({
                'height': height + 'px',
                'marginTop': '-' + (height / 2) + 'px'
            });
        }
    }
};
var cartPage = 'test';
document.observe('dom:loaded', function() {
    cartPage = new Epicor_cartPage.cartselect();
    Event.observe(window, "resize", function() {
        cartPage.updateWrapper();
    });
    var checkPickupPresent = $('branch_pickup');
    if (typeof(checkPickupPresent) != "undefined" && checkPickupPresent) {
        $('branch_pickup').observe('change', function(e) {
            $('brancherrormsg').hide();
            $('branchvalidationrmsg').hide();
            $('branchpickupshipping').checked = true;
        });
        changeOptionValues();
        updateNameShipping();
        onloadhideDiv();
        //
        if (typeof($('billing:use_for_shipping_no')) != "undefined" && $('billing:use_for_shipping_no')) {
            $('billing:use_for_shipping_no').observe('click', function() {
                hideAddressForm();
            });
        }
        if (typeof($('billing:use_for_shipping_yes')) != "undefined" && $('billing:use_for_shipping_yes')) {
            $('billing:use_for_shipping_yes').observe('click', function(e) {
                showAddressForm();
            });
        }
        Billing.prototype.save = function() {
            if (checkout.loadWaiting != false) return;
            var validator = new Validation(this.form);
            if (validator.validate()) {
                checkout.setLoadWaiting('billing');
                if (typeof($('billing:use_for_shipping_no')) != "undefined" && $(
                        'billing:use_for_shipping_no')) {
                    if ($('billing:use_for_shipping_no').checked) {
                        hideAddressForm();
                    }
                }
                if (typeof($('billing:use_for_shipping_yes')) != "undefined" && $(
                        'billing:use_for_shipping_yes')) {
                    if ($('billing:use_for_shipping_yes').checked) {
                        showAddressForm();
                    }
                }
                var request = new Ajax.Request(this.saveUrl, {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(this.form)
                });
            }
        }
        /// $('shipping:same_as_billing').disabled = true;
        /// $('billing:use_for_shipping_yes').disabled = true;
        // $('billing:use_for_shipping_no').disabled = true; 
        // Fix for Back Button in Onepage Checkout Page
        Checkout.prototype.back = function() {
            if (this.loadWaiting) return;
            //Navigate back to the previous available step
            var stepIndex = this.steps.indexOf(this.currentStep);
            var section = this.steps[--stepIndex];
            var sectionElement = $('opc-' + section);
            var selectedShipping = this.checkoptions();
            if ((section == "shipping_method") && (selectedShipping == "branchpickupshipping")) {
                var section = 'shipping';
                var sectionElement = $('opc-' + section);
            }
            //Traverse back to find the available section. Ex Virtual product does not have shipping section
            while (sectionElement === null && stepIndex > 0) {
                --stepIndex;
                section = this.steps[stepIndex];
                sectionElement = $('opc-' + section);
            }
            if ((section == "shipping_method") && (selectedShipping == "branchpickupshipping")) {
                var section = 'shipping';
            }
            this.changeSection('opc-' + section);
        }
        Checkout.prototype.checkoptions = function() {
            var shippingBranch = document.getElementsByName('selectshipping');
            for (var i = 0; i < shippingBranch.length; i++) {
                if (shippingBranch[i].checked) {
                    return selectedShipMValue = shippingBranch[i].value;
                }
            }
        }
        //user clicks "Normal Shipping" option in Shipping Information Page
        $('normalshipping').observe('click', function(e) {
            var checkPresent = $('shipping-address-select');
            if (typeof(checkPresent) != "undefined" && checkPresent) {
                if ($('shipping-address-select').value != "") {
                    $('shipping-new-address-form').hide();
                } else {
                    $('shipping-address-select').value = '';
                    $('shipping-new-address-form').show();
                }
            } else {
                $('shipping-new-address-form').show();
            }
            toggleGuestForm(1);
            var sameasbilling = $('shipping:same_as_billing');
            $('billing:use_for_shipping_no').checked = true;
            if (typeof(sameasbilling) != 'undefined' && sameasbilling != null) {
                $('shipping:same_as_billing').disabled = false;
            }
        });
        //Option buttons for Shipping Information (Branch pickup)
        $('branchpickupshipping').observe('click', function(e) {
            $('shipping-new-address-form').hide();
            var sameasbilling = $('shipping:same_as_billing');
            if (typeof(sameasbilling) != 'undefined' && sameasbilling != null) {
                $('shipping:same_as_billing').disabled = true;
            }
            $('billing:use_for_shipping_no').checked = true;
            toggleGuestForm(0);
        });
    }
    var shippingAddressSelect = $('shipping-address-select');
    if (typeof(shippingAddressSelect) != "undefined" && shippingAddressSelect) {
        //Normal Shipping option select
        $('shipping-address-select').observe('change', function(e) {
            $('brancherrormsg').hide();
            shipping.newAddress(!this.value);
            $('normalshipping').checked = true;
            $('branchpickupshipping').checked = false;
            var selectedString = $('shipping-address-select').options[$('shipping-address-select').selectedIndex]
                .value;
        });
        //Option buttons for Shipping Information (Select a address)
        $('shipping-address-select').observe('mousedown', function(e) {
            if ($('shipping-address-select').value != "") {
                $('shipping-new-address-form').hide();
            } else {
                $('shipping-address-select').value = '';
                if (typeof($('shipping:same_as_billing')) != 'undefined' && $('shipping:same_as_billing') != null) {
                    $('shipping:same_as_billing').checked = false;
                }                
                $('shipping-new-address-form').show();
            }
            //    $('shipping-new-address-form').show();
        });
        //Option buttons for Shipping Information (Select a address)
        $('shipping-address-select').observe('click', function(e) {
            if ($('shipping-address-select').value != "") {
                $('shipping-new-address-form').hide();
            } else {
                $('shipping-address-select').value = '';
                $('shipping-new-address-form').show();
            }
            //    $('shipping-new-address-form').show();
        });
        changeOptionValues();
        //$('billing:use_for_shipping_yes').disabled = true;
        //$('billing:use_for_shipping_no').disabled = true;
    }
});

function selectItemByValue(elmnt, value) {
    for (var i = 0; i < elmnt.options.length; i++) {
        if (elmnt.options[i].value == value) elmnt.selectedIndex = i;
    }
}
//when the user change the location. This function will get triggered
function onchangeBranchPickup() {
    var action_value = $('branch_pickup').value;
    checkBranchpickupChecked();
    if (action_value) {
        $('loading-mask').show();
        changeBranPickupLocation(action_value);
        if (typeof($('shipping:same_as_billing')) != "undefined" && $('shipping:same_as_billing')) {
            $('shipping:same_as_billing').checked = false;
            $('shipping:same_as_billing').disabled = true;
        }    
        $('shipping-new-address-form').hide();
        var validatename = $('form-validatename'); 
        if (typeof(validatename) != "undefined" && validatename) {
            toggleGuestForm(0);
        }            
    }
}
//set shipping method
function checkBranchpickupChecked() {
    var checkPresent = $('s_method_epicor_branchpickup_epicor_branchpickup');
    if (typeof(checkPresent) != "undefined" && checkPresent) {
        var optionChecked = $('s_method_epicor_branchpickup_epicor_branchpickup').checked;
        var action_value = $('branch_pickup').value;
        if ((optionChecked) && (!action_value)) {
            $('s_method_epicor_branchpickup_epicor_branchpickup').checked = true;
            $('brancherrormsg').show();
            return false;
        } else if ((optionChecked) && (action_value)) {
            $('s_method_epicor_branchpickup_epicor_branchpickup').checked = true;
            $('brancherrormsg').hide();
        } else {
            $('s_method_epicor_branchpickup_epicor_branchpickup').checked = true;
        }
    }
}

function isEmpty(value) {
    return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
}
//change brnach pickup location ajax call
function changeBranPickupLocation(action_value) {
    var ajax_url = $('ajaxpickupbranchurl').value;
    var ajaxcode = $('ajaxcode');
    var url = decodeURIComponent(ajax_url);
    if (typeof(ajaxcode) != "undefined" && ajaxcode) {
        var action_value = action_value.getAttribute("href");
        $('loading-mask').show();
    }
    this.ajaxRequest = new Ajax.Request(url, {
        method: 'post',
        parameters: {
            'locationcode': action_value
        },
        onComplete: function(request) {
            this.ajaxRequest = false;
        }.bind(this),
        onSuccess: function(data) {
            var json = data.responseText.evalJSON();
            var theHTML = json.html;
            var theValues = json.values;
            var loccode = json.locationcode;
            var getDetails = json.details;
            if ((json.type == 'success') && (Object.keys(theValues).length != 0)) {
                cartPage.openpopup('ecc_branchpickup_cart', this.href, theValues, loccode);
                return false;
            } else {
                if (getKeys(getDetails)) {
                    cartPage.setbranch('ecc_branchpickup_cart', this.href, loccode);
                    var branchValidation = $('branchvalidationrmsg');
                    var brancherrormsg = $('brancherrormsg');
                    if (typeof(branchValidation) != 'undefined' && branchValidation != null) {
                        $('branchvalidationrmsg').hide();
                    }
                    if (typeof(brancherrormsg) != 'undefined' && brancherrormsg != null) {
                        $('brancherrormsg').hide();
                    }
                    return false;
                }
            }
            $('loading-mask').hide();
        }.bind(this),
        onFailure: function(request) {
            alert('Error occured in Ajax Call');
        }.bind(this),
        onException: function(request, e) {
            alert(e);
        }.bind(this)
    });
}

function getKeys(objects) {
    var r = [];
    for (var key in objects) {
        var keyValue = objects[key];
        var keyIdentify = 'b' + key;
        if ($(keyIdentify)) {
            var keyvaluesinsert = $(keyIdentify).value = keyValue;
        }
    }
    return true;
}


//change the options for checkbox
function changeOptionValues() {
    var useShippingBranch = $('billing:use_for_shipping_no'); // #foo3
    useShippingBranch.nextElementSibling.innerHTML = "Ship to different address / Branch Pickup";
    var checkBranchShipping = $('branch_pickup').value;
    var optionbranchChecked = $('branchpickupshipping').checked;
    if (checkBranchShipping) {
        var useShipping = $('billing:use_for_shipping_yes');
        if (typeof(useShipping) != "undefined" && useShipping) {
            useShipping.parentNode.removeChild(useShipping.nextSibling);
            useShipping.remove();
        }
        var sameAsBilling = $('shipping:same_as_billing');
        if (typeof(sameAsBilling) != "undefined" && sameAsBilling) {
            sameAsBilling.parentNode.hide(sameAsBilling.nextSibling);
            //sameAsBilling.remove();
            $('billing:use_for_shipping_no').checked = true;
            $('shipping-new-address-form').hide();
        }
    }
}

//Validate first name/last name for the guest
function checkBranchName() {
    var branchFieldPresent = $('form-validatename'); // #foo3
    if (typeof(branchFieldPresent) != "undefined" && branchFieldPresent) {
        var dataForm = new VarienForm('form-validatename', true);
        return dataForm.validator.validate();
    } else {
        return true;
    }
}

//Show hide firstname/last name for the guest
function toggleGuestForm(hide) {
    var branchFieldPresent = $('form-validatename'); // #foo3
    if (typeof(branchFieldPresent) != "undefined" && branchFieldPresent) {
        if (hide) {
            $('guestname').hide();
        } else {
            $('guestname').show();
        }
    }
}


//Update the firstname& last name to shipping
function updateNameShipping() {
    var branchFieldPresent = $('form-validatename'); // #foo3
    if (typeof(branchFieldPresent) != "undefined" && branchFieldPresent) {
        $('billing:firstname').observe('change', function(e) {
            $('bfirstname').value = $('billing:firstname').value;
        });
        $('billing:lastname').observe('change', function(e) {
            $('blastname').value = $('billing:lastname').value;
        });
        //$('blastname').value  = $('billing:lastname').value;
    }
}


//Hide Div's based on the checkbox condition
function onloadhideDiv() {
    if (typeof($('billing:use_for_shipping_no')) != "undefined" && $('billing:use_for_shipping_no')) {
        if ($('billing:use_for_shipping_no').checked) {
            hideAddressForm();
        }
    }
    if (typeof($('billing:use_for_shipping_yes')) != "undefined" && $('billing:use_for_shipping_yes')) {
        if ($('billing:use_for_shipping_yes').checked) {
            showAddressForm();
        }
    }
}

//Show Address Form
function showAddressForm() {
    var branchFieldPresent = $('form-validatename'); // #foo3
    if (typeof(branchFieldPresent) != "undefined" && branchFieldPresent) {
        toggleGuestForm(1);
        $('shipping-new-address-form').show();
    }
    if (typeof($('shipping:same_as_billing')) != "undefined" && $('shipping:same_as_billing')) {
        $('shipping:same_as_billing').disabled = false;
    }
    $('branchpickupshipping').checked = false;
    $('normalshipping').checked = true;
}


//Hide Address Form
function hideAddressForm() {
    if($('isbranchselected') && $('isbranchselected').value !== ""){
        var branchFieldPresent = $('form-validatename'); // #foo3
        $('shipping-new-address-form').hide();
        $('branchpickupshipping').checked = true;
        if (typeof($('shipping:same_as_billing')) != "undefined" && $('shipping:same_as_billing')) {
            if (typeof($('billing:use_for_shipping_yes')) != "undefined" && $('billing:use_for_shipping_yes')) {
                $('shipping:same_as_billing').disabled = true;
            } else {
            sameAsBilling();  
            }
        }
        $('normalshipping').checked = false;
        if (typeof(branchFieldPresent) != "undefined" && branchFieldPresent) {
            toggleGuestForm(0);
        }
        var checkPresent = $('shipping-address-select');
        if (typeof(checkPresent) != "undefined" && checkPresent) {
            $('shipping-new-address-form').hide();
        }
    }
}

function sameAsBilling(){
    var sameAsBilling = $('shipping:same_as_billing');
    if (typeof(sameAsBilling) != "undefined" && sameAsBilling) {
        sameAsBilling.parentNode.hide(sameAsBilling.nextSibling);
    }    
}