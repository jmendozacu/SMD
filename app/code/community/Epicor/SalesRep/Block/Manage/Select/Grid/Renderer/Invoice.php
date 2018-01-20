<?php

/**
 * Column Renderer for Sales ERP Account Select Grid Address
 *
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Invoice extends Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Address
{

    public function render(Varien_Object $row)
    {
        return parent::renderAddress($row, 'invoice');
    }

}
