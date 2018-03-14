Event.live = function (s, e, f) {
    Event.observe(document, e, function (event, element) {
        if (element = event.findElement(s)) {
            f(element, event);
        }
    });
}

function performAjax(url, method, data, onSuccessFunction) {

    if ($('loading-mask')) {
        $('loading-mask').show();
    }

    this.ajaxRequest = new Ajax.Request(url, {
        method: method,
        parameters: data,
        onComplete: function (request) {
            this.ajaxRequest = false;
        }.bind(this),
        onSuccess: function (data) {
            onSuccessFunction(data);
        }.bind(this),
        onFailure: function (request) {
            if ($('loading-mask')) {
                $('loading-mask').hide();
            }
            alert(Translator.translate('Error occured in Ajax Call'));
        }.bind(this),
        onException: function (request, e) {
            if ($('loading-mask')) {
                $('loading-mask').hide();
            }
            alert(e);
        }.bind(this)
    });

    return this.ajaxRequest;
}

function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

function colorRows(table_id, table_extra) {

    var cssClass = 'even';
    $$('#' + table_id + ' tbody tr' + table_extra).findAll(function (el) {
        return el.visible();
    }).each(function (e) {
        if (e.visible()) {
            e.removeClassName('even');
            e.removeClassName('odd');
            e.addClassName(cssClass);

            if (cssClass == 'even') {
                cssClass = 'odd';
            } else {
                cssClass = 'even';
            }
        }
    });
}

function deleteElement(el, table_id) {
    var disabled = false;
    if (el.checked) {
        disabled = true;
    }
    if (el.parentNode.parentNode.hasClassName('new')) {
        el.parentNode.parentNode.remove();
        colorRows(table_id, '');
    } else {
        el.parentNode.parentNode.select('input[type=text],input[type=file],select,textarea').each(function (input) {
            input.disabled = disabled;
        });
    }
}

function resetInputs(row) {
    row.select('input,select,textarea').each(function (e) {
        if (e.readAttribute('type') == 'text' || e.tagName == 'textarea') {
            e.writeAttribute('value', '');
        } else if (e.readAttribute('type') == 'checkbox') {
            e.writeAttribute('checked', false);
        }

        e.writeAttribute('disabled', false);
    });

    return row;
}

function checkCount(table, rowclass, colspan) {
    var rowCount = $$('#' + table + '_table tbody tr.' + rowclass).findAll(function (el) {
        return el.visible();
    }).length;
    if (rowCount == 0) {
        row = '<tr class="even" style="">'
                + '<td colspan="' + colspan + '" class="empty-text a-center">' + Translator.translate('No records found.') + '</td>'
                + '</tr>';

        $(table + '_table').down('tbody').insert({bottom: row});

    }
}

