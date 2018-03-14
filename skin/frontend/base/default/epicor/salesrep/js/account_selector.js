/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (typeof Solarsoft == 'undefined') {
    var Solarsoft = {};
}

Solarsoft.accountSelector = Class.create();
Solarsoft.accountSelector.prototype = {
    type: null,
    target: null,
    formkey: null,
    wrapperId: 'selectErpAccountWrapperWindow',
    initialize: function () {
        if (!$('window-overlay')) {
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        }
        this.formkey = $('form_key').value; 

    },
    openpopup: function (target) {

        this.target = target;
        if ($(this.wrapperId)) {
            $(this.wrapperId).remove();
        }

        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
        $(document.body).insert(wrappingDiv);
        $(this.wrapperId).hide();

        var website = 0;
        $$('select#_accountwebsite_id option').each(function (o) {				// id = messages1
            if (o.selected == true) {
                website = o.value;
            }
        })
        
        this.type = $(target).value;

        var gridUrl = $(target + '_' + this.type + '_url').value;
        this.ajaxRequest = new Ajax.Request(gridUrl, {
            method: 'post',
            parameters: {field_id: target, website: website, type: this.type, form_key:this.formkey},
            onComplete: function (request) {
      //          this.ajaxRequest = false;              
            }.bind(this),
            onSuccess: function (request) {
                $(this.wrapperId).innerHTML = '';
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();               
                $('window-overlay').show();
                this.updateWrapper();
                $$("#selectErpAccountWrapperWindow #erp_account_grid_table tbody tr").invoke('observe', 'click', function(a) {
                    $('erpaccount_popup_label').innerHTML = this.down('td.column-name').innerHTML.trim();
                    $('salesrep_erpaccount').value = this.down('td.column-account-number').innerHTML.trim();
                    $('erp_account_selected').innerHTML = "Erp Account Selected: " + this.down('td.column-name').innerHTML.trim();
                    $('erp_account_selected').show();
                    $('selectErpAccountWrapperWindow').remove();
                    $('window-overlay').hide();
                });
            }.bind(this),
            onFailure: function (request) {
                alert('Error occured loading accounts grid');
                this.closepopup();
            }.bind(this),
            onException: function (request, e) {
                alert('Error occured loading accounts grid');
                this.closepopup();
            }.bind(this)     
        });

    },
    closepopup: function () {
        $(this.wrapperId).remove();
        $('window-overlay').hide();
    },
    selectAccount: function (grid, event) {
        if (typeof event != 'undefined') {
            var row = Event.findElement(event, 'tr');
            var account_id = row.title;
            var account_name = row.select('td.return-label').length > 0 ? row.select('td.return-label')[0].innerHTML : row.select('td.last')[0].innerHTML;
            $(this.target + '_account_id_' + this.type).value = account_id;
            $(this.target + '_label').innerHTML = account_name;
            $(this.target + '_' + this.type + '_label').value = account_name;
            if(this.type != 'salesrep'){
                var data = row.down('.rowdata').value;
                $(this.target + '_account_id_' + this.type).fire('epicor:account_id_change', {data: data});
            }
            this.closepopup();
        }
    },
    switchType: function (target, type) {
        var typeFound = false;

        $$('.type_field').each(function (e) {
            if (e.readAttribute('id') == target + '_account_id_' + type) {
                typeFound = true;
            }
        });

        if (!typeFound) {
            $('ecc_account_selector').hide();
        } else {
            var accountLabel = $(target + '_' + type + '_label').value;

            if (accountLabel == '') {
                accountLabel = $(target + '_no_account').value;
            }

            $(target + '_label').innerHTML = accountLabel;
            $('ecc_account_selector').show();
        }


    },
    removeAccount: function (target) {
        this.type = $(target).value;
        $(target + '_account_id_' + this.type).value = '';
        $(target + '_label').innerHTML = $(target + '_no_account').value;
        $(target + '_account_id_' + this.type).fire('epicor:account_id_change');
    },
    updateWrapper: function () {
        if ($(this.wrapperId)) {
            var height = 20;
            height += $(this.wrapperId).getHeight();
            
//            $$('#' + this.wrapperId + ' > *').each(function (item) {
//                height += item.getHeight();
//            });           

            if (height > ($(document.viewport).getHeight() - 40))
                height = $(document.viewport).getHeight() - 40;

            if (height < 35)
                height = 35;

            $(this.wrapperId).setStyle({
                'height': height + 'px',
                'marginTop': '-' + (height / 2) + 'px'
            });
        }
    },
}    

var accountSelector = 'test';
document.observe('dom:loaded', function () {
    accountSelector = new Solarsoft.accountSelector();

    Event.observe(window, "resize", function () {
        accountSelector.updateWrapper();
    });
});

window.onkeypress = function (event) {
    if (event.which == 13 || event.keyCode == 13) {
        return false;
    }
    return true;
};