<?php

/**
 * Filepermission grid column renderer. renders a file permission  in human readable format
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Filepermission extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render action grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $data = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $log = $helper->urlEncode($helper->encrypt($row->getFilename()));
        $url =  $this->getUrl('*/*/view', array('log' => $log));
        //return $this->getUrl('*/*/view', array('log' => $log));
        if($data['status'] =="notreadable") {
          return  '<span style="color:red">File not readable!</span>';  
        } else  if($data['status'] =="notwritable") {
          return  '<span style="color:red">File not writable!</span>';  
        } else {
          return  "<a  href='".$url."'>View</a>" ;    
        }
       
        
    }

}
