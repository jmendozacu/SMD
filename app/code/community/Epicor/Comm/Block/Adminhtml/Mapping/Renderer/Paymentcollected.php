<?php

/**
 * ERP Image type list renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Paymentcollected extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
         $paymentOptions = array(
            '' => '',
            'C' => 'C - Collected',
            'A' => 'A - Authorised',
            'D' => 'D - Authorised/Will capture on ship',
            'N' => 'N - Token only '
        );

        $paymentCollected = $row->getPaymentCollected();
        $html = $paymentOptions[$paymentCollected];
        return $html;
    }

}
