var rule_conditions_fieldset;

getProductChooser = function (url, chooserProduct, limitation) {

    $$('.chooser-container').each(function($elem) {
        $elem.innerHTML = '';
    });
    
    new Ajax.Request(
    url, {
        method: "post",
        parameters: {
            'selected[]': $(chooserProduct).value.replace(', ', ',').split(',')
        },
        onSuccess: function (b) {
            rule_conditions_fieldset = new VarienRulesForm(chooserProduct, limitation);
            $('chooser-container-'+chooserProduct).update(b.responseText);
        }
    })
};

var VarienRulesForm = new Class.create();
VarienRulesForm.prototype = {
    initialize: function (a, limitation) {
        this.newChildUrl = a;
        this.shownElement = null;
        this.limitation = limitation;
        this.updateElement = $(a);
        this.chooserSelectedItems = $H({});
        
        var values = this.updateElement.value.split(','), s = '';
        for (i=0; i<values.length; i++) {
            s = values[i].strip();
            if (s!='') {
               this.chooserSelectedItems.set(s,1);
            }
        }
    },
    
    chooserGridInit: function (grid) {},
    chooserGridRowInit: function (grid, row) {
        if (!grid.reloadParams) {
            grid.reloadParams = {'selected[]':this.chooserSelectedItems.keys()};
        }
    },
    chooserGridRowClick: function (grid, event) {
        var trElement = Event.findElement(event, 'tr');
        var isInput = Event.element(event).tagName == 'INPUT';
        if (trElement) {
            var checkbox = Element.select(trElement, 'input');
            if (checkbox[0]) {
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);

            }
        }
    },
    chooserGridCheckboxCheck: function (grid, element, checked) {
        if (checked) {
            if (!element.up('th')) {
                if(this.limitation != undefined && this.limitation == true)
                {
                    this.chooserSelectedItems = $H({});
                    $$('#chooser-container-'+this.newChildUrl+' input.checkbox[value!='+element.value+']').each(function(checkbox) {
                        checkbox.checked = false;
                    });
                }
                this.chooserSelectedItems.set(element.value,1);
            }
        } else {
            this.chooserSelectedItems.unset(element.value);
        }
        grid.reloadParams = {'selected[]':this.chooserSelectedItems.keys()};
        
        this.updateElement.value = this.chooserSelectedItems.keys().join(',');
    }
};