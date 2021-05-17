<?php
require_once __DIR__ .'/../libraries/XmlExportUser.php';

function pmue_pmxe_init_addons() {
    XmlExportEngine::$user_export = new XmlExportUser();

    if(isset(XmlExportEngine::$woo_customer_export)){
        XmlExportEngine::$woo_customer_export = new XmlExportWooCommerceCustomer();
    }
}