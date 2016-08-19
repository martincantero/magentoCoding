<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
        
CREATE TABLE IF NOT EXISTS `{$installer->getTable('nosize/item')}` (
  `nosize_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `simple_id` varchar(255) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_sku` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_brand` varchar(255) NOT NULL,
  `product_url` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `sent` BOOLEAN NOT NULL,
  `sent_date` datetime NOT NULL,
  PRIMARY KEY (`nosize_id`),
  UNIQUE KEY `nosize_id` (`nosize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

");

$installer->endSetup();