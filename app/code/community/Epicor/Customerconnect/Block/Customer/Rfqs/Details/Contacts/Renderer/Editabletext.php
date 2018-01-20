<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Contacts_Renderer_Editabletext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {

        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);

        $key = Mage::registry('rfq_new') ? 'new' : 'existing';

        if (Mage::registry('rfqs_editable')) {
            $html = '<input type="text" name="contacts[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="contacts_' . $index . '"/>';
        } else {
            $html = $value;
        }

        return $html;
    }

}
