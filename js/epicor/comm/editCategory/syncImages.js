/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function syncFtpImages()
{
    new Ajax.Request(syncFtpImageUrl, {
        method: 'get',
        onSuccess: function (transport) {
            if (transport.responseText == 'true') {
                alert("Ftp images Sync Sucessful");
                var concatSymbol = syncFtpSuccessUrl.indexOf('?') > -1 ? '&' : '?';
                categoryReset(syncFtpSuccessUrl + concatSymbol + 'isAjax=true', true);
            } else {
                alert("Image Sync Failed: \n".transport.responseText);
            }
        }
    });
}