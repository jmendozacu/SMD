
var listBrand;
function initListBrand(form, table){
    listBrand = new Epicor_Comm.childrenGrid;
    listBrand.parentIdField = 'list_id';
    listBrand.fieldsMap = {
        id:     'brand_id'
    };
    listBrand.initialize(form,table);
}
