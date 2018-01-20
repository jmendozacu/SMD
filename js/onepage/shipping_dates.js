/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var ShippingDates = Class.create(Checkout,{
    initialize: function($super,accordion, urls){
        $super(accordion, urls);
        //New Code Addded
        this.steps = ['login', 'billing', 'shipping', 'shipping_method', 'shipping_dates', 'payment', 'review'];
    }
});

var ShippingDatesMethod = Class.create();
ShippingDatesMethod.prototype = {
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
        if(!this.validator.validate()) {
            return false;
        }
        return true;
    },
 
    save: function(){
 
        if (checkout.loadWaiting!=false) return;
        if (this.validate()) {
            checkout.setLoadWaiting('shipping_dates');
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
 
 
        if (response.goto_section) {
            checkout.gotoSection(response.goto_section, true);
            checkout.reloadProgressBlock();
            return;
        }
 
        checkout.setPayment();
    }
}