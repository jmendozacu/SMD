document.observe('dom:loaded', function() {

    if ($('rfq_save')) {
        $('rfq_save').observe('click', function(event) {
            if (!validateRfqForm()) {
                event.stop();
            } else {

                $('loading-mask').show();
                url = $('rfq_update').readAttribute('action');
                url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
                var form_data = $('rfq_update').serialize(true);

                this.ajaxRequest = new Ajax.Request(url, {
                    method: 'post',
                    parameters: form_data,
                    onComplete: function(request) {
                        this.ajaxRequest = false;
                    }.bind(this),
                    onSuccess: function(data) {

                        var json = data.responseText.evalJSON();
                        if (json.type == 'success') {
                            if (json.redirect) {
                                window.location.replace(json.redirect);
                            }
                        } else {
                            $('loading-mask').hide();
                            if (json.message) {
                                showMessage(json.message, json.type);
                            }
                        }
                    }.bind(this),
                    onFailure: function(request) {
                        alert('Error occured in Ajax Call');
                    }.bind(this),
                    onException: function(request, e) {
                        alert(e);
                    }.bind(this)
                });
            }

            event.stop();
        });
    }

    document.observe('change', function(e, el) {
        if (el = e.findElement('#base_unit_price')) {
            recalcPriceBreaks();
        } else if (el = e.findElement('#discount_percent')) {
            recalcPriceBreaks();
        } else if (el = e.findElement('.price_break_modifier')) {
            recalcPriceBreakRow(el.parentNode.parentNode);
        } else if (el = e.findElement('#price_break_modifier')) {
            recalcPriceBreaks();
        } else if (el = e.findElement('#expires_date')) {
            recalcExpiry('days');
        } else if (el = e.findElement('#days')) {
            recalcExpiry('expires_date');
        } else if (el = e.findElement('.cross_reference_part_manufacturer')) {
            updateManufacturerProductCodes(el);
        }
    });

    document.observe('click', function(e, el) {
        if (el = e.findElement('.price_break_delete')) {
            deleteElement(el, 'rfq_price_breaks');
            checkCount('rfq_price_breaks', 'qpb_row', 4);
        } else if (el = e.findElement('.suom_delete')) {
            deleteElement(el, 'rfq_supplier_unit_of_measures');
            checkCount('rfq_supplier_unit_of_measures', 'suom_row', 6);
        } else if (el = e.findElement('.cross_reference_part_delete')) {
            deleteElement(el, 'rfq_cross_reference_parts');
            checkCount('rfq_cross_reference_parts', 'xref_row', 6);
        }
    });

    document.observe('keyup', function(e, el) {
        if (el = e.findElement('.price_break_modifier')) {
            formatNumber(el, true, true);
        } else if (el = e.findElement('.price_break_min_quantity')) {
            formatNumber(el, false, true);
        } else if (el = e.findElement('.price_break_days_out')) {
            formatNumber(el, false, false);
        } else if (el = e.findElement('.suom_value')) {
            formatNumber(el, false, true);
        } else if (el = e.findElement('.cross_reference_part_supplier_lead_days')) {
            formatNumber(el, false, false);
        } else if (el = e.findElement('#base_unit_price')) {
            formatNumber(el, false, true);
        } else if (el = e.findElement('#minimum_price')) {
            formatNumber(el, false, true);
        } else if (el = e.findElement('#discount_percent')) {
            formatNumber(el, true, true);
        } else if (el = e.findElement('#lead_days')) {
            formatNumber(el, false, false);
        } else if (el = e.findElement('#quantity_on_hand')) {
            formatNumber(el, false, false);
        } else if (el = e.findElement('#days')) {
            formatNumber(el, false, false);
        }
    });

    if ($('add_price_break')) {
        price_break_count = $$('#rfq_price_breaks tbody tr.qpb_row').length;
        $('add_price_break').observe('click', function(event) {
            addPriceBreakRow();
            event.stop();
        });
    }

    // Supplier uom processing
    if ($('add_suom')) {
        suom_count = $$('#rfq_supplier_unit_of_measures tbody tr.suom_row').length;
        $('add_suom').observe('click', function(event) {
            addSuomRow();
            event.stop();
        });
    }

    // Supplier uom processing
    if ($('add_cross_reference_part')) {
        cross_reference_count = $$('#rfq_cross_reference_parts tbody tr.xref_row').length;
        $('add_cross_reference_part').observe('click', function(event) {
            addCrossReferencePartRow();
            event.stop();
        });
    }
})

