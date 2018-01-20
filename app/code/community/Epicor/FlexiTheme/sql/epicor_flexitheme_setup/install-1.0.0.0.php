<?php


$installer = $this;

$installer->startSetup();


/************************************************************************
Step : Create Theme Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/theme'));
$table=$conn->newTable($this->getTable('flexitheme/theme'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('theme_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
        'nullable'  => false,
        ), 'Theme Name');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Updated At');

$conn->createTable($table);


/************************************************************************
Step : Create Theme Design Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/theme_design'));
$table=$conn->newTable($this->getTable('flexitheme/theme_design'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('theme_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Theme ID');
$table->addColumn('form_field_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Form Field Code');
$table->addColumn('css_selector',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Css Selector');
$table->addColumn('css_property',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5000, array(
        'nullable'  => false,
        ), 'Css Property');
$table->addColumn('value',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Value');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Updated At');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/theme_design'),
            array('theme_id')
        ),
        'theme_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/theme'), 'entity_id',
                $this->getTable('flexitheme/theme_design'), 'theme_id'
                ), 
        'theme_id',
        $this->getTable('flexitheme/theme'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);



/************************************************************************
Step : Create Layout Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout'));
$table=$conn->newTable($this->getTable('flexitheme/layout'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('layout_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
        'nullable'  => false,
        ), 'Layout Name');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$conn->createTable($table);

/************************************************************************
Step : Create Layout Page Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout_page'));
$table=$conn->newTable($this->getTable('flexitheme/layout_page'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('cms_page_id',Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
        ), 'CMS Page ID');
$table->addColumn('page_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
        'nullable'  => false,
        ), 'Page Name');
$table->addColumn('xml_node',Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
        'nullable'  => false,
        ), 'XML Node');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_page'),
            array('cms_page_id')
        ),
        'cms_page_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('cms/page'), 'page_id',
                $this->getTable('flexitheme/layout_page'), 'cms_page_id'
                ), 
        'cms_page_id',
        $this->getTable('cms/page'), 
        'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);

/************************************************************************
Step : Create Layout Template Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout_template'));
$table=$conn->newTable($this->getTable('flexitheme/layout_template'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('template_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
        'nullable'  => false,
        ), 'Template Name');
$table->addColumn('template_file',Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, array(
        'nullable'  => false,
        ), 'Template File');
$table->addColumn('template_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
        ), 'Template File');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$conn->createTable($table);

/************************************************************************
Step : Create Layout Template Section Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout_template_section'));
$table=$conn->newTable($this->getTable('flexitheme/layout_template_section'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('template_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Template ID');
$table->addColumn('section_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 500, array(
        'nullable'  => false,
        ), 'Section Name');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_template_section'),
            array('template_id')
        ),
        'template_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_template'), 'entity_id',
                $this->getTable('flexitheme/layout_template_section'), 'template_id'
                ), 
        'template_id',
        $this->getTable('flexitheme/layout_template'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);

/************************************************************************
Step : Create Layout Layout Page Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout_layout_page'));
$table=$conn->newTable($this->getTable('flexitheme/layout_layout_page'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('layout_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Layout ID');
$table->addColumn('page_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        ), 'Page ID');
$table->addColumn('cms_page_id',Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
        'unsigned'  => true,
        ), 'CMS Page ID');
$table->addColumn('template_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Template ID');
$table->addColumn('layout_xml_update',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Theme Name');
$table->addColumn('page_type',Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
        'nullable'  => false,
        'default'   => 'std_page',
        ), 'Template ID');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_layout_page'),
            array('layout_id')
        ),
        'layout_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_layout_page'),
            array('page_id')
        ),
        'page_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_layout_page'),
            array('cms_page_id')
        ),
        'cms_page_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_layout_page'),
            array('template_id')
        ),
        'template_id');

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout'), 'entity_id',
                $this->getTable('flexitheme/layout_layout_page'), 'layout_id'
                ), 
        'layout_id',
        $this->getTable('flexitheme/layout'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_page'), 'entity_id',
                $this->getTable('flexitheme/layout_layout_page'), 'page_id'
                ), 
        'page_id',
        $this->getTable('flexitheme/layout_page'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_template'), 'entity_id',
                $this->getTable('flexitheme/layout_layout_page'), 'template_id'
                ), 
        'template_id',
        $this->getTable('flexitheme/layout_template'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);


/************************************************************************
Step : Create Layout Block Table
*************************************************************************/
// Create erp_catalog_category
$conn->dropTable($this->getTable('flexitheme/layout_block'));
$table=$conn->newTable($this->getTable('flexitheme/layout_block'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('block_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Block Name');
$table->addColumn('block_type',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Type');
$table->addColumn('block_flexi_type',Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
        'nullable'  => false,
        'default'   => 'std_block',
        ), 'Block Flexi Type');
$table->addColumn('block_extra',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Block Extra');
$table->addColumn('block_template',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Template');
$table->addColumn('block_template_left',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Template Left');
$table->addColumn('block_template_right',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Template RIGHT');
$table->addColumn('block_template_header',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Template Header');
$table->addColumn('block_template_footer',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block Template Footer');
$table->addColumn('block_xml_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Block XML Name');
$table->addColumn('block_xml',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Block XML');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Updated At');

$conn->createTable($table);

/************************************************************************
Step : Create Layout Page Blocks Table
*************************************************************************/
// Create erp_catalog_category
$conn->dropTable($this->getTable('flexitheme/layout_page_block'));
$table=$conn->newTable($this->getTable('flexitheme/layout_page_block'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('layout_page_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Layout Page ID');
$table->addColumn('block_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Block ID');
$table->addColumn('section_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Section ID');
$table->addColumn('order',Varien_Db_Ddl_Table::TYPE_INTEGER, 4, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Order');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Created At');
$table->addColumn('modified_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Modified At');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_page_block'),
            array('layout_page_id')
        ),'layout_page_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_page_block'),
            array('block_id')
        ),'block_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/layout_page_block'),
            array('section_id')
        ),'section_id');

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_layout_page'), 'entity_id',
                $this->getTable('flexitheme/layout_page_block'), 'layout_page_id'
                ), 
        'layout_page_id',
        $this->getTable('flexitheme/layout_layout_page'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_block'), 'entity_id',
                $this->getTable('flexitheme/layout_page_block'), 'block_id'
                ), 
        'block_id',
        $this->getTable('flexitheme/layout_block'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/layout_template_section'), 'entity_id',
                $this->getTable('flexitheme/layout_page_block'), 'section_id'
                ), 
        'section_id',
        $this->getTable('flexitheme/layout_template_section'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);


/************************************************************************
Step : Create Layout Block Link Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/layout_block_link'));
$table=$conn->newTable($this->getTable('flexitheme/layout_block_link'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('display_title',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Display Title');
$table->addColumn('tooltip_title',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Tool Tip Title');
$table->addColumn('link_identifier',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Link Identifier');
$table->addColumn('link_url',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Custom Url');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Updated At');

$conn->createTable($table);


/************************************************************************
Step : Create Translation Table
*************************************************************************/

$conn->dropTable($this->getTable('flexitheme/translation_data'));
$table=$conn->newTable($this->getTable('flexitheme/translation_data'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('translation_string',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Translation String');

$conn->createTable($table);


/************************************************************************
Step : Create Translation Language Table
*************************************************************************/
$conn->dropTable($this->getTable('flexitheme/translation_language'));
$table=$conn->newTable($this->getTable('flexitheme/translation_language'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('translation_language',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Translation Language');
$table->addColumn('language_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Language Code');
$table->addColumn('active',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Active');

$conn->createTable($table);


/************************************************************************
Step : Create Translation Updates Table
*************************************************************************/
$conn->dropTable($this->getTable('flexitheme/translation_updates'));
$table=$conn->newTable($this->getTable('flexitheme/translation_updates'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('phrase_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => false,
        ), 'Phrase Id');
$table->addColumn('language_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => false,
        ), 'Language Id');
$table->addColumn('translated_phrase',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Translated Phrase');


$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/translation_updates'),
            array('phrase_id')
        ),
        'phrase_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('flexitheme/translation_updates'),
            array('language_id')
        ),
        'language_id');

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/translation_data'),'id',
                $this->getTable('flexitheme/translation_updates'),'phrase_id'
        ), 
        'phrase_id',																																					
        $this->getTable('flexitheme/translation_data'), 																			
        'id',																					
        Varien_Db_Ddl_Table::ACTION_CASCADE,														
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('flexitheme/translation_language'),'id',
                $this->getTable('flexitheme/translation_updates'),'language_id'
        ), 
        'language_id',																																					
        $this->getTable('flexitheme/translation_language'), 																			
        'id',																					
        Varien_Db_Ddl_Table::ACTION_CASCADE,														
        Varien_Db_Ddl_Table::ACTION_CASCADE);


$conn->createTable($table);

/* copied from flexitheme data install 1.0.0.0 */

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */

try {
    $hadSolarsoft = count($conn->fetchAll("SELECT * from solarsoft_flexitheme_theme")); 
} catch (Exception $exc) {
    $hadSolarsoft = false;	
}

if ($hadSolarsoft) {
    $helper->migrateData($this->getTable('flexitheme/theme'), "solarsoft_flexitheme_theme", $conn);	
    $helper->migrateData($this->getTable('flexitheme/theme_design'), "solarsoft_flexitheme_theme_design", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout'), "solarsoft_flexitheme_layout", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_page'), "solarsoft_flexitheme_page", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_template'), "solarsoft_flexitheme_template", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_template_section'), "solarsoft_flexitheme_template_section", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_layout_page'), "solarsoft_flexitheme_layout_page", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_block'), "solarsoft_flexitheme_block", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_block_link'), "solarsoft_flexitheme_block_link", $conn);
    $helper->migrateData($this->getTable('flexitheme/layout_page_block'), "solarsoft_flexitheme_page_block", $conn);
} else {


    /*     * **********************************************************************
      Step : Populate Layout Blocks
     * *********************************************************************** */

    $helper->createLayoutBlock('Basket', 'checkout/cart_sidebar', 'checkout/cart/sidebar.phtml', NULL, NULL, NULL, NULL, 'cart_sidebar', NULL);
    $helper->createLayoutBlock('WishList', 'wishlist/customer_sidebar', 'wishlist/sidebar.phtml', NULL, NULL, NULL, NULL, 'wishlist_sidebar', NULL);
    $helper->createLayoutBlock('Product Compare SideBar', 'catalog/product_compare_sidebar', 'catalog/product/compare/sidebar.phtml', NULL, NULL, NULL, NULL, 'catalog.compare.sidebar', NULL);
    $helper->createLayoutBlock('PayPal Logo', 'paypal/logo', 'paypal/partner/logo.phtml', NULL, NULL, NULL, NULL, 'paypal.partner.logo', NULL);
    $helper->createLayoutBlock('Popular Tags', 'tag/popular', 'tag/popular.phtml', NULL, NULL, NULL, NULL, 'tags_popular', NULL);
    $helper->createLayoutBlock('Navigation Sub Categories', 'catalog/navigation', 'catalog/navigation/left.phtml', NULL, NULL, NULL, NULL, 'catalog.leftnav', NULL);
    $helper->createLayoutBlock('Recently Viewed Products', 'reports/product_viewed', 'reports/product_viewed.phtml', NULL, NULL, NULL, NULL, 'reports.product.viewed', NULL);
    $helper->createLayoutBlock('Search', 'core/template', 'catalogsearch/form.mini.phtml', NULL, NULL, NULL, NULL, 'search', NULL);
    $helper->createLayoutBlock('Navigation All Categories', 'catalog/navigation', 'catalog/navigation/top.phtml', 'catalog/navigation/side.phtml', 'catalog/navigation/side.phtml', NULL, NULL, 'catalog.topnav', NULL);
    $helper->createLayoutBlock('Random Poll', 'poll/activePoll', NULL, NULL, NULL, NULL, NULL, 'right.poll', '<action method="setPollTemplate">\r\n       <template>poll/active.phtml</template>\r\n        <type>poll</type>\r\n </action>\r\n <action method="setPollTemplate">\r\n         <template>poll/result.phtml</template>\r\n         <type>results</type>\r\n</action>');
    $helper->createLayoutBlock('User Account Links', 'flexitheme/frontend_template_quicklinks', NULL, NULL, NULL, NULL, NULL, 'quick.links', '<action method="setClone"><clone>top.links</clone></action>');
    $helper->createLayoutBlock('Website Logo', 'flexitheme/frontend_template_logo', NULL, NULL, NULL, NULL, NULL, 'site.logo', NULL);
    $helper->createLayoutBlock('Welcome Msg', 'flexitheme/frontend_template_welcome', NULL, NULL, NULL, NULL, NULL, 'welcome', NULL);
    $helper->createLayoutBlock('Footer Links', 'flexitheme/frontend_template_quicklinks', NULL, NULL, NULL, NULL, NULL, 'footer.links', '<action method="setClone"><clone>footer_links</clone></action>');
    $helper->createLayoutBlock('Store Switcher', 'page/switch', 'page/switch/stores.phtml', NULL, NULL, NULL, NULL, 'store_switcher', NULL);


    /*     * **********************************************************************
      Step : Populate Layout Templates
     * *********************************************************************** */

    $helper->createLayoutTemplate('1 Column', 'page/1column.phtml', 'one_column');
    $helper->createLayoutTemplate('2 Column Left', 'page/2columns-left.phtml', 'two_columns_left');
    $helper->createLayoutTemplate('2 Column Right', 'page/2columns-right.phtml', 'two_columns_right');
    $helper->createLayoutTemplate('3 Columns', 'page/3columns.phtml', 'three_columns');


    /*     * **********************************************************************
      Step : Populate Layout Template Sections
     * *********************************************************************** */

    $helper->createLayoutTemplateSection(1, 'top.container');
    $helper->createLayoutTemplateSection(1, 'content');
    $helper->createLayoutTemplateSection(1, 'bottom.container');
    $helper->createLayoutTemplateSection(2, 'top.container');
    $helper->createLayoutTemplateSection(2, 'left');
    $helper->createLayoutTemplateSection(2, 'content');
    $helper->createLayoutTemplateSection(2, 'bottom.container');
    $helper->createLayoutTemplateSection(3, 'top.container');
    $helper->createLayoutTemplateSection(3, 'content');
    $helper->createLayoutTemplateSection(3, 'right');
    $helper->createLayoutTemplateSection(3, 'bottom.container');
    $helper->createLayoutTemplateSection(4, 'top.container');
    $helper->createLayoutTemplateSection(4, 'left');
    $helper->createLayoutTemplateSection(4, 'content');
    $helper->createLayoutTemplateSection(4, 'right');
    $helper->createLayoutTemplateSection(4, 'bottom.container');


    /*     * **********************************************************************
      Step : Populate Layout Pages
     * *********************************************************************** */

    $helper->createLayoutPage('Default', 'default');
    $helper->createLayoutPage('Product Details', 'catalog_product_view');
    $helper->createLayoutPage('Product List', 'catalog_category_default');
    $helper->createLayoutPage('Basket', 'checkout_cart_index');
    
    $tableName = $this->getTable('flexitheme/layout_page');
    $sql = 'UPDATE ' . $tableName . ' SET entity_id = 0 WHERE entity_id = 1';
    $conn->exec($sql);
}


$installer->endSetup();
