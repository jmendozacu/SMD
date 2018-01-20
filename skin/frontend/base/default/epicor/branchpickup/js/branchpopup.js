if(typeof Epicor_LocationSearchSelector == 'undefined') {
    var Epicor_LocationSearchSelector = {};
}
Epicor_LocationSearchSelector.pickupsearch = Class.create();
Epicor_LocationSearchSelector.pickupsearch.prototype = {
    target: null,
    wrapperId: "selectLocationSearchWrapperWindow", //selectProductWrapperWindow
    initialize: function(){
        if(!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');
    },
    openpopup:function(newtarget,ahref) {

        this.target = newtarget;
        if($(this.wrapperId))
            $(this.wrapperId).remove();
        // create Popup Wrapper
        var wrappingDiv = new Element('div');
        wrappingDiv.id = this.wrapperId;
                             
        $('loading-mask').show();        
        $(document.body).insert(wrappingDiv);
        
        $(this.wrapperId).hide();
        var website = 0;
        $$('select#_accountwebsite_id option').each(function(o){                // id = messages1
                if(o.selected == true){
                   website = o.value;
                }  
          })
        var productGridUrl = ahref; 
        $('branchpickupshipping').checked = true;
        this.ajaxRequest = new Ajax.Request(productGridUrl,{
            method: 'post',
            parameters: { field_id:newtarget, website:website},
            onComplete: function(request){
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request){
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                $(this.wrapperId).show();
                $('window-overlay').show();
                
                this.updateWrapper();
                
            }.bind(this),
            onFailure: function(request){
                alert('Error occured loading products grid');
                this.closepopup();
            }.bind(this),
            onException: function(request,e){
                alert('Error occured loading products grid');
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
            var height = 20;
        
            $$('#'+this.wrapperId +' > *').each(function(item){
                height += item.getHeight();
            });
        
            if(height > ($(document.viewport).getHeight()-40))
                height = $(document.viewport).getHeight()-40;
        
            if(height < 35)
                height = 35;
            
            $(this.wrapperId).setStyle({
                'height':height+'px',
                'marginTop': '-'+(height/2)+'px',
                'width': "70%"
            }); 
        }
    }
};

var LocationSearchSelector = 'test';
document.observe('dom:loaded', function() { 
    LocationSearchSelector = new Epicor_LocationSearchSelector.pickupsearch();
    Event.observe(window, "resize", function() { LocationSearchSelector.updateWrapper();} );
});


//close the popup window on key press(ESC)
window.onkeypress = function (event) {
    var branchWindowExist = $("selectLocationSearchWrapperWindow");
    if (!branchWindowExist) {
        return true;
    } 
    if (event.which == 13 || event.keyCode == 13) {
        return false;
    } else if (event.which == 27 || event.keyCode == 27){
        if (typeof(branchWindowExist) != 'undefined' && branchWindowExist != null){
            $('selectLocationSearchWrapperWindow').remove();
            $('window-overlay').hide();
        }
    }
    return true;
};