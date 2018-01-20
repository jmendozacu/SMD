document.observe("dom:loaded", function() {

    $$('select#layout')[0].observe('change', function($elem) {

        //change preview image
        var reg=new RegExp("layout_([0-9]*)", "g");
        $('layout_img').writeAttribute('src', $('layout_img').readAttribute('src').replace(reg, 'layout_'+this.value));
        
        //show/hide .phtml info div
        if(this.value == 0)
        {
            $('layout_phtml').show();
        }
        else
        {
            $('layout_phtml').hide();
        }
        
        //assign new layout
        changeLayout(false);
    });

    //load
    changeLayout(true);
    initExistingLayoutItems();
    
    //save dnd blocks
    dump_dnd_blocks = jQuery('#contenttype-dnd-blocks').html();
    
    //init Drag and drop
    initDragAndDropLayout();
});

var dump_dnd_blocks;

function changeLayout(init)
{
    var $newLayout = jQuery('#layout_'+jQuery('select#layout').val()).clone(true);
    var $currentLayout = jQuery('#layout-configure');
    
    $$('#layout-configure > .column-dropable').each(function($existingColumn) {

        $($existingColumn).getElementsBySelector('> div').each(function($item) {
            
            //save value before moving in the DOM
            $($item).getElementsBySelector('input[type=text]').each(function(input) {
                jQuery(input).attr('value', jQuery(input).val());
            });
            $($item).getElementsBySelector('select').each(function(select) {
                select.getElementsBySelector('option[value='+select.value+']')[0].writeAttribute('selected', 'selected');
            });
            $($item).getElementsBySelector('input[type=radio]').each(function(radio) {
                if(jQuery(radio).prop('checked') == true) jQuery(radio).attr('checked', 'checked');
                else jQuery(radio).removeAttr('checked');
            });            

            //move in new layout
            if(jQuery('#'+$($existingColumn).readAttribute('id'), $newLayout).length > 0)
            {
                jQuery($item).appendTo(jQuery('#'+$($existingColumn).readAttribute('id'), $newLayout));
            }
            else
            {
                jQuery($item).appendTo(jQuery('#col1', $newLayout));
            }
        });
    });
    
    jQuery('#layout-configure').html($newLayout.html());
    
    
    if(jQuery('select#layout').val() == 0)
    {
        jQuery('#contenttype_layout_items').hide().prev().hide();
        jQuery('#contenttype_layout_grid').hide().prev().hide();
    }
    else
    {
        jQuery('#contenttype_layout_items').show().prev().show();
        jQuery('#contenttype_layout_grid').show().prev().show();        
    }
    
    //init Drag and drop
    initDragAndDropLayout();
}

var current_layout_field_id = 1;
var current_layout_block_id = 1;
var current_layout_group_id = 1;

function initDragAndDropLayout()
{
    //init dnd for items
    jQuery('.column-dropable').sortable({
        items: '> .contenttype-dnd-block',
        connectWith: '.column-dropable',
        handle: '> label',
        scroll: true,
        start: function(event, ui) {
            ui.helper.parent().addClass('currently-dragging');
        },
        stop: function(event, ui) {
            
            jQuery('#contenttype-dnd-blocks').html(dump_dnd_blocks);
            
            if(ui.item.html().indexOf('{{id}}') != -1)
            {
                if(ui.item.hasClass('field'))
                {
                        ui.item.html(ui.item.html().replace(new RegExp('{{id}}', 'g'), current_layout_field_id));
                        current_layout_field_id++;
                }
                else if(ui.item.hasClass('cms_block'))
                {
                    ui.item.html(ui.item.html().replace(new RegExp('{{id}}', 'g'), current_layout_block_id));
                    current_layout_block_id++;
                }
                else if(ui.item.hasClass('group'))
                {
                    ui.item.html(ui.item.html().replace(new RegExp('{{id}}', 'g'), current_layout_group_id));
                    current_layout_group_id++;
                }
            }
            
            //init Drag and drop
            initDragAndDropLayout();
        }
    });
    
    jQuery('#layout-configure .contenttype-dnd-block > label').unbind('dblclick').dblclick(function() {
        collapseLayoutItem(jQuery(this).parent());
    });
}

function changeLayoutFieldType($select)
{
    if(jQuery('.format-'+jQuery('option[value='+jQuery($select).val()+']', $select).attr('type'), jQuery($select).parent().parent().parent()).length > 0)
    {
        jQuery('.contenttype-format', jQuery($select).parent().parent().parent()).hide();
        jQuery('.format-'+jQuery('option[value='+jQuery($select).val()+']', $select).attr('type'), jQuery($select).parent().parent().parent()).show();
        jQuery($select).parents('tbody').children('tr:last-child').show();
    }
    else
    {
        jQuery('.contenttype-format', jQuery($select).parent().parent().parent()).hide();
        jQuery(jQuery($select)).parent().parent().parent().children('tr:last-child').hide();
    }
}

function collapseLayoutItem($item)
{
    jQuery('> span.collapse', $item).toggleClass('collapsed');
    jQuery('> .layout-form', $item).toggle('normal');
}