function formatNumber(el, allowNegatives, allowFloats) {
    var value = el.value, firstChar, nextFirst;
    if (value.length == 0)
        return;

    firstChar = value.charAt(0);
    if (allowFloats) {
        value = value.replace(/[^0-9\.]/g, '');
        nextFirst = value.charAt(0);
    } else {
        value = parseInt(value);
        nextFirst = '';
    }

    if (nextFirst == '.') {
        value = '0' + value;
    }

    if (allowNegatives && firstChar == '-') {
        value = firstChar + value;
    }

    el.value = value;
}
var checkLengthLimits = Class.create();
checkLengthLimits.prototype = {
    initialize: function (name, address, telephone, instructions)
    {
        this.setData(name, address, telephone, instructions);
        this.name = name;
        this.address = address;
        this.telephone = telephone;
        this.instructions = instructions;
    },
    setData: function (name, address, telephone, instructions) {

        var limitarray = {
            '_name': 'name', // key contained within id of input field : store config value to be applied
            'firstname': 'name',
            'lastname': 'name',
            'company': 'name',
            '_address': 'address',
            'street': 'address',
            'telephone': 'telephone',
            '_phone': 'telephone',
            'mobile': 'telephone',
            'fax': 'telephone',
            'instructions': 'instructions'
        };
        var limitValues = {
            'name': name,
            'address': address,
            'telephone': telephone,
            'instructions': instructions
        }
        var excludeValues = [
            'email'
                    , 'email_address'
                    , 'rfq_address_details'
                    , 'delivery_address_code'
                    , 'billing_address_code'
                    , 'shipping_address_code'
                    , 'b2b_companyreg'
        ]
        Object.keys(limitarray).forEach(function (key) {
            $$('form input[id *="' + key + '"]', 'form textarea[id *="' + key + '"]', 'div input[id *="' + key + '"]', 'div textarea[id *="' + key + '"]').each(function (o) {

                if (excludeValues.indexOf(o.id) == -1) {            // don't process if field is in the excludeValues array
                    o.maxLength = limitValues[ limitarray[key] ];
                    o.addClassName('maximum-length-' + limitValues[ limitarray[key] ]);
                    if (o.value.length > limitValues[ limitarray[key] ] && limitValues[limitarray[key]] != 10234) {                      // this bit limits existing fields to the config length if not unlimited(10234)
                        o.value = o.value.substring(0, limitValues[ limitarray[key] ]);
                    }
                    if (limitValues[limitarray[key]] != 10234) {
                        if (!$('truncated_message_' + o.id)) {
                            if (o.type != 'hidden' && o.type != 'checkbox') {                   // don't apply if input field not displayed                               
                                var message = 'max ' + limitValues[limitarray[key]] + ' chars';
                                o.insert({after: '<div id="truncated_message_' + o.id + '">' + message + '</div>'});
                            }
                        }
                    }
                }
            })
        });

    }
}

function positionOverlayElement(elementId, useWidth, useHeight, noHeight) {
    var availableHeight = $(document.viewport).getHeight();
    var elementHeight = 0;
    if (this.height) {
        if (this.height < availableHeight * .6) {
            elementHeight = availableHeight * .6;
        } else {
            elementHeight = this.height;
        }
    } else {
        if (useHeight) {
            elementHeight = useHeight;
        } else {
            elementHeight = parseInt(availableHeight * .8);
        }
    }
    var elementWidth = $(elementId).getWidth();
    $(elementId).select('.box-account').each(function (z) {
        layout = new Element.Layout(z);
        boxAccountPaddingHeight = layout.get('padding-top');
        boxAccountPaddingBottom = layout.get('padding-bottom');
        elementHeight += boxAccountPaddingHeight;
    });
    if (noHeight == undefined) {
        $(elementId).setStyle({'height': elementHeight + 'px'});
    }
    elementWidth = $(document.viewport).getWidth() * .8;
    
    if (useWidth !== undefined) {
        elementWidth = useWidth;
    }
    
    var availableWidth = $(document.viewport).getWidth();
    if ((availableWidth - elementWidth) < 0) {
        var left = 0;
    } else {
        var left = (availableWidth - elementWidth) / 2;
    }
    if ((availableHeight - elementHeight) < 0) {
        var top = 20;
    } else {
        var top = (availableHeight - elementHeight) / 2;
    }

    if ($(elementId)) {
        var height = 22;
        $$('#' + elementId).each(function (item) {
            height += item.getHeight();
        });

        if (height > ($(document.viewport).getHeight() - 40))
            height = $(document.viewport).getHeight() - 40;

        if (height < 35) {
            height = 35;
            top:0;
        }
        
        if (useHeight !== undefined) {
            height = useHeight;
        }
        
        if (useWidth !== undefined) {
            elementWidth = useWidth;
        }
        
        $(elementId).setStyle({
            'width': elementWidth + 'px',
            'marginTop': top + 'px',
            'marginLeft': left + 'px',
        });
        
        if (noHeight == undefined) {
            $(elementId).setStyle({
                'height': height + 'px',
            });
        }
    }
}

Validation.addAllThese([
    ['validate-list-code', 'List Code is already taken by another list. Please enter a different  code.', function (v) {
            var url = $('listcodeurl').value;
            new Ajax.Request(url, {
                method: 'post',
                asynchronous: false,
                parameters: {'erp_code': v},
                onSuccess: function (data) {

                    var json = data.responseText.evalJSON();
                    if (json.error == 1) {
                        $('code_allowed').value = 'false';
                    } else {
                        $('code_allowed').value = 'true';
                    }
                }
            });
            if ($('code_allowed').value === 'true') {
                return true;
            } else {
                return false;
            }

        }]]);

