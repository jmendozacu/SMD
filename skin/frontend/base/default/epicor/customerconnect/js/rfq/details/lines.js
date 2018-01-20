document.observe('dom:loaded', function () {

    /***********************************************************
     * Quick Line Add
     */

    line_count = $$('#rfq_lines_table tbody tr.line_row').length;

    Event.live('#add_line', 'click', function (el, event) {
        $('line-add').show();
        $$("[id^=la_row_]").each(function (e) {
            e.show();
        });

        $('line-search').hide();
        event.stop();
    });

    Event.live('.la_sku', 'keydown', function (a, b) {
        if (b.keyCode == 9) {
            a.up('.la_row').down('.la_quantity').focus();
            b.stop();
        }
    });

    Event.live('.la_quantity', 'keydown', function (a, b) {
        if (b.keyCode == 9) {
            addLineAddRow(a);
            b.stop();
        }
    });

    Event.live('.la_quantity', 'keyup', function (el, e) {
//--SF        formatNumber(el, false, true);
        formatNumber(el, false, false);
    });

    Event.live('.la_custompart', 'click', function (el) {
        var rowId = el.readAttribute('id').replace('la_custompart_', '');
        var skubox = $('la_row_' + rowId).down('.la_sku_box');
        var namebox = $('la_row_' + rowId).down('.la_name_box');

        if (el.checked) {
            namebox.down('.input-text').value = skubox.down('.input-text').value;
            skubox.down('.input-text').value = '';
            skubox.hide();
            namebox.show();
        } else {
            skubox.down('.input-text').value = namebox.down('.input-text').value;
            namebox.down('.input-text').value = '';
            namebox.hide();
            skubox.show();
        }
    });

    Event.live('#lineadd-add', 'click', function (el, event) {
        addLineAddRow($$('.la_row .la_quantity').shift());
        event.stop();
    });


    Event.live('#line-add-close', 'click', function (el, event) {
        $$("[id^=la_row_]").each(function (e) {
            e.show();
        });

        resetLineAdd();
        event.stop();
    });

    Event.live('.la_delete', 'click', function (el, event) {
        if (lineadd_count > 1) {
            el.up('.la_row').hide();
            lineadd_count -= 1;
            if (lineadd_count == 1) {
                $$('.la_delete').each(function (e) {
                    e.hide();
                });
            }
        }
        event.stop();
    });

    Event.live('#lineadd-submit', 'click', function (el, event) {
        processLinesAdd(event);
    });

    /***********************************************************
     * Line Add By Search
     */

    Event.live('#add_search', 'click', function (el, event) {
        $('loading-mask').show();
        $('linesearch-iframe').writeAttribute('src', '/customerconnect/rfqs/linesearch/')
        $('line-search').show();
        $('line-add').hide();
        event.stop();
    });

    Event.live('#line-search-close', 'click', function (el, event) {
        $('line-search').hide();
        $('linesearch-iframe').writeAttribute('src', '');
        event.stop();
    });

    /***********************************************************
     * Line Selections
     */

    Event.live('#clone_selected', 'click', function (el, event) {

        if ($$('.lines_select:checked').length > 0) {
            if (confirmLinesClone()) {
                cloneLines();
                rfqHasChanged();
            }
        } else {
            alert(Translator.translate('Please select one or more lines'));
        }
        event.stop();
    });

    Event.live('#delete_selected', 'click', function (el, event) {
        if ($$('.lines_select:checked').length > 0) {
            if (confirmLinesDelete()) {
                deleteLines();
                checkCount('rfq_lines', 'lines_row', 3);
                recalcLineTotals();
                rfqHasChanged();
            }
        } else {
            alert(Translator.translate('Please select one or more lines'));
        }
        event.stop();
    });

    /***********************************************************
     * Line Editing
     */

    Event.live('.lines_quantity', 'change', function (el) {
        hideButtons();
        formatNumber(el, false, false);
//--SF        formatNumber(el, false, true);
        parentRow = el.up('tr');
        if (parentRow.down('.lines_type').value == 'S') {
            sendMsqForLine(parentRow);
        } else {
            recalcLineTotals();
        }
    });

    /***********************************************************
     * Product Configuration
     */

    Event.live('#configure_product', 'click', function (el, event) {
        $('loading-mask').show();
        submitConfigurableProduct();
        event.stop();
    });

    Event.live('#cancel_configure_product', 'click', function (el, event) {
        $('window-overlay').hide();
        event.stop();
    });


    /***********************************************************
     * Line Attachments
     */

    Event.live('.line_attachments_delete', 'click', function (el) {

        if (deleteWarning(el)) {
            hideButtons();
            var tableId = el.up('table').readAttribute('id');
            deleteElement(el, tableId);
            checkCount(tableId.replace('_table', ''), 'line_attachment_row', 4);
        }
    });

    Event.live('.rfq_line_attachment_add', 'click', function (el) {
        hideButtons();
        addLineAttachment(el);
    });

});

/***********************************************************
 * Quick Line Add
 */

