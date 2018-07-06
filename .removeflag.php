<?php
/**
 * StandAlone script to remove the maintenance flag from the server
 * Due to some reason magento maintenance flag is not deleted in the server
 * Issue could be due to file permission hence changing the file permission and trying to delete the maintenance flag file
 */
$filename = $_SERVER['DOCUMENT_ROOT'] . '/maintenance.flag';
if (file_exists($filename)) {
    chmod($filename, 0777);
    unlink($filename);
    echo "Maintainance Flag Removed Successfully";
} else {
    echo "Maintainance Flag doesn't exist";
}

