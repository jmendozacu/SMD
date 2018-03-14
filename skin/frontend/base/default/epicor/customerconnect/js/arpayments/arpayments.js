/* global $$, Translator, Mage, Class, Ajax, Event, toString */
if (typeof Epicor_Arpayments == 'undefined') {
    var Epicor_Arpayments = {};
}
Epicor_Arpayments.arpayments = Class.create();
Epicor_Arpayments.arpayments.prototype = {
    target: null,
    wrapperId: "selectarpaymentsdetails",
    wrapperId1: "selectarpaymentsconfirm",
    initialize: function() {
        var prefix = '';
        if (Mage.Cookies.path != "/") {
            prefix = Mage.Cookies.path;
        }
        this.detailsUrl = prefix + "/customerconnect/arpayments/invoicedetails";

        this.reviewUrl = prefix + "/customerconnect/arpayments/review";

        this.savepaymentUrl = prefix + "/customerconnect/arpayments/savepayment";

        if (!$('window-overlay-arpayments'))
            $(document.body).insert('<div id="window-overlay-arpayments" class="window-overlay" style="display:none;"></div>');
        
        if (!$('loading-mask-arpayments'))
            $(document.body).insert('<div id="loading-mask-arpayments" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>');        
        

    },
    openpopup: function(newtarget, invoiceid) {
        var erpAccountGridUrl = this.detailsUrl + '?invoice=' + newtarget;
        $('loading-mask-arpayments').show();
        this.target = newtarget;
        if ($(this.wrapperId))
            $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId).hide();
        var postjsonvals = $('arpaymentjson_' + newtarget).value;
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o) {
            if (o.selected == true) {
                website = o.value;
            }
        });
        var elementInvoice = $('dispute_invoices_' + invoiceid);
        if (typeof(elementInvoice) != 'undefined' && elementInvoice != null) {
            var dispute = $('dispute_invoices_' + invoiceid).checked;
            var disputeComment = $('dispute_invoices_comments_' + invoiceid).value;
        } else {
            dispute = '';
            disputeComment = '';
        }
        this.ajaxRequest = new Ajax.Request(erpAccountGridUrl, {
            method: 'post',
            parameters: {
                field_id: newtarget,
                website: website,
                postparams: postjsonvals,
                invoiceid: invoiceid,
                dispute: dispute,
                disputeComment: disputeComment
            },
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request) {
                $('loading-mask-arpayments').hide();
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();
                $('window-overlay-arpayments').show();
                this.updateWrapper();
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured loading erp accounts grid');
                this.closepopup();
            }.bind(this),
            onException: function(request, e) {
                alert('Error occured loading erp accounts grid');
                this.closepopup();
            }.bind(this)
        });
    },
    openreviewpopup: function(newtarget) {
        var erpAccountGridUrl = this.reviewUrl;
        $('loading-mask-arpayments').show();
        this.target = newtarget;
        if ($(this.wrapperId))
            $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o) {
            if (o.selected == true) {
                website = o.value;
            }
        });
        var proceedPost = proceedToPost();
        var allocatedAmount = parseFloat($('allocate_amount_original').value);
        if (isNaN(allocatedAmount)) {
            allocatedAmount =0;
        }
        var amountLeft = $('amount_left_ar').value;
        var paymentOnAccount = $('payment_on_account').checked;
        var postData = {
            'from': 'arpaymentscreen',
            'postvals[]': [proceedPost],
            'allocatedAmount': allocatedAmount,
            'amountLeft': amountLeft,
            'paymentOnAccount': paymentOnAccount,
            'payment[arpaymentpage]':true
        };
        this.ajaxRequest = new Ajax.Request(erpAccountGridUrl, {
            method: 'post',
            parameters: postData,
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request) {
                $('loading-mask-arpayments').hide();
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();
                $('window-overlay-arpayments').show();
                this.updateWrapper();
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured loading erp accounts grid');
                this.closepopup();
            }.bind(this),
            onException: function(request, e) {
                alert('Error occured loading erp accounts grid');
                this.closepopup();
            }.bind(this)
        });
    },
    openpaymentpopup: function(newtarget, selectedPayment) {
        var erpAccountGridUrl = this.savepaymentUrl;
        $('loading-mask-arpayments').show();
        this.target = newtarget;
        if ($(this.wrapperId1))
            $(this.wrapperId1).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId1;
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId1).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o) {
            if (o.selected == true) {
                website = o.value;
            }
        });
        var proceedPost = proceedToPost();
        var getAllitems = $('invoicearvariables').value;
        var totalpayments = $('totalarpayment').value;
        var postData = {
            'from': 'rfq',
            'postvals[]': [getAllitems],
            'totalpayment': totalpayments,
            'paymentmethod': selectedPayment
        };
        this.ajaxRequest = new Ajax.Request(erpAccountGridUrl, {
            method: 'post',
            parameters: postData,
            onComplete: function(request) {
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request) {
                $('loading-mask-arpayments').hide();
                $(this.wrapperId1).insert(request.responseText);
                $(this.wrapperId1).show();
                $('window-overlay-arpayments').show();
                this.updateWrapperConfirm();
            }.bind(this),
            onFailure: function(request) {
                alert('Error occured loading erp accounts grid');
                this.closepopuppayment();
            }.bind(this),
            onException: function(request, e) {
                alert('Error occured loading erp accounts grid');
                this.closepopuppayment();
            }.bind(this)
        });
    },
    closepopup: function(invoiceId) {
        if (typeof(invoiceId) != 'undefined' && invoiceId != null) {
            setCommenttoGrid(invoiceId);
        }
        $(this.wrapperId).remove();
        $('window-overlay-arpayments').hide();
    },
    closepopuppayment: function() {
        $(this.wrapperId1).remove();
        $('window-overlay-arpayments').hide();
    },
    selectErpAccount: function(grid, event) {
        if (typeof event != 'undefined') {
            var row = Event.findElement(event, 'tr');
            var erpaccount_id = row.title;
            var erpaccount_name = row.select('td.last')[0].innerHTML;
            $(this.target).value = erpaccount_id;
            $(this.target + '_name').innerHTML = erpaccount_name;
            this.closepopup();
        }
    },
    removeErpAccount: function(target) {
        $(target).value = '';
        $(target + '_name').innerHTML = 'No ERP Account Selected';
    },
    updateWrapper: function() {
        if ($(this.wrapperId)) {
            var height = 40;

            $$('#' + this.wrapperId + ' > *').each(function(item) {
                height += item.getHeight();
            });

            if (height > ($(document.viewport).getHeight() - 60))
                height = $(document.viewport).getHeight() - 60;

            if (height < 35)
                height = 55;

            $(this.wrapperId).setStyle({
                'height': height + 'px',
                'marginTop': '-' + (height / 2) + 'px',
                'width': "70%"
            });
        }
    },
    updateWrapperConfirm: function() {
        if ($(this.wrapperId)) {
            var height = 20;

            $$('#' + this.wrapperId + ' > *').each(function(item) {
                height += item.getHeight();
            });

            if (height > ($(document.viewport).getHeight() - 40))
                height = $(document.viewport).getHeight() - 40;

            if (height < 35)
                height = 35;

            $(this.wrapperId).setStyle({
                'height': height + 'px',
                'marginTop': '-' + (height / 2) + 'px',
                'width': "70%"
            });
        }
    }
};

