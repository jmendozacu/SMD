
document.observe('dom:loaded', function () {

    if ($('add_all_to_basket')) {
        var allAddFormHtml = jQuery('.addalltobasketform').html();
        jQuery('.quickorderpad').delegate("#add_all_to_basket", "click", function (event) {
            event.preventDefault();
            var products = 0;
            if(checkDecimal('qop-list'))
            {
            $$('.addall_qty').each(function (ele) {
                if (ele.value > 0) {
                    productForm = ele.up();
                    var product = ele.readAttribute('id').replace('qty_', '');
                    products++;
                    inputs = productForm.getInputs();
                    for (i = 0; i < inputs.length; i++) {
                        input = inputs[i].clone();
                        name = input.readAttribute('name');

                        if (name.indexOf('[') != -1) {
                            name = 'products[' + product + '][multiple][' + products + '][' + name.replace('[', '][')
                        } else {
                            name = 'products[' + product + '][multiple][' + products + '][' + name + ']';
                        }

                        input.writeAttribute('name', name);
                        input.writeAttribute('type', 'hidden');

                        $('add_all_to_basket').insert({
                            after: input
                        });
                    }
                }
            });
            }
            var allAddForm = $('add_all_to_basket').up();
            jQuery.ajax({
                type: "POST",
                url: '/retailer/ajax/addAllToBasket',
                data: allAddForm.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 'Success') {
                      allAddForm.submit();
                    } else if (data.status == 'Failed') {
                        jQuery('.addalltobasketform').html(allAddFormHtml);
                        var faildProducts = data.faild_products;
                        for(var p in faildProducts){
                            jQuery('#qty_'+p).parent().append('<div class="validation-advice" style="white-space: normal;word-wrap: break-word;">Sorry but your order quantity exceeds the available maximum quantity of #. Please contact our Customer services team on +44 (0) 1772 651199 for further assistance.</div>'.replace('#', faildProducts[p]));
                        }
                    } else {
                        console.log('wrong');
                    }
                },
            });
            // if (products == 0) {
            //     event.preventDefault();
            // }
        });
    }
});