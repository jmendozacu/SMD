<?php

/**
 * Payment method renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Cardtype_Renderer_Paymentmethod extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return $this->_getPaymentMethod($row->getData($this->getColumn()->getIndex()));
    }
    
    public function _getPaymentMethod($code)
    {
        if (!Mage::registry('payment_method_cache')) {
            $payments = Mage::getSingleton('payment/config')->getActiveMethods();

            $methods = array(array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

            $methods['all'] = array(
                'label' => 'All Payment Methods',
                'value' => 'all',
            );

            foreach ($payments as $paymentCode => $paymentModel) {
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode,
                );
            }
            
            Mage::register('payment_method_cache', $methods, true);
        } else {
            $methods = Mage::registry('payment_method_cache');
        }

        return isset($methods[$code]) ? $methods[$code]['label'] : $code;
    }
    

}
