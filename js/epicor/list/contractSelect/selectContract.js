/**
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */


if(typeof Epicor_ProductSelector == 'undefined') {
    var Epicor_ProductSelector = {};
}
Epicor_ProductSelector.product = Class.create();
Epicor_ProductSelector.product.prototype = {
    target: null,
    wrapperId: "selectContractProductWrapperWindow", //selectProductWrapperWindow
    initialize: function(){
        if(!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');

        if(!$('loading-mask'))
            $(document.body).insert('<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>');    
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
        $$('select#_accountwebsite_id option').each(function(o){				// id = messages1
                if(o.selected == true){
                   website = o.value;
                }  
          })
        var productGridUrl = ahref; 
        this.ajaxRequest = new Ajax.Request(productGridUrl,{
            method: 'post',
            parameters: { field_id:newtarget, website:website},
            onComplete: function(request){
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request){
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                $$('#selectContractProductWrapperWindow span.separator:last').each(function(a){
                    a.innerHTML = '';
                    a.insert('</br>');  
                })
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
                'marginTop': '-'+(height/2)+'px'
            }); 
        }
    }
};

var productSelector = 'test';
var addressSelector = 'test';
document.observe('dom:loaded', function() { 
    productSelector = new Epicor_ProductSelector.product();
    Event.observe(window, "resize", function() { productSelector.updateWrapper();} );
    addressSelector = new Epicor_AddressSelector.address();
    Event.observe(window, "resize", function() { addressSelector.updateWrapper();} );    
});



if(typeof Epicor_AddressSelector == 'undefined') {
    var Epicor_AddressSelector = {};
}
Epicor_AddressSelector.address = Class.create();
Epicor_AddressSelector.address.prototype = {
    target: null,
    wrapperId: "selectContractLocationWrapperWindow", //selectProductWrapperWindow
    initialize: function(){
        if(!$('window-overlay'))
            $(document.body).insert('<div id="window-overlay" class="window-overlay" style="display:none;"></div>');

        if(!$('loading-mask'))
            $(document.body).insert('<div id="loading-mask" style="display:none;"><p class="loader" id="loading_mask_loader">Please wait...</p></div>');    
        
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
        var addressGridUrl = ahref; 
        this.ajaxRequest = new Ajax.Request(addressGridUrl,{
            method: 'post',
            parameters: { field_id:newtarget, website:website},
            onComplete: function(request){
                this.ajaxRequest = false;
            }.bind(this),
            onSuccess: function(request){
                $('loading-mask').hide();
                $(this.wrapperId).insert(request.responseText);
                 $$('#selectContractLocationWrapperWindow span.separator:last').each(function(a){
                    a.innerHTML = '';
                    a.insert('</br>');  
                })
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
                'marginTop': '-'+(height/2)+'px'
            }); 
        }
    }
};

//close the popup window on key press(ESC)
window.onkeypress = function (event) {
    var productWindowExist =document.getElementById("selectContractProductWrapperWindow");
    var locationWindowExist=document.getElementById("selectContractLocationWrapperWindow");
    if (event.which == 13 || event.keyCode == 13) {
        return false;
    } else if (event.which == 27 || event.keyCode == 27){
        if (typeof(productWindowExist) != 'undefined' && productWindowExist != null){
            $('selectContractProductWrapperWindow').remove();
            $('window-overlay').hide();
        }
        if (typeof(locationWindowExist) != 'undefined' && locationWindowExist != null){
            $('selectContractLocationWrapperWindow').remove();
            $('window-overlay').hide();
        }        
    }
    return true;
};



