if (typeof Epicor == 'undefined') {
    var Epicor = {};
}

Epicor.lineContractSelect = Class.create();
Epicor.lineContractSelect.prototype = {
    wrapperId: 'line-contract-select',
    height: 0,
    boxAccountPaddingHeight: 0,
    boxAccountPaddingBottom: 0,
    initialize: function () {
        if (!$('window-overlay')) {
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        }
        if (!$('loading-mask')) {
            $(document.body).insert('<div id="loading-mask" class="loading-mask" style="display:none;"></div>');
        }
        if (!$(this.wrapperId)) {
            $(document.body).insert('<div id="' + this.wrapperId + '" class="' + this.wrapperId + '" style="display:none;"></div>');
        }
    },
    openpopup: function (itemid) {
        $(this.wrapperId).hide();
        $(this.wrapperId).update('');
        // create Popup Wrapper
        $('loading-mask').show();
        
        var url = $('line_contract_select_url').value;
        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
        performAjax(url, 'post', {'itemid': itemid, return_url: window.location.href}, this.showContent);
    },
    showContent: function (request) {
        $('line-contract-select').update(request.responseText);
        $('window-overlay').insert($('line-contract-select'));
        $('line-contract-select').show();
        $('window-overlay').show();
        $('loading-mask').hide();
        lineContractSelect.updateWrapper();
    },
    updateWrapper: function () {
        positionOverlayElement('line-contract-select');
    },
    closepopup: function () {
        $(this.wrapperId).hide();
        $('window-overlay').hide();
    },
};

var lineContractSelect;
 
document.observe('dom:loaded', function () {
    lineContractSelect = new Epicor.lineContractSelect();
    if ($('line_contract_select_url')) {
        Event.observe(window, 'resize', function () {
            lineContractSelect.updateWrapper();
        });
    }
});
