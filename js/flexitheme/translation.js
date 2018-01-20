
function updatePhrases(url) {
    $('loading-mask').show(); 
    var form_data = ""; // supply blank form_data
    var ajaxOrNot = true;
    controllerRedirect(url, ajaxOrNot);
} 
function controllerRedirect(url, ajax) { 
    if(!ajax){  
        window.location.replace(url);
        
    }else{ 
        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true'); 
        
        jQuery.ajax({
            url: url, 
      //      type: 'POST',
            async: true,   
            dataType: "json", 
            success: function(data){ 
                    $('loading-mask').hide();  
                    if(data.type == 'success'){
                        if(data.redirect){
                            controllerRedirect(data.redirect);
                        } 
                    }else{
                        if(data.message){
                            showMessage(data.message,data.type); 
                        }
                    }    
            }
        });
    }          
}       
function autoTranslate(form_data, url) {  
    $('loading-mask').show();
    formSubmit(form_data, url);
}
function formSubmit(form_data, url) {  
      
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');     
    jQuery.ajax({
        url: url, 
        type: 'POST',
        async: true,
        data: form_data.serialize(),
        enctype: 'multipart/form-data',
        dataType: "json", 
        success: function(data){             
            if(data.type == 'success'){
                if(data.redirect){
                    controllerRedirect(data.redirect);
                } 
            }else{
                if(data.message){
                    showMessage(data.message,data.type); 
                }
            }    
        }
    }); 
}     

function showMessage(txt, type) {
    var html = '<ul class="messages"><li class="'+type+'-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
} 

// phrases grid update code 
if(typeof Solarsoft_Flexitheme == 'undefined') {
    var Solarsoft_Flexitheme = {};
}
Solarsoft_Flexitheme.translationPhrasesUpdateGrid = Class.create();
Solarsoft_Flexitheme.translationPhrasesUpdateGrid.prototype = {
   
    
    initialize: function(){
        this.gridPhrases   = $H({});        
        
    },
 
    phrasesGridRowInit: function (grid, row) {

        var inputs = $(row).select('input[type="text"]');
 
        if (inputs.length > 0) {
            
            var translatedPhrase = $(row).select('input[name=translated_phrase]')[0];
            
            this.gridObj = grid;
            row.translatedPhraseElement = $(translatedPhrase).readAttribute('translated_phrase');
            row.numberElement  = $(translatedPhrase).readAttribute('rel');                      // save rel value containing index          
  
            Event.observe(translatedPhrase,'keyup',this.phrasesGridRowInputChange.bind(this));
            Event.observe(translatedPhrase,'change',this.phrasesGridRowInputChange.bind(this));
            
        }
    },

    phrasesGridRowInputChange: function (event) {   
        var element = Event.element(event);  
     
        var translated_phrase = element.value;
            var rel = element.readAttribute('rel');       

            if (element){            
                    this.gridPhrases.set(rel,translated_phrase);    
                } 

              this.gridObj.reloadParams = {
                         'gridPhrases' : Object.toJSON(this.gridPhrases)                  
                     };
              $('translated_phrases').value = Object.toJSON(this.gridPhrases);  // add entire grid object to hidden column    
    }

};
var translationPhrasesUpdateGrid = new Solarsoft_Flexitheme.translationPhrasesUpdateGrid();
//document.observe('dom:loaded', function(){  
//    $('flexitheme_update_translation_phrases_list').observe('click', function() {
//        $('loading-mask').show();           
//    });
//    
//});
Solarsoft_Flexitheme.userTranslationGrid = Class.create();
Solarsoft_Flexitheme.userTranslationGrid.prototype = {
    initialize: function(){
        var url = '';
        this.urlandpath = '';
        this.submitpath = 'flexitheme/translation/userTranslation';
        this.url = '';
        this.standard_add_form = '<table id="userTranslationGrid_table_add" class="data" cellspacing="0">'
                               + '<tr class="headings">'
                               + '<th colspan="2">'
                               + '<span>Add Custom Translation Data</span>'
                               + '</th>'
                               + '</tr>' 
                               + '<tr class="headings">'
                               + '<td>' 
                               + '<span>Original Phrase</span>'
                               + '</td>' 
                               + '<td>' 
                               + '<span>Replacement Phrase</span>'
                               + '</td>' 
                               + '</tr>'
                               + '<tr>' 
                               + '<td><input id="userTranslation_string" class="input-text required-entry" type="text" value="" name="user_translation_string"></td>'
                               + '<td><input id="userTranslation_update" class="input-text required-entry" type="text" value="" name="user_translation_string_update"></td>'
                               + '</tr>'   
                               + '</div>'
                               + '</table>'
                               + '<div id="buttons">'
                               + '<button type="submit" id="userTranslationGrid_table_add_submit">submit</button>'
//                               + '<button type="submit" id="userTranslationGrid_table_add_submit" onclick="userTranslationGrid.submit()">submit</button>'
                               + '<button type="button" id="userTranslationGrid_table_add_cancel" onclick="userTranslationGrid.cancel()">cancel</button>'
                               + '</div>'   
                               + '</form>'
                               + '<script type="text/javascript">'
                               + '   var user_translation_add_form = new varienForm("user_translation_add_form");'
                                 '</script>';
    }, 
  addCustomTranslation : function (url, path, key, language_id){
    this.urlandpath = url + path + key; 
    console.log('gets to addcustom translation');
    console.log(language_id);
    if(!$('user_translation_add_form')) { 
              this.userTranslationTableHeader = '<form id="user_translation_add_form" action="'+this.urlandpath+'" method="post">'
                                              + '<input name="form_key" type="hidden" value="'+ key +'"/>'  
                                              + '<input name="language_id" type="hidden" value="'+ language_id +'"/>';  
              userTranslationTable = this.userTranslationTableHeader
                                   + this.standard_add_form;
              $('userTranslationGrid_table').insert({before: userTranslationTable});
    }
    if(!$('user_translation_add_form').visible()) { 
        $('user_translation_add_form').show();
    }
    $('userTranslationGrid_table').hide();
  },
  cancel: function(){
      console.log('gets to cancel function ');
      $('user_translation_add_form').hide();
      $('userTranslationGrid_table').show();
  }
};
var userTranslationGrid = new Solarsoft_Flexitheme.userTranslationGrid();

