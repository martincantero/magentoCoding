<?php

 class Cocot_Nosize_Model_Resource_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{

    protected function _construct() 
    {
        $this->_init('nosize/item');
    }
//    
    public function getNoSent()
    {
        $this->_select->where("sent = 0");
        return $this;
    }
    
    public function getEmailAndId($email)
    {
        $this->_select->where("cocot.email = $email");
        return $this;
    }
//    
//    public function setGropuByCity()
//    {
//        $this->_select->group("city");
//        return $this;
//    }
//    
//    public function setCp($cp)
//    {
//        $this->_select->where("cp = '$cp'");
//        return $this;
//    }

}