function addLineAddRow(el) {
    var newRow = el.up('.la_row').clone(true);

    lineadd_count += 1;

    newRow.writeAttribute('id', 'la_row_' + lineadd_count);
    newRow.down('.lineadd-autocomplete').writeAttribute('id', 'lineadd_autocomplete_' + lineadd_count).checked = false
    if (newRow.down('.la_custompart')) {
        newRow.down('.la_custompart').writeAttribute('id', 'la_custompart_' + lineadd_count).checked = false
    }
    newRow.down('.la_sku').writeAttribute('id', 'la_sku_' + lineadd_count).value = '';
    newRow.down('.la_product_id').writeAttribute('id', 'la_product_id_' + lineadd_count).value = '';
    newRow.down('.la_uom').writeAttribute('id', 'la_uom_' + lineadd_count).value = '';
    newRow.down('.la_sku_box').show();
    newRow.down('.la_name_box').hide();
    newRow.down('.la_name').writeAttribute('id', 'la_name_' + lineadd_count).value = '';
    newRow.down('.la_quantity').writeAttribute('id', 'la_quantity_' + lineadd_count).value = '';
    newRow.down('.la_packsize').writeAttribute('id', 'la_pack_' + lineadd_count).hide();
    $('la_rows').insert({bottom: newRow});
    newRow.down('.la_sku').focus();

    tempForm = new Epicor.searchForm('la_row_' + lineadd_count, 'la_sku_' + lineadd_count, '', '', 'la_uom_' + lineadd_count, 'la_pack_' + lineadd_count, 'la_product_id_' + lineadd_count, '', 'la_quantity_' + lineadd_count);
    tempForm.initAutocomplete($('la_submit_url').value, 'lineadd_autocomplete_' + lineadd_count);

    laSearchForm[laSearchForm.length] = tempForm;

    $$('.la_delete').each(function (e) {
        e.show();
    });
}

function processLinesAdd(event) {

    hideButtons();

    var skudata = [];
    var customdata = [];
    var showConfiguratorMessage = false;
    var errors = [];

    $$('.la_row').each(function (el) {
        if (el.visible()) {
            var custom = false;
            var customtick = el.select('.la_custompart').shift();
            if (customtick !== undefined) {
                custom = customtick.checked;
            }
            var sku = el.select('.la_sku').shift().value;
            var productid = el.select('.la_product_id').shift().value;
            var name = el.select('.la_name').shift().value;
            var uom = el.select('.la_uom').shift().value;
            var qty = el.select('.la_quantity').shift().value;

            if (isLineEmpty(el)) {
                return;
            }

            var lineErrors = isLineValid(el);
            if (lineErrors.length != 0) {
                errors = lineErrors.concat(errors);
                return;
            }

            if (custom == false) {
                var sendSku = sku;
                if (uom != '') {
                    sendSku = sku + $('la_separator').value + uom;
                }

                skudata[skudata.length] = {'sku': sku, 'sendSku': sendSku, 'uom': uom, 'qty': qty, 'productid': productid}
            } else {
                customdata[customdata.length] = {'sku': name, 'uom': uom, 'qty': qty}
            }
        }
    });


    if (errors.length != 0) {
        displayLineAddInputErrors(errors)
        event.stop();
    }

    if (skudata.length == 0) {
        resetLineAdd();
        if (customdata.length > 0) {
            for (index = 0; index < customdata.length; index++) {
                addLineRow(true, customdata[index].sku, customdata[index].qty, {});
            }
            recalcLineTotals();
        }
        event.stop();
        return;
    }

    var url = $('la_msq_link').value;

    skuArr = [];
    qtyArr = [];
    idArr = [];

    for (index = 0; index < skudata.length; index++) {
        skuArr[skuArr.length] = skudata[index].sendSku;
        qtyArr[qtyArr.length] = skudata[index].qty;
        idArr[idArr.length] = skudata[index].productid;
    }

    var postData = {'from': 'rfq', 'sku[]': skuArr, 'qty[]': qtyArr, 'id[]': idArr, 'currency_code': $('quote_currency_code').value, 'use_index': 'row_id'}

    $('loading-mask').show();

    performAjax(url, 'post', postData, function (data) {
        var msqData = data.responseText.evalJSON();

        if (msqData['has_errors']) {
            message = Translator.translate('One or more lines had errors:') + '\n\n';
            for (index = 0; index < skudata.length; index++) {
                sku = skudata[index].sendSku;
                qty = skudata[index].qty;
                pData = msqData[index];//msqData[sku];

                pData.sku = getNiceSku(pData, skudata[index].sendSku);
                if (pData.error == 1) {
                    message += Translator.translate('SKU') + ' ' + pData.sku + ' ';
                    if ($$('.la_custompart').length > 0) {
                        message += Translator.translate('Does not exist - Select Custom Part') + '\n';
                    } else {
                        message += Translator.translate('Does not exist') + '\n';
                    }
                }
                if (pData.status_error == 1) {
                    message += Translator.translate('SKU') + ' ' + pData.sku + ' ' + Translator.translate('Not currently available');
                }


                $('loading-mask').hide();
            }
            alert(message);
            return;
        }

        for (index = 0; index < skudata.length; index++) {
            sku = skudata[index].sendSku;
            pData = msqData[index];//msqData[sku];
            pData.sku = getNiceSku(pData, skudata[index].sendSku);

            addLineRow(false, pData.sku, skudata[index].qty, pData);
            if (isProductConfigurable(pData)) {
                showConfiguratorMessage = true;
            }
        }

        if (customdata.length > 0) {
            for (indexc = 0; indexc < customdata.length; indexc++) {
                addLineRow(true, customdata[indexc].sku, customdata[indexc].qty, {});
            }
        }

        resetLineAdd();
        recalcLineTotals();

        message = Translator.translate('Lines added successfully');

        if (showConfiguratorMessage)
        {
            message += '\n\n' + Translator.translate('One or more products require configuration. Please click on each "Configure" link in the lines list');
            //alert(message);
        }



        $('loading-mask').hide();
        rfqHasChanged();
    });

    event.stop();
}

