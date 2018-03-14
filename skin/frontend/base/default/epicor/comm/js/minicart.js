Minicart.prototype.updateItem = function(el) {   
        var cart = this;
        var input = $j(this.selectors.quantityInputPrefix + $j(el).data('item-id'));
        var quantity = input.val();
        
        if(checkDecimal('qty-'+$j(el).data('item-id')))
        {
        cart.hideMessage();
        cart.showOverlay();
        $j.ajax({
            type: 'POST',
            dataType: 'json',
            url: input.data('link'),
            data: {qty: quantity, form_key: cart.formKey}
        }).done(function(result) {
            cart.hideOverlay();
            if (result.success) {
                cart.updateCartQty(result.qty);
                if (quantity !== 0) {
                    cart.updateContentOnUpdate(result);
                } else {
                    cart.updateContentOnRemove(result, input.closest('li'));
                }
            } else {
                cart.showMessage(result);
            }
        }).error(function() {
            cart.hideOverlay();
            cart.showError(cart.defaultErrorMessage);
        });
    }
        return false;
    }