function showMessage(txt, type) {
    $$('.messages').each(function(e) {
        e.hide()
    })
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
}

function validateRfqForm() {

    valid = true;
    // validate UOMS
    // - must not be duplicate UOM
    // - must be at least one
    errorMessage = '';
    suomlist = $$('#rfq_supplier_unit_of_measures_table tbody tr.suom_row');
    if (suomlist.length == 0) {
        errorMessage += 'You must supply at least one Supplier Unit Of Measure\n';
    } else {
        blank = true;
        duplicates = false;
        uomArray = new Array();
        suomlist.each(function(e) {
            var uom = e.select('.suom_unit_of_measure').shift().value;
            var deleted = false;
            
            if(e.select('.suom_delete').length > 0) {
                var deleted = e.select('.suom_delete').shift().checked;
            }
            
            if (uom != '' && !deleted) {
                blank = false;
                if (uomArray.indexOf(uom) != -1) {
                    duplicates = true;
                }
                uomArray[uomArray.length] = uom;
            }
        });

        if (blank) {
            errorMessage += 'You must supply at least one Supplier Unit Of Measure \n';
        } else if (duplicates) {
            errorMessage += 'You must supply only unique Supplier Unit Of Measures \n';
        }
    }

    // - Cross reference parts
    // - no duplicate combo of Manufacturer + Manufacturers part + Supplier part
    xrlist = $$('#rfq_cross_reference_parts_table tbody tr.xref_row');
    duplicates = false;
    missing = false;
    xrArray = new Array();
    xrlist.each(function(e) {
        var manufacturer = e.select('.cross_reference_part_manufacturer').shift().value;
        var manufacturer_product = e.select('.cross_reference_part_manufacturers_product_code').shift().value;
        var supplier_product = e.select('.cross_reference_part_supplier_product_code').shift().value;
        var combined = manufacturer.trim() + manufacturer_product.trim() + supplier_product.trim();
        var deleted = e.select('.cross_reference_part_delete').shift().checked;
        if (combined != '' && !deleted) {
            blank = false;
            if(supplier_product == '') {
                 missing = true;
            }
            
            if (xrArray.indexOf(combined) != -1) {
                duplicates = true;
            }
            xrArray[xrArray.length] = combined;
        } else {
            missing = deleted ? false : true;
        }
    });

    if (duplicates) {
        errorMessage += 'Each Cross Reference Part must have a unique combination of Manufacturer, Manufacturer\'s Part and Supplier\'s Part \n';
    }
    
    if (missing) {
        errorMessage += 'You must supply a Supplier\'s Part for each Cross Reference Part\n';
    }

    // - Price breaks
    // - no duplicate quantity
    pblist = $$('#rfq_price_breaks_table tbody tr.qpb_row');
    duplicates = false;
    pbArray = new Array();
    pblist.each(function(e) {
        var quantity = e.select('.price_break_min_quantity').shift().value;
        var deleted = e.select('.price_break_delete').shift().checked;
        if (quantity != '' && !deleted) {
            blank = false;
            if (pbArray.indexOf(parseInt(quantity)) != -1) {
                duplicates = true;
            }
            pbArray[pbArray.length] = parseInt(quantity);
        }
    });

    if (duplicates) {
        errorMessage += 'You must supply only unique Quanities in the Quantity Price Breaks \n';
    }

    if (errorMessage != '') {
        valid = false;
        alert(errorMessage);
    }


    return valid;
}

