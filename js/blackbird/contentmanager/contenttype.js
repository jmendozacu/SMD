document.observe("dom:loaded", function() {
    
    //delete all contenttype-layout (fieldsets and field) when click on save button
    if($$('.adminhtml-contenttype-edit, .adminhtml-contenttype-listing-edit').length > 0)
    {
        varienGlobalEvents.attachEventHandler("formSubmit", onSaveCctForm);
    }
    
    //delete all chooser when saving
    if($$('.adminhtml-contenttype-content-edit').length > 0)
    {
        varienGlobalEvents.attachEventHandler("formSubmit", onSaveContentForm);
    }
    
    varienGlobalEvents.attachEventHandler("showTab", onExportContentType);
    
});

function onSaveContentForm()
{
    $$('.chooser-container').each(function(container) {
        container.innerHTML = '';
    });
}

var itemCount = 1;

/**
 * onSubmit CT form
 */
function onSaveCctForm()
{
    //Remove all layout from DOM
    $$('.contenttype-layout').each(function(layout) {
        layout.remove();
    });
    
    //Set fieldset order
    var i = 1;
    $$('.contenttype-fieldset').each(function(fieldset) {
        Element.down(fieldset, '.fieldset-order').value = i;
        i++;
        
        var j = 1;
        $$('#'+fieldset.readAttribute('id')+' .fields-container .option-box').each(function(field) {
            Element.down(field, '.sort_order').value = j;
            Element.down(field, '.fieldset_id').value = Element.down(fieldset, '.fieldset-id').value;
            Element.down(field, '.fieldset_random').value = Element.down(fieldset, '.fieldset-random').value;
            j++;
        });
    });
    
    //set layout fields order
    $$('#layout-configure > .column-dropable').each(function(column) {
        var position = 1;
        var column_id = column.readAttribute('id').replace('col', '');
        
        $$('#layout-configure #col'+column_id+' .contenttype-dnd-block').each(function(item) {
            Element.down(item, '.input_column').value = column_id;
            Element.down(item, '.input_sort_order').value = position;
            
            if(item.up().readAttribute('rel') != undefined)
            {
                Element.down(item, '.input_layout_group_id').value = item.up().readAttribute('rel');
            }
        
            position++;
        });
    });
    
    jQuery('#contenttype-dnd-blocks').remove();
}

/**
 * Add fieldset when creating content type
 */
function contenttypeAddFieldset(title, fieldset_id)
{
    var $layout = $('layout-fieldset').innerHTML;
    $layout = replaceAll('{{title}}', title, $layout);
    $layout = replaceAll('{{id}}', fieldset_id, $layout);
    $layout = replaceAll('{{random}}', parseInt(Math.random()*100000), $layout);
    
    Element.insert($$('.contenttype-custom-options')[0], {'bottom': $layout});
    
    contenttypeInitDragAndDrop();
}

/**
 * Add field to fieldset with JSON
 */
function contenttypeAddFieldWithJson(data)
{
    var random = $$('.contenttype-fieldset-'+data.fieldset_id)[0].readAttribute('id').replace('fieldset_'+data.fieldset_id+'_', '');
    contenttypeAddField(random, data);
}

/**
 * Add field to fieldset
 */