function isLineEmpty(line) {
    var custom = false;
    var customtick = line.select('.la_custompart').shift();
    if (customtick !== undefined) {
        custom = customtick.checked;
    }
    var sku = line.select('.la_sku').shift().value;
    var name = line.select('.la_name').shift().value;
    var qty = line.select('.la_quantity').shift().value;

    var lineEmpty = false;

    var qtyEmpty = (qty == '' || !(!isNaN(parseNumber(qty)) && /^\s*-?\d*(\.\d*)?\s*$/.test(qty)))

    if (custom && name == '' && qtyEmpty) {
        lineEmpty = true;
    } else if (!custom && sku == '' && qtyEmpty) {
        lineEmpty = true;
    }

    return lineEmpty;
}

function isLineValid(line) {
    var custom = false;
    var customtick = line.select('.la_custompart').shift();
    if (customtick !== undefined) {
        custom = customtick.checked;
    }
    var sku = line.select('.la_sku').shift().value;
    var name = line.select('.la_name').shift().value;
    var qty = line.select('.la_quantity').shift().value;

    var lineErrors = [];

    var skuValid = (sku != '');
    var nameValid = (name != '');
    var qtyValid = !(qty == '' || !(!isNaN(parseNumber(qty)) && /^\s*-?\d*(\.\d*)?\s*$/.test(qty)))

    if (!custom && !skuValid) {
        lineErrors['sku'] = 1;
    }

    if (custom && !nameValid) {
        lineErrors['name'] = 1;
    }

    if (!qtyValid) {
        lineErrors['qty'] = 1;
    }

    return lineErrors;
}

function displayLineAddInputErrors(errors) {
    errorMessage = '';

    if (errors['sku'] !== undefined) {
        errorMessage += Translator.translate('You must provide an SKU for all non-custom parts') + '\n';
    }

    if (errors['name'] !== undefined) {
        errorMessage += Translator.translate('You must provide a name for all custom parts') + '\n';
    }

    if (errors['qty'] !== undefined) {
        errorMessage += Translator.translate('All quantities must be valid') + '\n';
    }

    alert(errorMessage);
}

function resetLineAdd() {
    $$('#la_rows .la_row').each(function (e) {
        if (e.readAttribute('id') != 'la_row_1') {
            e.remove();
        } else {
            if (e.down('.la_custompart')) {
                e.down('.la_custompart').checked = false;
            }

            e.down('.la_sku').value = '';
            e.down('.la_uom').value = '';
            e.down('.la_product_id').value = '';
            e.down('.la_sku_box').show();
            e.down('.la_name_box').hide();
            e.down('.la_name').value = '';
            e.down('.la_quantity').value = '';
            e.down('.packsize').update('');
            e.down('.la_packsize').hide();
        }
    });

    $('line-add').hide();
}

/***********************************************************
 * Line Addition
 */