function recalcExpiry(fieldToChange) {

    newValue = '';

    if (fieldToChange == 'days') {
        var effectiveDate = new Date($('effective_date').value);
        var expiryDate = new Date($('expires_date').value);
        if (expiryDate.getFullYear() < 2000) {
            expiryDate.setFullYear(expiryDate.getFullYear() + 100);
        }
        newValue = Math.round((expiryDate - effectiveDate) / (1000 * 60 * 60 * 24));
    } else if (fieldToChange == 'expires_date') {
        var effectiveDate = new Date($('effective_date').value);
        var days = parseInt($('days').value);
        var expiryDate = new Date();
        expiryDate.setDate(effectiveDate.getDate() + days)
        newValue = (expiryDate.getMonth() + 1) + '/' + expiryDate.getDate() + '/' + expiryDate.getFullYear();
    }

    $(fieldToChange).value = newValue;

}

function recalcPriceBreaks() {
    $$('#rfq_price_breaks tbody tr.qpb_row').each(function(e) {
        recalcPriceBreakRow(e);
    });
}

function recalcPriceBreakRow(el) {

    if ($('base_unit_price').value.length == 0) {
        $('base_unit_price').value = 0;
    }

    if ($('discount_percent').value.length == 0) {
        $('discount_percent').value = 0;
    }
    
    if (el.down('.price_break_modifier').value.length == 0) {
        el.down('.price_break_modifier').value = 0;
    }
    
    var basePrice = parseFloat($('base_unit_price').value).toFixed(5);
    var discount = parseFloat($('discount_percent').value).toFixed(5);
    var pbmodifier = $('price_break_modifier').value;
    var modifier = parseFloat(el.down('.price_break_modifier').value);

    if (pbmodifier == '$') {
        basePrice = parseFloat(basePrice) + parseFloat(modifier);
    } else {
        basePrice = basePrice * ((100 - modifier) / 100);
    }

    if (discount > 0) {
        basePrice = basePrice * ((100 - discount) / 100);
    }

    basePrice = basePrice.toFixed(5);
    el.down('.price_break_effective_price').value = basePrice;
    el.down('.price_break_effective_price_label').innerHTML = basePrice;
}

function updateManufacturerProductCodes(el) {
    var manufacturer = el.selectedIndex >= 0 ? el.options[el.selectedIndex].value : undefined;

    if ($('manufacturer_product_codes_' + manufacturer)) {
        el.parentNode.parentNode.select('.cross_reference_part_manufacturers_product_code').shift().innerHTML = $('manufacturer_product_codes_' + manufacturer).innerHTML;
    } else {
        el.parentNode.parentNode.select('.cross_reference_part_manufacturers_product_code').shift().innerHTML = '<option value=""></option>';
    }

}

function deleteElement(el, table_id) {
    var disabled = false;
    if (el.checked) {
        disabled = true;
    }
    if (el.parentNode.parentNode.hasClassName('new')) {
        el.parentNode.parentNode.remove();
        colorRows(table_id, '');
    } else {
        el.parentNode.parentNode.select('input[type=text],select').each(function(input) {
            input.disabled = disabled;
        });
    }
}

function formatNumber(el, allowNegatives, allowFloats) {
    var value = el.value, firstChar, nextFirst;
    if (value.length == 0)
        return;

    firstChar = value.charAt(0);
    if (allowFloats) {
        value = value.replace(/[^0-9\.]/g, '');
        nextFirst = value.charAt(0);
    } else {
        value = value.replace(/[^0-9]/g, '');
        nextFirst = value.charAt(0);
    }

    if (nextFirst == '.') {
        value = '0' + value;
    }

    if (allowNegatives && firstChar == '-') {
        value = firstChar + value;
    }

    el.value = value;
}

var price_break_count = 0;
var suom_count = 0;
var cross_reference_count = 0;

