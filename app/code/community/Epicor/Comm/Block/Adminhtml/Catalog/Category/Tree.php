<?php

/**
 * Categories tree block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Epicor
 */
class  Epicor_Comm_Block_Adminhtml_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get category name
     *
     * @param Varien_Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result='';
        $erpModel=Mage::getModel('catalog/category')->load($node->getId());
        if ($erpModel->getErpCode())
            $result.=$erpModel->getErpCode().': ';
       
        $result.= parent::buildNodeName($node);
        return $result;
    }
}


