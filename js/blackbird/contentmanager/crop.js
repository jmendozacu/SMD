var marker_edited;
var position_edited;
var jcrop_api;

PointEditor = function (options) {
	this.options = options;

	this.jwindow = jQuery(window);
	this.jeditor = null;
	this.jeditor_content = null;
	this.jtextarea = null;
	this.jcontextualmenu = null;
	this.mouse_event_owner = null;
	this.dialog_v_padding = 10;
	this.dialog_h_padding = 15;
	this.opened = false;
	this.shipping_code = null;
}

PointEditor.prototype = {
	/**
	 * @private
	 */
	_init: function () {
		var jeditor = this._dialog('point-editor',"<div style=\"width:100%;height:100%;position:relative;\" id=\"point-editor-content\"></div>");
		this.jeditor = jeditor;
		var jdialogbox = jeditor.find('.dialog-box');
		this.jeditor_content = jeditor.find('#point-editor-content');
		this.jeditor_content.css({width: jdialogbox.innerWidth()-this.dialog_h_padding*2, height: jdialogbox.innerHeight()-this.dialog_v_padding*2, border: '0'});
	},

	/**
	 * @private
	 */
	_dialog: function (id, content) {
		var w = this.jwindow.width();
		var h = this.jwindow.height();
		var margin = 50;
		var v_padding = this.dialog_v_padding;
		var h_padding = this.dialog_h_padding;
		var width = w-2*margin;
		var height = h-2*margin;
		var dialog_w = Math.max(width+2*h_padding,350);
		var dialog_h = Math.max(height+2*v_padding,250);
		var top = (h-dialog_h)/2;
		var left = (w-dialog_w)/2;
		var margin_top = (dialog_h-height)/2-v_padding;
		var margin_left = (dialog_w-width)/2-h_padding;
		var jdialog = jQuery("<div style=\"position:fixed;top:0;left:0;width:100%;height:100%;z-index:100;\" id=\""+id+"\">"
			+"<div class=\"dialog-bg\" style=\"position:fixed;top:0;left:0;z-index:100;width:100%;height:100%;background:#000;\"></div>"
			+"<div class=\"dialog-box\" style=\"position:fixed;background:#fff;-moz-box-shadow: #000 0 0 10px;top:"+top+"px;left:"+left+"px;width:"+dialog_w+"px;"
				+"height:"+dialog_h+"px;z-index:200;\"><div style=\"padding:"+v_padding+"px "+h_padding+"px;margin:"+margin_top+"px 0 0 "+margin_left+"px;\">"
			+content+'</div></div></div>');
		jdialog.find('.dialog-bg').click(function(event){
				jdialog.fadeOut(function(){jdialog.hide();});
			})
			.css({
				opacity: '0.7'
			})
		;
		jQuery('body').append(jdialog);
		return jdialog;
	},

	/**
	 * @public
	 */
	save: function () {
            jQuery('#'+this.options.identifier+'_ctdi').val(jQuery('#positionx1').val()+':'+jQuery('#positiony1').val()+':'+jQuery('#positionx2').val()+':'+jQuery('#positiony2').val()+':'+jQuery('#contenttype_crop_'+this.options.identifier+'').width()+':'+jQuery('#contenttype_crop_'+this.options.identifier+'').height());
            jQuery('.contenttype-overlay-crop', jQuery('input#'+this.options.identifier).parent()).fadeIn('slow');
            
            this.opened = false;
            this.jeditor.fadeOut();
	},

	/**
	 * @public
	 */
	open: function (object) {
            
            this.opened = true;
            
		if (this.jeditor==null) this._init();

		this.jeditor.fadeIn();
		this.jeditor_content.html('<div class=\"loading rule-param-wait\">'+this.options.loading_label+'</div>');
                
                jQuery('#point-editor .content-header').remove();
		this.jeditor_content.html(
                        '<div class="content-header">'+
                            '<p class="form-buttons" style="float: left;">'+
                                '<label for="positionx1">x1 </label>'+
                                '<input style="width: 40px; margin-right: 10px;" class="input-text" type="text" id="positionx1" name="x1" value="" />'+
                                '<label for="positionx2"> x2 </label>'+
                                '<input style="width: 40px; margin-right: 10px;" class="input-text" type="text" id="positionx2" name="x2" value="" />'+
                                '<label for="positiony1"> y1 </label>'+
                                '<input style="width: 40px; margin-right: 10px;" class="input-text" type="text" id="positiony1" name="y1" value="" />'+
                                '<label for="positiony2"> y2 </label>'+
                                '<input style="width: 40px; margin-right: 10px;" class="input-text" type="text" id="positiony2" name="y2" value="" />'+
                                '<label for="width"> width </label>'+
                                '<input style="width: 40px; margin-right: 10px;" class="input-text" type="text" id="width" name="width" value="" />'+
                                '<label for="height"> height </label>'+
                                '<input style="width: 40px; margin-right: 50px;" class="input-text" type="text" id="height" name="height" value="" />'+
                            '</p>'+
                            '<p class="form-buttons" style="float: left;">'+
                                '<button type="button" class="button back" onclick="pointeditor[\''+this.options.identifier+'\'].close();"><span><span></span>Cancel</span></button>'+
                                '<button type="button" class="button" onclick="updateCropSelect();"><span><span></span>Update selection</span></button>'+
                                '<button type="button" class="button save" onclick="pointeditor[\''+this.options.identifier+'\'].save();"><span><span></span>Save crop</span></button>'+
                            '</p>'+
                        '</div>'+
                        '<div style="overflow: scroll; height: '+(jQuery('#point-editor-content').height()-35)+'px;">'+
                            '<img onload="pointeditor[\''+this.options.identifier+'\'].jcrop();" src="'+this.options.url_image+'" id="contenttype_crop_'+this.options.identifier+'" />'+
                        '</div>'+
                        '');
                
                

	},
        
        jcrop: function() {
            
                jQuery('#contenttype_crop_'+this.options.identifier).Jcrop(
                    this.options.keep_aspect_ratio == 1 ? { aspectRatio: this.options.crop_w/this.options.crop_h, onSelect: showCoords }: { onSelect: showCoords }
                    ,function(){
                        jcrop_api = this;
                        bindCropEvent();
                    }
                );
                
                var positions = jQuery('#'+this.options.identifier+'_ctdi').val().split(':');
                jcrop_api.setSelect([positions[0], positions[1], positions[2], positions[3]]);
                
        },

	/**
	 * @public
	 */
	close: function () {
		this.opened = false;
		this.jeditor.fadeOut();
	}

}

function showCoords(c)
{
    jQuery('#positionx1').val(parseInt(c.x));
    jQuery('#positiony1').val(parseInt(c.y));
    jQuery('#positionx2').val(parseInt(c.x2));
    jQuery('#positiony2').val(parseInt(c.y2));
    jQuery('#width').val(parseInt(c.w));
    jQuery('#height').val(parseInt(c.h));
}

function bindCropEvent()
{
    jQuery('#positionx1, #positionx2, #positiony1, #positiony2').unbind('blur').blur(function() {
        updateCropSelect();
    });
    jQuery('#width, #height').unbind('blur').blur(function() {
        jQuery('#positionx2').val(parseInt(jQuery('#positionx1').val())+parseInt(jQuery('#width').val()));
        jQuery('#positiony2').val(parseInt(jQuery('#positiony1').val())+parseInt(jQuery('#height').val()));
        //updateCropSelect();
    });
}

function updateCropSelect()
{
    jcrop_api.setSelect([parseInt(jQuery('#positionx1').val()), parseInt(jQuery('#positiony1').val()), parseInt(jQuery('#positionx2').val()), parseInt(jQuery('#positiony2').val())]);
}

