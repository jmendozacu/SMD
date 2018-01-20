/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
document.observe('dom:loaded', function() {
    if ($('media_gallery_content_grid')) {
        $('media_gallery_content_grid').select('div.buttons')[0].insert({after: '<button id="" title="Sync Images" type="button" class="scalable" onclick="syncFtpImages()" style=""><span><span><span>Sync Images</span></span></span></button>'}
        );
    }
});

function syncFtpImages()
{
    new Ajax.Request(syncFtpImageUrl, {
            method:     'get',
            onSuccess: function(transport){
                if (transport.responseText=='true'){
                    alert("Ftp images Sync Sucessful");
                   window.location = syncFtpSuccessUrl;
                   
                }
                else
                {
                    alert("Image Sync Failed: \n".transport.responseText);
                }
            }
        });
}