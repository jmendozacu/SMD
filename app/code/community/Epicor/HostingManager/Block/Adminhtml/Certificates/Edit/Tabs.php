<?php

/**
 * Certificates edit tabs block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('certificate_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Certificate');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('details', array(
            'label' => 'Certificate Details',
            'title' => 'Certificate Details',
            'content' => $this->getLayout()->createBlock('hostingmanager/adminhtml_certificates_edit_tab_details')->initForm()->toHtml(),
        ));

        $this->addTab('csr', array(
            'label' => 'Generate CSR',
            'title' => 'Generate CSR',
            'content' => $this->getLayout()->createBlock('hostingmanager/adminhtml_certificates_edit_tab_csr')->initForm()->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
