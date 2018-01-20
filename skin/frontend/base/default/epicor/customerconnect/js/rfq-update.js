

document.observe('dom:loaded', function () {

    window.parent.$('loading-mask').hide();

    window.parent.$('rfq_update').update($('rfq_update').innerHTML);
    window.parent.$('messages').update('<ul>' + $$('.messages').shift().innerHTML + '</ul>');
    
    window.parent.resetDeliveryAddress();
    if (typeof window.parent.resetSalesRepPricing === "function") {
        window.parent.resetSalesRepPricing();
    }
    
    window.parent.$('rfq-form-iframe').writeAttribute('src','');
});