function contenttypeAddField(fieldset_random, data, fieldset_id)
{
    //replace values in template
    var layout = $('layout-field').innerHTML;
    var templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
    var template = new Template(layout, templateSyntax);
    var new_field = false;
    
    if(!data.id){
        data = {};
        data.id  = itemCount;
        data.type = '';
        data.option_id = 0;
        data.fieldset_id = fieldset_id;
        new_field = true;
    } 
    else {
        if(data.item_count > itemCount)
        {
            itemCount = data.item_count;            
        }
    }   
    data.fieldset_random = fieldset_random;
    
    //add field to the DOM
    var fieldset = Element.down($('fieldset_'+data.fieldset_id+'_'+fieldset_random), '.fields-container');
    Element.insert(fieldset, {'bottom': template.evaluate(data)});
    
    //bind event on "input type"
    var types = $$('.select-contenttype-option-type');
    for(var i=0;i<types.length;i++){
        if(!$(types[i]).binded){
            $(types[i]).binded = true;
            Event.observe(types[i], 'change', function(event){
                contenttypeOptionTypeChange(event);
            });
        }
    }
    
    if(!new_field)
    {
        //set values for is require select
        $A($('contenttype_option_'+data.option_id+'_is_require').options).each(function(option){
            if (option.value==data.is_require) option.selected = true;
        });

        //set values for show in grid select
        $A($('contenttype_option_'+data.option_id+'_show_in_grid').options).each(function(option){
            if (option.value==data.show_in_grid) option.selected = true;
        });

        //set values for is searchable
        $A($('contenttype_option_'+data.option_id+'_is_searchable').options).each(function(option){
            if (option.value==data.is_searchable) option.selected = true;
        });

        //set values for search weight
        $A($('contenttype_option_'+data.option_id+'_search_attribute_weight').options).each(function(option){
            if (option.value==data.search_attribute_weight) option.selected = true;
        });

        //set values for type
        if(data.type)
        {
            $A($('contenttype_option_'+data.option_id+'_type').options).each(function(option){;
                if (option.value==data.type) option.selected = true;
            });

            // Disable already selected types
            var our_element = $('contenttype_option_'+data.id+'_type');
            var tmp_name =  our_element.readAttribute("name");

            our_element.writeAttribute("disabled", true);
            our_element.writeAttribute("name", "tmp_"+tmp_name);

            Element.insert(our_element, {'after': '<input type="hidden" value="'+data.type+'" name="'+tmp_name+'" /> '}); 
        }
    }
    
    //disable identifier
    if(data.identifier)
    {
        // Disable already selected types
        var our_element = $('contenttype_option_'+data.id+'_identifier');
        var tmp_name =  our_element.readAttribute("name");

        our_element.writeAttribute("disabled", true);
        our_element.writeAttribute("name", "tmp_"+tmp_name);

        Element.insert(our_element, {'after': '<input type="hidden" value="'+data.identifier+'" name="'+tmp_name+'" /> '}); 
    }
    
    //Tab event
    var tabs = new varienTabs('contenttype_field_tabs_'+data.id, 'contenttype_tab_content_'+data.id, 'contenttype_option_'+data.id+'_fields_tabs_general');    
    tabs.moveTabContentInDest();
    
    itemCount++;
}

/**
 * Fired when user change input type
 */
function contenttypeOptionTypeChange(event)
{
    var element = $(Event.findElement(event, 'select'));
    contenttypeOptionTypeChangeElement(element);
}

/**
 * Fired when user change input type
 */
function contenttypeOptionTypeChangeElement(element)
{
    var group = '';
    var previousGroupElm = $(element.readAttribute('id').sub('_type', '_previous_group'));
    
    switch(element.getValue()){
        case 'field':
        case 'area':
        case 'password':
        case 'int':
            template = OptionTemplateText;
            group = 'text';
            break;
        case 'file':
        case 'image':
            template = OptionTemplateFile;
            group = 'file';
            break;
        case 'drop_down':
        case 'radio':
        case 'checkbox':
        case 'multiple':
            template = OptionTemplateSelect;
            group = 'select';
            break;
        case 'date':
        case 'date_time':
        case 'time':
            template = OptionTemplateDate;
            group = 'date';
            break;
        case 'product':
        case 'category':
        case 'content':
        case 'attribute':
        	template = OptionTemplateRelation;
        	group = 'relation';
        	break;
        default:
            template = '';
            group = 'unknown';
            break;
    }
            
    //default value for extra fields
    var file_extensions = '';
    switch(element.getValue()){
        case 'image':
            file_extensions = 'png,jpg,gif';
            break;
        case 'file':
            file_extensions = 'doc,docx,pdf,odt,xls,xlsx,csv';
            break;
    }

    if (previousGroupElm.getValue() != group) {
        
        if ($(element.readAttribute('id')+'_'+previousGroupElm.getValue())) {
            formElm = $(element.readAttribute('id')+'_'+previousGroupElm.getValue()).descendants();
            formElm.each(function(elm){
                if (elm.tagName == 'input' || elm.tagName == 'select') {
                    elm.name = '__delete__'+elm.readAttribute('name');
                }
            });

            $(element.readAttribute('id')+'_'+previousGroupElm.getValue()).addClassName('no-display');
            $(element.readAttribute('id')+'_'+previousGroupElm.getValue()).addClassName('ignore-validate');
            $(element.readAttribute('id')+'_'+previousGroupElm.getValue()).hide();
        }

        previousGroupElm.value = group;

        if ($(element.readAttribute('id')+'_'+group)) {
            formElm = $(element.readAttribute('id')+'_'+group).descendants();
            formElm.each(function(elm){
                if (elm.match('input') || elm.match('select')) {
                    elm.name = elm.readAttribute('name').sub('__delete__', '');
                }
            });
            $(element.readAttribute('id')+'_'+group).removeClassName('no-display');
            $(element.readAttribute('id')+'_'+group).removeClassName('ignore-validate');
            $(element.readAttribute('id')+'_'+group).show();

        } else {
            var templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
            var template = '<div id="'+element.readAttribute('id')+'_'+group+'" class="grid tier form-list">'+template+'</div><div id="'+element.readAttribute('id')+'_'+group+'_advice"></div';
            var secondTemplate = new Template(template, templateSyntax);
            
            data = {};
            if (!data.id) {
                data = {};
                data.id = $(element.readAttribute('id').sub('_type', '_id')).getValue();
            }
            if (!data.option_id) {
                data = {};
                data.option_id = $(element.readAttribute('id').sub('_type', '_id')).getValue();
            }

            Element.insert(element.readAttribute('id').sub('_type', '_fields_tabs_extra_content'), {'bottom':secondTemplate.evaluate(data)});

            switch(element.getValue()){
                case 'drop_down':
                case 'radio':
                case 'checkbox':
                case 'multiple':
                    selectOptionType.bindAddButton();
                    break;
            }
            
        }
    }
    doSpecialRenderByType(element.getValue(), element.readAttribute('id').sub('_type', '').replace('contenttype_option_', ''));
    
    //apply default values
    if(file_extensions != '')
    {
        var id_option = $(element.readAttribute('id').sub('_type', '_id')).getValue();
        $$('#option_'+id_option+' .file_extensions')[0].writeAttribute('value', file_extensions);
    }
}

