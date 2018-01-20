if (typeof Epicor_LocationEditSelector == 'undefined') {
    var Epicor_LocationEditSelector = {};
}
Epicor_LocationEditSelector.locationedit = Class.create();
Epicor_LocationEditSelector.locationedit.prototype = {
    target: null,
    wrapperId: "selectLocationEditWrapperWindow", //selectProductWrapperWindow
    initialize: function () {
        if (!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
    },
    openpopup: function (newtarget, ahref) {
        this.target = newtarget;
        if ($(this.wrapperId))
            $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $('loading-mask').show();
        $(document.body).insert(wrappingDiv);

        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function (o) {                // id = messages1
            if (o.selected == true) {
                website = o.value;
            }
        })
        var productGridUrl = ahref;
        $('branchpickupshipping').checked = true;
        this.ajaxRequest = new Ajax.Request(productGridUrl, {
            method: 'post',
            parameters: {field_id: newtarget, website: website},
            onComplete: function (request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function (request) {
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();
                $('window-overlay').show();
                this.updateWrapper();
            }.bind(this),
            onFailure: function (request) {
                alert('Error occured loading location form page');
                this.closepopup();
            }.bind(this),
            onException: function (request, e) {
                alert('Error occured loading location form page');
                this.closepopup();
            }.bind(this)
        });

    },
    locationeditsubmit: function () {
        var dataForm = new VarienForm('form-validate', true);
        if (dataForm.validator && dataForm.validator.validate()) {
            updateLocationValues();
        }
        return false;
    },
    closepopup: function () {
        $(this.wrapperId).remove();
        $('window-overlay').hide();
    },
    updateWrapper: function () {
        if ($(this.wrapperId)) {
            var height = 40;

            $$('#' + this.wrapperId + ' > *').each(function (item) {
                height += item.getHeight();
            });

            if (height > ($(document.viewport).getHeight() - 60))
                height = $(document.viewport).getHeight() - 60;

            if (height < 55)
                height = 55;

            $(this.wrapperId).setStyle({
                'height': height + 'px',
                'marginTop': '-' + (height / 2) + 'px'
            });
        }
    }
};

var LocationEditSelector = 'test';
document.observe('dom:loaded', function () {
    LocationEditSelector = new Epicor_LocationEditSelector.locationedit();
    Event.observe(window, "resize", function () {
        LocationEditSelector.updateWrapper();
    });
});


//change brnach pickup location ajax call
function updateLocationValues(action_value) {
    var ajax_url = $('savelocation').value;
    var ajaxcode = $('ajaxcode');
    var url = decodeURIComponent(ajax_url);
    if (typeof (ajaxcode) != "undefined" && ajaxcode) {
        var action_value = action_value.getAttribute("href");
        $('loading-mask').show();
    }
    var serializeVals = $('form-validate').serialize();
    this.ajaxRequest = new Ajax.Request(url, {
        method: 'post',
        parameters: {
            'data': serializeVals
        },
        onComplete: function (request) {
            this.ajaxRequest = false;
        }.bind(this),
        onSuccess: function (data) {
            var json = data.responseText.evalJSON();
            var address1 = json.data.address1;
            var city = json.data.city;
            var county_id = json.data.county_id;
            var region = json.data.region;
            var country = json.data.country;
            var postcode = json.data.postcode;
            var country = json.data.country;
            var telephoneNumber = json.data.telephone_number;
            var locationcode = json.data.locationcode;
            var firstname = json.data.firstname;
            var lastname = json.data.lastname;
            if (json.type == 'success') {
                if (typeof (firstname) != 'undefined' && firstname != null) {
                    $('bfirstname').value = firstname;
                }
                if (typeof (lastname) != 'undefined' && lastname != null) {
                    $('blastname').value = lastname;
                }
                if (typeof (address1) != 'undefined' && address1 != null) {
                    $('bstreet1').value = address1;
                }
                if (typeof (city) != 'undefined' && city != null) {
                    $('bcity').value = city;
                }
                if (typeof (region) != 'undefined' && region != null) {
                    $('bregion').value = region;
                }
                if (typeof (county_id) != 'undefined' && county_id != null) {
                    $('bregion_id').value = county_id;
                }
                if (typeof (country) != 'undefined' && country != null) {
                    $('bcountry_id').value = country;
                }
                if (typeof (postcode) != 'undefined' && postcode != null) {
                    $('bpostcode').value = postcode;
                }
                if (typeof (telephoneNumber) != 'undefined' && telephoneNumber != null) {
                    $('btelephone').value = telephoneNumber;
                }
            }
            $('loading-mask').hide();
            $('branchvalidationrmsg').hide();
            $('brancherrormsg').hide();
            LocationEditSelector.closepopup();
        }.bind(this),
        onFailure: function (request) {
            alert('Error occured in Ajax Call');
        }.bind(this),
        onException: function (request, e) {
            alert(e);
        }.bind(this)
    });
}


//close the popup window on key press(ESC)
window.onkeypress = function (event) {
    var branchWindowExist = $("selectLocationEditWrapperWindow");
    if (!branchWindowExist) {
        return true;
    }
    if (event.which == 13 || event.keyCode == 13) {
        return false;
    } else if (event.which == 27 || event.keyCode == 27) {
        if (typeof (branchWindowExist) != 'undefined' && branchWindowExist != null) {
            $('selectLocationEditWrapperWindow').remove();
            $('window-overlay').hide();
        }
    }
    return true;
};