function addLineRow(custom, sku, qty, product, sendMsq) {

    $$('#rfq_lines_table tbody tr:not(.lines_row)').each(function (e) {
        if (typeof e.up('.lines_row') === 'undefined') {
            e.remove();
        }
    });

    var row = $('lines_row_template').clone(true);

    var type = custom ? 'N' : 'S';
    var is_kit = 'N';
    var configure_html = '';
    var requires_configuration = false;
    var show_configuration = false;
    setupLineInputs(row);

    product.is_custom = custom;

    row.down('.description_display').update(product.name);
    row.down('.lines_product_code').writeAttribute('value', sku);

    row.down('.lines_quantity').writeAttribute('value', qty);
    row.down('.lines_type').writeAttribute('value', type);
    row.down('.lines_product_json').writeAttribute('value', JSON.stringify(product));
    row.down('.lines_product_id').writeAttribute('value', product.entity_id);

    if (custom) {
        row.down('.lines_description').addClassName('required-entry');
        if (!valueEmpty(product.custom_description)) {
            row.down('.lines_description').writeAttribute('value', product.custom_description);
            row.down('.lines_price').writeAttribute('value', parseFloat(product.use_price).toFixed(2));
            row.down('.lines_price_display').update(product.formatted_price);
            row.down('.lines_line_value').writeAttribute('value', parseFloat(product.use_price).toFixed(2) * qty);
            row.down('.lines_line_value_display').update(product.formatted_total);
        } else {
            row.down('.lines_price_display').update(Translator.translate('TBA'));
            row.down('.lines_line_value_display').update(Translator.translate('TBA'));
        }
    } else {
        row.down('.uom_display').update(product.uom);
        row.down('.lines_unit_of_measure_code').writeAttribute('value', product.uom);

        row.down('.lines_description').writeAttribute('value', product.name);
        row.down('.lines_description').writeAttribute('type', 'hidden');

        if (product.ewa_attributes && product.ewa_attributes != undefined && product.ewa_attributes != 'value') {
            row.down('.lines_attributes').writeAttribute('value', product.ewa_attributes);
        }

        row.down('.lines_price').writeAttribute('value', parseFloat(product.use_price).toFixed(2));
        row.down('.lines_price_display').update(product.formatted_price);

        row.down('.lines_line_value').writeAttribute('value', parseFloat(product.use_price).toFixed(2) * qty);
        row.down('.lines_line_value_display').update(product.formatted_total);

        row.down('.lines_ewa_code').writeAttribute('value', '');

        if (product.configurator == 1) {
            configureFunction = 'configureEwaProduct';
            show_configuration = true;
            if (valueEmpty(product.ewa_code)) {
                requires_configuration = true;
            } else {
                updateLineEwaInfo(row, product, true);
            }
        } else if (product.type_id == 'configurable' || !valueEmpty(product.has_options) || product.type_id == 'grouped') {
            configureFunction = 'configureProduct';
            show_configuration = true;
            requires_configuration = true;
        }

        if (show_configuration) {
            if (requires_configuration) {
                row.down('.lines_configured').writeAttribute('value', 'TBC');
                row.down('.lines_price').writeAttribute('value', '');
                row.down('.lines_price_display').update(Translator.translate('TBA'));
                row.down('.lines_line_value').writeAttribute('value', '');
                row.down('.lines_line_value_display').update(Translator.translate('TBA'));
                var configure_html = '<br /><a href="javascript:' + configureFunction + '(\'' + line_count + '\')">' + Translator.translate('Configure') + '</a>';
            } else {
                var configure_html = '<br /><a href="javascript:' + configureFunction + '(\'' + line_count + '\')">' + Translator.translate('Edit Configuration') + '</a>';
            }
        }

        if (product.stk_type == 'E') {
            is_kit = 'Y';
        }
    }


    if (!valueEmpty(product.lines_orig_quantity)) {
        row.down('.lines_orig_quantity').writeAttribute('value', product.lines_orig_quantity);
    }

    if (!valueEmpty(product.request_date)) {
        row.down('.lines_request_date').writeAttribute('value', product.request_date);
    }

    row.down('.lines_is_kit').writeAttribute('value', is_kit);
    row.down('.is_kit_display').update(is_kit);
    row.down('.product_code_display').update(sku + configure_html);

    if (sendMsq === true) {
        sendMsqForLine(row);
    } else {
        processRowExtra(line_count, row, product, requires_configuration);
    }

    $('rfq_lines').down('tbody').insert({bottom: row});

    // ATTACHMENTS....

    var row = $('line_attachments_row_template').clone(true);

    row.addClassName('new');
    row.setAttribute('id', 'row-attachments-' + line_count);
    row.down('.rfq_line_attachment_add').writeAttribute('id', 'add_line_attachment_' + line_count);
    row.down('#rfq_line_attachments_').writeAttribute('id', 'rfq_line_attachments_' + line_count);
    row.down('#rfq_line_attachments__table').writeAttribute('id', 'rfq_line_attachments_' + line_count + '_table');
    row.down('#line_attachment_row_template_').writeAttribute('id', 'line_attachment_row_template_' + line_count);
    $('rfq_lines').down('tbody').insert({bottom: row});

    Calendar.setup({
        inputField: 'line_' + line_count + '_request_date',
        ifFormat: $('date_input_format').value,
        button: 'date_from_trig',
        align: 'Bl',
        singleClick: true
    });

    colorRows('rfq_lines_table', ':not(.attachment)');
    line_count += 1;
    rfqHasChanged();
}

function addLinesByJson(jsonData) {
    ewaProduct.closepopup("ewaWrapper");
    $('loading-mask').show();

    jsonData = jsonData.evalJSON();

    errors = false;

    if (jsonData.errors) {
        errors = true;
    }

    linesAdded = false;
    showConfiguratorMessage = false;

    if (jsonData.products) {
        linesAdded = true;
        for (index = 0; index < jsonData.products.length; index++) {
            product = jsonData.products[index];

            if (product.sku.search(escapeRegExp($('la_separator').value)) != -1) {
                product.sku = product.sku.replace($('la_separator').value + product.uom, '');
            }

            addLineRow(false, product.sku, product.qty, product);

            if (product.configurator == 1 || product.type_id == 'configurable' || (product.type_id == 'grouped' && !product.stk_type)) {
                showConfiguratorMessage = true;
            }
        }
    }

    if (!linesAdded) {
        message = Translator.translate('No lines added');
    } else {
        message = Translator.translate('Line(s) added successfully');

        if (showConfiguratorMessage)
        {
            message += '\n\n' + Translator.translate('One or more products require configuration. Please click on each "Configure" link in the lines list');
        }
        rfqHasChanged();
    }

    recalcLineTotals();

    alert(message);

    $('linesearch-iframe').writeAttribute('src', '/customerconnect/rfqs/linesearch/')

}