/**
 * 
 * Called by all "select" types to fill option / values
 */
function contenttypeAddDataToValues(data){

    switch(data.type){
        case 'field':
        case 'area':
        case 'password':
        case 'int':
            template = OptionTemplateText;
            group = 'text';
            break;
        case 'file':
        case 'image':
            template = OptionTemplateFile;
            group = 'file';
            break;
        case 'drop_down':
        case 'radio':
        case 'checkbox':
        case 'multiple':
            template = OptionTemplateSelect;
            group = 'select';
            break;
        case 'product':
        case 'category':
        case 'content':
        case 'attribute':
        	template = OptionTemplateRelation;
        	group = 'relation';
        	break;
        case 'date':
        case 'date_time':
        case 'time':
            template = OptionTemplateDate;
            group = 'date';
            break;
    }

    $('contenttype_option_'+data.id+'_previous_group').value = group;
    
    var templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
    var template = '<div id="contenttype_option_{{id}}_type_'+group+'" class="grid tier form-list">'+template+'</div><div id="contenttype_option_{{id}}_type_'+group+'_advice"></div>';

    var secondTemplate = new Template(template, templateSyntax);

    Element.insert($('contenttype_option_'+data.id+'_fields_tabs_extra_content'), {'bottom':secondTemplate.evaluate(data)});
    
    doSpecialRenderByType(data.type, data.id);

    //set options for all select types
    switch(data.type){
        case 'drop_down':
        case 'radio':
        case 'checkbox':
        case 'multiple':
            data.optionValues.each(function(value) {
                selectOptionType.add(value);
            });
            selectOptionType.bindAddButton();
            break;
    }
    
    //set wysiwyg editor check
    if(data.wysiwyg_editor == 1)
    {
        $('wysiwyg_editor_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set crop check
    if(data.crop == 1)
    {
        $('crop_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set keep aspect ratio check
    if(data.keep_aspect_ratio == 1)
    {
        $('keep_aspect_ratio_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set img alt check
    if(data.img_alt == 1)
    {
        $('img_alt_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set img alt check
    if(data.img_title == 1)
    {
        $('img_title_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set img url check
    if(data.img_url == 1)
    {
        $('img_url_'+data.id).writeAttribute("checked", "checked");
    }
    
    //set content type pre selected
    if(data.content_type != null)
    {
        jQuery('#content_type_'+data.id).val(data.content_type);
    }

    //set attribute pre selected
    if(data.content_type != null)
    {
        jQuery('#attribute_product_'+data.id).val(data.attribute);
        jQuery('#attribute_limit_'+data.id).val(data.max_characters);
    }


}

/**
 * Delete field
 */
function contenttypeDeleteField(field_id)
{
    var element = $$('.contenttype-fieldset #option_'+field_id)[0];
    
    $('contenttype_option_'+field_id+'_'+'is_delete').value = '1';
    element.addClassName('no-display');
    element.addClassName('ignore-validate');
    element.hide();
}


function replaceAll(find, replace, str) {
  return str.replace(new RegExp(find, 'g'), replace);
}

/**
 * Show / hide special fields by sub-types
 */
function doSpecialRenderByType(type, id)
{
    //element "area"
    if(type == 'area')
    {
        $$('#option_'+id+' .textareaonly').each(function(elem) {
            elem.show();
        });
        
        $$('#option_'+id+' .no-password').each(function(elem) {
            elem.show();
        });
        
        $$('#option_'+id+' .no-textarea').each(function(elem) {
            elem.hide();
        });
    }
    else if(type == 'field')
    {
        $$('#option_'+id+' .no-password').each(function(elem) {
            elem.show();
        });
        
        $$('#option_'+id+' .textareaonly').each(function(elem) {
            elem.hide();
        });

        $$('#option_'+id+' .no-textarea').each(function(elem) {
            elem.show();
        });
    }
    else if(type == 'int')
    {
        $$('#option_'+id+' .no-password').each(function(elem) {
            elem.show();
        });
        
        $$('#option_'+id+' .textareaonly').each(function(elem) {
            elem.hide();
        });

        $$('#option_'+id+' .no-textarea').each(function(elem) {
            elem.show();
        });
    }
    else if(type == 'password')
    {
        $$('#option_'+id+' .textareaonly').each(function(elem) {
            elem.hide();
        });
        
        $$('#option_'+id+' .no-textarea').each(function(elem) {
            elem.show();
        });
        
        $$('#option_'+id+' .no-password').each(function(elem) {
            elem.hide();
        });
    }
    
    //element "image"
    if(type == 'image')
    {
        $$('#option_'+id+' .imageonly').each(function(elem) {
            elem.show();
        });
    }
    else
    {
        $$('#option_'+id+' .imageonly').each(function(elem) {
            elem.hide();
        });
    }
    
    //element content type
    if(type == 'content')
    {
        $$('#option_'+id+' .contentonly').each(function(elem) {
            elem.show();
        });
        $$('#option_'+id+' .attributeonly').each(function(elem) {
            elem.hide();
        });
    }
    else if(type == 'attribute')
    {
        $$('#option_'+id+' .contentonly').each(function(elem) {
            elem.hide();
        });
        $$('#option_'+id+' .attributeonly').each(function(elem) {
            elem.show();
        });
    }
    else
    {
        $$('#option_'+id+' .contentonly').each(function(elem) {
            elem.hide();
        });
        $$('#option_'+id+' .attributeonly').each(function(elem) {
            elem.hide();
        });
    }
}

/**
 * Function to initialize drag and drop for fieldset and fields
 */
function contenttypeInitDragAndDrop()
{
    //init dnd for fieldset
    jQuery('.contenttype-custom-options').sortable({
        items: '> .contenttype-fieldset',
        handle: '.contenttype-handle',
        containment: "parent"
    });
    
    //init dnd for fields
    jQuery('.contenttype-fieldset .fields-container').sortable({
        items: '> .option-box',
        connectWith: '.contenttype-fieldset .fields-container',
        handle: '.contenttype-handle-field',
        start: function() {
            jQuery('.contenttype-fieldset .fields-container').addClass('fields-container-dash');
        },
        stop: function() {
            jQuery('.contenttype-fieldset .fields-container').removeClass('fields-container-dash');
        }
    });
}

/**
 * Delete the while fieldset and his fields
 */

function contenttypeDeleteFieldset(fieldset_id)
{
    $('fieldset_'+fieldset_id).getElementsBySelector('.option-box').each(function($field) {
        contenttypeDeleteField($field.readAttribute('id').replace('option_', ''));
    });
    
    $('fieldset_'+fieldset_id).getElementsBySelector('.fieldset-delete').each(function($field) {
        $field.value = 1;
    });
    
    var element = $('fieldset_'+fieldset_id);
    element.addClassName('no-display');
    element.addClassName('ignore-validate');
    element.hide();
}

function checkCtTitle(elem)
{
    var value = jQuery(elem).val();
    value = value.toLowerCase();
    value = value.replace(/[^a-z0-9]/g,'_');
    value = value.replace('___', '_');
    value = value.replace('__', '_');

    if(jQuery('.ct-identifier', jQuery(elem).parent().parent().parent()).attr('disabled') != 'disabled')
    {
        jQuery('.ct-identifier', jQuery(elem).parent().parent().parent()).val(value);
    }
}

function checkCtIdentifier(elem)
{
    var value = jQuery(elem).val();
    value = value.toLowerCase();
    value = value.replace(/[^a-z0-9]/g,'_');
    value = value.replace('___', '_');
    value = value.replace('__', '_');

    if(value != jQuery(elem).val()) 
    {
        jQuery(elem).val(value);
    }
}


/*
 * Export
 */
var export_in_progress = false;
var export_url;
function exportContent(url)
{
    //load tab for fields
    contenttype_tabsJsTabs.showTabContent($('contenttype_tabs_custom_options'));
    
    //change variables
    export_url = url;
    export_in_progress = true;
}
function onExportContentType(tab)
{
    if(tab && export_in_progress)
    {
        if($(tab.tab).readAttribute('id') == "contenttype_tabs_custom_options")
        {
            //change edit form action URL
            export_in_progress = false;
            window.setTimeout('onExportContentTypeWaitRendering()', 500);
        }
    }       
}

function onExportContentTypeWaitRendering()
{
    onSaveCctForm();
    jQuery('#edit_form').attr('action', export_url).submit();    
}



