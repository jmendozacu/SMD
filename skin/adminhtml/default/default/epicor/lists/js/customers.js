function customerSelectAll()
{
    $$('#customersGrid_table input[type=checkbox]').each(function (elem) {
        if (elem.checked == false) {
            elem.simulate('click');
        }
    });
}


function customerUnselectAll()
{
    $$('#customersGrid_table input[type=checkbox]').each(function (elem) {
        if (elem.checked) {
            elem.simulate('click');
        }
    });
}
