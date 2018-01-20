document.observe("dom:loaded", function() {
    
    //init Drag and drop
    initDragAndDropMenu();
    
    //delete all contenttype-layout (fieldsets and field) when click on save button
    if($$('.adminhtml-contenttype-menu-edit').length > 0)
    {
        varienGlobalEvents.attachEventHandler("formSubmit", onSaveCctMenuForm);
    }    
    
    jQuery('select.menu_type').change();
});

var increment_menu = 0;
function addCtMenuNode(node_id, menu_id, parent_id, type, entity_id, label, format, status, status_label, position, level, target, classes, firstchild, url, url_path, canonical)
{
    var menu_layout = jQuery('#menu-layout').html();
    menu_layout = replaceAll('{{node_id}}', node_id, menu_layout);
    menu_layout = replaceAll('{{menu_id}}', menu_id, menu_layout);
    menu_layout = replaceAll('{{parent_id}}', parent_id, menu_layout);
    menu_layout = replaceAll('{{entity_id}}', entity_id, menu_layout);
    menu_layout = replaceAll('{{label}}', label, menu_layout);
    menu_layout = replaceAll('{{format}}', format, menu_layout);
    menu_layout = replaceAll('{{status_label}}', status_label, menu_layout);
    menu_layout = replaceAll('{{position}}', position, menu_layout);
    menu_layout = replaceAll('{{level}}', level, menu_layout);
    menu_layout = replaceAll('{{classes}}', classes, menu_layout);
    menu_layout = replaceAll('{{url}}', url, menu_layout);
    menu_layout = replaceAll('{{url_path}}', url_path, menu_layout);
    menu_layout = replaceAll('{{increment}}', increment_menu, menu_layout);
    
    increment_menu++;
    
    //status
    if(status == 0)
    {
        menu_layout = replaceAll('{{status_0}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{status_1}}', 'rel', menu_layout);
    }
    else if(status == 1)
    {
        menu_layout = replaceAll('{{status_0}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{status_1}}', 'selected', menu_layout);
    }
    
    //canonical
    if(canonical == 0)
    {
        menu_layout = replaceAll('{{canonical_0}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{canonical_1}}', 'rel', menu_layout);
    }
    else if(canonical == 1)
    {
        menu_layout = replaceAll('{{canonical_0}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{canonical_1}}', 'selected', menu_layout);
    }
    
    //target
    if(target == '_self')
    {
        menu_layout = replaceAll('{{target_self}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{target_blank}}', 'rel', menu_layout);
    }
    else if(target == '_blank')
    {
        menu_layout = replaceAll('{{target_self}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{target_blank}}', 'selected', menu_layout);
    }
    
    //type
    if(type == 'content')
    {
        menu_layout = replaceAll('{{type_content}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'page')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'category')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'product')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'node')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'block')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'selected', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'rel', menu_layout);
    }
    else if(type == 'custom')
    {
        menu_layout = replaceAll('{{type_content}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_page}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_category}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_product}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_node}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_block}}', 'rel', menu_layout);
        menu_layout = replaceAll('{{type_custom}}', 'selected', menu_layout);
    }
    
    //firstchild
    if(firstchild == 1)
    {
        menu_layout = replaceAll('{{firstchild}}', 'checked', menu_layout);
    }
    
    //append node
    if(parent_id == '')
    {
        //append to root
        jQuery('#menu-container').append(menu_layout);
    }
    else
    {
        //append to parent
        jQuery('#menu-'+parent_id+' > .menu-properties > .children').append(menu_layout);
    }
    
    //bind double click
    jQuery('#menu-container .menu-node > label').unbind('dblclick').dblclick(function() {
        collapseMenuItem(jQuery(this).parent());
    });
    
}

function changeMenuType(elem_type)
{
    var $table = jQuery(elem_type).parent().parent().parent();
    jQuery('.format_custom', $table).hide();
    jQuery('.format_node', $table).hide();
    jQuery('.format_product', $table).hide();
    jQuery('.format_content', $table).hide();
    jQuery('.format_category', $table).hide();
    jQuery('.format_page', $table).hide();
    jQuery('.format_block', $table).hide();
    
    if(jQuery(elem_type).val() == 'custom')
    {
        jQuery('.format_custom', $table).show();
    }
    else if(jQuery(elem_type).val() == 'node')
    {
        jQuery('.format_node', $table).show();
    }
    else if(jQuery(elem_type).val() == 'product')
    {
        jQuery('.format_product', $table).show();
    }
    else if(jQuery(elem_type).val() == 'category')
    {
        jQuery('.format_category', $table).show();
    }
    else if(jQuery(elem_type).val() == 'content')
    {
        jQuery('.format_content', $table).show();
    }
    else if(jQuery(elem_type).val() == 'page')
    {
        jQuery('.format_page', $table).show();
    }
    else if(jQuery(elem_type).val() == 'block')
    {
        jQuery('.format_block', $table).show();
    }
}

function replaceAll(find, replace, str) {
    return str.replace(new RegExp(find, 'g'), replace);
}

function collapseMenuItem($item)
{
    jQuery('> span.collapse', $item).toggleClass('collapsed');
    jQuery('> .menu-properties', $item).toggle('normal');
}

function addCtVirginMenuNode(menu_id, elem)
{
    var menu_layout = jQuery('#menu-layout').html();
    menu_layout = replaceAll('{{node_id}}', '', menu_layout);
    menu_layout = replaceAll('{{menu_id}}', menu_id, menu_layout);
    menu_layout = replaceAll('{{parent_id}}', '', menu_layout);
    menu_layout = replaceAll('{{type}}', '', menu_layout);
    menu_layout = replaceAll('{{entity_id}}', '', menu_layout);
    menu_layout = replaceAll('{{label}}', '', menu_layout);
    menu_layout = replaceAll('{{format}}', '', menu_layout);
    menu_layout = replaceAll('{{status}}', '', menu_layout);
    menu_layout = replaceAll('{{status_label}}', '', menu_layout);
    menu_layout = replaceAll('{{position}}', '', menu_layout);
    menu_layout = replaceAll('{{level}}', '', menu_layout);
    menu_layout = replaceAll('{{classes}}', '', menu_layout);
    menu_layout = replaceAll('{{url}}', '', menu_layout);
    menu_layout = replaceAll('{{url_path}}', '', menu_layout);
    menu_layout = replaceAll('{{increment}}', increment_menu, menu_layout);
    
    increment_menu++;
    
    //append to elem
    var new_elem = jQuery(menu_layout);
    jQuery(elem).append(new_elem);
    
    collapseMenuItem(new_elem);
    
    initDragAndDropMenu();
    
    //bind double click
    jQuery('#menu-container .menu-node > label').unbind('dblclick').dblclick(function() {
        collapseMenuItem(jQuery(this).parent());
    });
}

function initDragAndDropMenu()
{
    //init dnd for items
    jQuery('.column-dropable').sortable({
        items: '> .menu-node',
        connectWith: '.column-dropable',
        handle: '> label',
        scroll: true
    });
}


/**
 * onSubmit CT Menu form
 */

function onSaveCctMenuForm()
{
    //Remove all layout from DOM
    $('menu-layout').remove();
    
    //Set node order and parent
    var parentsLevel = new Array();
    console.log(jQuery('#menu-container > .menu-node').length );
    parseNodes(jQuery('#menu-container > .menu-node'), parentsLevel);
    
    //remove unecessary tr.format-* rows
    $$('#menu-container tr').each(function($tr) {
        if($tr.style.display == 'none')
        {
            $($tr).remove();
        }
    });
    
    //close chooser containers
    $$('.chooser-container').each(function(container) {
        container.remove();
    });
}

function parseNodes(nodes, parentsLevel)
{
    var index = 0;
    jQuery(nodes).each(function() {
        //construct new name prefix
        var node = jQuery(this);
        var new_prefix = 'nodes[';
        $A(parentsLevel).each(function(elem) {
            new_prefix += elem+"][children][";
        });
        new_prefix += index+"]";
        
        //apply to all inputs
        jQuery('> .menu-properties > .prefix_to_change, > .menu-properties > table .prefix_to_change', node).each(function() {
            var input = jQuery(this);
            input.attr('name', input.attr('name').replace('{{prefix}}', new_prefix));
        });
        
        //apply position
        var menu_position = jQuery('> .menu-properties > .menu-position', node);
        menu_position.val(index);
        
        //recursive for childs
        if(jQuery('> .menu-properties > .children > .menu-node', node).length > 0)
        {
            var newParentsLevel = new Array();
            $A(parentsLevel).each(function(elem) {
                newParentsLevel[newParentsLevel.length] = elem;
            });
            newParentsLevel[newParentsLevel.length] = index;
            parseNodes(jQuery('> .menu-properties > .children > .menu-node', node), newParentsLevel);
        }
        
        index++;
    });
    
}

function removeMenuItem(node_menu)
{
    jQuery('.delete_menu_item', node_menu).val(1);
    node_menu.hide();
}

