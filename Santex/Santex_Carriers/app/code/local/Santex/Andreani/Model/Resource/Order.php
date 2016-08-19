<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Resource_Order extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('andreani/order', 'id');
     }
}