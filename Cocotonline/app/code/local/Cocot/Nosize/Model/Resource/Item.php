<?php

class Cocot_Nosize_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract 
{

    protected function _construct() 
    {
        $this->_init('nosize/item', 'nosize_id');
    }

}

