<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version
 */

class Blackbird_ContentManager_Adminhtml_ContenttypeController extends Mage_Adminhtml_Controller_Action
{
    private $_tmpFieldset = array(); //temporary save new fieldsets id to link them to new fields
    private $_tmpLocalLayoutGroup = array(); //temporary save groups local id to link them to layout items
    private $_hasFieldError = false;

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('contentmanager/manage_contenttypes');
        return $this;
    }

    protected function _initContentType()
    {
        $this->_title($this->__('Content Type'))
            ->_title($this->__('Manage content types'));

        $contentTypeId  = (int) $this->getRequest()->getParam('ct_id');
        $contentTypeModel = Mage::getModel('contentmanager/contenttype')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        $contentTypeModel->setData('_edit_mode', true);
        if ($contentTypeId) {
            try {
                $contentTypeModel->load($contentTypeId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        Mage::register('contentmanager', $contentTypeModel);
        Mage::register('current_contenttype', $contentTypeModel);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $contentTypeModel;
    }

    public function indexAction() {

        $this->loadLayout();
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_contenttype'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $contentTypeId     = $this->getRequest()->getParam('ct_id');
        $contentTypeModel  = Mage::getModel('contentmanager/contenttype')->load($contentTypeId);

        $this->_initContentType();

        if ($contentTypeModel->getId() || $contentTypeId == 0) {

            Mage::register('contenttype_data', $contentTypeModel);

            $this->loadLayout();
            $this->_setActiveMenu('contentmanager/items');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit'))
                ->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('Content type does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function importAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('contentmanager/items');
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_import'));
        $this->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_import_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getParams()) {
            try {
                //check if some params are set (avoid admin login redirect with no params)
                if(!$this->getRequest()->getParam('identifier'))
                {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('You have been disconnected while you were editing this content type. Ask your administrator to increase the session duration.'));
                    $this->_redirect('*/*/edit/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                    return;
                }

                //check doublon
                $contentTypeModel = Mage::getModel('contentmanager/contenttype');
                $id = 0;
                if (isset($data['ct_id'])) {
                    $id = $data['ct_id'];
                }
                $checkDoublon = Mage::getModel('contentmanager/contenttype')->getCollection()
                    ->addFieldToFilter('identifier', $data['identifier'])
                    ->addFieldToFilter('ct_id', array('neq' => $id));

                if($checkDoublon->getSize() > 0)
                {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('The content type identifier must be unique'));
                    Mage::getSingleton('adminhtml/session')->setContentTypeData($this->getRequest()->getPost());
                    $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                    return;
                }

                //SAVE FIELDSET - init model and set data
                $fieldsets = $this->_saveFieldsets($data);

                //SAVE CONTENT TYPE - init model and set data
                $contentTypeModel = $this->_saveContentType($data);

                //UPDATE FIELDSET
                $this->_updateFieldsets($fieldsets, $contentTypeModel);

                //CREATE / Update EAV Attribute
                $this->_saveEavAttribute($data, $contentTypeModel);

                //SUCCESS MESSAGE AND REDIRECT
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contentmanager')->__('Content type was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setContentTypeData(false);

                //SAVE LAYOUT
                $this->_saveLayoutGroups($data, $contentTypeModel);
                $this->_saveLayoutFields($data, $contentTypeModel);
                $this->_saveLayoutBlocks($data, $contentTypeModel);

                //Empty backend menu cache
                Mage::app()->getCacheInstance()->cleanType(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') || $this->_hasFieldError === true) {
                    $this->_redirect('*/*/edit', array('ct_id' => $contentTypeModel->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setContentTypeData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Generate json file from posted data for further import
     */
    public function exportAction()
    {
        if ($data = $this->getRequest()->getParams()) {
            try {
                //remove unnecessary data
                unset($data['key']);
                unset($data['form_key']);
                unset($data['ct_id']);

                //reset fieldset id
                if(is_array($data['fieldset']) && is_array($data['fieldset']['id']))
                {
                    for($i = 0; $i < count($data['fieldset']['id']); $i++)
                    {
                        $data['fieldset']['id'][$i] = '0';
                    }
                }

                //reset field id
                if(is_array($data['contenttype']) && is_array($data['contenttype']['options']))
                {
                    $i = 1;
                    foreach($data['contenttype']['options'] as $key => $option)
                    {
                        $data['contenttype']['options'][$key]['id'] = $i.'';
                        $data['contenttype']['options'][$key]['fieldset_id'] = '0';
                        $data['contenttype']['options'][$key]['option_id'] = '0';
                        $i++;

                        if(isset($data['contenttype']['options'][$key]['values']))
                        {
                            foreach($data['contenttype']['options'][$key]['values'] as $key2 => $value)
                            {
                                $data['contenttype']['options'][$key]['values'][$key2]['option_type_id'] = '-1';
                            }
                        }
                    }
                }

                //TODO: export layout field
                unset($data['layout_field']);
                unset($data['layout_block']);
                unset($data['layout_group']);

                $this->getResponse ()
                    ->setHttpResponseCode ( 200 )
                    ->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
                    ->setHeader ( 'Pragma', 'public', true )
                    ->setHeader ( 'Content-type', 'application/force-download' )
                    /*->setHeader ( 'Content-Length', filesize($filepath) )*/
                    ->setHeader ('Content-Disposition', 'attachment' . '; filename=acm-export-' . $data['identifier'].'.json' );
                $this->getResponse ()->clearBody ();
                $this->getResponse ()->sendHeaders ();
                echo json_encode($data);
                exit;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setContentTypeData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Handle import file
     */
    public function postImportAction()
    {
        $helper = Mage::helper('contentmanager');
        foreach($_FILES as $identifier => $dataFile) {
            if(isset($_FILES[$identifier]['name']) && $_FILES[$identifier]['name'] != '') {

                if(file_exists($_FILES[$identifier]['tmp_name'])){

                    $path = Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, 'import/');

                    try {
                        $uploader = new Varien_File_Uploader($identifier);
                        $uploader->setAllowedExtensions(array('json'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);


                        try{
                            $dataFile = $uploader->save($path, $_FILES[$identifier]['name']);
                            $dataFileName = $dataFile['path'].$dataFile['file'];

                            $data = json_decode(file_get_contents($dataFileName), true);
                            $this->getRequest()->setParams($data);
                            $this->_forward('save');
                        }
                        catch(Exception $e){
                            Mage::getSingleton('core/session')->addWarning($e->getMessage());
                        }
                    }
                    catch(Exception $e) {
                        Mage::log($e->getMessage());
                    }
                }
                else{
                    throw new Mage_Core_Exception("Import file problem. Format invalid or path not writtable (media/contenttype/import)");
                }
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Update fieldset ID
     */
    private function _updateFieldsets($fieldsets, $contentType)
    {
        foreach($fieldsets as $fieldset)
        {
            $fieldset->setCtId($contentType->getId());
            $fieldset->save();
        }
    }

    /**
     * Save fieldsets from the current CT form in DB
     * @param type $data
     */
    private function _saveFieldsets($data)
    {
        $result = array();
        if(isset($data['fieldset']))
        {
            for($i = 0; $i < count($data['fieldset']['name']); $i++)
            {
                //create (or update) fieldset
                $fieldset = Mage::getModel('contentmanager/fieldset');
                if($data['fieldset']['id'][$i] != 0)
                {
                    $fieldset->load($data['fieldset']['id'][$i]);
                }

                //delete fieldset if needed
                if($data['fieldset']['delete'][$i] === '1' && $data['fieldset']['id'][$i] != 0)
                {
                    $fieldset->delete();
                }
                elseif($data['fieldset']['delete'][$i] !== '1')
                {
                    $fieldset->setTitle($data['fieldset']['name'][$i]);
                    $fieldset->setSortOrder($data['fieldset']['order'][$i]);
                    if(isset($data['ct_id'])) $fieldset->setCtId($data['ct_id']);
                    $fieldset->save();
                    if(!isset($data['ct_id']) || !$data['ct_id'])
                    {
                        $result[] = $fieldset;
                    }

                    if($data['fieldset']['id'][$i] == 0)
                    {
                        $this->_tmpFieldset[$data['fieldset']['random'][$i]] = $fieldset->getId();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Save content type and his options from the current CT form in DB
     * @param type $data
     */
    private function _saveContentType($data)
    {
        //load model or creat a new one
        $contentTypeModel = Mage::getModel('contentmanager/contenttype');

        if (isset($data['ct_id']) && $id = $data['ct_id']) {
            $contentTypeModel->load($id);
        }

        //update basic data
        $contentTypeModel->setData($data);

        /******************************** Preparing save options ***********************************/
        if (isset($data['contenttype']['options'])) {

            foreach($data['contenttype']['options'] as $key => $option)
            {
                //link to the new fieldset if it is a new one
                if($option['fieldset_id'] == 0)
                {
                    $data['contenttype']['options'][$key]['fieldset_id'] = $this->_tmpFieldset[$option['fieldset_random']];
                }
                //set checkbox to 0 if not present
                if(!isset($data['contenttype']['options'][$key]['wysiwyg_editor']))
                {
                    $data['contenttype']['options'][$key]['wysiwyg_editor'] = 0;
                }
                if(!isset($data['contenttype']['options'][$key]['keep_aspect_ratio']))
                {
                    $data['contenttype']['options'][$key]['keep_aspect_ratio'] = 0;
                }
                if(!isset($data['contenttype']['options'][$key]['img_alt']))
                {
                    $data['contenttype']['options'][$key]['img_alt'] = 0;
                }
                if(!isset($data['contenttype']['options'][$key]['img_url']))
                {
                    $data['contenttype']['options'][$key]['img_url'] = 0;
                }
                if(!isset($data['contenttype']['options'][$key]['img_title']))
                {
                    $data['contenttype']['options'][$key]['img_title'] = 0;
                }
                if(!isset($data['contenttype']['options'][$key]['crop']))
                {
                    $data['contenttype']['options'][$key]['crop'] = 0;
                }
            }


            $existingIdentifierWithinCurrentCct = array();
            //check for unique identifier name
            foreach($data['contenttype']['options'] as $key => $option)
            {
                $identifier = $option['identifier'];

                if(in_array($identifier, $existingIdentifierWithinCurrentCct) || $this->_checkFieldExists($identifier, $option['option_id']))
                {
                    //identifier already used
                    Mage::getSingleton('adminhtml/session')->addError('The identifier "'.$identifier.'" is already used for another field or is a system identifier.');
                    $this->_hasFieldError = true;
                    unset($data['contenttype']['options'][$key]);
                }

                $existingIdentifierWithinCurrentCct[] = $identifier;
            }

            //check for file path
            foreach($data['contenttype']['options'] as $key => $option)
            {
                if(!isset($data['contenttype']['options'][$key]['file_path']))
                {
                    $data['contenttype']['options'][$key]['file_path'] = '';
                }

                $data['contenttype']['options'][$key]['file_path'] = str_replace('.', '', $data['contenttype']['options'][$key]['file_path']);
                $data['contenttype']['options'][$key]['file_path'] = str_replace('//', '', $data['contenttype']['options'][$key]['file_path']);
                $data['contenttype']['options'][$key]['file_path'] = str_replace('//', '', $data['contenttype']['options'][$key]['file_path']);
            }

            //set new option (= fields)
            $contentTypeModel->setContentTypeOptions($data['contenttype']['options']);
        }

        //allow the model to save the options
        if(isset($data['affect_contenttype_custom_options']))
        {
            $contentTypeModel->setCanSaveCustomOptions((bool)$data['affect_contenttype_custom_options']);
        }
        else
        {
            $contentTypeModel->setCanSaveCustomOptions(false);
        }
        /******************************** End save options *****************************************/

        //save breacrumbs middle
        if(isset($data['breadcrumb_prev_name']))
            $contentTypeModel->setData('breadcrumb_prev_name', serialize($data['breadcrumb_prev_name']));
        else
            $contentTypeModel->setData('breadcrumb_prev_name', '');

        if(isset($data['breadcrumb_prev_name']))
            $contentTypeModel->setData('breadcrumb_prev_link', serialize($data['breadcrumb_prev_link']));
        else
            $contentTypeModel->setData('breadcrumb_prev_link', '');

        //update date for this model
        if( isset($id) && $id ) {
            $contentTypeModel->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        } else{
            $contentTypeModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        }

        //save the entire model
        $contentTypeModel->save();

        return $contentTypeModel;
    }

    /**
     * Save / edit EAV attribute
     * @param type $data
     */
    private function _saveEavAttribute($data, $contentType)
    {
        $helper = Mage::helper('contentmanager');

        if (isset($data['contenttype']['options'])) {
            foreach($data['contenttype']['options'] as $option)
            {
                if($option['is_delete'] !== '1')
                {
                    // Specials backend models for date and datetime
                    $backendModel = null;
                    if($option['type'] == 'date'){
                        $backendModel = 'eav/entity_attribute_backend_datetime';
                    }
                    elseif($option['type'] == 'date_time'){
                        $backendModel = 'contentmanager/entity_attribute_backend_datetime';
                    }

                    $attribute  = array(
                        'type'          => $helper->getAttributeTypeByFieldType($option['type']),
                        'label'         => $option['title'],
                        'visible'       => true,
                        'required'      => false,
                        'user_defined'  => false,
                        'searchable'    => false,
                        'filterable'    => false,
                        'comparable'    => false,
                        'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'backend'       => $backendModel,
                    );

                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, $option['identifier'], $attribute);

                    if($option['type'] == 'image')
                    {
                        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

                        //DIMENSION
                        $attribute  = array(
                            'type'          => $helper->getAttributeTypeByFieldType('image_dimensions'),
                            'label'         => $option['title'].' '.Mage::helper('adminhtml')->__(' - Image dimensions'),
                            'visible'       => false,
                            'required'      => false,
                            'user_defined'  => false,
                            'searchable'    => false,
                            'filterable'    => false,
                            'comparable'    => false,
                            'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        );

                        $setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, $option['identifier'].'_ctdi', $attribute);

                        //IMG ALT
                        $attribute  = array(
                            'type'          => $helper->getAttributeTypeByFieldType('img_alt'),
                            'label'         => $option['title'].' '.Mage::helper('adminhtml')->__(' - Image ALT'),
                            'visible'       => false,
                            'required'      => false,
                            'user_defined'  => false,
                            'searchable'    => false,
                            'filterable'    => false,
                            'comparable'    => false,
                            'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        );

                        $setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, $option['identifier'].'_alt', $attribute);

                        //IMG URL
                        $attribute  = array(
                            'type'          => $helper->getAttributeTypeByFieldType('img_url'),
                            'label'         => $option['title'].' '.Mage::helper('adminhtml')->__(' - Image URL'),
                            'visible'       => false,
                            'required'      => false,
                            'user_defined'  => false,
                            'searchable'    => false,
                            'filterable'    => false,
                            'comparable'    => false,
                            'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        );

                        $setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, $option['identifier'].'_url', $attribute);

                        //IMG TITLE
                        $attribute  = array(
                            'type'          => $helper->getAttributeTypeByFieldType('img_titl'),
                            'label'         => $option['title'].' '.Mage::helper('adminhtml')->__(' - Image TITLE'),
                            'visible'       => false,
                            'required'      => false,
                            'user_defined'  => false,
                            'searchable'    => false,
                            'filterable'    => false,
                            'comparable'    => false,
                            'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        );

                        $setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, $option['identifier'].'_titl', $attribute);
                    }

                    //Link attribute to option
                    $entity_type = Mage::getModel('eav/entity_type')->load(Blackbird_ContentManager_Model_Content::ENTITY, 'entity_type_code');
                    $attribute = Mage::getModel('eav/entity_attribute')->getCollection()
                        ->addFieldToFilter('attribute_code', $option['identifier'])
                        ->addFieldToFilter('entity_type_id', $entity_type->getEntityTypeId())
                        ->getFirstItem();

                    //save attribute_id relation
                    Mage::getModel('contentmanager/contenttype_option')->getResource()->updateAttributeId($option['identifier'], $attribute->getAttributeId());

                    //set extra attribute value
                    $overridedAttribute = Mage::getModel('contentmanager/attribute')->load($attribute->getAttributeId());
                    $overridedAttribute->setIsSearchable($option['is_searchable']);
                    $overridedAttribute->setSearchAttributeWeight($option['search_attribute_weight']);
                    $overridedAttribute->save();
                }
                elseif($option['identifier'])
                {
                    $entity_type = Mage::getModel('eav/entity_type')->load(Blackbird_ContentManager_Model_Content::ENTITY, 'entity_type_code');

                    //Delete EAV Attribute
                    $attribute = Mage::getModel('eav/entity_attribute')->getCollection()
                        ->addFieldToFilter('attribute_code', $option['identifier'])
                        ->addFieldToFilter('entity_type_id', $entity_type->getEntityTypeId())
                        ->getFirstItem();

                    if($attribute)
                    {
                        $attribute->delete();
                    }

                    if($option['type'] == 'image')
                    {
                        $attributes = Mage::getModel('eav/entity_attribute')
                            ->getCollection()
                            ->addFieldToFilter('attribute_code', array('in' => array(
                                $option['identifier'].'_ctdi',
                                $option['identifier'].'_alt',
                                $option['identifier'].'_url',
                                $option['identifier'].'_titl'
                            )))
                            ->addFieldToFilter('entity_type_id', $entity_type->getEntityTypeId());

                        foreach($attributes as $attribute)
                        {
                            $attribute->delete();
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if an option (=field) already exists, to avoid creating two times a field with the same identifier: it have to be unique
     * @param type $identifier
     */
    private function _checkFieldExists($identifier, $option_id)
    {
        //check for other options
        $option_exists = Mage::getModel('contentmanager/contenttype_option')
            ->getCollection()
            ->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('option_id', array('neq' => $option_id))
            ->getSize();

        $attribute_exists = 0;
        if($option_exists == 0)
        {
            //check for generic attributes
            $attribute_exists = in_array($identifier, Mage::helper('contentmanager')->getForbiddenIdentifier());
        }

        return $option_exists > 0 || $attribute_exists === true;
    }

    /**
     * Save layout fields items
     * @param type $data
     */
    private function _saveLayoutFields($data, $contentType)
    {
        //delete existing fields
        $collection = Mage::getModel('contentmanager/contenttype_layout_field')->getCollection()->addFieldToFilter('ct_id', $contentType->getId());
        foreach($collection as $layoutField)
        {
            $layoutField->delete();
        }

        //save fields
        if(isset($data['layout_field']))
        {
            foreach($data['layout_field'] as $key => $layout_field)
            {
                $layoutField = Mage::getModel('contentmanager/contenttype_layout_field');
                $layoutField->setColumn($layout_field['column']);
                $layoutField->setSortOrder($layout_field['sort_order']);
                $layoutField->setLabel($layout_field['label']);
                $layoutField->setHtmlClass($layout_field['html_class']);
                $layoutField->setHtmlId($layout_field['html_id']);
                $layoutField->setHtmlTag($layout_field['html_tag']);
                $layoutField->setHtmlLabelTag($layout_field['html_label_tag']);
                if($layout_field['option_id'] > 0) $layoutField->setOptionId($layout_field['option_id']);
                $layoutField->setCtId($contentType->getId());
                if(isset($this->_tmpLocalLayoutGroup[$layout_field['layout_group_id']])) $layoutField->setLayoutGroupId($this->_tmpLocalLayoutGroup[$layout_field['layout_group_id']]);

                $layoutField->setFormat(
                    serialize(array(
                        'type' => isset($layout_field['format'])?$layout_field['format']:'',
                        'extra' => isset($layout_field['format_extra'])?$layout_field['format_extra']:'',
                        'height' => isset($layout_field['format_height'])?$layout_field['format_height']:'',
                        'width' => isset($layout_field['format_width'])?$layout_field['format_width']:'',
                        'link' => isset($layout_field['link'])?$layout_field['link']:'',
                    ))
                );

                $layoutField->save();
            }
        }
    }

    /**
     * Save layout blocks items
     * @param type $data
     */
    private function _saveLayoutBlocks($data, $contentType)
    {
        //delete existing blocks
        $collection = Mage::getModel('contentmanager/contenttype_layout_block')->getCollection()->addFieldToFilter('ct_id', $contentType->getId());
        foreach($collection as $layoutBlock)
        {
            $layoutBlock->delete();
        }

        //save blocks
        if(isset($data['layout_block']))
        {
            foreach($data['layout_block'] as $key => $layout_block)
            {
                $layoutBlock = Mage::getModel('contentmanager/contenttype_layout_block');
                $layoutBlock->setColumn($layout_block['column']);
                $layoutBlock->setSortOrder($layout_block['sort_order']);
                $layoutBlock->setLabel($layout_block['label']);
                $layoutBlock->setHtmlClass($layout_block['html_class']);
                $layoutBlock->setHtmlId($layout_block['html_id']);
                $layoutBlock->setHtmlTag($layout_block['html_tag']);
                $layoutBlock->setHtmlLabelTag($layout_block['html_label_tag']);
                if($layout_block['block_id'] > 0) $layoutBlock->setBlockId($layout_block['block_id']);
                $layoutBlock->setCtId($contentType->getId());
                if(isset($this->_tmpLocalLayoutGroup[$layout_block['layout_group_id']])) $layoutBlock->setLayoutGroupId($this->_tmpLocalLayoutGroup[$layout_block['layout_group_id']]);

                $layoutBlock->save();
            }
        }
    }

    /**
     * Save layout groups items
     * @param type $data
     */
    private function _saveLayoutgroups($data, $contentType)
    {
        //delete existing groups
        $collection = Mage::getModel('contentmanager/contenttype_layout_group')->getCollection()->addFieldToFilter('ct_id', $contentType->getId());
        foreach($collection as $layoutGroup)
        {
            $layoutGroup->delete();
        }

        //tmp group to save 2 times to link them to their parent group (if existing only)
        $_layoutGroupArray = array();

        //save groups
        if(isset($data['layout_group']))
        {
            foreach($data['layout_group'] as $key => $layout_group)
            {
                $layoutGroup = Mage::getModel('contentmanager/contenttype_layout_group');
                $layoutGroup->setColumn($layout_group['column']);
                $layoutGroup->setSortOrder($layout_group['sort_order']);
                $layoutGroup->setLabel($layout_group['label']);
                $layoutGroup->setHtmlName($layout_group['html_name']);
                $layoutGroup->setHtmlClass($layout_group['html_class']);
                $layoutGroup->setHtmlId($layout_group['html_id']);
                $layoutGroup->setHtmlTag($layout_group['html_tag']);
                $layoutGroup->setHtmlLabelTag($layout_group['html_label_tag']);
                $layoutGroup->setLocalParentLayoutGroupId($layout_group['parent_layout_group_id']);
                $layoutGroup->setCtId($contentType->getId());

                $layoutGroup->save();

                if($layoutGroup->getLocalParentLayoutGroupId())
                {
                    $_layoutGroupArray[] = $layoutGroup;
                }

                //save correspondance between local group id and final group id
                $this->_tmpLocalLayoutGroup[$layout_group['layout_group_id']] = $layoutGroup->getId();
            }
        }

        //Saved 2 times to link them to their parent group (if existing only)
        foreach($_layoutGroupArray as $layoutGroup)
        {
            $layoutGroup->setParentLayoutGroupId($this->_tmpLocalLayoutGroup[$layoutGroup->getLocalParentLayoutGroupId()]);
            $layoutGroup->save();
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('ct_id') > 0 ) {
            try {
                //Delete EAV Attribute
                $entity_type = Mage::getModel('eav/entity_type')->load(Blackbird_ContentManager_Model_Content::ENTITY, 'entity_type_code');

                $options = Mage::getModel('contentmanager/contenttype_option')->getCollection()->addFieldToFilter('ct_id', $this->getRequest()->getParam('ct_id'));
                foreach($options as $option)
                {
                    $attribute = Mage::getModel('eav/entity_attribute')
                        ->getCollection()
                        ->addFieldToFilter('attribute_code', $option->getIdentifier())
                        ->addFieldToFilter('entity_type_id', $entity_type->getEntityTypeId())
                        ->getFirstItem();

                    if($attribute)
                    {
                        $attribute->delete();
                    }

                    if($option->getType() == 'image')
                    {
                        $attributes = Mage::getModel('eav/entity_attribute')
                            ->getCollection()
                            ->addFieldToFilter('attribute_code', array('in' => array(
                                $option->getIdentifier().'_ctdi',
                                $option->getIdentifier().'_alt',
                                $option->getIdentifier().'_url',
                                $option->getIdentifier().'_titl'
                            )))
                            ->addFieldToFilter('entity_type_id', $entity_type->getEntityTypeId())
                        ;

                        foreach($attributes as $attribute)
                        {
                            $attribute->delete();
                        }
                    }
                }

                //delete fieldset
                $fieldsets = Mage::getModel('contentmanager/fieldset')->getCollection()->addFieldToFilter('ct_id', $this->getRequest()->getParam('ct_id'));
                foreach($fieldsets as $fieldset)
                {
                    $fieldset->delete();
                }

                //delete model
                $contentTypeModel = Mage::getModel('contentmanager/contenttype');
                $contentTypeModel->setId($this->getRequest()->getParam('ct_id'))->delete();

                //clean cache
                Mage::app()->getCacheInstance()->cleanType(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS);

                //success message and redirect
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Content type was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('importedit/adminhtml_contenttype_grid')->toHtml()
        );
    }

    /**
     * Get options fieldset block
     *
     */
    public function optionsAction()
    {
        $this->_initContentType();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_contenttypes');
    }
}