var arpayments = 'test';
document.observe('dom:loaded', function() {
    arpayments = new Epicor_Arpayments.arpayments();
    Event.observe(window, "resize", function() {
        arpayments.updateWrapper();
    });
    Event.observe(window, "resize", function() {
        arpayments.updateWrapperConfirm();
    });

    Event.live('.allocate_amount', 'keyup', function(el, e) {
        el.value = el.value.replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2');
    });
    Event.live('.allocate_amount', 'paste', function(el, e) {
        setTimeout(function() {
            el.value = el.value.replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2');
        }, 100);
    });
    Event.live('.arpayment_amount', 'keyup', function(el, e) {
        el.value = el.value.replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2');
        checkArRowTotal(el, e);
        calculateArAmount(el, e);
    });
    Event.live('.arpayment_amount', 'paste', function(el, e) {
        setTimeout(function() {
            el.value = el.value.replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2');
            checkArRowTotal(el, e);
            calculateArAmount(el, e);
        }, 100);
    });
    Event.live('.arpayment_amount', 'cut', function(el, e) {
        setTimeout(function() {
            el.value = el.value.replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2');
            checkArRowTotal(el, e);
            calculateArAmount(el, e);
        }, 100);
    });
    Event.live('.select_arpayments', 'click', function(el, e) {
        if (!el.checked) {
            var checkboxid = el.value;
            $('arpayment_amount' + checkboxid).value = 0;
            calculateArAmountAllocate();
        }
    });
    Event.live('.dispute_invoices', 'click', function(el, e) {
        if (el.checked) {
            id = el.value;
            if ($('dispute_invoices_comments_' + id)) {
                var plusminus = $(id).innerHTML;
                if (plusminus == "+") {
                    $('dispute_invoices_comments_' + id).show();
                    $(id).innerHTML == '+' ? $(id).innerHTML = '-' : $(id).innerHTML = '+';
                }  else  if (plusminus == "-") {
                    $('dispute_invoices_comments_' + id).show();
                  
                }
                else {
                    $('dispute_invoices_comments_' + id).hide();
                    $(id).innerHTML == '-' ? $(id).innerHTML = '+' : $(id).innerHTML = '-';
                }
                
            }
        } else {
            id = el.value;
            if ($('dispute_invoices_comments_' + id)) {
                var plusminus = $(id).innerHTML;
                $('dispute_invoices_comments_' + id).hide();
                $(id).innerHTML == '+' ? $(id).innerHTML = '+' : $(id).innerHTML = '+';
            }
        }
    });

    //function for Total Amount to appyly with blur method  
    var allocateAmt = $("allocate_amount");
    allocateAmt.onblur = function() {
        var amountLeft = $('amount_left_ar').value;
        var originalAmnt = $('allocate_amount_original').value;
        if (((amountLeft != "") && (amountLeft != 0) && (amountLeft != '0.00') && (allocateAmt.value != originalAmnt)) || ((allocateAmt.value != originalAmnt) && (originalAmnt != '') && (originalAmnt != '0')&& (originalAmnt != '0.00'))) {
            var r = confirm("Do you like to change the allocated amount");
            if (r == true) {
                $('payment_on_account').checked = false;
                $('allocate_amount').value = allocateAmt.value;
                if($('allocate_amount').value =="") {
                    $('allocate_amount').value = 0;
                }
                calculateArSum(true);
            } else {
                var originalAllocated = $('allocate_amount');
                originalAllocated.value = $('allocate_amount_original').value;
            }
        } else if ((allocateAmt.value == 0) && (allocateAmt.value != originalAmnt)) {
            calculateArSum(true);
        } else {
            calculateArCheckSum();
        }
    }

    //Expand the dispute column when the user clicked on "+" button in the front page
    Event.live('.expand-row', 'click', function(element) {
        id = element.down(".plus-minus").readAttribute('id');
        if ($('dispute_invoices_comments_' + id)) {
            var plusminus = element.down(".plus-minus").innerHTML;
            if (plusminus == "+") {
                $('dispute_invoices_comments_' + id).show();
            } else {
                $('dispute_invoices_comments_' + id).hide();
            }
            element.down(".plus-minus").innerHTML == '-' ? element.down(".plus-minus").innerHTML = '+' : element.down(".plus-minus").innerHTML = '-';
        }
    });

});