function addPriceBreakRow() {
    $$('#rfq_price_breaks_table tbody tr:not(.qpb_row)').each(function(e) {
        e.remove();
    });

    var row = $('price_break_row_template').clone(true);

    row.setAttribute('id', 'price_breaks_' + price_break_count);
    row.addClassName('new');
    row = resetInputs(row);

    row.down('.price_break_effective_price').writeAttribute('name', 'price_breaks[new][' + price_break_count + '][effective_price]');
    row.down('.price_break_min_quantity').writeAttribute('name', 'price_breaks[new][' + price_break_count + '][quantity]');
    row.down('.price_break_days_out').writeAttribute('name', 'price_breaks[new][' + price_break_count + '][days_out]');
    row.down('.price_break_modifier').writeAttribute('name', 'price_breaks[new][' + price_break_count + '][modifier]');

    $('rfq_price_breaks').down('tbody').insert({bottom: row});
    row.down('.price_break_min_quantity').focus();
    colorRows('rfq_price_breaks');
    price_break_count += 1;
}

function checkCount(table, rowclass, colspan) {
    var rowCount = $$('#' + table + '_table tbody tr.' + rowclass).length;
    if (rowCount == 0) {
        row = '<tr class="even" style="">'
                + '<td colspan="' + colspan + '" class="empty-text a-center">No records found.</td>'
                + '</tr>';

        $(table + '_table').down('tbody').insert({bottom: row});

    }
}

function addSuomRow() {

    $$('#rfq_supplier_unit_of_measures_table tbody tr:not(.suom_row)').each(function(e) {
        e.remove();
    });

    var row = $('supplier_unit_of_measures_row_template').clone(true);
    row.addClassName('new');
    row.setAttribute('id', 'suom_' + suom_count);
    row = resetInputs(row);

    row.down('.suom_unit_of_measure').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][unit_of_measure]');
    row.down('.suom_conversion_factor').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][conversion_factor]');
    row.down('.suom_operator').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][operator]');
    row.down('.suom_value').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][values]');
    row.down('.suom_result').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][result]');
    row.down('.suom_delete').writeAttribute('name', 'supplier_unit_of_measures[new][' + suom_count + '][delete]');

    $('rfq_supplier_unit_of_measures').down('tbody').insert({bottom: row});
    colorRows('rfq_supplier_unit_of_measures');
    suom_count += 1;
}

function addCrossReferencePartRow() {

    $$('#rfq_cross_reference_parts_table tbody tr:not(.xref_row)').each(function(e) {
        e.remove();
    });

    var row = $('cross_reference_parts_row_template').clone(true);
    row.addClassName('new');
    row.setAttribute('id', 'cross_reference_part_' + cross_reference_count);
    row = resetInputs(row);

    row.down('.cross_reference_part_delete').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][delete]');
    row.down('.cross_reference_part_manufacturer').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][manufacturer_code]');
    row.down('.cross_reference_part_manufacturers_product_code').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][manufacturers_product_code]');
    row.down('.cross_reference_part_supplier_product_code').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][supplier_product_code]');
    row.down('.cross_reference_part_supplier_lead_days').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][supplier_lead_days]');
    row.down('.cross_reference_part_supplier_reference').writeAttribute('name', 'cross_reference_parts[new][' + cross_reference_count + '][supplier_reference]');

    $('rfq_cross_reference_parts').down('tbody').insert({bottom: row});
    colorRows('rfq_cross_reference_parts');
    cross_reference_count += 1;
}

function resetInputs(row) {
    row.select('input,select').each(function(e) {
        if (e.readAttribute('type') == 'text') {
            e.writeAttribute('value', '');
        } else if (e.readAttribute('type') == 'checkbox') {
            e.writeAttribute('checked', false);
        }

        e.writeAttribute('disabled', false);
    });

    return row;
}

function colorRows(table_id) {

    var cssClass = 'even';
    $$('#' + table_id + ' tbody tr').each(function(e) {
        e.removeClassName('even');
        e.removeClassName('odd');
        e.addClassName(cssClass);

        if (cssClass == 'even') {
            cssClass = 'odd';
        } else {
            cssClass = 'even';
        }
    });
}