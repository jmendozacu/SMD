document.observe('dom:loaded', function() {
    // Orders New List Page / confirm changes page
    $$(".rfq_confirm").invoke('observe', 'click', function() {
        var rfqId = this.readAttribute('id').replace('rfq_confirm_', '');
        if (this.checked) {
            id = this.readAttribute('id').replace('confirm', 'reject');
            $(id).checked = false;
            $('rfq_' + rfqId + '_customer_reference_box').show();
        } else {
            $('rfq_' + rfqId + '_customer_reference_box').hide();
            $('rfq_' + rfqId + '_customer_reference').value = '';
        }
    });

    $$(".rfq_reject").invoke('observe', 'click', function() {
        if (this.checked) {
            id = this.readAttribute('id').replace('reject', 'confirm');
            $(id).checked = false;
            var rfqId = this.readAttribute('id').replace('rfq_reject_', '');
            $('rfq_' + rfqId + '_customer_reference_box').hide();
            $('rfq_' + rfqId + '_customer_reference').value = '';
        }
    });
    
    if ($('rfq_confirmreject_save')) {
        $('rfq_confirmreject_save').observe('click', function() {
            $('rfq_confirmreject').submit();
        });
    }
});