//Function to remove zero when the user focus on payment amount textbox
function checkOnFocus(e1) {
    if (e1.value == "0") {
        e1.value = "";
    }
}

//Function to check whether the amount entered is numberic or not
function checkIfArNumeric(s) {
    if (isNaN(s)) {
        alert("Please enter a valid amount");
        return false;
    }
    return true;
}

//Function to get the outstanding values
function getArItems() {
    var items = new Array();
    var i = 0;
    $$('#customerconnect_arpayments_table tbody tr:not(.disable_check_arpayment)').each(function(row) {
        items[i] = row.down('.aroutstanding_value').value;
        i++;
    });
    return items;
}


//Calculating the total amount, amount left, original amount
function calculateArCheckSum() {
    var payAmt = parseFloat($('allocate_amount').value);
    if ($$('.select_arpayments:checked').length > 0) {
        var i = 0;
        $$('.select_arpayments:checked').each(function(e) {
            var row = e.parentNode.parentNode;
            var temp = payAmt - row.down('.arpayment_amount').value;
            if (temp > 0) {
                payAmt = temp;
            } else if (temp <= 0) {
                payAmt = 0;
            }
        });
    }

    if (!isNaN(payAmt)) {
        var pendingAmount = payAmt.toFixed(2);
        $('arpayment_left').down('.amount_left_ar').update(pendingAmount);
        $('amount_left_ar').value = pendingAmount;
        $('allocate_amount_original').value = parseFloat($('allocate_amount').value);
    }
}


