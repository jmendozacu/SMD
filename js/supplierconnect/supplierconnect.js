document.observe('dom:loaded', function() {
    if ($('supplier_connect_order_comments')) {
    }

    // Orders New List Page / confirm changes page
    $$(".po_confirm").invoke('observe', 'click', function() {
        if (this.checked) {
            id = this.readAttribute('id').replace('confirm', 'reject');
            $(id).checked = false;
        }
    });

    $$(".po_reject").invoke('observe', 'click', function() {
        if (this.checked) {
            id = this.readAttribute('id').replace('reject', 'confirm');
            $(id).checked = false;
        }
    });

    if ($('purchase_order_confirmreject_save')) {
        $('purchase_order_confirmreject_save').observe('click', function() {
            $('purchase_order_confirmreject').submit();
        });
    }

    // Orders Details Page

    $$(".purchase_order_changed").invoke('observe', 'click', function() {
        var disabled = true;
        if (this.checked) {
            disabled = false;
        }

        this.parentNode.parentNode.select('input[type=text]', 'textarea').each(function(el) {
            el.disabled = disabled;
        });
    });

    if ($('purchase_order_save')) {
        $('purchase_order_save').observe('click', function() {
            $('purchase_order_update').submit();
        });
    }
});