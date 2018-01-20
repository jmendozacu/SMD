<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Edit_Tab_Links extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('navigationblocklinksgrid');
        $this->setDefaultSort('display_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('epicor_common');
        $this->setIdColumn('id');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $this->setUseAjax(true);
        $this->setSkipGenerateContent(true);

        $this->setCustomData($this->getCustomData());
    }

    protected function getCustomData()
    {
        $collection = Mage::getModel('flexitheme/layout_block_link')->getCollection();
        /* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Block_Link_Collection */
        $collection->addFieldToSelect('entity_id', 'id');
        $collection->addFieldToSelect('display_title');
        $collection->addFieldToSelect('tooltip_title');
        $collection->addFieldToSelect('link_url');
        $collection->addFieldToSelect('link_identifier');
        $links = $collection->getItems();

        $cms_pages = Mage::getModel('cms/page')->getCollection();
        /* @var $cms_pages Mage_Cms_Model_Resource_Page_Collection */
        $cms_pages->addFieldToSelect('page_id', 'id');
        $cms_pages->addFieldToSelect('title', 'display_title');
        $cms_pages->addFieldToSelect('page_id', 'tooltip_title');
        $cms_pages->addFieldToSelect('identifier', 'link_url');
        $cms_pages->addFieldToSelect('identifier', 'link_identifier');
        $pageModels = $cms_pages->getItems();
        $pages = array();
        foreach ($pageModels as $page) {
            $pageData = $page->getData();
            $pageData['id'] = $pageData['id'] * -1;
            $pageData['page_id'] = $pageData['page_id'] * -1;
            $pages[] = new Varien_Object($pageData);
        }


        $collection = Mage::getModel('epicor_common/message_collection');
        /* @var $collection Epicor_Common_Model_Message_Collection */

        $items = $links + $pages;

        return $items;
    }

    protected function _getColumns()
    {
        $columns = array();

        $columns['in_group'] = array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'links[]',
            'name' => 'link',
            'values' => array_values($this->getSelectedData('links')),
            'align' => 'center',
            'editable' => true,
            'index' => 'id',
            'sortable' => false
        );

        $columns['display_title'] = array(
            'header' => Mage::helper('flexitheme')->__('Display Title'),
            'align' => 'left',
            'index' => 'display_title',
            'sortable' => false
        );

        $columns['link_url'] = array(
            'header' => Mage::helper('flexitheme')->__('Links To'),
            'align' => 'left',
            'index' => 'link_url',
            'sortable' => false
        );

        $columns['order'] = array(
            'header_css_class' => 'a-center',
            'type' => 'number',
            'name' => 'orders',
            'values' => $this->getSelectedData('order'),
            'align' => 'center',
            'editable' => true,
            'renderer' => 'Epicor_FlexiTheme_Block_Adminhtml_Renderer_Layout_Block_Navigation_Order',
            'sortable' => false
        );


        return $columns;
    }

    public function getRowUrl($row)
    {
        return null;
    }

    public function getSelectedData($key)
    {
        $model = Mage::registry('layout_block_data');
        $data = unserialize($model->getBlockExtra());
        return $data[$key];
    }

}
