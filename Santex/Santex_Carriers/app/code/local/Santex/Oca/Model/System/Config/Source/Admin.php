<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_System_Config_Source_Admin
{
    public function toOptionArray()
    {
        $results = array();
        $collection = Mage::getModel('admin/user')->getCollection();
        if ($collection->count() > 0) {
            foreach($collection as $item) {
                $results[] = array('value' => $item->getData('user_id') , 'label' => $item->getData('username'));
            }
        }
        return $results;
    }
}
