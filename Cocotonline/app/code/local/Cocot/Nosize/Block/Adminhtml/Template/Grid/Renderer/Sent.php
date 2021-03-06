<?php

class Cocot_Nosize_Block_Adminhtml_Template_Grid_Renderer_Sent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row)
    {
//        $val = $row->getData($this->getColumn()->getIndex());
//        $val = str_replace("no_selection", "", $val);
//        $url = Mage::getBaseUrl('media') . 'catalog/product' . $val;
//        $out = "<img src=" . $url . " width='60px'/>";
//        return $out;
        if ($row->getSent()) {
            $date = new DateTime($row->getSentDate());
            return $date->format('Y-m-d');
        } else {
            return '-';
        }
        ;//$row->getData($this->getColumn()->getSent()) ;
    }

}