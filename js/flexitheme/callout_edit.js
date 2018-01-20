/**
 * Callout edit page Type change field hiding js
 */
function calloutEditTypeChange()
{
    jQuery('#image_file').parents('tr').hide();
    jQuery('#url').parents('tr').hide();
    jQuery('#image_alt').parents('tr').hide();
    jQuery('#product_sku').parents('tr').hide();
    jQuery('#html').parents('tr').hide();
    if (jQuery(this).val() == 'callout') {
        jQuery('#image_file').parents('tr').show();
        jQuery('#url').parents('tr').show();
        jQuery('#image_alt').parents('tr').show();
    }
    
    if (jQuery(this).val() == 'featured_product') {
        jQuery('#product_sku').parents('tr').show();
    }
    
    if (jQuery(this).val() == 'custom_html') {
        jQuery('#html').parents('tr').show();
    }
}

jQuery(document).ready(function() {
    jQuery('#edit_form #type').bind('click', calloutEditTypeChange);
    jQuery('#edit_form #type').click();
});