function checkDecimal(el, allowemptyqty) {
    if (allowemptyqty) {
        allowemptyqty = allowemptyqty;
    } else {
        allowemptyqty = 0;
    }
    $(el).select('.validation-advice').each(function (errorOccurance) {
        errorOccurance.remove();
    });
    error = 0;
    $(el).select('.qty').each(function (qtyOccurance) {
        value = qtyOccurance.value.trim();
        decimalPlaces = qtyOccurance.getAttribute('decimal');
        msg = "Decimal Places not Permitted";
        if (decimalPlaces > 0) {
            zero = '';
            for (j = 0; j < decimalPlaces; j++) {
                zero = zero + 'x';
            }
            msg = "Qty must be in the form of xxx." + zero;
        }
        if (decimalPlaces != '')
        {
            if (value != '')
            {
                var numNum = +value;
                if (!isNaN(numNum))
                {
                    if ((value != 0 && allowemptyqty == 1) || (value > 0 && allowemptyqty == 0) || (value == 0 && allowemptyqty == 1))
                    {
                        var isdecimal = (value.match(/\./g) || []).length;
                        var decimal = 0;
                        if (isdecimal > 0)
                        {
                            decimal = parseInt(value.toString().split(".")[1].length || 0);
                        }
                        if ((decimalPlaces == 0 && isdecimal > 0) || (decimalPlaces > 0 && isdecimal > 0 && decimal == 0) || (decimalPlaces > 0 && decimal > 0 && decimal > decimalPlaces) || (decimalPlaces == 0 && decimal > 0) || (allowemptyqty == 1 && value == 0))
                        {
                            qtyOccurance.insert({after: new Element('div').addClassName('validation-advice').update(msg)});
                            error = error + 1;

                        } else
                        {
                            //  return true;
                        }
                    }
                } else
                {
                    qtyOccurance.insert({after: new Element('div').addClassName('validation-advice').update("Enter a Valid Qty")});
                    error = error + 1;
                }
            } else
            {
                if (allowemptyqty == 1 && error > 1)
                {
                    error = error - 1;
                }
                if (allowemptyqty == false)
                {
                    qtyOccurance.insert({after: new Element('div').addClassName('validation-advice').update(msg)});
                    error = error + 1;
                }
            }
        }
    });

    if (error == 0)
    {
        return true;
    } else
    {
        return false;
    }



}
function preventFormSubmit(form, el, allowemptyqty) {
    if (allowemptyqty) {
        allowemptyqty = allowemptyqty;
    } else {
        allowemptyqty = 0;
    }
    Event.observe(form, 'submit', function (event) {
        if (checkDecimal(el, allowemptyqty))
        {
            return true;
        } else
        {
            Event.stop(event);
        }
    });

}

