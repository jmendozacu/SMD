document.observe('dom:loaded', function(element) {

    Event.live('.expand-row', 'mouseover', function(element) {
        element.style.cursor = "pointer";
    });
    
    Event.live('.expand-row', 'click', function(element) {
        id = element.down(".plus-minus").readAttribute('id');
        if ($('row-' + id)) {
            $('row-' + id).toggle();
            element.down(".plus-minus").innerHTML == '-' ? element.down(".plus-minus").innerHTML = '+ Click to Expand2' : element.down(".plus-minus").innerHTML = '-';
        }
    });
    
    if($('customerconnect_rph')) {
        $$('#customerconnect_rph th:has(a[name="last_ordered_date"])').each(function(e) {
            e.writeAttribute('style', 'width: 160px');
        });
        
        $$('#customerconnect_rph div.range-line:has(input[name="last_ordered_date[from]"])').each(function(e) {
            e.writeAttribute('style', 'width: 160px');
        });     
        $$('#customerconnect_rph div.range-line:has(input[name="last_ordered_date[to]"])').each(function(e) {
            e.writeAttribute('style', 'width: 160px');
        });  
        
        $$('input[name="last_ordered_date[from]"]').each(function(e) {
            e.writeAttribute('style', 'width: 100px !important');
        });  
        $$('input[name="last_ordered_date[to]"]').each(function(e) {
            e.writeAttribute('style', 'width: 100px !important');
        });  
    }
});