function getAllPaymentItems() {
    var items = new Array();
    var i = 0;
    $$('table#invoicepaymentamounts  tbody tr:not(.disable_check_arpayment)').each(function(row) {
        items[i] = row.down('.confirmpaymentamount').value;
        i++;
    });
    return items;
}

//Allocate the amount to all the invoices
function setArItems(items, payAmt) {
    var originalAllocated = $('allocate_amount').value;
    $('arpayment_left').down('.amount_left_ar').update(payAmt);
    $('allocate_amount_original').value = originalAllocated;
    $('amount_left_ar').value = payAmt;
    var i = 0;
    $$('#customerconnect_arpayments_table tbody tr:not(.disable_check_arpayment)').each(function(row) {
        row.down('.select_arpayments ').checked = false;
        row.down('.arpayment_amount').value = items[i];
        if (items[i] > 0) {
            var outstanding = row.down('.aroutstanding_value ').value;
            row.down('.select_arpayments ').checked = true;
            row.down('.balance_ar').innerHTML = outstanding - items[i];
        }
        i++;
    });
    calculateArAmountAllocate();
}

function itemArSum(items) {
    var sum = 0;
    if (toString.call(items) !== "[object Array]")
        return false;
    for (var i = 0; i < items.length; i++) {
        /*if (isNaN(items[i])) {
            continue;
        }*/
        sum = parseFloat(items[i]) + sum;
    }
    return sum;
}

function calculateArSum(inputElm) {
    var items = getArItems();
    var payAmt = parseFloat($('allocate_amount').value);
    var checkIfNumeric = $('allocate_amount').value;
    if ((!checkIfNumeric)) {
        alert("Please Enter a valid Amount");
        $('allocate_amount').value = $('allocate_amount_original').value;
        return false;
    }
    var checkAnythingSelected = true;
    if (typeof(inputElm) != "undefined") {
        checkAnythingSelected = checkboxSelected();
    }
    ///check anything was selected or not
    if (checkAnythingSelected) {
        var newItems = [];
        for (var i = 0; i < items.length; i++) {
            newItems[i] = 0;
        }
        var i = 0;
        while (payAmt != 0 && i < items.length) {
            var temp = payAmt - items[i];
            if (temp > 0) {
                newItems[i] = items[i];
                payAmt = temp;
            } else if (temp <= 0) {
                newItems[i] = payAmt.toFixed(2);
                payAmt = 0;
            }
            i++;
            //sum = itemSum(items);
        }
        var pendingAmount = payAmt.toFixed(2);
        setArItems(newItems, pendingAmount);
    } else {
        calculateArCheckSum();
    }
}


function checkboxSelected() {
    if ($$('.select_arpayments:checked').length > 0) {
        return true;
    } else {
        return false;
    }
}




//Input box validations for payment amount
function checkArRowTotal(el, b) {
    var parentRows = el.up('tr');
    var outstanding = parentRows.down('.aroutstanding_value ').value;
    var inptTxtVal = el.value;
    if (inptTxtVal.length == 0 && b.keyCode != '8') {
        el.value = 0;
        parentRows.down('.balance_ar').innerHTML = outstanding;
        parentRows.down('.select_arpayments').checked = false;
        return;
    }
    applyArpaymentsBalance();
    var currentHiddentAmount = parentRows.down('.aroutstanding_value').value;
    if (Number(inptTxtVal) > Number(currentHiddentAmount)) {
        el.value = currentHiddentAmount;
        parentRows.down('.select_arpayments').checked = true;
        parentRows.down('.balance_ar').innerHTML = (outstanding - currentHiddentAmount).toFixed(2);
        alert("Maximum payment amount allowed for this invoice is " + currentHiddentAmount);
        b.stop();
        return;
    } else {

        parentRows.down('.balance_ar').innerHTML = (outstanding - inptTxtVal).toFixed(2);
        if (inptTxtVal == 0) {
            parentRows.down('.select_arpayments').checked = false;
        } else {
            parentRows.down('.select_arpayments').checked = true;
        }
        el.value = inptTxtVal;
        return true;
    }
}


