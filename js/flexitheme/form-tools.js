/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var submited_field_sets = 0;
var submited_field_sets_orig = 0;
var submited_error = false;
function submitTabs1By1() {
    $('loading-mask').show();

    var entity_id = jQuery('#edit_form #entity_id').val();
    var theme_name = jQuery('#edit_form #theme_name').val();
    var editform = new varienForm($('edit_form'))
    var valid = editform.validate();
    if (!valid) {
        $('loading-mask').hide();
        jQuery('#messages').html('<ul class="messages"><li class="error-msg">Errors Found in Form</li></messages>');
        jQuery('#form_tabs a.error')[0].click();
    } else {

        var url = jQuery('#name_validation_url').val();
        var form_data = jQuery('#edit_form #entity_id').add(jQuery('#edit_form #theme_name')).add(jQuery('#edit_form input[name=form_key]'));
        this.ajaxRequest = new Ajax.Request(url, {
            method: 'post',
            parameters: form_data.serialize(),
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(data) {
                var json = data.responseText.evalJSON();
                if (json.type == 'success') {
                    if (entity_id == '') {
                        var url = jQuery('#edit_form').attr('action').replace('save', 'ajaxSave');
                        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
                        var form_data = jQuery('#edit_form #entity_id').add(jQuery('#edit_form #theme_name')).add(jQuery('#edit_form input[name=form_key]'));
                        jQuery.ajax({
                            url: url,
                            type: 'POST',
                            data: form_data.serialize(),
                            async: true,
                            enctype: 'multipart/form-data',
                            success: function(data) {
                                if (parseFloat(data) == parseInt(data, 10) && !isNaN(data))
                                    jQuery('#edit_form #entity_id').val(data);

                                startSavingTabs1By1(jQuery('#edit_form .fieldset'));

                            },
                            error: function(data) {

                            }
                        });
                    } else {

                        var fieldsets = null;
                        jQuery('#form_tabs a.changed').each(function() {
                            var id = this.id;
                            fieldsets = jQuery('#' + id + '_content .fieldset').add(fieldsets);
                        });
                        startSavingTabs1By1(fieldsets);
                    }
                } else {
                    $('loading-mask').hide();
                    if (json.message) {
                        showMessage(json.message, json.type);
                    }
                }
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured in Ajax Call');
            }.bind(this),
            onException: function(request, e) {
                alert(e);
            }.bind(this)
        });
    }
}

function startSavingTabs1By1(fieldsets) {

    jQuery('#edit_form').find('.fieldset').hide();
    var loading_bar = '<span style="height: 18px; display: block; margin: 0px -21px; width: 190px; border: 1px solid rgb(228, 140, 84); background: none repeat scroll 0px 0px rgb(255, 255, 255);"></span><span id="solarsoft_loading_bar" style="height: 18px; display: block; border: 1px solid rgb(228, 140, 84); margin: -20px -21px 0px; width: 1px; background: none repeat scroll 0px 0px rgb(228, 140, 84);"></span><span id="solarsoft_loading_text" style="height: 18px; display: block; width: 190px; border: 1px solid rgb(228, 140, 84); background: none repeat scroll 0% 0% transparent; margin: -20px -21px 0px;">0%</span>';
    jQuery('#loading_mask_loader').append(loading_bar);
    if (fieldsets != null) {
        submited_field_sets = fieldsets.length;
        submited_field_sets_orig = submited_field_sets;
        fieldsets.each(function() {
            var action = jQuery('#edit_form').attr('action').replace('save', 'ajaxSave');
            var none_image_data = jQuery(this).find('select, textarea, input:not(input.img_upload_field)').add(jQuery('#edit_form #entity_id')).add(jQuery('#edit_form #theme_name')).add(jQuery('#edit_form input[name=form_key]'));
            submitData(none_image_data, action);
        });
    } else {

        jQuery('#solarsoft_loading_bar').css({
            'width': '190px'
        });
        jQuery('#solarsoft_loading_text').html('100%');
        jQuery('#edit_form').find('select, textarea, input:not(input.img_upload_field, input[name=form_key], #theme_name, #entity_id)').remove();
        jQuery('#edit_form').submit()
    }
}

function submitTabs1By1AndContinueEdit() {
    var form_action = jQuery('#edit_form').attr('action');
    jQuery('#edit_form').attr('action', form_action + 'back/edit/');
    submitTabs1By1();
}
/**
 * @todo add validation for fields
 */
function submitData(form_data, url) {  
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');   
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: form_data.serialize(),
        async: true,
        enctype: 'multipart/form-data',
        success: function(data) {
            submited_field_sets--;

            var new_width = 190 - (190 * (submited_field_sets / submited_field_sets_orig));
            var new_percent = 100 - parseInt((submited_field_sets / submited_field_sets_orig) * 100);
            jQuery('#solarsoft_loading_bar').css({
                'width': new_width + 'px'
                });
            jQuery('#solarsoft_loading_text').html(new_percent + '%');
                

            if (typeof data === 'number' && parseFloat(data) == parseInt(data, 10) && !isNaN(data))
                jQuery('#edit_form #entity_id').val(data);
            if (submited_field_sets <= 0 && !submited_error) {
                jQuery('#edit_form').find('select, textarea, input:not(input.img_upload_field, input[name=form_key], #theme_name, #entity_id)').remove();
                jQuery('#edit_form').submit()
            } else if(submited_field_sets <= 0 && submited_error) {
               alert("An Error has occured");
            }
        },
        error: function(data) {
            submited_error = true;
            submited_field_sets--;

            var new_width = 190 - (190 * (submited_field_sets / submited_field_sets_orig));
            var new_percent = 100 - parseInt((submited_field_sets / submited_field_sets_orig) * 100);
            jQuery('#solarsoft_loading_bar').css({
                'width': new_width + 'px'
                });
            jQuery('#solarsoft_loading_text').html(new_percent + '%');
                

            if (typeof data === 'number' && parseFloat(data) == parseInt(data, 10) && !isNaN(data))
                jQuery('#edit_form #entity_id').val(data);
            
            if(submited_field_sets <= 0) {
               alert("An Error has occured");
            }
        }
        
    });
}

function checkRadiusSize(size) {
    if (size == 'default') {
        size == '0px';
    }
    return size;
}

function setHoverContainer(container, hide_fnc) {
    //alert('container set');
    hover_container = container;
    if (hide_fnc)
        hover_container_hide_fnc = hide_fnc;
    else
        hover_container_hide_fnc = function() {
        };
}

var hover_container;
var hover_container_hide_fnc = function() {
};

function closeParent(obj) {
    hover_container_hide_fnc();
    jQuery(obj).parent().hide();
    return false;
}
//jQuery(document).mouseup(function (e)
//{
//    if (hover_container.has(e.target).length === 0 && $('.miniColors').has(e.target).length === 0)
//    {
//        hover_container.hide();
//        hover_container_hide_fnc();
//    }
//});

Ajax.Responders.register({
    onComplete: function() {
        jQuery('#edit_form > div[id]').each(function() {
            if (!jQuery(this).hasClass('loadedtab') && jQuery(this).children().size()>0)
            {
                //get div id
                id = '#'+jQuery(this).attr('id');

                tabid = id.replace('_content', '');
                selector = id + ' input, '+ id +' select, '+ id +' textarea';
                //find all selects,inputs and text areas in div
                jQuery(selector).prop('tabid',tabid);
                jQuery(selector).change(function() {
                    tabid = jQuery(this).prop('tabid');
                    jQuery(tabid).addClass('changed');
                });
                jQuery(this).addClass('loadedtab');
            }
        });
    }
});