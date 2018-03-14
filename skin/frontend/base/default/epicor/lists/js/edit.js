var product_opened = 0;
var account_opened = 0;
var customer_opened = 0;

document.observe('dom:loaded', function () {
    var listForm = new VarienForm('list_form', true);


    if ($('update_list')) {
        $('update_list').addEventListener("click", function (e) {
            if (listForm.validator && listForm.validator.validate())
            {
                e.target.form.submit();
            }
            else {
                return;
            }
        });
    }
    if ($('create_list')) {

        $('create_list').addEventListener("click", function (e) {
            if (listForm.validator && listForm.validator.validate()) {
                e.target.form.submit();
            } else {
                primary_details('primary_details');
                return;
            }
        });
    }
    
    if ($('type')) {
        $('type').addEventListener("change", function (e) {
            checkSettingTypes()
        });
    }    
    
});
function primary_details(lid) {
    if (lid)
        setCurrent(lid);
    $('primary_detail_content').show();
    $('product_grid').hide();
    $('customer_grid').hide();
}

function setCurrent(lid) {
    tablinks = document.getElementsByClassName("current");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("current", "");
    }
    var d = document.getElementById(lid);
    d.className += " current";
}

function startLoading() {
    $('please-wait').show();
}
function list_product(lid) {
    if (lid)
        setCurrent(lid);
    $('primary_detail_content').hide();
    $('customer_grid').hide();
    if (product_opened != 1) {
        product_opened = 1;
        new Ajax.Request(product_url, {
            method: 'POST',
            onLoading: startLoading,
            onComplete: function (transport) {
                $('please-wait').hide();
                $('product_grid').show();
                div = $('product_grid');
                div.update(transport.responseText);

            }
        });
    }
    else {
        $('product_grid').show();
    }
}    
function masquerade_accounts(lid) {
    if (lid)
        setCurrent(lid);
    $('primary_detail_content').hide();
    $('customer_grid').hide();
    $('product_grid').hide();
    if (account_opened != 1) {
        account_opened = 1;
        new Ajax.Request(account_url, {
            method: 'POST',
            onLoading: startLoading,
            onComplete: function (transport) {
             //   $('please-wait').hide();
                div = $('masquerade_accounts_grid');
                div.update(transport.responseText);
                $('masquerade_accounts_grid').show();                

            }
        });
    }
    else {
        $('masquerade_accounts_grid').show();
    }
}
function list_customer(lid) {
    if (lid)
        setCurrent(lid);
    $('primary_detail_content').hide();
    $('product_grid').hide();
    if (customer_opened != 1) {
        customer_opened = 1;
        new Ajax.Request(customer_url, {
            method: 'POST',
            onLoading: startLoading,
            onComplete: function (transport) {
                $('please-wait').hide();
                $('customer_grid').show();
                div = $('customer_grid');
                div.update(transport.responseText);

            }

        });
    }
    else {
        $('customer_grid').show();
    }
}

function checkSettingTypes() {
    var chosenType = $('type').value;
    var supportedSettings = $('supported_settings_' + chosenType).value.split('');
    var allSettings = $('supported_settings_all').value.split('');
    for (i = 0; i < allSettings.length; i++) {
        if(supportedSettings.indexOf(allSettings[i]) == -1) {
            $('settings_' + allSettings[i]).checked = false;
            $('settings_' + allSettings[i]).parentNode.hide();
        } else {
            $('settings_' + allSettings[i]).parentNode.show();
        }
    }
}
