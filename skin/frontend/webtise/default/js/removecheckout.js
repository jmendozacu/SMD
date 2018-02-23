var SilkCheckout = Class.create(ShippingDates, {
    initialize: function($super,accordion, urls){
        $super(accordion, urls);
        this.steps = ['login', 'billing', 'shipping', 'review'];
    }
});


var SilkCheckoutReview = Class.create(Review,{
    initialize: function($super,saveUrl, successUrl, agreementsForm){
        $super(saveUrl, successUrl, agreementsForm);
    },
    save: function(){
        if (checkout.loadWaiting!=false) return;
        checkout.setLoadWaiting('review');
        var params = 'payment[method]=free';
        if (this.agreementsForm) {
            params += '&'+Form.serialize(this.agreementsForm);
        }
        params.save = true;
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                parameters:params,
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout)
            }
        );
    }
});