function applyArpaymentsBalance() {
    var allocate_amount = $('allocate_amount').value;
    var allocate_amount_original = $('allocate_amount_original').value;
    if ((allocate_amount) && (allocate_amount_original === "")) {
        $('allocate_amount_original').value = allocate_amount;
        $('arpayment_left').down('.amount_left_ar').update(allocate_amount);
        $('amount_left_ar').value = allocate_amount;
    }
}

//Calculate the payment amount when the user entered the amount in the payment amount textbox
function calculateArAmount(el, b) {
    var parentRows = el.up('tr');
    var inptTxtVal = el.value;
    var outstanding = parentRows.down('.aroutstanding_value ').value;
    if (inptTxtVal.length == 0 && b.keyCode != '8') {
        el.value = 0;
        parentRows.down('.balance_ar').innerHTML = outstanding;
        parentRows.down('.select_arpayments').checked = false;
        return;
    }
    var totalValue = 0;
    $$('#customerconnect_arpayments_table tbody tr:not(.disable_check_arpayment)').each(function(row) {
        var amountVals = row.down('.arpayment_amount').value;
        var checkamount = (amountVals) ? amountVals : '0';
        if (checkamount == 0) {
            row.down('.select_arpayments').checked = false;
        }
        totalValue += parseFloat(checkamount);
    });
    $('paymentamount_arpays').down('.paymentamount_arpay').update(totalValue.toFixed(2));
    incDecAmount(totalValue.toFixed(2));
    //$('paymentamount_arpay').innerHtml = '1111111111';
}

function incDecAmount(inptTxtVal) {
    var allocate_amount = $('allocate_amount').value;
    var amountLeft = $('amount_left_ar').value;
    if (allocate_amount && amountLeft) {
        var caclculateAmount = allocate_amount - inptTxtVal;
        if (caclculateAmount >= 0) {
            $('arpayment_left').down('.amount_left_ar').update(caclculateAmount.toFixed(2));
            $('amount_left_ar').value = caclculateAmount.toFixed(2);
        } else {
            $('arpayment_left').down('.amount_left_ar').update(0.00);
            $('amount_left_ar').value = 0.00;
        }
    }
}

// Calculate the payment amount when the user clicked on allocated amount 
// also the user selected the checkbox
function calculateArAmountAllocate(items) {
    var totalValue = 0;
    $$('#customerconnect_arpayments_table tbody tr:not(.disable_check_arpayment)').each(function(row) {
        var amountVals = row.down('.arpayment_amount').value;
        var outstanding = row.down('.aroutstanding_value ').value;
        row.down('.balance_ar').innerHTML = (outstanding - amountVals).toFixed(2);
        totalValue += parseFloat(amountVals);
    });
    $('paymentamount_arpays').down('.paymentamount_arpay').update(totalValue.toFixed(2));
    incDecAmount(totalValue);
}

//check all the select boxes are checked or not
//also checking whether the amount is greater than 0

function proceedToPreview() {
    var newItems = [];
    if ($$('.select_arpayments:checked').length > 0) {
        var i = 0;
        $$('.select_arpayments:checked').each(function(e) {
            var row = e.parentNode.parentNode;
            var amountVals = row.down('.arpayment_amount').value;
            newItems[i] = amountVals;
            i++;
        });
    } else {
        var amountVals = $('allocate_amount').value;
        var paymentOnAccount = $('payment_on_account').checked;
        var ignoreItems = false;
        if ((paymentOnAccount) && (amountVals > 0)) {
            ignoreItems = true;
        } else if ((paymentOnAccount) && (amountVals <= 0)) {
            alert("Please enter a valid amount");
            return false;
        } else {
            alert(Translator.translate('Please select one or more invoices'));
            return false;
        }
    }
    if (!ignoreItems) {
        var hasEmptyElements = hasEmptyArElement(newItems);
        if (!hasEmptyElements) {
            alert("Please enter a valid amount for the selected invoices");
            return false;
        }
    }
    arpayments.openreviewpopup('open_review_screen');

}



