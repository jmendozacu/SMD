 document.observe('dom:loaded', function() {
        if (!$('window-overlay')) {
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        }
        if (!$('loading-mask')) {
            $(document.body).insert('<div id="loading-mask" class="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>');
        }
 });   
function selectMasquerade(masquerade_as) {
    var postVal = masquerade_as.getAttribute('href');
    var return_url = document.getElementById('return_url').value;
    var ajax_url = document.getElementById('ajax_url').value;
    var url = decodeURIComponent(ajax_url);
    var returns = document.getElementById('jreturn_url').value;
    $('loading-mask').show();
    this.ajaxRequest = new Ajax.Request(url, {
        method: 'post',
        parameters: {'masquerade_as': postVal,'return_url' :return_url ,'isAjax':'true' },
        onComplete: function(request) {
            this.ajaxRequest = false;
        }.bind(this),
        onSuccess: function(data) {
            var json = data.responseText.evalJSON();
            var theHTML = json.html;
            if (json.type == 'success') {
              $('loading-mask').hide();
              window.location.href = returns;
            }
        }.bind(this),
        onFailure: function(request) {
            alert('Error occured in Ajax Call');
        }.bind(this),
        onException: function(request, e) {
            alert(e);
        }.bind(this)
    });
} 