function setupLineInputs(row) {

    row.addClassName('new');
    row.setAttribute('id', 'lines_' + line_count);
    resetInputs(row);

    row.down('.plus-minus').writeAttribute('id', 'attachments-' + line_count);
    row.down('.lines_orig_quantity').writeAttribute('name', 'lines[new][' + line_count + '][lines_orig_quantity]');
    row.down('.lines_delete').writeAttribute('name', 'lines[new][' + line_count + '][delete]');
    row.down('.lines_product_code').writeAttribute('name', 'lines[new][' + line_count + '][product_code]');
    row.down('.lines_description').writeAttribute('name', 'lines[new][' + line_count + '][description]');
    row.down('.lines_attributes').writeAttribute('name', 'lines[new][' + line_count + '][attributes]');
    row.down('.lines_group_sequence').writeAttribute('name', 'lines[new][' + line_count + '][group_sequence]');
    row.down('.lines_group_sequence').writeAttribute('id', 'group_sequence_' + line_count);
    row.down('.lines_configured').writeAttribute('name', 'lines[new][' + line_count + '][configured]');
    row.down('.lines_ewa_code').writeAttribute('name', 'lines[new][' + line_count + '][ewa_code]');
    row.down('.lines_ewa_code').writeAttribute('id', 'ewa_code_' + line_count);
    row.down('.lines_quantity').writeAttribute('name', 'lines[new][' + line_count + '][quantity]');
    row.down('.lines_type').writeAttribute('name', 'lines[new][' + line_count + '][type]');
    row.down('.lines_is_kit').writeAttribute('name', 'lines[new][' + line_count + '][is_kit]');
    row.down('.lines_price').writeAttribute('name', 'lines[new][' + line_count + '][price]');
    row.down('.lines_line_value').writeAttribute('name', 'lines[new][' + line_count + '][line_value]');
    row.down('.lines_unit_of_measure_code').writeAttribute('name', 'lines[new][' + line_count + '][unit_of_measure_code]');
    row.down('.lines_request_date').writeAttribute('name', 'lines[new][' + line_count + '][request_date]');
    row.down('.lines_request_date').writeAttribute('id', 'line_' + line_count + '_request_date');
    row.down('.lines_additional_text').writeAttribute('name', 'lines[new][' + line_count + '][additional_text]');
    row.down('.lines_product_json').writeAttribute('name', 'lines[new][' + line_count + '][lines_product_json]');
    row.down('.lines_orig_quantity').value = 0;
}

/*************************************
 * Line Editing
 */

function sendMsqForLine(line) {
    $('loading-mask').show();
    var pSku = line.down('.lines_product_code').value;

    if (line.down('.lines_unit_of_measure_code').value && valueEmpty(line.down('.lines_ewa_code').value)) {
        pSku = pSku + $('la_separator').value + line.down('.lines_unit_of_measure_code').value;
    }

    var skuData = pSku;
    var qty = parseFloat(line.down('.lines_quantity').value);
    var att = line.down('.lines_attributes').value;
    var ewa = '';

    if (!valueEmpty(line.down('.lines_ewa_code').value)) {
        ewa = line.down('.lines_ewa_code').value;
    }

    var postData = {'from': 'rfq', 'sku[]': [skuData], 'qty[]': [qty], 'att[]': [att], 'ewa[]': [ewa], 'currency_code': $('quote_currency_code').value}
    var url = $('la_msq_link').value;

    performAjax(url, 'post', postData, function (data) {
        var msqData = data.responseText.evalJSON();

        if (!msqData[pSku]) {
            $('loading-mask').hide();
            return;
        }
        pData = msqData[pSku];
        updateLinePrice(line, pData);
        line = processRowExtra(line.readAttribute('id').replace('lines_', ''), line, pData, false);
        recalcLineTotals();
        $('loading-mask').hide();
    });
}

/*****************************************
 * EWA functions
 */

function configureEwaProduct(line_id) {
    var returnurl = $('rfq_ewa_returnurl').value;

    var row = $('lines_' + line_id);
    var data = {
        productId: row.down('.lines_product_id').value,
        groupSequence: row.down('.lines_group_sequence').value,
        ewaCode: row.down('.lines_ewa_code').value,
        sku: row.down('.lines_product_code').value,
        qty: row.down('.lines_quantity').value,
        quoteId: $('web_reference').value,
        lineNumber: getLineNumberById(line_id)
    };

    configurator_line = line_id;
    if (valueEmpty(data.ewaCode) && valueEmpty(data.groupSequence)) {
        ewaProduct.submit(data, returnurl);
    } else {
        ewaProduct.edit(data, returnurl);
    }
}

