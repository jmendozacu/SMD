function populateMasqueradeSelectParent(value) {
    var rowId = value.trim();
    $$('select#masquerade_as option').each(function(o) {
        console.log(o.value); 
         if (o.value == rowId) {
              o.selected = true;
         }
      });
    $('window-overlay').hide();
    event.stop();
    return false;
}