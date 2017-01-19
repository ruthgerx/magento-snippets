<?php

//SETTINGS
$since      = strtotime('yesterday'); //Speaks for itself
$mailto     = "mail@mailprovider.com";
$mailfrom   = "mail@mailprovider.com";
$adminurl   = "https://www.youradmin/admin";

date_default_timezone_set('Europe/Amsterdam'); //Change to your own needs

//End of settings

require_once '../app/Mage.php'; //Location of App/Mage.php

Mage::app(); //Load magento

$outOfStockItems = Mage::getModel('cataloginventory/stock_item')
    ->getCollection()
    ->addFieldToFilter('is_in_stock', 0)
    ->addFieldToFilter('low_stock_date', array('date' => true, 'from' => date("Y-m-d", $since)))
;

//Start email message
$message = '<html><body>';
$message .= '<h1>Producten die niet meer voorradig zijn:</h1>';


foreach ($outOfStockItems as $item) {
    $product = Mage::getModel('catalog/product')->load($item->getOrigData('product_id'));
    $message .= '<a href="' . $adminurl . '/catalog_product/edit/id/' . $product->getId() . '/"><strong>' .   $product->getName() . ' </strong></a> | SKU:  ' . $product->getSku() . '<br>';
}


$subject = 'Out of stock products';

$headers = "From: " . $mailto . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$message .= '</body></html>';
if(count($outOfStockItems) > 0) {
    mail($mailto, $subject, $message, $headers);
} //Only mail if 1 or more 
