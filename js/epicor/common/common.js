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
    $$('#' + table_id + ' tbody tr' + table_extra).findAll(function(el){return el.visible();}).each(function (e) {
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
        colorRows(table_id,'');
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
    var rowCount = $$('#' + table + '_table tbody tr.' + rowclass).findAll(function(el){return el.visible();}).length;
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
     initialize : function(name,address, telephone, instructions)
    {
        this.setData(name, address, telephone, instructions);          
        this.name = name;
        this.address = address;
        this.telephone = telephone;
        this.instructions = instructions;
    },
    setData: function(name,address, telephone, instructions) {       
        
        var limitarray = {
                '_name'             : 'name',                   // key contained within id of input field : store config value to be applied
                'firstname'         : 'name',
                'lastname'          : 'name',
                'company'           : 'name',
                '_address'          : 'address',
                'street'            : 'address',
                'telephone'         : 'telephone',
                '_phone'            : 'telephone',
                'mobile'            : 'telephone',
                'fax'               : 'telephone',
                'instructions'      : 'instructions'
            };
        var limitValues = {
            'name'          :   name,
            'address'       :   address,
            'telephone'     :   telephone,
            'instructions'  :   instructions
        }  
        var excludeValues = [
            'email'
           ,'email_address'
           ,'rfq_address_details' 
           ,'delivery_address_code' 
           ,'billing_address_code' 
           ,'shipping_address_code' 
           ,'b2b_companyreg' 
        ]
        Object.keys(limitarray).forEach(function (key) {
            $$('form input[id *="'+ key +'"]', 'form textarea[id *="'+ key +'"]','div input[id *="'+ key +'"]', 'div textarea[id *="'+ key +'"]').each(function(o){
               
                if(excludeValues.indexOf(o.id) == -1){            // don't process if field is in the excludeValues array
                    o.maxLength = limitValues[ limitarray[key] ];
                    o.addClassName('maximum-length-' + limitValues[ limitarray[key] ]);
                    if(o.value.length > limitValues[ limitarray[key] ] && limitValues[limitarray[key]] != 10234){                      // this bit limits existing fields to the config length if not unlimited(10234)
                       o.value = o.value.substring(0, limitValues[ limitarray[key] ]);
                    } 
                   if(limitValues[limitarray[key]] != 10234){   
                       if(!$('truncated_message_'+o.id)){
                           if(o.type !='hidden' &&  o.type !='checkbox'){                   // don't apply if input field not displayed                               
                                var message = 'max '+limitValues[limitarray[key]]+' chars'; 
                                o.insert({after:'<div id="truncated_message_'+o.id+'">' + message + '</div>'});
                           }
                       }
                   } 
                }
            })   
        }); 
            
    } 
} 

function positionOverlayElement(elementId) {
    var availableHeight = $(document.viewport).getHeight();
        var elementHeight = 0;
        if (this.height) {
            if (this.height < availableHeight * .6) {
                elementHeight = availableHeight * .6;
            } else {
                elementHeight = this.height;
            }
        } else {
            elementHeight = parseInt(availableHeight * .8);
        }
        var elementWidth = $(elementId).getWidth();
        $(elementId).select('.box-account').each(function (z) {
            layout = new Element.Layout(z);
            boxAccountPaddingHeight = layout.get('padding-top');
            boxAccountPaddingBottom = layout.get('padding-bottom');
            elementHeight += boxAccountPaddingHeight;
        });
        $(elementId).setStyle({'height': elementHeight + 'px'});
        elementWidth = $(document.viewport).getWidth() * .8;
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
            $(elementId).setStyle({
                'height': height + 'px',
                'width': elementWidth + 'px',
                'marginTop': top + 'px',
                'marginLeft': left + 'px',
            });
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