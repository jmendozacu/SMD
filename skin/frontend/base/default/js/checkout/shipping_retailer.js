/**
 * Created by daniel on 17-3-4.
 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var ShippingDates = Class.create(Checkout,{
    initialize: function($super,accordion, urls){
        $super(accordion, urls);
        //New Code Addded
        this.steps = ['login', 'billing', 'shipping', 'shipping_retailer', 'shipping_method', 'shipping_dates', 'payment', 'review'];
    }
});

var ShippingRetailerMethod = Class.create();
ShippingRetailerMethod.prototype = {
    initialize: function(form, saveUrl){
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        this.saveUrl = saveUrl;
        this.validator = new Validation(this.form);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },

    validate: function() {
        var methods = document.getElementsByName('shipping_retailer');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping retailer available for it. Please make necessary changes in your shipping address.').stripTags());
            return false;
        }

        if(!this.validator.validate()) {
            return false;
        }

        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        alert(Translator.translate('Please specify shipping retailer.').stripTags());
        return false;
    },

    save: function(){

        if (checkout.loadWaiting!=false) return;
        if (this.validate()) {
            checkout.setLoadWaiting('shipping_retailer');
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },

    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
    },

    nextStep: function(transport){
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }

        if (response.error) {
            alert(response.message);
            return false;
        }

        if (response.update_section) {
            $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
        }

        payment.initWhatIsCvvListeners();

        if (response.goto_section) {
            checkout.gotoSection(response.goto_section, true);
            checkout.reloadProgressBlock();
            return;
        }

        if (response.payment_methods_html) {
            $('checkout-payment-method-load').update(response.payment_methods_html);
        }

        checkout.setShippingMethod();
    }
}

//Checkout.prototype.setShippingRetailer = function() {
//    //this.nextStep();
//    this.gotoSection('shipping_method', true);
//    //this.accordion.openNextSection(true);
//}
//
//Checkout.prototype.setShipping = function() {
//    //this.nextStep();
//    this.gotoSection('shipping_retailer', true);
//    //this.accordion.openNextSection(true);
//}

var googleRetailerMap = {
    Init: function() {

    },
    // base on zip code get lat&lng
    getLocation: function(zipCode) {

    }
};