function updateConfiguratorProductsByJson(jsonData) {
    ewaProduct.closepopup("ewaWrapper");
    $('loading-mask').show();
    jsonData = jsonData.evalJSON();

    if (jsonData.errors.length > 0) {
        $('loading-mask').hide();
        return;
    }

    if (jsonData.products) {
        for (index = 0; index < jsonData.products.length; index++) {
            var product = jsonData.products[index];
            break
        }

        product.sku = getNiceSku(product, product.sku);
        var row = $('lines_' + configurator_line);
        var qty = row.down('.lines_quantity').value;
        updateLineEwaInfo(row, product);

        updateLinePrice(row, product);
        row = processRowExtra(configurator_line, row, product, false);
        recalcLineTotals();

        if (row.down('.lines_quantity').value > 1) {
            sendMsqForLine(row);
        }
    }

    $('loading-mask').hide();
}

function updateLineEwaInfo(row, product, newline) {
    var br = '<br />';
    var ewa_text = ''

    var rowId = row.readAttribute('id').replace('lines_', '');

    if (newline !== true) {
        ewa_text = Translator.translate('Edit Configuration');
    } else {
        ewa_text = Translator.translate('Configure');
    }

    var ewa_html = br + '<a href="javascript:configureEwaProduct(\'' + rowId + '\')">' + ewa_text + '</a>';
    row.down('.product_code_display').update(product.ewa_sku + ewa_html);

    var ewa_desc = [];

    if ($('rfq_ewa_show_basedesc').value == 'Y') {
        ewa_desc.push(product.name);
    }

    if ($('rfq_ewa_show_title').value == 'Y') {
        ewa_desc.push(atob(product.ewa_title));
    }
    if ($('rfq_ewa_show_sku').value == 'Y') {
        ewa_desc.push(product.ewa_sku);
    }
    if ($('rfq_ewa_show_shortdesc').value == 'Y') {
        ewa_desc.push(atob(product.ewa_short_description));
    }
    if ($('rfq_ewa_show_desc').value == 'Y') {
        ewa_desc.push(atob(product.ewa_description));
    }
    row.down('.description_display').update(ewa_desc.join(br + br));
    row.down('.lines_ewa_title').value = product.ewa_title;
    row.down('.lines_ewa_sku').value = product.ewa_sku;
    row.down('.lines_ewa_short_description').value = product.ewa_short_description;
    row.down('.lines_ewa_description').value = product.ewa_description;
    row.down('.lines_ewa_code').value = product.ewa_code;
    row.down('.lines_attributes').value = product.ewa_attributes;
    /* E10 has an issue and always returns qty as 1 -- Keep this line until the issue is fixed */
    if (product.qty > 1) {
        row.down('.lines_quantity').value = product.qty;
    }
    row.down('.lines_configured').value = '';

    formatNumber(row.down('.lines_quantity'));
}

function updateLinePrice(row, product) {
    if (!row.down('.lines_price').hasClassName('no_update')) {
        row.down('.lines_price').value = parseFloat(product.use_price).toFixed(2);
        row.down('.lines_line_value').value = (parseFloat(product.use_price).toFixed(2) * product.qty);
        row.down('.lines_price_display').update(product.formatted_price);
        row.down('.lines_line_value_display').update(product.formatted_total);
        rfqHasChanged();
    }
}

/*****************************************
 * Configurable product functions
 */

function configureProduct(line_id) {
    var url = $('rfq_configurable_url').value;
    var row = $('lines_' + line_id);
    var product_id = row.down('.lines_product_id').value;
    var qty = row.down('.lines_quantity').value;
    var lines_attributes = row.down('.lines_attributes').value;
    var child_id = '';

    if (row.down('.lines_child_id')) {
        child_id = row.down('.lines_child_id').value;
    }

    $('loading-mask').show();

    var form_data = {'productid': product_id, 'child': child_id, 'currency_code': $('quote_currency_code').value, 'qty': qty, 'options': lines_attributes};
    configurator_line = line_id;
    configure_id = product_id;

    performAjax(url, 'post', form_data, function (data) {
        var json = data.responseText.evalJSON();
        if (!json.error || json.error == '') {
            optionsPrice = new Product.OptionsPrice(json.jsonconfig);
            $('loading-mask').hide();
            showConfigureOverlay(json.html);
        } else {
            alert(json.error);
            if (json.error) {
                showMessage(json.error, 'error');
            }
        }

        $('loading-mask').hide();
    });
}

function showConfigureOverlay(htmlToShow) {
    var body = document.body, html = document.documentElement;
    var width = Math.max(body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollWidth, html.offsetWidth);
    var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
//    if(!$('window-overlay').down(id_to_show)){
    $('window-overlay-content').update(htmlToShow);
    $('window-overlay').setStyle({width: width + 'px', height: height + 'px'});
    $('window-overlay').show();

    var elementHeight = $('window-overlay-content').getHeight();
    var elementWidth = $('window-overlay-content').getWidth();
    var formwidth = ((html.clientWidth - elementWidth) / 2);
    var viewport = document.viewport.getDimensions();   // Gets the viewport as an object literal
    var visibleheight = viewport.height;                       // Usable window height
    var topoffset = (visibleheight - elementHeight - 50) / 2;
    $('window-overlay-content').setStyle({'top': topoffset + 'px', 'left': formwidth + 'px'});
    $('window-overlay-content').show();
}

