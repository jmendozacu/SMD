<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    UPDATE  " . $this->getTable('epicor_comm/message_log') . "
    SET     message_secondary_subject = REPLACE(message_secondary_subject, 'Quote ID', 'Basket Quote ID')
    WHERE   message_type = 'GOR'
");

$installer->endSetup();