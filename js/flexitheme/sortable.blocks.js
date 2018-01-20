
            
function updateSortableLists(event, ui) {
    
    var block = ui.item.children("input")[0];
    var block_name = convertName(block.name, jQuery(block).attr('rel'));
    var from = block_name;
    var to = convertName(block.name, jQuery(ui.item.parent()[0]).attr('rel'));
    var new_rel = jQuery(ui.item.parent()[0]).attr('rel');
    if(from != to) {
        block.name = block_name.replace(from, to);
        jQuery(block).attr('rel', new_rel);
    }
    jQuery("#"+this.id+" input").each(function(index) {
        this.name = this.name.replace(/\[[\d]+\]/,"["+index+"]");
    });
}

function convertName(name, rel) {
    var pattern = /([^\[]+)\[([^\]]+)\]\[([^\]]+)\]\[([^\]]+)\]\[([^\]]+)\]/;
    var regex_matches = name.match(pattern);
    return regex_matches[1]+'['+regex_matches[2]+']'+'['+rel+']'+'['+regex_matches[4]+']'+'['+regex_matches[5]+']';
}
