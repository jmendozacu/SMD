document.observe('dom:loaded', function() {
    Payment.prototype.save = Payment.prototype.save.wrap(function(save) {
        if (checkout.loadWaiting != false) return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            checkout.setLoadWaiting('payment');
            var checkCrePresent = $('creverified');
            if (typeof(checkCrePresent) != 'undefined' && checkCrePresent != null) {
              var creVerified = checkCrePresent.value; 
            } else {
              var creVerified = 1; 
            }
            if ((this.currentMethod == "cre") && (!creVerified)) {
                $('loading-mask').show();
                if (typeof creloadpayment === "function") { 
                    creloadpayment();
                    payment.resetLoadWaiting();
                } else {
                    alert("Please Add a valid javascript url in the payment method");
                    $('loading-mask').hide();
                    payment.resetLoadWaiting();
                }
                return false;
            } else {
                if (typeof(checkCrePresent) != 'undefined' && checkCrePresent != null) {
                    checkCrePresent.value = ""; 
                }
                var request = new Ajax.Request(
                    this.saveUrl, {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form)
                    }
                );
                save();
            }
        }
    });

});
if (typeof Cre == 'undefined') {
    var Cre = {};
}
Cre.Checkout = Class.create();
Cre.Checkout.prototype = {

    initialize: function() {
        var prefix = '';
        if (Mage.Cookies.path != "/") {
            prefix = Mage.Cookies.path;
        }
        this.saveUrl = prefix + "/cre/payment/opcSaveReview";
        this.saveQuoteUrl = prefix + "/cre/payment/opsSaveCreQuote";
        this.ajaxRequest = false;
        if (!$('window-overlay')) {
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        }
        if (!$('loading-mask')) {
            $(document.body).insert('<div id="loading-mask" class="loading-mask" style="display:none;"></div>');
        }
    },
    start: function() {
        this.oldSaveUrl = payment.saveUrl;
        payment.saveUrl = this.saveUrl;
        payment.onSave = this.reviewSave.bindAsEventListener(this);
    },
    reviewSave: function(transport) {
        myjson = transport.responseText.evalJSON();
        var url = myjson.url;
        // create cre Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = "creWrapper";
        // create Close link
        var closeBtn = new Element('a');
        closeBtn.href = 'javascript:cre.closepopup("creWrapper");';
        closeBtn.update('Close');
        wrappingDiv.insert(closeBtn);
        // create iFrame
        var myIframe = new Element('iframe');
        myIframe.src = url;
        wrappingDiv.insert(myIframe)
        $('window-overlay').setStyle({
            'display': 'block',
            'position': 'fixed'
        });
        // show CRE Wrapper
        $(document.body).insert(wrappingDiv);
    },
    save: function() {
        this.closepopup("creWrapper");
        review.saveUrl = this.oldSaveUrl;
        review.onSave = review.nextStep.bindAsEventListener(review);
        window.setTimeout(this.submitSave, 100);
    },
    saveQuote: function(myJSON) {

    },
    submitSave: function() {
        review.save();
    },
    closepopup: function(popup) {
        if ($(popup))
            $(popup).remove();
        if ($('loading-mask')) {
            $('loading-mask').hide();
        }

        if ($('window-overlay'))
            $('window-overlay').setStyle({
                'display': 'none',
                'position': 'absolute'
            });
    }
}


//Save the response from the CRE payment Gateway
function saveCreQuote(myJSON) {
    var prefix = '';
    if (Mage.Cookies.path != "/") {
        prefix = Mage.Cookies.path;
    }
    $('loading-mask').show();
    this.ajaxRequests = false;
    var saveQuoteUrl = prefix + "/cre/payment/opsSaveCreQuote";
    var website = 0;
    var cresaveurl = saveQuoteUrl;
    this.ajaxRequests = new Ajax.Request(cresaveurl, {
        method: 'post',
        parameters: {
            payment: 'cre',
            website: website,
            parameters: myJSON
        },
        onComplete: function(request) {
            this.ajaxRequests = false;
        }.bind(this),
        onSuccess: function(data) {
            var json = data.responseText.evalJSON();
            if (json.success) {
                $('creverified').value = "cre";
                payment.save();
            } else {
                if(json.msg) {
                    alert(json.msg);
                } else {
                   alert("Invalid Response from CRE"); 
                }
            }
            $('loading-mask').hide();

        }.bind(this),
        onFailure: function(request) {
            alert('Error occured loading form pages');
            $('loading-mask').hide();
        }.bind(this),
        onException: function(request, e) {
            alert('Error occured loading exception form page');
            $('loading-mask').hide();
        }.bind(this)
    });
}