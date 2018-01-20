
if (!window.Epicor)
    var Epicor = new Object();

/**
 * Quick Search form client model
 */
Epicor.ewaProduct = Class.create();
Epicor.ewaProduct.prototype = {
    loaded: false,
    live: true,
    debugData: {data: '{"width":1024,"height":768}'},
    ewaLoadTimeout: 60000,
    badUrlTimeout: 5000,
    badUrlTimer: null,
    errorTimer: null,
    prefix: '',
    initialize: function() {
        if (Mage.Cookies.path != "/") {
            ewaProduct.prefix = Mage.Cookies.path;
        }
        if (!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        this.warningMessage = Translator.translate('Warning: Your changes will be lost if you close. Click OK if you are you sure you want to close without saving.');
    },
    submit: function (data, returnurl) {
        var ewaUrl = this.buildEwaUrl(ewaProduct.prefix + '/comm/configurator/loadewa', data);
        this.buildWindow(ewaUrl, returnurl);
    },
    edit: function (data, returnurl) {
        var ewaUrl = this.buildEwaUrl(ewaProduct.prefix + '/comm/configurator/editewa', data);
        this.buildWindow(ewaUrl, returnurl);
    },
    buildEwaUrl: function (ewaUrl, data) {
    
        for (var key in data) {
            if (data[key] != undefined && data[key] != '' && data[key] != 0) {
                ewaUrl += '/' + key + '/' + data[key];
            }
        }
        
        var address = this.getAddress();
        if (address) {
            ewaUrl += '/address/' + encodeURIComponent(Object.toJSON(address));
        }
        return ewaUrl;
    },
    buildWindow: function(url, returnurl, error) {
        
        if(error === undefined){
            error = false;
        }
        ewaProduct.loaded = false;
        // create EWA Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = "ewaWrapper";

        // create Close link
        var closeBtn = new Element('a');
        if(error) {
            closeBtn.href = 'ewaProduct.closepopup("ewaWrapper")';
        } else {
            closeBtn.href = 'javascript:if(confirm(ewaProduct.warningMessage)){ewaProduct.closepopup("ewaWrapper");}';
        }
        closeBtn.update('Close');
        wrappingDiv.insert(closeBtn);

        // create Loading Image
        var myImg = new Element('img');
        myImg.src = ewaProduct.prefix + '/skin/adminhtml/default/default/images/ajax-loader-tr.gif';
        var myLoadingText = new Element('p');
        myLoadingText.insert('Loading...');

        var myLoader = new Element('div');
        myLoader.className = 'loading';
        myLoader.insert(myImg);
        myLoader.insert(myLoadingText);

        wrappingDiv.insert(myLoader);

        // create iFrame
        var myIframe = new Element('iframe');

        if (returnurl) {
            url = url + '/return/' + returnurl;
        }

        myIframe.src = url;
        if(!error) {
            myIframe.writeAttribute('onload', 'clearTimeout(ewaProduct.badUrlTimer);clearTimeout(ewaProduct.errorTimer);ewaProduct.errorTimer = setTimeout("ewaProduct.autoreveal()",' + this.ewaLoadTimeout + ');');
            this.badUrlTimer = setTimeout("ewaProduct.badUrl()", this.badUrlTimeout);
        }
        myIframe.writeAttribute('scrolling', 'no');

        wrappingDiv.insert(myIframe)

        if ($('window-overlay')) {
            $('window-overlay').setStyle({
                'display': 'block',
                'position': 'fixed'
            });
        }
        // show EWA Wrapper
        $(document.body).insert(wrappingDiv);
    },
    badUrl: function() {
        if (!this.loaded) {
            if (this.live) {
                this.closepopup('ewaWrapper');
                this.buildWindow(this.prefix + '/comm/configurator/badurl', undefined, true);
            } else {
                this.onMessage(this.debugData);
            }
        }
    },
    redirect: function(url) {
        this.closepopup('ewaWrapper');

        if (url == '')
            location.reload();
        else
            location.replace(url);
    },
    closepopup: function(popup) {
        if ($(popup)) {
            $(popup).remove();
        }
        clearTimeout(ewaProduct.badUrlTimer);
        clearTimeout(ewaProduct.errorTimer);
        if ($('window-overlay'))
            $('window-overlay').setStyle({
                'display': 'none',
                'position': 'absolute'
            });
    },
    autoreveal: function() {
        if (!this.loaded) {
            if (this.live) {
                this.closepopup('ewaWrapper');
                this.buildWindow(this.prefix + '/comm/configurator/error', undefined, true);
            } else if (!this.live) {
                this.onMessage(this.debugData);
            }
        }
    },
    onMessage: function(event) {
        clearTimeout(ewaProduct.badUrlTimer);
        clearTimeout(ewaProduct.errorTimer);
        ewaProduct.loaded = true;
        var padding = 10;
        var border = 2;
        var closelink = 20;


        var message = JSON.parse(event.data);
        var ewaWidth = message.width * 1;
        var ewaHeight = message.height * 1;

        var maxWidth = $(document.viewport).getWidth() - 40;
        var maxHeight = $(document.viewport).getHeight() - 40;

        var wrapperWidth = Math.min(maxWidth - padding - border, ewaWidth);
        var wrapperHeight = Math.min(maxHeight - padding - border, ewaHeight + closelink);

        var iframeWidth = Math.min(maxWidth - padding - border, ewaWidth);
        var iframeHeight = Math.min(maxHeight - padding - border - closelink, ewaHeight);

        console.log(wrapperWidth + ' x ' + wrapperHeight);
        console.log(iframeWidth + ' x ' + iframeHeight);

        $('ewaWrapper').setStyle({
            'height': wrapperHeight + 'px',
            'width': wrapperWidth + 'px',
            'marginTop': '-' + (wrapperHeight / 2) + 'px',
            'marginLeft': '-' + (wrapperWidth / 2) + 'px'
        });
        $('ewaWrapper').select('iframe')[0].setStyle({
            'height': iframeHeight + 'px',
            'width': iframeWidth + 'px',
            'display': 'block',
            'marginLeft': '0'
        });
        $('ewaWrapper').select('.loading')[0].setStyle({
            'display': 'none'
        });

    },
    getAddress: function() {
        var content = $('delivery-address-content');
        if (content != '' && content != null) {
            var serialized = Form.serialize(content, true);
            var address = {};
            for (var index in serialized){
                var newIndex = index.replace('delivery_address[', '').replace(']','');
                if(newIndex != 'old_data'){
                    address[newIndex] = serialized[index];
                }
            }
            var contactName = '';
            $$('#rfq_contacts_table .contacts_row').each(function(e){
                if(e.visible()){
                    var nameColumn = e.down('.last');
                    var select = nameColumn.down('select ');
                    if (select) {
                        contactName = select[select.selectedIndex].innerHTML;
                    } else {
                        contactName = e.down('.last').innerHTML.trim();
                    }
                    throw $break;
                }
            });
            address.contact_name = contactName;
            return address;
        } else {
            return false;
        }
    }
};
var ewaProduct = null;

document.observe('dom:loaded', function() {
    ewaProduct = new Epicor.ewaProduct();
    if (window.attachEvent)
        window.attachEvent('onmessage', ewaProduct.onMessage);
    else if (window.addEventListener)
        window.addEventListener('message', ewaProduct.onMessage, false);
});
