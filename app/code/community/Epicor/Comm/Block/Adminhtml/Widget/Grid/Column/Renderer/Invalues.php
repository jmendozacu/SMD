<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Country column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Invalues
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $values = $this->getColumn()->getValues();
        $options = array_values($this->getColumn()->getOptions());
        
        if (!is_array($values)) {
            $values = array($values);
        }
        
        if (!is_array($options)) {
            $options = array('', $options);
        } elseif (count($options) == 0) {
            $options = array('', '');
        } elseif (count($options) == 1) {
            $options = array('', $options[0]);
        }
        
        $option = in_array($value, $values) ? 1 : 0;
        return $options[$option];
    }
}