function submitConfigurableProduct() {
    $('window-overlay').hide();
    $('loading-mask').show();
    var url = $('configure_product_form').readAttribute('action');
    var form_data = $('configure_product_form').serialize(true);

    performAjax(url, 'post', form_data, function (data) {
        var json = data.responseText.evalJSON();
        if (json.grouped) {
            var row = $('lines_' + configurator_line);
            row.remove();

            for (index = 0; index < json.grouped.length; index++) {
                product = json.grouped[index];
                product.sku = getNiceSku(product, product.sku);
                addLineRow(false, product.sku, product.qty, product);
            }

            recalcLineTotals();
            $('window-overlay-content').update('');
        } else if (json[configure_id]) {

            var product = json[configure_id];
            var row = $('lines_' + configurator_line);

            row.down('.lines_product_code').writeAttribute('value', product.sku);

            updateLinePrice(row, product);

            row.down('.uom_display').update(product.uom);
            row.down('.lines_unit_of_measure_code').writeAttribute('value', product.uom);
            row.down('.lines_child_id').writeAttribute('value', product.entity_id);
            ewa_html = '<br /><a href="javascript:configureProduct(\'' + configurator_line + '\')">' + Translator.translate('Edit Configuration') + '</a>';
            row.down('.description_display').update(product.name);
            row.down('.product_code_display').update(product.sku + ewa_html);
            row.down('.lines_configured').value = '';
            //row.down('.lines_attributes').writeAttribute('value', product.configured_options);

            if (product.option_values) {
                var optdesc = '<br /><br />';
                var options = product.option_values;
                for (index = 0; index < options.length; index++) {
                    optdesc += '<strong>' + options[index].description + '</strong>: ' + options[index].value + '<br />';
                }
                row.down('.description_display').update(product.name + optdesc);
                row.down('.lines_attributes').writeAttribute('value', product.option_values_encoded);
            }

            row = processRowExtra(configurator_line, row, product);

            recalcLineTotals();
            $('window-overlay-content').update('');
            rfqHasChanged();
        } else {
            if (json.error) {
                alert(json.error);
                $('window-overlay').show();
            }
        }
        $('loading-mask').hide();
    });

}


/***********************************************************
 * Line Deletion
 */

function confirmLinesDelete(el) {
    var allowDelete = true;
    if (confirm(Translator.translate('Are you sure you want to delete selected lines?')) === false) {
        allowDelete = false;
    }
    return allowDelete;
}

var rowProcessors = [];

function deleteLines() {

    $$('.lines_select:checked').each(function (e) {
        if (e.checked) {
            var row = e.parentNode.parentNode;
            var attachmentsRow = $('row-' + row.down('.plus-minus').readAttribute('id'));
            if (row.hasClassName('new')) {
                row.remove();
                attachmentsRow.remove();
            } else {
                row.hide();
                row.down('.lines_delete').value = 1;
                row.down('.lines_select').checked = false;
                attachmentsRow.hide();
            }
        }
    });
}

/***********************************************************
 * Line Cloning
 */

function confirmLinesClone(el) {
    var allowClone = true;
    if (confirm(Translator.translate('Are you sure you want to clone selected lines?')) === false) {
        allowClone = false;
    }
    return allowClone;
}

function cloneLines() {
    $$('.lines_select:checked').each(function (e) {
        var row = e.parentNode.parentNode;
        var rowQty = row.down('.lines_quantity').value;
        var rowProduct = row.down('.lines_product_json').value.evalJSON();
        var configured = row.down('.lines_configured').value;

        rowProduct.sku = getNiceSku(rowProduct, rowProduct.sku);
        rowProduct.qty = rowQty;
        
        var oldId = row.readAttribute('id');
        oldId = oldId.replace('lines_', '');

        rowProduct.lines_orig_quantity = rowQty;

        processRowCloneExtra(row, rowProduct);

        rowProduct.lines_additional_text = row.down('.lines_additional_text').value;

        if (rowProduct.configurator == 1 && configured == '') {
            $('loading-mask').show();
            var ewa_code = row.down('.lines_ewa_code').value;
            var gs = row.down('.lines_group_sequence').value;
            var postData = {
                ewaCode: ewa_code,
                groupSequence: gs,
                productId: rowProduct.entity_id,
                action: 'C',
                quoteId: $('web_reference').value,
                lineNumber: $$('.lines_row:not(.attachment)').length
            }
            var url = $('rfq_ewa_cimurl').value;

            performAjax(url, 'post', postData, function (data) {
                var cimData = data.responseText.evalJSON();

                if (!valueEmpty(cimData.error)) {
                    return;
                }

                rowProduct.ewa_title = row.down('.lines_ewa_title').value;
                rowProduct.ewa_sku = row.down('.lines_ewa_sku').value;
                rowProduct.ewa_short_description = row.down('.lines_ewa_short_description').value;
                rowProduct.ewa_description = row.down('.lines_ewa_description').value;

                rowProduct.ewa_code = cimData.ewa_code;
                rowProduct.ewa_attributes = cimData.ewa_attributes;

                addLineRow(rowProduct.is_custom, rowProduct.sku, rowQty, rowProduct, true);
                recalcLineTotals();
                $('loading-mask').hide();
                e.checked = false;
            });
        } else {
            if (rowProduct.is_custom == 1) {
                rowProduct.custom_description = row.down('.lines_description').value;
            }
            addLineRow(rowProduct.is_custom, rowProduct.sku, rowQty, rowProduct, true);
            e.checked = false;
        }
    });
    recalcLineTotals();
}

