<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */

/**
 * @var Mage_Core_Model_Resource_Setup $this
 */

$this->startSetup();

$logTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/deleteRequest'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ),
        'Date of logging'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Customer Id'
    )
    ->addColumn(
        'customer_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        127,
        array(
            'nullable' => false
        ),
        'Customer Name'
    )
    ->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        127,
        array(
            'nullable' => false
        ),
        'Customer Email'
    );

$this->getConnection()->createTable($logTable);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/deleteRequest', 'customer_id', 'customer/entity', 'entity_id'),
    $this->getTable('amgdpr/deleteRequest'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$consentLogTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/consentLog'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Customer Id'
    )
    ->addColumn(
        'date_consented',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ),
        'Date of consent'
    )
    ->addColumn(
        'policy_version',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'default' => ''
        ),
        'Policy version'
    );

$this->getConnection()->createTable($consentLogTable);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/consentLog', 'customer_id', 'customer/entity', 'entity_id'),
    $this->getTable('amgdpr/consentLog'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$privacyPolicyTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/privacyPolicy'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ),
        'Date of creating'
    )
    ->addColumn(
        'policy_version',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => false,
            'default' => ''
        ),
        'Policy Version'
    )
    ->addColumn(
        'content',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64K',
        array(
            'nullable' => false,
            'default' => ''
        ),
        'Policy Content'
    )
    ->addColumn(
        'date_last_edited',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false
        ),
        'Date of last editing'
    )
    ->addColumn(
        'last_edited_by',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        255,
        array(
            'nullable' => true,
        ),
        'Last edited By'
    )
    ->addColumn(
        'comment',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'default' => ''
        ),
        'Comment'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_TINYINT,
        255,
        array(
            'nullable' => false,
            'default' => 0
        ),
        'Status'
    );

$this->getConnection()->createTable($privacyPolicyTable);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/privacyPolicy', 'last_edited_by', 'admin/user', 'user_id'),
    $this->getTable('amgdpr/privacyPolicy'),
    'last_edited_by',
    $this->getTable('admin/user'),
    'user_id',
    Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL
);

$privacyPolicyContentTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/policyContent'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'policy_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false
        ),
        'Date of creating'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false
        ),
        'Policy Version'
    )
    ->addColumn(
        'content',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64K',
        array(
            'nullable' => false,
            'default' => ''
        ),
        'Policy Content'
    );

$this->getConnection()->createTable($privacyPolicyContentTable);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/policyContent', 'policy_id', 'amgdpr/privacyPolicy', 'id'),
    $this->getTable('amgdpr/policyContent'),
    'policy_id',
    $this->getTable('amgdpr/privacyPolicy'),
    'id'
);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/policyContent', 'store_id', 'core/store', 'store_id'),
    $this->getTable('amgdpr/policyContent'),
    'store_id',
    $this->getTable('core/store'),
    'store_id'
);

$consentQueueTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/consentQueue'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Customer Id'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_TINYINT,
        null,
        array(
            'nullable' => false,
            'default' => 0
        ),
        'Status'
    );

$this->getConnection()->createTable($consentQueueTable);

$this->getConnection()
    ->addForeignKey(
        $this->getFkName('amgdpr/consentQueue', 'customer_id', 'customer/entity', 'entity_id'),
        $this->getTable('amgdpr/consentQueue'),
        'customer_id',
        $this->getTable('customer/entity'),
        'entity_id'
    );

$this->getConnection()
    ->addIndex(
        $this->getTable('amgdpr/consentQueue'),
        $this->getIdxName(
            $this->getTable('amgdpr/consentQueue'),
            'customer_id',
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        'customer_id',
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );


$actionLogTable = $this->getConnection()
    ->newTable($this->getTable('amgdpr/actionLog'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Id')
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => true
        ),
        'Customer Id'
    )
    ->addColumn(
        'ip',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        127,
        array(
            'nullable' => false,
        ),
        'Remote Ip Address'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ),
        'Date of logging'
    )
    ->addColumn(
        'action',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        ),
        'Performed Action'
    );

$this->getConnection()->createTable($actionLogTable);

$this->getConnection()->addForeignKey(
    $this->getFkName('amgdpr/actionLog', 'customer_id', 'customer/entity', 'entity_id'),
    $this->getTable('amgdpr/actionLog'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$this->endSetup();
