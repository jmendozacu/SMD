    Validation.add('validate-trackurl','Please enter a valid Tracking URL.',function(v){
        var regex = /^((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_{}]*)#?(?:[\w]*))?)$/i.test(v);
        if((v.indexOf('{{TNUM}}') <= 0) && (v.length > 0) && (regex)){
            alert("URL should contain this value {{TNUM}} (otherwise tracking replacement wont work)");
            return false;
        }
        return Validation.get('IsEmpty').test(v) || regex
    });

if(typeof Epicor_Trackurl == 'undefined') {
    var Epicor_Trackurl = {};
}
Epicor_Trackurl.shippingmethodtrack = Class.create();
Epicor_Trackurl.shippingmethodtrack.prototype = {
    target: null,
    wrapperId: "selectShippingMethodTrackWrapperWindow",//"selectShippingMethodTrackWrapperWindow",
    initialize: function(){
        if(!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
        
    },
    openpopup:function(newtarget,trackshipurl) {
        
        this.target = newtarget;
        if($(this.wrapperId))
            $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
                             
            
        $(document.body).insert(wrappingDiv);
        
        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o){			
                if(o.selected == true){
                   website = o.value;
                }  
          })
        var gridUrl = $('popupshipurl').value;
        var editTrackPage = $('edittrack');
        if (typeof(editTrackPage) != 'undefined' && editTrackPage != null) {   
            var gettrackUrl = $('tracking_url').value;  
            if(!gettrackUrl) {
                alert("Please enter a URL.");
                return false;  
            }
            var validateTrack = ValidTrackURL(gettrackUrl);
            if(!validateTrack) {
                return false;
            }
          var trackshipurlId = gettrackUrl;  
        } else {
          var trackshipurlId = $('trackshipurl_'+trackshipurl).value;  
        }
        
        this.ajaxRequest = new Ajax.Request(gridUrl,{
            method: 'post',
            parameters: { field_id:newtarget, website:website},
            onComplete: function(request){
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request){
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                var textbox = $('entertrackingurl');
                textbox.value =trackshipurlId;
                $(this.wrapperId).show();
                $('window-overlay').show();
                this.updateWrapper();
                
            }.bind(this),
            onFailure: function(request){
                alert('Error occured loading Sales Rep Accounts grid');
                this.closepopup();
            }.bind(this),
            onException: function(request,e){
                alert('Error occured loading Sales Rep Accounts grid');
                this.closepopup();
            }.bind(this)
        });
        
    },
    closepopup:function() {
        $(this.wrapperId).remove();
        $('window-overlay').hide();
    },    
    updateWrapper: function() {
        if($(this.wrapperId)) {
            var height = 50;
        
            $$('#'+this.wrapperId +' > *').each(function(item){
                height += item.getHeight();
            });
        
            if(height > ($(document.viewport).getHeight()-40))
                height = $(document.viewport).getHeight()-40;
        
            if(height < 35)
                height = 35;
            
            $(this.wrapperId).setStyle({
                'height':height+'px',
                'marginTop': '-'+(height/2)+'px'
            }); 
        }
    },
};

var shippingmethodtrack = 'test';
document.observe('dom:loaded', function() { 
    shippingmethodtrack = new Epicor_Trackurl.shippingmethodtrack();
    Event.observe(window, "resize", function() { shippingmethodtrack.updateWrapper();} );
});

function ValidTrackURL(str) {
    var regex = /^((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_{}]*)#?(?:[\w]*))?)$/;
    if((str.indexOf('{{TNUM}}') <= 0) && (str.length > 0) && (regex)){
        alert("URL should contain this value {{TNUM}} (otherwise tracking replacement wont work)");
        return false;
    }    
    if(!regex .test(str)) {
        alert("Please enter valid URL.");
        return false;
    } else {
        return true;
   }
}