Event.observe(document, 'click', function (event) {
    var element = Event.element(event).up(1);
    if (typeof element !== "undefined" && element.hasClassName('btn-empty'))
    {
        $('updatecart').select('.qty').each(function (qtyOccurance) {
            qtyOccurance.value = 0;
        });
        $('updatecart').select('.validation-advice').each(function (errorOccurance) {
            console.log(errorOccurance);
            errorOccurance.remove();
            check = 0;
        });
        return true;

    }
});
document.observe('dom:loaded', function () {      
    //only run nonerpproductcheck on page load when in cart
    
     //add reorder-button class to my account orders page reorder button
    $$('.title-buttons .link-reorder').each(function(a){
        if(a.href.indexOf('/reorder/') != -1){            
            if(!a.hasClassName('reorder-button')){
                a.addClassName('reorder-button');
            }
        }
    })
    
    $$('.reorder-button').invoke('observe', 'click', function(event) {
        var  baseurl = location.protocol + "//" + location.hostname;
        
        var href = this.href;       
        
        var url = baseurl + '/epicor/sales_order/cartReorderOption/?isAjax=true';               
        new Ajax.Request(url, {
            method: 'post',
            asynchronous: false,
            requestHeaders: {Accept: 'application/json'},
            onComplete: function (transport) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function (transport) {
                var response = JSON.parse(transport.responseText);
                  if(response.success == 'prompt' && response.existing_cart_items){
                    event.preventDefault();  
                    if(!$('confirm_html')){
                        $$('body').first().insert('<div id="confirm_html" style="display:none; background:white;"></br><div class="message" style="margin-left:8px;"></div></br><span id="confirm_buttons"><button style="margin-right:10px" id="confirm_button_yes">Yes</button><button id="confirm_button_no" >No</button><button id="confirm_button_cancel" style="margin-left:10px">Cancel</button></span></br></div>');
                    }
                    confirmMessage(href); 
                    var urlMinusBase = href.split(location.hostname + '/');
                    var positionOfFirstSlash = urlMinusBase[1].indexOf('/')
                    var positionOfSecondSlash = urlMinusBase[1].indexOf('/', positionOfFirstSlash + 1)
                    var positionOfThirdSlash = urlMinusBase[1].indexOf('/', positionOfSecondSlash + 1)
                    var controllerAction = urlMinusBase[1].substring(0, positionOfThirdSlash);
           
                    $$("#window-overlay #confirm_button_yes").invoke('observe', 'click', function() {    
                        removeCartItems = 1;
                        var rebuiltUrl = urlMinusBase[0] + location.hostname + '/' + controllerAction + '/' + 'removeFromCart/' + removeCartItems + '/' + urlMinusBase[1].substring(positionOfThirdSlash + 1);
                        window.location.href = rebuiltUrl;
                    })
                    $$("#window-overlay #confirm_button_no").invoke('observe', 'click', function() {        
                        removeCartItems = 0;
                        var rebuiltUrl = urlMinusBase[0] + location.hostname + '/' + controllerAction + '/' + 'removeFromCart/' + removeCartItems + '/' + urlMinusBase[1].substring(positionOfThirdSlash + 1);
                        window.location.href = rebuiltUrl;
                    })
                    $$("#window-overlay #confirm_button_cancel").invoke('observe', 'click', function() {        
                        $('window-overlay').hide();
                    })
                }                
            }.bind(this),
            onFailure: function (request) {
                console.log('Error occured retrieving Cart Merge option');
            }.bind(this),
            onException: function (request, e) {
                console.log('Error occured retrieving Cart Merge option');
                console.log(e);
            }.bind(this)
        });
        
    })    
})

confirmMessage = function (href) {
    if (!$('window-overlay')) {
        $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
    }
    
    if (!$('loading-mask')) {
        $(document.body).insert(
            '<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>'
        );
    }

    $('confirm_html').hide();
    $$('#confirm_html .message').first().update('Remove Existing Items From Cart?');
    $('window-overlay').appendChild($('confirm_html').remove())
    $('confirm_html').show();
    $('window-overlay').show();
    positionOverlayElement('confirm_html', 260, 110);
};    

/*START ship status collection*/
var shipStatusCollectiondata;
function shipCollection(value, visible, shipcount) {
    var count = shipcount;
    var ship_status_visible = visible;
    if (count > 0 && ship_status_visible) {
        var shipStatusCollection = shipStatusCollectiondata;
        if (shipStatusCollection) {
            var JSONObject = JSON.parse(shipStatusCollection);
            for (var i = 0; i < JSONObject.length; i++) {
                if (value == JSONObject[i]["code"]) {
                    var status_help = JSONObject[i]["status_help"];
                    $$('.status_help>i').invoke('update', status_help);
                }
            }
        }
    }
}
document.observe('dom:loaded', function () {
    if (document.getElementById('ship_status_erpcode') != null) {
        shipCollection(document.getElementById('ship_status_erpcode').value, 1, 1);
    }
    if (document.getElementById('ship_status_erpcodedefault') != null) {
        shipCollection(document.getElementById('ship_status_erpcodedefault').value, 1, 1);
    }
});
/*END ship status collection script*/
