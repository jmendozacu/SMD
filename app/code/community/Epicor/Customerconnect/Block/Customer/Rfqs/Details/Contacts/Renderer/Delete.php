<?php

class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Contacts_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        if (Mage::registry('rfqs_editable')) {
            $html = '<input type="checkbox" class="contacts_delete" name="contacts[' . $key . '][' . $row->getUniqueId() . '][delete]" />';
        } else {
            $html = '';
        }

        $oldDetails = array(
            'name' => $row->getName(),
            'number' => $row->getNumber(),
        );

        $html .= '<input type="hidden" name="contacts[' . $key . '][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';
        $html .= '<input type="hidden" name="contacts[' . $key . '][' . $row->getUniqueId() . '][name]" value="' . $oldDetails['name'] . '" /> ';
        $html .= '<input type="hidden" name="contacts[' . $key . '][' . $row->getUniqueId() . '][number]" value="' . $oldDetails['number'] . '" /> ';
        return $html;
    }

}
