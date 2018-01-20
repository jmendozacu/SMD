document.observe('dom:loaded', function () {
    // Contacts
    contact_count = $$('#rfq_contacts_table tbody tr.contact_row').length;

    Event.live('#add_contact', 'click', function (el, event) {
        hideButtons();
        addContactRow();
        event.stop();
    });

    Event.live('.contacts_delete', 'click', function (el) {
        if (el.checked && confirmContactDelete(el)) {
            el.up('tr').hide();
            hideButtons();
            deleteElement(el, 'rfq_contacts');
            colorRows('rfq_contacts','');
            checkCount('rfq_contacts', 'contacts_row', 3);
            rfqHasChanged();
        } else {
            el.checked = false;
        }
    });

});


function confirmContactDelete(el) {
    var allowDelete = true;
    if (confirm(Translator.translate('Are you sure you want to delete selected contact?')) === false) {
        allowDelete = false;
    }
    return allowDelete;
}

function addContactRow() {

    $$('#rfq_contacts_table tbody tr:not(.contacts_row)').each(function (e) {
        e.remove();
    });

    var row = $('contacts_row_template').clone(true);

    if (row.down('.contacts_details').options.length > 0) {

        row.addClassName('new');
        row.setAttribute('id', 'contacts_' + contact_count);
        row = resetInputs(row);

        row.down('.contacts_delete').writeAttribute('name', 'contacts[new][' + contact_count + '][delete]');
        row.down('.contacts_details').writeAttribute('name', 'contacts[new][' + contact_count + '][details]');

        $('rfq_contacts').down('tbody').insert({bottom: row});
        colorRows('rfq_contacts');
        contact_count += 1;
        rfqHasChanged();
    } else {
        alert(Translator.translate('No Contacts Available'));
    }
}
