var attachments_count = 0;
var line_attachment_count = 0;
var contact_count = 0;
var salesrep_count = 0;
var line_count = 0;
var lineadd_count = 1;
var laSearchForm = [];
var configurator_line = '';
var configure_id = '';
var optionsPrice;
var rfqHasChanges = false;


document.observe('dom:loaded', function () {

    if ($('loading-mask')) {
        $('loading-mask').hide();
    }

    if ($('loading-mask')) {
        $('loading-mask').hide();
    }
    
    Event.live('#rfq_save', 'click', function (el, event) {
        $('loading-mask').show();

        if (!validateRfqForm()) {
            $('loading-mask').hide();
            event.stop();
        } else {
            rfqSubmit();
            if(window.nonErpProductItems){
                $('loading-mask').hide();
                moreInformationBox(window.msgText, 200, 150, 'confirm_html', true, 'quote');         
                return;
            }
            var url = $('rfq_update').readAttribute('action');
            var rfqDatas = $('rfq_update').serialize();
            
            if ($('rfq_serialize_data').setValue(rfqDatas))
            {
                $$('#rfq_update input[type="file"]').each(function (elem) {
                    if (elem.name != '')
                    {
                        $('rfq_serialize_data').insert({
                            after: elem.clone(true)
                        });
                    }

                });
                $('rfq_submit_wrapper_form').submit();
            }
        }

    });


    Event.live('#rfq_duplicate', 'click', function (el, event) {
        if (!rfqHasChanges || confirm(Translator.translate('There are unsaved changes to this quote. These changes will be lost. Are you sure you wish to continue?'))) {
            $('loading-mask').show();
            window.location = $('duplicate_url').value;
        }
        event.stop();
    });

    Event.live('#rfq_confirm', 'click', function (el, event) {
        $('loading-mask').show();
        submitConfirmReject('confirm');
        event.stop();
    });

    Event.live('#rfq_reject', 'click', function (el, event) {
        $('loading-mask').show();
        submitConfirmReject('reject');
        event.stop();
    });

    Event.live('#rfq_checkout', 'click', function (el, event) {
        $('loading-mask').show();
        window.location.replace($('checkout_url').value);
        event.stop();
    });

    // if custom address reselected 
    Event.live('#rfq_update input', 'keypress', function (el, event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });
    
    Event.live('#rfq_update *', 'change', function(el, event){
        if (!el.up('#line-add') && el.type != 'checkbox') {
            rfqHasChanged();
        }
    });

});

function validateRfqForm() {

    valid = true;
    errorMessage = '';


    var rfqform = new varienForm($('rfq_update'));
    var valid = rfqform.validate();

    if (!valid) {
//        if($$('.fancybox-skin').length > 0){            
//            $j.fancybox.close();
//        }
        errorMessage += Translator.translate('One or more options is incorrect, please see page for details') + '\n';
    }

    if ($('rfq_new')) {
        contacts = $$('#rfq_contacts_table tbody tr.contacts_row');
        if (contacts.length == 0) {
            errorMessage += Translator.translate('You must supply at least one Contact') + '\n';
        }

        lines = $$('#rfq_lines_table tbody tr.lines_row');
        if (lines.length == 0) {
            errorMessage += Translator.translate('You must supply at least one Line') + '\n';
        }
    }

    var configuratorError = false;

    $$('.lines_configured').each(function (e) {
        if (e.value == 'TBC') {
            configuratorError = true;
        }
    });

    if (configuratorError) {
        errorMessage += Translator.translate('One or more lines require configuration, please see lines with a "Configure" link') + '\n';
    }

    if (errorMessage != '') {
        valid = false;
        alert(errorMessage);
    }

    return valid;
}

function submitConfirmReject(action) {
    var url = $(action + '_url').value;
    var form_data = $('rfq_update').serialize(true);

    performAjax(url, 'post', form_data, function (data) {
        var json = data.responseText.evalJSON();
        if (json.type == 'success') {
            if (json.redirect) {
                window.location.replace(json.redirect);
            }
        } else {
            $('loading-mask').hide();
            if (json.message) {
                showMessage(json.message, json.type);
            }
            if(json.errordescription){                
                showMessage(json.errordescription['text'], json.type);
            }
        }
    });

}

function hideButtons() {
    if ($('rfq_confirm')) {
        $('rfq_confirm').hide();
    }
    if ($('rfq_reject')) {
        $('rfq_reject').hide();
    }
    if ($('rfq_checkout')) {
        $('rfq_checkout').hide();
    }
}

function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

function showMessage(txt, type, position) {
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    if(position == false){    
        $('messages').update(html);        
    }else{
        $('messages').insert(html);        
    }
}

function deleteWarning(el) {
    var allowDelete = true;
    if (confirm(Translator.translate('Are you sure you want to delete selected line?')) === false) {
        allowDelete = false;
    }
    return allowDelete;
}

function valueEmpty(value) {

    if (value == '' || value == undefined || value == 0) {
        return true;
    }

    return false;
}

function rfqHasChanged()
{
    rfqHasChanges = true;
}