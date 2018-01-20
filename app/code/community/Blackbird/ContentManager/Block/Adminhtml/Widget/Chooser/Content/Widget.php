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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS block chooser for Wysiwyg CMS widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Block_Adminhtml_Widget_Chooser_Content_Widget extends Blackbird_ContentManager_Block_Adminhtml_Content_Grid_Abstract
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_is_active' => '1'));
    }
    

    protected function _prepareLayout()
    {
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        return $this;
    }    

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('adminhtml/cm_ajax_widget/chooser', array('uniq_id' => $uniqId, 'attribute' => 'content_widget'));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId)
            ->setAttribute('content_widget');


        if ($element->getValue()) {
            $content = Mage::getModel('contentmanager/content')->load($element->getValue());
            if ($content->getId()) {
                $chooser->setLabel($content->getTitle(). '(ID: '.$content->getId().')');
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("div.entity_id").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td.title").innerHTML;
                '.$chooserJsObject.'.setElementValue(blockId);
                '.$chooserJsObject.'.setElementLabel(blockTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare columns for Cms blocks grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('contentmanager')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('flags', array(
            'header'    => Mage::helper('contentmanager')->__('Flag'),
            'align'     =>'left',
            'index'     => 'flags',
            'type'          => 'flags',
            'width'     => '80px',
            'store_all'     => true,
            'filter'        => false,
            'store_view'    => true,
            'renderer'  => new Blackbird_ContentManager_Block_Adminhtml_Content_Renderer_Flags(),
        ));
        
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('contentmanager')->__('Title'),
            'name'      => 'title',
            'index'     => 'title',
            'column_css_class' => 'title',
            'renderer'  => new Blackbird_ContentManager_Block_Adminhtml_Content_Renderer_Text(),
        ));

        $this->addColumn('ct_id',
            array(
                'header'=> Mage::helper('contentmanager')->__('Content type'),
                'width' => '60px',
                'index' => 'ct_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('contentmanager/contenttype')->getOptionArray(),
        ));
        
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('contentmanager')->__('Updated At'),
            'name'      => 'updated_at',
            'index'     => 'updated_at'
        ));
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('contentmanager')->__('Created At'),
            'name'      => 'created_at',
            'index'     => 'created_at'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        //$uniqId = Mage::helper('core')->uniqHash($element->getId());
        return $this->getUrl('*/cm_ajax_widget/chooser', array('attribute' => 'content_widget', '_current' => true));
    }
    
}