function checkArPaymentMethod() {
    var selectPayment = [];
    var paymentGateway = '';
    if ($$('.payment_armethod:checked').length > 0) {
        var i = 0;
        $$('.payment_armethod:checked').each(function(e) {
            var row = e.parentNode.parentNode;
            paymentGateway = row.down('.payment_armethod').value;
            selectPayment[i] = paymentGateway;
            i++;
        });
    } else {
        alert(Translator.translate('Please select a payment gateway'));
        return false;
    }
    arpayments.openpaymentpopup('ecc_selected_arpayment', selectPayment);
    return paymentGateway;

}


function proceedToPost() {
    var newItems = {};
    if ($$('.select_arpayments:checked').length > 0) {
        var i = 0;
        $$('.select_arpayments:checked').each(function(e) {
            var row = e.parentNode.parentNode;
            var jsonVals = row.down('.arpaymentjson').value;
            var jsonPaymentVals = row.down('.arpayment_amount').value;
            if (row.down('.dispute_invoices')) {
                var dispute = row.down('.dispute_invoices').checked;
                var disputeComment = row.down('.dispute_invoices_comments').value;
            } else {
                var dispute = '';
                var disputeComment = '';
            }
            var settlementDiscount = row.down('.settlement_discount').value;
            var settlementTermAmount = row.down('.settlement_term_amount').value;
            //var newJson = jsonVals.push({'myKey':jsonPaymentVals, 'status':1});
            var appendjson = JSON.parse(JSON.stringify({
                'userPaymentAmount': jsonPaymentVals,
                'dispute': dispute,
                'disputeComment': disputeComment,
                'settlementDiscount': settlementDiscount,
                'settlementTermAmount': settlementTermAmount
            }));
            var createjsonObject = JSON.parse(jsonVals);
            //combining two jsons
            createjsonObjects = extendArJson(createjsonObject, appendjson);;
            newItems[i] = createjsonObjects;
            i++;
        });
    }

    //alert(JSON.stringify(newItems));

    return JSON.stringify(newItems);
}

//check array have zero.
function hasEmptyArElement(my_arr) {
    for (var i = 0; i < my_arr.length; i++) {
        if (my_arr[i] == 0)
            return false;
    }
    return true;
}

function extendArJson(o1, o2) {
    for (var key in o2) {
        o1[key] = o2[key];
    }
    return o1;
}

function opeArpaymentTab() {
    var accordion = new Accordion('checkoutSteps', '.step-title', true);
    var checkout = new Checkout(accordion, {
        progress: '',
        review: '',
        saveMethod: '',
        failure: ''
    });
    accordion.closeSection('opc-review');
    checkout.gotoSection('payment', true);
    checkout.reloadProgressBlock();
}

function toggleArCheckbox(element, invoiceId) {
    var checkedId = 'dispute_invoices_' + invoiceId;
    if (element.checked) {
        $(checkedId).checked = true;
    } else {
        $(checkedId).checked = false;
    }
}

function checkValidPaymentAccount() {
    var original = $('allocate_amount_original');
    if ((!original.value) || (original.value == 0)) {
        alert("Please Enter A Total Amount And Click On Apply Payment");
        $('payment_on_account').checked = false;
    }
}


function setCommenttoGrid(invoiceId) {
    var checkedId = 'dispute_popupinvoices_comments_' + invoiceId;
    var checkedIdExists = $(checkedId);
    if (typeof(checkedIdExists) != 'undefined' && checkedIdExists != null) {
        var assignValue = $(checkedId).value;
        if (assignValue !== '') {
            $('dispute_invoices_comments_' + invoiceId).value = '';
            $('dispute_invoices_comments_' + invoiceId).value += assignValue;
        } else {
            $('dispute_invoices_comments_' + invoiceId).value = '';
        }
    }
}