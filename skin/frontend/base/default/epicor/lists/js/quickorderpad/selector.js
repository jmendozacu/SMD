if (typeof Epicor_Lists == 'undefined') {
    var Epicor_Lists = {};
}

function populateListsSelect(row, event) {
    var trElement = event.findElement('tr');
    window.parent.updateFieldListValue(trElement.readAttribute('title'));
}

function listsSearchClosePopup() {
    window.parent.document.getElementById('window-overlay').hide();
    window.parent.document.getElementById('search_iframe').remove();
}
