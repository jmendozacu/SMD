

// phrases grid update code 
if (typeof Epicor_Comm == 'undefined') {
    var Epicor_Comm = {};
}
Epicor_Comm.childrenGrid = Class.create();
Epicor_Comm.childrenGrid.prototype = {
    rows: $H({}),
    form: null,
    formWrapper: null,
    table: null,
    ajaxRequest: false,
    parentId: null,
    parentIdField: 'id',
    fieldsMap: {},
    initialize: function (form, table) {
        if ($(form)) {
            this.form = $(form);
            this.url = this.form.select('input[name=post_url]')[0].value;
            this.deleteUrl = this.form.select('input[name=delete_url]')[0].value;
            this.parentId = this.form.select('input[name=' + this.parentIdField + ']')[0].value;
            this.addSubmit = this.form.select('input[name=addSubmit]')[0];
            this.updateSubmit = this.form.select('input[name=updateSubmit]')[0];
            this.formWrapper = this.form.up();
            this.formWrapper.hide();
        }
        this.table = table;
    },
    save: function () {
        var data = {};
        this.form.select('input').forEach(function (input) {
            data[input.name] = input.value;
            if (input.classList.contains('check-empty')) {
                if (input.id) {
                    node = document.getElementById(input.id);
                    adviceEntry = document.getElementById("advice-required-entry-" + input.id);
                    if (adviceEntry) {
                        adviceEntry.parentNode.removeChild(adviceEntry);
                    }
                }
            }
        });
        this.form.select('select').forEach(function (select) {
            data[select.name] = select.value;
        });
        data[this.parentIdField] = this.parentId;
        if (this.ajaxRequest) {
            alert('Form already submitted');
        } else if (this.validate()) {
            this.ajaxRequest = true;
            new Ajax.Request(this.url, {
                method: 'post',
                parameters: data,
                requestHeaders: {Accept: 'application/json'},
                onComplete: function (request) {
                    this.ajaxRequest = false;
                }.bind(this),
                onSuccess: function (request) {
                    var json = request.responseText.evalJSON(true);
                    this.insertMessage(json.message, json.type);
                    this.close();
                    eval(this.table + 'JsObject').reload();
                }.bind(this),
                onFailure: function (request) {
                    alert('Error');
                }.bind(this),
                onException: function (request, e) {
                    alert(e);
                }.bind(this)
            });
        }
        return false;
    },
    rowEdit: function (row, id) {
        var rowData = row.up('tr').select('input[name=rowData[]]')[0].value.evalJSON();
        if (rowData) {
            for (var key in rowData) {
                if (this.fieldsMap.hasOwnProperty(key)) {
                    var field = this.fieldsMap[key];
                } else {
                    var field = key;
                }
                if (this.form.select('input[name=' + field + ']').length > 0) {
                    this.form.select('input[name=' + field + ']')[0].value = rowData[key];
                }
            }
            this.updateSubmit.show();
            this.addSubmit.hide();
            this.formWrapper.show();
        } else {
            alert('Data not found for this row');
        }
    },
    rowDelete: function (row) {
        var data = row.up('tr').select('input[name:rowData[]]')[0].value.evalJSON();
        if (data) {
            this.ajaxRequest = true;
            new Ajax.Request(this.deleteUrl, {
                method: 'post',
                parameters: data,
                requestHeaders: {Accept: 'application/json'},
                onComplete: function (request) {
                    this.ajaxRequest = false;
                }.bind(this),
                onSuccess: function (request) {
                    var json = request.responseText.evalJSON(true);
                    this.insertMessage(json.message, json.type);
                    this.close();
                    eval(this.table + 'JsObject').reload();
                }.bind(this),
                onFailure: function (request) {
                    alert('Error');
                }.bind(this),
                onException: function (request, e) {
                    alert(e);
                }.bind(this)
            });

        } else {
            alert('Data not found for this row');
        }
    },
    add: function () {
        this.form.select('input').forEach(function (element) {
            if (element.name != 'updateSubmit' && element.name != 'addSubmit') {
                element.value = '';
            }
        });
        this.updateSubmit.hide();
        this.addSubmit.show();

        this.formWrapper.show();
    },
    close: function () {
        this.formWrapper.hide();
        this.form.select('input.required-entry').forEach(function (element) {
            if (element.name != 'updateSubmit' && element.name != 'addSubmit') {
                element.value = 'default';
            }
        });
    },
    validate: function () {
        var validation = true;
        this.form.select('input').forEach(function (element) {
            if (!Validation.validate(element)) {
                validation = false;
            }
            if (element.classList.contains('check-empty')) {
                if (element.value == "") {
                    if (element.id) {
                        node = document.getElementById(element.id);
                        adviceEntry = document.getElementById("advice-required-entry-" + element.id);
                        if (adviceEntry) {
                            adviceEntry.parentNode.removeChild(adviceEntry);
                        }
                        node.insertAdjacentHTML('afterend', '<div class="validation-advice" id="advice-required-entry-' + element.id + '" style="">This is a required field.</div>');
                        validation = false;
                    }
                }
            }
        });
        return validation;
    },
    insertMessage: function (message, type) {
        if ($('messages').select('ul').length == 0) {
            $('messages').insert('<ul class="messages"></ul>');
        } else {
            $('messages').select('li').forEach(function (element) {
                element.remove()
            });
        }
        $('messages').select('ul')[0].insert('<li class="' + type + '"><span>' + message + '</span></li>');
    }
};

