<?php
/*
 * @category    Epicor
 * @package     Epicor_Customerconnect
 */
class Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_Renderer_Shippingsync extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renders column
     * Adding Custom onchange event to Action Drop down
     * 
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $out = parent::render($row);
        $out = str_replace("varienGridAction.execute(this);","varienGridAction.execute(this);customerConnect.shippingEdit(this);",$out);
        return $out;
    }
}
?>