/***********************************************************
 * Line Attachments
 */

function addLineAttachment(element) {

    var rowId = element.readAttribute('id').replace('add_line_attachment_', '');
    var row = $('line_attachment_row_template_' + rowId).clone(true);

    $$('#rfq_line_attachments_' + rowId + '_table tbody tr:not(.line_attachment_row)').each(function (e) {
        e.remove();
    });

    row.addClassName('new');
    row.setAttribute('id', 'line_attachments_' + line_attachment_count);
    row = resetInputs(row);

    row.down('.line_attachments_delete').writeAttribute('name', 'lineattachments[new][' + rowId + '][' + line_attachment_count + '][delete]');
    row.down('.line_attachments_description').writeAttribute('name', 'lineattachments[new][' + rowId + '][' + line_attachment_count + '][description]');
    row.down('.line_attachments_filename').writeAttribute('name', 'lineattachments[new][' + rowId + '][' + line_attachment_count + '][filename]');

    $('rfq_line_attachments_' + rowId + '_table').down('tbody').insert({bottom: row});
    colorRows('rfq_line_attachments_' + rowId + '_table', '.line_attachment_row');
    line_attachment_count += 1;

}

/***********************************************************
 * UTILITY
 */

function isProductConfigurable(product) {
    return (product.configurator == 1 || product.type_id == 'configurable' || (product.type_id == 'grouped' && !product.stk_type) || product.has_options)
}

function getNiceSku(pData, defaultValue) {
    if (pData.sku) {
        if (pData.sku.search(escapeRegExp($('la_separator').value)) != -1) {
            pData.sku = pData.sku.replace($('la_separator').value + pData.uom, '');
        }
    } else {
        pData.sku = defaultValue;
    }

    return pData.sku;

}

function recalcLineTotals() {
    var subtotal = 0;
    $$('#rfq_lines_table tbody tr.lines_row:not(.attachment)').each(function (row) {

        if (row.down('.lines_delete').value != 1) {
            oqty = parseFloat(row.down('.lines_orig_quantity').value);
            qty = parseFloat(row.down('.lines_quantity').value);
            price = parseFloat(row.down('.lines_price').value);
            total = parseFloat(row.down('.lines_line_value').value);

            if (row.down('.lines_type').value == 'S') {
                total = qty * price;
                row.down('.lines_line_value').value = total;
                row.down('.lines_line_value_display').update(priceFormatter.formatPrice(total));
                row.down('.lines_orig_quantity').value = qty;
            } else {
                if (oqty != qty || price == '') {
                    //row.down('.lines_line_value').value = '';
                    row.down('.lines_line_value_display').update(Translator.translate('TBA'));
                    subtotal = Translator.translate('TBA');
                } else {
                    row.down('.lines_line_value_display').update(priceFormatter.formatPrice(total));
                }
            }

            if (subtotal != Translator.translate('TBA')) {
                subtotal += total;
            }
        }
    });

    if (subtotal != Translator.translate('TBA')) {
        subtotal = priceFormatter.formatPrice(subtotal);
    }
    $('rfq_lines_table').down('.subtotal .price').update(subtotal);
    $('rfq_lines_table').down('.shipping .price').update(Translator.translate('TBA'));
    if ($('rfq_lines_table').down('.tax .price')) {
        $('rfq_lines_table').down('.tax .price').update(Translator.translate('TBA'));
    }
    $('rfq_lines_table').down('.grand_total .price').update(Translator.translate('TBA'));
}

var rowProcessors = [];

function addRowProcessor(rowfunction) {
    rowProcessors[rowProcessors.length] = rowfunction;
}

function processRowExtra(rowid, row, product, requires_configuration) {

    for (rowcount = 0; rowcount < rowProcessors.length; rowcount++) {
        extrafunction = rowProcessors[rowcount];
        row = extrafunction(rowid, row, product, requires_configuration);
    }

    return row;
}

var cloneProcessors = [];

function addRowCloneProcessor(rowfunction) {
    cloneProcessors[cloneProcessors.length] = rowfunction;
}

function processRowCloneExtra(row, product) {

    for (clonecount = 0; clonecount < cloneProcessors.length; clonecount++) {
        extrafunction = cloneProcessors[clonecount];
        row = extrafunction(row, product);
    }

    return row;
}

function getLineNumberById(line_id) {
    var lineNumber = 1;
    $$('.lines_row').each(function (e) {
        var currentLineId = e.identify();
        if (currentLineId == 'lines_' + line_id) {
            throw $break;
        } else if (currentLineId.substr(0, 6) == 'lines_') {
            lineNumber++;
        }
    });
    return lineNumber;
}